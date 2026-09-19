@extends('layouts.admin')
@section('title','Add Supplier')
@section('content')
<div class="page-head">
    <div><h1>Add Supplier</h1><p class="page-sub">Store supplier details for purchases</p></div>
    <a href="{{ route('suppliers.index') }}" class="btn btn-ghost btn-sm">Back</a>
</div>
<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('suppliers.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field @error('name') err @enderror">
                    <label class="field-label">Supplier Name *</label>
                    <input name="name" value="{{ old('name') }}" required placeholder="e.g. ABC Wholesalers">
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Phone</label>
                    <input name="phone" value="{{ old('phone') }}" placeholder="+255...">
                </div>
                <div class="field">
                    <label class="field-label">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" placeholder="optional">
                </div>
                <div class="field">
                    <label class="field-label">TIN / VAT</label>
                    <input name="tin" value="{{ old('tin') }}" placeholder="optional">
                </div>
                <div class="field">
                    <label class="field-label">Contact Person</label>
                    <input name="contact_person" value="{{ old('contact_person') }}" placeholder="optional">
                </div>
                <div class="field">
                    <label class="field-label">Address</label>
                    <input name="address" value="{{ old('address') }}" placeholder="optional">
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('suppliers.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Save Supplier</button>
            </div>
        </form>
    </div>
</div>
@endsection
