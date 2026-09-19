@extends('layouts.admin')
@section('title','Customers')
@section('content')
<div class="page-head">
    <div><h1>Customers</h1><p class="page-sub">Walk-in for quick sales · Registered for credit & history</p></div>
    <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Customer</a>
</div>

<div class="table-card">
    <form method="GET" class="table-toolbar">
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Search name, phone...">
        </div>
        <button class="btn btn-ghost btn-sm">Search</button>
    </form>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Customer</th><th>Phone</th><th class="center">Type</th><th class="right">Credit</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($customers as $c)
                <tr>
                    <td>
                        <div class="cell-main">
                            <div class="thumb {{ $c->type==='registered' ? 'thumb-green' : 'thumb-grey' }}">{{ strtoupper(substr($c->name,0,1)) }}</div>
                            <div><div class="cell-title">{{ $c->name }}</div><div class="cell-sub">{{ $c->email ?? '—' }} @if($c->address) · {{ $c->address }} @endif</div></div>
                        </div>
                    </td>
                    <td><span class="cell-mono">{{ $c->phone ?? '—' }}</span></td>
                    <td class="center">@if($c->type==='registered') <span class="tag tag-green">Registered</span> @else <span class="tag tag-grey">Walk-in</span> @endif</td>
                    <td class="right">TZS {{ number_format($c->credit_balance,0) }}</td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('customers.edit', encId($c->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                            @if(auth()->check() && auth()->user()->isAdmin())
                            <form id="del-cust-{{ $c->id }}" method="POST" action="{{ route('customers.destroy', encId($c->id)) }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-icon danger" onclick="confirmModal('Delete customer','Are you sure want to delete {{ addslashes($c->name) }}? This cannot be undone.', function(){ document.getElementById('del-cust-{{ $c->id }}').submit(); })"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><strong>No customers</strong><p>Walk-in is default for POS. Add registered for credit.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$customers])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $customers->total() }} customers</div></div>
    @endif
</div>
@endsection
