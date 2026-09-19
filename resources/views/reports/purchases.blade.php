@extends('layouts.admin')
@section('title','Purchase Reports')
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">Purchase Reports <span class="info-icon" tabindex="0">i<span class="tooltip">Purchases · Supplier purchases · Purchase returns · Supplier balances — live</span></span></h1><p class="page-sub">Purchases</p></div>
    <span class="tag tag-green">Live</span>
</div>

<div class="stat-grid">
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg></div><span class="tag tag-terracotta">{{ $totalPurchases ?? 0 }} purchases</span></div><div class="stat-label">Total Purchases</div><div class="stat-value">{{ $totalPurchases ?? 0 }}</div><div class="stat-sub">All statuses</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/></svg></div><span class="tag tag-gold">TZS {{ number_format($totalPurchaseAmount ?? 0,0) }}</span></div><div class="stat-label">Purchase Value</div><div class="stat-value">TZS {{ number_format($totalPurchaseAmount ?? 0,0) }}</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><span class="tag tag-green">{{ $supplierCount ?? 0 }} suppliers</span></div><div class="stat-label">Suppliers</div><div class="stat-value">{{ $supplierCount ?? 0 }}</div></div>
    <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/></svg></div><span class="tag tag-blue">Received</span></div><div class="stat-label">Received</div><div class="stat-value">{{ $received ?? 0 }}</div></div>
</div>

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-top:16px">
    <div class="panel" style="padding:16px"><div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px"><div style="font-weight:800;color:var(--coffee-900)">Purchases by Supplier — Count</div><select onchange="toast('Supplier view: '+this.value,'info')" style="padding:4px 8px;border:1px solid var(--line);border-radius:6px;font-size:12px;background:var(--white)"><option>All Suppliers</option><option>Top 5</option><option>By Value</option></select></div><canvas id="purchasesBySupplierChart" height="180"></canvas></div>
    <div class="panel" style="padding:16px"><div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px"><div style="font-weight:800;color:var(--coffee-900)">Purchase Value by Supplier</div><select onchange="toast('Value by: '+this.value,'info')" style="padding:4px 8px;border:1px solid var(--line);border-radius:6px;font-size:12px;background:var(--white)"><option>Value</option><option>Count</option><option>Paid vs Balance</option></select></div><canvas id="purchaseValueChart" height="180"></canvas></div>
</div>

<div class="table-card" style="margin-top:16px">
    <div class="panel-head"><div class="panel-title">Purchases by Supplier</div></div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Supplier</th><th class="center">Purchases</th><th class="right">Total</th><th class="right">Paid</th><th class="right">Balance</th></tr></thead>
            <tbody>
            @forelse($bySupplier ?? [] as $row)
                <tr><td><div class="cell-title" style="font-size:13px">{{ $row->supplier->name ?? '—' }}</div></td><td class="center">{{ $row->cnt }}</td><td class="right">TZS {{ number_format($row->total,0) }}</td><td class="right">TZS {{ number_format($row->paid,0) }}</td><td class="right" style="font-weight:700">TZS {{ number_format($row->total - $row->paid,0) }}</td></tr>
            @empty
                <tr><td colspan="5"><div class="empty-state" style="padding:16px"><strong>No purchases</strong></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const bySupplier=@json($bySupplier ?? []);
  const labels=bySupplier.map(r=>r.supplier ? r.supplier.name : '—');
  const counts=bySupplier.map(r=>r.cnt);
  const totals=bySupplier.map(r=>r.total);
  const ctx1=document.getElementById('purchasesBySupplierChart');
  if(ctx1 && labels.length){
    new Chart(ctx1,{type:'bar',data:{labels, datasets:[{label:'Purchases',data:counts,backgroundColor:'#0a4260',borderRadius:6}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
  }
  const ctx2=document.getElementById('purchaseValueChart');
  if(ctx2 && labels.length){
    new Chart(ctx2,{type:'doughnut',data:{labels, datasets:[{data:totals,backgroundColor:['#0a4260','#e8b82f','#2e7d6b','#0066cc','#b33a3a'],borderWidth:0}]},options:{responsive:true,plugins:{legend:{position:'bottom'}},cutout:'60%'}});
  }
});
</script>
<style>@media(max-width:900px){ div[style*="grid-template-columns:repeat(2,1fr)"]{grid-template-columns:1fr !important} }</style>
@endsection
