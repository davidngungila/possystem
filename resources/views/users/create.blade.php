@extends('layouts.admin')
@section('title','Add User')
@section('content')
<div class="page-head">
    <div><h1>Add User</h1><p class="page-sub">Owner has full access — Admin manages shop — Cashier runs POS only</p></div>
    <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm">Back</a>
</div>
<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field @error('name') err @enderror">
                    <label class="field-label">Full Name *</label>
                    <input name="name" value="{{ old('name') }}" required placeholder="e.g. Jane Owner">
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('email') err @enderror">
                    <label class="field-label">Email *</label>
                    <input name="email" type="email" value="{{ old('email') }}" required placeholder="user@shop.co.tz">
                    @error('email')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('phone') err @enderror">
                    <label class="field-label">Phone</label>
                    <input name="phone" value="{{ old('phone') }}" placeholder="+255...">
                </div>
                <div class="field @error('role') err @enderror">
                    <label class="field-label">Role *</label>
                    <select name="role" required id="roleSelect" onchange="toggleShopField()">
                        <option value="owner" @selected(old('role')==='owner')>Owner — Full access (all shops)</option>
                        <option value="admin" @selected(old('role','admin')==='admin')>Admin — Manager (all shops)</option>
                        <option value="cashier" @selected(old('role')==='cashier')>Cashier — POS only (assigned shop)</option>
                    </select>
                    @error('role')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('shop_id') err @enderror" id="shopField">
                    <label class="field-label">Assigned Shop <span id="shopReq" style="color:var(--danger)">*</span></label>
                    <select name="shop_id" id="shopSelect">
                        <option value="">— Select shop —</option>
                        @foreach($shops as $shop)<option value="{{ $shop->id }}" @selected(old('shop_id')==$shop->id)>{{ $shop->name }} ({{ $shop->code }})</option>@endforeach
                    </select>
                    @error('shop_id')<span class="field-err">{{ $message }}</span>@enderror
                    <span class="field-hint" id="shopHint">Required for cashier — which shop they will sell in</span>
                </div>
                <div class="field @error('password') err @enderror">
                    <label class="field-label">Password *</label>
                    <input name="password" type="password" required placeholder="••••••••">
                    @error('password')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Confirm Password *</label>
                    <input name="password_confirmation" type="password" required placeholder="••••••••">
                </div>
                <div class="field">
                    <label class="check-row"><input type="checkbox" name="is_active" value="1" {{ old('is_active',true) ? 'checked' : '' }}> Active</label>
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('users.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Create User</button>
            </div>
        </form>
    </div>
</div>
<script>
function toggleShopField(){
    const role=document.getElementById('roleSelect').value;
    const field=document.getElementById('shopField');
    const req=document.getElementById('shopReq');
    const hint=document.getElementById('shopHint');
    if(role==='cashier'){
        field.style.display='';
        req.style.display='';
        hint.textContent='Required for cashier — which shop they will sell in';
        document.getElementById('shopSelect').required=true;
    } else {
        hint.textContent='Optional for owner/admin — leave empty for all shops access';
        req.style.display='none';
        document.getElementById('shopSelect').required=false;
    }
}
toggleShopField();
</script>
@endsection
