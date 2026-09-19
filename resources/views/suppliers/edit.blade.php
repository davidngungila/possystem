@extends('layouts.admin')
@section('title','Edit Supplier')
@section('content')
<div class="page-head">
    <div><h1>Edit Supplier</h1><p class="page-sub">{{ $supplier->name }}</p></div>
    <a href="{{ route('suppliers.index') }}" class="btn btn-ghost btn-sm">Back</a>
</div>
<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('suppliers.update', encId($supplier->id)) }}">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Supplier Name *</label>
                    <input name="name" value="{{ old('name',$supplier->name) }}" required>
                </div>
                <div class="field">
                    <label class="field-label">Phone</label>
                    <input name="phone" value="{{ old('phone',$supplier->phone) }}">
                </div>
                <div class="field">
                    <label class="field-label">Email</label>
                    <input name="email" type="email" value="{{ old('email',$supplier->email) }}">
                </div>
                <div class="field">
                    <label class="field-label">TIN / VAT</label>
                    <input name="tin" value="{{ old('tin',$supplier->tin) }}">
                </div>
                <div class="field">
                    <label class="field-label">Contact Person</label>
                    <input name="contact_person" value="{{ old('contact_person',$supplier->contact_person) }}">
                </div>
                <div class="field">
                    <label class="field-label">Address</label>
                    <input name="address" value="{{ old('address',$supplier->address) }}">
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('suppliers.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update Supplier</button>
            </div>
        </form>
    </div>
</div>
@endsection
