@extends('layouts.auth')
@section('title','Register')
@section('content')
<div style="text-align:center;margin-bottom:18px;">
    <div style="display:inline-flex;align-items:center;gap:8px;background:var(--acacia-100);color:var(--acacia-600);padding:6px 12px;border-radius:20px;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">New Shop Account</div>
    <h1 style="margin-top:12px;font-size:22px;font-weight:800;color:var(--coffee-900);">Create account</h1>
    <p style="margin-top:6px;color:var(--ink-soft);font-size:13px;">Owner / Manager / Cashier — role assigned by owner.</p>
</div>
@if($errors->any())<div class="alert alert-error"><ul style="margin:0 0 0 16px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('register') }}" style="margin:0">
    @csrf
    <div class="field @error('name') err @enderror">
        <label for="name">Full Name *</label>
        <input id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. David M." style="font-size:14px">
        @error('name')<div style="color:var(--danger);font-size:12px;font-weight:600;margin-top:4px;">{{ $message }}</div>@enderror
    </div>
    <div class="field @error('email') err @enderror">
        <label for="email">Email *</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required placeholder="you@shop.co.tz">
        @error('email')<div style="color:var(--danger);font-size:12px;font-weight:600;margin-top:4px;">{{ $message }}</div>@enderror
    </div>
    <div class="field @error('password') err @enderror" style="position:relative;">
        <label for="password">Password *</label>
        <input id="password" name="password" type="password" required placeholder="••••••••" style="padding-right:42px">
        @error('password')<div style="color:var(--danger);font-size:12px;font-weight:600;margin-top:4px;">{{ $message }}</div>@enderror
    </div>
    <div class="field">
        <label for="password_confirmation">Confirm Password *</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="••••••••">
    </div>
    <button type="submit" class="btn" style="margin-top:16px;display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%;">Create Account <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></button>
    <p style="font-size:13px;text-align:center;margin-top:14px;color:var(--ink-soft);">Have an account? <a href="{{ route('login') }}" style="color:var(--terracotta-600);font-weight:700;">Sign in</a></p>
    <p style="text-align:center;margin-top:8px;"><a href="{{ route('home') }}" style="font-size:12px;color:var(--ink-soft);display:inline-flex;align-items:center;gap:4px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg> Back to home</a></p>
</form>
@endsection
