@extends('layouts.admin')
@section('title','Audit Logs')
@section('content')
<div class="page-head">
    <div><h1>Audit Logs</h1><p class="page-sub">Every create · update · delete · price change — with user, IP & values</p></div>
    <span class="tag tag-grey">{{ $logs->total() }} entries</span>
</div>

<div class="table-card">
    <form method="GET" class="table-toolbar">
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Search action, model, user...">
        </div>
        <button class="btn btn-ghost btn-sm">Search</button>
    </form>
    <div class="table-scroll">
        <table>
            <thead><tr><th>At</th><th>User</th><th>Action</th><th>Model</th><th>IP</th><th class="center">View</th></tr></thead>
            <tbody>
            @forelse($logs as $l)
                <tr>
                    <td class="muted" style="font-size:12px;white-space:nowrap">{{ $l->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>
                        <div class="cell-title" style="font-size:13px">{{ $l->user->name ?? 'system' }}</div>
                        <div class="cell-sub">{{ $l->user->email ?? '—' }}</div>
                    </td>
                    <td><span class="tag {{ str_starts_with($l->action,'delete') ? 'tag-red' : (str_starts_with($l->action,'create') ? 'tag-green' : 'tag-gold') }}">{{ $l->action }}</span></td>
                    <td>
                        <div class="cell-mono" style="font-size:12px">{{ class_basename($l->model_type ?? '—') }}</div>
                        <div class="cell-sub">#{{ $l->model_id ?? '—' }}</div>
                    </td>
                    <td class="cell-mono" style="font-size:12px">{{ $l->ip_address ?? '—' }}</td>
                    <td class="center">
                        <button class="btn-icon" title="View details" onclick="showAuditDetail({{ $l->id }})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><strong>No audit logs yet</strong><p>Create, update or delete a product/category/supplier to see entries here.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div id="auditDetailModal" class="modal-backdrop" onclick="if(event.target===this) closeAuditDetail()">
        <div class="modal" style="max-width:560px">
            <div class="modal-head">
                <div>
                    <h3 id="auditDetailTitle" style="font-size:15px">Audit Details</h3>
                    <p id="auditDetailSub" style="font-size:12px;color:var(--ink-soft)"></p>
                </div>
                <button class="modal-close" onclick="closeAuditDetail()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="modal-body" id="auditDetailBody" style="max-height:60vh;overflow:auto"></div>
            <div class="modal-foot">
                <button class="btn btn-ghost" onclick="closeAuditDetail()">Close</button>
            </div>
        </div>
    </div>
    @if($logs->hasPages())
        @include('layouts.partials.pagination', ['paginator'=>$logs])
    @else
        <div class="table-pagination"><div class="pager-info">{{ $logs->total() }} audit entries — click <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> to view details</div></div>
    @endif
</div>
@php
$auditMap = [];
foreach($logs as $logItem){
  $auditMap[$logItem->id] = [
    'action'=>$logItem->action,
    'model'=>class_basename($logItem->model_type ?? ''),
    'model_id'=>$logItem->model_id,
    'user'=>$logItem->user->name ?? 'system',
    'email'=>$logItem->user->email ?? '',
    'ip'=>$logItem->ip_address,
    'at'=>$logItem->created_at->format('d/m/Y H:i:s'),
    'old'=>$logItem->old_values,
    'new'=>$logItem->new_values
  ];
}
@endphp
<script>
const auditLogsData = @json($auditMap);
function showAuditDetail(id){
  const data = auditLogsData[id];
  if(!data){ toast('Details not found','error'); return; }
  document.getElementById('auditDetailTitle').textContent = data.action + ' — ' + data.model + ' #' + data.model_id;
  document.getElementById('auditDetailSub').textContent = data.user + ' (' + data.email + ') · ' + data.ip + ' · ' + data.at;
  let html = '';
  if(data.action === 'price_change' && data.old){
    html += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px"><div style="padding:10px;background:var(--sand-50);border:1px solid var(--line);border-radius:8px"><div style="font-size:11px;font-weight:700;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.06em">Buying Price</div><div style="font-family:monospace;font-size:13px;margin-top:4px">'+ (data.old.buying_price ?? '—') +' → '+(data.new.buying_price ?? '—')+'</div></div><div style="padding:10px;background:var(--sand-50);border:1px solid var(--line);border-radius:8px"><div style="font-size:11px;font-weight:700;color:var(--ink-soft);text-transform:uppercase">Selling Price</div><div style="font-family:monospace;font-size:13px;margin-top:4px">'+ (data.old.selling_price ?? '—') +' → '+(data.new.selling_price ?? '—')+'</div></div></div>';
  }
  const formatJson = (obj)=> obj ? '<pre style="background:var(--sand-50);border:1px solid var(--line);border-radius:8px;padding:10px;font-size:11px;overflow:auto;max-height:200px;white-space:pre-wrap;word-break:break-all">'+JSON.stringify(obj,null,2)+'</pre>' : '<span style="color:var(--ink-soft)">—</span>';
  html += '<div style="margin-top:8px"><div style="font-size:12px;font-weight:700;color:var(--coffee-900);margin-bottom:4px">Old Values</div>'+formatJson(data.old)+'</div>';
  html += '<div style="margin-top:12px"><div style="font-size:12px;font-weight:700;color:var(--coffee-900);margin-bottom:4px">New Values</div>'+formatJson(data.new)+'</div>';
  document.getElementById('auditDetailBody').innerHTML = html;
  const modal = document.getElementById('auditDetailModal');
  modal.classList.add('show'); modal.style.display='flex';
}
function closeAuditDetail(){
  const m=document.getElementById('auditDetailModal');
  if(m){ m.classList.remove('show'); m.style.display='none'; }
}
document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeAuditDetail(); });
</script>
@endsection
