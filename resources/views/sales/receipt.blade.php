<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Receipt {{ $sale->receipt_number }}</title>
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  @page { size: 80mm auto; margin: 3mm; }
  *{box-sizing:border-box}
  body{margin:0;font-family:'Raleway',system-ui,-apple-system,Segoe UI,Roboto,sans-serif;font-size:11px;color:#0f2430;background:#f5f8fc;-webkit-print-color-adjust:exact}
  .receipt-wrap{max-width:88mm;margin:12px auto;padding:0 8px}
  .receipt{width:80mm;max-width:80mm;margin:0 auto;background:#fff;border:1px solid #c9d6e2;border-radius:10px;overflow:hidden;box-shadow:0 8px 28px rgba(16,36,48,.14)}
  .r-head{padding:14px 12px 10px;background:linear-gradient(135deg,#052b3d 0%,#0a4260 100%);color:#fff;text-align:center}
  .r-logo{width:42px;height:42px;border-radius:10px;background:#fff;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;overflow:hidden;font-weight:800;color:#0a4260}
  .r-logo img{width:100%;height:100%;object-fit:contain}
  .r-shop{font-size:14px;font-weight:800;letter-spacing:.02em;line-height:1.1}
  .r-tag{font-size:9px;letter-spacing:.12em;text-transform:uppercase;color:#f9cc41;font-weight:700;margin-top:2px}
  .r-contact{font-size:9.5px;color:rgba(255,255,255,.82);margin-top:6px;line-height:1.4}
  .r-body{padding:10px 10px 12px}
  .r-title{text-align:center;margin:8px 0 6px}
  .r-title h2{margin:0;font-size:12px;font-weight:800;letter-spacing:.1em;color:#052b3d;text-transform:uppercase}
  .r-title .r-no{margin-top:4px;display:inline-block;padding:4px 10px;background:#f5f8fc;border:1px solid #e2eaf2;border-radius:20px;font-family:ui-monospace,monospace;font-size:11px;font-weight:700;color:#0a4260}
  .r-meta{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin:10px 0;padding:8px;background:#f8fafc;border:1px solid #e6eef6;border-radius:8px;font-size:10.5px;line-height:1.4}
  .r-meta span{color:#40637a;font-size:9px;letter-spacing:.06em;text-transform:uppercase;font-weight:700;display:block}
  .r-meta strong{color:#0f2430;font-weight:700;word-break:break-all}
  .r-table{width:100%;border-collapse:collapse;margin-top:8px;font-size:10.5px}
  .r-table th{font-size:9px;letter-spacing:.07em;text-transform:uppercase;color:#5b7a90;padding:6px 4px;border-bottom:1.5px solid #1a3a52;text-align:left;font-weight:800}
  .r-table th.right,.r-table td.right{text-align:right}
  .r-table th.center,.r-table td.center{text-align:center}
  .r-table td{padding:7px 4px;border-bottom:1px solid #eef2f7;vertical-align:top;color:#0f2430}
  .r-table td small{color:#5b7a90;font-size:10px}
  .r-totals{margin-top:8px;border-top:1.5px dashed #c9d6e2;padding-top:8px;font-size:11px}
  .r-row{display:flex;justify-content:space-between;gap:8px;padding:3px 0}
  .r-row.total{font-weight:800;font-size:12px;color:#052b3d;border-top:1.5px solid #0a4260;padding-top:6px;margin-top:4px}
  .r-row.paid{color:#2e7d6b}
  .r-row.change{color:#0a4260;font-weight:700;background:#f1f8f3;padding:4px 6px;border-radius:6px;margin-top:4px}
  .r-pay{margin-top:8px;padding:8px;background:#f8fafc;border:1px solid #e6eef6;border-radius:8px}
  .r-pay h4{margin:0 0 6px;font-size:10px;letter-spacing:.08em;text-transform:uppercase;color:#5b7a90}
  .r-pay-row{display:flex;justify-content:space-between;gap:8px;padding:4px 0;border-bottom:1px dashed #e6eef6;font-size:11px}
  .r-pay-row:last-child{border-bottom:none}
  .r-barcode{margin:10px 0 0;text-align:center;padding:8px;background:#fff;border:1px solid #e6eef6;border-radius:8px}
  .r-barcode svg{max-width:100%}
  .r-footer{text-align:center;margin-top:10px;padding:8px;background:#052b3d;color:#fff;border-radius:0 0 10px 10px;font-size:10px;line-height:1.5}
  .r-footer .thanks{font-weight:700;letter-spacing:.04em}
  .r-actions{margin-top:10px;display:flex;gap:8px;justify-content:center;flex-wrap:wrap}
  .r-btn{padding:9px 14px;border-radius:8px;border:1.5px solid #c9d6e2;font-weight:700;font-size:12px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:all .15s}
  .r-btn-primary{background:#0a4260;color:#fff;border-color:#0a4260}
  .r-btn-primary:hover{background:#083652}
  .r-btn-ghost{background:#fff;color:#0a4260}
  .r-btn-ghost:hover{background:#f5f8fc}
  .no-print{display:flex}
  @media print{
    body{margin:0;background:#fff;padding:0}
    .receipt-wrap{margin:0;padding:0;max-width:none}
    .receipt{width:80mm;max-width:80mm;margin:0 auto;box-shadow:none;border:none;border-radius:0}
    .r-head{ -webkit-print-color-adjust:exact; print-color-adjust:exact}
    .no-print{display:none !important}
    .receipt{border:none}
  }
  @media screen and (max-width:480px){
    .receipt-wrap{padding:0 4px}
    .r-meta{grid-template-columns:1fr}
  }
</style>
</head>
<body>
<div class="receipt-wrap">
<div class="receipt" id="receiptContent">
  <div class="r-head">
    @php
      $shopLogo = \App\Models\Setting::getValue('shop_logo', \App\Models\Setting::getValue('university_logo'));
      $shopName = \App\Models\Setting::getValue('shop_name', \App\Models\Setting::getValue('university_name','SHOP POS'));
      $shopAcro = \Illuminate\Support\Str::substr(\App\Models\Setting::getValue('shop_acronym', \App\Models\Setting::getValue('university_acronym','SP')),0,2);
    @endphp
    <div class="r-logo" style="@if($shopLogo)background:#fff;padding:4px;@endif">
      @if($shopLogo)<img src="{{ asset($shopLogo) }}" alt="Logo">@else {{ $shopAcro }} @endif
    </div>
    <div class="r-shop">{{ $shopName }}</div>
    <div class="r-tag">Barcode POS & Inventory</div>
    <div class="r-contact">
      {{ \App\Models\Setting::getValue('shop_address','Dar es Salaam, Tanzania') }}<br>
      {{ \App\Models\Setting::getValue('shop_phone','+255 700 000 000') }} · {{ \App\Models\Setting::getValue('shop_email','info@shop.co.tz') }}
      @if(\App\Models\Setting::getValue('shop_tin'))<br>TIN: {{ \App\Models\Setting::getValue('shop_tin') }}@endif
      @if(isset($sale->shop) && $sale->shop) <br>{{ $sale->shop->name }} ({{ $sale->shop->code }}) @endif
    </div>
  </div>

  <div class="r-body">
    <div class="r-title">
      <h2>Sales Receipt</h2>
      <div class="r-no">{{ $sale->receipt_number }}</div>
    </div>

    <div class="r-meta">
      <div><span>Date & Time</span><strong>{{ $sale->created_at->format('d/m/Y · H:i:s') }}</strong></div>
      <div><span>Cashier</span><strong>{{ $sale->cashier->name ?? '—' }}</strong></div>
      <div><span>Customer</span><strong>{{ $sale->customer->name ?? 'Walk-in' }}</strong> @if($sale->customer && $sale->customer->phone)<br><small>{{ $sale->customer->phone }}</small>@endif</div>
      <div><span>Shop</span><strong>{{ $sale->shop->name ?? $shopName }}</strong></div>
    </div>

    <table class="r-table">
      <thead><tr><th style="width:38%">Item</th><th class="center" style="width:12%">Qty</th><th class="right" style="width:25%">Price</th><th class="right" style="width:25%">Total</th></tr></thead>
      <tbody>
      @foreach($sale->items as $idx=>$it)
        <tr>
          <td>
            <div style="font-weight:700;line-height:1.2">{{ $it->product->name ?? '—' }}</div>
            <small class="mono">{{ $it->product->sku ?? '' }}@if($it->product->barcode) · {{ $it->product->barcode }}@endif</small>
          </td>
          <td class="center">{{ $it->quantity }}</td>
          <td class="right">TZS {{ number_format($it->selling_price,0) }}@if($it->discount>0)<br><small>-{{ number_format($it->discount,0) }}</small>@endif</td>
          <td class="right" style="font-weight:800">TZS {{ number_format($it->total,0) }}</td>
        </tr>
      @endforeach
      </tbody>
    </table>

    <div class="r-totals">
      <div class="r-row"><span>Subtotal ({{ $sale->items->sum('quantity') }} items)</span><span>TZS {{ number_format($sale->subtotal,0) }}</span></div>
      @if($sale->discount_amount>0)<div class="r-row"><span>Discount</span><span>- TZS {{ number_format($sale->discount_amount,0) }}</span></div>@endif
      @if($sale->tax_amount>0)<div class="r-row"><span>Tax</span><span>TZS {{ number_format($sale->tax_amount,0) }}</span></div>@endif
      <div class="r-row total"><span>TOTAL</span><span>TZS {{ number_format($sale->total_amount,0) }}</span></div>
      <div class="r-row paid"><span>Paid</span><span>TZS {{ number_format($sale->paid_amount,0) }}</span></div>
      @if($sale->paid_amount > $sale->total_amount)
        <div class="r-row change"><span>Change to Customer</span><span>TZS {{ number_format($sale->paid_amount - $sale->total_amount,0) }}</span></div>
      @endif
      @if($sale->profit_amount)<div class="r-row" style="font-size:10px;color:#5b7a90"><span>Profit</span><span>TZS {{ number_format($sale->profit_amount,0) }}</span></div>@endif
    </div>

    <div class="r-pay">
      <h4>Payment Details</h4>
      @foreach($sale->payments as $p)
        <div class="r-pay-row"><span>{{ ucfirst(str_replace('_',' ',$p->payment_method)) }} @if($p->reference)<small class="mono" style="color:#5b7a90">({{ $p->reference }})</small>@endif</span><span style="font-weight:700">TZS {{ number_format($p->amount,0) }}</span></div>
      @endforeach
      @if($sale->payments->isEmpty())
        <div class="r-pay-row"><span>Cash</span><span>TZS {{ number_format($sale->total_amount,0) }}</span></div>
      @endif
    </div>

    <div class="r-barcode">
      <svg id="receiptBarcode"></svg>
      <div style="font-size:9px;letter-spacing:.08em;text-transform:uppercase;color:#5b7a90;margin-top:4px">{{ $sale->receipt_number }}</div>
    </div>

    <div style="text-align:center;margin-top:8px;font-size:10px;color:#5b7a90;line-height:1.4">
      Thank you for your purchase!<br>
      <strong style="color:#0f2430">{{ \App\Models\Setting::getValue('receipt_footer','Goods once sold are not returnable unless faulty. Keep receipt for returns.') }}</strong>
    </div>
  </div>

  <div class="r-footer">
    <div class="thanks">Karibu Tena — Welcome Again!</div>
    <div style="opacity:.8;margin-top:2px">Printed {{ now()->format('d/m/Y H:i') }} · 80mm Thermal Print · System v1.0</div>
  </div>
</div>

<div class="r-actions no-print">
  <button onclick="handlePrint()" class="r-btn r-btn-primary"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg> Print</button>
  <button onclick="handleDownloadPDF()" class="r-btn r-btn-ghost"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download PDF</button>
  <button onclick="if(window.opener){window.close()}else{history.back()}" class="r-btn r-btn-ghost">Close</button>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script>
  // Render receipt barcode (CODE128)
  try { JsBarcode("#receiptBarcode", "{{ $sale->receipt_number }}", {format:"CODE128", width:1.4, height:36, displayValue:false, margin:6}); } catch(e){}

  function handlePrint(){
    // hide actions for clean print, then print
    const actions = document.querySelector('.r-actions');
    const prevDisplay = actions ? actions.style.display : '';
    if(actions) actions.style.display='none';
    window.print();
    setTimeout(()=>{ if(actions) actions.style.display=prevDisplay||'flex'; }, 500);
  }

  async function handleDownloadPDF(){
    const receipt = document.getElementById('receiptContent') || document.querySelector('.receipt');
    if(!receipt){ window.print(); return; }
    const btn = event?.target?.closest('button');
    const origText = btn ? btn.innerHTML : '';
    if(btn) btn.innerHTML='Generating…';
    try {
      const canvas = await html2canvas(receipt, {scale:2, backgroundColor:'#ffffff', useCORS:true});
      const imgData = canvas.toDataURL('image/png');
      const { jsPDF } = window.jspdf;
      // 80mm width = 226.77 pt (80 * 2.83465), height auto
      const pdfWidth = 80; // mm
      const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
      const pdf = new jsPDF({orientation:'portrait', unit:'mm', format:[pdfWidth, pdfHeight]});
      pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
      pdf.save('{{ $sale->receipt_number }}.pdf');
    } catch(e){
      // fallback to print
      window.print();
    } finally {
      if(btn) btn.innerHTML=origText;
    }
  }

  // Auto-print when opened as popup after sale
  const params = new URLSearchParams(location.search);
  if(params.get('autoprint')==='1'){ setTimeout(()=>{ handlePrint(); }, 500); }

  // Allow parent modal to trigger print via postMessage
  window.addEventListener('message', (e)=>{
    if(e.data==='print-receipt') handlePrint();
  });
</script>
</body>
</html>
