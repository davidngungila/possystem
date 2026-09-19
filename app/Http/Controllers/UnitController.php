<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $units = Unit::when($q, fn($qq)=> $qq->where('name','like',"%{$q}%"))->withCount('products')->latest()->paginate(12)->withQueryString();
        return view('units.index', compact('units','q'));
    }
    public function create(){ return view('units.create'); }
    public function store(Request $request)
    {
        $data = $request->validate(['name'=>'required|string|max:191','short_name'=>'nullable|string|max:20']);
        $data['is_active'] = $request->boolean('is_active', true);
        $unit = Unit::create($data);
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'create_unit','model_type'=>Unit::class,'model_id'=>$unit->id,'ip_address'=>$request->ip()]);
        return redirect()->route('units.index')->with('success','Unit created: '.$unit->name);
    }
    public function edit($encId){ $unit = Unit::findOrFail(decIdOrRaw($encId)); return view('units.edit', compact('unit')); }
    public function update(Request $request, $encId)
    {
        $unit = Unit::findOrFail(decIdOrRaw($encId));
        $data = $request->validate(['name'=>'required|string|max:191','short_name'=>'nullable|string|max:20']);
        $data['is_active'] = $request->boolean('is_active');
        $unit->update($data);
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'update_unit','model_type'=>Unit::class,'model_id'=>$unit->id,'ip_address'=>$request->ip()]);
        return redirect()->route('units.index')->with('success','Unit updated');
    }
    public function destroy(Request $request, $encId)
    {
        if(auth()->check() && auth()->user()->isCashier()) abort(403, 'Cashiers cannot delete records.');
        $unit = Unit::findOrFail(decIdOrRaw($encId));
        if($unit->products()->exists()) return back()->with('error','Cannot delete unit with products.');
        $unit->delete();
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'delete_unit','model_type'=>Unit::class,'model_id'=>$unit->id,'ip_address'=>$request->ip()]);
        return redirect()->route('units.index')->with('success','Unit deleted');
    }
}
