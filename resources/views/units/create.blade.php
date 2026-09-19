@extends('layouts.admin')
@section('title','Add Unit')
@section('content')
<div class="page-head">
    <div><h1>Add Unit</h1><p class="page-sub">Example: Piece, Bottle, Kg, Liter</p></div>
    <a href="{{ route('units.index') }}" class="btn btn-ghost btn-sm">Back</a>
</div>
<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('units.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field @error('name') err @enderror">
                    <label class="field-label">Unit Name *</label>
                    <input name="name" value="{{ old('name') }}" required placeholder="e.g. Piece">
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Short Name</label>
                    <input name="short_name" value="{{ old('short_name') }}" placeholder="e.g. pc, kg, l">
                </div>
                <div class="field">
                    <label class="check-row"><input type="checkbox" name="is_active" value="1" {{ old('is_active',true) ? 'checked' : '' }}> Active</label>
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('units.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Save Unit</button>
            </div>
        </form>
    </div>
</div>
@endsection
