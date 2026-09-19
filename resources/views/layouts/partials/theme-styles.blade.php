{{-- Internal CMS theme — table-card, toolbar, chips, tags, modals, toasts, forms, stat-cards --}}
<style>
/* ── page header ── */
.page-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;flex-wrap:wrap}
.page-head h1{font-size:22px;font-weight:800;color:var(--coffee-900);line-height:1.2}
.page-sub{font-size:13px;color:var(--ink-soft);margin-top:4px;line-height:1.5}
.page-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap}

/* ── stat grid ── */
.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:22px}
.stat-card{background:var(--white);border:1px solid var(--line);border-radius:14px;padding:18px;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;gap:10px;position:relative;overflow:hidden}
.stat-top{display:flex;align-items:center;justify-content:space-between}
.stat-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex:none}
.stat-icon svg{width:20px;height:20px}
.ic-terracotta{background:var(--terracotta-100);color:var(--terracotta-600)}
.ic-green{background:var(--acacia-100);color:var(--acacia-600)}
.ic-gold{background:var(--gold-100);color:#8a6418}
.ic-blue{background:#E4ECF8;color:#2C5AA0}
.ic-grey{background:var(--sand-200);color:var(--coffee-700)}
.stat-label{font-size:10.5px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--ink-soft)}
.stat-value{font-size:26px;font-weight:800;color:var(--coffee-900);line-height:1}
.stat-sub{font-size:12px;color:var(--ink-soft);line-height:1.4}
.stat-trend{font-size:12px;font-weight:700;padding:3px 8px;border-radius:20px}
.stat-trend.up{background:var(--acacia-100);color:var(--acacia-600)}
.stat-trend.down{background:var(--danger-100);color:var(--danger)}

/* ── panels ── */
.panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 18px;border-bottom:1px solid var(--line);flex-wrap:wrap}
.panel-title{font-size:14px;font-weight:800;color:var(--coffee-900)}
.panel-sub{font-size:12px;color:var(--ink-soft);margin-top:2px}
.panel-body{padding:18px}

/* ── table-card (CMS pattern) ── */
.table-card{background:var(--white);border:1px solid var(--line);border-radius:14px;box-shadow:var(--shadow-sm);overflow:hidden}
.table-toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px 18px;border-bottom:1px solid var(--line);flex-wrap:wrap;background:var(--white)}
.chip-filters{display:flex;gap:6px;flex-wrap:wrap;align-items:center}
.chip{background:transparent;border:1.5px solid var(--line);color:var(--ink-soft);padding:6px 13px;border-radius:20px;font-size:12.5px;font-weight:700;cursor:pointer;transition:all .15s;white-space:nowrap}
.chip:hover{border-color:var(--coffee-300);color:var(--coffee-700)}
.chip.active{background:var(--coffee-900);border-color:var(--coffee-900);color:#fff}
.table-search{position:relative;display:flex;align-items:center}
.table-search svg{position:absolute;left:11px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--ink-soft);pointer-events:none}
.table-search input{padding:9px 14px 9px 34px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;width:240px;background:var(--white);color:var(--ink);transition:border-color .15s,box-shadow .15s,width .15s}
.table-search input::placeholder{color:var(--ink-soft)}
.table-search input:focus{outline:none;border-color:var(--terracotta-600);box-shadow:0 0 0 3px var(--terracotta-100);width:280px}
.table-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch}
.table-scroll table{width:100%;border-collapse:collapse;font-size:13.5px;min-width:640px}
.table-scroll thead th{text-align:left;padding:11px 16px;font-size:10.5px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--ink-soft);background:var(--sand-100);border-bottom:1px solid var(--line);white-space:nowrap}
.table-scroll thead th.center,.table-scroll tbody td.center{text-align:center}
.table-scroll thead th.right,.table-scroll tbody td.right{text-align:right}
.table-scroll tbody td{padding:12px 16px;border-bottom:1px solid var(--line);color:var(--coffee-800);vertical-align:middle}
.table-scroll tbody tr:last-child td{border-bottom:none}
.table-scroll tbody tr{transition:background .12s}
.table-scroll tbody tr:hover{background:var(--sand-100)}
.cell-main{display:flex;align-items:center;gap:12px}
.thumb{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;flex:none}
.thumb-green{background:var(--acacia-100);color:var(--acacia-600)}
.thumb-terracotta{background:var(--terracotta-100);color:var(--terracotta-600)}
.thumb-gold{background:var(--gold-100);color:#8a6418}
.thumb-grey{background:var(--sand-200);color:var(--coffee-700)}
.thumb-blue{background:#E4ECF8;color:#2C5AA0}
.thumb-coffee{background:var(--sand-100);color:var(--coffee-700);border:1px solid var(--line)}
.cell-title{font-weight:700;color:var(--coffee-900);font-size:13.5px;line-height:1.3}
.cell-sub{font-size:12px;color:var(--ink-soft);margin-top:2px;line-height:1.3}
.cell-mono{font-family:ui-monospace,monospace;font-size:12.5px;color:var(--coffee-700)}
.row-actions{display:flex;justify-content:flex-end;gap:6px;align-items:center}
.btn-icon{width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;border:1.5px solid var(--line);background:var(--white);color:var(--coffee-700);transition:all .15s;flex:none}
.btn-icon:hover{border-color:var(--terracotta-600);color:var(--terracotta-600)}
.btn-icon.danger:hover{border-color:var(--danger);color:var(--danger);background:var(--danger-100)}
.btn-icon svg{width:14px;height:14px}

/* ── tags ── */
.tag-green{background:var(--acacia-100);color:var(--acacia-600)}
.tag-gold{background:var(--gold-100);color:#8a6418}
.tag-red{background:var(--danger-100);color:var(--danger)}
.tag-grey{background:var(--sand-200);color:var(--ink-soft)}
.tag-terracotta{background:var(--terracotta-100);color:var(--terracotta-600)}
.tag-blue{background:#E4ECF8;color:#2C5AA0}
.tag-coffee{background:var(--sand-100);color:var(--coffee-700);border:1px solid var(--line)}

/* ── pagination ── */
.table-pagination{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px 18px;border-top:1px solid var(--line);flex-wrap:wrap;background:var(--white)}
.pager-info{font-size:12.5px;color:var(--ink-soft);font-weight:600}
.pager-pages{display:flex;gap:5px;align-items:center;flex-wrap:wrap}
.pager-btn{min-width:32px;height:32px;padding:0 8px;border:1.5px solid var(--line);border-radius:8px;background:var(--white);font-size:13px;font-weight:700;color:var(--coffee-700);display:inline-flex;align-items:center;justify-content:center;transition:all .15s}
.pager-btn:hover{border-color:var(--terracotta-600);color:var(--terracotta-600)}
.pager-btn.active{background:var(--coffee-900);border-color:var(--coffee-900);color:#fff}
.pager-btn.disabled{opacity:.45;pointer-events:none}

/* ── buttons ── */
.btn-sm{padding:8px 13px;font-size:13px}
.btn-danger{background:var(--danger);color:#fff;box-shadow:0 6px 16px rgba(179,58,58,.22)}
.btn-danger:hover{background:#9c2f2f}
.btn-soft{background:var(--acacia-100);color:var(--acacia-600);border:1.5px solid #c8d7a8}
.btn-soft:hover{background:#d9e4c0}
.btn-gold{background:var(--gold-500);color:#fff}
.btn-gold:hover{background:#c9973f}

/* ── info icon tooltip ── */
.page-head{overflow:visible}
.info-icon{position:relative;display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:50%;background:var(--terracotta-100);color:var(--terracotta-600);border:1px solid var(--line);font-size:10px;font-weight:800;cursor:help;flex:none;z-index:1;}
.info-icon .tooltip{position:absolute;left:50%;top:calc(100% + 10px);transform:translateX(-50%);background:var(--coffee-900);color:#fff;padding:10px 12px;border-radius:8px;font-size:11px;line-height:1.5;white-space:normal;max-width:280px;width:max-content;min-width:180px;box-shadow:0 12px 28px rgba(0,0,0,.24);display:none;z-index:9999;text-align:left;font-weight:500;letter-spacing:0;pointer-events:none;}
.info-icon .tooltip::after{content:"";position:absolute;bottom:100%;left:50%;margin-left:-6px;border-width:6px;border-style:solid;border-color:transparent transparent var(--coffee-900) transparent;}
.info-icon:hover .tooltip, .info-icon:focus .tooltip, .info-icon:focus-within .tooltip{display:block;}
@media(max-width:640px){
  .info-icon .tooltip{max-width:200px;left:auto;right:0;transform:none;}
  .info-icon .tooltip::after{left:auto;right:16px;margin-left:0;}
}

/* ── forms ── */
.form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
.form-grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.field{display:flex;flex-direction:column;gap:6px}
.field-label{font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--coffee-700)}
.field input,.field select,.field textarea{width:100%;padding:11px 13px;border:1.5px solid var(--line);border-radius:10px;font-size:14px;background:var(--white);color:var(--ink);transition:border-color .15s,box-shadow .15s}
.field input::placeholder,.field textarea::placeholder{color:var(--ink-soft)}
.field input:focus,.field select:focus,.field textarea:focus{outline:none;border-color:var(--terracotta-600);box-shadow:0 0 0 3px var(--terracotta-100)}
.field textarea{resize:vertical;min-height:90px}
.field-hint{font-size:12px;color:var(--ink-soft);line-height:1.4}
.field.err input,.field.err select,.field.err textarea{border-color:var(--danger)}
.field-err{font-size:12px;color:var(--danger);font-weight:600}
.form-row{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
.form-actions{display:flex;gap:10px;justify-content:flex-end;padding-top:18px;border-top:1px solid var(--line);margin-top:22px;flex-wrap:wrap}
.check-row{display:flex;align-items:center;gap:9px;font-size:13.5px;color:var(--coffee-800)}
.check-row input[type="checkbox"]{width:17px;height:17px;accent-color:var(--terracotta-600);flex:none}

/* ── empty state ── */
.empty-state{padding:48px 20px;text-align:center;color:var(--ink-soft)}
.es-icon{width:56px;height:56px;border-radius:16px;background:var(--sand-100);border:1px solid var(--line);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;color:var(--coffee-500)}
.es-icon svg{width:24px;height:24px}
.empty-state strong{display:block;color:var(--coffee-800);font-size:15px;margin-bottom:4px}
.empty-state p{font-size:13px;line-height:1.5;margin:0}

/* ── modal ── */
.modal-backdrop{position:fixed;inset:0;background:rgba(20,12,6,.52);backdrop-filter:blur(3px);z-index:500;display:none;align-items:center;justify-content:center;padding:20px}
.modal-backdrop.show{display:flex}
.modal{background:var(--white);border-radius:18px;max-width:520px;width:100%;max-height:88vh;overflow:auto;box-shadow:0 30px 70px rgba(0,0,0,.28);animation:modalIn .2s ease}
.modal.modal-lg{max-width:760px}
.modal.modal-sm{max-width:420px}
@keyframes modalIn{from{opacity:0;transform:translateY(14px) scale(.98)}to{opacity:1;transform:none}}
.modal-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px 22px;border-bottom:1px solid var(--line)}
.modal-head h3{font-size:16px;font-weight:800;color:var(--coffee-900)}
.modal-head p{font-size:12.5px;color:var(--ink-soft);margin-top:3px}
.modal-close{width:34px;height:34px;border-radius:9px;border:1.5px solid var(--line);background:var(--white);display:flex;align-items:center;justify-content:center;color:var(--ink-soft)}
.modal-close:hover{color:var(--coffee-900);border-color:var(--coffee-300)}
.modal-body{padding:22px}
.modal-foot{display:flex;justify-content:flex-end;gap:10px;padding:16px 22px;border-top:1px solid var(--line);flex-wrap:wrap}

/* ── toasts ── */
#toastHost{position:fixed;top:20px;right:20px;z-index:1500;display:flex;flex-direction:column;gap:10px;max-width:360px;pointer-events:none}
.toast{background:var(--coffee-900);color:#fff;padding:13px 16px;border-radius:12px;box-shadow:0 14px 34px rgba(0,0,0,.22);font-size:13.5px;font-weight:600;display:flex;align-items:center;gap:10px;animation:toastIn .25s ease;border-left:4px solid var(--gold-500);pointer-events:auto}
.toast.success{border-left-color:var(--acacia-500, #7A8450)}
.toast.error{border-left-color:var(--danger)}
.toast.info{border-left-color:var(--gold-500)}
@keyframes toastIn{from{opacity:0;transform:translateX(24px)}to{opacity:1;transform:none}}
.toast.out{opacity:0;transform:translateX(24px);transition:all .3s}

/* ── key-value ── */
.kv{border:1px solid var(--line);border-radius:12px;background:var(--white);overflow:hidden}
.kv-row{display:flex;justify-content:space-between;gap:16px;padding:12px 16px;border-bottom:1px solid var(--line);font-size:13.5px}
.kv-row:last-child{border-bottom:none}
.kv .k{color:var(--ink-soft);font-weight:600}
.kv .v{color:var(--coffee-900);font-weight:700;text-align:right;word-break:break-word}

/* ── tabs ── */
.tabs-row{display:flex;gap:4px;border-bottom:1px solid var(--line);margin-bottom:18px;overflow-x:auto}
.tab{padding:10px 16px;border:none;background:none;font-weight:700;font-size:13.5px;color:var(--ink-soft);border-bottom:2.5px solid transparent;white-space:nowrap;cursor:pointer}
.tab.active{color:var(--terracotta-600);border-bottom-color:var(--terracotta-600)}

/* ── helpers ── */
.muted{color:var(--ink-soft)}
.mono{font-family:ui-monospace,monospace}
.grid-2{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
.grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.flex-between{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}

/* responsive */
@media(max-width:900px){
  .form-grid,.form-grid-3,.grid-2,.grid-3{grid-template-columns:1fr}
  .stat-grid{grid-template-columns:repeat(2,1fr)}
  .table-search input{width:180px}
  .table-search input:focus{width:200px}
}
@media(max-width:640px){
  .stat-grid{grid-template-columns:1fr}
  .page-head{flex-direction:column}
  .table-toolbar{flex-direction:column;align-items:stretch}
  .table-search input{width:100%}
  .table-search input:focus{width:100%}
  .table-scroll table{min-width:560px}
}
</style>
