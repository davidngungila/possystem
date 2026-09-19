@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : (auth()->check() ? 'layouts.pos' : 'layouts.app'))
@section('title','500 — Server Error')
@section('content')
<div style="max-width:780px;margin:0 auto;padding:48px 24px 60px;text-align:center;">
    <div style="display:inline-flex;align-items:center;gap:8px;background:var(--danger-100);color:var(--danger);padding:6px 14px;border-radius:20px;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">Error 500</div>
    <div style="margin-top:18px;width:88px;height:88px;border-radius:18px;background:var(--danger);display:flex;align-items:center;justify-content:center;margin-left:auto;margin-right:auto;color:#fff;font-weight:800;font-size:28px;">500</div>
    <h1 style="margin-top:18px;font-size:28px;font-weight:800;color:var(--coffee-900)">Internal server error</h1>
    <p style="margin-top:10px;color:var(--ink-soft);font-size:15px;line-height:1.6;max-width:560px;margin-left:auto;margin-right:auto;">Something went wrong on our side. Our team has been notified. Please try again shortly. If the problem persists, contact the Admissions Office.</p>
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
        <button onclick="location.reload()" class="btn btn-ghost">Reload Page</button>
        <button onclick="history.back()" class="btn btn-ghost">Go Back</button>
    </div>
    <div class="panel" style="margin-top:28px;text-align:left;">
        <div class="panel-body" style="font-size:13px;color:var(--ink-soft);">If you were submitting an application, your data is safe — please try again. Reference: <span class="cell-mono">{{ request()->path() }}</span></div>
    </div>
</div>
@endsection
