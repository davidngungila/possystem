@extends('layouts.admin')
@section('title','Add Category')
@section('content')
<div class="page-head">
    <div><h1>Add Category</h1><p class="page-sub">Example: Groceries, Beverages, Fresh Food</p></div>
    <a href="{{ route('categories.index') }}" class="btn btn-ghost btn-sm">Back</a>
</div>
<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field @error('name') err @enderror">
                    <label class="field-label">Category Name *</label>
                    <input name="name" value="{{ old('name') }}" required placeholder="e.g. Groceries">
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
                <a href="{{ route('categories.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Save Category</button>
            </div>
        </form>
    </div>
</div>
@endsection
