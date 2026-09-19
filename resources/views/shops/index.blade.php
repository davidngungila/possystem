@extends('layouts.admin')
@section('title','Shops')
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">Shops <span class="info-icon" tabindex="0">i<span class="tooltip">Owner manages all shops · Cashier locked to assigned shop</span></span></h1><p class="page-sub">Shops management</p></div>
    @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
    <a href="{{ route('shops.create') }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Shop</a>
    @endif
</div>

@if(isCompanyView())
<div class="panel" style="padding:12px 16px;background:var(--acacia-100);border:1px solid #c8d7a8;margin-bottom:16px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
    <span class="tag tag-green">All Shops — Company</span>
    <span style="font-size:13px;color:var(--coffee-800)">Viewing combined data for all shops — dashboard & reports show company totals. Switch to a shop to manage its products/purchases.</span>
</div>
@endif

<div class="table-card">
    <form method="GET" class="table-toolbar">
        <div class="table-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input name="q" value="{{ $q }}" placeholder="Search shop..."></div>
        <button class="btn btn-ghost btn-sm">Search</button>
    </form>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Shop</th><th>Code</th><th>Contact</th><th class="center">Products</th><th class="center">Sales</th><th class="center">Active</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($shops as $shop)
                <tr @if(currentShopId()==$shop->id) style="background:var(--sand-50)" @endif>
                    <td>
                        <div class="cell-main">
                            <div class="thumb" style="background:linear-gradient(135deg,var(--terracotta-500),var(--gold-500));color:#fff">{{ strtoupper(substr($shop->name,0,1)) }}</div>
                            <div><div class="cell-title">{{ $shop->name }}</div><div class="cell-sub">{{ $shop->address ?? '—' }}</div></div>
                        </div>
                    </td>
                    <td><span class="cell-mono">{{ $shop->code }}</span></td>
                    <td><div class="cell-title" style="font-size:13px">{{ $shop->phone ?? '—' }}</div><div class="cell-sub">{{ $shop->email ?? '' }}</div></td>
                    <td class="center"><span class="tag tag-grey">{{ $shop->products_count ?? $shop->products()->count() }}</span></td>
                    <td class="center"><span class="tag tag-grey">{{ $shop->sales_count ?? $shop->sales()->count() }}</span></td>
                    <td class="center">@if($shop->is_active) <span class="tag tag-green">Active</span> @else <span class="tag tag-red">Inactive</span> @endif</td>
                    <td>
                        <div class="row-actions">
                            @if(currentShopId() == $shop->id)
                                <span class="tag tag-gold">Current</span>
                            @else
                                @if(auth()->check() && auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('shops.switch') }}" style="display:inline">
                                    @csrf
                                    <input type="hidden" name="shop_id" value="{{ encId($shop->id) }}">
                                    <button class="btn btn-ghost btn-sm" style="padding:4px 8px;font-size:12px" @if(!auth()->user()->isOwner() && !auth()->user()->isAdmin() && auth()->user()->shop_id != $shop->id) disabled title="Assigned shop only" @endif>Switch</button>
                                </form>
                            @endif
                            @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
                            <a href="{{ route('shops.edit', encId($shop->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                            @if(auth()->check() && auth()->user()->isAdmin())
                            <form id="del-shop-{{ $shop->id }}" method="POST" action="{{ route('shops.destroy', encId($shop->id)) }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-icon danger" onclick="confirmModal('Delete shop','Are you sure want to delete {{ addslashes($shop->name) }}? This cannot be undone.', function(){ document.getElementById('del-shop-{{ $shop->id }}').submit(); })"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                            </form>
                            @endif
                            @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty-state"><strong>No shops</strong><p>Create your first shop.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($shops->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$shops])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $shops->total() }} shops — owner sees all, cashier sees assigned only</div></div>
    @endif
</div>

<div class="panel" style="margin-top:16px;padding:16px;background:var(--sand-50)">
    <div style="font-weight:800;color:var(--coffee-900)">How shops work</div>
    <div style="font-size:13px;color:var(--ink-soft);line-height:1.6;margin-top:6px">
        <div>• Owner: switch to a shop to manage its products/purchases/sales — data scoped to that shop</div>
        <div>• Select <strong>All Shops — Company</strong> to see full company details combined (dashboard & reports show totals across all shops for company progress)</div>
        <div>• Cashier is locked to <code>shop_id</code> assigned by owner — cannot switch, sales/reports filtered to assigned shop only</div>
        <div>• Use the top-bar shop switcher to change view — <strong>Main Shop</strong> is one branch, <strong>All Shops</strong> is company</div>
    </div>
</div>
@endsection
