@extends('layouts.admin')
@section('title','Returns')
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">Returns <span class="info-icon" tabindex="0">i<span class="tooltip">Original sale → select item → quantity → reason → approval → stock returned → refund/credit</span></span></h1><p class="page-sub">Returns — stock restored</p></div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('sales.index') }}" class="btn btn-ghost btn-sm">Sales History</a>
        <a href="{{ route('returns.create') }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/></svg> New Return</a>
    </div>
</div>

<div class="table-card">
    <div class="table-toolbar">
        <div style="display:flex;align-items:center;gap:8px">
            <span class="tag tag-grey">{{ $returns->total() }} returns</span>
            <span class="muted" style="font-size:12px">Never delete a completed sale — returns reference original sale</span>
        </div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="tableSearch" placeholder="Search receipt, reason...">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Original Sale</th><th>Item</th><th class="center">Qty</th><th>Reason</th><th class="center">Stock</th><th>Returned At</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($returns as $r)
                <tr>
                    <td><span class="cell-mono">{{ $r->receipt_number }}</span><div class="cell-sub">{{ $r->created_at->format('d/m/Y H:i') }} · {{ $r->cashier->name ?? '—' }}</div></td>
                    <td>
                        @foreach($r->items->take(2) as $it)
                            <div class="cell-title" style="font-size:13px">{{ $it->product->name ?? '—' }}</div>
                        @endforeach
                        @if($r->items->count() > 2)<span class="tag tag-grey">+{{ $r->items->count()-2 }} more</span>@endif
                    </td>
                    <td class="center">{{ $r->items->sum('quantity') }}</td>
                    <td><span class="tag tag-gold">{{ $r->return_reason ?? '—' }}</span></td>
                    <td class="center"><span class="tag tag-green">Returned</span></td>
                    <td class="muted" style="font-size:12px">{{ $r->returned_at ? \Carbon\Carbon::parse($r->returned_at)->format('d/m/Y H:i') : $r->updated_at->format('d/m H:i') }}<div class="cell-sub">{{ $r->returnedBy->name ?? '' }}</div></td>
                    <td><div class="row-actions"><a href="{{ route('returns.show', encId($r->id)) }}" class="btn-icon" title="View details"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a><a href="{{ route('sales.show', encId($r->id)) }}" class="btn-icon" title="Original sale"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></a></div></td>
                </tr>
            @empty
                <tr><td colspan="7">
                    <div class="empty-state">
                        <div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg></div>
                        <strong>No returns yet</strong>
                        <p>Workflow: Original Sale → Return → Select Item → Quantity → Reason (Wrong product / Damaged / Changed mind) → Approval → Stock Returned → Refund/Credit. Price changes logged.</p>
                        <div style="margin-top:10px;display:flex;gap:8px;justify-content:center;flex-wrap:wrap">
                            <span class="tag tag-grey">Wrong product</span><span class="tag tag-red">Damaged</span><span class="tag tag-gold">Changed mind</span><span class="tag tag-green">Wrong quantity</span>
                        </div>
                    </div>
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($returns->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$returns])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $returns->total() }} returns — stock returned via stock_movements (return)</div></div>
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px">
    <div class="panel" style="padding:16px">
        <div style="font-weight:800;color:var(--coffee-900)">Return Reasons</div>
        <div style="margin-top:10px;display:flex;flex-wrap:wrap;gap:8px">
            <span class="tag tag-grey">Wrong product</span>
            <span class="tag tag-red">Damaged</span>
            <span class="tag tag-gold">Customer changed mind</span>
            <span class="tag tag-terracotta">Wrong quantity</span>
            <span class="tag tag-blue">Other</span>
        </div>
        <div style="font-size:12px;color:var(--ink-soft);margin-top:10px">Every return references original sale, creates stock movement <code>return</code> and audit log.</div>
    </div>
    <div class="panel" style="padding:16px;background:var(--sand-50)">
        <div style="font-weight:800;color:var(--coffee-900);display:flex;align-items:center;gap:8px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> Rules</div>
        <div style="font-size:13px;color:var(--ink-soft);line-height:1.6;margin-top:6px">
            <div>• Completed sales cannot be deleted</div>
            <div>• Returns require approval & reason</div>
            <div>• Stock returned via <code>stock_movements</code></div>
        </div>
    </div>
</div>
<style>@media(max-width:900px){ div[style*="grid-template-columns:1fr 1fr"]{grid-template-columns:1fr !important} }</style>
@endsection
