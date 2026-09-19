<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $categories = Category::when($q, fn($qq)=> $qq->where('name','like',"%{$q}%"))
            ->withCount('products')->latest()->paginate(12)->withQueryString();
        return view('categories.index', compact('categories','q'));
    }

    public function create(){ return view('categories.create'); }

    public function store(Request $request)
    {
        $data = $request->validate(['name'=>'required|string|max:191','description'=>'nullable|string|max:500']);
        $data['slug'] = Str::slug($data['name']).'-'.Str::random(4);
        $data['is_active'] = $request->boolean('is_active', true);
        $cat = Category::create($data);
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'create_category','model_type'=>Category::class,'model_id'=>$cat->id,'new_values'=>$cat->toArray(),'ip_address'=>$request->ip()]);
        return redirect()->route('categories.index')->with('success','Category created: '.$cat->name);
    }

    public function edit($encId)
    {
        $category = Category::findOrFail(decIdOrRaw($encId));
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, $encId)
    {
        $category = Category::findOrFail(decIdOrRaw($encId));
        $data = $request->validate(['name'=>'required|string|max:191','description'=>'nullable|string|max:500']);
        $data['is_active'] = $request->boolean('is_active');
        $category->update($data);
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'update_category','model_type'=>Category::class,'model_id'=>$category->id,'ip_address'=>$request->ip()]);
        return redirect()->route('categories.index')->with('success','Category updated');
    }

    public function destroy(Request $request, $encId)
    {
        if(auth()->check() && auth()->user()->isCashier()) abort(403, 'Cashiers cannot delete records.');
        $category = Category::findOrFail(decIdOrRaw($encId));
        if($category->products()->exists()){
            return back()->with('error','Cannot delete category with products. Set inactive instead.');
        }
        $category->delete();
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'delete_category','model_type'=>Category::class,'model_id'=>$category->id,'ip_address'=>$request->ip()]);
        return redirect()->route('categories.index')->with('success','Category deleted');
    }
}
