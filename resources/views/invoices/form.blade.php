@extends('layouts.admin')
@section('title', isset($invoice) ? 'Edit Invoice' : 'New Invoice')
@section('content')
@php
$invItems = isset($invoice) ? $invoice->items : collect();
if (!$invItems->count() && ($source ?? null)) $invItems = $presets['items'] ?? collect();
$initialItems = $invItems->map(fn($i)=>['product_id'=>$i->product_id,'description'=>$i->description,'quantity'=>$i->quantity,'unit_price'=>$i->unit_price,'discount'=>$i->discount,'total'=>$i->total])->values()->toArray();
$srcLabel = ($sourceType ?? null) && ($source ?? null)
    ? ucfirst($sourceType).' '.($sourceType==='sale' ? ($source->receipt_number ?? '#'.$source->id) : ($source->quotation_number ?? $source->proforma_number ?? $source->invoice_number ?? '#' . $source->id))
    : null;
@endphp
<div class="page-head">
    <div><h1>{{ isset($invoice) ? 'Edit Invoice' : 'New Invoice' }}</h1><p class="page-sub">{{ $number }} · Final sales document</p></div>
    <a href="{{ route('invoices.index') }}" class="btn btn-ghost btn-sm">← Back to Invoices</a>
</div>

@if($srcLabel)
<div style="margin-bottom:14px;padding:10px 14px;background:var(--sand-100);border-radius:10px;font-size:13px;font-weight:700;color:var(--coffee-900)">Source document: {{ $srcLabel }} — items are carried from the source</div>
@endif

<div class="panel">
    <div class="panel-head"><div class="panel-title">Invoice Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ isset($invoice) ? route('invoices.update', encId($invoice->id)) : route('invoices.store') }}" id="invForm" novalidate>
            @csrf
            @isset($invoice) @method('PUT') @endisset
            @if($sourceType && $source)
                <input type="hidden" name="source_type" value="{{ $sourceType }}">
                <input type="hidden" name="source_id" value="{{ encId($source->id) }}">
            @endif
            <div class="form-grid">
                <div class="field @error('customer_id') err @enderror">
                    <label class="field-label">Customer</label>
                    <select name="customer_id">
                        <option value="">— Walk-in / select customer —</option>
                        @foreach($customers as $c)<option value="{{ $c->id }}" @selected(old('customer_id', ($invoice->customer_id ?? $presets['customer_id'] ?? '')) == $c->id)>{{ $c->name }} @if($c->phone)({{ $c->phone }})@endif</option>@endforeach
                    </select>
                    @error('customer_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('invoice_date') err @enderror">
                    <label class="field-label">Invoice Date *</label>
                    <input name="invoice_date" type="date" value="{{ old('invoice_date', $invoice->invoice_date ?? date('Y-m-d')) }}" required>
                    @error('invoice_date')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Due Date</label>
                    <input name="due_date" type="date" value="{{ old('due_date', $invoice->due_date ?? '') }}">
                </div>
                <div class="field @error('payment_status') err @enderror">
                    <label class="field-label">Payment Status *</label>
                    <select name="payment_status" required>
                        @foreach($paymentStatuses as $ps)<option value="{{ $ps }}" @selected(old('payment_status', $invoice->payment_status ?? (($source && $sourceType==='sale') ? 'paid' : 'unpaid')) == $ps)>{{ ucfirst(str_replace('_',' ',$ps)) }}</option>@endforeach
                    </select>
                    @error('payment_status')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Payment Method</label>
                    <select name="payment_method">
                        <option value="">— select —</option>
                        @foreach(['cash','m-pesa','airtel_money','mixx','halopesa','bank','card','credit'] as $m)
                            <option value="{{ $m }}" @selected(old('payment_method', $invoice->payment_method ?? $presets['payment_method'] ?? '') == $m)>{{ paymentMethodName($m) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label class="field-label">Salesperson</label>
                    <select name="salesperson_id">
                        <option value="">— none —</option>
                        @foreach($salespeople as $u)<option value="{{ $u->id }}" @selected(old('salesperson_id', $invoice->salesperson_id ?? '') == $u->id)>{{ $u->name }}</option>@endforeach
                    </select>
                </div>
                <div class="field">
                    <label class="field-label">Number</label>
                    <input value="{{ $number }}" readonly>
                </div>
            </div>

            <div class="panel-head" style="margin:22px -18px -18px;border-radius:0;border-bottom:none;border-top:1px solid var(--line)">
                <div class="panel-title">Items</div>
                <button type="button" class="btn btn-ghost btn-sm" onclick="addInvRow()" @if($srcLabel)disabled style="opacity:.5" @endif>Add Item</button>
            </div>
            <div class="table-card" style="border-top:none">
                <div class="table-scroll">
                    <table style="table-layout:fixed;min-width:820px">
                        <thead>
                            <tr><th style="width:34%">Product / Description</th><th style="width:12%">Qty</th><th style="width:18%">Unit Price</th><th style="width:18%">Discount</th><th class="right" style="width:14%">Total</th><th style="width:4%"></th></tr>
                        </thead>
                        <tbody id="itemsBody"></tbody>
                    </table>
                </div>
                <div class="table-pagination">
                    <div class="pager-info">
                        @if($srcLabel)
                            Items locked to source; adjust totals below
                        @else
                            Pick a product to auto-fill unit price
                        @endif
                    </div>
                    <strong>Subtotal: TZS <span id="subtotal">0</span></strong>
                </div>
            </div>

            <div class="form-grid" style="margin-top:20px">
                <div class="field">
                    <label class="field-label">Discount Type</label>
                    <select name="discount_type" id="discType" onchange="calcTotals()">
                        <option value="none" @selected(old('discount_type',$invoice->discount_type ?? 'none')==='none')>No Discount</option>
                        <option value="fixed" @selected(old('discount_type',$invoice->discount_type ?? '')==='fixed')>Fixed (TZS)</option>
                        <option value="percent" @selected(old('discount_type',$invoice->discount_type ?? '')==='percent')>Percent (%)</option>
                    </select>
                </div>
                <div class="field">
                    <label class="field-label">Discount Value</label>
                    <input name="discount_value" type="number" step="0.01" min="0" id="discValue" value="{{ old('discount_value', $invoice->discount_value ?? $presets['discount_value'] ?? 0) }}" oninput="calcTotals()">
                </div>
                <div class="field">
                    <label class="field-label">Tax / VAT (%)</label>
                    <input name="tax_rate" type="number" step="0.01" min="0" max="100" id="taxRate" value="{{ old('tax_rate', $invoice->tax_rate ?? $presets['tax_rate'] ?? 0) }}" oninput="calcTotals()">
                </div>
                <div class="field">
                    <label class="field-label">Discount (computed)</label>
                    <input id="discAmt" value="0" readonly>
                </div>
                <div class="field">
                    <label class="field-label">Tax (computed)</label>
                    <input id="taxAmt" value="0" readonly>
                </div>
                <div class="field">
                    <label class="field-label">Grand Total</label>
                    <input id="grandTotal" value="0" readonly>
                </div>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Notes</label>
                    <textarea name="notes" rows="3">{{ old('notes', $invoice->notes ?? '') }}</textarea>
                </div>
                <div class="field">
                    <label class="field-label">Terms & Conditions</label>
                    <textarea name="terms" rows="3">{{ old('terms', $invoice->terms ?? '') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('invoices.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Invoice</button>
            </div>
        </form>
    </div>
</div>

<script>
const products = [
@foreach($products as $pr){id:{{ $pr->id }},name:@js($pr->name),sku:@js($pr->sku),price:{{ $pr->selling_price }}},
@endforeach
];
let rowIdx = 3000;
const initialItems = @json($initialItems);
function addInvRow(data){
    data = data || {product_id:'',description:'',quantity:1,unit_price:0,discount:0,total:0};
    const tbody=document.getElementById('itemsBody');
    const idx=rowIdx++;
    const tr=document.createElement('tr');
    tr.innerHTML = `
        <td><select name="items[${idx}][product_id]" style="width:100%;padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:13px" onchange="onProduct(this)"><option value="">— Select product —</option>${products.map(p=>`<option value="${p.id}" data-price="${p.price}" ${data.product_id==p.id?'selected':''}>${p.name}${p.sku?' ('+p.sku+')':''}</option>`).join('')}</select>
            <input name="items[${idx}][description]" placeholder="Or type custom description" value="${data.description||''}" style="width:100%;margin-top:6px;padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:13px" oninput="calcTotals()"></td>
        <td><input name="items[${idx}][quantity]" type="number" step="0.01" min="0.01" value="${data.quantity}" style="width:100%;padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:13px" oninput="calcTotals()"></td>
        <td><input name="items[${idx}][unit_price]" type="number" step="0.01" min="0" value="${data.unit_price}" style="width:100%;padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:13px" oninput="calcTotals()"></td>
        <td><input name="items[${idx}][discount]" type="number" step="0.01" min="0" value="${data.discount}" style="width:100%;padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:13px" oninput="calcTotals()"></td>
        <td class="right"><span class="row-total">0</span></td>
        <td><button type="button" class="btn-icon danger" onclick="this.closest('tr').remove();calcTotals()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></td>
    `;
    tbody.appendChild(tr);
    calcTotals();
}
function onProduct(sel){
    const opt=sel.selectedOptions[0];
    const tr=sel.closest('tr');
    if(opt && opt.dataset.price!==undefined){
        const price=tr.querySelector('input[name*="[unit_price]"]');
        if(price && !price.value) price.value=opt.dataset.price;
        const desc=tr.querySelector('input[name*="[description]"]');
        const p=products.find(x=>x.id==sel.value);
        if(p && desc) desc.value=p.name+(p.sku?' ('+p.sku+')':'');
    }
    calcTotals();
}
function fmt(n){ return Math.round(n).toLocaleString(); }
function calcTotals(){
    let sub=0;
    document.querySelectorAll('#itemsBody tr').forEach(tr=>{
        const q=parseFloat(tr.querySelector('input[name*="[quantity]"]').value)||0;
        const p=parseFloat(tr.querySelector('input[name*="[unit_price]"]').value)||0;
        const d=parseFloat(tr.querySelector('input[name*="[discount]"]').value)||0;
        const t=q*p-d;
        const el=tr.querySelector('.row-total');
        if(el) el.textContent=fmt(t);
        sub+=t;
    });
    document.getElementById('subtotal').textContent=fmt(sub);
    const type=document.getElementById('discType').value;
    const dv=parseFloat(document.getElementById('discValue').value)||0;
    const taxRate=parseFloat(document.getElementById('taxRate').value)||0;
    let disc=0;
    if(type==='fixed') disc=Math.min(dv,sub);
    if(type==='percent') disc=sub*dv/100;
    const taxable=Math.max(0,sub-disc);
    const tax=taxable*taxRate/100;
    document.getElementById('discAmt').value=fmt(disc);
    document.getElementById('taxAmt').value=fmt(tax);
    document.getElementById('grandTotal').value=fmt(taxable+tax);
}
if(initialItems.length){ initialItems.forEach(addInvRow); } else { addInvRow(); }
calcTotals();
</script>
@endsection