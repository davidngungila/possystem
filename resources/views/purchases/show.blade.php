@extends('layouts.admin')
@section('title','Purchase')
@section('content')
<div class="page-head">
    <div><h1>Purchase {{ $purchase->invoice_number ?? '#'.$purchase->id }}</h1><p class="page-sub">{{ $purchase->supplier->name ?? '—' }} · {{ $purchase->status }}</p></div>
    <a href="{{ route('purchases.index') }}" class="btn btn-ghost btn-sm">Back</a>
</div>

<div class="table-card">
    <div class="panel-head"><div class="panel-title">Items</div><span class="tag tag-grey">{{ $purchase->items->count() }} items</span></div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Product</th><th class="center">Qty</th><th class="right">Buying Price</th><th class="right">Total</th></tr></thead>
            <tbody>
            @foreach($purchase->items as $it)
                <tr><td>{{ $it->product->name ?? '—' }} <span class="cell-mono">({{ $it->product->sku ?? '' }})</span></td><td class="center">{{ $it->quantity }}</td><td class="right">TZS {{ number_format($it->buying_price,0) }}</td><td class="right">TZS {{ number_format($it->total,0) }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="table-pagination" style="justify-content:flex-end"><strong>Total: TZS {{ number_format($purchase->total_amount,0) }} · Status: {{ $purchase->status }} @if(in_array($purchase->status,['received','partially_received'])) · Stock IN done @endif</strong></div>
</div>
@endsection
