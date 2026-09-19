<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Admin') — {{ \App\Models\Setting::getValue('shop_name', \App\Models\Setting::getValue('university_name','SHOP POS')) }} Admin</title>
    @include('layouts.partials.favicon')
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{--primary:#0A4260;--primary-dark:#07364F;--primary-light:#285B78;--secondary:#0066CC;--accent:#F9CC41;--accent-dark:#E8B82F;--sand-50:#F5F8FC;--sand-100:#ECF1F6;--sand-200:#DCE5EE;--coffee-900:#052B3D;--coffee-800:#07364F;--coffee-700:#0A4260;--coffee-500:#285B78;--coffee-300:#5E88A3;--terracotta-600:#0066CC;--terracotta-500:#1B80E0;--terracotta-100:#E4F0FA;--acacia-600:#2E7D6B;--acacia-500:#3E8F7C;--acacia-100:#E1EFEA;--gold-500:#E8B82F;--gold-100:#F9EFD2;--ink:#052C3F;--ink-soft:#40637A;--line:#C9D6E2;--white:#fff;--danger:#B33A3A;--danger-100:#F6DCDA;--sidebar-w:264px;--sidebar-w-collapsed:76px;--topbar-h:72px;--radius-md:14px;--shadow-sm:0 1px 2px rgba(42,27,16,.08);}
        *{box-sizing:border-box;}html,body{height:100%;}body{margin:0;font-family:'Raleway',sans-serif;background:var(--sand-50);color:var(--ink);-webkit-font-smoothing:antialiased;overflow-x:hidden;}
        h1,h2,h3{margin:0;color:var(--coffee-900);}a{color:inherit;text-decoration:none;}button{font-family:inherit;cursor:pointer;}input,select,textarea{font-family:inherit;}
        ::-webkit-scrollbar{width:9px;height:9px;}::-webkit-scrollbar-thumb{background:var(--coffee-300);border-radius:10px;}
        .sidebar{position:fixed;top:0;left:0;bottom:0;width:var(--sidebar-w);z-index:200;background:var(--coffee-900);background-image:radial-gradient(circle at 0% 0%, rgba(212,162,76,.10), transparent 55%);display:flex;flex-direction:column;transition:width .25s, transform .25s;border-right:1px solid rgba(255,255,255,.06);}
        .sidebar.collapsed{width:var(--sidebar-w-collapsed);}
        .sb-brand{display:flex;align-items:center;gap:12px;padding:22px 20px;border-bottom:1px solid rgba(255,255,255,.08);min-height:var(--topbar-h);}
        .sb-mark{width:38px;height:38px;border-radius:10px;flex:none;background:linear-gradient(155deg,var(--terracotta-600),var(--gold-500));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;}
        .sb-brand-text{overflow:hidden;white-space:nowrap;}
        .sb-brand-text strong{display:block;color:#fff;font-size:15px;line-height:1.1;}
        .sb-brand-text span{display:block;color:var(--gold-500);font-size:11px;letter-spacing:.06em;text-transform:uppercase;font-weight:600;}
        .sidebar.collapsed .sb-brand-text{display:none;}
        .sb-nav{flex:1;overflow-y:auto;padding:16px 12px;}
        .sb-label{color:rgba(255,255,255,.32);font-size:10.5px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;padding:14px 12px 8px;}
        .sidebar.collapsed .sb-label{display:none;}
        .sb-item{display:flex;align-items:center;gap:13px;padding:11px 12px;border-radius:10px;color:rgba(255,255,255,.62);font-size:14px;font-weight:500;margin-bottom:2px;position:relative;white-space:nowrap;}
        .sb-item:hover{background:rgba(255,255,255,.06);color:#fff;}
        .sb-item.active{background:rgba(212,162,76,.16);color:var(--gold-500);}
        .sb-item.active::before{content:"";position:absolute;left:-12px;top:8px;bottom:8px;width:3px;border-radius:3px;background:var(--gold-500);}
        .sb-item svg{width:19px;height:19px;flex:none;overflow:visible;}
        .sb-item .badge{margin-left:auto;background:var(--terracotta-600);color:#fff;font-size:11px;font-weight:700;padding:1px 7px;border-radius:20px;}
        .sidebar.collapsed .sb-item span:not(.badge){display:none;}
        .sidebar.collapsed .sb-item .badge{display:none;}
        .sidebar.collapsed .sb-item{justify-content:center;}
        .sb-drop{position:relative;}
        .sb-drop-toggle{width:100%;cursor:pointer;background:none;border:none;font-family:inherit;}
        .sb-drop-toggle .chev{margin-left:auto;opacity:.55;transition:transform .25s;width:15px;height:15px;flex:none;}
        .sb-drop.open .sb-drop-toggle .chev{transform:rotate(180deg);}
        .sb-drop-menu{display:none;margin:2px 0 4px;padding-left:12px;}
        .sb-drop.open .sb-drop-menu{display:block;}
        .sidebar.collapsed .sb-drop-menu{display:none;}
        .sb-drop-sub{display:flex;align-items:center;gap:9px;padding:9px 12px;border-radius:8px;margin-bottom:1px;color:rgba(255,255,255,.55);font-size:13px;font-weight:500;}
        .sb-drop-sub:hover{color:#fff;background:rgba(255,255,255,.06);}
        .sb-drop-sub.active{color:var(--gold-500);background:rgba(212,162,76,.12);}
        .sb-drop.open > .sb-drop-toggle{color:var(--gold-500);}
        .sb-drop-sub svg{width:14px;height:14px;flex:none;overflow:visible;}
        .sb-footer{padding:14px 20px 20px;border-top:1px solid rgba(255,255,255,.08);}
        .sb-user{display:flex;align-items:center;gap:11px;}
        .sb-avatar{width:36px;height:36px;border-radius:50%;flex:none;background:linear-gradient(155deg,var(--acacia-500),var(--acacia-600));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;}
        .sb-user-text{overflow:hidden;white-space:nowrap;}
        .sb-user-text strong{display:block;color:#fff;font-size:13.5px;}
        .sb-user-text span{display:block;color:rgba(255,255,255,.5);font-size:11.5px;}
        .sidebar.collapsed .sb-user-text{display:none;}
        .main{margin-left:var(--sidebar-w);transition:margin-left .25s;min-height:100vh;display:flex;flex-direction:column;}
        .sidebar.collapsed ~ .main{margin-left:var(--sidebar-w-collapsed);}
        .topbar{position:sticky;top:0;z-index:100;height:var(--topbar-h);background:rgba(251,247,239,.86);backdrop-filter:blur(10px);border-bottom:1px solid var(--line);display:flex;align-items:center;gap:16px;padding:0 28px;}
        .tb-toggle{width:38px;height:38px;border-radius:10px;border:1.5px solid var(--line);background:var(--white);display:flex;align-items:center;justify-content:center;flex:none;}
        .tb-title{font-weight:700;color:var(--coffee-900);white-space:nowrap;}
        .tb-right{margin-left:auto;display:flex;align-items:center;gap:10px;}
        .tb-live{display:flex;align-items:center;gap:7px;background:var(--acacia-100);color:var(--acacia-600);padding:7px 13px;border-radius:20px;font-size:12.5px;font-weight:700;}
        .tb-live::before{content:"";width:7px;height:7px;border-radius:50%;background:var(--acacia-600);}
        .tb-profile-wrapper{position:relative;display:flex;align-items:center}
        .tb-profile{cursor:pointer}
        .tb-profile-menu{position:absolute;top:calc(100% + 8px);right:0;background:var(--white);border:1px solid var(--line);border-radius:12px;box-shadow:0 12px 28px rgba(0,0,0,.15);min-width:220px;display:none;z-index:300;overflow:hidden}
        .tb-profile-wrapper:hover .tb-profile-menu, .tb-profile-wrapper:focus-within .tb-profile-menu, .tb-profile-wrapper.open .tb-profile-menu{display:block}
        .tb-profile-menu-head{padding:14px;display:flex;gap:10px;align-items:center;background:var(--sand-50);border-bottom:1px solid var(--line)}
        .tb-profile-menu-head .sb-avatar{width:44px;height:44px;flex:none;font-size:15px}
        .tb-profile-item{display:flex;align-items:center;gap:10px;padding:11px 14px;font-size:13px;color:var(--coffee-800);text-decoration:none;width:100%;text-align:left;background:none;border:none;font-family:inherit;cursor:pointer;transition:background .12s}
        .tb-profile-item:hover{background:var(--sand-100);color:var(--coffee-900)}
        .tb-profile-item svg{width:16px;height:16px;flex:none;opacity:.8}
        .view-wrap{padding:28px;flex:1;}
        .mobile-overlay{position:fixed;inset:0;background:rgba(36,20,8,.45);z-index:190;display:none;}
        .mobile-overlay.show{display:block;}
        @media(max-width:1100px){.topbar{padding:0 20px;}}
        @media(max-width:900px){.sidebar{transform:translateX(-100%);width:var(--sidebar-w);}.sidebar.mobile-open{transform:translateX(0);}.main{margin-left:0 !important;}.topbar{flex-wrap:wrap;height:auto;min-height:var(--topbar-h);padding:10px 16px;gap:10px;}}
        @media(max-width:640px){.view-wrap{padding:16px;}.topbar{padding:8px 10px;gap:6px;flex-wrap:nowrap;overflow:visible;}.tb-toggle{width:32px;height:32px;flex:none;}.tb-title{display:none;}.tb-live{display:none !important;}.tb-right{gap:6px;flex-wrap:nowrap;flex:1;justify-content:flex-end;min-width:0;overflow:visible;}.tb-right .shop-switcher{padding:4px 8px !important;gap:4px !important;flex:none;max-width:42%;}.shop-switcher-name{overflow:hidden;text-overflow:ellipsis;max-width:70px;display:inline-block;}.shop-switcher select{max-width:90px;font-size:11px;}.tb-profile{margin-left:4px !important;padding-left:8px !important;gap:6px !important;flex:none;}.tb-profile-text{max-width:80px;overflow:hidden;}.tb-profile .sb-avatar{width:28px !important;height:28px !important;}.tb-profile-menu{right:-8px !important;left:auto !important;min-width:200px !important;max-width:92vw !important;}}
        @media(max-width:480px){.topbar{padding:6px 8px;gap:4px;}.tb-right{ gap:4px;}.tb-live{display:none !important;}.shop-switcher{padding:3px 6px !important;gap:4px !important;max-width:38%;}.shop-switcher-name{max-width:50px;font-size:12px;}.shop-switcher select{max-width:70px;font-size:11px;padding:1px 4px !important;}.tb-profile-email{display:none;}.tb-profile-text{max-width:60px;}.tb-profile .sb-avatar{width:26px !important;height:26px !important;}}
        @media(max-width:640px){.tb-right{justify-content:center !important;gap:8px !important;}.shop-switcher{flex:1 !important;max-width:62% !important;justify-content:center !important;padding:6px 10px !important;}.shop-switcher-name{max-width:none !important;font-size:12px !important;display:inline !important;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}.tb-profile{flex:none !important;max-width:85px !important;gap:6px !important;}.tb-profile-text{max-width:70px !important;}}
        @media(max-width:360px){.shop-switcher{max-width:58% !important;}.shop-switcher-name{font-size:12px !important;}.tb-profile{max-width:75px !important;}}
        .btn{padding:11px 18px;border-radius:8px;border:none;font-weight:600;font-size:14px;display:inline-flex;align-items:center;gap:8px;cursor:pointer;}
        .btn-primary{background:var(--terracotta-600);color:#fff;box-shadow:0 6px 16px rgba(194,89,43,.32);}
        .btn-primary:hover{background:var(--terracotta-500);}
        .btn-ghost{background:transparent;border:1.5px solid var(--line);color:var(--coffee-700);}
        .tag{padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;}
        .tag-green{background:var(--acacia-100);color:var(--acacia-600);}.tag-gold{background:var(--gold-100);color:#8a6418;}
        .tag-red{background:var(--danger-100);color:var(--danger);}
        .panel{background:var(--white);border:1px solid var(--line);border-radius:14px;box-shadow:0 1px 2px rgba(42,27,16,.08);overflow:hidden;}
    </style>
    @include('layouts.partials.theme-styles')
</head>
<body>
    @include('layouts.partials.loader')
    <div id="toastHost"></div>
    <div class="mobile-overlay" id="mobileOverlay" onclick="closeMobile()"></div>
    <aside class="sidebar" id="sidebar">
        <div class="sb-brand">
            @php
                $shopName = \App\Models\Setting::getValue('shop_name', \App\Models\Setting::getValue('university_name', config('app.name','SHOP POS')));
                $shopLogo = \App\Models\Setting::getValue('shop_logo', \App\Models\Setting::getValue('university_logo', null));
                $shopAcro = \App\Models\Setting::getValue('shop_acronym', substr(\App\Models\Setting::getValue('university_acronym','SP'),0,2));
            @endphp
            <div class="sb-mark" style="overflow:hidden;@if($shopLogo)background:#fff;padding:4px;@endif">@if($shopLogo)<img src="{{ asset($shopLogo) }}" style="width:100%;height:100%;object-fit:contain;" alt="Logo">@else {{ $shopAcro }} @endif</div>
            <div class="sb-brand-text"><strong>{{ $shopName }}</strong><span>Shop POS</span></div>
        </div>
                <nav class="sb-nav">
            <a href="{{ \Illuminate\Support\Facades\Route::has('dashboard') ? route('dashboard') : (\Illuminate\Support\Facades\Route::has('admin.dashboard') ? route('admin.dashboard') : url('/dashboard')) }}" class="sb-item {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->is('admin') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>Dashboard</span>
            </a>

            <div class="sb-drop {{ request()->routeIs('pos*') || request()->routeIs('sales*') || request()->routeIs('returns*') ? 'open' : '' }}">
                <button type="button" class="sb-item sb-drop-toggle" onclick="toggleSbDrop(this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <span>Point of Sale</span>
                    <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="sb-drop-menu">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('pos.create') ? route('pos.create') : url('/pos') }}" class="sb-drop-sub {{ request()->routeIs('pos.create') || request()->is('pos') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg> New Sale — POS</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('pos.held') ? route('pos.held') : url('/pos/held') }}" class="sb-drop-sub {{ request()->routeIs('pos.held*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Held Sales</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('sales.index') ? route('sales.index') : url('/sales') }}" class="sb-drop-sub {{ request()->routeIs('sales*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg> Sales History</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('returns.index') ? route('returns.index') : url('/returns') }}" class="sb-drop-sub {{ request()->routeIs('returns*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg> Returns</a>
                </div>
            </div>

            <div class="sb-drop {{ request()->routeIs('quotations*') || request()->routeIs('proforma-invoices*') || request()->routeIs('invoices*') ? 'open' : '' }}">
                <button type="button" class="sb-item sb-drop-toggle" onclick="toggleSbDrop(this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="9" y1="11" x2="15" y2="11"/></svg>
                    <span>Sales Documents</span>
                    <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="sb-drop-menu">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('quotations.index') ? route('quotations.index') : url('/quotations') }}" class="sb-drop-sub {{ request()->routeIs('quotations*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Quotations</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('proforma-invoices.index') ? route('proforma-invoices.index') : url('/proforma-invoices') }}" class="sb-drop-sub {{ request()->routeIs('proforma-invoices*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg> Proforma Invoices</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('invoices.index') ? route('invoices.index') : url('/invoices') }}" class="sb-drop-sub {{ request()->routeIs('invoices*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> Invoices</a>
                </div>
            </div>

            <div class="sb-drop {{ request()->routeIs('products*') || request()->routeIs('stock*') || request()->routeIs('stock-count*') || request()->routeIs('adjustments*') || request()->routeIs('barcodes*') || request()->routeIs('categories*') || request()->routeIs('brands*') || request()->routeIs('units*') ? 'open' : '' }}">
                <button type="button" class="sb-item sb-drop-toggle" onclick="toggleSbDrop(this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/></svg>
                    <span>Inventory</span>
                    <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="sb-drop-menu">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('products.index') ? route('products.index') : url('/products') }}" class="sb-drop-sub {{ request()->routeIs('products*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/></svg> Products</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('stock.index') ? route('stock.index') : url('/stock') }}" class="sb-drop-sub {{ request()->routeIs('stock.index') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h18v18H3z"/><path d="M3 9h18"/><path d="M9 21V9"/></svg> Stock Levels</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('stock-count.index') ? route('stock-count.index') : url('/stock-count') }}" class="sb-drop-sub {{ request()->routeIs('stock-count*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg> Stock Count</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('adjustments.index') ? route('adjustments.index') : url('/adjustments') }}" class="sb-drop-sub {{ request()->routeIs('adjustments*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg> Adjustments</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('barcodes.index') ? route('barcodes.index') : url('/barcodes') }}" class="sb-drop-sub {{ request()->routeIs('barcodes*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="7" y1="8" x2="7" y2="16"/><line x1="11" y1="8" x2="11" y2="16"/><line x1="15" y1="8" x2="15" y2="16"/></svg> Barcodes</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('categories.index') ? route('categories.index') : url('/categories') }}" class="sb-drop-sub {{ request()->routeIs('categories*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg> Categories</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('brands.index') ? route('brands.index') : url('/brands') }}" class="sb-drop-sub {{ request()->routeIs('brands*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8a4 4 0 0 1 4 4"/><path d="M12 16h.01"/></svg> Brands</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('units.index') ? route('units.index') : url('/units') }}" class="sb-drop-sub {{ request()->routeIs('units*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h6v6H9z"/><path d="M9 3v6M15 3v6M9 15v6M15 15v6M3 9h6M3 15h6M15 9h6M15 15h6"/></svg> Units</a>
                </div>
            </div>

            <div class="sb-drop {{ request()->routeIs('purchases*') || request()->routeIs('suppliers*') ? 'open' : '' }}">
                <button type="button" class="sb-item sb-drop-toggle" onclick="toggleSbDrop(this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                    <span>Purchasing</span>
                    <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="sb-drop-menu">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('purchases.index') ? route('purchases.index') : url('/purchases') }}" class="sb-drop-sub {{ request()->routeIs('purchases*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg> Purchases</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('suppliers.index') ? route('suppliers.index') : url('/suppliers') }}" class="sb-drop-sub {{ request()->routeIs('suppliers*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 11v2"/><path d="M17 8v6"/></svg> Suppliers</a>
                </div>
            </div>

            <div class="sb-drop {{ request()->routeIs('customers*') ? 'open' : '' }}">
                <button type="button" class="sb-item sb-drop-toggle" onclick="toggleSbDrop(this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M14 11a4 4 0 0 1 2 3v1"/></svg>
                    <span>Customers</span>
                    <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="sb-drop-menu">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('customers.index') ? route('customers.index') : url('/customers') }}" class="sb-drop-sub {{ request()->routeIs('customers*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M14 11a4 4 0 0 1 2 3v1"/></svg> Customers</a>
                </div>
            </div>

            @if(auth()->check() && auth()->user()->isAdmin())
            <a href="{{ \Illuminate\Support\Facades\Route::has('shops.index') ? route('shops.index') : url('/shops') }}" class="sb-item {{ request()->routeIs('shops*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>Shops</span>
            </a>
            @endif

            @if(auth()->check() && auth()->user()->isAdmin())
            <div class="sb-drop {{ request()->routeIs('payments*') || request()->routeIs('expenses*') || request()->routeIs('shifts*') ? 'open' : '' }}">
                <button type="button" class="sb-item sb-drop-toggle" onclick="toggleSbDrop(this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    <span>Finance</span>
                    <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="sb-drop-menu">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('payments.index') ? route('payments.index') : url('/payments') }}" class="sb-drop-sub {{ request()->routeIs('payments*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg> Payments</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('expenses.index') ? route('expenses.index') : url('/expenses') }}" class="sb-drop-sub {{ request()->routeIs('expenses*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h6a3.5 3.5 0 0 1 0 7H6"/></svg> Expenses</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('shifts.current') ? route('shifts.current') : url('/shifts/current') }}" class="sb-drop-sub {{ request()->routeIs('shifts.current') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Current Shift</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('shifts.index') ? route('shifts.index') : url('/shifts') }}" class="sb-drop-sub {{ request()->routeIs('shifts.index') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg> All Shifts</a>
                </div>
            </div>
            @endif

            @if(auth()->check() && auth()->user()->isAdmin())
            <div class="sb-drop {{ request()->routeIs('reports*') ? 'open' : '' }}">
                <button type="button" class="sb-item sb-drop-toggle" onclick="toggleSbDrop(this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    <span>Reports</span>
                    <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="sb-drop-menu">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('reports.index') ? route('reports.index') : url('/reports') }}" class="sb-drop-sub {{ request()->routeIs('reports.index') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg> Reports Center</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('reports.sales') ? route('reports.sales') : url('/reports/sales') }}" class="sb-drop-sub {{ request()->routeIs('reports.sales') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg> Sales Report</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('reports.inventory') ? route('reports.inventory') : url('/reports/inventory') }}" class="sb-drop-sub {{ request()->routeIs('reports.inventory') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg> Inventory Report</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('reports.purchases') ? route('reports.purchases') : url('/reports/purchases') }}" class="sb-drop-sub {{ request()->routeIs('reports.purchases') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="12" y1="12" x2="12" y2="16"/></svg> Purchases Report</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('reports.financial') ? route('reports.financial') : url('/reports/financial') }}" class="sb-drop-sub {{ request()->routeIs('reports.financial') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h6a3.5 3.5 0 0 1 0 7H6"/><circle cx="12" cy="12" r="10" opacity="0.2"/></svg> Financial Report</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('reports.profit') ? route('reports.profit') : url('/reports/profit') }}" class="sb-drop-sub {{ request()->routeIs('reports.profit') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg> Profit Report</a>
                </div>
            </div>
            @endif

            @if(auth()->check() && auth()->user()->isAdmin())
            <div class="sb-drop {{ request()->routeIs('users*') || request()->routeIs('admin.users*') || request()->routeIs('settings*') || request()->routeIs('admin.settings*') || request()->routeIs('audit-logs*') || request()->routeIs('admin.audit-logs*') ? 'open' : '' }}">
                <button type="button" class="sb-item sb-drop-toggle" onclick="toggleSbDrop(this)">
                    <svg viewBox="-1 -1 26 26" fill="none" stroke="currentColor" stroke-width="2" style="overflow:visible"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 1 0-3.02 1.65 1.65 0 0 0 1-1.51V9a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V15a1.65 1.65 0 0 0 1.51 1"/></svg>
                    <span>Administration</span>
                    <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="sb-drop-menu">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('users.index') ? route('users.index') : (\Illuminate\Support\Facades\Route::has('admin.users.index') ? route('admin.users.index') : url('/users')) }}" class="sb-drop-sub {{ request()->routeIs('users*') || request()->routeIs('admin.users*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> Users</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('settings.index') ? route('settings.index') : (\Illuminate\Support\Facades\Route::has('admin.settings.index') ? route('admin.settings.index') : url('/settings')) }}" class="sb-drop-sub {{ request()->routeIs('settings*') || request()->routeIs('admin.settings*') ? 'active' : '' }}"><svg viewBox="-1 -1 26 26" fill="none" stroke="currentColor" stroke-width="2" style="overflow:visible"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 1 0-3.02 1.65 1.65 0 0 0 1-1.51V9a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V15a1.65 1.65 0 0 0 1.51 1"/></svg> Settings</a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('audit-logs.index') ? route('audit-logs.index') : (\Illuminate\Support\Facades\Route::has('admin.audit-logs.index') ? route('admin.audit-logs.index') : url('/audit-logs')) }}" class="sb-drop-sub {{ request()->routeIs('audit-logs*') || request()->routeIs('admin.audit-logs*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg> Audit Logs</a>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : url('/logout') }}" style="margin-top:12px">@csrf<button class="sb-item" style="width:100%;background:none;border:none;font-family:inherit;cursor:pointer;text-align:left;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg><span>Logout</span></button></form>
        </nav>

    </aside>

    <div class="main" id="mainArea">
        <header class="topbar">
            <button class="tb-toggle" onclick="toggleSidebar()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg></button>
            <div class="tb-title">Shop POS — Admin Panel</div>
            <div class="tb-right">
                <div class="tb-live"><span>Live</span></div>
                @php $csTop = currentShop(); $shopsTop = availableShops(); $isAll = isCompanyView(); @endphp
                <div class="shop-switcher" style="display:flex;align-items:center;gap:8px;padding:6px 10px;background:#fff;border:1.5px solid var(--line);border-radius:20px;font-size:13px;font-weight:700;color:var(--coffee-900);flex:none">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span class="shop-switcher-name" style="white-space:nowrap">{{ $isAll ? 'All Shops' : ($csTop->name ?? 'Select Shop') }}</span>
                    @if(!auth()->user()->isCashier())
                        <form method="POST" action="{{ route('shops.switch') }}" style="margin:0">
                            @csrf
                            <select name="shop_id" onchange="this.form.submit()" style="border:1px solid var(--line);border-radius:6px;padding:2px 6px;font-size:12px;background:var(--sand-50);max-width:150px">
                                <option value="all" @selected($isAll)>All Shops — Company</option>
                                @foreach($shopsTop as $shop)
                                    <option value="{{ encId($shop->id) }}" @selected(!$isAll && $csTop && $shop->id == $csTop->id)>{{ $shop->name }} @if($shop->code) ({{ $shop->code }}) @endif</option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <span class="tag tag-gold" style="font-size:10px">Assigned</span>
                    @endif
                </div>
                <div class="tb-profile-wrapper" tabindex="0" onclick="if(window.innerWidth<=900) this.classList.toggle('open')">
                    <div class="tb-profile" style="display:flex;align-items:center;gap:10px;margin-left:8px;padding-left:12px;border-left:1px solid var(--line);min-width:0;cursor:pointer">
                        @php $u = auth()->user(); $initials = strtoupper(substr($u->name ?? 'G',0,1).substr(explode(' ', $u->name ?? 'Guest')[1] ?? '',0,1)); $hasAvatar = !empty($u->avatar) && file_exists(public_path($u->avatar)); @endphp
                        <div class="sb-avatar" style="width:36px;height:36px;border-radius:50%;flex:none;background:var(--sand-100);border:1.5px solid var(--line);display:flex;align-items:center;justify-content:center;color:var(--coffee-700);overflow:hidden">
                            @if($hasAvatar)<img src="{{ asset($u->avatar) }}" style="width:100%;height:100%;object-fit:cover" alt="Profile">@else @if($initials){{ $initials }}@else<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>@endif @endif
                        </div>
                        <div class="tb-profile-text" style="line-height:1.2;min-width:0;overflow:hidden;">
                            <div style="font-weight:700;font-size:13px;color:var(--coffee-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $u->name ?? 'Guest' }}</div>
                            <div style="font-size:11px;font-weight:600;color:var(--terracotta-600);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $u->roleLabel() ?? ($u->role ?? 'Admin') }}</div>
                        </div>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:.5;flex:none"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                    <div class="tb-profile-menu">
                        <div class="tb-profile-menu-head">
                            <div class="sb-avatar" style="width:44px;height:44px;overflow:hidden">
                                @if($hasAvatar)<img src="{{ asset($u->avatar) }}" style="width:100%;height:100%;object-fit:cover" alt="Profile">@else {{ $initials }} @endif
                            </div>
                            <div style="flex:1;min-width:0;overflow:hidden">
                                <div style="font-weight:800;font-size:13px;color:var(--coffee-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $u->name ?? 'Guest' }}</div>
                                <div style="font-size:12px;color:var(--ink-soft);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $u->email ?? '' }}</div>
                                <div style="margin-top:4px"><span class="tag tag-gold">{{ $u->roleLabel() ?? $u->role }}</span> @if($u->isCashier() && $u->shop)<span class="tag tag-grey" style="margin-left:4px">{{ $u->shop->name }}</span>@endif</div>
                            </div>
                        </div>
                        <a href="{{ route('profile.show') }}" class="tb-profile-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Profile</a>
                        <a href="{{ route('account.setting') }}" class="tb-profile-item"><svg viewBox="-1 -1 26 26" fill="none" stroke="currentColor" stroke-width="2" style="overflow:visible"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 1 0-3.02 1.65 1.65 0 0 0 1-1.51V9a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V15a1.65 1.65 0 0 0 1.51 1"/></svg> Account Setting</a>
                        <form method="POST" action="{{ route('logout') }}" style="margin:0;border-top:1px solid var(--line)">@csrf<button class="tb-profile-item" style="color:var(--danger)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Logout</button></form>
                    </div>
                </div>
            </div>
        </header>
        <div class="view-wrap">
            @if(session('success'))<div style="background:var(--acacia-100);border:1px solid #c8d7a8;color:var(--acacia-600);padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;">{{ session('success') }}</div>@endif
            @if(session('error'))<div style="background:var(--danger-100);border:1px solid #e8b4b0;color:var(--danger);padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;">{{ session('error') }}</div>@endif
            @if(isset($errors) && $errors->any())<div style="background:var(--danger-100);border:1px solid #e8b4b0;color:var(--danger);padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:16px;"><ul style="margin:0 0 0 16px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </div>
        @include('layouts.partials.system-footer')
    </div>
    @include('layouts.partials.confirm-modal')
    <form id="idleLogoutForm" method="POST" action="{{ \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : url('/logout') }}" style="display:none">@csrf</form>
    @include('layouts.partials.theme-scripts')
    @if(session('success'))<script>document.addEventListener('DOMContentLoaded',()=>toast(@json(session('success')),'success'))</script>@endif
    @if(session('error'))<script>document.addEventListener('DOMContentLoaded',()=>toast(@json(session('error')),'error'))</script>@endif
</body>
</html>
