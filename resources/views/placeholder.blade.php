@extends('layouts.admin')
@section('title', $title ?? 'Module')
@section('content')
<div class="page-head">
    <div>
        <h1>{{ $title ?? 'Module' }}</h1>
        <p class="page-sub">{{ $subtitle ?? 'This module follows the same theme as admissionsystemnew — single-shop POS.' }}</p>
    </div>
    <span class="tag tag-grey">Placeholder</span>
</div>

<div class="panel" style="padding:28px;text-align:center;">
    <div class="es-icon" style="margin-bottom:14px;background:var(--sand-100);border:1px solid var(--line);"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg></div>
    <strong style="display:block;color:var(--coffee-900);font-size:16px;">{{ $title ?? 'Coming soon' }}</strong>
    <p style="color:var(--ink-soft);font-size:13px;margin-top:6px;line-height:1.6;max-width:520px;margin-left:auto;margin-right:auto;">
        This view uses the exact design system copied from <code style="background:var(--sand-100);padding:2px 6px;border-radius:6px;border:1px solid var(--line);">D:\server01\admisionsystemnew\resources</code>
        — <span style="font-weight:700;color:var(--coffee-700)">theme-styles, theme-scripts, loader, confirm-modal, table-card, stat-grid, panel, topbar/sidebar</span>.
        Wire it to POS logic: {{ $subtitle ?? '' }}
    </p>
    <div style="margin-top:16px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
        <a href="{{ url('/dashboard') }}" class="btn btn-ghost btn-sm">Back to Dashboard</a>
        <a href="{{ url('/pos') }}" class="btn btn-primary btn-sm">Open POS</a>
    </div>
    <div style="margin-top:18px;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;text-align:left;">
        <div class="panel" style="padding:14px"><div style="font-weight:700;color:var(--coffee-900);font-size:13px;display:flex;align-items:center;gap:6px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="7" y1="8" x2="7" y2="16"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="17" y1="8" x2="17" y2="16"/></svg> Barcode flow</div><div style="font-size:12px;color:var(--ink-soft);margin-top:4px;display:flex;align-items:center;gap:6px;flex-wrap:wrap;"><span>Scan</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Find product</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Check stock</span> <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> <span>Add to cart</span></div></div>
        <div class="panel" style="padding:14px"><div style="font-weight:700;color:var(--coffee-900);font-size:13px;">Stock movement</div><div style="font-size:12px;color:var(--ink-soft);margin-top:4px;">Every change logged — no silent edits</div></div>
        <div class="panel" style="padding:14px"><div style="font-weight:700;color:var(--coffee-900);font-size:13px;">Payments</div><div style="font-size:12px;color:var(--ink-soft);margin-top:4px;">Cash + M-Pesa + Airtel + Mixx + HaloPesa</div></div>
    </div>
</div>
@endsection
