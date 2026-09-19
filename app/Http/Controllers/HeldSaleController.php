<?php

namespace App\Http\Controllers;

use App\Models\HeldSale;
use Illuminate\Http\Request;

class HeldSaleController extends Controller
{
    public function index()
    {
        $shopId = currentShopId();
        $base = HeldSale::with('cashier')->when($shopId, fn($q)=>$q->where('shop_id',$shopId));
        $heldSales = (clone $base)->where('cashier_id', auth()->id())->latest()->paginate(10);
        // also show all for owner/admin
        if (auth()->user()->isAdministrator()) {
            $heldSales = (clone $base)->latest()->paginate(10);
        }
        return view('held.index', compact('heldSales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|integer|exists:products,id',
            'cart.*.name' => 'required|string',
            'cart.*.qty' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'reference' => 'nullable|string|max:50',
        ]);

        $held = HeldSale::create([
            'shop_id' => currentShopId(),
            'cashier_id' => auth()->id(),
            'reference' => $request->input('reference') ?: 'HOLD-'.strtoupper(\Illuminate\Support\Str::random(6)),
            'cart' => $request->input('cart'),
            'total' => $request->input('total'),
        ]);
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'hold_sale','model_type'=>HeldSale::class,'model_id'=>$held->id,'new_values'=>['reference'=>$held->reference,'total'=>$held->total],'ip_address'=>$request->ip()]);

        return response()->json(['success'=>true,'id'=>encId($held->id),'reference'=>$held->reference]);
    }

    public function resume($encId)
    {
        $held = HeldSale::findOrFail(decIdOrRaw($encId));
        if($held->shop_id && currentShopId() && $held->shop_id !== currentShopId()) abort(404);
        // store cart in session to resume in POS
        session(['held_cart' => $held->cart, 'held_reference' => $held->reference]);
        $held->delete();
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'resume_held_sale','model_type'=>HeldSale::class,'model_id'=>$held->id,'ip_address'=>$request->ip()]);
        return redirect()->route('pos.create')->with('success','Held sale resumed: '.($held->reference ?? ''));
    }

    public function destroy($encId)
    {
        if(auth()->check() && auth()->user()->isCashier()) abort(403, 'Cashiers cannot delete records.');
        $held = HeldSale::findOrFail(decIdOrRaw($encId));
        if($held->shop_id && currentShopId() && $held->shop_id !== currentShopId()) abort(404);
        // only owner or own cashier can delete
        if (!auth()->user()->isAdministrator() && $held->cashier_id !== auth()->id()) {
            abort(403);
        }
        $held->delete();
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'delete_held_sale','model_type'=>HeldSale::class,'model_id'=>$held->id,'ip_address'=>$request->ip()]);
        return back()->with('success','Held sale deleted');
    }
}

