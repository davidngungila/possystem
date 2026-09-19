@extends('layouts.admin')
@section('title','Adjustments')
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">Adjustments <span class="info-icon" tabindex="0">i<span class="tooltip">Never allow silent stock editing — every adjustment logged with reason & approval</span></span></h1><p class="page-sub">Stock adjustments</p></div>
    <span class="tag tag-gold">Audit</span>
</div>

<div class="table-card">
    <form method="GET" class="table-toolbar">
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Search product, SKU, reason...">
        </div>
        <button class="btn btn-ghost btn-sm">Search</button>
    </form>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Product</th><th class="center">Type</th><th class="center">Qty</th><th class="center">Prev → New</th><th>Reason</th><th>User</th><th>At</th></tr></thead>
            <tbody>
            @forelse($movements as $m)
                <tr>
                    <td><div class="cell-title" style="font-size:13px">{{ $m->product->name ?? '—' }}</div><div class="cell-sub mono">{{ $m->product->sku ?? '' }}</div></td>
                    <td class="center"><span class="tag {{ $m->type==='adjustment' ? 'tag-gold' : ($m->type==='damage' ? 'tag-red' : 'tag-grey') }}">{{ $m->type }}</span></td>
                    <td class="center">{{ $m->quantity }}</td>
                    <td class="center"><span class="cell-mono">{{ $m->previous_stock }} → {{ $m->new_stock }}</span> <span class="tag {{ $m->new_stock > $m->previous_stock ? 'tag-green' : 'tag-red' }}" style="margin-left:4px">{{ $m->new_stock - $m->previous_stock >0 ? '+' : '' }}{{ $m->new_stock - $m->previous_stock }}</span></td>
                    <td>{{ $m->reason ?? '—' }}</td>
                    <td>{{ $m->user->name ?? 'system' }}</td>
                    <td class="muted" style="font-size:12px">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/></svg></div><strong>No adjustments yet</strong><p>Example: System Sugar 50 → Physical 48 → Difference -2 · Reason: Stock count · Approved by: Manager</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($movements->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$movements])
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px">
    <div class="panel" style="padding:16px">
        <div style="font-weight:800;color:var(--coffee-900)">Recent Movements (live)</div>
        <div style="margin-top:10px;display:flex;flex-direction:column;gap:8px">
            @forelse($all as $a)
                <div style="display:flex;justify-content:space-between;padding:8px 12px;background:var(--sand-50);border:1px solid var(--line);border-radius:10px;font-size:13px">
                    <span>{{ $a->product->name ?? '—' }} <span class="tag {{ $a->type==='in' ? 'tag-green' : ($a->type==='out' ? 'tag-red' : 'tag-gold') }}" style="margin-left:6px">{{ $a->type }}</span></span>
                    <span class="cell-mono">{{ $a->previous_stock }} → {{ $a->new_stock }}</span>
                </div>
            @empty
                <div class="muted" style="font-size:13px">No movements</div>
            @endforelse
        </div>
    </div>
    <div class="panel" style="padding:16px;background:var(--sand-50)">
        <div style="font-weight:800;color:var(--coffee-900)">Rules</div>
        <div style="font-size:13px;color:var(--ink-soft);line-height:1.6;margin-top:6px">
            <div>• Stock cannot be changed silently — every change creates <code>stock_movements</code></div>
            <div>• Adjustment requires reason & approval (Manager)</div>
            <div>• Price changes are logged in <code>audit_logs</code></div>
        </div>
    </div>
</div>
<style>@media(max-width:900px){ div[style*="grid-template-columns:1fr 1fr"]{grid-template-columns:1fr !important} }</style>
@endsection
