@extends('layouts.admin')
@section('title','Receipt '.$sale->receipt_number)
@section('content')
<div class="page-head">
    <div><h1>Receipt {{ $sale->receipt_number }}</h1><p class="page-sub">{{ $sale->created_at->format('d/m/Y H:i') }} · {{ $sale->shop->name ?? '' }} · {{ $sale->cashier->name ?? '' }}</p></div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('sales.index') }}" class="btn btn-ghost btn-sm">Back to Sales</a>
        <a href="{{ route('sales.receipt', encId($sale->id)) }}" target="_blank" class="btn btn-ghost btn-sm">Thermal Print</a>
        <button onclick="window.print()" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg> Print</button>
        <button onclick="downloadReceiptPDF()" class="btn btn-primary btn-sm" style="background:#2e7d6b"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download PDF</button>
    </div>
</div>

<div style="display:flex;justify-content:center;padding:20px;background:var(--sand-100);border-radius:12px">
<div class="receipt" id="receiptFull" style="width:80mm;max-width:80mm;background:#fff;border:1px solid #c9d6e2;border-radius:10px;overflow:hidden;box-shadow:0 8px 28px rgba(16,36,48,.14)">
  <div style="padding:14px 12px 10px;background:linear-gradient(135deg,#052b3d 0%,#0a4260 100%);color:#fff;text-align:center">
    @php $shopName = \App\Models\Setting::getValue('shop_name', \App\Models\Setting::getValue('university_name','SHOP POS')); @endphp
    <div style="width:42px;height:42px;border-radius:10px;background:#fff;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;font-weight:800;color:#0a4260">{{ \Illuminate\Support\Str::substr($shopName,0,2) }}</div>
    <div style="font-size:14px;font-weight:800;letter-spacing:.02em">{{ $shopName }}</div>
    <div style="font-size:9px;letter-spacing:.12em;text-transform:uppercase;color:#f9cc41;font-weight:700;margin-top:2px">Barcode POS & Inventory</div>
    <div style="font-size:9.5px;color:rgba(255,255,255,.82);margin-top:6px;line-height:1.4">
      {{ \App\Models\Setting::getValue('shop_address','Dar es Salaam, Tanzania') }}<br>
      {{ \App\Models\Setting::getValue('shop_phone','+255 700 000 000') }} · {{ \App\Models\Setting::getValue('shop_email','info@shop.co.tz') }}
    </div>
  </div>
  <div style="padding:10px 10px 12px">
    <div style="text-align:center;margin:8px 0 6px"><h2 style="margin:0;font-size:12px;font-weight:800;letter-spacing:.1em;color:#052b3d;text-transform:uppercase">Sales Receipt</h2><div style="margin-top:4px;display:inline-block;padding:4px 10px;background:#f5f8fc;border:1px solid #e2eaf2;border-radius:20px;font-family:monospace;font-size:11px;font-weight:700;color:#0a4260">{{ $sale->receipt_number }}</div></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin:10px 0;padding:8px;background:#f8fafc;border:1px solid #e6eef6;border-radius:8px;font-size:10.5px;line-height:1.4">
      <div><span style="color:#40637a;font-size:9px;letter-spacing:.06em;text-transform:uppercase;font-weight:700;display:block">Date & Time</span><strong style="color:#0f2430">{{ $sale->created_at->format('d/m/Y · H:i:s') }}</strong></div>
      <div><span style="color:#40637a;font-size:9px;letter-spacing:.06em;text-transform:uppercase;font-weight:700;display:block">Cashier</span><strong style="color:#0f2430">{{ $sale->cashier->name ?? '—' }}</strong></div>
      <div><span style="color:#40637a;font-size:9px;letter-spacing:.06em;text-transform:uppercase;font-weight:700;display:block">Customer</span><strong style="color:#0f2430">{{ $sale->customer->name ?? 'Walk-in' }}</strong></div>
      <div><span style="color:#40637a;font-size:9px;letter-spacing:.06em;text-transform:uppercase;font-weight:700;display:block">Shop</span><strong style="color:#0f2430">{{ $sale->shop->name ?? $shopName }}</strong></div>
    </div>
    <table style="width:100%;border-collapse:collapse;margin-top:8px;font-size:10.5px">
      <thead><tr><th style="font-size:9px;letter-spacing:.07em;text-transform:uppercase;color:#5b7a90;padding:6px 4px;border-bottom:1.5px solid #1a3a52;text-align:left;font-weight:800;width:38%">Item</th><th style="font-size:9px;letter-spacing:.07em;text-transform:uppercase;color:#5b7a90;padding:6px 4px;border-bottom:1.5px solid #1a3a52;text-align:center;width:12%">Qty</th><th style="font-size:9px;letter-spacing:.07em;text-transform:uppercase;color:#5b7a90;padding:6px 4px;border-bottom:1.5px solid #1a3a52;text-align:right;width:25%">Price</th><th style="font-size:9px;letter-spacing:.07em;text-transform:uppercase;color:#5b7a90;padding:6px 4px;border-bottom:1.5px solid #1a3a52;text-align:right;width:25%">Total</th></tr></thead>
      <tbody>
      @foreach($sale->items as $it)
        <tr><td style="padding:7px 4px;border-bottom:1px solid #eef2f7;vertical-align:top"><div style="font-weight:700;line-height:1.2">{{ $it->product->name ?? '—' }}</div><small style="color:#5b7a90;font-size:10px;font-family:monospace">{{ $it->product->sku ?? '' }}@if($it->product->barcode) · {{ $it->product->barcode }}@endif</small></td><td style="padding:7px 4px;border-bottom:1px solid #eef2f7;text-align:center">{{ $it->quantity }}</td><td style="padding:7px 4px;border-bottom:1px solid #eef2f7;text-align:right">TZS {{ number_format($it->selling_price,0) }}</td><td style="padding:7px 4px;border-bottom:1px solid #eef2f7;text-align:right;font-weight:800">TZS {{ number_format($it->total,0) }}</td></tr>
      @endforeach
      </tbody>
    </table>
    <div style="margin-top:8px;border-top:1.5px dashed #c9d6e2;padding-top:8px;font-size:11px">
      <div style="display:flex;justify-content:space-between;gap:8px;padding:3px 0"><span>Subtotal</span><span>TZS {{ number_format($sale->subtotal,0) }}</span></div>
      @if($sale->discount_amount>0)<div style="display:flex;justify-content:space-between;gap:8px;padding:3px 0"><span>Discount</span><span>- TZS {{ number_format($sale->discount_amount,0) }}</span></div>@endif
      <div style="display:flex;justify-content:space-between;gap:8px;padding:3px 0;font-weight:800;font-size:12px;color:#052b3d;border-top:1.5px solid #0a4260;padding-top:6px;margin-top:4px"><span>TOTAL</span><span>TZS {{ number_format($sale->total_amount,0) }}</span></div>
      <div style="display:flex;justify-content:space-between;gap:8px;padding:3px 0;color:#2e7d6b"><span>Paid</span><span>TZS {{ number_format($sale->paid_amount,0) }}</span></div>
      @if($sale->paid_amount > $sale->total_amount)<div style="display:flex;justify-content:space-between;gap:8px;padding:4px 6px;border-radius:6px;margin-top:4px;background:#f1f8f3;color:#0a4260;font-weight:700"><span>Change</span><span>TZS {{ number_format($sale->paid_amount - $sale->total_amount,0) }}</span></div>@endif
    </div>
    <div style="margin-top:8px;padding:8px;background:#f8fafc;border:1px solid #e6eef6;border-radius:8px">
      <h4 style="margin:0 0 6px;font-size:10px;letter-spacing:.08em;text-transform:uppercase;color:#5b7a90">Payment Details</h4>
      @foreach($sale->payments as $p)
        <div style="display:flex;justify-content:space-between;gap:8px;padding:4px 0;border-bottom:1px dashed #e6eef6;font-size:11px"><span>{{ ucfirst(str_replace('_',' ',$p->payment_method)) }} @if($p->reference)<small style="color:#5b7a90">({{ $p->reference }})</small>@endif</span><span style="font-weight:700">TZS {{ number_format($p->amount,0) }}</span></div>
      @endforeach
    </div>
    <div style="margin:10px 0 0;text-align:center;padding:8px;background:#fff;border:1px solid #e6eef6;border-radius:8px">
      <svg id="receiptBarcodeFull"></svg>
      <div style="font-size:9px;letter-spacing:.08em;text-transform:uppercase;color:#5b7a90;margin-top:4px">{{ $sale->receipt_number }}</div>
    </div>
    <div style="text-align:center;margin-top:8px;font-size:10px;color:#5b7a90;line-height:1.4">Thank you for your purchase!<br><strong style="color:#0f2430">{{ \App\Models\Setting::getValue('receipt_footer','Goods once sold are not returnable unless faulty.') }}</strong></div>
  </div>
  <div style="text-align:center;margin-top:10px;padding:8px;background:#052b3d;color:#fff;border-radius:0 0 10px 10px;font-size:10px;line-height:1.5">
    <div style="font-weight:700;letter-spacing:.04em">Karibu Tena — Welcome Again!</div>
    <div style="opacity:.8;margin-top:2px">Printed {{ now()->format('d/m/Y H:i') }} · 80mm Thermal Print</div>
  </div>
</div>
</div>

<div style="display:flex;gap:8px;justify-content:center;margin-top:16px;flex-wrap:wrap" class="no-print">
  <button onclick="window.print()" class="btn btn-primary"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg> Print (80mm)</button>
  <button onclick="downloadFullPDF()" class="btn btn-ghost"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download PDF</button>
  <a href="{{ route('sales.index') }}" class="btn btn-ghost">Back to Sales</a>
  <a href="{{ route('pos.create') }}" class="btn btn-ghost">New Sale</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script>
try{ JsBarcode("#receiptBarcodeFull", "{{ $sale->receipt_number }}", {format:"CODE128", width:1.4, height:36, displayValue:false, margin:6}); }catch(e){}
async function downloadFullPDF(){
  const el=document.getElementById('receiptFull');
  const btn=event?.target?.closest('button');
  const orig=btn?btn.innerHTML:'';
  if(btn) btn.innerHTML='Generating…';
  try{
    const canvas=await html2canvas(el,{scale:2,backgroundColor:'#ffffff',useCORS:true});
    const imgData=canvas.toDataURL('image/png');
    const {jsPDF}=window.jspdf;
    const pdfW=80;
    const pdfH=(canvas.height*pdfW)/canvas.width;
    const pdf=new jsPDF({orientation:'portrait',unit:'mm',format:[pdfW,pdfH]});
    pdf.addImage(imgData,'PNG',0,0,pdfW,pdfH);
    pdf.save('{{ $sale->receipt_number }}.pdf');
  }catch(e){ window.print(); } finally{ if(btn) btn.innerHTML=orig; }
}
</script>
<style>
@media print{
  body *{visibility:hidden}
  .receipt, .receipt *{visibility:visible}
  .receipt{position:absolute;left:50%;transform:translateX(-50%);top:0;box-shadow:none !important;border:none !important}
  .no-print{display:none !important}
}
@media(max-width:640px){ .receipt{width:92vw !important;max-width:92vw !important} }
</style>
@endsection
