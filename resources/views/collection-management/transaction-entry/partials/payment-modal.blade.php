{{-- Reusable payment modal. Include INSIDE the entry <form> so its inputs submit
     with FormData(form). Flow: on Proceed, call
     window.cqmOpenPaymentModal(onConfirm); the modal captures the payment, and
     onConfirm() runs after the user clicks "Confirm Payment" (e.g. open the
     print-preview modal). Input names map 1:1 to transaction_logs columns. --}}
<style>
    .pm-overlay { position: fixed; inset: 0; z-index: 2100; display: none; align-items: center; justify-content: center; background: rgba(20,30,50,.45); padding: 20px; }
    .pm-overlay.open { display: flex; }
    /* overflow visible so the custom dropdown menu isn't clipped by the modal edge */
    .pm-modal { width: 100%; max-width: 460px; background: #fff; border-radius: 14px; box-shadow: 0 20px 50px rgba(16,32,56,.25); overflow: visible; font-family: 'Manrope', sans-serif; }
    .pm-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: var(--primary, #427AB5); color: #fff; border-radius: 14px 14px 0 0; }
    .pm-head .t { font-weight: 700; font-size: 15px; }
    .pm-head button { border: none; background: none; color: #fff; font-size: 22px; line-height: 1; cursor: pointer; }
    .pm-body { padding: 20px; display: flex; flex-direction: column; gap: 14px; }
    .pm-field { display: flex; flex-direction: column; gap: 6px; }
    .pm-field label { font-size: 12.5px; font-weight: 600; }
    .pm-input { height: 42px; border: 1px solid var(--line, #E3E8EF); border-radius: 8px; padding: 0 12px; font-family: 'Manrope', sans-serif; font-size: 14px; background: #fff; }
    .pm-input.has-error { border-color: var(--danger, #db7788); box-shadow: 0 0 0 3px rgba(219,119,136,.15); }
    .pm-group { display: none; flex-direction: column; gap: 14px; }
    .pm-group.active { display: flex; }
    .pm-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .pm-foot { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 20px; border-top: 1px solid var(--line, #E3E8EF); border-radius: 0 0 14px 14px; background: #fff; }
    .pm-btn { height: 42px; padding: 0 20px; border-radius: 8px; font-family: 'Manrope', sans-serif; font-weight: 600; font-size: 14px; cursor: pointer; border: 1px solid transparent; }
    .pm-btn-cancel { background: #fff; border-color: var(--line, #E3E8EF); color: var(--muted, #6b7685); }
    .pm-btn-confirm { background: var(--primary, #427AB5); color: #fff; }
    .pm-btn-confirm:hover { background: #355f8f; }
    @media (max-width: 520px) { .pm-row { grid-template-columns: 1fr; } }
</style>

<div class="pm-overlay" id="paymentModalOverlay">
    <div class="pm-modal" role="dialog" aria-modal="true" aria-labelledby="pmTitle">
        <div class="pm-head">
            <span class="t" id="pmTitle">Payment</span>
            <button type="button" id="paymentModalClose" aria-label="Close">&times;</button>
        </div>
        <div class="pm-body">
            <div class="pm-field">
                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method" class="pm-input js-cs">
                    <option value="cash" selected>Cash</option>
                    <option value="cheque">Cheque</option>
                    <option value="online">Online</option>
                    <option value="money_order">Money Order</option>
                </select>
            </div>

            <div class="pm-group" data-method="cheque">
                <div class="pm-row">
                    <div class="pm-field"><label>Bank Name</label><input type="text" class="pm-input" name="payer_bank_name" autocomplete="off"></div>
                    <div class="pm-field"><label>Cheque Number</label><input type="text" class="pm-input" name="payment_reference" autocomplete="off"></div>
                </div>
                <div class="pm-field"><label>Cheque Date</label><input type="date" class="pm-input" name="payment_reference_date"></div>
            </div>

            <div class="pm-group" data-method="online">
                <div class="pm-row">
                    <div class="pm-field">
                        <label>Channel</label>
                        <select class="pm-input js-cs" name="payment_channel">
                            <option value="" disabled selected>Select channel…</option>
                            <option value="GCash">GCash</option>
                            <option value="LandBank Link.BizPortal">LandBank Link.BizPortal</option>
                            <option value="Maya">Maya</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="pm-field"><label>Reference Number</label><input type="text" class="pm-input" name="payment_reference" autocomplete="off"></div>
                </div>
                <div class="pm-field"><label>Date</label><input type="date" class="pm-input" name="payment_reference_date"></div>
            </div>

            <div class="pm-group" data-method="money_order">
                <div class="pm-row">
                    <div class="pm-field"><label>Money Order Number</label><input type="text" class="pm-input" name="payment_reference" autocomplete="off"></div>
                    <div class="pm-field"><label>Date</label><input type="date" class="pm-input" name="payment_reference_date"></div>
                </div>
            </div>
        </div>
        <div class="pm-foot">
            <button type="button" class="pm-btn pm-btn-cancel" id="paymentModalCancel">Cancel</button>
            <button type="button" class="pm-btn pm-btn-confirm" id="paymentModalConfirm">Confirm Payment</button>
        </div>
    </div>
</div>

<script>
    (function () {
        const overlay = document.getElementById('paymentModalOverlay');
        const method = document.getElementById('payment_method');
        if (!overlay || !method) return;
        const groups = overlay.querySelectorAll('.pm-group');
        let onConfirm = null;

        function apply() {
            const val = method.value;
            groups.forEach((g) => {
                const on = g.dataset.method === val;
                g.classList.toggle('active', on);
                // Disable inactive groups so duplicate name="payment_reference" fields don't clash.
                g.querySelectorAll('input, select').forEach((el) => { el.disabled = !on; });
            });
        }
        method.addEventListener('change', apply);
        apply();

        function activeInputs() {
            const g = overlay.querySelector(`.pm-group[data-method="${method.value}"]`);
            return g ? [...g.querySelectorAll('input, select')] : [];
        }
        function validate() {
            let ok = true;
            activeInputs().forEach((el) => {
                el.classList.remove('has-error');
                if (!el.value || !el.value.trim()) { el.classList.add('has-error'); ok = false; }
            });
            return ok; // cash has no active inputs -> always ok
        }

        const close = () => overlay.classList.remove('open');
        document.getElementById('paymentModalClose').addEventListener('click', close);
        document.getElementById('paymentModalCancel').addEventListener('click', close);
        overlay.addEventListener('click', (e) => { if (e.target === overlay) close(); });

        document.getElementById('paymentModalConfirm').addEventListener('click', function () {
            if (!validate()) return;
            close();
            if (typeof onConfirm === 'function') onConfirm();
        });

        // Open the payment modal; run `cb` after the user confirms payment.
        window.cqmOpenPaymentModal = function (cb) {
            onConfirm = cb || null;
            overlay.classList.add('open');
        };
    })();
</script>
