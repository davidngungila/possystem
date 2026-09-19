@extends('layouts.admin')
@section('title','Payments')
@section('content')
<div class="page-head">
    <div><h1>Payments</h1><p class="page-sub">Every payment belongs to a transaction — Cash, M-Pesa, Airtel Money, Mixx, HaloPesa, Bank, Card</p></div>
    <span class="tag tag-green">Live</span>
</div>

<div class="table-card">
    <form method="GET" class="table-toolbar">
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Search receipt, method, reference...">
        </div>
        <button class="btn btn-ghost btn-sm">Search</button>
    </form>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Receipt</th><th>Date</th><th>Method</th><th>Reference</th><th class="right">Amount</th></tr></thead>
            <tbody>
            @forelse($payments as $p)
                <tr>
                    <td><span class="cell-mono">{{ $p->sale->receipt_number ?? '—' }}</span></td>
                    <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                    <td><span class="tag tag-grey">{{ ucfirst(str_replace('_',' ',$p->payment_method)) }}</span></td>
                    <td><span class="cell-mono">{{ $p->reference ?? '—' }}</span></td>
                    <td class="right" style="font-weight:700">TZS {{ number_format($p->amount,0) }}</td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/></svg></div><strong>No payments yet</strong><p>Payments are created on POS sale — split payments supported.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$payments])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $payments->total() }} payments — Cash collected vs Mobile-money reconciliation</div></div>
    @endif
</div>
@endsection
