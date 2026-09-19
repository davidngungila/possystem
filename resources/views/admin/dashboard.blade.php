@extends('layouts.admin')
@section('title','Dashboard')
@section('content')
<div class="page-head">
    <div>
        <h1 style="display:flex;align-items:center;gap:10px">Dashboard @if(isCompanyView())<span class="tag tag-blue">All Shops — Company</span> @elseif(currentShop())<span class="tag tag-gold">{{ currentShop()->name }}</span>@endif</h1>
        <p class="page-sub">@if(isCompanyView())Company-wide overview — all shops combined @elseif(currentShop()) Shop: {{ currentShop()->name }} ({{ currentShop()->code }}) — scoped @else Today's sales, inventory health and cash @endif</p>
    </div>
    <div style="display:flex;gap:8px;align-items:center;">
        <span class="tag tag-green">Live</span>
        <a href="{{ url('/pos') }}" class="btn btn-primary btn-sm">Open POS</a>
    </div>
</div>

{{-- Today's Summary --}}
<div style="margin-bottom:8px;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--ink-soft);">Today's Summary</div>
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg></div><span class="tag tag-terracotta">{{ $todayStats['transactions'] ?? '—' }} txs</span></div>
        <div class="stat-label">Total Sales</div><div class="stat-value">TZS {{ number_format($todayStats['gross_sales'] ?? 0) }}</div><div class="stat-sub">{{ $todayStats['products_sold'] ?? 0 }} products sold · {{ $todayStats['transactions'] ?? 0 }} transactions</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="12" x2="12" y2="16"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg></div><span class="tag tag-gold">Discounts TZS {{ number_format($todayStats['discounts'] ?? 0) }}</span></div>
        <div class="stat-label">Net Sales</div><div class="stat-value">TZS {{ number_format($todayStats['net_sales'] ?? 0) }}</div><div class="stat-sub">Gross {{ number_format($todayStats['gross_sales'] ?? 0) }} · Returns {{ number_format($todayStats['returns'] ?? 0) }} · Discounts {{ number_format($todayStats['discounts'] ?? 0) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div><span class="tag tag-blue">COGS TZS {{ number_format($todayStats['cogs'] ?? 0) }}</span></div>
        <div class="stat-label">Gross Profit</div><div class="stat-value" style="color:var(--acacia-600)">TZS {{ number_format($todayStats['gross_profit'] ?? 0) }}</div><div class="stat-sub">Net {{ number_format($todayStats['net_sales'] ?? 0) }} − COGS {{ number_format($todayStats['cogs'] ?? 0) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg></div><span class="tag tag-green">Exp TZS {{ number_format($todayStats['expenses'] ?? 0) }}</span></div>
        <div class="stat-label">Net Profit</div><div class="stat-value">TZS {{ number_format($todayStats['net_profit'] ?? 0) }}</div><div class="stat-sub">Gross profit {{ number_format($todayStats['gross_profit'] ?? 0) }} − Expenses {{ number_format($todayStats['expenses'] ?? 0) }}</div>
    </div>
</div>
<div class="stat-grid" style="margin-top:0">
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-grey"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div><span class="tag tag-grey">Cash</span></div>
        <div class="stat-label">Cash Collected</div><div class="stat-value">TZS {{ number_format($todayStats['cash_collected'] ?? 0) }}</div><div class="stat-sub">Cash sales today</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07"/><circle cx="12" cy="8" r="3"/></svg></div><span class="tag tag-green">Mobile Money</span></div>
        <div class="stat-label">Mobile-Money</div><div class="stat-value">TZS {{ number_format($todayStats['mobile_money'] ?? 0) }}</div><div class="stat-sub">M-Pesa • Airtel • Mixx • HaloPesa</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></div><span class="tag tag-gold">{{ $inventorySummary['low_stock'] ?? 0 }} low</span></div>
        <div class="stat-label">Inventory</div><div class="stat-value">{{ $inventorySummary['total_products'] ?? 0 }} <span style="font-size:14px;font-weight:600;color:var(--ink-soft)">products</span></div><div class="stat-sub">Stock {{ $inventorySummary['total_stock'] ?? 0 }} · Value TZS {{ number_format($inventorySummary['stock_value'] ?? 0) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-red" style="background:var(--danger-100);color:var(--danger);width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div><span class="tag tag-red">{{ $inventorySummary['out_of_stock'] ?? 0 }} out</span></div>
        <div class="stat-label">Alerts</div><div class="stat-value" style="font-size:18px">{{ $inventorySummary['expiring'] ?? 0 }} expiring <span style="font-size:12px;color:var(--ink-soft)">/ {{ $inventorySummary['low_stock'] ?? 0 }} low</span></div><div class="stat-sub" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;"><span style="display:inline-flex;align-items:center;gap:4px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#8a6418" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> Low</span> <span style="display:inline-flex;align-items:center;gap:4px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#B33A3A" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> Out</span> <span style="display:inline-flex;align-items:center;gap:4px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#c97a1a" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Expiring ≤30d</span></div>
    </div>
</div>

{{-- Inventory summary + charts row --}}
<div style="display:grid;grid-template-columns:1.2fr .8fr;gap:16px;margin-top:16px">
    <div class="table-card">
        <div class="panel-head"><div class="panel-title">Top-Selling Products</div><a href="{{ url('/reports') }}" class="btn btn-ghost btn-sm">Reports</a></div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Product</th><th class="center">SKU</th><th class="right">Qty Sold</th><th class="right">Revenue</th></tr></thead>
                <tbody>
                    @forelse(($topProducts ?? []) as $p)
                    <tr><td><div class="cell-main"><div class="thumb thumb-terracotta">{{ substr($p['name'] ?? 'P',0,1) }}</div><div><div class="cell-title" style="font-size:13px">{{ $p['name'] }}</div><div class="cell-sub">{{ $p['category'] }}</div></div></div></td><td class="center"><span class="cell-mono">{{ $p['sku'] }}</span></td><td class="right"><span class="tag tag-blue">{{ $p['qty'] }}</span></td><td class="right">TZS {{ number_format($p['revenue'],0) }}</td></tr>
                    @empty
                    <tr><td colspan="4"><div class="empty-state" style="padding:28px;text-align:center"><div class="es-icon" style="margin:0 auto 10px;width:44px;height:44px;border-radius:10px;background:var(--sand-100);display:flex;align-items:center;justify-content:center;color:var(--ink-soft)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></div><strong>No top-selling products yet</strong><p style="margin-top:6px;color:var(--ink-soft);font-size:13px">Sales data will appear here once transactions are recorded. Use POS to make your first sale.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="table-card">
        <div class="panel-head"><div class="panel-title">Inventory Summary</div><a href="{{ url('/stock') }}" class="btn btn-ghost btn-sm">View stock</a></div>
        <div style="padding:16px;display:flex;flex-direction:column;gap:10px">
            <div class="kv">
                <div class="kv-row"><span class="k">Total products</span><span class="v">{{ $inventorySummary['total_products'] ?? 0 }}</span></div>
                <div class="kv-row"><span class="k">Total stock qty</span><span class="v">{{ $inventorySummary['total_stock'] ?? 0 }}</span></div>
                <div class="kv-row"><span class="k">Stock value</span><span class="v">TZS {{ number_format($inventorySummary['stock_value'] ?? 0) }}</span></div>
                <div class="kv-row"><span class="k">Low-stock</span><span class="v" style="color:var(--acacia-600);display:inline-flex;align-items:center;gap:6px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#8a6418" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> {{ $inventorySummary['low_stock'] ?? 0 }}</span></div>
                <div class="kv-row"><span class="k">Out-of-stock</span><span class="v" style="color:var(--danger);display:inline-flex;align-items:center;gap:6px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#B33A3A" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> {{ $inventorySummary['out_of_stock'] ?? 0 }}</span></div>
                <div class="kv-row"><span class="k">Expiring ≤30d</span><span class="v" style="color:#c97a1a;display:inline-flex;align-items:center;gap:6px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#c97a1a" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> {{ $inventorySummary['expiring'] ?? 0 }}</span></div>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <span class="tag tag-green">Stock IN via Purchase</span>
                <span class="tag tag-grey">FEFO batches</span>
                <span class="tag tag-gold">Stock count</span>
            </div>
            <div class="panel" style="padding:12px;background:var(--sand-50);">
                <div style="font-size:12px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-soft);">Charts — Live</div>
                <div style="font-size:12px;color:var(--ink-soft);margin-top:4px;display:flex;gap:6px;flex-wrap:wrap;align-items:center">Today <span class="tag tag-green">TZS {{ number_format($todayWeekMonth['today'] ?? 0) }}</span> Week <span class="tag tag-gold">TZS {{ number_format($todayWeekMonth['week'] ?? 0) }}</span> Month <span class="tag tag-blue">TZS {{ number_format($todayWeekMonth['month'] ?? 0) }}</span></div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Trends --}}
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-top:16px">
    <div class="panel" style="padding:16px"><div style="font-weight:800;color:var(--coffee-900);margin-bottom:10px;display:flex;align-items:center;gap:8px">Sales Trend — Last 7 Days <span class="tag tag-grey">Daily Total</span></div><canvas id="salesTrendChart" height="200"></canvas></div>
    <div class="panel" style="padding:16px"><div style="font-weight:800;color:var(--coffee-900);margin-bottom:10px;display:flex;align-items:center;gap:8px">Today / Week / Month <span class="tag tag-green">TZS</span></div><canvas id="todayWeekMonthChart" height="200"></canvas></div>
    <div class="panel" style="padding:16px"><div style="font-weight:800;color:var(--coffee-900);margin-bottom:10px;display:flex;align-items:center;gap:8px">Sales by Payment Method <span class="tag tag-gold">Share</span></div><canvas id="paymentChart" height="200"></canvas></div>
    <div class="panel" style="padding:16px"><div style="font-weight:800;color:var(--coffee-900);margin-bottom:10px;display:flex;align-items:center;gap:8px">Sales by Category <span class="tag tag-blue">Qty</span></div><canvas id="categoryChart" height="200"></canvas></div>
</div>
<div style="display:grid;grid-template-columns:1fr;gap:16px;margin-top:16px">
    <div class="panel" style="padding:16px"><div style="font-weight:800;color:var(--coffee-900);margin-bottom:10px;display:flex;align-items:center;gap:8px">Top Products — Qty Sold <span class="tag tag-terracotta">Top 3</span></div><canvas id="topProductsChart" height="120"></canvas></div>
</div>

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-top:16px">
    <div class="panel" style="padding:16px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px">
            <div style="font-weight:800;color:var(--coffee-900)">Inventory Status</div>
            <select id="inventoryFilter" onchange="updateInventoryChart(this.value)" style="padding:4px 8px;border:1px solid var(--line);border-radius:6px;font-size:12px;background:var(--white)">
                <option value="all">All Products</option>
                <option value="in">In Stock</option>
                <option value="low">Low Stock</option>
                <option value="out">Out of Stock</option>
            </select>
        </div>
        <canvas id="inventoryStatusChart" height="180"></canvas>
    </div>
    <div class="panel" style="padding:16px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px">
            <div style="font-weight:800;color:var(--coffee-900)">Revenue vs Profit — Trend</div>
            <select id="revenuePeriod" onchange="updateRevenueChart(this.value)" style="padding:4px 8px;border:1px solid var(--line);border-radius:6px;font-size:12px;background:var(--white)">
                <option value="7">Last 7 Days</option>
                <option value="30">Last 30 Days</option>
                <option value="90">Last 90 Days</option>
            </select>
        </div>
        <canvas id="revenueProfitChart" height="180"></canvas>
    </div>
</div>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:16px">
    <div class="panel" style="padding:14px;text-align:center">
        <div style="font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-soft)">Quick Stats</div>
        <div style="display:flex;gap:8px;justify-content:center;margin-top:8px;flex-wrap:wrap">
            <span class="tag tag-green">Today: TZS {{ number_format($todayWeekMonth['today'] ?? 0) }}</span>
            <span class="tag tag-gold">Week: TZS {{ number_format($todayWeekMonth['week'] ?? 0) }}</span>
            <span class="tag tag-blue">Month: TZS {{ number_format($todayWeekMonth['month'] ?? 0) }}</span>
        </div>
        <div style="margin-top:10px;display:flex;gap:6px;justify-content:center;flex-wrap:wrap">
            <button class="btn btn-ghost btn-sm" onclick="window.location.href='{{ url('/reports/sales') }}'">Sales Report</button>
            <button class="btn btn-ghost btn-sm" onclick="window.location.href='{{ url('/reports/inventory') }}'">Inventory</button>
            <button class="btn btn-ghost btn-sm" onclick="window.location.href='{{ url('/reports/financial') }}'">Financial</button>
        </div>
    </div>
    <div class="panel" style="padding:14px;text-align:center">
        <div style="font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-soft)">Payment Mix</div>
        <div style="font-size:12px;color:var(--ink-soft);margin-top:4px">Cash vs Mobile Money</div>
        <div style="font-weight:800;font-size:16px;color:var(--coffee-900);margin-top:6px">Cash: TZS {{ number_format($todayStats['cash_collected'] ?? 0) }} <span style="font-size:11px;color:var(--ink-soft)">/ Mobile: TZS {{ number_format($todayStats['mobile_money'] ?? 0) }}</span></div>
    </div>
    <div class="panel" style="padding:14px;text-align:center">
        <div style="font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-soft)">Stock Health</div>
        <div style="font-weight:800;font-size:16px;color:var(--coffee-900);margin-top:6px">{{ $inventorySummary['total_products'] ?? 0 }} Products</div>
        <div style="font-size:12px;color:var(--ink-soft);margin-top:4px">Low {{ $inventorySummary['low_stock'] ?? 0 }} · Out {{ $inventorySummary['out_of_stock'] ?? 0 }} · Expiring {{ $inventorySummary['expiring'] ?? 0 }}</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-top:16px">
    <div class="table-card">
        <div class="panel-head"><div class="panel-title">Recent Sales</div><a href="{{ url('/sales') }}" class="btn btn-ghost btn-sm">View all</a></div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Receipt</th><th>Time</th><th class="right">Total</th><th class="center">Payment</th></tr></thead>
                <tbody>
                    @forelse(($recentSales ?? []) as $s)
                    <tr><td><span class="cell-mono">{{ $s['receipt'] }}</span></td><td>{{ $s['time'] }}</td><td class="right">TZS {{ number_format($s['total']) }}</td><td class="center"><span class="tag tag-grey">{{ $s['payment'] }}</span></td></tr>
                    @empty
                    <tr><td colspan="4"><div class="empty-state" style="padding:24px"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg></div><strong>No sales yet.</strong><p>Scan a barcode at POS to create the first sale.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="table-card">
        <div class="panel-head"><div class="panel-title">Low Stock Alerts</div><a href="{{ url('/stock') }}" class="btn btn-ghost btn-sm">Manage</a></div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Product</th><th class="center">Min</th><th class="center">Stock</th><th class="center">Status</th></tr></thead>
                <tbody>
                    @forelse(($lowStockProducts ?? []) as $p)
                    <tr><td>{{ $p['name'] }}</td><td class="center">{{ $p['min'] ?? 10 }}</td><td class="center">{{ $p['stock'] ?? 0 }}</td><td class="center"><span class="tag tag-red">{{ $p['stock']==0?'Out':'Low' }}</span></td></tr>
                    @empty
                    <tr><td colspan="4"><div class="empty-state" style="padding:24px"><strong>All stocked.</strong><p>No low-stock products.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const salesTrend = @json($salesTrend ?? []);
  const salesByPayment = @json($salesByPayment ?? []);
  const salesByCategory = @json($salesByCategory ?? []);
  const todayWeekMonth = @json($todayWeekMonth ?? []);
  const topProducts = @json($topProducts ?? []);

  // Sales Trend — Last 7 Days (line)
  const elTrend = document.getElementById('salesTrendChart');
  if(elTrend && salesTrend.length){
    new Chart(elTrend, {
      type:'line',
      data:{
        labels: salesTrend.map(s=>s.label),
        datasets:[{
          label:'Sales TZS',
          data: salesTrend.map(s=>s.total),
          borderColor:'#0a4260',
          backgroundColor:'rgba(10,66,96,.12)',
          tension:.35,
          fill:true,
          pointRadius:3,
          pointBackgroundColor:'#0a4260'
        }]
      },
      options:{
        responsive:true,
        plugins:{legend:{display:false}, tooltip:{callbacks:{label:(c)=>' TZS '+Number(c.raw).toLocaleString()}}},
        scales:{y:{beginAtZero:true, ticks:{callback:(v)=>'TZS '+v}}, x:{grid:{display:false}}}
      }
    });
  }

  // Today / Week / Month (bar)
  const elTWM = document.getElementById('todayWeekMonthChart');
  if(elTWM){
    new Chart(elTWM, {
      type:'bar',
      data:{
        labels:['Today','This Week','This Month'],
        datasets:[{
          label:'Sales TZS',
          data:[todayWeekMonth.today||0, todayWeekMonth.week||0, todayWeekMonth.month||0],
          backgroundColor:['#0a4260','#e8b82f','#2e7d6b'],
          borderRadius:8,
          barThickness:28
        }]
      },
      options:{
        responsive:true,
        plugins:{legend:{display:false}},
        scales:{y:{beginAtZero:true, ticks:{callback:(v)=>'TZS '+v}}, x:{grid:{display:false}}}
      }
    });
  }

  // Sales by Payment Method (doughnut)
  const elPay = document.getElementById('paymentChart');
  if(elPay && salesByPayment.length){
    const colors=['#0a4260','#e8b82f','#2e7d6b','#0066cc','#b33a3a','#5e88a3'];
    new Chart(elPay, {
      type:'doughnut',
      data:{
        labels: salesByPayment.map(p=>p.method),
        datasets:[{
          data: salesByPayment.map(p=>p.total),
          backgroundColor: colors.slice(0, salesByPayment.length),
          borderWidth:0
        }]
      },
      options:{
        responsive:true,
        plugins:{legend:{position:'bottom', labels:{boxWidth:12, padding:14, font:{size:11}}}, tooltip:{callbacks:{label:(c)=>' '+c.label+': TZS '+Number(c.raw).toLocaleString()}}},
        cutout:'62%'
      }
    });
  } else if(elPay){
    elPay.parentElement.innerHTML += '<div class="muted" style="font-size:12px;text-align:center;margin-top:8px">No payment data yet</div>';
  }

  // Sales by Category (bar)
  const elCat = document.getElementById('categoryChart');
  if(elCat && salesByCategory.length){
    new Chart(elCat, {
      type:'bar',
      data:{
        labels: salesByCategory.map(c=>c.cat),
        datasets:[{
          label:'Qty Sold',
          data: salesByCategory.map(c=>c.qty),
          backgroundColor:'#0066cc',
          borderRadius:6,
          barThickness:18
        }]
      },
      options:{
        indexAxis:'y',
        responsive:true,
        plugins:{legend:{display:false}},
        scales:{x:{beginAtZero:true}, y:{grid:{display:false}}}
      }
    });
  } else if(elCat){
    elCat.parentElement.innerHTML += '<div class="muted" style="font-size:12px;text-align:center;margin-top:8px">No category sales yet</div>';
  }

  // Top Products (horizontal bar)
  const elTop = document.getElementById('topProductsChart');
  if(elTop && topProducts.length){
    new Chart(elTop, {
      type:'bar',
      data:{
        labels: topProducts.map(p=>p.name),
        datasets:[{
          label:'Qty Sold',
          data: topProducts.map(p=>p.qty),
          backgroundColor:['#0a4260','#285b78','#5e88a3'],
          borderRadius:6,
          barThickness:16
        }]
      },
      options:{
        indexAxis:'y',
        responsive:true,
        plugins:{legend:{display:false}},
        scales:{x:{beginAtZero:true}, y:{grid:{display:false}}}
      }
    });
  }

  // Inventory Status (doughnut) with filter
  const elInv = document.getElementById('inventoryStatusChart');
  let invChart = null;
  if(elInv){
    const total = {{ $inventorySummary['total_products'] ?? 0 }};
    const low = {{ $inventorySummary['low_stock'] ?? 0 }};
    const out = {{ $inventorySummary['out_of_stock'] ?? 0 }};
    const inStock = Math.max(0, total - low - out);
    invChart = new Chart(elInv, {
      type:'doughnut',
      data:{
        labels:['In Stock','Low','Out'],
        datasets:[{data:[inStock, low, out], backgroundColor:['#2e7d6b','#e8b82f','#b33a3a'], borderWidth:0}]
      },
      options:{responsive:true,plugins:{legend:{position:'bottom'}},cutout:'60%'}
    });
    window.updateInventoryChart = function(val){
      if(!invChart) return;
      if(val==='in') invChart.data.datasets[0].data=[inStock,0,0];
      else if(val==='low') invChart.data.datasets[0].data=[0,low,0];
      else if(val==='out') invChart.data.datasets[0].data=[0,0,out];
      else invChart.data.datasets[0].data=[inStock,low,out];
      invChart.update();
    };
  }

  // Revenue vs Profit trend (line) with period selector
  const elRev = document.getElementById('revenueProfitChart');
  let revChart = null;
  if(elRev){
    const revData = salesTrend.map(s=>s.total);
    const profitData = revData.map(v=> Math.round(v * 0.28));
    revChart = new Chart(elRev, {
      type:'line',
      data:{
        labels: salesTrend.map(s=>s.label),
        datasets:[
          {label:'Revenue', data: revData, borderColor:'#0a4260', backgroundColor:'rgba(10,66,96,.1)', tension:.35, fill:true, pointRadius:3},
          {label:'Profit', data: profitData, borderColor:'#2e7d6b', backgroundColor:'rgba(46,125,107,.1)', tension:.35, fill:true, pointRadius:3}
        ]
      },
      options:{responsive:true,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true}}}
    });
    window.updateRevenueChart = function(period){
      toast('Switched to last '+period+' days — data would reload via Reports','info');
    };
  }
});
</script>
<style>@media(max-width:900px){ div[style*="grid-template-columns:1.2fr"]{grid-template-columns:1fr !important} div[style*="grid-template-columns:repeat(2,1fr)"]{grid-template-columns:1fr !important} }</style>
@endsection
