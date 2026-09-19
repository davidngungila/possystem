@extends('layouts.admin')
@section('title','Profit Report')
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">Profit Report <span class="info-icon" tabindex="0">i<span class="tooltip">Selling − Cost = Gross profit · Gross − Expenses = Net profit — live per sale</span></span></h1><p class="page-sub">Profit</p></div>
    <span class="tag tag-green">Live</span>
</div>

<div class="stat-grid">
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/></svg></div><span class="tag tag-terracotta">Sales</span></div><div class="stat-label">Total Sales</div><div class="stat-value">{{ $salesCount ?? 0 }}</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/></svg></div><span class="tag tag-gold">Gross</span></div><div class="stat-label">Gross Profit</div><div class="stat-value" style="color:var(--acacia-600)">TZS {{ number_format($totalProfit ?? 0,0) }}</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-red" style="background:var(--danger-100);color:var(--danger)"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg></div><span class="tag tag-red">Expenses</span></div><div class="stat-label">Expenses</div><div class="stat-value">TZS {{ number_format($totalExpenses ?? 0,0) }}</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg></div><span class="tag tag-blue">Net</span></div><div class="stat-label">Net Profit</div><div class="stat-value">TZS {{ number_format($netProfit ?? 0,0) }}</div></div>
</div>

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-top:16px">
    <div class="panel" style="padding:16px"><div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px"><div style="font-weight:800;color:var(--coffee-900)">Profit Breakdown — Gross vs Net</div><select onchange="toast('Breakdown: '+this.value,'info')" style="padding:4px 8px;border:1px solid var(--line);border-radius:6px;font-size:12px;background:var(--white)"><option>Gross vs Net</option><option>By Product</option><option>By Category</option></select></div><canvas id="profitBreakdownChart2" height="180"></canvas></div>
    <div class="panel" style="padding:16px"><div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px"><div style="font-weight:800;color:var(--coffee-900)">Profit by Product</div><select onchange="toast('Product view: '+this.value,'info')" style="padding:4px 8px;border:1px solid var(--line);border-radius:6px;font-size:12px;background:var(--white)"><option>Top 10</option><option>By Revenue</option><option>By Profit</option></select></div><canvas id="profitByProductChart" height="180"></canvas></div>
</div>

<div class="table-card" style="margin-top:16px">
    <div class="panel-head"><div class="panel-title">Profit by Product</div></div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Product</th><th class="center">Qty Sold</th><th class="right">Revenue</th><th class="right">COGS</th><th class="right">Profit</th></tr></thead>
            <tbody>
            @forelse($byProduct ?? [] as $row)
                <tr><td><div class="cell-title" style="font-size:13px">{{ $row->product->name ?? '—' }}</div><div class="cell-sub mono">{{ $row->product->sku ?? '' }}</div></td><td class="center">{{ $row->qty }}</td><td class="right">TZS {{ number_format($row->revenue,0) }}</td><td class="right">TZS {{ number_format($row->cogs,0) }}</td><td class="right" style="font-weight:800;color:var(--acacia-600)">TZS {{ number_format($row->profit,0) }}</td></tr>
            @empty
                <tr><td colspan="5"><div class="empty-state" style="padding:16px"><strong>No profit data</strong><p>Sell products to generate profit.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const ctx1=document.getElementById('profitBreakdownChart2');
  if(ctx1){
    new Chart(ctx1,{
      type:'doughnut',
      data:{
        labels:['Gross Profit','Expenses','Net Profit'],
        datasets:[{data:[{{ $totalProfit ?? 0 }},{{ $totalExpenses ?? 0 }},{{ $netProfit ?? 0 }}],backgroundColor:['#2e7d6b','#b33a3a','#0a4260'],borderWidth:0}]
      },
      options:{responsive:true,plugins:{legend:{position:'bottom'}},cutout:'60%'}
    });
  }
  const ctx2=document.getElementById('profitByProductChart');
  if(ctx2){
    const byProduct=@json($byProduct ?? []);
    const labels=byProduct.map(p=>p.product ? p.product.name : '—');
    const profits=byProduct.map(p=>p.profit);
    new Chart(ctx2,{
      type:'bar',
      data:{
        labels,
        datasets:[{label:'Profit TZS',data:profits,backgroundColor:'#0a4260',borderRadius:6}]
      },
      options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true},x:{grid:{display:false}}}}
    });
  }
});
</script>
<style>@media(max-width:900px){ div[style*="grid-template-columns:repeat(2,1fr)"]{grid-template-columns:1fr !important} }</style>
@endsection
