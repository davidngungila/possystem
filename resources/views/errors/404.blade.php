@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : (auth()->check() ? 'layouts.applicant' : 'layouts.app'))
@section('title','404 — Page Not Found')
@section('content')
<div style="max-width:780px;margin:0 auto;padding:48px 24px 60px;text-align:center;">
    <div style="display:inline-flex;align-items:center;gap:8px;background:var(--terracotta-100);color:var(--terracotta-600);padding:6px 14px;border-radius:20px;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">Error 404</div>
    <div style="margin-top:18px;width:88px;height:88px;border-radius:18px;background:var(--coffee-900);display:flex;align-items:center;justify-content:center;margin-left:auto;margin-right:auto;color:#fff;font-weight:800;font-size:28px;box-shadow:var(--shadow-md)">404</div>
    <h1 style="margin-top:18px;font-size:28px;font-weight:800;color:var(--coffee-900)">Page not found</h1>
    <p style="margin-top:10px;color:var(--ink-soft);font-size:15px;line-height:1.6;max-width:560px;margin-left:auto;margin-right:auto;">The page you’re looking for doesn’t exist, was moved, or the link has expired. Check the URL or return to the homepage. If you followed an encrypted link, it may have expired.</p>
    <div style="margin-top:22px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Admin Dashboard</a>
            @else
                <a href="{{ route('applicant.dashboard') }}" class="btn btn-primary">My Application</a>
            @endif
        @else
            <a href="{{ route('home') }}" class="btn btn-primary">Go to Homepage</a>
            <a href="{{ route('public.calendar') }}" class="btn btn-ghost">Admission Calendar</a>
        @endauth
        <button onclick="history.back()" class="btn btn-ghost">Go Back</button>
    </div>
</div>
@endsection
