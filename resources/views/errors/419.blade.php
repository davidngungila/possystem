@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : (auth()->check() ? 'layouts.applicant' : 'layouts.app'))
@section('title','419 — Page Expired')
@section('content')
<div style="max-width:780px;margin:0 auto;padding:48px 24px 60px;text-align:center;">
    <div style="display:inline-flex;align-items:center;gap:8px;background:var(--gold-100);color:#8a6418;padding:6px 14px;border-radius:20px;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">Error 419</div>
    <div style="margin-top:18px;width:88px;height:88px;border-radius:18px;background:var(--gold-500);display:flex;align-items:center;justify-content:center;margin-left:auto;margin-right:auto;color:#fff;font-weight:800;font-size:28px;">419</div>
    <h1 style="margin-top:18px;font-size:28px;font-weight:800;color:var(--coffee-900)">Page expired</h1>
    <p style="margin-top:10px;color:var(--ink-soft);font-size:15px;line-height:1.6;max-width:560px;margin-left:auto;margin-right:auto;">Your session has expired due to inactivity or invalid token. Please refresh and try again. If you were submitting a form, go back and resubmit.</p>
    <div style="margin-top:22px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Admin Dashboard</a>
            @else
                <a href="{{ route('applicant.dashboard') }}" class="btn btn-primary">My Application</a>
            @endif
        @else
            <a href="{{ route('home') }}" class="btn btn-primary">Go to Homepage</a>
        @endauth
        <button onclick="location.reload()" class="btn btn-ghost">Refresh Page</button>
        <button onclick="history.back()" class="btn btn-ghost">Go Back</button>
    </div>
</div>
@endsection
