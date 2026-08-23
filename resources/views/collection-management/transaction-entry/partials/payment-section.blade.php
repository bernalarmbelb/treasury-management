{{-- Reusable collection payment capture. Inputs map 1:1 to transaction_logs columns.
     Include inside each entry <form>, then also @include('partials.select-enhancer'). --}}
<style>
    .pay-sec { margin-top: 16px; }
    .pay-bar { background: var(--primary, #427AB5); color: #fff; padding: 8px 14px; border-radius: 8px 8px 0 0; font-weight: 600; font-size: 13px; font-family: 'Manrope', sans-serif; }
    .pay-body { border: 1px solid var(--line, #E3E8EF); border-top: none; border-radius: 0 0 8px 8px; padding: 16px; display: flex; flex-direction: column; gap: 14px; }
    .pay-field { display: flex; flex-direction: column; gap: 6px; }
    .pay-field label { font-size: 12.5px; font-weight: 600; font-family: 'Manrope', sans-serif; }
    .pay-input { height: 42px; border: 1px solid var(--line, #E3E8EF); border-radius: 8px; padding: 0 12px; font-family: 'Manrope', sans-serif; font-size: 14px; background: #fff; }
    .pay-group { display: none; flex-direction: column; gap: 14px; }
    .pay-group.active { display: flex; }
    .pay-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    @media (max-width: 640px) { .pay-row { grid-template-columns: 1fr; } }
</style>

<div class="pay-sec">
    <div class="pay-bar">Payment</div>
    <div class="pay-body">
        <div class="pay-field">
            <label for="payment_method">Payment Method</label>
            <select id="payment_method" name="payment_method" class="pay-input js-cs">
                <option value="cash" selected>Cash</option>
                <option value="cheque">Cheque</option>
                <option value="online">Online</option>
                <option value="money_order">Money Order</option>
            </select>
        </div>

        {{-- Cheque --}}
        <div class="pay-group" data-method="cheque">
            <div class="pay-row">
                <div class="pay-field"><label>Bank Name</label><input type="text" class="pay-input" name="payer_bank_name" autocomplete="off"></div>
                <div class="pay-field"><label>Cheque Number</label><input type="text" class="pay-input" name="payment_reference" autocomplete="off"></div>
            </div>
            <div class="pay-field"><label>Cheque Date</label><input type="date" class="pay-input" name="payment_reference_date"></div>
        </div>

        {{-- Online --}}
        <div class="pay-group" data-method="online">
            <div class="pay-row">
                <div class="pay-field">
                    <label>Channel</label>
                    <select class="pay-input js-cs" name="payment_channel">
                        <option value="" disabled selected>Select channel…</option>
                        <option value="GCash">GCash</option>
                        <option value="LandBank Link.BizPortal">LandBank Link.BizPortal</option>
                        <option value="Maya">Maya</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="pay-field"><label>Reference Number</label><input type="text" class="pay-input" name="payment_reference" autocomplete="off"></div>
            </div>
            <div class="pay-field"><label>Date</label><input type="date" class="pay-input" name="payment_reference_date"></div>
        </div>

        {{-- Money Order --}}
        <div class="pay-group" data-method="money_order">
            <div class="pay-row">
                <div class="pay-field"><label>Money Order Number</label><input type="text" class="pay-input" name="payment_reference" autocomplete="off"></div>
                <div class="pay-field"><label>Date</label><input type="date" class="pay-input" name="payment_reference_date"></div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const method = document.getElementById('payment_method');
        if (!method) return;
        const groups = document.querySelectorAll('.pay-group');
        // Only the active method's inputs submit their names; disable the rest so the
        // duplicate name="payment_reference" fields don't clobber each other.
        function apply() {
            const val = method.value;
            groups.forEach((g) => {
                const on = g.dataset.method === val;
                g.classList.toggle('active', on);
                g.querySelectorAll('input, select').forEach((el) => { el.disabled = !on; });
            });
        }
        method.addEventListener('change', apply);
        apply();
    })();
</script>
