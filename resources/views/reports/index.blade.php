@extends('layouts.admin')
@section('title','Reports')
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">Reports <span class="info-icon" tabindex="0">i<span class="tooltip">Sales · Inventory · Purchases · Financial — COGS, Gross/Net profit, Cash reconciliation.</span></span></h1><p class="page-sub">Sales</p></div>
    <span class="tag tag-green">Live Reports</span>
</div>

<div class="stat-grid">
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/></svg></div><span class="tag tag-terracotta">{{ $totalSales ?? 0 }} sales</span></div><div class="stat-label">Total Revenue</div><div class="stat-value">TZS {{ number_format($totalRevenue ?? 0,0) }}</div><div class="stat-sub">{{ $totalSales ?? 0 }} transactions</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8"/></svg></div><span class="tag tag-gold">{{ $lowStock ?? 0 }} low · {{ $outStock ?? 0 }} out</span></div><div class="stat-label">Stock Value</div><div class="stat-value">TZS {{ number_format($stockValue ?? 0,0) }}</div><div class="stat-sub">Low {{ $lowStock ?? 0 }} · Out {{ $outStock ?? 0 }}</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg></div><span class="tag tag-green">Purchases</span></div><div class="stat-label">COGS</div><div class="stat-value">TZS {{ number_format($totalCogs ?? 0,0) }}</div><div class="stat-sub">Cost of goods sold</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/></svg></div><span class="tag tag-blue">Profit</span></div><div class="stat-label" style="display:flex;align-items:center;gap:6px;">Gross <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> Net</div><div class="stat-value" style="color:var(--acacia-600)">TZS {{ number_format($totalProfit ?? 0,0) }}</div><div class="stat-sub">Net TZS {{ number_format($netProfit ?? 0,0) }} after expenses TZS {{ number_format($totalExpenses ?? 0,0) }}</div></div>
</div>

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-top:16px">
    <div class="panel" style="padding:16px"><div style="font-weight:800;color:var(--coffee-900);margin-bottom:10px;display:flex;align-items:center;gap:8px">Financial Overview <span class="tag tag-blue">Live</span></div><canvas id="financialOverviewChart" height="200"></canvas></div>
    <div class="panel" style="padding:16px"><div style="font-weight:800;color:var(--coffee-900);margin-bottom:10px;display:flex;align-items:center;gap:8px">Sales vs Profit <span class="tag tag-green">Trend</span></div><canvas id="salesProfitChart" height="200"></canvas></div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <div class="table-card">
        <div class="panel-head"><div class="panel-title">Financial</div><span class="tag tag-grey">Real</span></div>
        <div style="padding:16px">
            <div class="kv">
                <div class="kv-row"><span class="k">Revenue (Net sales)</span><span class="v">TZS {{ number_format($totalRevenue ?? 0,0) }}</span></div>
                <div class="kv-row"><span class="k">COGS</span><span class="v">TZS {{ number_format($totalCogs ?? 0,0) }}</span></div>
                <div class="kv-row"><span class="k">Gross profit</span><span class="v" style="color:var(--acacia-600)">TZS {{ number_format($totalProfit ?? 0,0) }}</span></div>
                <div class="kv-row"><span class="k">Expenses</span><span class="v">TZS {{ number_format($totalExpenses ?? 0,0) }}</span></div>
                <div class="kv-row" style="background:var(--sand-50)"><span class="k" style="font-weight:800">Net profit</span><span class="v" style="font-weight:800">TZS {{ number_format($netProfit ?? 0,0) }}</span></div>
            </div>
            <div style="font-size:12px;color:var(--ink-soft);margin-top:10px;">Per-sale: Selling − Cost = Gross profit. Rest wired to purchases/expenses. Stock movements logged.</div>
        </div>
    </div>
    <div class="table-card">
        <div class="panel-head"><div class="panel-title">Top Selling Products</div></div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Product</th><th class="center">Qty</th><th class="right">Revenue</th></tr></thead>
                <tbody>
                @forelse($topProducts ?? [] as $tp)
                    <tr><td><div class="cell-title">{{ $tp->product->name ?? '—' }}</div><div class="cell-sub">{{ $tp->product->sku ?? '' }}</div></td><td class="center"><span class="tag tag-blue">{{ $tp->qty }}</span></td><td class="right">TZS {{ number_format($tp->revenue,0) }}</td></tr>
                @empty
                    <tr><td colspan="3"><div class="empty-state" style="padding:16px"><strong>No sales yet</strong><p>Sell a product via POS to see top products.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="table-card" style="margin-top:16px">
    <div class="panel-head"><div class="panel-title">Recent Sales</div><a href="{{ route('sales.index') }}" class="btn btn-ghost btn-sm">View all</a></div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Receipt</th><th>Date</th><th>Cashier</th><th class="right">Total</th><th>Payment</th></tr></thead>
            <tbody>
            @forelse($recentSales ?? [] as $s)
                <tr><td><span class="cell-mono">{{ $s->receipt_number }}</span></td><td>{{ $s->created_at->format('d/m/Y H:i') }}</td><td>{{ $s->cashier->name ?? '—' }}</td><td class="right">TZS {{ number_format($s->total_amount,0) }}</td><td>@foreach($s->payments as $pay)<span class="tag tag-grey">{{ $pay->payment_method }}</span> @endforeach</td></tr>
            @empty
                <tr><td colspan="5"><div class="empty-state" style="padding:16px"><strong>No sales</strong></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const ctx1=document.getElementById('financialOverviewChart');
  if(ctx1){
    new Chart(ctx1,{
      type:'bar',
      data:{
        labels:['Revenue','COGS','Gross Profit','Expenses','Net Profit'],
        datasets:[{label:'TZS',data:[{{ $totalRevenue ?? 0 }},{{ $totalCogs ?? 0 }},{{ $totalProfit ?? 0 }},{{ $totalExpenses ?? 0 }},{{ $netProfit ?? 0 }}],backgroundColor:['#0a4260','#d4a24c','#2e7d6b','#b33a3a','#5e88a3'],borderRadius:6,barThickness:18}]
      },
      options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{callback:v=>'TZS '+v}},x:{grid:{display:false}}}}
    });
  }
  const ctx2=document.getElementById('salesProfitChart');
  if(ctx2){
    new Chart(ctx2,{
      type:'line',
      data:{
        labels:['Revenue','Gross Profit','Net Profit'],
        datasets:[{label:'TZS',data:[{{ $totalRevenue ?? 0 }},{{ $totalProfit ?? 0 }},{{ $netProfit ?? 0 }}],borderColor:'#0a4260',backgroundColor:'rgba(10,66,96,.12)',tension:.35,fill:true,pointRadius:3}]
      },
      options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
    });
  }
});
</script>
<style>@media(max-width:900px){ div[style*="grid-template-columns:1fr 1fr"]{grid-template-columns:1fr !important} div[style*="grid-template-columns:repeat(2,1fr)"]{grid-template-columns:1fr !important} }</style>
@endsection
