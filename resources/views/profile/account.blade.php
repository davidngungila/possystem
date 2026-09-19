@extends('layouts.admin')
@section('title','Account Setting')
@section('content')
<div class="page-head">
    <div><h1>Account Setting</h1><p class="page-sub">Update your account — name, email, password, profile image</p></div>
    <a href="{{ route('profile.show') }}" class="btn btn-ghost btn-sm">Back to Profile</a>
</div>

@if(session('success'))<div style="background:var(--acacia-100);border:1px solid #c8d7a8;color:var(--acacia-600);padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;">{{ session('success') }}</div>@endif
@if(session('error'))<div style="background:var(--danger-100);border:1px solid #e8b4b0;color:var(--danger);padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;">{{ session('error') }}</div>@endif

<div style="display:grid;grid-template-columns:320px 1fr;gap:16px">
    <div class="panel" style="padding:20px;text-align:center">
        @php $u = auth()->user(); $initials = strtoupper(substr($u->name ?? 'G',0,1).substr(explode(' ', $u->name ?? 'Guest')[1] ?? '',0,1)); $hasAvatar = !empty($u->avatar) && file_exists(public_path($u->avatar)); @endphp
        <div class="sb-avatar" style="width:96px;height:96px;margin:0 auto;font-size:30px;overflow:hidden;border:2px solid var(--line)">
            @if($hasAvatar)<img src="{{ asset($u->avatar) }}?v={{ time() }}" style="width:100%;height:100%;object-fit:cover" alt="Profile">@else {{ $initials }} @endif
        </div>
        <div style="margin-top:10px;font-weight:800;color:var(--coffee-900)">{{ $u->name }}</div>
        <div style="font-size:12px;color:var(--ink-soft)">{{ $u->email }}</div>
        <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" style="margin-top:14px;text-align:left">
            @csrf
            <label class="field-label">Profile Image</label>
            <input type="file" name="avatar" accept="image/*" required style="width:100%;padding:8px;border:1.5px solid var(--line);border-radius:8px;font-size:12px">
            @error('avatar')<span class="field-err">{{ $message }}</span>@enderror
            <button class="btn btn-primary btn-sm" style="width:100%;margin-top:8px">Save Profile Image</button>
            <div style="font-size:11px;color:var(--ink-soft);margin-top:4px">JPG, PNG, max 2MB. Saved to <code>public/avatars</code></div>
        </form>
    </div>
    <div class="panel">
        <div class="panel-head"><div class="panel-title">Account Details</div></div>
        <div style="padding:16px">
            <form method="POST" action="{{ route('account.update') }}">
                @csrf @method('PUT')
                <div class="form-grid">
                    <div class="field @error('name') err @enderror">
                        <label class="field-label">Full Name *</label>
                        <input name="name" value="{{ old('name', $u->name) }}" required>
                        @error('name')<span class="field-err">{{ $message }}</span>@enderror
                    </div>
                    <div class="field @error('email') err @enderror">
                        <label class="field-label">Email *</label>
                        <input name="email" type="email" value="{{ old('email', $u->email) }}" required>
                        @error('email')<span class="field-err">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label class="field-label">Phone</label>
                        <input name="phone" value="{{ old('phone', $u->phone) }}" placeholder="+255...">
                    </div>
                    <div class="field">
                        <label class="field-label">Role</label>
                        <input value="{{ $u->roleLabel() }}" readonly style="background:var(--sand-100)">
                    </div>
                    <div class="field">
                        <label class="field-label">Shop</label>
                        <input value="{{ $u->isCashier() && $u->shop ? $u->shop->name.' ('.$u->shop->code.')' : 'All Shops — Company' }}" readonly style="background:var(--sand-100)">
                    </div>
                    <div class="field @error('password') err @enderror">
                        <label class="field-label">New Password <span style="text-transform:none;font-weight:600;color:var(--ink-soft)">leave empty to keep</span></label>
                        <input name="password" type="password" placeholder="••••••••">
                        @error('password')<span class="field-err">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label class="field-label">Confirm Password</label>
                        <input name="password_confirmation" type="password" placeholder="••••••••">
                    </div>
                    <div class="field @error('current_password') err @enderror" style="grid-column:1/-1">
                        <label class="field-label">Current Password * <span style="text-transform:none;font-weight:600;color:var(--ink-soft)">required to save changes</span></label>
                        <input name="current_password" type="password" required placeholder="Enter current password to confirm">
                        @error('current_password')<span class="field-err">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="form-actions">
                    <a href="{{ route('profile.show') }}" class="btn btn-ghost">Cancel</a>
                    <button class="btn btn-primary">Save Account Setting</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>@media(max-width:900px){ div[style*="grid-template-columns:320px"]{grid-template-columns:1fr !important} }</style>
@endsection
