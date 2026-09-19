@extends('layouts.admin')
@section('title','Return '.$sale->receipt_number)
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">Return {{ $sale->receipt_number }} <span class="info-icon" tabindex="0">i<span class="tooltip">Return details — stock restored</span></span></h1><p class="page-sub">Return details</p></div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('returns.index') }}" class="btn btn-ghost btn-sm">← Back to Returns</a>
        <a href="{{ route('sales.show', encId($sale->id)) }}" class="btn btn-ghost btn-sm">Original Sale</a>
        <a href="{{ route('sales.receipt', encId($sale->id)) }}" target="_blank" class="btn btn-ghost btn-sm">Receipt</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1.2fr .8fr;gap:16px">
    <div style="display:flex;flex-direction:column;gap:16px">
        <div class="table-card">
            <div class="panel-head"><div class="panel-title">Returned Items — stock restored</div><span class="tag tag-red">{{ $sale->items->count() }} items</span></div>
            <div class="table-scroll">
                <table>
                    <thead><tr><th>Product</th><th class="center">Qty</th><th class="right">Price</th><th class="right">Total</th><th class="center">Stock After</th></tr></thead>
                    <tbody>
                    @foreach($sale->items as $it)
                        @php
                            $mov = $movements->firstWhere('product_id', $it->product_id);
                        @endphp
                        <tr>
                            <td><div class="cell-title">{{ $it->product->name ?? '—' }}</div><div class="cell-sub">{{ $it->product->sku ?? '' }} @if($it->product->barcode) · {{ $it->product->barcode }} @endif</div></td>
                            <td class="center"><span class="tag tag-grey">{{ $it->quantity }}</span></td>
                            <td class="right">TZS {{ number_format($it->selling_price,0) }}</td>
                            <td class="right" style="font-weight:700">TZS {{ number_format($it->total,0) }}</td>
                            <td class="center">
                                @if($mov)
                                    <span class="cell-mono">{{ $mov->previous_stock }} → {{ $mov->new_stock }}</span> <span class="tag tag-green" style="margin-left:4px">+{{ $mov->quantity }}</span>
                                @else
                                    <span class="tag tag-grey">Restored</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="table-pagination"><div class="pager-info">{{ $sale->items->sum('quantity') }} units returned — stock movements type <code>return</code></div><strong>Refund total: TZS {{ number_format($sale->total_amount,0) }}</strong></div>
        </div>

        @if($movements->isNotEmpty())
        <div class="table-card">
            <div class="panel-head"><div class="panel-title">Stock Movements — return</div><span class="tag tag-grey">{{ $movements->count() }} movements</span></div>
            <div class="table-scroll">
                <table>
                    <thead><tr><th>Product</th><th class="center">Prev → New</th><th class="center">Qty</th><th>Reason</th><th>At</th></tr></thead>
                    <tbody>
                    @foreach($movements as $m)
                        <tr>
                            <td><div class="cell-title" style="font-size:13px">{{ $m->product->name ?? '—' }}</div><div class="cell-sub">{{ $m->product->sku ?? '' }}</div></td>
                            <td class="center"><span class="cell-mono">{{ $m->previous_stock }} → {{ $m->new_stock }}</span></td>
                            <td class="center"><span class="tag tag-green">+{{ $m->quantity }}</span></td>
                            <td style="font-size:12px">{{ $m->reason }}</td>
                            <td class="muted" style="font-size:12px">{{ $m->created_at->format('d/m H:i') }}<div class="cell-sub">{{ $m->user->name ?? '' }}</div></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <div style="display:flex;flex-direction:column;gap:16px">
        <div class="panel">
            <div class="panel-head"><div class="panel-title">Return Summary</div><span class="tag tag-red">Returned</span></div>
            <div style="padding:16px">
                <div class="kv">
                    <div class="kv-row"><span class="k">Receipt</span><span class="v cell-mono">{{ $sale->receipt_number }}</span></div>
                    <div class="kv-row"><span class="k">Sale Date</span><span class="v">{{ $sale->created_at->format('d/m/Y H:i') }}</span></div>
                    <div class="kv-row"><span class="k">Returned At</span><span class="v">{{ $sale->returned_at ? \Carbon\Carbon::parse($sale->returned_at)->format('d/m/Y H:i') : $sale->updated_at->format('d/m/Y H:i') }}</span></div>
                    <div class="kv-row"><span class="k">Returned By</span><span class="v">{{ $sale->returnedBy->name ?? $sale->cashier->name ?? '—' }}</span></div>
                    <div class="kv-row"><span class="k">Cashier</span><span class="v">{{ $sale->cashier->name ?? '—' }}</span></div>
                    <div class="kv-row"><span class="k">Customer</span><span class="v">{{ $sale->customer->name ?? 'Walk-in' }}</span></div>
                    <div class="kv-row"><span class="k">Reason</span><span class="v"><span class="tag tag-gold">{{ $sale->return_reason }}</span></span></div>
                    <div class="kv-row" style="background:var(--sand-50)"><span class="k" style="font-weight:800">Refund Total</span><span class="v" style="font-weight:800">TZS {{ number_format($sale->total_amount,0) }}</span></div>
                    <div class="kv-row"><span class="k">Subtotal</span><span class="v">TZS {{ number_format($sale->subtotal,0) }}</span></div>
                    <div class="kv-row"><span class="k">Discount</span><span class="v">TZS {{ number_format($sale->discount_amount,0) }}</span></div>
                    <div class="kv-row"><span class="k">Profit Reversed</span><span class="v" style="color:var(--danger)">TZS {{ number_format($sale->profit_amount,0) }}</span></div>
                    <div class="kv-row"><span class="k">Status</span><span class="v"><span class="tag tag-red">returned</span></span></div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><div class="panel-title">Payments — original</div></div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:8px">
                @forelse($sale->payments as $p)
                    <div style="display:flex;justify-content:space-between;padding:8px 12px;background:var(--sand-50);border:1px solid var(--line);border-radius:10px;">
                        <span style="display:inline-flex;align-items:center;gap:6px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/></svg> {{ ucfirst(str_replace('_',' ',$p->payment_method)) }}</span>
                        <span style="font-weight:700">TZS {{ number_format($p->amount,0) }}</span>
                    </div>
                @empty
                    <div class="muted" style="font-size:13px">No payments</div>
                @endforelse
                <div style="font-size:12px;color:var(--ink-soft);margin-top:4px">Refund to be processed via original payment method.</div>
            </div>
        </div>

        <div class="panel" style="padding:16px;background:var(--sand-50)">
            <div style="font-weight:800;color:var(--coffee-900)">Audit</div>
            <div style="font-size:12px;color:var(--ink-soft);margin-top:6px;line-height:1.6">
                Return creates <code>audit_logs</code> entry <span class="tag tag-grey">return_sale</span> and <code>stock_movements</code> type <code>return</code> per product — check <a href="{{ route('audit-logs.index') }}?q=return_sale" style="color:var(--terracotta-600);font-weight:700">Audit Logs</a>.
            </div>
        </div>
    </div>
</div>
<style>@media(max-width:900px){ div[style*="grid-template-columns:1.2fr"]{grid-template-columns:1fr !important} }</style>
@endsection
