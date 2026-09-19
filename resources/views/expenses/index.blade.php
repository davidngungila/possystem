@extends('layouts.admin')
@section('title','Expenses')
@section('content')
<div class="page-head">
    <div><h1>Expenses</h1><p class="page-sub">Separate from purchases — Rent, Electricity, Water, Internet, Transport, Salary, Fuel, Repairs, Packaging, Other</p></div>
    <div style="display:flex;gap:8px;align-items:center">
        <span class="tag tag-grey">Total TZS {{ number_format($total,0) }}</span>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Expense</a>
    </div>
</div>

<div class="table-card">
    <form method="GET" class="table-toolbar">
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Search category, description...">
        </div>
        <button class="btn btn-ghost btn-sm">Search</button>
    </form>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Category</th><th>Description</th><th class="center">Date</th><th class="right">Amount</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($expenses as $e)
                <tr>
                    <td><span class="tag tag-gold">{{ $e->category }}</span></td>
                    <td>{{ $e->description ?? '—' }}</td>
                    <td class="center">{{ $e->expense_date ? $e->expense_date->format('Y-m-d') : $e->created_at->format('Y-m-d') }}</td>
                    <td class="right" style="font-weight:800">TZS {{ number_format($e->amount,0) }}</td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('expenses.edit', encId($e->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                            @if(auth()->check() && auth()->user()->isAdmin())
                            <form id="del-exp-{{ $e->id }}" method="POST" action="{{ route('expenses.destroy', encId($e->id)) }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-icon danger" onclick="confirmModal('Delete expense','Are you sure want to delete expense {{ $e->category }} TZS {{ number_format($e->amount,0) }}? This cannot be undone.', function(){ document.getElementById('del-exp-{{ $e->id }}').submit(); })"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/></svg></div><strong>No expenses yet</strong><p>Add operating expenses separate from purchases.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$expenses])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $expenses->total() }} expenses — Gross profit − Expenses = Net profit</div></div>
    @endif
</div>
@endsection
