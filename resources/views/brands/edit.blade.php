@extends('layouts.admin')
@section('title','Edit Brand')
@section('content')
<div class="page-head">
    <div><h1>Edit Brand</h1><p class="page-sub">{{ $brand->name }}</p></div>
    <a href="{{ route('brands.index') }}" class="btn btn-ghost btn-sm">Back</a>
</div>
<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('brands.update', encId($brand->id)) }}">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Brand Name *</label>
                    <input name="name" value="{{ old('name',$brand->name) }}" required>
                </div>
                <div class="field">
                    <label class="field-label">Description</label>
                    <input name="description" value="{{ old('description',$brand->description) }}">
                </div>
                <div class="field">
                    <label class="check-row"><input type="checkbox" name="is_active" value="1" {{ old('is_active',$brand->is_active) ? 'checked' : '' }}> Active</label>
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('brands.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update Brand</button>
            </div>
        </form>
    </div>
</div>
@endsection
