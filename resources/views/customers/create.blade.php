@extends('layouts.admin')
@section('title','Add Customer')
@section('content')
<div class="page-head">
    <div><h1>Add Customer</h1><p class="page-sub">Registered customers can buy on credit</p></div>
    <a href="{{ route('customers.index') }}" class="btn btn-ghost btn-sm">Back</a>
</div>
<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field @error('name') err @enderror">
                    <label class="field-label">Name *</label>
                    <input name="name" value="{{ old('name') }}" required placeholder="e.g. John Doe">
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
                    <label class="field-label">Address</label>
                    <input name="address" value="{{ old('address') }}" placeholder="optional">
                </div>
                <div class="field">
                    <label class="field-label">Type *</label>
                    <select name="type" required>
                        <option value="walk_in" @selected(old('type')==='walk_in')>Walk-in</option>
                        <option value="registered" @selected(old('type')==='registered')>Registered</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('customers.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Save Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
