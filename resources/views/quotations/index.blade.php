@extends('layouts.admin')
@section('title','Quotations')
@section('content')
<div class="page-head">
    <div><h1>Quotations</h1><p class="page-sub">Customer price requests — QT-YYYY-###### · convert to Proforma or Invoice</p></div>
    <a href="{{ route('quotations.create') }}" class="btn btn-primary btn-sm">New Quotation</a>
</div>

<div class="table-card">
    <form method="GET" class="table-toolbar">
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Search number, customer...">
        </div>
        <select name="st" onchange="this.form.submit()" style="padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:13px">
            <option value="all" @selected(($st??'')==='all' || !$st)>All Statuses</option>
            @foreach(['draft','sent','accepted','rejected','expired','converted'] as $s)
                <option value="{{ $s }}" @selected($st===$s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button class="btn btn-ghost btn-sm">Search</button>
    </form>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Number</th><th>Customer</th><th>Date</th><th>Valid Until</th><th class="right">Total</th><th class="center">Status</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($quotations as $q)
                <tr>
                    <td><span class="cell-mono">{{ $q->quotation_number }}</span></td>
                    <td>{{ $q->customer->name ?? 'Walk-in' }}</td>
                    <td>{{ \Carbon\Carbon::parse($q->date)->format('d/m/Y') }}</td>
                    <td>{{ $q->valid_until ? \Carbon\Carbon::parse($q->valid_until)->format('d/m/Y') : '—' }}</td>
                    <td class="right" style="font-weight:800">TZS {{ number_format($q->total_amount,0) }}</td>
                    <td class="center">
                        @php
                            $c=['draft'=>'tag-grey','sent'=>'tag-blue','accepted'=>'tag-green','rejected'=>'tag-red','expired'=>'tag-terracotta','converted'=>'tag-gold'];
                        @endphp
                        <span class="tag {{ $c[$q->status] ?? 'tag-grey' }}">{{ ucfirst($q->status) }}</span>
                    </td>
                    <td><div class="row-actions">
                        <a href="{{ route('quotations.show', encId($q->id)) }}" class="btn-icon" title="View"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                        @if($q->status !== 'converted')
                        <a href="{{ route('quotations.edit', encId($q->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5z"/></svg></a>
                        <a href="{{ route('quotations.convert-proforma', encId($q->id)) }}" class="btn-icon" title="Convert to Proforma" onclick="return confirm('Convert to Proforma Invoice?')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg></a>
                        <a href="{{ route('quotations.convert-invoice', encId($q->id)) }}" class="btn-icon" title="Convert directly to Invoice (no proforma)" onclick="return confirm('Skip proforma and convert straight to Invoice?')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></a>
                        @endif
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg></div><strong>No quotations yet</strong><p>Create a quotation when a customer asks for prices before buying.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($quotations->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$quotations])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $quotations->total() }} quotations</div></div>
    @endif
</div>
@endsection