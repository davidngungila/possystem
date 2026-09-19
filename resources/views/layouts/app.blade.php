<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $shopNameApp = \App\Models\Setting::getValue('shop_name', \App\Models\Setting::getValue('university_name', config('app.name','SHOP POS')));
    @endphp
    <title>@yield('title', $shopNameApp) — Single-Shop POS</title>
    @include('layouts.partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{
            --sand-50:#F5F8FC;--sand-100:#ECF1F6;--sand-200:#DCE5EE;
            --coffee-900:#052B3D;--coffee-800:#07364F;--coffee-700:#0A4260;--coffee-500:#285B78;--coffee-300:#5E88A3;
            --terracotta-600:#0066CC;--terracotta-500:#1B80E0;--terracotta-100:#E4F0FA;
            --acacia-600:#2E7D6B;--acacia-500:#3E8F7C;--acacia-100:#E1EFEA;
            --gold-500:#E8B82F;--gold-100:#F9EFD2;
            --ink:#052C3F;--ink-soft:#40637A;--line:#C9D6E2;--white:#FFFFFF;
            --danger:#B33A3A;--danger-100:#F6DCDA;--success:#3F6B3F;
            --radius-sm:8px;--radius-md:14px;--radius-lg:20px;
            --shadow-sm:0 1px 2px rgba(42,27,16,.08);--shadow-md:0 8px 24px rgba(42,27,16,.10);--shadow-lg:0 20px 48px rgba(42,27,16,.18);
        }
        *{box-sizing:border-box;}html,body{height:100%;}
        body{margin:0;font-family:'Raleway',sans-serif;background:var(--sand-50);color:var(--ink);-webkit-font-smoothing:antialiased;overflow-x:hidden;padding-top:142px;}
        @media(max-width:900px){body{padding-top:168px;}}
        @media(max-width:768px){body{padding-top:196px;}}
        h1,h2,h3,h4{font-family:'Raleway',sans-serif;margin:0;color:var(--coffee-900);letter-spacing:-0.01em;}
        a{color:inherit;text-decoration:none;}button{font-family:inherit;cursor:pointer;}input,select,textarea{font-family:inherit;}
        ::-webkit-scrollbar{width:9px;height:9px;}::-webkit-scrollbar-thumb{background:var(--coffee-300);border-radius:10px;}
        ::selection{background:var(--terracotta-100);color:var(--coffee-900);}

        /* Full header — fixed */
        .p-sticky-header{position:fixed;top:0;left:0;right:0;z-index:100;box-shadow:0 2px 12px rgba(42,27,16,.12);}
        .p-nav{position:relative;background:rgba(251,247,239,.92);backdrop-filter:blur(10px);border-bottom:1px solid var(--line);}
        .p-nav-inner{max-width:1280px;margin:0 auto;padding:0 24px;height:72px;display:flex;align-items:center;justify-content:space-between;gap:16px;}
        .p-brand{display:flex;align-items:center;gap:12px;}
        .p-mark{width:40px;height:40px;border-radius:10px;background:linear-gradient(155deg,var(--terracotta-600),var(--gold-500));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;}
        .p-brand-text strong{color:var(--coffee-900);font-size:15px;display:block;line-height:1.1;}
        .p-brand-text span{color:var(--terracotta-600);font-size:11px;letter-spacing:.06em;text-transform:uppercase;font-weight:700;}
        .p-links{display:flex;align-items:center;gap:8px;}
        .p-links a{display:inline-flex;align-items:center;gap:7px;color:var(--coffee-700);padding:8px 14px;border-radius:20px;font-size:13px;font-weight:700;background:transparent;border:1.5px solid transparent;transition:all .15s;white-space:nowrap;}
        .p-links a svg{width:15px;height:15px;flex:none;opacity:.8;}
        .p-links a:hover{background:var(--sand-100);color:var(--coffee-900);border-color:var(--line);transform:translateY(-1px);}
        .p-links a.active{background:var(--terracotta-600);color:#fff;border-color:var(--terracotta-600);box-shadow:0 4px 12px rgba(194,89,43,.25);}
        .p-links a.active svg{opacity:1;}
        .p-actions{display:flex;align-items:center;gap:10px;}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:11px 20px;border-radius:8px;border:none;font-weight:600;font-size:14px;transition:all .15s;}
        .btn-primary{background:var(--terracotta-600);color:#fff;box-shadow:0 6px 16px rgba(194,89,43,.32);}
        .btn-primary:hover{background:var(--terracotta-500);}
        .btn-ghost{background:transparent;color:var(--coffee-700);border:1.5px solid var(--line);}
        .btn-ghost:hover{background:var(--sand-100);}
        .btn-sm{padding:8px 14px;font-size:13px;}
        .tag{display:inline-flex;align-items:center;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;}
        .tag-green{background:var(--acacia-100);color:var(--acacia-600);}
        .tag-gold{background:var(--gold-100);color:#8a6418;}
        .tag-red{background:var(--danger-100);color:var(--danger);}
        .tag-grey{background:var(--sand-200);color:var(--ink-soft);}
        .panel{background:var(--white);border:1px solid var(--line);border-radius:14px;box-shadow:0 1px 2px rgba(42,27,16,.08);overflow:hidden;}

        .p-topbar{background:var(--coffee-900);color:rgba(255,255,255,.82);font-size:12px;border-bottom:1px solid rgba(255,255,255,.08);}
        .p-topbar-inner{max-width:1280px;margin:0 auto;padding:7px 24px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;}
        .p-topbar-left,.p-topbar-right{display:flex;align-items:center;gap:18px;flex-wrap:wrap;}
        .p-topbar-left span,.p-topbar-right span{display:inline-flex;align-items:center;gap:6px;}
        .p-topbar a{color:rgba(255,255,255,.9);font-weight:600;}
        .p-topbar a:hover{color:var(--gold-500);}
        .p-topbar svg{width:14px;height:14px;opacity:.8;flex:none;}

        .news-marquee{background:linear-gradient(135deg,var(--coffee-900),var(--terracotta-600));color:#fff;display:flex;align-items:center;gap:0;border-bottom:1px solid rgba(212,162,76,.35);overflow:hidden;height:38px;position:relative;z-index:1;}
        .news-label{background:var(--gold-500);color:var(--coffee-900);font-weight:800;font-size:11px;letter-spacing:.08em;text-transform:uppercase;padding:0 14px;height:100%;display:flex;align-items:center;gap:6px;flex:none;}
        .news-label svg{width:14px;height:14px;}
        .marquee-track{flex:1;overflow:hidden;position:relative;height:100%;display:flex;align-items:center;}
        .marquee-content{display:flex;align-items:center;gap:32px;white-space:nowrap;animation:marqueeScroll 45s linear infinite;will-change:transform;}
        .marquee-content span{display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:600;}
        .marquee-content span::before{content:"•";color:var(--gold-500);font-weight:800;}
        .news-marquee:hover .marquee-content{animation-play-state:paused;}
        @keyframes marqueeScroll{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}

        .p-bottombar{background:var(--sand-100);border-top:1px solid var(--line);border-bottom:1px solid var(--line);}
        .p-bottombar-inner{max-width:1280px;margin:0 auto;padding:12px 24px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;font-size:13px;}
        .p-bottombar-left{display:flex;align-items:center;gap:18px;flex-wrap:wrap;color:var(--coffee-700);}
        .p-bottombar-right{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}

        .udom-fab{position:fixed;bottom:18px;right:18px;z-index:120;height:38px;width:auto;border-radius:999px;background:var(--coffee-700);color:#fff;border:none;box-shadow:0 6px 18px rgba(10,66,96,.4),0 2px 6px rgba(0,0,0,.2);display:flex;align-items:center;justify-content:center;gap:6px;padding:0 13px;cursor:pointer;transition:transform .15s,box-shadow .15s;font-size:12px;font-weight:700;letter-spacing:.01em;line-height:1;}
        .udom-fab:hover{background:var(--coffee-800);transform:translateY(-2px);box-shadow:0 10px 24px rgba(10,66,96,.45);}
        .udom-fab:active{transform:scale(.96);}
        .udom-fab svg{width:15px;height:15px;}
        .udom-panel{position:fixed;bottom:88px;right:22px;z-index:119;width:520px;max-width:calc(100vw - 32px);background:var(--white);border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow-lg);overflow:hidden;display:none;flex-direction:column;max-height:78vh;}
        .udom-panel.open{display:flex;animation:udomIn .2s ease;}
        @keyframes udomIn{from{opacity:0;transform:translateY(8px) scale(.98)}to{opacity:1;transform:none}}
        .udom-panel-head{padding:16px 18px;background:var(--coffee-900);color:#fff;display:flex;align-items:center;justify-content:space-between;gap:12px;}
        .udom-panel-head strong{font-size:14px;}
        .udom-panel-head span{font-size:11px;opacity:.7;letter-spacing:.06em;text-transform:uppercase;}
        .udom-panel-close{width:28px;height:28px;border-radius:8px;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.1);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;}
        .udom-panel-body{padding:14px;overflow:auto;display:flex;flex-direction:column;gap:10px;}
        .udom-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
        .udom-item{padding:11px 12px;border:1px solid var(--line);border-radius:12px;background:var(--sand-50);display:flex;align-items:center;gap:10px;}
        .udom-item-icon{width:32px;height:32px;border-radius:9px;background:var(--white);border:1px solid var(--line);display:flex;align-items:center;justify-content:center;flex:none;color:var(--terracotta-600);}
        .udom-item-info{flex:1;min-width:0;}
        .udom-item-title{font-size:11px;font-weight:800;letter-spacing:.04em;text-transform:uppercase;color:var(--ink-soft);}
        .udom-item-num{font-size:13px;font-weight:800;color:var(--coffee-900);margin-top:2px;word-break:break-all;}
        .udom-item-sub{font-size:11px;color:var(--ink-soft);margin-top:1px;}
        .udom-panel-foot{padding:10px 12px;border-top:1px solid var(--line);background:var(--sand-100);display:flex;gap:8px;flex-wrap:wrap;}
        .udom-overlay{position:fixed;inset:0;z-index:118;background:rgba(36,20,8,.18);display:none;}
        .udom-overlay.open{display:block;}
        @media(max-width:640px){.udom-grid{grid-template-columns:1fr;}}
        @media(max-width:480px){.udom-fab{bottom:14px;right:14px;height:36px;padding:0 12px;font-size:11.5px;gap:5px}.udom-panel{bottom:60px;right:12px;left:12px;width:auto}}

        @media(max-width:1100px){.p-nav-inner{padding:0 20px;}.p-topbar-inner{padding:7px 20px;}.p-bottombar-inner{padding:12px 20px;}}
        @media(max-width:900px){.p-links{display:none;}.p-nav-inner{padding:0 16px;gap:12px;}.p-actions{gap:8px;}.p-topbar-left span:nth-child(3){display:none;}}
        @media(max-width:768px){.p-nav-inner{height:56px;min-height:56px;padding:8px 12px;flex-wrap:nowrap;gap:8px;overflow:hidden;}.p-brand{flex:1;min-width:0;overflow:hidden;gap:8px;}.p-mark{width:32px;height:32px;flex:none;}.p-brand-text{overflow:hidden;min-width:0;flex:1;}.p-brand-text strong{font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}.p-brand-text span{font-size:9px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}.p-actions{flex:none;gap:6px;flex-wrap:nowrap;}.p-actions .btn-sm{padding:6px 8px;font-size:12px;white-space:nowrap;}.p-topbar-inner{padding:6px 10px;gap:8px;flex-wrap:nowrap;overflow:hidden;}.p-topbar-left{flex:1;min-width:0;gap:10px;overflow:hidden;}.p-topbar-left span{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:42%;font-size:11px;}.news-label{font-size:10px;padding:0 8px;}.marquee-content span{font-size:11px;}}
        @media(max-width:640px){.p-nav-inner{height:52px;min-height:52px;padding:6px 10px;gap:6px;}.p-brand-text strong{font-size:13px;}.p-brand-text span{font-size:9px;}.p-actions{gap:4px;}.p-actions .btn-sm{padding:5px 7px;font-size:11px;}.p-topbar{font-size:11px;}.p-topbar-inner{padding:5px 10px;gap:6px;}.p-topbar-left{gap:8px;}.p-topbar-left span{font-size:10px;max-width:48%;}.p-topbar-right{gap:6px;flex:none;}.p-topbar-right span{font-size:10px;}.marquee-content span{font-size:11px;}footer div[style*="grid-template-columns:repeat(4,1fr)"]{grid-template-columns:1fr 1fr !important;}}
        @media(max-width:480px){.p-nav-inner{padding:6px 8px;gap:6px;}.p-mark{width:30px;height:30px;}.p-brand-text strong{font-size:12px;}.p-brand-text span{display:none;}.p-actions .btn-sm{padding:5px 6px;font-size:10px;}.p-topbar{font-size:10px;}.p-topbar-inner{padding:4px 8px;gap:6px;}.p-topbar-left span:nth-child(2){display:none;}.p-topbar-right{display:none;}.news-marquee{height:32px;}.news-label{font-size:9px;padding:0 8px;}.marquee-content span{font-size:10px;gap:16px;}footer div[style*="grid-template-columns:repeat(4,1fr)"]{grid-template-columns:1fr !important;}.btn-sm{padding:6px 8px;font-size:11px;}}
        @media(max-width:360px){.p-nav-inner{padding:4px 6px;}.p-mark{width:28px;height:28px;}.p-brand{gap:6px;}.p-actions{gap:4px;}}
    </style>
    @include('layouts.partials.theme-styles')
</head>
<body>
    @include('layouts.partials.loader')
    <div class="p-sticky-header">
    <div class="p-topbar">
        <div class="p-topbar-inner">
            <div class="p-topbar-left">
                <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg> {{ \App\Models\Setting::getValue('shop_email', \App\Models\Setting::getValue('admissions_email','info@shop.co.tz')) }}</span>
                <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 5.07 12.81 19.79 19.79 0 0 1 2 4.18 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.72c.12 1.2.4 2.37.82 3.5a2 2 0 0 1-.57 2.11L8.09 10.49a16 16 0 0 0 5.42 5.42l1.16-1.16a2 2 0 0 1 2.11-.57c1.13.42 2.3.7 3.5.82A2 2 0 0 1 22 16.92z"/></svg> {{ \App\Models\Setting::getValue('shop_phone','+255 700 000 000') }}</span>
                <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> {{ \App\Models\Setting::getValue('shop_address','Dar es Salaam, Tanzania') }}</span>
            </div>
            <div class="p-topbar-right">
                <span style="opacity:.65">TZS · Single-Shop POS</span>
                <span style="display:inline-flex;gap:10px;margin-left:8px">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('pos.create') ? route('pos.create') : url('/pos') }}" title="POS">POS</a>
                    <span style="opacity:.3">|</span>
                    <a href="{{ url('/products') }}" title="Products">Products</a>
                </span>
            </div>
        </div>
    </div>
    <nav class="p-nav">
        <div class="p-nav-inner">
            <a href="{{ url('/') }}" class="p-brand">
                @php
                    $hdrLogo = \App\Models\Setting::getValue('shop_logo', \App\Models\Setting::getValue('university_logo'));
                    $hdrName = \App\Models\Setting::getValue('shop_name', \App\Models\Setting::getValue('university_name','SHOP POS'));
                    $hdrAcro = substr(\App\Models\Setting::getValue('shop_acronym', \App\Models\Setting::getValue('university_acronym','SP')),0,2);
                @endphp
                <div class="p-mark" style="overflow:hidden;@if($hdrLogo)background:#fff;padding:4px;@endif">@if($hdrLogo)<img src="{{ asset($hdrLogo) }}" style="width:100%;height:100%;object-fit:contain;" alt="Logo">@else {{ $hdrAcro }} @endif</div>
                <div class="p-brand-text"><strong>{{ $hdrName }}</strong><span>Barcode POS & Inventory</span></div>
            </a>
            <div class="p-links">
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg> Home</a>
                <a href="{{ \Illuminate\Support\Facades\Route::has('pos.create') ? route('pos.create') : url('/pos') }}" class="{{ request()->is('pos*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg> POS</a>
                <a href="{{ url('/products') }}" class="{{ request()->is('products*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg> Products</a>
            </div>
            <div class="p-actions">
                @auth
                    @if(auth()->user() && method_exists(auth()->user(),'isAdmin') && auth()->user()->isAdmin())
                        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.dashboard') ? route('admin.dashboard') : url('/dashboard') }}" class="btn btn-primary btn-sm">Admin Panel</a>
                    @elseif(auth()->check())
                        <a href="{{ \Illuminate\Support\Facades\Route::has('pos.create') ? route('pos.create') : url('/pos') }}" class="btn btn-primary btn-sm">Open POS</a>
                    @endif
                    <form method="POST" action="{{ \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : url('/logout') }}">@csrf<button class="btn btn-ghost btn-sm">Logout</button></form>
                @else
                    <a href="{{ \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login') }}" class="btn btn-primary btn-sm">Login</a>
                @endauth
            </div>
        </div>
    </nav>
    @php
        $marqueeRaw = \App\Models\Setting::getValue('shop_marquee', \App\Models\Setting::getValue('marquee_news',''));
        if(!$marqueeRaw){
            $marqueeRaw = ($hdrName ?? 'SHOP POS') . ' — Barcode POS • Inventory • Purchases • Sales • Stock Movements • Split Payments (Cash, M-Pesa, Tigo-Mixx, Airtel, HaloPesa) • Receipt Printing • Returns • Cashier Shifts • Expenses • Profit Reports | Scan • Sell • Track • Profit';
        }
        $newsItems = array_filter(array_map('trim', explode('|', $marqueeRaw)));
    @endphp
    <div class="news-marquee" role="region" aria-label="Shop news">
        <div class="news-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> SHOP POS</div>
        <div class="marquee-track">
            <div class="marquee-content">
                @foreach($newsItems as $n)<span>{{ $n }}</span>@endforeach
                @foreach($newsItems as $n)<span aria-hidden="true">{{ $n }}</span>@endforeach
            </div>
        </div>
    </div>
    </div>

    @if(session('success'))<div style="max-width:1280px;margin:14px auto 0;padding:0 24px;"><div style="background:var(--acacia-100);border:1px solid #c8d7a8;color:var(--acacia-600);padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;">{{ session('success') }}</div></div>@endif
    @if(session('error'))<div style="max-width:1280px;margin:14px auto 0;padding:0 24px;"><div style="background:var(--danger-100);border:1px solid #e8b4b0;color:var(--danger);padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;">{{ session('error') }}</div></div>@endif
    @if(session('info'))<div style="max-width:1280px;margin:14px auto 0;padding:0 24px;"><div style="background:#e8f0fe;border:1px solid #b6c8f0;color:#1a4da1;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;">{{ session('info') }}</div></div>@endif
    @if(isset($errors) && $errors->any())<div style="max-width:1280px;margin:14px auto 0;padding:0 24px;"><div style="background:var(--danger-100);border:1px solid #e8b4b0;color:var(--danger);padding:12px 16px;border-radius:10px;font-size:13px;"><ul style="margin:0 0 0 16px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div></div>@endif

    <main style="min-height:calc(100vh - 72px);">@yield('content')</main>

    <div id="toastHost"></div>
    <div class="udom-overlay" id="udomOverlay" onclick="toggleUdomPanel(false)"></div>
    <button class="udom-fab" id="udomFab" onclick="toggleUdomPanel()" aria-label="Support">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/><path d="M8 10h.01"/><path d="M12 10h.01"/><path d="M16 10h.01"/></svg>
        <span>Help Desk</span>
    </button>
    <div class="udom-panel" id="udomPanel" role="dialog" aria-label="Shop Support">
        <div class="udom-panel-head">
            <div>
                <strong>{{ $hdrName }} Support</strong><br><span>Help Desk</span>
            </div>
            <button class="udom-panel-close" onclick="toggleUdomPanel(false)" aria-label="Close"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div class="udom-panel-body">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px;">
                <span style="display:inline-flex;align-items:center;gap:6px;background:var(--acacia-100);color:var(--acacia-600);padding:4px 10px;border-radius:20px;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;"><span style="width:7px;height:7px;border-radius:50%;background:var(--acacia-600);display:inline-block;"></span> AVAILABLE</span>
                <span class="tag tag-green" style="font-size:11px">Online</span>
            </div>
            <div class="udom-grid">
                @php
                    $contactOfficers = collect();
                    try { $contactOfficers = \App\Models\SupportOfficer::where('is_active',1)->orderBy('order_index')->get(); } catch(\Throwable $e) {}
                @endphp
                @forelse($contactOfficers as $o)
                    @php $tel = preg_replace('/^0/','255', preg_replace('/\D/','',$o->phone)); @endphp
                    <div class="udom-item">
                        <div class="udom-item-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
                        <div class="udom-item-info"><div class="udom-item-title">{{ $o->name }}</div><div class="udom-item-num"><a href="tel:{{ $tel }}" style="color:inherit;text-decoration:none;">{{ $o->phone }}</a></div>@if($o->designation)<div class="udom-item-sub">{{ $o->designation }}</div>@endif</div>
                    </div>
                @empty
                    <div class="udom-item"><div class="udom-item-info"><div class="udom-item-title">Shop Support</div><div class="udom-item-num"><a href="tel:{{ preg_replace('/\D/','',\App\Models\Setting::getValue('shop_phone','+255700000000')) }}" style="color:inherit;text-decoration:none;">{{ \App\Models\Setting::getValue('shop_phone','+255 700 000 000') }}</a></div></div></div>
                @endforelse
            </div>
            <div style="margin-top:8px;padding:10px 12px;border:1px solid var(--line);border-radius:10px;background:var(--sand-50);display:flex;gap:10px;align-items:center;">
                <div class="udom-item-icon" style="width:32px;height:32px;background:var(--white)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div>
                <div style="flex:1;min-width:0;"><div class="udom-item-title">Shop Email</div><div class="udom-item-num" style="font-size:13px;">{{ \App\Models\Setting::getValue('shop_email', \App\Models\Setting::getValue('admissions_email','info@shop.co.tz')) }}</div></div>
            </div>
            <div style="padding:8px 2px 2px;font-size:11px;color:var(--ink-soft);line-height:1.5;">Mon–Sat 08:00–20:00 EAT · Cash, M-Pesa, Airtel Money, Mixx by Yas, HaloPesa, Card · Barcode scan ready.</div>
        </div>
        <div class="udom-panel-foot">
            <a href="{{ url('/contact') }}" class="btn btn-ghost btn-sm" style="flex:1;">Contact</a>
            <a href="tel:{{ preg_replace('/\D/','',\App\Models\Setting::getValue('shop_phone','+255700000000')) }}" class="btn btn-primary btn-sm" style="flex:1;">Call Now</a>
        </div>
    </div>
    <script>
    function toggleUdomPanel(force){
        const p=document.getElementById('udomPanel');
        const o=document.getElementById('udomOverlay');
        if(!p||!o) return;
        const open = typeof force==='boolean' ? force : !p.classList.contains('open');
        p.classList.toggle('open', open);
        o.classList.toggle('open', open);
    }
    document.addEventListener('keydown', e=>{ if(e.key==='Escape') toggleUdomPanel(false); });
    </script>
    @include('layouts.partials.confirm-modal')
    <form id="idleLogoutForm" method="POST" action="{{ \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : url('/logout') }}" style="display:none">@csrf</form>
    @include('layouts.partials.theme-scripts')
    @if(session('success'))<script>document.addEventListener('DOMContentLoaded',()=>toast(@json(session('success')),'success'))</script>@endif
    @if(session('error'))<script>document.addEventListener('DOMContentLoaded',()=>toast(@json(session('error')),'error'))</script>@endif
    <footer style="background:var(--coffee-900);color:#fff;margin-top:40px;">
        <div style="max-width:1280px;margin:0 auto;padding:40px 24px;display:grid;grid-template-columns:repeat(4,1fr);gap:24px;">
            <div><div style="font-weight:700;">{{ $hdrName }}</div><p style="color:rgba(255,255,255,.6);font-size:13px;margin-top:8px;line-height:1.6;">Single-Shop Barcode POS — Sales • Inventory • Purchasing • Cash Management • Reports.</p></div>
            <div><div style="font-weight:600;font-size:13px;">POS</div><div style="margin-top:10px;display:flex;flex-direction:column;gap:6px;font-size:13px;color:rgba(255,255,255,.6);"><a href="{{ \Illuminate\Support\Facades\Route::has('pos.create') ? route('pos.create') : url('/pos') }}">New Sale</a><a href="{{ url('/products') }}">Products</a><a href="{{ url('/stock') }}">Stock</a></div></div>
            <div><div style="font-weight:600;font-size:13px;">Operations</div><div style="margin-top:10px;display:flex;flex-direction:column;gap:6px;font-size:13px;color:rgba(255,255,255,.6);"><a href="{{ url('/purchases') }}">Purchases</a><a href="{{ url('/suppliers') }}">Suppliers</a><a href="{{ url('/customers') }}">Customers</a></div></div>
            <div><div style="font-weight:600;font-size:13px;">Contact</div><p style="color:rgba(255,255,255,.6);font-size:13px;margin-top:10px;line-height:1.6;">{{ \App\Models\Setting::getValue('shop_address','Dar es Salaam, Tanzania') }}<br>{{ \App\Models\Setting::getValue('shop_email','info@shop.co.tz') }}<br>{{ \App\Models\Setting::getValue('shop_phone','+255 700 000 000') }}</p></div>
        </div>
        <div style="border-top:1px solid rgba(255,255,255,.08);padding:16px 24px;text-align:center;color:rgba(255,255,255,.4);font-size:12px;">Version 1.0 Copyright © 2026 {{ $hdrName }} POS. All rights reserved.</div>
    </footer>
</body>
</html>
