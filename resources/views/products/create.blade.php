@extends('layouts.admin')
@section('title','Add Product')
@section('content')
<div class="page-head">
    <div><h1>Add Product</h1><p class="page-sub">SKU = internal code · Barcode = scanned code · Barcode unique guard</p></div>
    <a href="{{ route('products.index') }}" class="btn btn-ghost btn-sm">Back to Products</a>
</div>

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('products.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field @error('name') err @enderror">
                    <label class="field-label">Product Name *</label>
                    <input name="name" value="{{ old('name') }}" required placeholder="e.g. Coca Cola 500ml">
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('sku') err @enderror">
                    <label class="field-label">SKU * <span style="text-transform:none;font-weight:600;color:var(--ink-soft)">internal</span></label>
                    <input name="sku" value="{{ old('sku') }}" required placeholder="e.g. COCA-500">
                    @error('sku')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('barcode') err @enderror">
                    <label class="field-label">Barcode <span style="text-transform:none;font-weight:600;color:var(--ink-soft)">scanned - leave empty if none</span></label>
                    <input name="barcode" value="{{ old('barcode') }}" placeholder="e.g. 1234567890123 or 2510VFD090007">
                    @error('barcode')<span class="field-err">{{ $message }}</span>@enderror
                    <span class="field-hint">Supports EAN-13, EAN-8, UPC, Code128, Code39, QR, Internal. Duplicate blocked.</span>
                </div>
                <div class="field @error('category_id') err @enderror">
                    <label class="field-label">Category</label>
                    <select name="category_id">
                        <option value="">— None —</option>
                        @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id')==$c->id)>{{ $c->name }}</option>@endforeach
                    </select>
                    @error('category_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('brand_id') err @enderror">
                    <label class="field-label">Brand</label>
                    <select name="brand_id">
                        <option value="">— None —</option>
                        @foreach($brands as $b)<option value="{{ $b->id }}" @selected(old('brand_id')==$b->id)>{{ $b->name }}</option>@endforeach
                    </select>
                </div>
                <div class="field @error('unit_id') err @enderror">
                    <label class="field-label">Unit</label>
                    <select name="unit_id">
                        <option value="">— None —</option>
                        @foreach($units as $u)<option value="{{ $u->id }}" @selected(old('unit_id')==$u->id)>{{ $u->name }} @if($u->short_name) ({{ $u->short_name }}) @endif</option>@endforeach
                    </select>
                </div>
                <div class="field @error('supplier_id') err @enderror">
                    <label class="field-label">Supplier</label>
                    <select name="supplier_id">
                        <option value="">— None —</option>
                        @foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id')==$s->id)>{{ $s->name }}</option>@endforeach
                    </select>
                </div>
                <div class="field @error('buying_price') err @enderror">
                    <label class="field-label">Buying Price *</label>
                    <input name="buying_price" type="number" step="0.01" value="{{ old('buying_price',0) }}" required>
                    @error('buying_price')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('selling_price') err @enderror">
                    <label class="field-label">Selling Price *</label>
                    <input name="selling_price" type="number" step="0.01" value="{{ old('selling_price',0) }}" required>
                    @error('selling_price')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('wholesale_price') err @enderror">
                    <label class="field-label">Wholesale Price</label>
                    <input name="wholesale_price" type="number" step="0.01" value="{{ old('wholesale_price') }}" placeholder="optional">
                </div>
                <div class="field @error('current_stock') err @enderror">
                    <label class="field-label">Current Stock *</label>
                    <input name="current_stock" type="number" value="{{ old('current_stock',0) }}" required>
                    @error('current_stock')<span class="field-err">{{ $message }}</span>@enderror
                    <span class="field-hint">Initial stock creates stock movement IN</span>
                </div>
                <div class="field @error('min_stock') err @enderror">
                    <label class="field-label">Minimum Stock *</label>
                    <input name="min_stock" type="number" value="{{ old('min_stock',5) }}" required>
                </div>
                <div class="field @error('tax_rate') err @enderror">
                    <label class="field-label">Tax %</label>
                    <input name="tax_rate" type="number" step="0.01" value="{{ old('tax_rate',0) }}">
                </div>
                <div class="field @error('expiry_date') err @enderror">
                    <label class="field-label">Expiry Date</label>
                    <input name="expiry_date" type="date" value="{{ old('expiry_date') }}">
                </div>
                <div class="field @error('batch_number') err @enderror">
                    <label class="field-label">Batch Number</label>
                    <input name="batch_number" value="{{ old('batch_number') }}" placeholder="optional">
                </div>
                <div class="field @error('status') err @enderror">
                    <label class="field-label">Status *</label>
                    <select name="status" required>
                        <option value="active" @selected(old('status','active')==='active')>Active</option>
                        <option value="inactive" @selected(old('status')==='inactive')>Inactive</option>
                        <option value="discontinued" @selected(old('status')==='discontinued')>Discontinued</option>
                    </select>
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Description</label>
                <textarea name="description" placeholder="optional">{{ old('description') }}</textarea>
            </div>

            <div class="form-actions">
                <a href="{{ route('products.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Save Product</button>
            </div>
        </form>
    </div>
</div>
@endsection
