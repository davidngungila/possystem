{{-- Internal CMS JS: toast, modal, confirm, sidebar, idle logout --}}
<script>
function toast(msg, type='info'){
  const host=document.getElementById('toastHost');
  if(!host) return;
  const svgIcons={
    success:'<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>',
    error:'<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
    info:'<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
    warning:'<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'
  };
  const el=document.createElement('div');
  el.className='toast '+(type||'info');
  el.style.display='flex'; el.style.alignItems='center'; el.style.gap='10px';
  const ic=document.createElement('span');
  ic.innerHTML=svgIcons[type]||svgIcons.info;
  ic.style.width='22px'; ic.style.height='22px'; ic.style.borderRadius='50%'; ic.style.display='inline-flex'; ic.style.alignItems='center'; ic.style.justifyContent='center'; ic.style.flex='none';
  if(type==='success'){ ic.style.background='var(--acacia-600)'; ic.style.color='#fff'; }
  else if(type==='error'){ ic.style.background='var(--danger)'; ic.style.color='#fff'; }
  else if(type==='warning'){ ic.style.background='var(--gold-500)'; ic.style.color='#fff'; }
  else { ic.style.background='var(--coffee-700)'; ic.style.color='#fff'; }
  const tx=document.createElement('span'); tx.textContent=msg; tx.style.flex='1';
  const close=document.createElement('button'); close.innerHTML='<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>'; close.style.background='transparent'; close.style.border='none'; close.style.color='rgba(255,255,255,.7)'; close.style.cursor='pointer'; close.style.lineHeight='1'; close.style.display='inline-flex'; close.style.alignItems='center'; close.onclick=()=>{ el.classList.add('out'); setTimeout(()=>el.remove(),300); };
  el.appendChild(ic); el.appendChild(tx); el.appendChild(close);
  host.appendChild(el);
  // progress bar
  const bar=document.createElement('div'); bar.style.position='absolute'; bar.style.left='0'; bar.style.bottom='0'; bar.style.height='3px'; bar.style.background='rgba(255,255,255,.85)'; bar.style.width='100%'; bar.style.borderRadius='0 0 12px 12px'; bar.style.transition='width 3.6s linear';
  el.style.position='relative'; el.style.overflow='hidden'; el.appendChild(bar);
  requestAnimationFrame(()=>{ bar.style.width='0%'; });
  setTimeout(()=>{ el.classList.add('out'); setTimeout(()=>el.remove(),300); }, 3600);
}
function openModal(id){
  const el=document.getElementById(id);
  if(!el) return;
  el.classList.add('show');
  document.body.style.overflow='hidden';
}
function closeModal(id){
  const el=document.getElementById(id);
  if(!el) return;
  el.classList.remove('show');
  if(!document.querySelector('.modal-backdrop.show')) document.body.style.overflow='';
}
function confirmModal(title, msg, onConfirm){
  const t=document.getElementById('confirmTitle');
  const m=document.getElementById('confirmMsg');
  const b=document.getElementById('confirmBtn');
  if(t) t.textContent=title||'Are you sure?';
  if(m) m.textContent=msg||'This action cannot be undone.';
  if(b) b.onclick=function(){ closeConfirmModal(); if(typeof onConfirm==='function') onConfirm(); };
  openModal('confirmBackdrop');
}
function closeConfirmModal(){ closeModal('confirmBackdrop'); }
function toggleSidebar(){
  if(window.innerWidth <= 900){
    document.getElementById('sidebar')?.classList.toggle('mobile-open');
    document.getElementById('mobileOverlay')?.classList.toggle('show');
  } else {
    const sb=document.getElementById('sidebar');
    sb?.classList.toggle('collapsed');
    if(sb) localStorage.setItem('sbCollapsed', sb.classList.contains('collapsed') ? '1' : '0');
  }
}
function closeMobile(){
  document.getElementById('sidebar')?.classList.remove('mobile-open');
  document.getElementById('mobileOverlay')?.classList.remove('show');
}
function toggleSbDrop(btn){
  const drop = btn.closest('.sb-drop');
  if(!drop) return;
  const willOpen = !drop.classList.contains('open');
  document.querySelectorAll('.sb-nav .sb-drop').forEach(d=> d.classList.remove('open'));
  if(willOpen) drop.classList.add('open');
  const arr=[];
  document.querySelectorAll('.sb-nav .sb-drop.open').forEach(d=>{
    const i=[...document.querySelectorAll('.sb-nav .sb-drop')].indexOf(d);
    if(i>=0) arr.push(i);
  });
  sessionStorage.setItem('sbDrops', JSON.stringify(arr));
}
(function(){
  const idleMax=10*60*1000; let t;
  function reset(){ clearTimeout(t); t=setTimeout(()=>{ document.getElementById('idleLogoutForm')?.submit(); }, idleMax); }
  ['click','keydown','mousemove','touchstart','scroll'].forEach(ev=>window.addEventListener(ev, reset, {passive:true}));
  reset();
})();
document.addEventListener('DOMContentLoaded', function(){
  const s=document.getElementById('tableSearch');
  if(s){
    s.addEventListener('input', function(){
      const q=this.value.toLowerCase().trim();
      document.querySelectorAll('.table-scroll tbody tr').forEach(tr=>{
        if(!q){ tr.style.display=''; return; }
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  }
  // chip filter: data-filter on rows via data-status / data-cat etc — generic: chip has data-filter, row has data-filter-value
  document.querySelectorAll('.chip[data-filter]').forEach(chip=>{
    chip.addEventListener('click', function(){
      document.querySelectorAll('.chip[data-filter]').forEach(c=>c.classList.remove('active'));
      this.classList.add('active');
      const f=this.getAttribute('data-filter');
      document.querySelectorAll('.table-scroll tbody tr').forEach(tr=>{
        if(f==='all'){ tr.style.display=''; return; }
        const v=(tr.getAttribute('data-filter')||'').toLowerCase();
        tr.style.display = (v===f || v.includes(f)) ? '' : 'none';
      });
      const si=document.getElementById('tableSearch');
      if(si) si.value='';
    });
  });
  // Sidebar scroll persistence
  const sbNav = document.querySelector('.sb-nav');
  if(sbNav){
    const saved = sessionStorage.getItem('sbScroll');
    if(saved) sbNav.scrollTop = parseInt(saved, 10);
    let sbTimer;
    sbNav.addEventListener('scroll', ()=>{
      clearTimeout(sbTimer);
      sbTimer = setTimeout(()=> sessionStorage.setItem('sbScroll', sbNav.scrollTop), 60);
    });
  }
  // Sidebar collapsed state persistence across refresh
  if(window.innerWidth > 900 && localStorage.getItem('sbCollapsed') === '1'){
    document.getElementById('sidebar')?.classList.add('collapsed');
  }
  // Sidebar dropdown group open-state persistence - single open (accordion)
  const sbDrops = document.querySelectorAll('.sb-nav .sb-drop');
  if(sbDrops.length){
    let savedDrops=[];
    try{ savedDrops=JSON.parse(sessionStorage.getItem('sbDrops')||'[]'); }catch(e){}
    if(savedDrops.length > 1) savedDrops = [savedDrops[0]];
    if(savedDrops.length === 1){
      sbDrops.forEach(d=> d.classList.remove('open'));
      const idx = savedDrops[0];
      if(sbDrops[idx]) sbDrops[idx].classList.add('open');
    } else {
      const opens = document.querySelectorAll('.sb-nav .sb-drop.open');
      if(opens.length > 1){
        opens.forEach((d,i)=>{ if(i>0) d.classList.remove('open'); });
      }
    }
  }
  // Back button to logout (block navigating back into previous screens)
  const backGuardForm = document.getElementById('idleLogoutForm');
  if(backGuardForm){
    history.pushState({backGuard:true}, '', location.href);
    window.addEventListener('popstate', function(){
      backGuardForm.submit();
    });
  }
});
</script>
