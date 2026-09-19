@extends('layouts.admin')
@section('title','Add Expense')
@section('content')
<div class="page-head">
    <div><h1>Add Expense</h1><p class="page-sub">Not a purchase — operating cost</p></div>
    <a href="{{ route('expenses.index') }}" class="btn btn-ghost btn-sm">Back</a>
</div>
<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field @error('category') err @enderror">
                    <label class="field-label">Category *</label>
                    <select name="category" required>
                        <option value="">— Select —</option>
                        @foreach(['Rent','Electricity','Water','Internet','Transport','Salary','Fuel','Repairs','Packaging','Other'] as $cat)
                            <option value="{{ $cat }}" @selected(old('category')==$cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('amount') err @enderror">
                    <label class="field-label">Amount *</label>
                    <input name="amount" type="number" step="0.01" value="{{ old('amount') }}" required placeholder="e.g. 50000">
                    @error('amount')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Date</label>
                    <input name="expense_date" type="date" value="{{ old('expense_date', date('Y-m-d')) }}">
                </div>
                <div class="field">
                    <label class="field-label">Description</label>
                    <input name="description" value="{{ old('description') }}" placeholder="optional">
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('expenses.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Save Expense</button>
            </div>
        </form>
    </div>
</div>
@endsection
