@extends('layouts.admin')
@section('title','Edit Expense')
@section('content')
<div class="page-head">
    <div><h1>Edit Expense</h1><p class="page-sub">{{ $expense->category }} — TZS {{ number_format($expense->amount,0) }}</p></div>
    <a href="{{ route('expenses.index') }}" class="btn btn-ghost btn-sm">Back</a>
</div>
<div class="panel">
    <div class="panel-body">
        <form method="POST" action="{{ route('expenses.update', encId($expense->id)) }}">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Category *</label>
                    <select name="category" required>
                        @foreach(['Rent','Electricity','Water','Internet','Transport','Salary','Fuel','Repairs','Packaging','Other'] as $cat)
                            <option value="{{ $cat }}" @selected(old('category',$expense->category)==$cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label class="field-label">Amount *</label>
                    <input name="amount" type="number" step="0.01" value="{{ old('amount',$expense->amount) }}" required>
                </div>
                <div class="field">
                    <label class="field-label">Date</label>
                    <input name="expense_date" type="date" value="{{ old('expense_date', $expense->expense_date ? $expense->expense_date->format('Y-m-d') : '') }}">
                </div>
                <div class="field">
                    <label class="field-label">Description</label>
                    <input name="description" value="{{ old('description',$expense->description) }}">
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('expenses.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update Expense</button>
            </div>
        </form>
    </div>
</div>
@endsection
