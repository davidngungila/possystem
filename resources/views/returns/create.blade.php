@extends('layouts.admin')
@section('title','New Return')
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">New Return <span class="info-icon" tabindex="0">i<span class="tooltip">Select completed sale → confirm items → reason → stock returned</span></span></h1><p class="page-sub">Create return</p></div>
    <a href="{{ route('returns.index') }}" class="btn btn-ghost btn-sm">← Back to Returns</a>
</div>

<div class="panel">
    <div class="panel-head"><div class="panel-title">Return Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('returns.store') }}" id="returnForm" onsubmit="event.preventDefault(); const _f=this; confirmModal('Return this sale?','Stock will be restored for all items. This cannot be undone.',()=>_f.submit())">
            @csrf
            <div class="form-grid">
                <div class="field @error('sale_id') err @enderror">
                    <label class="field-label">Sale *</label>
                    <select name="sale_id" required>
                        <option value="">— Select completed sale —</option>
                        @foreach($sales as $s)
                            <option value="{{ encId($s->id) }}" @selected((isset($sale) && $sale->id==$s->id) || old('sale_id')==encId($s->id))>
                                {{ $s->receipt_number }} — {{ $s->customer->name ?? 'Walk-in' }} · TZS {{ number_format($s->total_amount,0) }} · {{ $s->created_at->format('d/m/Y H:i') }}
                            </option>
                        @endforeach
                    </select>
                    @error('sale_id')<span class="field-err">{{ $message }}</span>@enderror
                    @if(isset($sale))
                        <span class="field-hint">Prefilled from sales list — verify before confirming</span>
                    @endif
                </div>
                <div class="field @error('return_reason') err @enderror">
                    <label class="field-label">Reason *</label>
                    <select name="return_reason" required>
                        <option value="">— Select reason —</option>
                        <option value="Wrong product" @selected(old('return_reason')=='Wrong product')>Wrong product</option>
                        <option value="Damaged" @selected(old('return_reason')=='Damaged')>Damaged</option>
                        <option value="Changed mind" @selected(old('return_reason')=='Changed mind')>Changed mind / Customer request</option>
                        <option value="Wrong quantity" @selected(old('return_reason')=='Wrong quantity')>Wrong quantity</option>
                        <option value="Expired" @selected(old('return_reason')=='Expired')>Expired</option>
                        <option value="Other" @selected(old('return_reason')=='Other')>Other</option>
                    </select>
                    @error('return_reason')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="field" style="margin-top:16px">
                <label class="field-label">Notes (optional)</label>
                <textarea name="notes" rows="2" placeholder="Additional details…">{{ old('notes') }}</textarea>
                <span class="field-hint">Will be stored in stock movement reason and audit log</span>
            </div>

            @if(isset($sale) && $sale->items)
                <div class="table-card" style="margin-top:18px">
                    <div class="panel-head" style="border-bottom:1px solid var(--line)"><div class="panel-title">Items to be returned — stock will be restored</div><span class="tag tag-grey">{{ $sale->items->count() }} items</span></div>
                    <div class="table-scroll">
                        <table>
                            <thead><tr><th>Product</th><th class="center">Qty</th><th class="right">Price</th><th class="right">Total</th></tr></thead>
                            <tbody>
                            @foreach($sale->items as $it)
                                <tr>
                                    <td><div class="cell-title">{{ $it->product->name ?? '—' }}</div><div class="cell-sub">{{ $it->product->sku ?? '' }}</div></td>
                                    <td class="center">{{ $it->quantity }}</td>
                                    <td class="right">TZS {{ number_format($it->selling_price,0) }}</td>
                                    <td class="right" style="font-weight:700">TZS {{ number_format($it->total,0) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="table-pagination"><div class="pager-info">Sale total TZS {{ number_format($sale->total_amount,0) }} will be marked as returned; stock for each item restores.</div><strong>Total refund: TZS {{ number_format($sale->total_amount,0) }}</strong></div>
                </div>
            @endif

            <div class="form-actions">
                <a href="{{ route('returns.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Create Return</button>
            </div>
        </form>
    </div>
</div>
@endsection
