@extends('layouts.pos')
@section('title','Held Sales')
@section('content')
<div class="page-head">
    <div><h1>Held Sales</h1><p class="page-sub">Sales put on hold — resume to POS, or delete. Held cart is saved per cashier.</p></div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('pos.create') }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/></svg> New Sale</a>
    </div>
</div>

<div class="table-card">
    <div class="table-toolbar">
        <div style="display:flex;align-items:center;gap:8px">
            <span class="tag tag-grey">{{ $heldSales->total() }} held</span>
            <span class="muted" style="font-size:12px">Offline hold syncs via held_sales with reference</span>
        </div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="tableSearch" placeholder="Search reference...">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Reference</th><th>Cashier</th><th>Items</th><th class="right">Total</th><th>At</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($heldSales as $h)
                <tr>
                    <td><span class="cell-mono" style="font-weight:700">{{ $h->reference ?? 'HOLD-'.$h->id }}</span></td>
                    <td>{{ $h->cashier->name ?? '—' }}</td>
                    <td>
                        <div style="display:flex;flex-direction:column;gap:4px">
                            @foreach(array_slice($h->cart ?? [], 0, 3) as $it)
                                <div style="font-size:13px"><span class="cell-title" style="font-size:13px">{{ $it['name'] ?? '—' }}</span> <span class="muted">× {{ $it['qty'] ?? 1 }}</span> <span class="cell-mono" style="font-size:11px">{{ $it['barcode'] ?? $it['sku'] ?? '' }}</span></div>
                            @endforeach
                            @if(count($h->cart ?? []) > 3)
                                <span class="tag tag-grey">+{{ count($h->cart)-3 }} more</span>
                            @endif
                        </div>
                    </td>
                    <td class="right" style="font-weight:800">TZS {{ number_format($h->total,0) }}</td>
                    <td class="muted" style="font-size:12px">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="row-actions">
                            <form method="POST" action="{{ route('pos.held.resume', encId($h->id)) }}" style="display:inline">
                                @csrf
                                <button class="btn btn-ghost btn-sm" style="padding:6px 10px;font-size:12px;display:inline-flex;align-items:center;gap:6px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="5 12 19 12"/><polyline points="12 5 19 12 12 19"/></svg> Resume</button>
                            </form>
                            @if(auth()->check() && auth()->user()->isAdmin())
                            <form id="del-held-{{ $h->id }}" method="POST" action="{{ route('pos.held.destroy', encId($h->id)) }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-icon danger" title="Delete held" onclick="confirmModal('Delete held sale','Are you sure want to delete held sale {{ $h->reference ?? 'HOLD-'.$h->id }}? This cannot be undone.', function(){ document.getElementById('del-held-{{ $h->id }}').submit(); })"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">
                    <div class="empty-state">
                        <div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg></div>
                        <strong>No held sales</strong>
                        <p>When POS cart has items, click <em>Hold Sale</em> — it will be saved here per cashier with reference and can be resumed.</p>
                    </div>
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($heldSales->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$heldSales])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $heldSales->total() }} held sales — Stock not deducted until resumed and paid</div></div>
    @endif
</div>

<div class="panel" style="margin-top:16px;padding:16px;background:var(--sand-50)">
    <div style="font-weight:800;color:var(--coffee-900);display:flex;align-items:center;gap:8px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="7" y1="8" x2="7" y2="16"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="17" y1="8" x2="17" y2="16"/></svg> Barcode flow (held)</div>
    <div style="font-size:13px;color:var(--ink-soft);margin-top:6px;line-height:1.6;display:flex;align-items:center;gap:6px;flex-wrap:wrap"><span>Scan</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Find product</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Check stock</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Add to cart</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Hold</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Resume</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Pay</span></div>
</div>
@endsection
