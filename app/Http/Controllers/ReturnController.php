<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('q',''));
        $shopId = currentShopId();
        $returns = Sale::with(['items.product','cashier','returnedBy'])
            ->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))
            ->where('status','returned')
            ->when($q, fn($qq)=> $qq->where('receipt_number','like',"%{$q}%")->orWhere('return_reason','like',"%{$q}%"))
            ->latest('returned_at')
            ->paginate(10)->withQueryString();
        return view('returns.index', compact('returns','q'));
    }

    public function create(Request $request)
    {
        $shopId = currentShopId();
        $sale = null;
        if($request->filled('sale')){
            $sale = Sale::with(['items.product','customer'])->find(decIdOrRaw($request->input('sale')));
            if($sale && $sale->shop_id && $sale->shop_id !== $shopId) abort(404);
            if($sale && $sale->status === 'returned'){
                return redirect()->route('returns.create')->with('error','Sale already returned: '.$sale->receipt_number);
            }
        }
        // recent completed sales for selection — scoped to shop
        $sales = Sale::with('customer')->when($shopId, fn($q)=>$q->where('shop_id',$shopId))->where('status','completed')->latest()->limit(50)->get();
        return view('returns.create', compact('sales','sale'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required',
            'return_reason' => 'required|string|max:191',
            'notes' => 'nullable|string|max:500',
        ]);

        $saleId = decIdOrRaw($request->input('sale_id'));
        $sale = Sale::with('items.product')->findOrFail($saleId);
        if($sale->shop_id && currentShopId() && $sale->shop_id !== currentShopId()) abort(404);

        if($sale->status === 'returned'){
            return back()->with('error','Sale already returned.');
        }
        if($sale->status !== 'completed'){
            return back()->with('error','Only completed sales can be returned.');
        }

        return DB::transaction(function() use ($sale, $request){
            // restore stock for each item
            foreach($sale->items as $it){
                $product = $it->product;
                if(!$product) continue;
                $prev = $product->current_stock;
                $new = $prev + $it->quantity;
                $product->increment('current_stock', $it->quantity);
                StockMovement::create([
                    'product_id'=>$product->id,
                    'type'=>'return',
                    'quantity'=>$it->quantity,
                    'previous_stock'=>$prev,
                    'new_stock'=>$new,
                    'reference_type'=>'sale_return',
                    'reference_id'=>$sale->id,
                    'user_id'=>auth()->id(),
                    'reason'=>'Return '.$sale->receipt_number.' — '.$request->input('return_reason').($request->input('notes') ? ' ('.$request->input('notes').')' : ''),
                ]);
            }

            $sale->update([
                'status'=>'returned',
                'return_reason'=>$request->input('return_reason'),
                'returned_at'=>now(),
                'returned_by'=>auth()->id(),
            ]);

            AuditLog::create([
                'user_id'=>auth()->id(),
                'action'=>'return_sale',
                'model_type'=>Sale::class,
                'model_id'=>$sale->id,
                'old_values'=>['status'=>'completed','receipt'=>$sale->receipt_number],
                'new_values'=>['status'=>'returned','return_reason'=>$sale->return_reason],
                'ip_address'=>$request->ip(),
            ]);

            return redirect()->route('returns.index')->with('success','Sale returned: '.$sale->receipt_number.' — stock restored');
        });
    }

    public function show($encId)
    {
        $sale = Sale::with(['items.product','payments','customer','cashier','returnedBy'])->findOrFail(decIdOrRaw($encId));
        if($sale->shop_id && currentShopId() && $sale->shop_id !== currentShopId()) abort(404);
        if($sale->status !== 'returned'){
            return redirect()->route('sales.show', encId($sale->id));
        }
        $movements = StockMovement::with(['product','user'])->where('reference_type','sale_return')->where('reference_id',$sale->id)->latest()->get();
        if($movements->isEmpty()){
            // fallback: type return for this sale
            $movements = StockMovement::with(['product','user'])->where('type','return')->where('reference_id',$sale->id)->latest()->get();
        }
        return view('returns.show', compact('sale','movements'));
    }
}

