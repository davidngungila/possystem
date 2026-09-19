@extends('layouts.admin')
@section('title','Profile')
@section('content')
<div class="page-head">
    <div><h1>My Profile</h1><p class="page-sub">Account details & shop assignment</p></div>
    <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">Back to Dashboard</a>
</div>

<div style="display:grid;grid-template-columns:320px 1fr;gap:16px">
    <div class="panel" style="padding:20px;text-align:center">
        @php $u = auth()->user(); $initials = strtoupper(substr($u->name ?? 'G',0,1).substr(explode(' ', $u->name ?? 'Guest')[1] ?? '',0,1)); $hasAvatar = !empty($u->avatar) && file_exists(public_path($u->avatar)); @endphp
        <div class="sb-avatar" style="width:84px;height:84px;margin:0 auto;font-size:28px;overflow:hidden">
            @if($hasAvatar)<img src="{{ asset($u->avatar) }}" style="width:100%;height:100%;object-fit:cover" alt="Profile">@else {{ $initials }} @endif
        </div>
        <div style="margin-top:12px;font-weight:800;font-size:18px;color:var(--coffee-900)">{{ $u->name }}</div>
        <div style="font-size:13px;color:var(--ink-soft)">{{ $u->email }}</div>
        <div style="margin-top:8px;display:flex;gap:6px;justify-content:center;flex-wrap:wrap">
            <span class="tag {{ $u->role==='owner' ? 'tag-terracotta' : ($u->role==='admin' ? 'tag-gold' : 'tag-green') }}">{{ $u->roleLabel() }}</span>
            @if($u->isCashier() && $u->shop)<span class="tag tag-grey">{{ $u->shop->name }}</span>@elseif($u->isCashier())<span class="tag tag-grey">No Shop</span>@else<span class="tag tag-green">All Shops</span>@endif
            @if($u->is_active)<span class="tag tag-green">Active</span>@else<span class="tag tag-red">Inactive</span>@endif
        </div>
        <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" style="margin-top:12px;display:flex;gap:6px;flex-direction:column;align-items:center">
            @csrf
            <input type="file" name="avatar" accept="image/*" required style="font-size:12px;max-width:200px">
            <button class="btn btn-ghost btn-sm">Upload Profile Image</button>
            @error('avatar')<span class="field-err">{{ $message }}</span>@enderror
        </form>
        <div style="margin-top:14px;display:flex;gap:8px;justify-content:center">
            <a href="{{ route('users.edit', encId($u->id)) }}" class="btn btn-ghost btn-sm">Edit Profile</a>
            <a href="{{ route('account.setting') }}" class="btn btn-primary btn-sm">Account Setting</a>
        </div>
    </div>
    <div style="display:flex;flex-direction:column;gap:16px">
        <div class="panel">
            <div class="panel-head"><div class="panel-title">Account Details</div></div>
            <div style="padding:16px">
                <div class="kv">
                    <div class="kv-row"><span class="k">Full Name</span><span class="v">{{ $u->name }}</span></div>
                    <div class="kv-row"><span class="k">Email</span><span class="v">{{ $u->email }}</span></div>
                    <div class="kv-row"><span class="k">Phone</span><span class="v">{{ $u->phone ?? '—' }}</span></div>
                    <div class="kv-row"><span class="k">Role</span><span class="v">{{ $u->roleLabel() }} ({{ $u->role }})</span></div>
                    <div class="kv-row"><span class="k">Shop</span><span class="v">@if($u->isCashier() && $u->shop){{ $u->shop->name }} ({{ $u->shop->code }})@elseif($u->isCashier()) No Shop Assigned @else All Shops — Company (all branches) @endif</span></div>
                    <div class="kv-row"><span class="k">Status</span><span class="v">@if($u->is_active)<span class="tag tag-green">Active</span>@else<span class="tag tag-red">Inactive</span>@endif</span></div>
                    <div class="kv-row"><span class="k">Member Since</span><span class="v">{{ $u->created_at->format('d/m/Y H:i') }}</span></div>
                </div>
            </div>
        </div>
        <div class="panel" style="padding:16px;background:var(--sand-50)">
            <div style="font-weight:800;color:var(--coffee-900)">Quick Links</div>
            <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap">
                <a href="{{ route('account.setting') }}" class="btn btn-ghost btn-sm">Account Setting</a>
                <a href="{{ route('shops.index') }}" class="btn btn-ghost btn-sm">Shops</a>
                <a href="{{ route('audit-logs.index') }}" class="btn btn-ghost btn-sm">Audit Logs</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf<button class="btn btn-ghost btn-sm" style="color:var(--danger)">Logout</button></form>
            </div>
        </div>
    </div>
</div>
<style>@media(max-width:900px){ div[style*="grid-template-columns:320px"]{grid-template-columns:1fr !important} }</style>
@endsection

