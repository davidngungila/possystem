@extends('layouts.admin')
@section('title','Barcode Management')
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">Barcode Management <span class="info-icon" tabindex="0">i<span class="tooltip">EAN-13, EAN-8, UPC, Code128, Code39, QR, Internal — assign / change / remove / generate / print / duplicate guard</span></span></h1><p class="page-sub">Barcode types</p></div>
    <span class="tag tag-terracotta">Live</span>
</div>

@if($duplicates->isNotEmpty())
    <div style="background:var(--danger-100);border:1px solid #e8b4b0;color:var(--danger);padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Duplicate barcode detected: {{ $duplicates->implode(', ') }} — second assignment blocked until admin resolves
    </div>
@endif

<div class="table-card">
    <form method="GET" class="table-toolbar">
        <div class="chip-filters">
            <span class="tag tag-grey">Types: EAN-13 · EAN-8 · UPC · Code128 · Code39 · QR · Internal</span>
        </div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Search name, SKU, barcode...">
        </div>
    </form>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Product</th><th>SKU</th><th>Barcode</th><th class="center">Type</th><th class="center">Status</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($products as $p)
                <tr @if($duplicates->contains($p->barcode)) style="background:var(--danger-100)" @endif>
                    <td>
                        <div class="cell-main">
                            <div class="thumb thumb-terracotta">{{ strtoupper(substr($p->name,0,1)) }}</div>
                            <div><div class="cell-title">{{ $p->name }}</div><div class="cell-sub">{{ $p->category->name ?? '—' }} · {{ $p->brand->name ?? '—' }}</div></div>
                        </div>
                    </td>
                    <td><span class="cell-mono">{{ $p->sku }}</span></td>
                    <td>
                        @if($p->barcode)
                            <span class="cell-mono" style="font-weight:700">{{ $p->barcode }}</span>
                            <div style="margin-top:6px;background:#fff;border:1px solid #eef2f7;border-radius:6px;padding:6px;max-width:200px;overflow:hidden;display:flex;justify-content:center">{!! DNS1D::getBarcodeSVG($p->barcode, 'C128', 1.2, 32, 'black', false, true) !!}</div>
                        @else
                            <span class="muted">— no barcode</span>
                            <div style="margin-top:6px;background:#fff8f0;border:1px dashed #e8b82f;border-radius:6px;padding:6px;max-width:200px;overflow:hidden;display:flex;justify-content:center">{!! DNS1D::getBarcodeSVG($p->sku, 'C128', 1, 28, 'black', false, true) !!}</div>
                        @endif
                    </td>
                    <td class="center">
                        @php
                            $len = strlen($p->barcode ?? '');
                            $type = $len==13 ? 'EAN-13' : ($len==12 ? 'UPC' : ($len==8 ? 'EAN-8' : ($p->barcode ? 'Code128' : '—')));
                        @endphp
                        <span class="tag tag-grey">{{ $type }}</span>
                    </td>
                    <td class="center">
                        @if($p->barcode && $duplicates->contains($p->barcode))
                            <span class="tag tag-red">Duplicate</span>
                        @elseif($p->barcode)
                            <span class="tag tag-green">Assigned</span>
                        @else
                            <span class="tag tag-gold">Missing</span>
                        @endif
                    </td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('products.show', encId($p->id)) }}" class="btn-icon" title="View"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                            <a href="{{ route('products.edit', encId($p->id)) }}" class="btn-icon" title="Change barcode"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                            <button class="btn-icon" title="Print" onclick="toast('Print barcode for {{ $p->sku }}','info')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg></button>
                            <button class="btn-icon" title="Scan test" onclick="toast('Scan {{ $p->barcode ?? $p->sku }} → found {{ $p->name }}','success')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="7" y1="8" x2="7" y2="16"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="17" y1="8" x2="17" y2="16"/></svg></button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><strong>No products</strong></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$products])
    @endif
</div>

<div class="panel" style="margin-top:16px;padding:16px;background:var(--sand-50)">
    <div style="font-weight:800;color:var(--coffee-900)">Barcode actions</div>
    <div style="font-size:13px;color:var(--ink-soft);line-height:1.6;margin-top:6px;display:flex;gap:8px;flex-wrap:wrap">
        <span class="tag tag-green">Assign</span> <span class="tag tag-gold">Change</span> <span class="tag tag-red">Remove</span> <span class="tag tag-grey">Generate</span> <span class="tag tag-terracotta">Print</span> <span class="tag tag-blue">Search</span> <span class="tag tag-grey">Scan</span>
        <span style="margin-left:8px;color:var(--ink-soft)">SKU = internal code · Barcode = scanned code · Real scannable CODE128 below · Duplicate blocked unless admin resolves</span>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('.barcode-cell').forEach(function(el){
    const val = el.dataset.barcode;
    if(!val) return;
    try{ JsBarcode("#"+el.id, val, {format:"CODE128", width:1.2, height:32, displayValue:false, margin:2}); }catch(e){}
  });
});
</script>
@endsection
