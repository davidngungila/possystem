@extends('layouts.admin')
@section('title','Stock Count')
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">Stock Count <span class="info-icon" tabindex="0">i<span class="tooltip">System stock → enter physical count → compare → show differences → approve adjustments (never silent edit)</span></span></h1><p class="page-sub">Stock count</p></div>
    <span class="tag tag-gold">Live</span>
</div>

<div class="table-card">
    <form method="GET" class="table-toolbar">
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Search product, SKU...">
        </div>
        <button class="btn btn-ghost btn-sm">Search</button>
    </form>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Product</th><th class="center">System Stock</th><th class="center">Physical Qty</th><th class="center">Difference</th><th class="center">Status</th><th class="right">Actions</th></tr></thead>
            <tbody>
            @forelse($products as $p)
                <tr>
                    <td>
                        <div class="cell-main">
                            <div class="thumb thumb-terracotta">{{ strtoupper(substr($p->name,0,1)) }}</div>
                            <div><div class="cell-title">{{ $p->name }}</div><div class="cell-sub mono">{{ $p->sku }} @if($p->barcode) · {{ $p->barcode }} @endif</div></div>
                        </div>
                    </td>
                    <td class="center"><span class="tag tag-grey">{{ $p->current_stock }}</span></td>
                    <td class="center"><input type="number" placeholder="0" style="width:80px;padding:6px 8px;border:1.5px solid var(--line);border-radius:8px;text-align:center" class="phys-input" data-system="{{ $p->current_stock }}"></td>
                    <td class="center"><span class="tag tag-grey diff">—</span></td>
                    <td class="center"><span class="tag tag-green">OK</span></td>
                    <td><div class="row-actions"><a href="{{ route('products.show', encId($p->id)) }}" class="btn-icon" title="View"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/></svg></a></div></td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><strong>No products</strong></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$products])
    @endif
    <div class="table-pagination" style="justify-content:flex-end;gap:8px">
        <button class="btn btn-ghost btn-sm" onclick="toast('Enter physical quantities first','info')">Compare</button>
        <button class="btn btn-primary btn-sm" onclick="confirmModal('Approve adjustments','Are you sure want to approve adjustments for differences? This will create stock movements with reason & approval.', function(){ toast('Adjustments approved — stock updated','success') })">Approve Adjustments</button>
    </div>
</div>

<div class="panel" style="margin-top:16px;padding:16px;background:var(--sand-50);">
    <div style="font-weight:800;color:var(--coffee-900);display:flex;align-items:center;gap:8px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg> Workflow</div>
    <div style="font-size:13px;color:var(--ink-soft);margin-top:6px;line-height:1.6">Start Stock Count → System displays products → Enter physical quantity → Compare → Show differences → Approve adjustments → Stock updated (via <code>stock_movements</code> with approval).</div>
</div>

<script>
document.querySelectorAll('.phys-input').forEach(inp=>{
    inp.addEventListener('input', ()=>{
        const tr=inp.closest('tr');
        const sys=parseInt(inp.dataset.system)||0;
        const phys=parseInt(inp.value)||0;
        const diff=phys - sys;
        const el=tr.querySelector('.diff');
        const status=tr.querySelector('td:nth-child(5) .tag');
        if(inp.value===''){ el.textContent='—'; el.className='tag tag-grey'; status.textContent='OK'; status.className='tag tag-green'; return; }
        el.textContent=(diff>0?'+':'')+diff;
        if(diff===0){ el.className='tag tag-green'; status.textContent='OK'; status.className='tag tag-green'; }
        else if(diff<0){ el.className='tag tag-red'; status.textContent='Short'; status.className='tag tag-red'; }
        else { el.className='tag tag-gold'; status.textContent='Over'; status.className='tag tag-gold'; }
    });
});
</script>
@endsection
