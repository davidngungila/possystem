<style>
.page-loader{position:fixed;inset:0;background:rgba(251,247,239,.92);backdrop-filter:blur(6px);display:flex;flex-direction:column;align-items:center;justify-content:center;z-index:9999;transition:opacity .35s, visibility .35s;}
.page-loader.hidden{opacity:0;visibility:hidden;pointer-events:none;}
.loader-logo{position:relative;width:88px;height:88px;display:flex;align-items:center;justify-content:center;}
.loader-ring{position:absolute;inset:0;border:4px solid var(--line);border-top-color:var(--terracotta-600);border-right-color:var(--gold-500);border-bottom-color:var(--acacia-600);border-radius:50%;animation:spin 1s linear infinite;}
.loader-mark{width:56px;height:56px;border-radius:14px;background:linear-gradient(155deg,var(--terracotta-600),var(--gold-500));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:24px;box-shadow:0 4px 16px rgba(194,89,43,.35);}
.loader-text{margin-top:16px;font-size:12px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:var(--coffee-700);}
.loader-sub{margin-top:6px;font-size:12px;color:var(--ink-soft);}
@keyframes spin{to{transform:rotate(360deg)}}
</style>
@php
    $loaderLogo = \App\Models\Setting::getValue('shop_logo', \App\Models\Setting::getValue('university_logo'));
    $loaderName = \App\Models\Setting::getValue('shop_name', \App\Models\Setting::getValue('university_name','SHOP POS'));
    $loaderAcro = substr(\App\Models\Setting::getValue('shop_acronym', \App\Models\Setting::getValue('university_acronym','SP')),0,2);
@endphp
<div id="pageLoader" class="page-loader" aria-hidden="true">
    <div class="loader-logo">
        <div class="loader-ring"></div>
        <div class="loader-mark" style="overflow:hidden;@if($loaderLogo)background:#fff;padding:4px;@endif">@if($loaderLogo)<img src="{{ asset($loaderLogo) }}" style="width:100%;height:100%;object-fit:contain;border-radius:10px;" alt="Logo">@else {{ $loaderAcro }} @endif</div>
    </div>
    <div class="loader-text">{{ $loaderName }}</div>
    <div class="loader-sub">Point of Sale system — Loading…</div>
</div>
<script>
(function(){
    const loader = document.getElementById('pageLoader');
    function hideLoader(){ if(loader) loader.classList.add('hidden'); }
    function showLoader(){ if(loader) loader.classList.remove('hidden'); }
    window.addEventListener('load', ()=> setTimeout(hideLoader, 400));
    if(document.readyState === 'complete') setTimeout(hideLoader, 400);
    else document.addEventListener('DOMContentLoaded', ()=> setTimeout(hideLoader, 800));
    document.addEventListener('click', e=>{
        const a = e.target.closest('a[href]');
        if(a && a.href && !a.target && !a.href.startsWith('javascript:') && !a.href.startsWith('#') && !a.hasAttribute('download') && a.origin === location.origin){
            if(a.href !== location.href) showLoader();
        }
    });
    document.addEventListener('submit', e=>{
        if(e.defaultPrevented) return;
        const f = e.target;
        if(f.tagName === 'FORM' && !f.hasAttribute('data-no-loader')){
            if(document.querySelector('.modal-backdrop.show')) return;
            showLoader();
        }
    });
    const origSubmit = HTMLFormElement.prototype.submit;
    HTMLFormElement.prototype.submit = function(){
        if(!this.hasAttribute('data-no-loader') && !document.querySelector('.modal-backdrop.show')){
            showLoader();
        }
        return origSubmit.call(this);
    };
    window.addEventListener('beforeunload', showLoader);
    window.showPageLoader = showLoader;
    window.hidePageLoader = hideLoader;
})();
</script>
