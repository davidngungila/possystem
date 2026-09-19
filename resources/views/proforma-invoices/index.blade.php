@extends('layouts.admin')
@section('title','Proforma Invoices')
@section('content')
<div class="page-head">
    <div><h1>Proforma Invoices</h1><p class="page-sub">Provisional invoices awaiting payment — PI-YYYY-######</p></div>
    <a href="{{ route('proforma-invoices.create') }}" class="btn btn-primary btn-sm">New Proforma Invoice</a>
</div>

<div class="table-card">
    <form method="GET" class="table-toolbar">
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Search number, customer...">
        </div>
        <select name="st" onchange="this.form.submit()" style="padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:13px">
            <option value="all" @selected(($st??'')==='all' || !$st)>All Statuses</option>
            @foreach(['draft','sent','accepted','partially_paid','paid','converted','cancelled','expired'] as $s)
                <option value="{{ $s }}" @selected($st===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
        <button class="btn btn-ghost btn-sm">Search</button>
    </form>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Number</th><th>Customer</th><th>Date</th><th class="right">Total</th><th class="right">Paid</th><th class="center">Status</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($proformas as $p)
                <tr>
                    <td><span class="cell-mono">{{ $p->proforma_number }}</span>@if($p->quotation)<div style="font-size:11px;color:var(--ink-soft)">from {{ $p->quotation->quotation_number }}</div>@endif</td>
                    <td>{{ $p->customer->name ?? 'Walk-in' }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->date)->format('d/m/Y') }}</td>
                    <td class="right" style="font-weight:800">TZS {{ number_format($p->total_amount,0) }}</td>
                    <td class="right" style="color:var(--ink-soft)">TZS {{ number_format($p->paid_amount,0) }}</td>
                    <td class="center">
                        @php $c=['draft'=>'tag-grey','sent'=>'tag-blue','accepted'=>'tag-green','partially_paid'=>'tag-gold','paid'=>'tag-green','converted'=>'tag-gold','cancelled'=>'tag-red','expired'=>'tag-terracotta']; @endphp
                        <span class="tag {{ $c[$p->status] ?? 'tag-grey' }}">{{ ucfirst(str_replace('_',' ',$p->status)) }}</span>
                    </td>
                    <td><div class="row-actions">
                        <a href="{{ route('proforma-invoices.show', encId($p->id)) }}" class="btn-icon" title="View"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                        @if(!in_array($p->status,['converted','cancelled']))
                        <a href="{{ route('proforma-invoices.edit', encId($p->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5z"/></svg></a>
                        @endif
                        <a href="{{ route('invoices.create', ['from'=>'proforma','id'=>encId($p->id)]) }}" class="btn-icon" title="Invoice from this Proforma" onclick="return confirm('Convert to Invoice?')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></a>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg></div><strong>No proforma invoices yet</strong><p>Convert an accepted quotation to a proforma, or create one directly.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($proformas->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$proformas])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $proformas->total() }} proforma invoices</div></div>
    @endif
</div>
@endsection