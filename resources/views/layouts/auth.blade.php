<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php $authShopName = \App\Models\Setting::getValue('shop_name', \App\Models\Setting::getValue('university_name','SHOP POS')); @endphp
    <title>@yield('title','Auth') — {{ $authShopName }}</title>
    @include('layouts.partials.favicon')
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{--sand-50:#F5F8FC;--sand-100:#ECF1F6;--coffee-900:#052B3D;--coffee-700:#0A4260;--terracotta-600:#0066CC;--terracotta-100:#E4F0FA;--line:#C9D6E2;--gold-500:#E8B82F;--danger:#B33A3A;--danger-100:#F6DCDA;}
        *{box-sizing:border-box;}body{margin:0;font-family:'Raleway',sans-serif;background:var(--coffee-900);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;color:#241408;}
        .card{background:#fff;border-radius:20px;box-shadow:0 20px 48px rgba(0,0,0,.25);padding:32px;width:100%;max-width:460px;}
        .brand{display:flex;align-items:center;gap:10px;justify-content:center;margin-bottom:20px;color:#fff;position:absolute;top:24px;left:50%;transform:translateX(-50%);}
        .mark{width:38px;height:38px;border-radius:10px;background:linear-gradient(155deg,#0A4260,#E8B82F);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;}
        a{color:var(--terracotta-600);text-decoration:none;font-weight:600;}
        .field{margin-bottom:14px;}
        .field label{display:block;font-size:12px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:var(--coffee-700);margin-bottom:6px;}
        .field input{width:100%;padding:12px 14px;border:1.5px solid var(--line);border-radius:8px;font-size:14px;font-family:inherit;}
        .field input:focus{outline:none;border-color:var(--terracotta-600);box-shadow:0 0 0 3px var(--terracotta-100);}
        .btn{width:100%;padding:13px;border:none;border-radius:8px;background:var(--terracotta-600);color:#fff;font-weight:700;font-size:15px;cursor:pointer;}
        .btn:hover{background:#285B78;}
        .alert{padding:10px 12px;border-radius:8px;font-size:13px;margin-bottom:14px;}
        .alert-error{background:var(--danger-100);color:var(--danger);border:1px solid #e8b4b0;}
        .alert-success{background:#E2E7D4;color:#5E6E3F;border:1px solid #c8d7a8;}
    </style>
    @include('layouts.partials.theme-styles')
</head>
<body>
    @include('layouts.partials.loader')
    @php
        $authLogo = \App\Models\Setting::getValue('shop_logo', \App\Models\Setting::getValue('university_logo'));
        $authAcro = substr(\App\Models\Setting::getValue('shop_acronym', \App\Models\Setting::getValue('university_acronym','SP')),0,2);
    @endphp
    <a href="{{ \Illuminate\Support\Facades\Route::has('home') ? route('home') : url('/') }}" class="brand"><div class="mark" style="overflow:hidden;@if($authLogo)background:#fff;padding:4px;@endif">@if($authLogo)<img src="{{ asset($authLogo) }}" style="width:100%;height:100%;object-fit:contain;" alt="Logo">@else {{ $authAcro }} @endif</div><div style="text-align:left;line-height:1.1;"><div style="font-weight:700;">{{ $authShopName }}</div><div style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;color:var(--terracotta-100);">Barcode POS & Inventory</div></div></a>
    <div class="card">
        @if(isset($errors) && $errors->any())<div class="alert alert-error"><ul style="margin:0 0 0 16px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @yield('content')
    </div>
    <div style="position:fixed;bottom:0;left:0;right:0;padding:10px 16px;text-align:center;color:rgba(255,255,255,.65);font-size:11px;letter-spacing:.02em;">Version 1.0 Copyright © 2026 {{ $authShopName }} POS. All rights reserved.</div>
    <div id="toastHost"></div>
    @include('layouts.partials.theme-scripts')
</body>
</html>
