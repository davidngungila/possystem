@extends('layouts.admin')
@section('title','Stock')
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">Stock <span class="info-icon" tabindex="0">i<span class="tooltip">Live inventory — current stock, valuation, movements · Stock changes via purchases, sales, adjustments</span></span></h1><p class="page-sub">Inventory</p></div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('products.create') }}" class="btn btn-ghost btn-sm">Add Product</a>
        <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">Products</a>
    </div>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8"/></svg></div><span class="tag tag-terracotta">{{ $stats['total_products'] }} products</span></div>
        <div class="stat-label">Total Products</div><div class="stat-value">{{ $stats['total_products'] }}</div><div class="stat-sub">Active inventory items</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><span class="tag tag-blue">{{ $stats['total_stock'] }} units</span></div>
        <div class="stat-label">Total Stock Qty</div><div class="stat-value">{{ number_format($stats['total_stock']) }}</div><div class="stat-sub">Sum of current_stock</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div><span class="tag tag-gold">TZS {{ number_format($stats['stock_value'],0) }}</span></div>
        <div class="stat-label">Stock Value</div><div class="stat-value">TZS {{ number_format($stats['stock_value'],0) }}</div><div class="stat-sub">At buying price</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-red" style="background:var(--danger-100);color:var(--danger);width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div><span class="tag tag-red">{{ $stats['out_stock'] }} out</span></div>
        <div class="stat-label">Alerts</div><div class="stat-value" style="font-size:18px"><span style="color:#c97a1a">{{ $stats['expiring'] }} expiring</span> <span style="font-size:12px;color:var(--ink-soft)">/ {{ $stats['low_stock'] }} low</span></div><div class="stat-sub" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;"><span style="display:inline-flex;align-items:center;gap:4px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#8a6418" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> Low</span> <span style="display:inline-flex;align-items:center;gap:4px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#B33A3A" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> Out</span> <span style="display:inline-flex;align-items:center;gap:4px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#c97a1a" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Expiring ≤30d</span></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1.2fr .8fr;gap:16px">
    <div class="table-card">
        <form method="GET" class="table-toolbar">
            <div class="chip-filters">
                <a href="{{ route('stock.index', ['q'=>$q]) }}" class="chip {{ $filter==='all' ? 'active' : '' }}">All</a>
                <a href="{{ route('stock.index', ['q'=>$q,'filter'=>'low']) }}" class="chip {{ $filter==='low' ? 'active' : '' }}">Low</a>
                <a href="{{ route('stock.index', ['q'=>$q,'filter'=>'out']) }}" class="chip {{ $filter==='out' ? 'active' : '' }}">Out</a>
                <a href="{{ route('stock.index', ['q'=>$q,'filter'=>'expiring']) }}" class="chip {{ $filter==='expiring' ? 'active' : '' }}">Expiring</a>
            </div>
            <div class="table-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input name="q" value="{{ $q }}" placeholder="Search name, SKU, barcode...">
                <input type="hidden" name="filter" value="{{ $filter }}">
            </div>
        </form>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Product</th><th class="center">Stock</th><th class="right">Min</th><th class="right">Value</th><th class="center">Status</th><th class="right">Actions</th></tr></thead>
                <tbody>
                @forelse($products as $p)
                    @php
                        $isLow = $p->current_stock > 0 && $p->current_stock <= $p->min_stock;
                        $isOut = $p->current_stock <= 0;
                        $val = $p->current_stock * $p->buying_price;
                    @endphp
                    <tr>
                        <td>
                            <div class="cell-main">
                                <div class="thumb {{ $isOut ? 'thumb-grey' : ($isLow ? 'thumb-gold' : 'thumb-terracotta') }}">{{ strtoupper(substr($p->name,0,1)) }}</div>
                                <div>
                                    <div class="cell-title">{{ $p->name }}</div>
                                    <div class="cell-sub"><span class="cell-mono">{{ $p->sku }}</span> @if($p->barcode) · <span class="cell-mono">{{ $p->barcode }}</span> @endif · {{ $p->category->name ?? '—' }}</div>
                                    @if($p->expiry_date)<div class="cell-sub" style="color:#c97a1a">Exp: {{ $p->expiry_date->format('Y-m-d') }} @if($p->batch_number) · Batch {{ $p->batch_number }} @endif</div>@endif
                                </div>
                            </div>
                        </td>
                        <td class="center">
                            @if($isOut)
                                <span class="tag tag-red" style="display:inline-flex;align-items:center;gap:4px;">{{ $p->current_stock }} <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#B33A3A" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></span>
                            @elseif($isLow)
                                <span class="tag tag-gold" style="display:inline-flex;align-items:center;gap:4px;">{{ $p->current_stock }} <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#8a6418" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></span>
                            @else
                                <span class="tag tag-green">{{ $p->current_stock }}</span>
                            @endif
                        </td>
                        <td class="right">{{ $p->min_stock }}</td>
                        <td class="right">TZS {{ number_format($val,0) }}</td>
                        <td class="center">
                            @if($isOut) <span class="tag tag-red">Out</span>
                            @elseif($isLow) <span class="tag tag-gold">Low</span>
                            @else <span class="tag tag-green">OK</span> @endif
                            @if($p->expiry_date && $p->expiry_date->lte(now()->addDays(30))) <span class="tag tag-gold" style="margin-left:4px">Expiring</span> @endif
                        </td>
                        <td><div class="row-actions"><a href="{{ route('products.show', encId($p->id)) }}" class="btn-icon" title="View"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a><a href="{{ route('products.edit', encId($p->id)) }}" class="btn-icon" title="Adjust"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a></div></td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8"/></svg></div><strong>No stock data</strong><p>Add products with stock. Every stock change is logged via stock_movements.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            @include('layouts.partials.pagination', ['paginator'=>$products])
        @else
            <div class="table-pagination"><div class="pager-info">{{ $products->total() }} products — valuation at buying price</div></div>
        @endif
    </div>

    <div style="display:flex;flex-direction:column;gap:16px">
        <div class="table-card">
            <div class="panel-head"><div class="panel-title">Recent Stock Movements</div><span class="tag tag-grey">Live</span></div>
            <div class="table-scroll" style="max-height:360px">
                <table>
                    <thead><tr><th>Product</th><th class="center">Type</th><th class="center">Qty</th><th class="center">Prev → New</th><th>Reason</th></tr></thead>
                    <tbody>
                    @forelse($movements as $m)
                        <tr>
                            <td><div class="cell-title" style="font-size:13px">{{ $m->product->name ?? '—' }}</div><div class="cell-sub mono">{{ $m->product->sku ?? '' }}</div></td>
                            <td class="center"><span class="tag {{ $m->type==='in' ? 'tag-green' : ($m->type==='out' ? 'tag-red' : 'tag-gold') }}">{{ $m->type }}</span></td>
                            <td class="center">{{ $m->quantity }}</td>
                            <td class="center"><span class="cell-mono">{{ $m->previous_stock }} → {{ $m->new_stock }}</span></td>
                            <td><span class="cell-sub">{{ $m->reason ?? $m->reference_type ?? '—' }}</span><div class="cell-sub" style="font-size:11px">{{ $m->created_at->format('d/m H:i') }} · {{ $m->user->name ?? 'system' }}</div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state" style="padding:16px"><strong>No movements yet</strong><p>Stock IN via purchase / IN via product create, OUT via sale.</p></div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>


    </div>
</div>
<style>@media(max-width:900px){ div[style*="grid-template-columns:1.2fr"]{grid-template-columns:1fr !important} }</style>
@endsection
