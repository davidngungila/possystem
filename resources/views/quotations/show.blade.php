@extends('layouts.admin')
@section('title',$quotation->quotation_number)
@section('content')
<div class="page-head">
    <div>
        <h1 style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">Quotation {{ $quotation->quotation_number }}
            @php $c=['draft'=>'tag-grey','sent'=>'tag-blue','accepted'=>'tag-green','rejected'=>'tag-red','expired'=>'tag-terracotta','converted'=>'tag-gold']; @endphp
            <span class="tag {{ $c[$quotation->status] ?? 'tag-grey' }}">{{ ucfirst($quotation->status) }}</span>
        </h1>
        <p class="page-sub">{{ \Carbon\Carbon::parse($quotation->date)->format('d/m/Y') }} · {{ $quotation->customer->name ?? 'Walk-in' }} · Validity {{ $quotation->valid_until ? \Carbon\Carbon::parse($quotation->valid_until)->format('d/m/Y') : '—' }}</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <a href="{{ route('quotations.index') }}" class="btn btn-ghost btn-sm">← Back</a>
        <a href="{{ route('quotations.print', encId($quotation->id)) }}" class="btn btn-ghost btn-sm" target="_blank">Print / PDF</a>
<a href="{{ route('quotations.pdf', encId($quotation->id)) }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download PDF</a>
        @if($quotation->status !== 'converted')
                            <form method="POST" action="{{ route('quotations.duplicate', encId($quotation->id)) }}">@csrf<button class="btn btn-ghost btn-sm">Duplicate</button></form>
            <a href="{{ route('quotations.edit', encId($quotation->id)) }}" class="btn btn-ghost btn-sm">Edit</a>
            @if(auth()->check() && auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('quotations.destroy', encId($quotation->id)) }}" onsubmit="return confirm('Delete this quotation?')">@csrf @method('DELETE')<button class="btn btn-ghost btn-sm" style="color:var(--danger)">Delete</button></form>
                            @endif
            <a href="{{ route('quotations.convert-proforma', encId($quotation->id)) }}" class="btn btn-primary btn-sm" onclick="return confirm('Convert to Proforma Invoice?')">Convert to Proforma</a>
            <a href="{{ route('quotations.convert-invoice', encId($quotation->id)) }}" class="btn btn-primary btn-sm" style="background:#2e7d6b" onclick="return confirm('Skip proforma — convert straight to Invoice?')">Convert to Invoice</a>
        @endif
    </div>
</div>

@if(in_array($quotation->status,['draft','sent','rejected','expired']))
<div class="tagbar" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:14px">
    <span style="font-size:12px;font-weight:700;color:var(--ink-soft)">Mark as:</span>
    @foreach(['sent','accepted','rejected','expired'] as $s)
        @if($quotation->status !== $s)
        <form method="POST" action="{{ route('quotations.status', encId($quotation->id)) }}">@csrf<input type="hidden" name="status" value="{{ $s }}"><button class="btn btn-ghost btn-xs">{{ ucfirst($s) }}</button></form>
        @endif
    @endforeach
</div>
@endif

<div class="panel">
    <div class="panel-head"><div class="panel-title">Quotation Items</div></div>
    <div class="table-card" style="border:none;box-shadow:none;margin:0">
        <div class="table-scroll">
            <table>
                <thead><tr><th>#</th><th>Description</th><th class="center">Qty</th><th class="right">Unit Price</th><th class="right">Discount</th><th class="right">Total</th></tr></thead>
                <tbody>
                @foreach($quotation->items as $i => $it)
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
            <div style="min-width:280px;font-size:13.5px;line-height:2">
                <div style="display:flex;justify-content:space-between"><span>Subtotal</span><span>TZS {{ number_format($quotation->subtotal,0) }}</span></div>
                @if($quotation->discount_amount>0)<div style="display:flex;justify-content:space-between"><span>Discount {{ $quotation->discount_type==='percent' ? '('.$quotation->discount_value.'%)' : '' }}</span><span style="color:var(--danger)">- TZS {{ number_format($quotation->discount_amount,0) }}</span></div>@endif
                @if($quotation->tax_amount>0)<div style="display:flex;justify-content:space-between"><span>Tax/VAT ({{ $quotation->tax_rate }}%)</span><span>TZS {{ number_format($quotation->tax_amount,0) }}</span></div>@endif
                <div style="display:flex;justify-content:space-between;font-weight:800;font-size:16px;border-top:1.5px solid var(--coffee-900);padding-top:6px"><span>Total</span><span>TZS {{ number_format($quotation->total_amount,0) }}</span></div>
            </div>
        </div>
    </div>
</div>

<div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <div class="panel">
        <div class="panel-head"><div class="panel-title">Notes</div></div>
        <div class="panel-body">{!! nl2br(e($quotation->notes ?: '—')) !!}</div>
    </div>
    <div class="panel">
        <div class="panel-head"><div class="panel-title">Terms & Conditions</div></div>
        <div class="panel-body">{!! nl2br(e($quotation->terms ?: '—')) !!}</div>
    </div>
</div>

@if($quotation->convertedProforma || $quotation->convertedInvoice)
<div class="panel" style="margin-top:16px;background:var(--sand-100)">
    <div class="panel-body" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
        <strong style="color:var(--coffee-900)">Converted →</strong>
        @if($quotation->convertedProforma)<a href="{{ route('proforma-invoices.show', encId($quotation->converted_proforma_id)) }}" class="btn btn-ghost btn-sm">{{ $quotation->convertedProforma->proforma_number }}</a>@endif
        @if($quotation->convertedInvoice)<a href="{{ route('invoices.show', encId($quotation->converted_invoice_id)) }}" class="btn btn-ghost btn-sm">{{ $quotation->convertedInvoice->invoice_number }}</a>@endif
    </div>
</div>
@endif
@endsection