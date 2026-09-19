@extends('layouts.admin')
@section('title','Edit Category')
@section('content')
<div class="page-head">
    <div><h1>Edit Category</h1><p class="page-sub">{{ $category->name }}</p></div>
    <a href="{{ route('categories.index') }}" class="btn btn-ghost btn-sm">Back</a>
</div>
<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('categories.update', encId($category->id)) }}">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Category Name *</label>
                    <input name="name" value="{{ old('name',$category->name) }}" required>
                </div>
                <div class="field">
                    <label class="field-label">Description</label>
                    <input name="description" value="{{ old('description',$category->description) }}">
                </div>
                <div class="field" style="grid-column:1/-1">
                    <label class="field-label">Assigned Shop Types <span style="font-weight:400;color:var(--ink-soft)">— leave empty for all shops</span></label>
                    <select name="shop_types[]" multiple size="8" style="width:100%;padding:8px;border:1.5px solid var(--line);border-radius:10px;background:#fff;min-height:130px">
                        @php $oldTypes = old('shop_types', $category->shopTypesArray()); @endphp
                        @foreach(\App\Models\Shop::types() as $key => $t)
                            <option value="{{ $key }}" @selected(is_array($oldTypes) && in_array($key, $oldTypes))>{{ $t['label'] }} — {{ $t['examples'] }}</option>
                        @endforeach
                    </select>
                    <span class="field-hint">Hold Ctrl/Cmd to select multiple. Empty = visible for all.</span>
                    @error('shop_types')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="check-row"><input type="checkbox" name="is_active" value="1" {{ old('is_active',$category->is_active) ? 'checked' : '' }}> Active</label>
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('categories.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update Category</button>
            </div>
        </form>
    </div>
</div>
@endsection
