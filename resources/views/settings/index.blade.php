@extends('layouts.admin')
@section('title','Settings')
@section('content')
<div class="page-head">
    <div><h1>Settings</h1><p class="page-sub">Shop info · POS rules · Receipt & hardware — persisted in settings table</p></div>
    <span class="tag tag-grey">Live</span>
</div>

@if(session('success'))<div style="background:var(--acacia-100);border:1px solid #c8d7a8;color:var(--acacia-600);padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;">{{ session('success') }}</div>@endif

<form method="POST" action="{{ route('settings.update') }}">
    @csrf
    <div class="panel" style="margin-bottom:18px">
        <div class="panel-head"><div class="panel-title">Shop Information</div></div>
        <div class="panel-body">
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Shop Name *</label>
                    <input name="shop_name" value="{{ old('shop_name', \App\Models\Setting::getValue('shop_name', \App\Models\Setting::getValue('university_name','SHOP POS'))) }}" required>
                </div>
                <div class="field">
                    <label class="field-label">Acronym</label>
                    <input name="shop_acronym" value="{{ old('shop_acronym', \App\Models\Setting::getValue('shop_acronym', \App\Models\Setting::getValue('university_acronym','SP'))) }}" maxlength="6">
                </div>
                <div class="field">
                    <label class="field-label">Phone</label>
                    <input name="shop_phone" value="{{ old('shop_phone', \App\Models\Setting::getValue('shop_phone', \App\Models\Setting::getValue('admissions_phone',''))) }}">
                </div>
                <div class="field">
                    <label class="field-label">Email</label>
                    <input name="shop_email" type="email" value="{{ old('shop_email', \App\Models\Setting::getValue('shop_email', \App\Models\Setting::getValue('admissions_email',''))) }}">
                </div>
                <div class="field" style="grid-column:1/-1">
                    <label class="field-label">Address</label>
                    <input name="shop_address" value="{{ old('shop_address', \App\Models\Setting::getValue('shop_address','')) }}" placeholder="Street, City">
                </div>
                <div class="field" style="grid-column:1/-1">
                    <label class="field-label">Receipt Footer</label>
                    <textarea name="receipt_footer" rows="2" placeholder="Thank you — visit again">{{ old('receipt_footer', \App\Models\Setting::getValue('receipt_footer','Thank you — welcome again!')) }}</textarea>
                    <span class="field-hint">Printed on 80mm thermal receipt</span>
                </div>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-bottom:18px">
        <div class="panel-head"><div class="panel-title">POS Rules</div></div>
        <div class="panel-body">
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Currency</label>
                    <input name="currency" value="{{ old('currency', \App\Models\Setting::getValue('currency','TZS')) }}">
                </div>
                <div class="field">
                    <label class="field-label">Default Tax %</label>
                    <input name="default_tax" type="number" step="0.01" value="{{ old('default_tax', \App\Models\Setting::getValue('default_tax','0')) }}">
                </div>
                <div class="field">
                    <label class="check-row" style="margin-top:26px"><input type="checkbox" name="allow_negative_stock" value="1" @checked(old('allow_negative_stock', \App\Models\Setting::getValue('allow_negative_stock','0'))=='1')> Allow negative stock (oversell)</label>
                    <span class="field-hint">When checked POS allows sale even if stock insufficient</span>
                </div>
                <div class="field">
                    <label class="check-row" style="margin-top:26px"><input type="checkbox" name="require_customer" value="1" @checked(old('require_customer', \App\Models\Setting::getValue('require_customer','0'))=='1')> Require customer on sale</label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('dashboard') }}" class="btn btn-ghost">Cancel</a>
        <button class="btn btn-primary">Save Settings</button>
    </div>
</form>
@endsection
