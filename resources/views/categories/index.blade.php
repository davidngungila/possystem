@extends('layouts.admin')
@section('title','Categories')
@section('content')
<div class="page-head">
    <div><h1>Categories</h1><p class="page-sub">Organize products — Fresh Food, Beverages, Groceries, etc. — live count</p></div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Category</a>
</div>
<div class="table-card">
    <form method="GET" class="table-toolbar">
        <div class="table-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input name="q" value="{{ $q }}" placeholder="Search category..."></div>
        <button class="btn btn-ghost btn-sm">Search</button>
    </form>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Category</th><th class="center">Products</th><th class="center">Active</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($categories as $c)
                <tr>
                    <td>
                        <div class="cell-main">
                            <div class="thumb thumb-terracotta">{{ strtoupper(substr($c->name,0,1)) }}</div>
                            <div><div class="cell-title">{{ $c->name }}</div><div class="cell-sub mono">{{ $c->slug }}</div></div>
                        </div>
                    </td>
                    <td class="center"><span class="tag tag-grey">{{ $c->products_count }}</span></td>
                    <td class="center">@if($c->is_active) <span class="tag tag-green">Active</span> @else <span class="tag tag-red">Inactive</span> @endif</td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('categories.edit', encId($c->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                            @if(auth()->check() && auth()->user()->isAdmin())
                            <form id="del-cat-{{ $c->id }}" method="POST" action="{{ route('categories.destroy', encId($c->id)) }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-icon danger" onclick="confirmModal('Delete category','Are you sure want to delete {{ addslashes($c->name) }}? This cannot be undone.', function(){ document.getElementById('del-cat-{{ $c->id }}').submit(); })"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4"><div class="empty-state"><strong>No categories</strong><p>Add categories to organize products.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$categories])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $categories->total() }} categories</div></div>
    @endif
</div>
@endsection
