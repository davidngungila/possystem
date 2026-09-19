@extends('layouts.admin')
@section('title',$proforma->proforma_number)
@section('content')
<div class="page-head">
    <div>
        <h1 style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">Proforma {{ $proforma->proforma_number }}
            @php $c=['draft'=>'tag-grey','sent'=>'tag-blue','accepted'=>'tag-green','partially_paid'=>'tag-gold','paid'=>'tag-green','converted'=>'tag-gold','cancelled'=>'tag-red','expired'=>'tag-terracotta']; @endphp
            <span class="tag {{ $c[$proforma->status] ?? 'tag-grey' }}">{{ ucfirst(str_replace('_',' ',$proforma->status)) }}</span>
        </h1>
        <p class="page-sub">{{ \Carbon\Carbon::parse($proforma->date)->format('d/m/Y') }} · {{ $proforma->customer->name ?? 'Walk-in' }} @if($proforma->quotation)· Quote {{ $proforma->quotation->quotation_number }}@endif</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <a href="{{ route('proforma-invoices.index') }}" class="btn btn-ghost btn-sm">← Back</a>
        <a href="{{ route('proforma-invoices.print', encId($proforma->id)) }}" class="btn btn-ghost btn-sm" target="_blank">Print / PDF</a>
<a href="{{ route('proforma-invoices.pdf', encId($proforma->id)) }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download PDF</a>
        @if(!in_array($proforma->status,['converted','cancelled']))
            <a href="{{ route('proforma-invoices.edit', encId($proforma->id)) }}" class="btn btn-ghost btn-sm">Edit</a>
            @if($proforma->paid_amount <= 0)
            @if(auth()->check() && auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('proforma-invoices.destroy', encId($proforma->id)) }}" onsubmit="return confirm('Delete this proforma?')">@csrf @method('DELETE')<button class="btn btn-ghost btn-sm" style="color:var(--danger)">Delete</button></form>
                            @endif
            @endif
            <a href="{{ route('proforma-invoices.convert', encId($proforma->id)) }}" class="btn btn-primary btn-sm" onclick="return confirm('Convert to final Invoice?')">Convert to Invoice</a>
        @endif
    </div>
</div>

@if(!in_array($proforma->status,['converted','cancelled','paid']))
<div class="tagbar" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:14px;padding:10px 14px;background:var(--sand-100);border-radius:10px">
    <span style="font-size:12px;font-weight:700;color:var(--ink-soft)">Mark as:</span>
    @foreach(['sent','accepted','expired','cancelled'] as $s)
        @if($proforma->status !== $s)
        <form method="POST" action="{{ route('proforma-invoices.status', encId($proforma->id)) }}">@csrf<input type="hidden" name="status" value="{{ $s }}"><button class="btn btn-ghost btn-xs">{{ ucfirst(str_replace('_',' ',$s)) }}</button></form>
        @endif
    @endforeach
    @if($proforma->balance_due > 0.01)
    <span style="flex:1"></span>
    <button class="btn btn-primary btn-sm" onclick="document.getElementById('payModal').style.display='flex'"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg> Record Payment</button>
    @endif
</div>
@endif

<div class="panel">
    <div class="panel-head"><div class="panel-title">Proforma Items</div></div>
    <div class="table-card" style="border:none;box-shadow:none;margin:0">
        <div class="table-scroll">
            <table>
                <thead><tr><th>#</th><th>Description</th><th class="center">Qty</th><th class="right">Unit Price</th><th class="right">Discount</th><th class="right">Total</th></tr></thead>
                <tbody>
                @foreach($proforma->items as $i => $it)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td><strong>{{ $it->description ?? ($it->product->name ?? 'Item') }}</strong>@if($it->product && $it->product->sku)<div class="cell-mono" style="font-size:11px">{{ $it->product->sku }}</div>@endif</td>
                        <td class="center">{{ $it->quantity }}</td>
                        <td class="right">TZS {{ number_format($it->unit_price,0) }}</td>
                        <td class="right">{{ $it->discount ? 'TZS '.number_format($it->discount,0) : '—' }}</td>
                        <td class="right" style="font-weight:800">TZS {{ number_format($it->total,0) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div style="display:flex;justify-content:flex-end;padding:14px 18px">
            <div style="min-width:300px;font-size:13.5px;line-height:2">
                <div style="display:flex;justify-content:space-between"><span>Subtotal</span><span>TZS {{ number_format($proforma->subtotal,0) }}</span></div>
                @if($proforma->discount_amount>0)<div style="display:flex;justify-content:space-between"><span>Discount</span><span style="color:var(--danger)">- TZS {{ number_format($proforma->discount_amount,0) }}</span></div>@endif
                @if($proforma->tax_amount>0)<div style="display:flex;justify-content:space-between"><span>Tax/VAT ({{ $proforma->tax_rate }}%)</span><span>TZS {{ number_format($proforma->tax_amount,0) }}</span></div>@endif
                <div style="display:flex;justify-content:space-between;font-weight:800;font-size:16px;border-top:1.5px solid var(--coffee-900);padding-top:6px"><span>Total</span><span>TZS {{ number_format($proforma->total_amount,0) }}</span></div>
                <div style="display:flex;justify-content:space-between;color:#2e7d6b;font-weight:700"><span>Paid</span><span>TZS {{ number_format($proforma->paid_amount,0) }}</span></div>
                @if($proforma->balance_due>0)<div style="display:flex;justify-content:space-between;font-weight:800;color:var(--terracotta-600)"><span>Balance Due</span><span>TZS {{ number_format($proforma->balance_due,0) }}</span></div>@endif
            </div>
        </div>
    </div>
</div>

<div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px">
    <div class="panel">
        <div class="panel-head"><div class="panel-title">Payment History</div></div>
        <div class="panel-body">
            @forelse($proforma->payments as $pay)
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px dashed var(--line);font-size:13px">
                    <span>{{ paymentMethodName($pay->payment_method) }} @if($pay->reference)<small style="color:var(--ink-soft)">({{ $pay->reference }})</small>@endif</span>
                    <strong>TZS {{ number_format($pay->amount,0) }} <small style="color:var(--ink-soft);font-weight:400">{{ $pay->created_at->format('d/m H:i') }}</small></strong>
                </div>
            @empty
                <div class="empty-state" style="padding:14px"><strong>No payments yet</strong></div>
            @endforelse
        </div>
    </div>
    <div class="panel">
        <div class="panel-head"><div class="panel-title">Payment Info & Terms</div></div>
        <div class="panel-body" style="font-size:13.5px;line-height:1.8">
            <strong>{{ $proforma->payment_terms ?? '—' }}</strong>
            {!! nl2br(e($proforma->bank_details ?? '—')) !!}
            <hr style="border:none;border-top:1px dashed var(--line);margin:10px 0">
            <strong>Notes:</strong><br>{!! nl2br(e($proforma->notes ?: '—')) !!}
        </div>
    </div>
</div>

@if($proforma->convertedInvoice)
<div class="panel" style="margin-top:16px;background:var(--sand-100)">
    <div class="panel-body" style="display:flex;gap:10px;align-items:center">
        <strong style="color:var(--coffee-900)">Converted to Invoice →</strong>
        <a href="{{ route('invoices.show', encId($proforma->converted_invoice_id)) }}" class="btn btn-ghost btn-sm">{{ $proforma->convertedInvoice->invoice_number }}</a>
    </div>
</div>
@endif

<div class="modal-backdrop" id="payModal" style="display:none">
    <div class="modal-box" style="max-width:420px">
        <div class="modal-head">
            <div class="modal-title">Record Payment — {{ $proforma->proforma_number }}</div>
            <button type="button" class="modal-close" onclick="document.getElementById('payModal').style.display='none'">×</button>
        </div>
        <form method="POST" action="{{ route('proforma-invoices.payments', encId($proforma->id)) }}">
            @csrf
            <div class="modal-body" style="display:grid;gap:12px">
                <div class="field">
                    <label class="field-label">Balance Due</label>
                    <input value="TZS {{ number_format($proforma->balance_due,0) }}" readonly>
                </div>
                <div class="field">
                    <label class="field-label">Payment Method *</label>
                    <select name="payment_method" required>
                        @foreach(['cash','m-pesa','airtel_money','mixx','halopesa','bank','card'] as $m)
                            <option value="{{ $m }}">{{ paymentMethodName($m) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label class="field-label">Amount *</label>
                    <input name="amount" type="number" step="0.01" min="0.01" value="{{ $proforma->balance_due }}" required>
                </div>
                <div class="field">
                    <label class="field-label">Reference</label>
                    <input name="reference" placeholder="optional">
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('payModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Payment</button>
            </div>
        </form>
    </div>
</div>
@endsection