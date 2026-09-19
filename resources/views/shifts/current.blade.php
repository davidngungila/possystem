@extends('layouts.pos')
@section('title','Current Shift')
@section('content')
<div class="page-head">
    <div><h1 style="display:flex;align-items:center;gap:8px">Current Shift <span class="info-icon" tabindex="0">i<span class="tooltip">Your open shift — cash collected, mobile, card, credit, refunds, expenses</span></span></h1><p class="page-sub">Open shift</p></div>
    <a href="{{ route('shifts.index') }}" class="btn btn-ghost btn-sm">All Shifts</a>
</div>

@if($shift)
    <div class="stat-grid">
        <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/></svg></div><span class="tag tag-green">Open</span></div><div class="stat-label">Opening Cash</div><div class="stat-value">TZS {{ number_format($shift->opening_cash,0) }}</div><div class="stat-sub">Opened {{ $shift->opened_at ? $shift->opened_at->format('d/m H:i') : $shift->created_at->format('d/m H:i') }}</div></div>
        <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="20" x2="12" y2="4"/></svg></div><span class="tag tag-gold">Live</span></div><div class="stat-label">Expected Cash</div><div class="stat-value">TZS {{ number_format($shift->expected_cash ?? 0,0) }}</div><div class="stat-sub">Cash sales + opening − expenses − refunds</div></div>
        <div class="stat-card"><div class="stat-top"><div class="stat-icon ic-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><span class="tag tag-terracotta">Now</span></div><div class="stat-label">Cashier</div><div class="stat-value" style="font-size:18px">{{ $shift->cashier->name ?? auth()->user()->name }}</div><div class="stat-sub">{{ $shift->cashier->email ?? '' }}</div></div>
    </div>
    <div class="panel" style="padding:16px">
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            <form method="POST" action="{{ route('shifts.close', $shift) }}" style="display:flex;gap:8px;align-items:center">
                @csrf
                <input name="closing_cash" type="number" step="0.01" placeholder="Closing cash" required style="padding:10px 12px;border:1.5px solid var(--line);border-radius:10px">
                <input name="actual_cash" type="number" step="0.01" placeholder="Actual cash counted" required style="padding:10px 12px;border:1.5px solid var(--line);border-radius:10px">
                <button class="btn btn-primary btn-sm" onclick="return confirm('Are you sure want to close your shift? System will calculate difference.')">Close Shift</button>
            </form>
            <span class="muted" style="font-size:12px">Expected vs Actual → Difference</span>
        </div>
    </div>
@else
    <div class="panel" style="padding:24px;text-align:center">
        <div class="es-icon" style="margin:0 auto 12px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        <strong>No open shift</strong>
        <p class="muted" style="font-size:13px;margin-top:4px">You have no open shift. Open one to start selling.</p>
        <form method="POST" action="{{ route('shifts.open') }}" style="margin-top:12px;display:flex;gap:8px;justify-content:center">
            @csrf
            <input name="opening_cash" type="number" step="0.01" placeholder="Opening cash" required style="padding:10px 12px;border:1.5px solid var(--line);border-radius:10px">
            <button class="btn btn-primary btn-sm">Open Shift</button>
        </form>
    </div>
@endif
@endsection
