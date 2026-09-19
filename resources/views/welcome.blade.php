@extends('layouts.app')
@section('title','Welcome')
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:24px 24px 40px">
    @php $shopWelcome = \App\Models\Setting::getValue('shop_name', config('app.name','SHOP POS')); @endphp
    <div style="background:linear-gradient(135deg,var(--coffee-900),var(--terracotta-600));color:#fff;padding:48px 24px;border-radius:18px;text-align:center;position:relative;overflow:hidden;">
        <div style="position:absolute;inset:0;background:radial-gradient(circle at 20% 20%, rgba(212,162,76,.18), transparent 55%);pointer-events:none;"></div>
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.18);border-radius:20px;padding:6px 12px;font-size:12px;font-weight:700;position:relative;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg> {{ $shopWelcome }} — Barcode POS • Inventory • Purchases</div>
        <h1 style="margin-top:16px;font-size:32px;font-weight:800;line-height:1.15;color:#fff;position:relative;">{{ $shopWelcome }}<br><span style="color:var(--gold-500)">Single-Shop POS</span></h1>
        <p style="margin-top:10px;color:rgba(255,255,255,.82);font-size:14px;line-height:1.6;max-width:600px;margin-left:auto;margin-right:auto;position:relative;display:flex;align-items:center;justify-content:center;gap:6px;flex-wrap:wrap;"><span>Complete shop cycle</span> <span style="opacity:.6">—</span> <span>Products</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Purchasing</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Stock</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Barcode</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Sales</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Payments</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Returns</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Expenses</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Cashier</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Reports</span></p>
        <div style="margin-top:20px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap;position:relative;">
            <a href="{{ \Illuminate\Support\Facades\Route::has('pos.create') ? route('pos.create') : url('/pos') }}" class="btn" style="background:#fff;color:var(--coffee-900);">Open POS <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left:2px"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
            <a href="{{ url('/products') }}" class="btn btn-primary" style="background:var(--gold-500);">Manage Inventory</a>
            @auth
                <a href="{{ \Illuminate\Support\Facades\Route::has('admin.dashboard') ? route('admin.dashboard') : url('/dashboard') }}" class="btn btn-ghost" style="background:rgba(255,255,255,.12);color:#fff;border-color:rgba(255,255,255,.2);">Dashboard</a>
            @else
                <a href="{{ \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login') }}" class="btn btn-ghost" style="background:rgba(255,255,255,.12);color:#fff;border-color:rgba(255,255,255,.2);">Login</a>
            @endauth
        </div>
    </div>

    <div class="stat-grid" style="margin-top:22px;">
        <div class="stat-card">
            <div class="stat-top"><div class="stat-icon ic-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/></svg></div><span class="tag tag-terracotta">POS</span></div>
            <div class="stat-value">Barcode</div>
            <div class="stat-label">Scan-to-Sell</div>
            <div class="stat-sub">EAN-13 • UPC • Code128 • QR • Internal</div>
        </div>
        <div class="stat-card">
            <div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></div><span class="tag tag-gold">Stock</span></div>
            <div class="stat-value">FEFO</div>
            <div class="stat-label">Inventory & Batches</div>
            <div class="stat-sub">Expiry • Low-stock • Adjustments</div>
        </div>
        <div class="stat-card">
            <div class="stat-top"><div class="stat-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div><span class="tag tag-green">Pay</span></div>
            <div class="stat-value">Split</div>
            <div class="stat-label">Cash & Mobile Money</div>
            <div class="stat-sub">M-Pesa • Airtel • Mixx • HaloPesa • Card</div>
        </div>
        <div class="stat-card">
            <div class="stat-top"><div class="stat-icon ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div><span class="tag tag-blue">Profit</span></div>
            <div class="stat-value">TZS</div>
            <div class="stat-label">Reports & Cashier</div>
            <div class="stat-sub">Sales • COGS • Expenses • Cash</div>
        </div>
    </div>

    <div class="panel" style="margin-top:18px;">
        <div class="panel-head">
            <div>
                <div class="panel-title">POS Flow</div>
                <div class="panel-sub" style="color:var(--ink-soft);font-size:12px;display:flex;align-items:center;gap:6px;flex-wrap:wrap;"><span>Supplier</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Purchase</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Stock In</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Product (SKU+Barcode)</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Scan</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>POS</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Payment</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Receipt</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Stock Out</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Reports</span></div>
            </div>
            <span class="tag tag-green">Live</span>
        </div>
        <div class="panel-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
                <a href="{{ \Illuminate\Support\Facades\Route::has('pos.create') ? route('pos.create') : url('/pos') }}" class="panel" style="padding:16px;display:block;">
                    <div style="font-weight:700;color:var(--coffee-900);display:flex;align-items:center;gap:8px;"><span class="thumb thumb-terracotta" style="width:28px;height:28px;font-size:12px">1</span> POS — New Sale</div>
                    <div class="muted" style="font-size:12.5px;margin-top:6px;">Barcode scan, stock check, add to cart</div>
                </a>
                <a href="{{ url('/products') }}" class="panel" style="padding:16px;display:block;">
                    <div style="font-weight:700;color:var(--coffee-900);display:flex;align-items:center;gap:8px;"><span class="thumb thumb-gold" style="width:28px;height:28px">2</span> Products & Barcodes</div>
                    <div class="muted" style="font-size:12.5px;margin-top:6px;">SKU separate from barcode, unique guard</div>
                </a>
                <a href="{{ url('/purchases') }}" class="panel" style="padding:16px;display:block;">
                    <div style="font-weight:700;color:var(--coffee-900);display:flex;align-items:center;gap:8px;"><span class="thumb thumb-green" style="width:28px;height:28px">3</span> Purchasing</div>
                    <div class="muted" style="font-size:12.5px;margin-top:6px;">Supplier, GRN, stock in on received</div>
                </a>
                <a href="{{ url('/reports') }}" class="panel" style="padding:16px;display:block;">
                    <div style="font-weight:700;color:var(--coffee-900);display:flex;align-items:center;gap:8px;"><span class="thumb thumb-blue" style="width:28px;height:28px">4</span> Reports</div>
                    <div class="muted" style="font-size:12.5px;margin-top:6px;">Daily sales, profit, cash reconciliation</div>
                </a>
            </div>
            <div style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap;">
                <a href="{{ \Illuminate\Support\Facades\Route::has('pos.create') ? route('pos.create') : url('/pos') }}" class="btn btn-primary">Start Selling</a>
                <a href="{{ url('/products') }}" class="btn btn-ghost">Browse Products</a>
                <a href="{{ \Illuminate\Support\Facades\Route::has('admin.dashboard') ? route('admin.dashboard') : url('/dashboard') }}" class="btn btn-ghost">Owner Dashboard</a>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1.1fr .9fr;gap:16px;margin-top:16px;">
        <div class="table-card">
            <div class="panel-head"><div class="panel-title">System Rules</div><span class="tag tag-grey">POS MVP</span></div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:10px;font-size:13px;color:var(--coffee-700);">
                <div style="display:flex;gap:10px;align-items:flex-start;"><span class="tag tag-red" style="flex:none">1</span> No selling beyond stock unless owner enables negative stock</div>
                <div style="display:flex;gap:10px;align-items:flex-start;"><span class="tag tag-gold" style="flex:none">2</span> Barcode uniqueness enforced — duplicate blocked until admin resolves</div>
                <div style="display:flex;gap:10px;align-items:flex-start;"><span class="tag tag-green" style="flex:none">3</span> Completed sales cannot be deleted — only returned via original sale</div>
                <div style="display:flex;gap:10px;align-items:flex-start;"><span class="tag tag-blue" style="flex:none">4</span> Every stock change creates a stock movement record</div>
                <div style="display:flex;gap:10px;align-items:flex-start;"><span class="tag tag-terracotta" style="flex:none">5</span> Price changes are audit-logged</div>
            </div>
        </div>
        <div class="panel" style="padding:18px;">
            <div style="font-weight:800;color:var(--coffee-900);">Main Navigation</div>
            <div style="font-size:12px;color:var(--ink-soft);margin-top:4px;">As spec — SHOP POS sidebar</div>
            <div style="margin-top:12px;display:grid;grid-template-columns:repeat(2,1fr);gap:8px;font-size:12.5px;">
                <div style="padding:8px 10px;background:var(--sand-100);border-radius:8px;border:1px solid var(--line);display:flex;align-items:center;gap:8px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg> Dashboard</div>
                <div style="padding:8px 10px;background:var(--sand-100);border-radius:8px;border:1px solid var(--line);display:flex;align-items:center;gap:8px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg> POS</div>
                <div style="padding:8px 10px;background:var(--sand-100);border-radius:8px;border:1px solid var(--line);display:flex;align-items:center;gap:8px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg> Inventory</div>
                <div style="padding:8px 10px;background:var(--sand-100);border-radius:8px;border:1px solid var(--line);display:flex;align-items:center;gap:8px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Purchases</div>
                <div style="padding:8px 10px;background:var(--sand-100);border-radius:8px;border:1px solid var(--line);display:flex;align-items:center;gap:8px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg> Customers</div>
                <div style="padding:8px 10px;background:var(--sand-100);border-radius:8px;border:1px solid var(--line);display:flex;align-items:center;gap:8px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/></svg> Returns</div>
                <div style="padding:8px 10px;background:var(--sand-100);border-radius:8px;border:1px solid var(--line);display:flex;align-items:center;gap:8px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg> Finance</div>
                <div style="padding:8px 10px;background:var(--sand-100);border-radius:8px;border:1px solid var(--line);display:flex;align-items:center;gap:8px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg> Reports</div>
            </div>
        </div>
    </div>
    <style>@media(max-width:900px){ div[style*="grid-template-columns:1.1fr"]{grid-template-columns:1fr !important} }</style>
    <p class="muted" style="text-align:center;margin-top:18px;font-size:12px;">v{{ app()->version() }} · Theme copied from admissionsystemnew · Adapted for SHOP POS</p>
</div>
@endsection
