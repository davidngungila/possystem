<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $shopId = currentShopId();
        $suppliers = Supplier::when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))
            ->when($q, fn($qq)=> $qq->where('name','like',"%{$q}%")->orWhere('phone','like',"%{$q}%"))
            ->withCount('purchases')
            ->latest()->paginate(12)->withQueryString();
        return view('suppliers.index', compact('suppliers','q'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:191',
            'address' => 'nullable|string|max:500',
            'tin' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:191',
        ]);
        $data['shop_id'] = currentShopId();
        $supplier = Supplier::create($data);
        \App\Models\AuditLog::create([
            'user_id'=>auth()->id(),'action'=>'create_supplier','model_type'=>Supplier::class,'model_id'=>$supplier->id,'new_values'=>$supplier->toArray(),'ip_address'=>$request->ip()
        ]);
        return redirect()->route('suppliers.index')->with('success','Supplier created: '.$supplier->name);
    }

    public function edit($encId)
    {
        $supplier = Supplier::findOrFail(decIdOrRaw($encId));
        if($supplier->shop_id && currentShopId() && $supplier->shop_id !== currentShopId()) abort(404);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $encId)
    {
        $supplier = Supplier::findOrFail(decIdOrRaw($encId));
        if($supplier->shop_id && currentShopId() && $supplier->shop_id !== currentShopId()) abort(404);
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:191',
            'address' => 'nullable|string|max:500',
            'tin' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:191',
        ]);
        $supplier->update($data);
        \App\Models\AuditLog::create([
            'user_id'=>auth()->id(),'action'=>'update_supplier','model_type'=>Supplier::class,'model_id'=>$supplier->id,'new_values'=>$supplier->toArray(),'ip_address'=>$request->ip()
        ]);
        return redirect()->route('suppliers.index')->with('success','Supplier updated');
    }

    public function destroy(Request $request, $encId)
    {
        if(auth()->check() && auth()->user()->isCashier()) abort(403, 'Cashiers cannot delete records.');
        $supplier = Supplier::findOrFail(decIdOrRaw($encId));
        if($supplier->shop_id && currentShopId() && $supplier->shop_id !== currentShopId()) abort(404);
        if($supplier->purchases()->exists()){
            return back()->with('error','Cannot delete supplier with purchase history. Set inactive instead.');
        }
        $supplier->delete();
        \App\Models\AuditLog::create([
            'user_id'=>auth()->id(),'action'=>'delete_supplier','model_type'=>Supplier::class,'model_id'=>$supplier->id,'ip_address'=>$request->ip()
        ]);
        return redirect()->route('suppliers.index')->with('success','Supplier deleted');
    }
}

