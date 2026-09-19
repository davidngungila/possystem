@extends('layouts.admin')
@section('title','Financial Reports')
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">Financial Reports <span class="info-icon" tabindex="0">i<span class="tooltip">Revenue · COGS · Gross profit · Expenses · Net profit · Cash reconciliation — live</span></span></h1><p class="page-sub">Revenue</p></div>
    <span class="tag tag-green">Live</span>
</div>

<div class="stat-grid">
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/></svg></div><span class="tag tag-terracotta">Revenue</span></div><div class="stat-label">Revenue</div><div class="stat-value">TZS {{ number_format($revenue ?? 0,0) }}</div><div class="stat-sub">Net sales</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg></div><span class="tag tag-gold">COGS</span></div><div class="stat-label">COGS</div><div class="stat-value">TZS {{ number_format($cogs ?? 0,0) }}</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/></svg></div><span class="tag tag-green">Gross</span></div><div class="stat-label">Gross Profit</div><div class="stat-value" style="color:var(--acacia-600)">TZS {{ number_format($totalProfit ?? 0,0) }}</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg></div><span class="tag tag-blue">Net</span></div><div class="stat-label">Net Profit</div><div class="stat-value">TZS {{ number_format($netProfit ?? 0,0) }}</div><div class="stat-sub">After expenses TZS {{ number_format($totalExpenses ?? 0,0) }}</div></div>
</div>

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-top:16px">
    <div class="panel" style="padding:16px"><div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px"><div style="font-weight:800;color:var(--coffee-900)">Financial Overview</div><select onchange="toast('View: '+this.value,'info')" style="padding:4px 8px;border:1px solid var(--line);border-radius:6px;font-size:12px;background:var(--white)"><option>All</option><option>Revenue</option><option>COGS</option><option>Net Profit</option></select></div><canvas id="financialChart" height="180"></canvas></div>
    <div class="panel" style="padding:16px"><div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px"><div style="font-weight:800;color:var(--coffee-900)">Profit Breakdown</div><select onchange="toast('Breakdown: '+this.value,'info')" style="padding:4px 8px;border:1px solid var(--line);border-radius:6px;font-size:12px;background:var(--white)"><option>Gross vs Net</option><option>By Payment</option><option>By Category</option></select></div><canvas id="profitBreakdownChart" height="180"></canvas></div>
</div>

<div class="table-card" style="margin-top:16px">
    <div class="panel-head"><div class="panel-title">Payment Reconciliation</div></div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Method</th><th class="right">Amount</th><th class="right">Count</th></tr></thead>
            <tbody>
            @forelse($byPayment ?? [] as $row)
                <tr><td><span class="tag tag-grey">{{ ucfirst(str_replace('_',' ',$row->payment_method)) }}</span></td><td class="right">TZS {{ number_format($row->total,0) }}</td><td class="right">{{ $row->cnt }}</td></tr>
            @empty
                <tr><td colspan="3"><div class="empty-state" style="padding:16px"><strong>No payments</strong></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const ctx1=document.getElementById('financialChart');
  if(ctx1){
    new Chart(ctx1,{
      type:'bar',
      data:{
        labels:['Revenue','COGS','Gross Profit','Expenses','Net Profit'],
        datasets:[{label:'TZS',data:[{{ $revenue ?? 0 }},{{ $cogs ?? 0 }},{{ $totalProfit ?? 0 }},{{ $totalExpenses ?? 0 }},{{ $netProfit ?? 0 }}],backgroundColor:['#0a4260','#d4a24c','#2e7d6b','#b33a3a','#5e88a3'],borderRadius:6,barThickness:18}]
      },
      options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{callback:v=>'TZS '+v}},x:{grid:{display:false}}}}
    });
  }
  const ctx2=document.getElementById('profitBreakdownChart');
  if(ctx2){
    new Chart(ctx2,{
      type:'doughnut',
      data:{
        labels:['Gross Profit','Expenses','Net Profit'],
        datasets:[{data:[{{ $totalProfit ?? 0 }},{{ $totalExpenses ?? 0 }},{{ $netProfit ?? 0 }}],backgroundColor:['#2e7d6b','#b33a3a','#0a4260'],borderWidth:0}]
      },
      options:{responsive:true,plugins:{legend:{position:'bottom'}},cutout:'60%'}
    });
  }
});
</script>
<style>@media(max-width:900px){ div[style*="grid-template-columns:repeat(2,1fr)"]{grid-template-columns:1fr !important} }</style>
@endsection
