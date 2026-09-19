@extends('layouts.admin')
@section('title','Inventory Reports')
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">Inventory Reports <span class="info-icon" tabindex="0">i<span class="tooltip">Current stock · Stock valuation · Stock movement · Low stock · Out of stock · Expired — live</span></span></h1><p class="page-sub">Current stock</p></div>
    <span class="tag tag-green">Live</span>
</div>

<div class="stat-grid">
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8"/></svg></div><span class="tag tag-terracotta">{{ $totalProducts ?? 0 }} products</span></div><div class="stat-label">Total Products</div><div class="stat-value">{{ $totalProducts ?? 0 }}</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/></svg></div><span class="tag tag-gold">TZS {{ number_format($stockValue ?? 0,0) }}</span></div><div class="stat-label">Stock Value</div><div class="stat-value">TZS {{ number_format($stockValue ?? 0,0) }}</div><div class="stat-sub">At buying price</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-red" style="background:var(--danger-100);color:var(--danger)"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg></div><span class="tag tag-red">{{ $outStock ?? 0 }} out</span></div><div class="stat-label">Out of Stock</div><div class="stat-value">{{ $outStock ?? 0 }}</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><span class="tag tag-blue">{{ $expiring ?? 0 }} expiring</span></div><div class="stat-label">Expiring ≤30d</div><div class="stat-value">{{ $expiring ?? 0 }}</div></div>
</div>

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-top:16px">
    <div class="panel" style="padding:16px"><div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px"><div style="font-weight:800;color:var(--coffee-900)">Stock Status</div><select onchange="updateInventoryChart(this.value)" style="padding:4px 8px;border:1px solid var(--line);border-radius:6px;font-size:12px;background:var(--white)"><option value="all">All</option><option value="in">In Stock</option><option value="low">Low</option><option value="out">Out</option></select></div><canvas id="stockStatusChart" height="180"></canvas></div>
    <div class="panel" style="padding:16px"><div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px"><div style="font-weight:800;color:var(--coffee-900)">Stock Value vs Alerts</div><select onchange="toast('Value view: '+this.value,'info')" style="padding:4px 8px;border:1px solid var(--line);border-radius:6px;font-size:12px;background:var(--white)"><option>Value</option><option>Quantity</option><option>Alerts</option></select></div><canvas id="stockValueChart" height="180"></canvas></div>
</div>

<div class="table-card" style="margin-top:16px">
    <div class="panel-head"><div class="panel-title">Current Stock</div><span class="tag tag-grey">Live</span></div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Product</th><th class="center">Stock</th><th class="right">Buying</th><th class="right">Value</th><th class="center">Status</th></tr></thead>
            <tbody>
            @forelse($products ?? [] as $p)
                <tr><td><div class="cell-title" style="font-size:13px">{{ $p->name }}</div><div class="cell-sub mono">{{ $p->sku }} @if($p->barcode)· {{ $p->barcode }}@endif</div></td><td class="center">{{ $p->current_stock }}</td><td class="right">TZS {{ number_format($p->buying_price,0) }}</td><td class="right">TZS {{ number_format($p->current_stock * $p->buying_price,0) }}</td><td class="center">@if($p->current_stock<=0)<span class="tag tag-red">Out</span>@elseif($p->current_stock <= $p->min_stock)<span class="tag tag-gold">Low</span>@else<span class="tag tag-green">OK</span>@endif</td></tr>
            @empty
                <tr><td colspan="5"><div class="empty-state" style="padding:16px"><strong>No products</strong></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const ctx1=document.getElementById('stockStatusChart');
  if(ctx1){
    const total={{ $totalProducts ?? 0 }};
    const low={{ $lowStock ?? 0 }};
    const out={{ $outStock ?? 0 }};
    const inStock=Math.max(0,total-low-out);
    new Chart(ctx1,{
      type:'doughnut',
      data:{labels:['In Stock','Low','Out'],datasets:[{data:[inStock,low,out],backgroundColor:['#2e7d6b','#e8b82f','#b33a3a'],borderWidth:0}]},
      options:{responsive:true,plugins:{legend:{position:'bottom'}},cutout:'60%'}
    });
  }
  const ctx2=document.getElementById('stockValueChart');
  if(ctx2){
    new Chart(ctx2,{
      type:'bar',
      data:{
        labels:['Stock Value','Low','Out','Expiring'],
        datasets:[{label:'Count / TZS',data:[{{ $stockValue ?? 0 }},{{ $lowStock ?? 0 }},{{ $outStock ?? 0 }},{{ $expiring ?? 0 }}],backgroundColor:['#0a4260','#e8b82f','#b33a3a','#5e88a3'],borderRadius:6}]
      },
      options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
    });
  }
});
</script>
<style>@media(max-width:900px){ div[style*="grid-template-columns:repeat(2,1fr)"]{grid-template-columns:1fr !important} }</style>
@endsection
