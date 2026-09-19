@extends('layouts.admin')
@section('title','Edit Unit')
@section('content')
<div class="page-head">
    <div><h1>Edit Unit</h1><p class="page-sub">{{ $unit->name }}</p></div>
    <a href="{{ route('units.index') }}" class="btn btn-ghost btn-sm">Back</a>
</div>
<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('units.update', encId($unit->id)) }}">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Unit Name *</label>
                    <input name="name" value="{{ old('name',$unit->name) }}" required>
                </div>
                <div class="field">
                    <label class="field-label">Short Name</label>
                    <input name="short_name" value="{{ old('short_name',$unit->short_name) }}">
                </div>
                <div class="field">
                    <label class="check-row"><input type="checkbox" name="is_active" value="1" {{ old('is_active',$unit->is_active) ? 'checked' : '' }}> Active</label>
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('units.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update Unit</button>
            </div>
        </form>
    </div>
</div>
@endsection
