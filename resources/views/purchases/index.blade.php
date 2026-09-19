@extends('layouts.admin')
@section('title','Purchases')
@section('content')
<div class="page-head">
    <div><h1>Purchase History</h1><p class="page-sub">Supplier · Invoice · Status · Stock IN on received</p></div>
    <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> New Purchase</a>
</div>

<div class="table-card">
    <form method="GET" class="table-toolbar">
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Search invoice...">
        </div>
        <button class="btn btn-ghost btn-sm">Search</button>
    </form>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Invoice</th><th>Supplier</th><th class="center">Status</th><th class="center">Date</th><th class="right">Total</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($purchases as $p)
                <tr>
                    <td><span class="cell-mono">{{ $p->invoice_number ?? '#'.$p->id }}</span></td>
                    <td><div class="cell-title" style="font-size:13px">{{ $p->supplier->name ?? '—' }}</div><div class="cell-sub">{{ $p->supplier->phone ?? '' }}</div></td>
                    <td class="center">
                        @if($p->status==='received') <span class="tag tag-green">Received</span>
                        @elseif($p->status==='ordered') <span class="tag tag-gold">Ordered</span>
                        @elseif($p->status==='draft') <span class="tag tag-grey">Draft</span>
                        @else <span class="tag tag-grey">{{ ucfirst(str_replace('_',' ',$p->status)) }}</span> @endif
                    </td>
                    <td class="center">{{ $p->purchase_date ? $p->purchase_date->format('Y-m-d') : $p->created_at->format('Y-m-d') }}</td>
                    <td class="right" style="font-weight:800">TZS {{ number_format($p->total_amount,0) }}</td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('purchases.show', encId($p->id)) }}" class="btn-icon" title="View"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                            @if(auth()->check() && auth()->user()->isAdmin())
                            <form id="del-pur-{{ $p->id }}" method="POST" action="{{ route('purchases.destroy', encId($p->id)) }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-icon danger" onclick="confirmModal('Delete purchase','Are you sure want to delete purchase {{ $p->invoice_number ?? '#'.$p->id }}? This cannot be undone.', function(){ document.getElementById('del-pur-{{ $p->id }}').submit(); })"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><strong>No purchases yet</strong><p>Create a purchase — stock IN when received.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($purchases->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$purchases])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $purchases->total() }} purchases — Stock IN on received (not on draft)</div></div>
    @endif
</div>
@endsection
