@extends('layouts.admin')
@section('title','Cashier Shifts')
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">Cashier Shifts <span class="info-icon" tabindex="0">i<span class="tooltip">Open → Track (cash, mobile, card, credit, refunds, expenses, deposits) → Close → Expected vs Actual → Difference</span></span></h1><p class="page-sub">Shifts</p></div>
    <span class="tag tag-green">Live</span>
</div>

<div class="table-card">
    <div class="panel-head"><div class="panel-title">Shifts</div><span class="tag tag-grey">{{ $shifts->total() }} total</span></div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Cashier</th><th>Opened</th><th class="right">Opening</th><th class="right">Closing</th><th class="center">Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($shifts as $s)
                <tr>
                    <td><div class="cell-title">{{ $s->cashier->name ?? '—' }}</div><div class="cell-sub">{{ $s->cashier->email ?? '' }}</div></td>
                    <td>{{ $s->opened_at ? $s->opened_at->format('d/m/Y H:i') : $s->created_at->format('d/m/Y H:i') }}</td>
                    <td class="right">TZS {{ number_format($s->opening_cash,0) }}</td>
                    <td class="right">{{ $s->closing_cash !== null ? 'TZS '.number_format($s->closing_cash,0) : '—' }}</td>
                    <td class="center">@if($s->status==='open') <span class="tag tag-green">Open</span> @else <span class="tag tag-grey">Closed</span> @endif</td>
                    <td>
                        @if($s->status==='open')
                        <form method="POST" action="{{ route('shifts.close', $s) }}" style="display:flex;gap:6px;align-items:center">
                            @csrf
                            <input name="closing_cash" type="number" step="0.01" placeholder="Closing" style="width:90px;padding:6px 8px;border:1.5px solid var(--line);border-radius:8px">
                            <input name="actual_cash" type="number" step="0.01" placeholder="Actual" style="width:90px;padding:6px 8px;border:1.5px solid var(--line);border-radius:8px">
                            <button class="btn btn-ghost btn-sm" onclick="return confirm('Are you sure want to close this shift?')">Close</button>
                        </form>
                        @else
                            <span class="muted" style="font-size:12px">Diff: TZS {{ number_format($s->difference ?? 0,0) }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><strong>No shifts yet</strong><p>Open a shift to start tracking cash.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($shifts->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$shifts])
    @endif
    <div class="table-toolbar" style="justify-content:flex-start">
        <form method="POST" action="{{ route('shifts.open') }}" style="display:flex;gap:8px;align-items:center">
            @csrf
            <input name="opening_cash" type="number" step="0.01" placeholder="Opening cash" required style="padding:8px 12px;border:1.5px solid var(--line);border-radius:8px">
            <button class="btn btn-primary btn-sm">Open New Shift</button>
        </form>
        <span class="muted" style="font-size:12px;margin-left:8px">Every payment belongs to a transaction · Every cash change logged</span>
    </div>
</div>
@endsection
