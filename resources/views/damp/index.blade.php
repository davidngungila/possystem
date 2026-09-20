@extends('layouts.admin')
@section('title','Sample Catalogue (Damp)')
@section('content')
<div class="page-head">
    <div>
        <h1 style="display:flex;align-items:center;gap:10px">Sample Catalogue <span class="tag tag-gold">Damp</span> <span class="info-icon" tabindex="0">i<span class="tooltip">Demo/sample products — not counted in inventory. Use as template when creating real products. 3080 damp vs {{ $realCount }} real.</span></span></h1>
        <p class="page-sub">Damp / demo products — {{ $sampleCount }} samples · Not counted in Stock ({{ $realCount }} real products) · Select to autofill on product create</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">Add Real Product</a>
</div>

<div class="panel" style="padding:12px 16px;background:var(--sand-50);border:1px solid var(--line);margin-bottom:16px;display:flex;gap:12px;flex-wrap:wrap;align-items:center">
    <span class="tag tag-grey">Damp: {{ $sampleCount }}</span>
    <span class="tag tag-green">Real: {{ $realCount }}</span>
    <span style="font-size:12px;color:var(--ink-soft)">Stock shows only real ({{ $realCount }} active) — damp excluded via <code>is_sample</code></span>
</div>

<div class="table-card">
    <form method="GET" class="table-toolbar">
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;flex:1">
            <div class="table-search" style="flex:1;min-width:200px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input name="q" value="{{ $q }}" placeholder="Search sample name, SKU..."></div>
            <select name="shop_type" style="padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;background:#fff;min-width:180px">
                <option value="">All Shop Types</option>
                @foreach($shopTypes as $key => $t)
                    <option value="{{ $key }}" @selected($shopType==$key)>{{ $t['label'] }}</option>
                @endforeach
            </select>
            <button class="btn btn-ghost btn-sm">Filter</button>
            @if($q || $shopType)<a href="{{ route('damp.index') }}" class="btn btn-ghost btn-sm">Clear</a>@endif
        </div>
    </form>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Product</th><th>Category</th><th>Unit</th><th class="center">Shop Type</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($products as $p)
                <tr>
                    <td>
                        <div class="cell-main">
                            <div class="thumb thumb-terracotta">{{ strtoupper(substr($p->name,0,1)) }}</div>
                            <div>
                                <div class="cell-title">{{ $p->name }}</div>
                                <div class="cell-sub mono">{{ $p->sku }} @if($p->barcode) · {{ $p->barcode }} @endif</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="tag tag-grey">{{ $p->category->name ?? '—' }}</span></td>
                    <td>{{ $p->unit->name ?? '—' }} @if($p->unit->short_name) <span class="cell-sub">({{ $p->unit->short_name }})</span> @endif</td>
                    <td class="center">
                        @php $cats = $p->category?->shopTypesArray() ?? []; @endphp
                        @if(empty($cats))
                            <span class="tag tag-grey">All</span>
                        @else
                            <span class="tag tag-gold" style="font-size:11px">{{ count($cats) }} types</span>
                        @endif
                    </td>
                    <td>
                        <div class="row-actions" style="justify-content:flex-end">
                            <a href="{{ route('products.create', ['sample_id' => $p->id]) }}" class="btn btn-primary btn-sm" style="padding:6px 10px;font-size:12px">Use as Template</a>
                            <a href="{{ route('products.show', encId($p->id)) }}" class="btn-icon" title="View"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state"><strong>No damp products</strong><p>No samples match filter.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$products])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $products->total() }} damp products — {{ $sampleCount }} total damp in catalogue</div></div>
    @endif
</div>

<div class="panel" style="margin-top:16px;padding:16px;background:var(--sand-50)">
    <div style="font-weight:800;color:var(--coffee-900)">How damp catalogue works</div>
    <div style="font-size:13px;color:var(--ink-soft);line-height:1.6;margin-top:6px">
        <div>• Damp products have <code>is_sample=true</code> — excluded from <code>/products</code> and <code>/stock</code> counts (now {{ $realCount }} real vs {{ $sampleCount }} damp).</div>
        <div>• Use <strong>Search from Sample Catalogue (damp)</strong> on <a href="{{ route('products.create') }}" style="color:var(--terracotta-600);font-weight:700">Add Product</a> or click <strong>Use as Template</strong> here — autofills category, unit, tax, etc., then edit SKU/barcode before save as real product.</div>
        <div>• Stock value, low-stock, and inventory reports count only real products.</div>
    </div>
</div>
@endsection
