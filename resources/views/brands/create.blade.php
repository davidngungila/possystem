@extends('layouts.admin')
@section('title','Add Brand')
@section('content')
<div class="page-head">
    <div><h1>Add Brand</h1><p class="page-sub">Example: Coca-Cola, Azam, Samsung</p></div>
    <a href="{{ route('brands.index') }}" class="btn btn-ghost btn-sm">Back</a>
</div>
<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('brands.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field @error('name') err @enderror">
                    <label class="field-label">Brand Name *</label>
                    <input name="name" value="{{ old('name') }}" required placeholder="e.g. Coca-Cola">
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Description</label>
                    <input name="description" value="{{ old('description') }}" placeholder="optional">
                </div>
                <div class="field">
                    <label class="check-row"><input type="checkbox" name="is_active" value="1" {{ old('is_active',true) ? 'checked' : '' }}> Active</label>
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('brands.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Save Brand</button>
            </div>
        </form>
    </div>
</div>
@endsection
