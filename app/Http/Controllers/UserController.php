<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('shop')->latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $shops = \App\Models\Shop::where('is_active',1)->orderBy('name')->get();
        return view('users.create', compact('shops'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'role' => 'required|in:owner,admin,cashier',
            'shop_id' => 'nullable|exists:shops,id',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if($data['role']==='cashier' && empty($data['shop_id'])){
            return back()->withErrors(['shop_id'=>'Cashier must be assigned to a shop.'])->withInput();
        }
        if(in_array($data['role'],['owner','admin']) && empty($data['shop_id'])){
            $data['shop_id'] = null;
        }
        $data['is_active'] = $request->boolean('is_active', true);
        $user = User::create($data);
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_user',
            'model_type' => User::class,
            'model_id' => $user->id,
            'new_values' => $user->only(['name','email','role']),
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('users.index')->with('success','User created: '.$user->name.' ('.$user->roleLabel().')');
    }

    public function edit($encId)
    {
        $user = User::findOrFail(decIdOrRaw($encId));
        $shops = \App\Models\Shop::where('is_active',1)->orderBy('name')->get();
        return view('users.edit', compact('user','shops'));
    }

    public function update(Request $request, $encId)
    {
        $user = User::findOrFail(decIdOrRaw($encId));
        $old = $user->only(['name','email','role','is_active','shop_id']);
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'email' => ['required','email', Rule::unique('users','email')->ignore($user->id)],
            'phone' => 'nullable|string|max:30',
            'role' => 'required|in:owner,admin,cashier',
            'shop_id' => 'nullable|exists:shops,id',
            'password' => 'nullable|string|min:6|confirmed',
            'is_active' => 'boolean',
        ]);
        if($data['role']==='cashier' && empty($data['shop_id'])){
            return back()->withErrors(['shop_id'=>'Cashier must be assigned to a shop.'])->withInput();
        }
        if(in_array($data['role'],['owner','admin']) && empty($data['shop_id'])){
            $data['shop_id'] = null;
        }
        if (empty($data['password'])) unset($data['password']);
        $data['is_active'] = $request->boolean('is_active');
        $user->update($data);
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_user',
            'model_type' => User::class,
            'model_id' => $user->id,
            'old_values' => $old,
            'new_values' => $user->only(['name','email','role','is_active']),
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('users.index')->with('success','User updated');
    }

    public function destroy(Request $request, $encId)
    {
        if(auth()->check() && auth()->user()->isCashier()) abort(403, 'Cashiers cannot delete records.');
        $user = User::findOrFail(decIdOrRaw($encId));
        if ($user->id === auth()->id()) {
            return back()->with('error','You cannot delete your own account.');
        }
        $old = $user->only(['name','email','role']);
        $deletedId = $user->id;
        $user->delete();
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_user',
            'model_type' => User::class,
            'model_id' => $deletedId,
            'old_values' => $old,
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('users.index')->with('success','User deleted');
    }
}
