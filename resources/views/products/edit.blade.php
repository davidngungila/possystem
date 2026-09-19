@extends('layouts.admin')
@section('title','Edit Product')
@section('content')
<div class="page-head">
    <div><h1>Edit Product</h1><p class="page-sub">{{ $product->name }} — SKU {{ $product->sku }}</p></div>
    <a href="{{ route('products.index') }}" class="btn btn-ghost btn-sm">Back</a>
</div>

<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('products.update', encId($product->id)) }}">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field @error('name') err @enderror">
                    <label class="field-label">Product Name *</label>
                    <input name="name" value="{{ old('name',$product->name) }}" required>
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('sku') err @enderror">
                    <label class="field-label">SKU *</label>
                    <input name="sku" value="{{ old('sku',$product->sku) }}" required>
                    @error('sku')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('barcode') err @enderror">
                    <label class="field-label">Barcode</label>
                    <input name="barcode" value="{{ old('barcode',$product->barcode) }}" placeholder="leave empty if none">
                    @error('barcode')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('category_id') err @enderror">
                    <label class="field-label">Category</label>
                    <select name="category_id">
                        <option value="">— None —</option>
                        @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id',$product->category_id)==$c->id)>{{ $c->name }}</option>@endforeach
                    </select>
                    @error('category_id')<span class="field-err">{{ $message }}</span>@enderror
                    @if(isset($currentShop) && $currentShop && !empty($currentShop->shopTypesArray()))
                        <span class="field-hint">Filtered for {{ $currentShop->shopTypeLabel() }} — {{ $categories->count() }} of {{ \App\Models\Category::count() }} categories</span>
                    @else
                        <span class="field-hint">All categories — assign categories to shop types via Categories → Edit</span>
                    @endif
                </div>
                <div class="field">
                    <label class="field-label">Brand</label>
                    <select name="brand_id">
                        <option value="">— None —</option>
                        @foreach($brands as $b)<option value="{{ $b->id }}" @selected(old('brand_id',$product->brand_id)==$b->id)>{{ $b->name }}</option>@endforeach
                    </select>
                </div>
                <div class="field">
                    <label class="field-label">Unit</label>
                    <select name="unit_id">
                        <option value="">— None —</option>
                        @foreach($units as $u)<option value="{{ $u->id }}" @selected(old('unit_id',$product->unit_id)==$u->id)>{{ $u->name }}</option>@endforeach
                    </select>
                </div>
                <div class="field">
                    <label class="field-label">Supplier</label>
                    <select name="supplier_id">
                        <option value="">— None —</option>
                        @foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id',$product->supplier_id)==$s->id)>{{ $s->name }}</option>@endforeach
                    </select>
                </div>
                <div class="field @error('buying_price') err @enderror">
                    <label class="field-label">Buying Price *</label>
                    <input name="buying_price" type="number" step="0.01" value="{{ old('buying_price',$product->buying_price) }}" required>
                    @error('buying_price')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('selling_price') err @enderror">
                    <label class="field-label">Selling Price *</label>
                    <input name="selling_price" type="number" step="0.01" value="{{ old('selling_price',$product->selling_price) }}" required>
                    @error('selling_price')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Wholesale Price</label>
                    <input name="wholesale_price" type="number" step="0.01" value="{{ old('wholesale_price',$product->wholesale_price) }}">
                </div>
                <div class="field @error('current_stock') err @enderror">
                    <label class="field-label">Current Stock *</label>
                    <input name="current_stock" type="number" value="{{ old('current_stock',$product->current_stock) }}" required>
                    @error('current_stock')<span class="field-err">{{ $message }}</span>@enderror
                    <span class="field-hint">Changing creates stock movement & audit</span>
                </div>
                <div class="field">
                    <label class="field-label">Minimum Stock *</label>
                    <input name="min_stock" type="number" value="{{ old('min_stock',$product->min_stock) }}" required>
                </div>
                <div class="field">
                    <label class="field-label">Tax %</label>
                    <input name="tax_rate" type="number" step="0.01" value="{{ old('tax_rate',$product->tax_rate) }}">
                </div>
                <div class="field">
                    <label class="field-label">Expiry Date</label>
                    <input name="expiry_date" type="date" value="{{ old('expiry_date', $product->expiry_date? $product->expiry_date->format('Y-m-d') : '') }}">
                </div>
                <div class="field">
                    <label class="field-label">Batch Number</label>
                    <input name="batch_number" value="{{ old('batch_number',$product->batch_number) }}">
                </div>
                <div class="field">
                    <label class="field-label">Status *</label>
                    <select name="status" required>
                        <option value="active" @selected(old('status',$product->status)==='active')>Active</option>
                        <option value="inactive" @selected(old('status',$product->status)==='inactive')>Inactive</option>
                        <option value="discontinued" @selected(old('status',$product->status)==='discontinued')>Discontinued</option>
                    </select>
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Description</label>
                <textarea name="description">{{ old('description',$product->description) }}</textarea>
            </div>
            <div class="form-actions">
                <a href="{{ route('products.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update Product</button>
            </div>
        </form>
    </div>
</div>
@endsection
