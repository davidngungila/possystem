@extends('layouts.admin')
@section('title','Products')
@section('content')
<div class="page-head">
    <div><h1>Products</h1><p class="page-sub">SKU separate from barcode · Buying / Selling / Wholesale · Stock · Barcode unique guard</p></div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('products.export') }}" class="btn btn-success btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Export</a>
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Product</a>
        <button type="button" class="btn btn-info btn-sm" onclick="document.getElementById('importFile').click()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg> Import</button>
        <form id="importForm" action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" style="display:none">
            @csrf
            <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv" style="display:none" onchange="document.getElementById('importForm').submit()">
        </form>
    </div>
</div>

<div class="table-card">
    <form method="GET" class="table-toolbar">
        <div class="chip-filters">
            <a href="{{ route('products.index', ['q'=>$q]) }}" class="chip {{ $filter==='all' ? 'active' : '' }}">All</a>
            <a href="{{ route('products.index', ['q'=>$q,'filter'=>'active']) }}" class="chip {{ $filter==='active' ? 'active' : '' }}">Active</a>
            <a href="{{ route('products.index', ['q'=>$q,'filter'=>'low']) }}" class="chip {{ $filter==='low' ? 'active' : '' }}">Low stock</a>
            <a href="{{ route('products.index', ['q'=>$q,'filter'=>'out']) }}" class="chip {{ $filter==='out' ? 'active' : '' }}">Out</a>
            <a href="{{ route('products.index', ['q'=>$q,'filter'=>'expiring']) }}" class="chip {{ $filter==='expiring' ? 'active' : '' }}">Expiring</a>
        </div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Search name, SKU, barcode...">
            <input type="hidden" name="filter" value="{{ $filter }}">
        </div>
    </form>

    <div class="table-scroll">
        <table>
            <thead><tr><th>Product</th><th>SKU / Barcode</th><th class="center">Stock</th><th class="right">Buying</th><th class="right">Selling</th><th class="center">Status</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($products as $p)
                @php
                    $isLow = $p->current_stock > 0 && $p->current_stock <= $p->min_stock;
                    $isOut = $p->current_stock <= 0;
                    $isExp = $p->expiry_date && $p->expiry_date->lte(now()->addDays(30));
                @endphp
                <tr>
                    <td>
                        <div class="cell-main">
                            <div class="thumb {{ $isOut ? 'thumb-grey' : ($isLow ? 'thumb-gold' : 'thumb-terracotta') }}">{{ strtoupper(substr($p->name,0,1)) }}</div>
                            <div>
                                <div class="cell-title">{{ $p->name }}</div>
                                <div class="cell-sub">{{ $p->category->name ?? '—' }} · {{ $p->unit->short_name ?? $p->unit->name ?? 'Piece' }} · {{ $p->brand->name ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="cell-mono">{{ $p->sku }}</div>
                        <div class="cell-sub">
                            @if($p->barcode)
                                <span style="font-family:monospace">{{ $p->barcode }}</span>
                                <div style="margin-top:6px;background:#fff;border:1px solid #eef2f7;border-radius:6px;padding:6px;max-width:220px;overflow:hidden;display:flex;justify-content:center">{!! DNS1D::getBarcodeSVG($p->barcode, 'C128', 1.2, 32, 'black', false, true) !!}</div>
                            @else
                                <span class="muted">— no barcode</span>
                                <div style="margin-top:6px;background:#fff8f0;border:1px dashed #e8b82f;border-radius:6px;padding:6px;max-width:220px;overflow:hidden;display:flex;justify-content:center">{!! DNS1D::getBarcodeSVG($p->sku, 'C128', 1, 28, 'black', false, true) !!}</div>
                                <div style="font-size:10px;color:#8a6418;margin-top:2px;text-align:center">SKU as barcode</div>
                            @endif
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
                        <div class="cell-sub">min {{ $p->min_stock }}</div>
                    </td>
                    <td class="right">TZS {{ number_format($p->buying_price,0) }}</td>
                    <td class="right" style="font-weight:800">TZS {{ number_format($p->selling_price,0) }}</td>
                    <td class="center">
                        @if($p->status==='active') <span class="tag tag-green">Active</span>
                        @elseif($p->status==='inactive') <span class="tag tag-grey">Inactive</span>
                        @else <span class="tag tag-red">Discontinued</span> @endif
                        @if($isExp) <span class="tag tag-gold" style="margin-left:4px">Expiring</span> @endif
                    </td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('products.show', encId($p->id)) }}" class="btn-icon" title="View"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                            <a href="{{ route('products.edit', encId($p->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                            @if(auth()->check() && auth()->user()->isAdmin())
                            <form id="delete-form-{{ $p->id }}" method="POST" action="{{ route('products.destroy', encId($p->id)) }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-icon danger" title="Delete" onclick="confirmModal('Delete product', 'Are you sure want to delete {{ addslashes($p->name) }}? This will remove the product and log the action. Stock history will remain via movements.', function(){ document.getElementById('delete-form-{{ $p->id }}').submit(); })"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></div><strong>No products yet</strong><p>Create your first product with SKU & barcode. Stock will be tracked via movements.</p><a href="{{ route('products.create') }}" class="btn btn-primary btn-sm" style="margin-top:10px">Add Product</a></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$products])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $products->total() }} products — duplicate barcode guard active (unique)</div></div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('.barcode-cell').forEach(function(el){
    const val = el.dataset.barcode;
    if(val && el.id){ try{ JsBarcode("#"+el.id, val, {format:"CODE128", width:1.2, height:28, displayValue:false, margin:2}); }catch(e){} }
  });
});
</script>
@endsection
