@extends('layouts.app')
@section('title','Login')
@section('content')
@php $shopName = \App\Models\Setting::getValue('shop_name', config('app.name','SHOP POS')); @endphp
<div style="max-width:480px;margin:0 auto;padding:12px 24px 0">
    <div class="panel">
            <div class="panel-head">
                <div>
                    <div class="panel-title">Login</div>
                    <div class="panel-sub">Sign in to continue to POS</div>
                </div>
                <span class="tag tag-green">Secure</span>
            </div>
            <div class="panel-body">
                @if(session('error'))<div style="background:var(--danger-100);border:1px solid #e8b4b0;color:var(--danger);padding:10px 12px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:12px;">{{ session('error') }}</div>@endif
                @if(session('success'))<div style="background:var(--acacia-100);border:1px solid #c8d7a8;color:var(--acacia-600);padding:10px 12px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:12px;">{{ session('success') }}</div>@endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    <div class="form-grid" style="display:flex;flex-direction:column;gap:16px;">
                        <div class="field @error('email') err @enderror @error('login') err @enderror">
                            <label class="field-label">Email *</label>
                            <input id="email" name="email" type="email" value="{{ old('email', old('login')) }}" required autofocus placeholder="admin@shop.co.tz">
                            @error('email')<span class="field-err">{{ $message }}</span>@enderror
                            @error('login')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('password') err @enderror">
                            <label class="field-label">Password *</label>
                            <div style="display:flex;align-items:center;gap:8px;border:1.5px solid var(--line);border-radius:10px;background:#fff;padding:4px 8px 4px 12px">
                                <input id="password" name="password" type="password" required placeholder="••••••••" style="flex:1;border:none;outline:none;background:transparent;padding:8px 0;font-size:14px">
                                <button type="button" onclick="togglePwd(this)" aria-label="Toggle password visibility" style="flex:none;background:var(--sand-100);border:1px solid var(--line);border-radius:6px;padding:7px 10px;display:inline-flex;align-items:center;justify-content:center;color:var(--coffee-700);cursor:pointer;"><svg class="eye-open" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg><svg class="eye-off" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.53 9.53a3 3 0 1 0 4.24 4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg></button>
                            </div>
                            @error('password')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <label class="check-row" style="margin-top:14px;"><input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}> Remember me <span style="margin-left:auto;font-size:12px;color:var(--ink-soft);cursor:pointer" onclick="if(window.toast) toast('Contact owner','info')">Forgot?</span></label>
                    <button type="submit" class="btn btn-primary" style="width:100%;margin-top:16px;display:inline-flex;align-items:center;justify-content:center;gap:8px;">Sign In <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></button>
                </form>
            </div>
        </div>
</div>
<style>footer{display:none !important}</style>
<script>
function fillDemo(){
    document.getElementById('email').value='admin@shop.co.tz';
    document.getElementById('password').value='admin123';
    if(window.toast) toast('Demo filled','success');
}
function togglePwd(btn){
    const p=document.getElementById('password');
    const willShow = p.type==='password';
    p.type = willShow ? 'text' : 'password';
    if(btn){
        const eyeOpen = btn.querySelector('.eye-open');
        const eyeOff = btn.querySelector('.eye-off');
        if(eyeOpen && eyeOff){
            eyeOpen.style.display = willShow ? 'none' : 'block';
            eyeOff.style.display = willShow ? 'block' : 'none';
        }
    }
}
document.getElementById('loginForm')?.addEventListener('submit', function(){ if(window.showPageLoader) showPageLoader(); });
</script>
@endsection
