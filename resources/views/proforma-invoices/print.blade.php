@extends('layouts.admin')
@section('title','Print '.$proforma->proforma_number)
@section('content')
@php $shopName = \App\Models\Setting::getValue('shop_name', \App\Models\Setting::getValue('university_name','SHOP POS')); @endphp
<div class="no-print" style="display:flex;gap:8px;justify-content:flex-end;margin-bottom:12px">
    <a href="{{ route('proforma-invoices.pdf', encId($proforma->id)) }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download PDF</a>
    <button onclick="window.print()" class="btn btn-ghost btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg> Print</button>
    <a href="{{ route('proforma-invoices.show', encId($proforma->id)) }}" class="btn btn-ghost btn-sm">Back</a>
</div>

<div class="doc-page" id="docPage" style="max-width:210mm;margin:0 auto;background:#fff;color:#14242f;font-family:Raleway,Arial,sans-serif;border:1px solid #dfe8ee;border-radius:8px;overflow:hidden">
    <div style="padding:24px 28px;background:linear-gradient(135deg,#052b3d,#0a4260);color:#fff;display:flex;justify-content:space-between;align-items:flex-start">
        <div style="display:flex;align-items:center;gap:12px">
            <div style="width:48px;height:48px;border-radius:10px;background:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;color:#0a4260;font-size:18px">{{ \Illuminate\Support\Str::substr($shopName,0,2) }}</div>
            <div><div style="font-size:17px;font-weight:800">{{ $shopName }}</div><div style="font-size:11px;opacity:.85;margin-top:2px">{{ \App\Models\Setting::getValue('shop_address','Dar es Salaam, Tanzania') }} · {{ \App\Models\Setting::getValue('shop_phone','+255 700 000 000') }}</div></div>
        </div>
        <div style="text-align:right">
            <div style="font-size:20px;font-weight:800;letter-spacing:.06em;color:#f9cc41">PROFORMA INVOICE</div>
            <div style="font-family:monospace;font-size:13px;margin-top:4px">{{ $proforma->proforma_number }}</div>
        </div>
    </div>
    <div style="padding:22px 28px;display:flex;justify-content:space-between;gap:20px;border-bottom:1px solid #edf2f6">
        <div style="font-size:12.5px;line-height:1.7">
            <div style="font-weight:800;color:#052b3d;margin-bottom:4px">BILL TO</div>
            <div style="font-weight:700">{{ $proforma->customer->name ?? 'Walk-in Customer' }}</div>
            <div>{{ $proforma->customer->phone ?? '' }}</div>
            <div>{{ $proforma->customer->address ?? '' }}</div>
        </div>
        <div style="font-size:12.5px;line-height:1.9;text-align:right">
            <div><strong style="color:#5b7a90">Date:</strong> {{ \Carbon\Carbon::parse($proforma->date)->format('d/m/Y') }}</div>
            <div><strong style="color:#5b7a90">Valid until:</strong> {{ $proforma->valid_until ? \Carbon\Carbon::parse($proforma->valid_until)->format('d/m/Y') : '—' }}</div>
            <div><strong style="color:#5b7a90">Due date:</strong> {{ $proforma->due_date ? \Carbon\Carbon::parse($proforma->due_date)->format('d/m/Y') : '—' }}</div>
            <div><strong style="color:#5b7a90">Reference:</strong> {{ $proforma->quotation->quotation_number ?? '—' }}</div>
            <div><strong style="color:#5b7a90">Status:</strong> <span style="font-weight:800;text-transform:uppercase">{{ str_replace('_',' ',$proforma->status) }}</span></div>
        </div>
    </div>
    <div style="padding:14px 28px">
        <table style="width:100%;border-collapse:collapse;font-size:12.5px">
            <thead><tr style="background:#f4f8fb;color:#052b3d;text-transform:uppercase;font-size:10.5px;letter-spacing:.06em">
                <th style="padding:9px 8px;text-align:left;font-weight:800">#</th>
                <th style="padding:9px 8px;text-align:left;font-weight:800">Description</th>
                <th style="padding:9px 8px;text-align:center;font-weight:800">Qty</th>
                <th style="padding:9px 8px;text-align:right;font-weight:800">Unit Price</th>
                <th style="padding:9px 8px;text-align:right;font-weight:800">Discount</th>
                <th style="padding:9px 8px;text-align:right;font-weight:800">Amount</th>
            </tr></thead>
            <tbody>
            @foreach($proforma->items as $i=>$it)
                <tr style="border-bottom:1px solid #eef2f6">
                    <td style="padding:9px 8px">{{ $i+1 }}</td>
                    <td style="padding:9px 8px"><strong>{{ $it->description ?? ($it->product->name ?? 'Item') }}</strong>@if($it->product && $it->product->sku)<div style="font-family:monospace;font-size:11px;color:#5b7a90">{{ $it->product->sku }}</div>@endif</td>
                    <td style="padding:9px 8px;text-align:center">{{ $it->quantity }}</td>
                    <td style="padding:9px 8px;text-align:right">{{ number_format($it->unit_price,0) }}</td>
                    <td style="padding:9px 8px;text-align:right">{{ $it->discount ? number_format($it->discount,0) : '—' }}</td>
                    <td style="padding:9px 8px;text-align:right;font-weight:700">{{ number_format($it->total,0) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="display:flex;justify-content:flex-end;margin-top:14px">
            <div style="min-width:280px;font-size:12.5px;line-height:2.1">
                <div style="display:flex;justify-content:space-between"><span style="color:#40637a">Subtotal</span><span style="font-weight:700">{{ number_format($proforma->subtotal,0) }}</span></div>
                @if($proforma->discount_amount>0)<div style="display:flex;justify-content:space-between"><span style="color:#40637a">Discount</span><span style="font-weight:700;color:#b33a3a">- {{ number_format($proforma->discount_amount,0) }}</span></div>@endif
                @if($proforma->tax_amount>0)<div style="display:flex;justify-content:space-between"><span style="color:#40637a">VAT ({{ $proforma->tax_rate }}%)</span><span style="font-weight:700">{{ number_format($proforma->tax_amount,0) }}</span></div>@endif
                <div style="display:flex;justify-content:space-between;background:#052b3d;color:#fff;padding:9px 12px;border-radius:6px;font-weight:800;font-size:15px"><span>TOTAL (TZS)</span><span>{{ number_format($proforma->total_amount,0) }}</span></div>
                @if($proforma->paid_amount>0)
                <div style="display:flex;justify-content:space-between;padding:6px 12px"><span style="color:#40637a">Paid</span><span style="font-weight:700;color:#2e7d6b">{{ number_format($proforma->paid_amount,0) }}</span></div>
                <div style="display:flex;justify-content:space-between;padding:6px 12px;font-weight:800"><span style="color:#b33a3a">BALANCE DUE</span><span style="color:#b33a3a">{{ number_format($proforma->balance_due,0) }}</span></div>
                @endif
            </div>
        </div>
    </div>
    <div style="padding:0 28px 20px;display:grid;grid-template-columns:1fr 1fr;gap:16px;font-size:12px;line-height:1.6">
        <div><div style="font-weight:800;color:#052b3d;margin-bottom:4px">PAYMENT TERMS</div><div style="color:#40637a">{{ $proforma->payment_terms ?? '—' }}</div><div style="font-weight:800;color:#052b3d;margin-top:10px;margin-bottom:4px">PAYMENT DETAILS</div><div style="color:#40637a">{!! nl2br(e($proforma->bank_details ?: '—')) !!}</div></div>
        <div><div style="font-weight:800;color:#052b3d;margin-bottom:4px">NOTES & TERMS</div><div style="color:#40637a">{!! nl2br(e($proforma->notes ?: '')) !!} {!! nl2br(e($proforma->terms ?: '')) !!}</div></div>
    </div>
    <div style="text-align:center;padding:12px;background:#052b3d;color:#fff;font-size:11px">This is a provisional invoice — final invoice is issued after payment confirmation · {{ $shopName }}</div>
</div>
<style>
@media print{
  body *{visibility:hidden}
  .doc-page, .doc-page *{visibility:visible}
  .doc-page{position:absolute;left:0;top:0;border:none !important;box-shadow:none !important}
  .no-print{display:none !important}
}
</style>
@endsection