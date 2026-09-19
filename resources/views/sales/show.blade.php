@extends('layouts.admin')
@section('title','Sale '.$sale->receipt_number)
@section('content')
<div class="page-head">
    <div><h1>Sale {{ $sale->receipt_number }}</h1><p class="page-sub">{{ $sale->created_at->format('d/m/Y H:i') }} · Cashier {{ $sale->cashier->name ?? '—' }} · {{ $sale->customer->name ?? 'Walk-in' }}</p></div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('sales.index') }}" class="btn btn-ghost btn-sm">Back</a>
        <a href="{{ route('sales.receipt', encId($sale->id)) }}" target="_blank" class="btn btn-ghost btn-sm">Receipt (80mm)</a>
        <a href="{{ route('sales.receipt', encId($sale->id)) }}?autoprint=1" target="_blank" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg> Print Thermal</a>
        @if($sale->status !== 'returned')
            <a href="{{ route('returns.create', ['sale'=>encId($sale->id)]) }}" class="btn btn-ghost btn-sm" style="border-color:var(--danger);color:var(--danger)"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/></svg> Return</a>
        @else
            <span class="tag tag-red" style="align-self:center">Returned — {{ $sale->return_reason }}</span>
        @endif
    </div>
</div>

<div style="display:grid;grid-template-columns:1.2fr .8fr;gap:16px">
    <div class="table-card">
        <div class="panel-head"><div class="panel-title">Items</div><span class="tag tag-grey">{{ $sale->items->count() }} items</span></div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Product</th><th class="center">Qty</th><th class="right">Price</th><th class="right">Discount</th><th class="right">Total</th><th class="right">Profit</th></tr></thead>
                <tbody>
                @foreach($sale->items as $it)
                    <tr>
                        <td><div class="cell-title">{{ $it->product->name ?? '—' }}</div><div class="cell-sub">{{ $it->product->sku ?? '' }}</div></td>
                        <td class="center">{{ $it->quantity }}</td>
                        <td class="right">TZS {{ number_format($it->selling_price,0) }}</td>
                        <td class="right">TZS {{ number_format($it->discount,0) }}</td>
                        <td class="right" style="font-weight:700">TZS {{ number_format($it->total,0) }}</td>
                        <td class="right" style="color:var(--acacia-600)">TZS {{ number_format($it->profit,0) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div style="display:flex;flex-direction:column;gap:16px">
        <div class="panel">
            <div class="panel-head"><div class="panel-title">Summary</div></div>
            <div style="padding:16px">
                <div class="kv">
                    <div class="kv-row"><span class="k">Subtotal</span><span class="v">TZS {{ number_format($sale->subtotal,0) }}</span></div>
                    <div class="kv-row"><span class="k">Discount</span><span class="v" style="color:var(--danger)">- TZS {{ number_format($sale->discount_amount,0) }}</span></div>
                    <div class="kv-row"><span class="k">Tax</span><span class="v">TZS {{ number_format($sale->tax_amount,0) }}</span></div>
                    <div class="kv-row" style="background:var(--sand-50)"><span class="k" style="font-weight:800">Total</span><span class="v" style="font-weight:800">TZS {{ number_format($sale->total_amount,0) }}</span></div>
                    <div class="kv-row"><span class="k">Paid</span><span class="v">TZS {{ number_format($sale->paid_amount,0) }}</span></div>
                    <div class="kv-row"><span class="k">Profit</span><span class="v" style="color:var(--acacia-600)">TZS {{ number_format($sale->profit_amount,0) }}</span></div>
                    <div class="kv-row"><span class="k">Status</span><span class="v"><span class="tag tag-green">{{ $sale->status }}</span></span></div>
                </div>
            </div>
        </div>
        <div class="panel">
            <div class="panel-head"><div class="panel-title">Payments</div></div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:8px">
                @foreach($sale->payments as $p)
                    <div style="display:flex;justify-content:space-between;padding:8px 12px;background:var(--sand-50);border:1px solid var(--line);border-radius:10px;">
                        <span style="display:inline-flex;align-items:center;gap:6px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/></svg> {{ ucfirst(str_replace('_',' ',$p->payment_method)) }}</span>
                        <span style="font-weight:700">TZS {{ number_format($p->amount,0) }}</span>
                        @if($p->reference)<span class="cell-mono" style="font-size:12px">{{ $p->reference }}</span>@endif
                    </div>
                @endforeach
            </div>
        </div>
        <div class="panel" style="padding:16px;background:var(--sand-50);">
            <div style="font-weight:800;color:var(--coffee-900);">Receipt</div>
            <div style="margin-top:8px;background:#fff;border:1px solid var(--line);border-radius:10px;padding:14px;font-family:monospace;font-size:12px;line-height:1.6">
                <div style="text-align:center;font-weight:800">{{ \App\Models\Setting::getValue('shop_name','SHOP POS') }}</div>
                <div style="text-align:center;color:var(--ink-soft)">{{ $sale->receipt_number }} · {{ $sale->created_at->format('d/m/Y') }}</div>
                <div style="border-top:1px dashed var(--line);margin:8px 0"></div>
                @foreach($sale->items as $it)
                    <div style="display:flex;justify-content:space-between"><span>{{ $it->product->name ?? '—' }} × {{ $it->quantity }}</span><span>TZS {{ number_format($it->total,0) }}</span></div>
                @endforeach
                <div style="border-top:1px dashed var(--line);margin:8px 0"></div>
                <div style="display:flex;justify-content:space-between;font-weight:800"><span>TOTAL</span><span>TZS {{ number_format($sale->total_amount,0) }}</span></div>
            </div>
        </div>
    </div>
</div>
<style>@media(max-width:900px){ div[style*="grid-template-columns:1.2fr"]{grid-template-columns:1fr !important} }</style>
@endsection
