@extends('layouts.admin')
@section('title','New Purchase')
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">New Purchase <span class="info-icon" tabindex="0">i<span class="tooltip">Supplier → Invoice → Products → Quantity × Buying Price → Received → Stock IN</span></span></h1><p class="page-sub">Create purchase</p></div>
    <a href="{{ route('purchases.index') }}" class="btn btn-ghost btn-sm">← Back to Purchases</a>
</div>

<div class="panel">
    <div class="panel-head"><div class="panel-title">Purchase Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
            @csrf
            <div class="form-grid">
                <div class="field @error('supplier_id') err @enderror">
                    <label class="field-label">Supplier *</label>
                    <select name="supplier_id" required>
                        <option value="">— Select supplier —</option>
                        @foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id')==$s->id)>{{ $s->name }}</option>@endforeach
                    </select>
                    @error('supplier_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Invoice Number</label>
                    <input name="invoice_number" value="{{ old('invoice_number') }}" placeholder="optional">
                </div>
                <div class="field">
                    <label class="field-label">Purchase Date</label>
                    <input name="purchase_date" type="date" value="{{ old('purchase_date', date('Y-m-d')) }}">
                </div>
                <div class="field @error('status') err @enderror">
                    <label class="field-label">Status *</label>
                    <select name="status" required>
                        <option value="draft" @selected(old('status')==='draft')>Draft — no stock</option>
                        <option value="ordered" @selected(old('status')==='ordered')>Ordered — no stock</option>
                        <option value="received" @selected(old('status','received')==='received')>Received — Stock IN</option>
                        <option value="partially_received" @selected(old('status')==='partially_received')>Partially Received — Stock IN</option>
                        <option value="cancelled" @selected(old('status')==='cancelled')>Cancelled</option>
                    </select>
                    <span class="field-hint">Stock IN only when received/partially_received</span>
                </div>
                <div class="field">
                    <label class="field-label">Batch Number <span style="text-transform:none;font-weight:600;color:var(--ink-soft)">per purchase</span></label>
                    <input name="batch_number" value="{{ old('batch_number') ?? $autoBatchNumber }}" readonly>
                    <span class="field-hint">Auto-generated, read-only</span>
                </div>
                <div class="field">
                    <label class="field-label">Expiry Date <span style="text-transform:none;font-weight:600;color:var(--ink-soft)">per purchase</span></label>
                    <input name="expiry_date" type="date" value="{{ old('expiry_date') }}">
                    <span class="field-hint">Single expiry for this batch</span>
                </div>
            </div>

            <div class="panel-head" style="margin:22px -18px -18px;border-radius:0;border-bottom:none;border-top:1px solid var(--line)">
                <div class="panel-title">Items</div>
                <button type="button" class="btn btn-ghost btn-sm" onclick="addRow()">Add Item</button>
            </div>
            <div class="table-card" style="border-top:none;border-top-left-radius:0;border-top-right-radius:0">
                <div class="table-scroll">
                    <table style="table-layout:fixed;min-width:760px">
                        <thead>
                            <tr><th style="width:40%">Product</th><th style="width:9%">Qty</th><th style="width:16%">Buying Price</th><th style="width:16%">Selling Price</th><th class="right" style="width:13%">Total</th><th style="width:6%"></th></tr>
                        </thead>
                        <tbody id="itemsBody">
                            <tr>
                                <td>
                                    <select name="items[0][product_id]" required style="width:100%;padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:13px">
                                        <option value="">— Select product —</option>
                                        @foreach($products as $pr)<option value="{{ $pr->id }}" data-selling="{{ $pr->selling_price }}">{{ $pr->name }} — {{ $pr->sku }} (Stock {{ $pr->current_stock }})</option>@endforeach
                                    </select>
                                </td>
                                <td><input name="items[0][quantity]" type="number" value="1" min="1" required style="width:100%;padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:13px" oninput="calcTotals()"></td>
                                <td><input name="items[0][buying_price]" type="number" step="0.01" value="0" required style="width:100%;padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:13px" oninput="calcTotals()"></td>
                                <td><input name="items[0][selling_price]" type="number" step="0.01" value="0" required style="width:100%;padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:13px" placeholder="Enter selling price"></td>
                                <td class="right"><span class="row-total">0</span></td>
                                <td><button type="button" class="btn-icon danger" onclick="this.closest('tr').remove();calcTotals()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-pagination">
                    <div class="pager-info">Single batch per purchase — same expiry for all items</div>
                    <strong>Total: TZS <span id="grandTotal">0</span></strong>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('purchases.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Purchase</button>
            </div>
        </form>
    </div>
</div>

<script>
let rowIdx=1;
function addRow(){
    const tbody=document.getElementById('itemsBody');
    const tr=document.createElement('tr');
    tr.innerHTML = `
        <td><select name="items[${rowIdx}][product_id]" required style="width:100%;padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:13px"><option value="">— Select product —</option>@foreach($products as $pr)<option value="{{ $pr->id }}" data-selling="{{ $pr->selling_price }}">{{ $pr->name }} — {{ $pr->sku }} (Stock {{ $pr->current_stock }})</option>@endforeach</select></td>
        <td><input name="items[${rowIdx}][quantity]" type="number" value="1" min="1" required style="width:100%;padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:13px" oninput="calcTotals()"></td>
        <td><input name="items[${rowIdx}][buying_price]" type="number" step="0.01" value="0" required style="width:100%;padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:13px" oninput="calcTotals()"></td>
        <td><input name="items[${rowIdx}][selling_price]" type="number" step="0.01" value="0" required style="width:100%;padding:8px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:13px" placeholder="Enter selling price"></td>
        <td class="right"><span class="row-total">0</span></td>
        <td><button type="button" class="btn-icon danger" onclick="this.closest('tr').remove();calcTotals()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></td>
    `;
    tbody.appendChild(tr);
    rowIdx++;
}
function calcTotals(){
    let grand=0;
    document.querySelectorAll('#itemsBody tr').forEach(tr=>{
        const qty=parseFloat(tr.querySelector('input[name*="[quantity]"]')?.value)||0;
        const price=parseFloat(tr.querySelector('input[name*="[buying_price]"]')?.value)||0;
        const total=qty*price;
        const el=tr.querySelector('.row-total');
        if(el) el.textContent=total.toLocaleString();
        grand+=total;
    });
    document.getElementById('grandTotal').textContent=grand.toLocaleString();
}
document.addEventListener('input', e=>{ if(e.target.matches('input[name*="[quantity]"], input[name*="[buying_price]"]')) calcTotals(); });
calcTotals();
</script>
@endsection