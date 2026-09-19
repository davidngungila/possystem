@extends('layouts.admin')
@section('title', isset($proforma) ? 'Edit Proforma Invoice' : 'New Proforma Invoice')
@section('content')
@php
$pfItems = isset($proforma) ? $proforma->items : collect();
if (!$pfItems->count() && isset($sourceQt) && $sourceQt) $pfItems = $sourceQt->items;
$initialItems = $pfItems->map(fn($i)=>['product_id'=>$i->product_id,'description'=>$i->description,'quantity'=>$i->quantity,'unit_price'=>$i->unit_price,'discount'=>$i->discount,'total'=>$i->total])->values()->toArray();
@endphp
<div class="page-head">
    <div><h1>{{ isset($proforma) ? 'Edit Proforma Invoice' : 'New Proforma Invoice' }}</h1><p class="page-sub">{{ $number }} · Provisional invoice before final sale</p></div>
    <a href="{{ route('proforma-invoices.index') }}" class="btn btn-ghost btn-sm">← Back to Proformas</a>
</div>

<div class="panel">
    <div class="panel-head"><div class="panel-title">Proforma Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ isset($proforma) ? route('proforma-invoices.update', encId($proforma->id)) : route('proforma-invoices.store') }}" id="pfForm" novalidate>
            @csrf
            @isset($proforma) @method('PUT') @endisset
            <div class="form-grid">
                <div class="field @error('customer_id') err @enderror">
                    <label class="field-label">Customer</label>
                    <select name="customer_id">
                        <option value="">— Walk-in / select customer —</option>
                        @foreach($customers as $c)<option value="{{ $c->id }}" @selected(old('customer_id', ($proforma->customer_id ?? $sourceQt->customer_id ?? '')) == $c->id)>{{ $c->name }} @if($c->phone)({{ $c->phone }})@endif</option>@endforeach
                    </select>
                    @error('customer_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Quote Reference</label>
                    <select name="quotation_id">
                        <option value="">— none —</option>
                        @foreach($quotations as $qt)<option value="{{ $qt->id }}" @selected(old('quotation_id', $proforma->quotation_id ?? '') == $qt->id)>{{ $qt->quotation_number }} @if($qt->customer)({{ $qt->customer->name }})@endif</option>@endforeach
                    </select>
                </div>
                <div class="field @error('date') err @enderror">
                    <label class="field-label">Date *</label>
                    <input name="date" type="date" value="{{ old('date', $proforma->date ?? date('Y-m-d')) }}" required>
                    @error('date')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Valid Until</label>
                    <input name="valid_until" type="date" value="{{ old('valid_until', $proforma->valid_until ?? '') }}">
                </div>
                <div class="field">
                    <label class="field-label">Due Date</label>
                    <input name="due_date" type="date" value="{{ old('due_date', $proforma->due_date ?? '') }}">
                </div>
                <div class="field @error('status') err @enderror">
                    <label class="field-label">Status *</label>
                    <select name="status" required>
                        @foreach($statuses as $st)<option value="{{ $st }}" @selected(old('status', $proforma->status ?? 'draft') == $st)>{{ ucfirst(str_replace('_',' ',$st)) }}</option>@endforeach
                    </select>
                    @error('status')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Payment Terms</label>
                    <input name="payment_terms" value="{{ old('payment_terms', $proforma->payment_terms ?? 'Due on receipt') }}" placeholder="e.g. 50% advance, balance on delivery">
                </div>
                <div class="field">
                    <label class="field-label">Salesperson</label>
                    <select name="salesperson_id">
                        <option value="">— none —</option>
                        @foreach($salespeople as $u)<option value="{{ $u->id }}" @selected(old('salesperson_id', $proforma->salesperson_id ?? '') == $u->id)>{{ $u->name }}</option>@endforeach
                    </select>
                </div>
                <div class="field" style="grid-column:span 2">
                    <label class="field-label">Bank / Mobile-money Payment Details</label>
                    <textarea name="bank_details" rows="2" placeholder="Bank: NMB 1234567890 · Mobile: +255 7xx xxx xxx (M-Pesa)">{{ old('bank_details', $proforma->bank_details ?? '') }}</textarea>
                </div>
            </div>

            <div class="panel-head" style="margin:22px -18px -18px;border-radius:0;border-bottom:none;border-top:1px solid var(--line)">
                <div class="panel-title">Items</div>
                <button type="button" class="btn btn-ghost btn-sm" onclick="addPFRow()">Add Item</button>
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
                    <div class="pager-info">Pick a product to auto-fill unit price</div>
                    <strong>Subtotal: TZS <span id="subtotal">0</span></strong>
                </div>
            </div>

            <div class="form-grid" style="margin-top:20px">
                <div class="field">
                    <label class="field-label">Discount Type</label>
                    <select name="discount_type" id="discType" onchange="calcTotals()">
                        <option value="none" @selected(old('discount_type',$proforma->discount_type ?? 'none')==='none')>No Discount</option>
                        <option value="fixed" @selected(old('discount_type',$proforma->discount_type ?? '')==='fixed')>Fixed (TZS)</option>
                        <option value="percent" @selected(old('discount_type',$proforma->discount_type ?? '')==='percent')>Percent (%)</option>
                    </select>
                </div>
                <div class="field">
                    <label class="field-label">Discount Value</label>
                    <input name="discount_value" type="number" step="0.01" min="0" id="discValue" value="{{ old('discount_value', $proforma->discount_value ?? 0) }}" oninput="calcTotals()">
                </div>
                <div class="field">
                    <label class="field-label">Tax / VAT (%)</label>
                    <input name="tax_rate" type="number" step="0.01" min="0" max="100" id="taxRate" value="{{ old('tax_rate', $proforma->tax_rate ?? 0) }}" oninput="calcTotals()">
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
                    <textarea name="notes" rows="3">{{ old('notes', $proforma->notes ?? '') }}</textarea>
                </div>
                <div class="field">
                    <label class="field-label">Terms & Conditions</label>
                    <textarea name="terms" rows="3">{{ old('terms', $proforma->terms ?? '') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('proforma-invoices.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Proforma</button>
            </div>
        </form>
    </div>
</div>

<script>
const products = [
@foreach($products as $pr){id:{{ $pr->id }},name:@js($pr->name),sku:@js($pr->sku),price:{{ $pr->selling_price }}},
@endforeach
];
let rowIdx = 2000;
const initialItems = @json($initialItems);
function addPFRow(data){
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
if(initialItems.length){ initialItems.forEach(addPFRow); } else { addPFRow(); }
calcTotals();
</script>
@endsection