@extends('layouts.admin')
@section('title','Edit Customer')
@section('content')
<div class="page-head">
    <div><h1>Edit Customer</h1><p class="page-sub">{{ $customer->name }}</p></div>
    <a href="{{ route('customers.index') }}" class="btn btn-ghost btn-sm">Back</a>
</div>
<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('customers.update', encId($customer->id)) }}">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Name *</label>
                    <input name="name" value="{{ old('name',$customer->name) }}" required>
                </div>
                <div class="field">
                    <label class="field-label">Phone</label>
                    <input name="phone" value="{{ old('phone',$customer->phone) }}">
                </div>
                <div class="field">
                    <label class="field-label">Email</label>
                    <input name="email" type="email" value="{{ old('email',$customer->email) }}">
                </div>
                <div class="field">
                    <label class="field-label">Address</label>
                    <input name="address" value="{{ old('address',$customer->address) }}">
                </div>
                <div class="field">
                    <label class="field-label">Type *</label>
                    <select name="type" required>
                        <option value="walk_in" @selected(old('type',$customer->type)==='walk_in')>Walk-in</option>
                        <option value="registered" @selected(old('type',$customer->type)==='registered')>Registered</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('customers.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
