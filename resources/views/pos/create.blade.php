@extends('layouts.pos')
@section('title','POS — New Sale')
@section('content')
<div class="page-head" style="margin-bottom:12px">
    <div><h1 style="display:flex;align-items:center;gap:8px;">POS <span class="tag tag-green">Barcode Scanner Ready</span> <span class="info-icon" tabindex="0">i<span class="tooltip">Scan barcode → find product → check stock → add to cart → payment → receipt → stock deduction</span></span></h1><p class="page-sub">Barcode POS — Ready to Scan</p></div>
    <div style="display:flex;gap:8px;align-items:center;">
        <span class="tag tag-grey">Held: 0</span>
        <button class="btn btn-ghost btn-sm" onclick="toast('Holds offline then syncs via unique TxID','info')">Held Sales</button>
    </div>
</div>

<div style="display:grid;grid-template-columns:1.2fr .8fr;gap:16px;align-items:start;">
    {{-- Left — Scan + Cart --}}
    <div class="table-card">
        <div class="panel-head">
            <div style="flex:1;display:flex;align-items:center;gap:10px;">
                <div class="stat-icon ic-terracotta" style="width:36px;height:36px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="7" y1="8" x2="7" y2="16"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="17" y1="8" x2="17" y2="16"/></svg></div>
                <div style="flex:1">
                    <div class="field" style="margin:0">
                        <input id="barcodeInput" placeholder="SCAN or type barcode / SKU / name … (F2 to focus, Enter to add)" style="width:100%;padding:12px 14px;border:1.5px solid var(--line);border-radius:10px;font-size:14px;" autofocus>
                        <div id="searchResults" style="position:absolute;background:#fff;border:1px solid var(--line);border-radius:10px;box-shadow:var(--shadow-md);max-height:240px;overflow:auto;display:none;z-index:20;width:100%;margin-top:4px;"></div>
                    </div>
                    <div style="font-size:11px;color:var(--ink-soft);margin-top:4px;display:flex;align-items:center;gap:6px;flex-wrap:wrap">Barcode scan <span class="info-icon" tabindex="0">i<span class="tooltip">Barcode is primary · fallback manual search by name / SKU / category · camera fallback — scan or type 2+ chars to search</span></span><span style="opacity:.8">— scan or type to search</span></div>
                </div>
            </div>
            <button class="btn btn-ghost btn-sm" onclick="toast('Camera scanner would open here','info')" style="display:inline-flex;align-items:center;gap:6px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg> Camera</button>
        </div>
        <div class="table-toolbar" style="padding:10px 18px;">
            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                @foreach($products->take(6) as $prod)
                    <button class="btn btn-ghost btn-sm" style="padding:6px 10px;font-size:12px" onclick="addProduct({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->selling_price }}, {{ $prod->current_stock }}, '{{ $prod->barcode ?? $prod->sku }}')">{{ $prod->name }} — TZS {{ number_format($prod->selling_price,0) }}</button>
                @endforeach
                @if($products->isEmpty())
                    <span class="muted" style="font-size:12px">No products with stock. <a href="{{ route('products.create') }}" style="color:var(--terracotta-600);font-weight:700">Add product</a></span>
                @endif
            </div>
            <span class="tag tag-grey" style="flex:none">Negative stock: <strong style="margin-left:4px">OFF</strong></span>
        </div>
        <div class="table-scroll">
            <table id="cartTable">
                <thead><tr><th>Product</th><th class="center">Qty</th><th class="right">Price</th><th class="right">Total</th><th></th></tr></thead>
                <tbody id="cartBody">
                    <tr id="emptyCartRow"><td colspan="5"><div class="empty-state" style="padding:16px"><strong>Cart empty</strong><p>Scan a barcode or click a quick product above. Stock is checked before add.</p></div></td></tr>
                </tbody>
            </table>
        </div>
        <div style="padding:12px 18px;background:var(--sand-50);border-top:1px solid var(--line);display:flex;gap:8px;flex-wrap:wrap;">
            <button class="btn btn-ghost btn-sm" onclick="clearCart()">Clear Cart</button>
            <button class="btn btn-ghost btn-sm" onclick="toast('Held — will sync when online','success')">Hold Sale</button>
            <span style="margin-left:auto;font-size:12px;color:var(--ink-soft);">Thermal · A4 · PDF</span>
        </div>
    </div>

    {{-- Right — Totals + Payments --}}
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="panel" style="padding:0;overflow:hidden;">
            <div class="panel-head"><div class="panel-title">Cart Summary</div><span class="tag tag-green">Stock check ON</span></div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:10px;">
                <div class="kv">
                    <div class="kv-row"><span class="k">Subtotal</span><span class="v">TZS <span id="subtotal">0</span></span></div>
                    <div class="kv-row"><span class="k">Discount</span><span class="v" style="color:var(--danger)">- TZS <span id="discount">0</span></span></div>
                    <div class="kv-row"><span class="k">Tax</span><span class="v">TZS <span id="tax">0</span></span></div>
                    <div class="kv-row" style="background:var(--sand-50)"><span class="k" style="font-weight:800;color:var(--coffee-900)">TOTAL</span><span class="v" style="font-size:18px">TZS <span id="total">0</span></span></div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div class="field"><label class="field-label">Customer</label><select id="customerSelect"><option value="">Walk-in Customer</option></select></div>
                    <div class="field"><label class="field-label">Cashier</label><input value="{{ auth()->user()->name ?? 'Cashier' }}" readonly style="background:var(--sand-100)"></div>
                </div>
            </div>
        </div>

        <div class="panel" style="padding:0;overflow:hidden;">
            <div class="panel-head"><div class="panel-title">Payment</div></div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:12px;">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;border-radius:10px;background:var(--sand-100);border:1px solid var(--line);">
                    <span style="font-weight:800;color:var(--coffee-900)">TOTAL</span><span style="font-weight:800;font-size:18px" id="totalPay">TZS 0</span>
                </div>
                <div class="field">
                    <label class="field-label">Payment Method</label>
                    <select id="payMethod" onchange="togglePayRef()" style="width:100%;padding:10px 11px;border:1.5px solid var(--line);border-radius:10px">
                        <option value="cash">Cash</option>
                        <option value="m-pesa">M-Pesa</option>
                        <option value="airtel_money">Airtel Money</option>
                        <option value="mixx">Mixx</option>
                        <option value="halopesa">HaloPesa</option>
                        <option value="bank">Bank / Card</option>
                    </select>
                </div>
                <div class="field">
                    <label class="field-label">Amount Paid</label>
                    <input id="payAmount" type="number" step="0.01" value="0" oninput="recalcPay()" style="width:100%;padding:10px 11px;border:1.5px solid var(--line);border-radius:10px">
                </div>
                <div class="field" id="payRefWrap" style="display:none">
                    <label class="field-label">Reference</label>
                    <input id="payRef" placeholder="TXN ref (for mobile/bank)" style="width:100%;padding:10px 11px;border:1.5px solid var(--line);border-radius:10px">
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;border-radius:10px;background:var(--sand-100);border:1px solid var(--line);">
                    <span style="font-weight:700">Balance</span><span id="balance" style="font-weight:700">TZS 0</span>
                </div>
                <button id="payBtn" class="btn btn-primary" style="width:100%;padding:14px;font-size:16px;font-weight:800" onclick="submitSale()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                    Complete Sale & Print Receipt
                </button>
                <div style="text-align:center;font-size:11px;color:var(--ink-soft);display:flex;align-items:center;gap:6px;justify-content:center;flex-wrap:wrap"><span>Stock handling</span> <span class="info-icon" tabindex="0">i<span class="tooltip">Stock is deducted on successful payment. Completed sales cannot be deleted — only returned. Use Returns to restore stock.</span></span></div>
            </div>
        </div>
    </div>
</div>

<div id="receiptModal" class="modal-backdrop" onclick="if(event.target===this) closeReceipt()">
  <div class="modal modal-lg" style="max-width:420px">
    <div class="modal-head" style="padding:14px 18px">
      <div>
        <h3 style="font-size:15px">Sale Receipt — 80mm</h3>
        <p style="font-size:12px;color:var(--ink-soft)">Preview · Print or close</p>
      </div>
      <button class="modal-close" onclick="closeReceipt()" aria-label="Close"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="modal-body" id="receiptBody" style="padding:0;max-height:70vh;overflow:auto;background:var(--sand-50)">
      <div style="padding:20px;text-align:center;color:var(--ink-soft)">Receipt will appear here…</div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-ghost" onclick="closeReceipt()">Close</button>
      <button class="btn btn-ghost" onclick="downloadReceiptPDF()" style="border:1px solid var(--line)"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download PDF</button>
      <button class="btn btn-ghost" onclick="openReceiptNewWindow()" style="border:1px solid var(--line)">Open in new window</button>
      <button class="btn btn-primary" onclick="printReceipt()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg> Print (80mm)</button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script>
let cart = []; // {id, name, price, qty, stock, barcode}
let subtotal = 0;
const csrf = '{{ csrf_token() }}';

function renderCart(){
    const tbody = document.getElementById('cartBody');
    if(cart.length===0){
        tbody.innerHTML = '<tr id="emptyCartRow"><td colspan="5"><div class="empty-state" style="padding:16px"><strong>Cart empty</strong><p>Scan a barcode or click a quick product above.</p></div></td></tr>';
        document.getElementById('subtotal').textContent='0';
        document.getElementById('total').textContent='0';
        recalcPay();
        return;
    }
    let html='';
    subtotal=0;
    cart.forEach((item, idx)=>{
        const total = item.price * item.qty;
        subtotal += total;
        html += `<tr>
            <td><div class="cell-main"><div class="thumb thumb-terracotta">${item.name.charAt(0).toUpperCase()}</div><div><div class="cell-title" style="font-size:13px">${item.name}</div><div class="cell-sub">${item.barcode} · Stock ${item.stock}</div></div></div></td>
            <td class="center"><div style="display:flex;align-items:center;gap:6px;justify-content:center;"><button class="btn-icon" style="width:28px;height:28px" onclick="changeQty(${idx},-1)">−</button><span class="tag tag-grey">${item.qty}</span><button class="btn-icon" style="width:28px;height:28px" onclick="changeQty(${idx},1)">+</button></div></td>
            <td class="right">TZS ${item.price.toLocaleString()}</td>
            <td class="right" style="font-weight:700">TZS ${total.toLocaleString()}</td>
            <td><button class="btn-icon danger" onclick="removeItem(${idx})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></td>
        </tr>`;
    });
    tbody.innerHTML = html;
    document.getElementById('subtotal').textContent = subtotal.toLocaleString();
    document.getElementById('total').textContent = subtotal.toLocaleString();
    const payAmtEl=document.getElementById('payAmount');
    if(payAmtEl) payAmtEl.value = subtotal;
    recalcPay();
}

function addProduct(id, name, price, stock, barcode){
    const existing = cart.find(c=>c.id===id);
    if(existing){
        if(existing.qty + 1 > stock){
            toast('Insufficient stock for '+name+' (available '+stock+')','error');
            return;
        }
        existing.qty += 1;
    } else {
        if(stock < 1){
            toast('Out of stock: '+name,'error');
            return;
        }
        cart.push({id, name, price: parseFloat(price), qty:1, stock, barcode});
    }
    renderCart();
    toast(name+' — Added to cart','success');
}

function changeQty(idx, delta){
    const item = cart[idx];
    const newQty = item.qty + delta;
    if(newQty < 1){ removeItem(idx); return; }
    if(newQty > item.stock){
        toast('Insufficient stock for '+item.name,'error');
        return;
    }
    item.qty = newQty;
    renderCart();
}

function removeItem(idx){ cart.splice(idx,1); renderCart(); toast('Removed','info'); }
function clearCart(){ cart=[]; renderCart(); toast('Cart cleared','info'); }

function recalcPay(){
    const total = subtotal;
    const paid = parseFloat(document.getElementById('payAmount')?.value)||0;
    const bal = paid - total;
    const el=document.getElementById('balance');
    if(el){ el.textContent='TZS '+bal.toLocaleString(); el.style.color = bal>=0 ? 'var(--acacia-600)' : 'var(--danger)'; }
    const totalPayEl=document.getElementById('totalPay');
    if(totalPayEl) totalPayEl.textContent='TZS '+total.toLocaleString();
    const btn=document.getElementById('payBtn');
    if(btn) btn.disabled = cart.length===0 || bal < 0;
}
function togglePayRef(){
    const m=document.getElementById('payMethod')?.value;
    const wrap=document.getElementById('payRefWrap');
    if(!wrap) return;
    wrap.style.display = (m && m!=='cash') ? 'block' : 'none';
}

async function handleBarcode(q){
    if(!q) return;
    try {
        const res = await fetch('{{ route("products.lookup") }}?q='+encodeURIComponent(q), {headers:{'Accept':'application/json'}});
        const data = await res.json();
        if(Array.isArray(data)){
            if(data.length===1){
                const p=data[0];
                addProduct(p.id, p.name, p.selling_price, p.current_stock, p.barcode||p.sku);
            } else if(data.length>1){
                showSearchResults(data);
                return;
            } else {
                toast('Product not found for: '+q,'error');
            }
        } else if(data && data.id){
            addProduct(data.id, data.name, data.selling_price, data.current_stock, data.barcode||data.sku);
        } else {
            toast('Product not found for: '+q,'error');
        }
        hideSearchResults();
    } catch(e){ toast('Lookup failed','error'); }
}

function showSearchResults(list){
    const box=document.getElementById('searchResults');
    box.innerHTML = list.map(p=> `<div style="padding:10px 12px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--line)" onclick="addProduct(${p.id}, '${p.name.replace(/'/g,"\\'")}', ${p.selling_price}, ${p.current_stock}, '${(p.barcode||p.sku)}'); hideSearchResults(); document.getElementById('barcodeInput').value='';"><span><span style="font-weight:700">${p.name}</span> <span class="cell-mono" style="font-size:12px">${p.sku}</span></span><span class="tag tag-grey">TZS ${p.selling_price}</span></div>`).join('');
    box.style.display='block';
}
function hideSearchResults(){ document.getElementById('searchResults').style.display='none'; }

document.getElementById('barcodeInput')?.addEventListener('keydown', async e=>{
    if(e.key==='Enter'){
        e.preventDefault();
        const v=e.target.value.trim();
        if(!v) return;
        await handleBarcode(v);
        e.target.value='';
    }
});
document.getElementById('barcodeInput')?.addEventListener('input', async e=>{
    const v=e.target.value.trim();
    if(v.length<2){ hideSearchResults(); return; }
    // debounced search for manual typing
    clearTimeout(window._searchT);
    window._searchT = setTimeout(()=>handleBarcode(v), 300);
});
document.addEventListener('keydown',e=>{ if(e.key==='F2'){ e.preventDefault(); document.getElementById('barcodeInput')?.focus(); }});
document.addEventListener('click',e=>{ if(!e.target.closest('#barcodeInput') && !e.target.closest('#searchResults')) hideSearchResults(); });

async function submitSale(){
    if(cart.length===0){ toast('Cart empty','error'); return; }
    const total = subtotal;
    const payments=[];
    const method = document.getElementById('payMethod')?.value || 'cash';
    const amount = parseFloat(document.getElementById('payAmount')?.value)||0;
    const ref = document.getElementById('payRef')?.value||null;
    if(amount>0) payments.push({method, amount, reference: ref});
    const paid = payments.reduce((a,b)=>a+b.amount,0);
    if(paid < total){ toast('Paid amount less than total','error'); return; }
    const items = cart.map(c=>({product_id:c.id, quantity:c.qty, discount:0}));
    document.getElementById('payBtn').disabled=true;
    document.getElementById('payBtn').textContent='Processing...';
    try {
        const res = await fetch('{{ route("pos.store") }}', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf, 'Accept':'application/json'},
            body: JSON.stringify({items, payments, discount_amount:0, tax_amount:0})
        });
        const data = await res.json();
        if(data.success){
            toast('Sale complete — '+data.receipt+' — stock deducted','success');
            cart=[]; renderCart();
            // show receipt in-app popup with print option, plus keep new-window as fallback
            showReceipt(data.receipt_url);
        } else {
            toast(data.message||'Sale failed','error');
            document.getElementById('payBtn').disabled=false;
            document.getElementById('payBtn').textContent='Complete Sale & Print Receipt';
        }
    } catch(e){
        toast('Sale failed: '+e.message,'error');
        document.getElementById('payBtn').disabled=false;
        document.getElementById('payBtn').textContent='Complete Sale & Print Receipt';
    }
}
let lastReceiptUrl=null;
async function showReceipt(url){
  lastReceiptUrl=url;
  const body=document.getElementById('receiptBody');
  body.innerHTML='<div style="padding:20px;text-align:center;color:var(--ink-soft)">Loading receipt…</div>';
  const modal=document.getElementById('receiptModal');
  modal.classList.add('show'); modal.style.display='flex';
  try{
    const r=await fetch(url,{headers:{'Accept':'text/html'}});
    const html=await r.text();
    const doc=new DOMParser().parseFromString(html,'text/html');
    const receipt=doc.querySelector('.receipt');
    const receiptNo=doc.querySelector('.r-no')?.textContent?.trim() || '';
    const styleTags = Array.from(doc.querySelectorAll('style')).map(s=>s.outerHTML).join('');
    if(receipt){
      body.innerHTML='<div style="padding:14px;display:flex;justify-content:center;background:#f5f8fc">'+styleTags+receipt.outerHTML+'</div>';
      body.dataset.fullHtml=html;
      // ensure receipt is centered and fits modal width
      const injected = body.querySelector('.receipt');
      if(injected){ injected.style.margin='0 auto'; injected.style.flex='none'; }
      // render barcode inside modal (JsBarcode doesn't run on injected HTML)
      setTimeout(()=>{
        const svg=body.querySelector('#receiptBarcode');
        if(svg && window.JsBarcode && receiptNo){
          try{ JsBarcode(svg, receiptNo, {format:"CODE128", width:1.4, height:36, displayValue:false, margin:6}); }catch(e){}
        }
      }, 50);
    } else {
      body.innerHTML='<iframe src="'+url+'" style="width:100%;height:60vh;border:none;background:#fff"></iframe>';
      body.dataset.fullHtml=html;
    }
  }catch(e){
    body.innerHTML='<div style="padding:16px"><a href="'+url+'" target="_blank" class="btn btn-primary">Open Receipt</a><div style="margin-top:8px;font-size:12px;color:var(--ink-soft)">'+e.message+'</div></div>';
  }
}
function closeReceipt(){
  const m=document.getElementById('receiptModal');
  if(m){ m.classList.remove('show'); m.style.display='none'; }
}
function printReceipt(){
  const body=document.getElementById('receiptBody');
  const html=body.dataset.fullHtml;
  if(html){
    const w=window.open('','_blank','width=380,height=600');
    w.document.write(html);
    w.document.close();
    setTimeout(()=>w.print(),400);
  } else {
    const receiptEl=body.querySelector('.receipt');
    if(receiptEl){
      const w=window.open('','_blank','width=380,height=600');
      w.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Receipt 80mm</title><style>@page{size:80mm auto;margin:4mm}body{margin:0;font-family:monospace;font-size:11px}</style></head><body>'+receiptEl.outerHTML+'<script>setTimeout(()=>window.print(),400)<\/script></body></html>');
      w.document.close();
    } else if(lastReceiptUrl){
      window.open(lastReceiptUrl+'?autoprint=1','_blank','width=380,height=600');
    }
  }
}
async function downloadReceiptPDF(){
  const receiptEl=document.getElementById('receiptBody')?.querySelector('.receipt');
  const target=receiptEl || document.getElementById('receiptBody');
  if(!target){ window.print(); return; }
  const btn=event?.target?.closest('button');
  const orig=btn?btn.innerHTML:'';
  if(btn) btn.innerHTML='Generating…';
  try{
    const canvas=await html2canvas(target,{scale:2,backgroundColor:'#ffffff',useCORS:true});
    const imgData=canvas.toDataURL('image/png');
    const {jsPDF}=window.jspdf;
    const pdfW=80;
    const pdfH=(canvas.height * pdfW)/canvas.width;
    const pdf=new jsPDF({orientation:'portrait',unit:'mm',format:[pdfW,pdfH]});
    pdf.addImage(imgData,'PNG',0,0,pdfW,pdfH);
    const fileName=(lastReceiptUrl?.split('/').pop()?.split('?')[0] || 'receipt')+'.pdf';
    pdf.save(fileName);
  }catch(e){ window.print(); } finally{ if(btn) btn.innerHTML=orig; }
}
function openReceiptNewWindow(){ if(lastReceiptUrl) window.open(lastReceiptUrl,'_blank','width=380,height=600'); }
document.addEventListener('keydown',e=>{ if(e.key==='Escape') closeReceipt(); });
</script>
<style>@media(max-width:900px){ div[style*="grid-template-columns:1.2fr"]{grid-template-columns:1fr !important} }</style>
@endsection
