{{-- Bulk "Record Deposit" flow for the Incoming tab (admin only). Depends on
     .bdr-deposit-check checkboxes in the table + $bankAccounts. --}}
<style>
    .dep-bulkbar { display: none; align-items: center; justify-content: space-between; gap: 12px; margin: 12px 0; padding: 12px 18px; background: var(--primary-soft, rgba(66,122,181,.08)); border: 1px solid var(--primary, #427AB5); border-radius: 10px; }
    .dep-bulkbar.show { display: flex; }
    .dep-bulkbar .count { font-weight: 700; font-size: 13.5px; color: var(--primary, #427AB5); font-family: 'Manrope', sans-serif; }
    .dep-bulkbar .btns { display: flex; gap: 10px; }
    .dep-btn { height: 38px; padding: 0 18px; border-radius: 8px; font-family: 'Manrope', sans-serif; font-weight: 600; font-size: 13.5px; cursor: pointer; border: 1px solid transparent; }
    .dep-btn--go { background: var(--primary, #427AB5); color: #fff; }
    .dep-btn--clear { background: #fff; border-color: var(--line, #E3E8EF); color: var(--muted, #6b7685); }
    .dep-overlay { position: fixed; inset: 0; z-index: 2100; display: none; align-items: center; justify-content: center; background: rgba(20,30,50,.45); padding: 20px; }
    .dep-overlay.open { display: flex; }
    .dep-modal { width: 100%; max-width: 460px; background: #fff; border-radius: 14px; box-shadow: 0 20px 50px rgba(16,32,56,.25); overflow: visible; font-family: 'Manrope', sans-serif; }
    .dep-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: var(--primary, #427AB5); color: #fff; border-radius: 14px 14px 0 0; }
    .dep-head .t { font-weight: 700; font-size: 15px; }
    .dep-head button { border: none; background: none; color: #fff; font-size: 22px; line-height: 1; cursor: pointer; }
    .dep-body { padding: 20px; display: flex; flex-direction: column; gap: 14px; }
    .dep-field { display: flex; flex-direction: column; gap: 6px; }
    .dep-field label { font-size: 12.5px; font-weight: 600; }
    .dep-input { height: 42px; border: 1px solid var(--line, #E3E8EF); border-radius: 8px; padding: 0 12px; font-family: 'Manrope', sans-serif; font-size: 14px; background: #fff; }
    .dep-summary { background: var(--surface-2, #F7F9FB); border: 1px solid var(--line, #E3E8EF); border-radius: 8px; padding: 12px 14px; font-size: 13.5px; display: flex; justify-content: space-between; }
    .dep-summary b { font-variant-numeric: tabular-nums; }
    .dep-foot { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 20px; border-top: 1px solid var(--line, #E3E8EF); }
</style>

<div class="dep-bulkbar" id="depBulkBar">
    <span class="count" id="depCount">0 selected · ₱0.00</span>
    <div class="btns">
        <button type="button" class="dep-btn dep-btn--go" id="depRecordBtn">Record Deposit</button>
        <button type="button" class="dep-btn dep-btn--clear" id="depClearBtn">Clear</button>
    </div>
</div>

<div class="dep-overlay" id="depOverlay">
    <div class="dep-modal" role="dialog" aria-modal="true" aria-labelledby="depTitle">
        <div class="dep-head"><span class="t" id="depTitle">Record Deposit</span><button type="button" id="depClose" aria-label="Close">&times;</button></div>
        <div class="dep-body">
            <div class="dep-summary"><span id="depSummaryCount">0 collection(s)</span><b id="depSummaryTotal">₱ 0.00</b></div>
            <div class="dep-field">
                <label for="dep_bank_account_id">Deposit To (Bank Account)</label>
                <select class="dep-input js-cs" id="dep_bank_account_id" name="bank_account_id">
                    <option value="" disabled selected>Select bank account…</option>
                    @foreach ($bankAccounts as $account)
                        <option value="{{ $account->id }}">{{ $account->bank_name }} · {{ $account->account_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="dep-field">
                <label for="dep_date">Deposit Date</label>
                <input type="date" class="dep-input" id="dep_date" value="{{ now()->toDateString() }}">
            </div>
            <div class="dep-field">
                <label for="dep_slip">Deposit Slip No. <span style="color:var(--muted,#6b7685); font-weight:400;">(optional)</span></label>
                <input type="text" class="dep-input" id="dep_slip" autocomplete="off" placeholder="e.g. DS-0001">
            </div>
        </div>
        <div class="dep-foot">
            <button type="button" class="dep-btn dep-btn--clear" id="depCancel">Cancel</button>
            <button type="button" class="dep-btn dep-btn--go" id="depConfirm">Confirm Deposit</button>
        </div>
    </div>
</div>

<script>
    (function () {
        const container = document.getElementById('bdr-table-container');
        const bar = document.getElementById('depBulkBar');
        const count = document.getElementById('depCount');
        const overlay = document.getElementById('depOverlay');
        const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        const peso = (n) => '₱ ' + Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function selected() { return [...container.querySelectorAll('.bdr-deposit-check:checked')]; }
        function refreshBar() {
            const sel = selected();
            const total = sel.reduce((s, c) => s + parseFloat(c.dataset.amount || 0), 0);
            count.textContent = `${sel.length} selected · ${peso(total)}`;
            bar.classList.toggle('show', sel.length > 0);
        }

        container.addEventListener('change', function (e) {
            if (e.target.id === 'selectAllDepositable') {
                container.querySelectorAll('.bdr-deposit-check').forEach((c) => { c.checked = e.target.checked; });
            }
            if (e.target.classList.contains('bdr-deposit-check') || e.target.id === 'selectAllDepositable') refreshBar();
        });
        // Table reloads (search/sort) wipe the selection — reset the bar.
        new MutationObserver(refreshBar).observe(container, { childList: true });

        document.getElementById('depClearBtn').addEventListener('click', function () {
            container.querySelectorAll('.bdr-deposit-check, #selectAllDepositable').forEach((c) => { c.checked = false; });
            refreshBar();
        });

        function openModal() {
            const sel = selected();
            if (!sel.length) return;
            const total = sel.reduce((s, c) => s + parseFloat(c.dataset.amount || 0), 0);
            document.getElementById('depSummaryCount').textContent = `${sel.length} collection(s)`;
            document.getElementById('depSummaryTotal').textContent = peso(total);
            overlay.classList.add('open');
        }
        const close = () => overlay.classList.remove('open');
        document.getElementById('depRecordBtn').addEventListener('click', openModal);
        document.getElementById('depClose').addEventListener('click', close);
        document.getElementById('depCancel').addEventListener('click', close);
        overlay.addEventListener('click', (e) => { if (e.target === overlay) close(); });

        document.getElementById('depConfirm').addEventListener('click', function () {
            const bank = document.getElementById('dep_bank_account_id');
            const date = document.getElementById('dep_date');
            if (!bank.value) { bank.focus(); return; }
            const ids = selected().map((c) => parseInt(c.dataset.id));
            const btn = this;
            btn.disabled = true;
            fetch('/bank-deposit-reconciliation/deposits', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() },
                body: JSON.stringify({ ids, bank_account_id: bank.value, deposit_date: date.value, slip_number: document.getElementById('dep_slip').value }),
            })
                .then((r) => r.json().then((d) => ({ ok: r.ok, d })))
                .then(({ ok, d }) => {
                    btn.disabled = false;
                    if (!ok) { if (window.showToast) showToast('Could not record deposit', d.message || 'Please try again.', 'error'); return; }
                    close();
                    if (window.showToast) showToast('Deposit recorded', d.message, 'success');
                    document.getElementById('bdr-search-form').dispatchEvent(new Event('submit'));
                })
                .catch(() => { btn.disabled = false; if (window.showToast) showToast('Action could not be completed', 'Something went wrong.', 'error'); });
        });
    })();
</script>
