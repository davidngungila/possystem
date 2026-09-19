@extends('layouts.admin')
@section('title','Edit User')
@section('content')
<div class="page-head">
    <div><h1>Edit User</h1><p class="page-sub">{{ $user->name }} — {{ $user->email }}</p></div>
    <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm">Back</a>
</div>
<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('users.update', encId($user->id)) }}">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field @error('name') err @enderror">
                    <label class="field-label">Full Name *</label>
                    <input name="name" value="{{ old('name',$user->name) }}" required>
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('email') err @enderror">
                    <label class="field-label">Email *</label>
                    <input name="email" type="email" value="{{ old('email',$user->email) }}" required>
                    @error('email')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Phone</label>
                    <input name="phone" value="{{ old('phone',$user->phone) }}">
                </div>
                <div class="field @error('role') err @enderror">
                    <label class="field-label">Role *</label>
                    <select name="role" required id="roleSelect" onchange="toggleShopField()">
                        <option value="owner" @selected(old('role',$user->role)==='owner')>Owner — All shops</option>
                        <option value="admin" @selected(old('role',$user->role)==='admin')>Admin — All shops</option>
                        <option value="cashier" @selected(old('role',$user->role)==='cashier')>Cashier — Assigned shop</option>
                    </select>
                </div>
                <div class="field @error('shop_id') err @enderror" id="shopField">
                    <label class="field-label">Assigned Shop <span id="shopReq" style="color:var(--danger)">*</span></label>
                    <select name="shop_id" id="shopSelect">
                        <option value="">— {{ in_array($user->role,['owner','admin']) ? 'All shops (owner)' : 'Select shop' }} —</option>
                        @foreach($shops as $shop)<option value="{{ $shop->id }}" @selected(old('shop_id',$user->shop_id)==$shop->id)>{{ $shop->name }} ({{ $shop->code }})</option>@endforeach
                    </select>
                    @error('shop_id')<span class="field-err">{{ $message }}</span>@enderror
                    <span class="field-hint" id="shopHint">Cashier locked to one shop</span>
                </div>
                <div class="field">
                    <label class="field-label">New Password <span style="text-transform:none;font-weight:600;color:var(--ink-soft)">leave empty to keep</span></label>
                    <input name="password" type="password" placeholder="••••••••">
                    @error('password')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Confirm Password</label>
                    <input name="password_confirmation" type="password" placeholder="••••••••">
                </div>
                <div class="field">
                    <label class="check-row"><input type="checkbox" name="is_active" value="1" {{ old('is_active',$user->is_active) ? 'checked' : '' }}> Active</label>
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('users.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update User</button>
            </div>
        </form>
    </div>
</div>
<script>
function toggleShopField(){
    const role=document.getElementById('roleSelect').value;
    const hint=document.getElementById('shopHint');
    const req=document.getElementById('shopReq');
    if(role==='cashier'){
        req.style.display='';
        hint.textContent='Required for cashier — which shop they will sell in';
        document.getElementById('shopSelect').required=true;
    } else {
        hint.textContent='Leave empty for owner/admin — access all shops';
        req.style.display='none';
        document.getElementById('shopSelect').required=false;
    }
}
toggleShopField();
</script>
@endsection
