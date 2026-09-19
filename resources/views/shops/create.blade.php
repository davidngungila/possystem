@extends('layouts.admin')
@section('title','Add Shop')
@section('content')
<div class="page-head">
    <div><h1>Add Shop</h1><p class="page-sub">Create a new shop — owner will manage all shops</p></div>
    <a href="{{ route('shops.index') }}" class="btn btn-ghost btn-sm">Back to Shops</a>
</div>

<div class="panel">
    <div class="panel-head"><div class="panel-title">Shop Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('shops.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field @error('name') err @enderror">
                    <label class="field-label">Shop Name *</label>
                    <input name="name" value="{{ old('name') }}" required placeholder="e.g. Main Shop, Kariakoo Branch">
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('code') err @enderror">
                    <label class="field-label">Code <span style="text-transform:none;font-weight:600;color:var(--ink-soft)">auto if empty</span></label>
                    <input name="code" value="{{ old('code') }}" placeholder="e.g. MAIN-001">
                    @error('code')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('shop_type') err @enderror">
                    <label class="field-label">Shop Type *</label>
                    <select name="shop_type" required style="width:100%;padding:10px 11px;border:1.5px solid var(--line);border-radius:10px;background:#fff">
                        <option value="">Select shop type...</option>
                        @foreach(\App\Models\Shop::types() as $key => $t)
                            <option value="{{ $key }}" @selected(old('shop_type')==$key)>{{ $t['label'] }} — {{ $t['examples'] }}</option>
                        @endforeach
                    </select>
                    @error('shop_type')<span class="field-err">{{ $message }}</span>@enderror
                    <span class="field-hint">Choose the category that best fits your shop — controls suggested products</span>
                </div>
                <div class="field @error('phone') err @enderror">
                    <label class="field-label">Phone</label>
                    <input name="phone" value="{{ old('phone') }}" placeholder="+255 ...">
                    @error('phone')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('email') err @enderror">
                    <label class="field-label">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" placeholder="shop@example.com">
                    @error('email')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field" style="grid-column:1/-1">
                    <label class="field-label">Address</label>
                    <input name="address" value="{{ old('address') }}" placeholder="Street, City">
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',true))> Active</label>
            </div>
            <div class="form-actions">
                <a href="{{ route('shops.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Save Shop</button>
            </div>
        </form>
    </div>
</div>
@endsection
