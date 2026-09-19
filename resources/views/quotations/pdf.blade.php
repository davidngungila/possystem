<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quotation {{ $quotation->quotation_number }}</title>
<style>
*{margin:0;padding:0}
body{font-family:'DejaVu Sans',sans-serif;color:#14242f;font-size:12px}
.page{width:190mm;margin:0 auto;padding:16mm 0}
.head{background:#052b3d;color:#fff;padding:20px 24px;border-radius:6px 6px 0 0}
.head table{width:100%;border-collapse:collapse}
.badge{display:inline-block;width:42px;height:42px;line-height:42px;text-align:center;background:#fff;color:#0a4260;font-weight:800;font-size:17px;border-radius:8px;vertical-align:middle}
.doctitle{font-size:20px;font-weight:800;letter-spacing:.06em;color:#f9cc41}
.docnum{font-family:'DejaVu Sans Mono',monospace;font-size:13px;margin-top:4px}
.metabox{padding:20px 24px;border:1px solid #edf2f6;border-top:none}
.metabox table{width:100%;border-collapse:collapse;vertical-align:top}
.lbl{display:block;font-weight:800;color:#052b3d;margin-bottom:4px}
.muted{color:#40637a}
.small{font-size:11px}
.items{padding:16px 24px}
.items h4{font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#052b3d;margin-bottom:8px}
table.grid{width:100%;border-collapse:collapse;font-size:12px}
table.grid th{background:#f4f8fb;color:#052b3d;text-transform:uppercase;font-size:10px;letter-spacing:.05em;padding:9px 8px;text-align:left;font-weight:800}
table.grid td{padding:9px 8px;border-bottom:1px solid #eef2f6;vertical-align:top}
.r{text-align:right}.c{text-align:center}
.amounts{width:245px;float:right;margin-top:16px;font-size:12.5px;line-height:2.1}
.amounts .row{width:100%;overflow:hidden}
.amounts .k{float:left;color:#34495e;font-weight:600}
.amounts .v{float:right;font-weight:800;color:#0f2430}
.amounts .line{margin-top:6px;background:#052b3d;color:#fff;padding:8px 10px;border-radius:4px;font-weight:800;font-size:14px;overflow:hidden}
.amounts .line .k{float:left;color:#fff}
.amounts .line .v{float:right;color:#fff}
.footnotes{padding:0 24px 18px;font-size:11.5px;line-height:1.6;overflow:hidden;clear:both}
.footnotes table{width:100%;border-collapse:collapse;vertical-align:top}
.footer{text-align:center;padding:10px 24px;background:#052b3d;color:#fff;font-size:11px;border-radius:0 0 6px 6px}
.footer b{color:#f9cc41}
</style>
</head>
<body>
@php $shopName = \App\Models\Setting::getValue('shop_name', \App\Models\Setting::getValue('university_name','SHOP POS')); @endphp
<div class="page">
    <div class="head">
        <table><tr>
            <td>
                <span class="badge">{{ \Illuminate\Support\Str::substr($shopName,0,2) }}</span>
                <strong style="font-size:16px;margin-left:10px;vertical-align:middle">{{ $shopName }}</strong>
                <div class="small muted" style="color:#cfe0ea;margin-top:4px;margin-left:52px">{{ \App\Models\Setting::getValue('shop_address','Dar es Salaam, Tanzania') }} &middot; {{ \App\Models\Setting::getValue('shop_phone','+255 700 000 000') }}</div>
            </td>
            <td style="text-align:right">
                <div class="doctitle">QUOTATION</div>
                <div class="docnum">{{ $quotation->quotation_number }}</div>
            </td>
        </tr></table>
    </div>
    <div class="metabox">
        <table><tr>
            <td style="width:50%">
                <span class="lbl">BILL TO</span>
                <div style="font-weight:700">{{ $quotation->customer->name ?? 'Walk-in Customer' }}</div>
                <div class="muted">{{ $quotation->customer->phone ?? '' }}</div>
                <div class="muted">{{ $quotation->customer->address ?? '' }}</div>
            </td>
            <td style="text-align:right;line-height:1.9">
                <div><strong class="muted">Date:</strong> {{ \Carbon\Carbon::parse($quotation->date)->format('d/m/Y') }}</div>
                <div><strong class="muted">Valid until:</strong> {{ $quotation->valid_until ? \Carbon\Carbon::parse($quotation->valid_until)->format('d/m/Y') : '—' }}</div>
                <div><strong class="muted">Status:</strong> <span style="font-weight:800;text-transform:uppercase">{{ str_replace('_',' ',$quotation->status) }}</span></div>
            </td>
        </tr></table>
    </div>
    <div class="items">
        <h4>Items</h4>
        <table class="grid">
            <thead><tr>
                <th style="width:5%">#</th><th>Description</th><th class="c" style="width:9%">Qty</th><th class="r" style="width:16%">Unit Price</th><th class="r" style="width:14%">Discount</th><th class="r" style="width:15%">Amount</th>
            </tr></thead>
            <tbody>
            @foreach($quotation->items as $i=>$it)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td><strong>{{ $it->description ?? ($it->product->name ?? 'Item') }}</strong>@if($it->product && $it->product->sku)<div class="muted small" style="font-family:'DejaVu Sans Mono',monospace">{{ $it->product->sku }}</div>@endif</td>
                    <td class="c">{{ $it->quantity }}</td>
                    <td class="r">{{ number_format($it->unit_price,0) }}</td>
                    <td class="r">{{ $it->discount ? number_format($it->discount,0) : '—' }}</td>
                    <td class="r" style="font-weight:700">{{ number_format($it->total,0) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="amounts">
            <div class="row"><span class="k">Subtotal</span><span class="v">{{ number_format($quotation->subtotal,0) }}</span></div>
            @if($quotation->discount_amount>0)<div class="row"><span class="k">Discount</span><span class="v" style="color:#b33a3a">- {{ number_format($quotation->discount_amount,0) }}</span></div>@endif
            @if($quotation->tax_amount>0)<div class="row"><span class="k">VAT ({{ $quotation->tax_rate }}%)</span><span class="v">{{ number_format($quotation->tax_amount,0) }}</span></div>@endif
            <div class="line"><span class="k">TOTAL (TZS)</span><span class="v">{{ number_format($quotation->total_amount,0) }}</span></div>
        </div>
    </div>
    <div class="footnotes">
        <table><tr>
            <td style="width:50%;padding-right:20px">
                <span class="lbl">NOTES</span>
                <div class="muted">{!! nl2br(e($quotation->notes ?: '')) !!}</div>
            </td>
            <td>
                <span class="lbl">TERMS &amp; CONDITIONS</span>
                <div class="muted">{!! nl2br(e($quotation->terms ?: '')) !!}</div>
            </td>
        </tr></table>
    </div>
    <div class="footer">Quotation valid for the period stated &middot; <b>{{ $shopName }}</b> &middot; @if($quotation->salesperson) Prepared by {{ $quotation->salesperson->name }}@endif</div>
</div>
</body>
</html>