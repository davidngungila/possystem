<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $shopId = currentShopId();
        $customers = Customer::when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))
            ->when($q, fn($qq)=> $qq->where('name','like',"%{$q}%")->orWhere('phone','like',"%{$q}%"))
            ->latest()->paginate(15)->withQueryString();
        return view('customers.index', compact('customers','q'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:191',
            'address' => 'nullable|string|max:500',
            'type' => 'required|in:walk_in,registered',
        ]);
        $data['shop_id'] = currentShopId();
        $customer = Customer::create($data);
        \App\Models\AuditLog::create([
            'user_id'=>auth()->id(),'action'=>'create_customer','model_type'=>Customer::class,'model_id'=>$customer->id,'new_values'=>$customer->toArray(),'ip_address'=>$request->ip()
        ]);
        return redirect()->route('customers.index')->with('success','Customer created: '.$customer->name);
    }

    public function edit($encId)
    {
        $customer = Customer::findOrFail(decIdOrRaw($encId));
        if($customer->shop_id && currentShopId() && $customer->shop_id !== currentShopId()) abort(404);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $encId)
    {
        $customer = Customer::findOrFail(decIdOrRaw($encId));
        if($customer->shop_id && currentShopId() && $customer->shop_id !== currentShopId()) abort(404);
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:191',
            'address' => 'nullable|string|max:500',
            'type' => 'required|in:walk_in,registered',
        ]);
        $customer->update($data);
        \App\Models\AuditLog::create([
            'user_id'=>auth()->id(),'action'=>'update_customer','model_type'=>Customer::class,'model_id'=>$customer->id,'new_values'=>$customer->toArray(),'ip_address'=>$request->ip()
        ]);
        return redirect()->route('customers.index')->with('success','Customer updated');
    }

    public function destroy(Request $request, $encId)
    {
        if(auth()->check() && auth()->user()->isCashier()) abort(403, 'Cashiers cannot delete records.');
        $customer = Customer::findOrFail(decIdOrRaw($encId));
        if($customer->shop_id && currentShopId() && $customer->shop_id !== currentShopId()) abort(404);
        if($customer->sales()->exists()){
            return back()->with('error','Cannot delete customer with sales history.');
        }
        $customer->delete();
        \App\Models\AuditLog::create([
            'user_id'=>auth()->id(),'action'=>'delete_customer','model_type'=>Customer::class,'model_id'=>$customer->id,'ip_address'=>$request->ip()
        ]);
        return redirect()->route('customers.index')->with('success','Customer deleted');
    }
}

