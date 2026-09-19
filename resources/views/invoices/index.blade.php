@extends('layouts.admin')
@section('title','Invoices')
@section('content')
<div class="page-head">
    <div><h1>Invoices</h1><p class="page-sub">Final sales documents — INV-YYYY-###### · walk-in sales auto-invoice</p></div>
    <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">New Invoice</a>
</div>

<div class="table-card">
    <form method="GET" class="table-toolbar">
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Search number, customer...">
        </div>
        <select name="st" onchange="this.form.submit()" style="padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:13px">
            <option value="all" @selected(($st??'')==='all' || !$st)>All Payment Statuses</option>
            <option value="unpaid" @selected($st==='unpaid')>Unpaid</option>
            <option value="partially_paid" @selected($st==='partially_paid')>Partially Paid</option>
            <option value="paid" @selected($st==='paid')>Paid</option>
            <option value="overdue" @selected($st==='overdue')>Overdue</option>
            <option value="cancelled" @selected($st==='cancelled')>Cancelled</option>
        </select>
        <button class="btn btn-ghost btn-sm">Search</button>
    </form>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Number</th><th>Customer</th><th>Date</th><th>Due</th><th class="right">Total</th><th class="right">Paid</th><th class="right">Balance</th><th class="center">Status</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($invoices as $inv)
                @php $isOverdue = $inv->payment_status === 'unpaid' && $inv->due_date && \Carbon\Carbon::parse($inv->due_date)->lt(today()); @endphp
                <tr>
                    <td><span class="cell-mono">{{ $inv->invoice_number }}</span>@if($inv->sale)<div style="font-size:11px;color:var(--ink-soft)">sale {{ $inv->sale->receipt_number }}</div>@endif</td>
                    <td>{{ $inv->customer->name ?? 'Walk-in' }}</td>
                    <td>{{ \Carbon\Carbon::parse($inv->invoice_date)->format('d/m/Y') }}</td>
                    <td>{{ $inv->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d/m/Y') : '—' }}</td>
                    <td class="right" style="font-weight:800">TZS {{ number_format($inv->total_amount,0) }}</td>
                    <td class="right" style="color:var(--ink-soft)">TZS {{ number_format($inv->paid_amount,0) }}</td>
                    <td class="right" style="font-weight:700;@if($inv->balance_due>0)color:var(--terracotta-600)@endif">TZS {{ number_format($inv->balance_due,0) }}</td>
                    <td class="center">
                        @php $c=['unpaid'=>'tag-terracotta','partially_paid'=>'tag-gold','paid'=>'tag-green','overdue'=>'tag-red','cancelled'=>'tag-grey']; @endphp
                        <span class="tag {{ $isOverdue ? 'tag-red' : ($c[$inv->payment_status] ?? 'tag-grey') }}">{{ $isOverdue ? 'Overdue' : ucfirst(str_replace('_',' ',$inv->payment_status)) }}</span>
                    </td>
                    <td><div class="row-actions">
                        <a href="{{ route('invoices.show', encId($inv->id)) }}" class="btn-icon" title="View"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                        @if($inv->payment_status !== 'cancelled')
                        <a href="{{ route('invoices.edit', encId($inv->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5z"/></svg></a>
                        @endif
                        <a href="{{ route('invoices.print', encId($inv->id)) }}" class="btn-icon" title="Print" target="_blank"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg></a>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="9"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg></div><strong>No invoices yet</strong><p>Convert a quotation/proforma or complete a POS sale to generate invoices.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$invoices])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $invoices->total() }} invoices</div></div>
    @endif
</div>
@endsection