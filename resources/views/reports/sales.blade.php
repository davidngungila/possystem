@extends('layouts.admin')
@section('title','Sales Reports')
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">Sales Reports <span class="info-icon" tabindex="0">i<span class="tooltip">Daily / Weekly / Monthly / By product / By category / By cashier / By payment — live</span></span></h1><p class="page-sub">Daily / Weekly / Monthly / By </p></div>
    <span class="tag tag-green">Live</span>
</div>

<div class="stat-grid">
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/></svg></div><span class="tag tag-terracotta">{{ $today ?? 0 }} today</span></div><div class="stat-label">Today Sales</div><div class="stat-value">TZS {{ number_format($todayRevenue ?? 0,0) }}</div><div class="stat-sub">{{ $today ?? 0 }} transactions</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/></svg></div><span class="tag tag-blue">Week</span></div><div class="stat-label">This Week</div><div class="stat-value">TZS {{ number_format($weekRevenue ?? 0,0) }}</div><div class="stat-sub">{{ $week ?? 0 }} transactions</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8"/></svg></div><span class="tag tag-gold">Month</span></div><div class="stat-label">This Month</div><div class="stat-value">TZS {{ number_format($monthRevenue ?? 0,0) }}</div><div class="stat-sub">{{ $month ?? 0 }} transactions</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><span class="tag tag-green">Range</span></div><div class="stat-label">Custom Range</div><div class="stat-value">{{ $range ?? '—' }}</div><div class="stat-sub">Filter by date in sales history</div></div>
</div>

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-top:16px">
    <div class="panel" style="padding:16px"><div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px"><div style="font-weight:800;color:var(--coffee-900)">Today / Week / Month — Sales</div><select onchange="toast('Period: '+this.value,'info')" style="padding:4px 8px;border:1px solid var(--line);border-radius:6px;font-size:12px;background:var(--white)"><option>All</option><option>Today</option><option>This Week</option><option>This Month</option></select></div><canvas id="salesTWMChart" height="180"></canvas></div>
    <div class="panel" style="padding:16px"><div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px"><div style="font-weight:800;color:var(--coffee-900)">Sales by Payment Method</div><select onchange="toast('Payment: '+this.value,'info')" style="padding:4px 8px;border:1px solid var(--line);border-radius:6px;font-size:12px;background:var(--white)"><option>All Methods</option><option>Cash</option><option>M-Pesa</option><option>Airtel</option><option>Mixx</option></select></div><canvas id="salesPaymentChart" height="180"></canvas></div>
</div>

<div class="table-card" style="margin-top:16px">
    <div class="panel-head"><div class="panel-title">Sales by Payment Method</div></div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Method</th><th class="right">Amount</th><th class="right">Count</th></tr></thead>
            <tbody>
            @forelse($byPayment ?? [] as $row)
                <tr><td><span class="tag tag-grey">{{ ucfirst(str_replace('_',' ',$row->payment_method)) }}</span></td><td class="right">TZS {{ number_format($row->total,0) }}</td><td class="right">{{ $row->cnt }}</td></tr>
            @empty
                <tr><td colspan="3"><div class="empty-state" style="padding:16px"><strong>No sales</strong></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const ctx1=document.getElementById('salesTWMChart');
  if(ctx1){
    new Chart(ctx1,{
      type:'bar',
      data:{
        labels:['Today','This Week','This Month'],
        datasets:[{label:'TZS',data:[{{ $todayRevenue ?? 0 }},{{ $weekRevenue ?? 0 }},{{ $monthRevenue ?? 0 }}],backgroundColor:['#0a4260','#e8b82f','#2e7d6b'],borderRadius:6,barThickness:28}]
      },
      options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{callback:v=>'TZS '+v}},x:{grid:{display:false}}}}
    });
  }
  const ctx2=document.getElementById('salesPaymentChart');
  if(ctx2){
    const payData=@json($byPayment ?? []);
    const labels=payData.map(p=>p.payment_method);
    const totals=payData.map(p=>p.total);
    const colors=['#0a4260','#e8b82f','#2e7d6b','#0066cc','#b33a3a','#5e88a3'];
    new Chart(ctx2,{
      type:'doughnut',
      data:{labels, datasets:[{data:totals, backgroundColor:colors.slice(0,labels.length), borderWidth:0}]},
      options:{responsive:true,plugins:{legend:{position:'bottom',labels:{boxWidth:12,padding:12}}},cutout:'60%'}
    });
  }
});
</script>
<style>@media(max-width:900px){ div[style*="grid-template-columns:repeat(2,1fr)"]{grid-template-columns:1fr !important} }</style>
@endsection
