<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $shopId = currentShopId();
        $purchases = Purchase::with(['supplier','creator'])
            ->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))
            ->when($q, fn($qq)=> $qq->where('invoice_number','like',"%{$q}%"))
            ->latest()->paginate(12)->withQueryString();
        return view('purchases.index', compact('purchases','q'));
    }

    public function create()
    {
        $shopId = currentShopId();
        $suppliers = Supplier::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->where('is_active',1)->orderBy('name')->get();
        $products = Product::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->where('status','active')->orderBy('name')->get();
        $autoBatchNumber = 'BATCH-' . date('Y') . '-' . str_pad((Purchase::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);
        return view('purchases.create', compact('suppliers','products','autoBatchNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'status' => 'required|in:draft,ordered,received,partially_received,cancelled',
            'batch_number' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.buying_price' => 'required|numeric|min:0',
            'items.*.selling_price' => 'nullable|numeric|min:0',
            'items.*.expiry_date' => 'nullable|date',
        ]);

        return DB::transaction(function() use ($request){
            $total = collect($request->input('items'))->sum(fn($it)=> $it['quantity'] * $it['buying_price']);

            $purchase = Purchase::create([
                'shop_id' => currentShopId(),
                'supplier_id' => $request->input('supplier_id'),
                'invoice_number' => $request->input('invoice_number'),
                'batch_number' => $request->input('batch_number'),
                'expiry_date' => $request->input('expiry_date'),
                'purchase_date' => $request->input('purchase_date') ?? now(),
                'status' => $request->input('status'),
                'total_amount' => $total,
                'created_by' => auth()->id(),
            ]);

            foreach($request->input('items') as $it){
                $product = Product::findOrFail($it['product_id']);
                $qty = (int)$it['quantity'];
                $price = (float)$it['buying_price'];
                $sellingPrice = $it['selling_price'] ?? null;
                $expiryDate = $it['expiry_date'] ?? null;
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'buying_price' => $price,
                    'selling_price' => $sellingPrice,
                    'total' => $qty * $price,
                    'batch_number' => $request->input('batch_number'),
                    'expiry_date' => $expiryDate,
                ]);

                // Stock IN only when received
                if(in_array($request->input('status'), ['received','partially_received'])){
                    $prev = $product->current_stock;
                    $new = $prev + $qty;
                    $product->increment('current_stock', $qty);
                    // Update product selling price if provided
                    if($sellingPrice !== null && $sellingPrice > 0){
                        $product->update(['selling_price' => $sellingPrice]);
                    }
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'in',
                        'quantity' => $qty,
                        'previous_stock' => $prev,
                        'new_stock' => $new,
                        'reference_type' => 'purchase',
                        'reference_id' => $purchase->id,
                        'user_id' => auth()->id(),
                        'reason' => 'Purchase '.$purchase->invoice_number.' received',
                    ]);
                }
            }

            \App\Models\AuditLog::create([
                'user_id'=>auth()->id(),'action'=>'create_purchase','model_type'=>Purchase::class,'model_id'=>$purchase->id,'new_values'=>$purchase->toArray(),'ip_address'=>$request->ip()
            ]);

            return redirect()->route('purchases.index')->with('success','Purchase created: '.($purchase->invoice_number ?? '#'.$purchase->id));
        });
    }

    public function show($encId)
    {
        $purchase = Purchase::with(['supplier','items.product','creator'])->findOrFail(decIdOrRaw($encId));
        if($purchase->shop_id && currentShopId() && $purchase->shop_id !== currentShopId()) abort(404);
        return view('purchases.show', compact('purchase'));
    }

    public function destroy(Request $request, $encId)
    {
        if(auth()->check() && auth()->user()->isCashier()) abort(403, 'Cashiers cannot delete records.');
        $purchase = Purchase::findOrFail(decIdOrRaw($encId));
        if($purchase->shop_id && currentShopId() && $purchase->shop_id !== currentShopId()) abort(404);
        // only allow delete if draft or cancelled and no stock movements? For MVP allow if not received
        if(in_array($purchase->status, ['received','partially_received'])){
            return back()->with('error','Cannot delete received purchase. Cancel it instead.');
        }
        $purchase->delete();
        \App\Models\AuditLog::create([
            'user_id'=>auth()->id(),'action'=>'delete_purchase','model_type'=>Purchase::class,'model_id'=>$purchase->id,'ip_address'=>$request->ip()
        ]);
        return redirect()->route('purchases.index')->with('success','Purchase deleted');
    }
}

