<x-layout>
    <div class="x-header-container sub-nav-sticky">
        <div class="container-title">
            <div style="display: flex; flex-direction: column;">
                <span class="page-title">Cheque Management</span>
                <p class="page-links">
                    <a href="{{ route('home') }}">Home</a> |
                    <a href="{{ route('cheque-management') }}">Cheque Management</a> |
                    <span class="page-links-accent">Create Cheque</span>
                </p>
            </div>
        </div>
    </div>

    <style>
        .cqm-form-wrap { max-width: 860px; margin: 0 auto; }
        .cqm-card { background: #fff; border: 1px solid var(--line, #E3E8EF); border-radius: 12px; padding: 26px 28px; }
        .cqm-card-title { font-family: 'Manrope', sans-serif; font-weight: 700; font-size: 18px; margin: 0 0 4px; color: var(--ink, #1f2733); }
        .cqm-card-sub { color: var(--muted, #6b7685); font-size: 13px; margin: 0 0 22px; }
        .cqm-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px 22px; }
        .cqm-field { display: flex; flex-direction: column; gap: 6px; }
        .cqm-field.full { grid-column: 1 / -1; }
        .cqm-label { font-family: 'Manrope', sans-serif; font-size: 13px; font-weight: 600; color: var(--ink, #1f2733); }
        .cqm-label .req { color: var(--danger, #db7788); }
        .cqm-input, .cqm-select {
            height: 44px; border: 1px solid var(--line, #E3E8EF); border-radius: 8px; padding: 0 13px;
            font-family: 'Manrope', sans-serif; font-size: 14px; background: #fff; color: var(--ink, #1f2733); width: 100%;
        }
        .cqm-input:focus, .cqm-select:focus { outline: none; border-color: var(--primary, #427AB5); box-shadow: 0 0 0 3px rgba(66,122,181,0.12); }
        .cqm-input.readonly { background: var(--surface-2, #F7F9FB); color: var(--muted, #6b7685); }
        .cqm-input.auto { background: rgba(66,122,181,0.05); }
        .cqm-hint { font-size: 11.5px; color: var(--muted, #6b7685); }
        .cqm-input.has-error, .cqm-select.has-error { border-color: var(--danger, #db7788); box-shadow: 0 0 0 3px rgba(219,119,136,0.15); }
        .cqm-actions { display: flex; gap: 12px; margin-top: 26px; padding-top: 20px; border-top: 1px solid var(--line, #E3E8EF); }
        .cqm-btn { height: 44px; padding: 0 22px; border-radius: 8px; font-family: 'Manrope', sans-serif; font-weight: 600; font-size: 14px; cursor: pointer; border: 1px solid transparent; }
        .cqm-btn-primary { background: var(--primary, #427AB5); color: #fff; }
        .cqm-btn-primary:hover { background: #355f8f; }
        .cqm-btn-ghost { background: rgba(66,122,181,0.08); border-color: var(--primary, #427AB5); color: var(--primary, #427AB5); }
        .cqm-btn-cancel { background: #fff; border-color: var(--line, #E3E8EF); color: var(--muted, #6b7685); text-decoration: none; display: inline-flex; align-items: center; }
        .cqm-peso { position: relative; }
        .cqm-peso .cqm-input { padding-left: 26px; }
        .cqm-peso::before { content: '₱'; position: absolute; left: 11px; top: 12px; color: var(--muted, #6b7685); font-size: 14px; }
        @media (max-width: 720px) { .cqm-grid { grid-template-columns: 1fr; } }

        /* Bank-account label row + inline add link */
        .cqm-label-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .cqm-add-account-link { border: none; background: none; padding: 0; font-family: 'Manrope', sans-serif; font-size: 12.5px; font-weight: 600; color: var(--primary, #427AB5); cursor: pointer; }
        .cqm-add-account-link:hover { text-decoration: underline; }

        /* Add Bank Account modal */
        .cqm-modal-overlay { position: fixed; inset: 0; z-index: 2000; display: none; align-items: center; justify-content: center; background: rgba(20, 30, 50, .45); padding: 20px; }
        .cqm-modal-overlay.open { display: flex; }
        .cqm-modal { width: 100%; max-width: 440px; background: #fff; border-radius: 14px; box-shadow: 0 20px 50px rgba(16, 32, 56, .25); overflow: hidden; }
        .cqm-modal-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; border-bottom: 1px solid var(--line, #E3E8EF); }
        .cqm-modal-title { font-family: 'Manrope', sans-serif; font-weight: 700; font-size: 16px; margin: 0; }
        .cqm-modal-close { border: none; background: none; font-size: 22px; line-height: 1; color: var(--muted, #6b7685); cursor: pointer; }
        .cqm-modal-body { padding: 20px 22px; display: flex; flex-direction: column; gap: 14px; }
        .cqm-modal-foot { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 22px; border-top: 1px solid var(--line, #E3E8EF); }
    </style>

    <div class="collection-content">
        <div class="cqm-form-wrap">
            <form class="cqm-card" id="chequeForm" method="POST" action="{{ route('cheque-management.store') }}">
                @csrf
                <p class="cqm-card-title">New Cheque</p>
                <p class="cqm-card-sub">Fill the fields, validate, then save. The cheque number is checked for reuse on the selected bank account.</p>

                <div class="cqm-grid">
                    <div class="cqm-field">
                        <div class="cqm-label-row">
                            <label class="cqm-label" for="bank_account_id">Drawer Bank Account <span class="req">*</span></label>
                            <button type="button" class="cqm-add-account-link" id="openAddAccountBtn">＋ Add bank account</button>
                        </div>
                        <select class="cqm-select js-cs" name="bank_account_id" id="bank_account_id">
                            <option value="" disabled selected>Select bank account…</option>
                            @foreach ($bankAccounts as $account)
                                <option value="{{ $account->id }}"
                                    data-account-name="{{ $account->account_name }}"
                                    data-account-number="{{ $account->account_number }}">
                                    {{ $account->bank_name }} · {{ $account->account_number }}
                                </option>
                            @endforeach
                        </select>
                        <span class="cqm-hint" id="accountNumberHint"></span>
                    </div>

                    <div class="cqm-field">
                        <label class="cqm-label" for="account_name_display">Account Name</label>
                        <input type="text" class="cqm-input readonly" id="account_name_display" value="" readonly placeholder="Auto-filled from account">
                    </div>

                    <div class="cqm-field">
                        <label class="cqm-label" for="cheque_date">Cheque Date <span class="req">*</span></label>
                        <input type="date" class="cqm-input" name="cheque_date" id="cheque_date" value="{{ now()->toDateString() }}">
                    </div>

                    <div class="cqm-field">
                        <label class="cqm-label" for="check_number">Check Number <span class="req">*</span></label>
                        <input type="text" class="cqm-input" name="check_number" id="check_number" autocomplete="off" placeholder="e.g. 626888">
                        <span class="cqm-hint">Bank pre-printed number — validated for reuse.</span>
                    </div>

                    <div class="cqm-field full">
                        <label class="cqm-label" for="pay_to_order_of">Pay to the Order of <span class="req">*</span></label>
                        <input type="text" class="cqm-input" name="pay_to_order_of" id="pay_to_order_of" autocomplete="off" placeholder="Payee name">
                    </div>

                    <div class="cqm-field">
                        <label class="cqm-label" for="amount">Amount <span class="req">*</span></label>
                        <div class="cqm-peso">
                            <input type="number" class="cqm-input" name="amount" id="amount" min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>

                    <div class="cqm-field">
                        <label class="cqm-label" for="nature_of_payment">Nature of Payment</label>
                        <input type="text" class="cqm-input" name="nature_of_payment" id="nature_of_payment" autocomplete="off" placeholder="e.g. purchase / withdrawal (optional)">
                    </div>

                    <div class="cqm-field full">
                        <label class="cqm-label" for="amount_in_words">Amount in Words</label>
                        <input type="text" class="cqm-input auto" name="amount_in_words" id="amount_in_words" autocomplete="off">
                        <span class="cqm-hint">Auto-generated from Amount · editable.</span>
                    </div>
                </div>

                <div class="cqm-actions">
                    <a href="{{ route('cheque-management') }}" class="cqm-btn cqm-btn-cancel">Cancel</a>
                    <button type="button" class="cqm-btn cqm-btn-ghost" id="validateBtn">Validate</button>
                    <button type="submit" class="cqm-btn cqm-btn-primary" id="saveBtn">Save Cheque</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Add Bank Account modal --}}
    <div class="cqm-modal-overlay" id="addAccountOverlay">
        <div class="cqm-modal" role="dialog" aria-modal="true" aria-labelledby="addAccountTitle">
            <div class="cqm-modal-head">
                <p class="cqm-modal-title" id="addAccountTitle">Add Bank Account</p>
                <button type="button" class="cqm-modal-close" id="closeAddAccountBtn" aria-label="Close">&times;</button>
            </div>
            <div class="cqm-modal-body">
                <div class="cqm-field">
                    <label class="cqm-label" for="new_bank_name">Bank Name <span class="req">*</span></label>
                    <input type="text" class="cqm-input" id="new_bank_name" autocomplete="off" placeholder="e.g. LBP — Sorsogon Branch">
                </div>
                <div class="cqm-field">
                    <label class="cqm-label" for="new_account_number">Account Number <span class="req">*</span></label>
                    <input type="text" class="cqm-input" id="new_account_number" autocomplete="off" placeholder="e.g. 00782-1019-43">
                </div>
                <div class="cqm-field">
                    <label class="cqm-label" for="new_account_name">Account Name <span class="req">*</span></label>
                    <input type="text" class="cqm-input" id="new_account_name" autocomplete="off" value="Municipality of Prieto Diaz">
                </div>
            </div>
            <div class="cqm-modal-foot">
                <button type="button" class="cqm-btn cqm-btn-cancel" id="cancelAddAccountBtn">Cancel</button>
                <button type="button" class="cqm-btn cqm-btn-primary" id="saveAddAccountBtn">Add Account</button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('chequeForm');
            const select = document.getElementById('bank_account_id');
            const accountName = document.getElementById('account_name_display');
            const accountNumberHint = document.getElementById('accountNumberHint');
            const amount = document.getElementById('amount');
            const words = document.getElementById('amount_in_words');
            let wordsEdited = false;

            // Auto-fill account name from the chosen bank account.
            select.addEventListener('change', function () {
                const opt = select.options[select.selectedIndex];
                accountName.value = opt.dataset.accountName || '';
                accountNumberHint.textContent = opt.dataset.accountNumber ? 'Account No. ' + opt.dataset.accountNumber : '';
                clearError(select);
            });

            // ── Peso amount → words (mirrors Cheque::spellAmount on the server) ──
            const ONES = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten',
                'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
            const TENS = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
            const SCALES = ['', ' thousand', ' million', ' billion', ' trillion'];
            function hundreds(g) {
                let t = '';
                const h = Math.floor(g / 100), r = g % 100;
                if (h) t += ONES[h] + ' hundred';
                if (r) { t += t ? ' ' : ''; if (r < 20) t += ONES[r]; else { t += TENS[Math.floor(r / 10)]; if (r % 10) t += ' ' + ONES[r % 10]; } }
                return t;
            }
            function intWords(n) {
                if (n < 20) return ONES[n];
                const groups = [];
                while (n > 0) { groups.push(n % 1000); n = Math.floor(n / 1000); }
                const parts = [];
                groups.forEach((g, i) => { if (g) parts[i] = hundreds(g) + SCALES[i]; });
                return parts.reverse().filter(Boolean).join(' ').trim();
            }
            function spell(value) {
                const v = Math.round((parseFloat(value) || 0) * 100) / 100;
                const pesos = Math.floor(v), cents = Math.round((v - pesos) * 100);
                const w = pesos === 0 ? 'zero' : intWords(pesos);
                return w.charAt(0).toUpperCase() + w.slice(1) + ' and ' + String(cents).padStart(2, '0') + '/100 pesos';
            }
            function refreshWords() { if (!wordsEdited) words.value = amount.value === '' ? '' : spell(amount.value); }
            amount.addEventListener('input', function () { refreshWords(); clearError(amount); });
            words.addEventListener('input', function () { wordsEdited = words.value.trim() !== ''; });

            // ── Validation ──
            const required = ['bank_account_id', 'cheque_date', 'check_number', 'pay_to_order_of', 'amount'];
            function clearError(el) { el.classList.remove('has-error'); }
            function validate() {
                let ok = true, firstBad = null;
                required.forEach((name) => {
                    const el = form.querySelector('[name="' + name + '"]');
                    const empty = !el.value || el.value.trim() === '';
                    el.classList.toggle('has-error', empty);
                    if (empty && !firstBad) firstBad = el;
                    if (empty) ok = false;
                });
                if (!ok && firstBad) firstBad.focus();
                return ok;
            }
            document.getElementById('validateBtn').addEventListener('click', function () {
                if (validate()) {
                    if (window.showToast) showToast('Looks good', 'All required fields are filled. You can save the cheque.', 'success');
                }
            });

            // ── Save ──
            const saveBtn = document.getElementById('saveBtn');
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!validate()) return;
                saveBtn.disabled = true;
                const original = saveBtn.textContent;
                saveBtn.textContent = 'Saving…';
                fetch(form.action, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: new FormData(form) })
                    .then((r) => r.json().then((d) => ({ ok: r.ok, d })))
                    .then(({ ok, d }) => {
                        if (!ok) {
                            saveBtn.disabled = false;
                            saveBtn.textContent = original;
                            document.getElementById('check_number').classList.add('has-error');
                            if (window.showToast) showToast('Error in adding cheque', d.message || 'Please check the form.', 'error');
                            return;
                        }
                        window.location.href = d.redirect;
                    })
                    .catch(() => {
                        saveBtn.disabled = false;
                        saveBtn.textContent = original;
                        if (window.showToast) showToast('Action could not be completed', 'Something went wrong while saving.', 'error');
                    });
            });
        })();

        // ── Add Bank Account modal ──
        (function () {
            const overlay = document.getElementById('addAccountOverlay');
            const openBtn = document.getElementById('openAddAccountBtn');
            const select = document.getElementById('bank_account_id');
            const bankName = document.getElementById('new_bank_name');
            const accountNumber = document.getElementById('new_account_number');
            const accountName = document.getElementById('new_account_name');
            const saveBtn = document.getElementById('saveAddAccountBtn');
            const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            const close = () => overlay.classList.remove('open');
            openBtn.addEventListener('click', () => { overlay.classList.add('open'); bankName.focus(); });
            document.getElementById('closeAddAccountBtn').addEventListener('click', close);
            document.getElementById('cancelAddAccountBtn').addEventListener('click', close);
            overlay.addEventListener('click', (e) => { if (e.target === overlay) close(); });

            saveBtn.addEventListener('click', function () {
                const fields = [bankName, accountNumber, accountName];
                fields.forEach((el) => el.classList.remove('has-error'));
                let ok = true;
                fields.forEach((el) => { if (!el.value.trim()) { el.classList.add('has-error'); ok = false; } });
                if (!ok) return;

                saveBtn.disabled = true;
                const original = saveBtn.textContent;
                saveBtn.textContent = 'Adding…';
                fetch('/cheque-management/bank-accounts', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() },
                    body: JSON.stringify({
                        bank_name: bankName.value.trim(),
                        account_number: accountNumber.value.trim(),
                        account_name: accountName.value.trim(),
                    }),
                })
                    .then((r) => r.json().then((d) => ({ ok: r.ok, d })))
                    .then(({ ok, d }) => {
                        saveBtn.disabled = false;
                        saveBtn.textContent = original;
                        if (!ok) {
                            const msg = d.errors ? Object.values(d.errors)[0][0] : (d.message || 'Could not add account.');
                            accountNumber.classList.add('has-error');
                            if (window.showToast) showToast('Could not add account', msg, 'error');
                            return;
                        }
                        const opt = document.createElement('option');
                        opt.value = d.id;
                        opt.textContent = d.label;
                        opt.dataset.accountName = d.account_name;
                        opt.dataset.accountNumber = d.account_number;
                        select.appendChild(opt);
                        select.value = d.id;
                        select.dispatchEvent(new Event('change'));
                        bankName.value = '';
                        accountNumber.value = '';
                        close();
                        if (window.showToast) showToast('Bank account added', d.label, 'success');
                    })
                    .catch(() => {
                        saveBtn.disabled = false;
                        saveBtn.textContent = original;
                        if (window.showToast) showToast('Action could not be completed', 'Something went wrong.', 'error');
                    });
            });
        })();
    </script>

    @include('cheque-management.partials.select-enhancer')
</x-layout>
