@extends('layouts.admin')
@section('title','Users')
@section('content')
<div class="page-head">
    <div><h1>Users</h1><p class="page-sub">Owner — full access · Admin — manager · Cashier — POS only</p></div>
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add User</a>
</div>

<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters">
            <span class="tag tag-green">Owner</span>
            <span class="tag tag-gold">Admin</span>
            <span class="tag tag-grey">Cashier</span>
        </div>
        <div class="table-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input id="tableSearch" placeholder="Search name, email, role..."></div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>User</th><th>Role</th><th>Shop</th><th class="center">Active</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($users as $u)
                <tr>
                    <td>
                        <div class="cell-main">
                            <div class="thumb {{ $u->role==='owner' ? 'thumb-terracotta' : ($u->role==='admin' ? 'thumb-gold' : 'thumb-green') }}">{{ strtoupper(substr($u->name,0,1)) }}</div>
                            <div>
                                <div class="cell-title">{{ $u->name }}</div>
                                <div class="cell-sub">{{ $u->email }} @if($u->phone) · {{ $u->phone }} @endif</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($u->role==='owner') <span class="tag tag-terracotta">Owner</span>
                        @elseif($u->role==='admin') <span class="tag tag-gold">Admin</span>
                        @else <span class="tag tag-green">Cashier</span> @endif
                    </td>
                    <td>
                        @if($u->isCashier() && $u->shop) <span class="tag tag-grey">{{ $u->shop->name }}</span><div class="cell-sub">{{ $u->shop->code }}</div>
                        @elseif($u->isCashier()) <span class="tag tag-grey">No Shop</span>
                        @else <span class="tag tag-green">All shops</span>
                        @endif
                    </td>
                    <td class="center">@if($u->is_active) <span class="tag tag-green">Active</span> @else <span class="tag tag-red">Inactive</span> @endif</td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('users.edit', encId($u->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                            @if($u->id !== auth()->id())
                            @if(auth()->check() && auth()->user()->isAdmin())
                            <form id="delete-user-{{ $u->id }}" method="POST" action="{{ route('users.destroy', encId($u->id)) }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-icon danger" title="Delete" onclick="confirmModal('Delete user', 'Are you sure want to delete {{ addslashes($u->name) }} ({{ $u->email }})? This cannot be undone.', function(){ document.getElementById('delete-user-{{ $u->id }}').submit(); })"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                            </form>
                            @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state"><strong>No users</strong></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$users])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $users->total() }} users — Owner / Admin / Cashier</div></div>
    @endif
</div>
@endsection
