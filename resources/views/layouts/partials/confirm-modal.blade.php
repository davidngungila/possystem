<div class="modal-backdrop" id="confirmBackdrop" onclick="if(event.target===this)closeConfirmModal()">
    <div class="modal modal-sm">
        <div class="modal-body" style="text-align:center;padding:28px 22px 18px">
            <div class="es-icon" style="background:var(--danger-100);border-color:#e8b4b0;color:var(--danger);margin-bottom:14px">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <h3 id="confirmTitle" style="font-size:15px;font-weight:800;color:var(--coffee-900)">Are you sure?</h3>
            <p id="confirmMsg" style="font-size:13px;color:var(--ink-soft);margin-top:6px;line-height:1.5">This action cannot be undone.</p>
        </div>
        <div class="modal-foot" style="justify-content:center">
            <button type="button" class="btn btn-ghost btn-sm" onclick="closeConfirmModal()">Cancel</button>
            <button type="button" class="btn btn-danger btn-sm" id="confirmBtn">Confirm</button>
        </div>
    </div>
</div>
