<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('q',''));
        $user = auth()->user();
        $query = Shop::when($q, fn($qq)=> $qq->where('name','like',"%{$q}%")->orWhere('code','like',"%{$q}%"));
        if($user && $user->isCashier()){
            $query->where('id', $user->shop_id);
        }
        $shops = $query->withCount(['products','sales','users'])->latest()->paginate(10)->withQueryString();
        return view('shops.index', compact('shops','q'));
    }

    public function create()
    {
        if(auth()->user()->isCashier()) abort(403, 'Only owner can create shops');
        return view('shops.create');
    }

    public function store(Request $request)
    {
        if(auth()->user()->isCashier()) abort(403);
        $data = $request->validate([
            'name'=>'required|string|max:191',
            'code'=>'nullable|string|max:20|unique:shops,code',
            'address'=>'nullable|string|max:255',
            'phone'=>'nullable|string|max:30',
            'email'=>'nullable|email|max:191',
        ]);
        if (empty($data['code'])) {
            $data['code'] = strtoupper(Str::slug($data['name'], '-')).'-'.rand(100,999);
        }
        $data['is_active'] = $request->boolean('is_active', true);
        $shop = Shop::create($data);
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'create_shop','model_type'=>Shop::class,'model_id'=>$shop->id,'new_values'=>$shop->toArray(),'ip_address'=>$request->ip()]);
        return redirect()->route('shops.index')->with('success','Shop created: '.$shop->name);
    }

    public function edit($encId)
    {
        if(auth()->user()->isCashier()) abort(403);
        $shop = Shop::findOrFail(decIdOrRaw($encId));
        return view('shops.edit', compact('shop'));
    }

    public function update(Request $request, $encId)
    {
        $shop = Shop::findOrFail(decIdOrRaw($encId));
        $data = $request->validate([
            'name'=>'required|string|max:191',
            'code'=>'nullable|string|max:20|unique:shops,code,'.$shop->id,
            'address'=>'nullable|string|max:255',
            'phone'=>'nullable|string|max:30',
            'email'=>'nullable|email|max:191',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $shop->update($data);
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'update_shop','model_type'=>Shop::class,'model_id'=>$shop->id,'ip_address'=>$request->ip()]);
        return redirect()->route('shops.index')->with('success','Shop updated');
    }

    public function destroy(Request $request, $encId)
    {
        if(auth()->check() && auth()->user()->isCashier()) abort(403, 'Cashiers cannot delete records.');
        $shop = Shop::findOrFail(decIdOrRaw($encId));
        if ($shop->products()->exists() || $shop->sales()->exists()) {
            return back()->with('error','Cannot delete shop with products/sales. Deactivate instead.');
        }
        if (Shop::count() <= 1) {
            return back()->with('error','At least one shop must remain.');
        }
        $shopId = $shop->id;
        $shop->delete();
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'delete_shop','model_type'=>Shop::class,'model_id'=>$shopId,'ip_address'=>$request->ip()]);
        // clear current_shop_id if it was this shop
        if (session('current_shop_id') == $shopId) session()->forget('current_shop_id');
        return redirect()->route('shops.index')->with('success','Shop deleted');
    }

    public function switch(Request $request)
    {
        $request->validate(['shop_id'=>'required']);
        $raw = $request->input('shop_id');
        // Handle "All Shops" for owner/admin
        if($raw === 'all' || $raw === 'ALL'){
            if(auth()->user()->isCashier()) return back()->with('error','You are assigned to '.(auth()->user()->shop->name ?? 'your shop').' only.');
            session(['current_shop_id'=>'all']);
            \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'switch_shop','model_type'=>Shop::class,'model_id'=>0,'ip_address'=>$request->ip()]);
            return back()->with('success','Switched to All Shops — Company Overview');
        }
        $sid = decIdOrRaw($raw);
        $shop = Shop::findOrFail($sid);
        $user = auth()->user();
        // cashier cannot switch to other shop
        if ($user->isCashier() && (int)$user->shop_id !== (int)$shop->id) {
            return back()->with('error','You are assigned to '.($user->shop->name ?? 'your shop').' only.');
        }
        if (!$shop->is_active) {
            return back()->with('error','Shop is inactive.');
        }
        session(['current_shop_id'=>$shop->id]);
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'switch_shop','model_type'=>Shop::class,'model_id'=>$shop->id,'ip_address'=>$request->ip()]);
        return back()->with('success','Switched to '.$shop->name);
    }
}
