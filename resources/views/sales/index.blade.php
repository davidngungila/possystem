@extends('layouts.admin')
@section('title','Sales History')
@section('content')
<div class="page-head">
    <div><h1>Sales History</h1><p class="page-sub">Search by receipt, date, cashier, payment — view, print, return</p></div>
    <a href="{{ route('pos.create') }}" class="btn btn-primary btn-sm">New Sale</a>
</div>

<div class="table-card">
    <form method="GET" class="table-toolbar">
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Search receipt, customer...">
        </div>
        <button class="btn btn-ghost btn-sm">Search</button>
    </form>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Receipt</th><th>Date</th><th>Cashier</th><th>Customer</th><th class="right">Total</th><th class="center">Payment</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($sales as $s)
                <tr>
                    <td><span class="cell-mono">{{ $s->receipt_number }}</span></td>
                    <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $s->cashier->name ?? '—' }}</td>
                    <td>{{ $s->customer->name ?? 'Walk-in' }}</td>
                    <td class="right" style="font-weight:800">TZS {{ number_format($s->total_amount,0) }}</td>
                    <td class="center">
                        @foreach($s->payments as $pay)
                            <span class="tag tag-grey">{{ $pay->payment_method }} {{ number_format($pay->amount,0) }}</span>
                        @endforeach
                    </td>
                    <td><div class="row-actions"><a href="{{ route('sales.show', encId($s->id)) }}" class="btn-icon" title="View"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>@if($s->status !== 'returned')<a href="{{ route('returns.create', ['sale'=>encId($s->id)]) }}" class="btn-icon" title="Return"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg></a>@else<span class="tag tag-red" style="font-size:10px">Returned</span>@endif</div></td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg></div><strong>No sales yet</strong><p>Use POS to create first sale. Barcode scan → stock deduction → receipt.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($sales->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$sales])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $sales->total() }} sales</div></div>
    @endif
</div>
@endsection
