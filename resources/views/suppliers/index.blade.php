@extends('layouts.admin')
@section('title','Suppliers')
@section('content')
<div class="page-head">
    <div><h1>Suppliers</h1><p class="page-sub">Phone · Email · TIN · Contact person · Purchase history</p></div>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Supplier</a>
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
            <thead><tr><th>Supplier</th><th>Contact</th><th class="center">Purchases</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($suppliers as $s)
                <tr>
                    <td>
                        <div class="cell-main">
                            <div class="thumb thumb-terracotta">{{ strtoupper(substr($s->name,0,1)) }}</div>
                            <div><div class="cell-title">{{ $s->name }}</div><div class="cell-sub">{{ $s->address ?? '—' }} @if($s->tin) · TIN {{ $s->tin }} @endif</div></div>
                        </div>
                    </td>
                    <td>
                        <div class="cell-sub"><span class="cell-mono">{{ $s->phone ?? '—' }}</span> @if($s->email) · {{ $s->email }} @endif</div>
                        <div class="cell-sub">@if($s->contact_person) Contact: {{ $s->contact_person }} @endif</div>
                    </td>
                    <td class="center"><span class="tag tag-grey">{{ $s->purchases_count }}</span></td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('suppliers.edit', encId($s->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                            @if(auth()->check() && auth()->user()->isAdmin())
                            <form id="del-sup-{{ $s->id }}" method="POST" action="{{ route('suppliers.destroy', encId($s->id)) }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-icon danger" onclick="confirmModal('Delete supplier','Are you sure want to delete {{ addslashes($s->name) }}? This cannot be undone.', function(){ document.getElementById('del-sup-{{ $s->id }}').submit(); })"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4"><div class="empty-state"><strong>No suppliers</strong><p>Add supplier to track purchases.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$suppliers])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $suppliers->total() }} suppliers</div></div>
    @endif
</div>
@endsection
