<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $brands = Brand::when($q, fn($qq)=> $qq->where('name','like',"%{$q}%"))->withCount('products')->latest()->paginate(12)->withQueryString();
        return view('brands.index', compact('brands','q'));
    }
    public function create(){ return view('brands.create'); }
    public function store(Request $request)
    {
        $data = $request->validate(['name'=>'required|string|max:191','description'=>'nullable|string|max:500']);
        $data['slug'] = Str::slug($data['name']).'-'.Str::random(4);
        $data['is_active'] = $request->boolean('is_active', true);
        $brand = Brand::create($data);
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'create_brand','model_type'=>Brand::class,'model_id'=>$brand->id,'ip_address'=>$request->ip()]);
        return redirect()->route('brands.index')->with('success','Brand created: '.$brand->name);
    }
    public function edit($encId){ $brand = Brand::findOrFail(decIdOrRaw($encId)); return view('brands.edit', compact('brand')); }
    public function update(Request $request, $encId)
    {
        $brand = Brand::findOrFail(decIdOrRaw($encId));
        $data = $request->validate(['name'=>'required|string|max:191','description'=>'nullable|string|max:500']);
        $data['is_active'] = $request->boolean('is_active');
        $brand->update($data);
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'update_brand','model_type'=>Brand::class,'model_id'=>$brand->id,'ip_address'=>$request->ip()]);
        return redirect()->route('brands.index')->with('success','Brand updated');
    }
    public function destroy(Request $request, $encId)
    {
        if(auth()->check() && auth()->user()->isCashier()) abort(403, 'Cashiers cannot delete records.');
        $brand = Brand::findOrFail(decIdOrRaw($encId));
        if($brand->products()->exists()) return back()->with('error','Cannot delete brand with products.');
        $brand->delete();
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'delete_brand','model_type'=>Brand::class,'model_id'=>$brand->id,'ip_address'=>$request->ip()]);
        return redirect()->route('brands.index')->with('success','Brand deleted');
    }
}
