<x-layout>
    <div class="x-header-container sub-nav-sticky">
        <div class="container-title">
            <div style="display: flex; flex-direction: column;">
                <span class="page-title">Collection Management</span>
                <p class="page-links">
                    <a href="{{ route('home') }}">Home</a> |
                    <a href="{{ route('collections') }}">Collections Management</a> |
                    <a href="{{ route('transaction-entry') }}">Transactions Entry</a> |
                    <span class="page-links-accent">Corporation Cedula {{ $form->form_code }}</span>
                </p>
            </div>
        </div>
        <div class="ctc-tabs-row">
            <a href="{{ route('transaction-entry') }}" class="ctc-tab">Transactions Log</a>
            <div class="ctc-tab active">New Entry</div>
        </div>
    </div>

    <div class="collection-content">
        <form class="ctc-page ctc-page--corporation" id="ctcForm" method="POST" action="{{ route('transaction-entry.corporation-cedula.store', $form->id, false) }}" style="height: 960px;">
            @csrf
            <div class="ctc-field ctc-field--title" style="left:40px; top:40px; width:438px; height:42px;">
                <p>Community Tax Certificate</p>
            </div>
            <div class="ctc-field ctc-badge ctc-badge--corporation" style="left:478px; top:40px; width:172px; height:42px;">
                <p>Corporation</p>
            </div>
            <div class="ctc-field ctc-cert-no" style="left:650px; top:40px; width:272px; height:58px;">
                <input type="text" class="ctc-cert-no-prefix-input" id="ctc-cert-prefix" name="certificate_prefix" value="{{ $nextSerialPrefix ?? 'CCC2021' }}">
                <input type="text" class="ctc-cert-no-input" id="ctc-cert-no" name="certificate_number" value="{{ $nextSerialNumber ?? '00259338' }}">
            </div>
            <div class="ctc-field ctc-divider" style="left:650px; top:98px; width:272px; height:5px;"></div>
            <div class="ctc-field ctc-copy-label" style="left:650px; top:103px; width:272px; height:21px;">
                <p>Taxpayer's Copy</p>
            </div>

            <div class="ctc-field ctc-input-wrap" style="left:40px; top:82px; width:69px; height:42px;">
                <input type="number" class="ctc-input" id="ctc-year" name="year" value="{{ now()->year }}" placeholder=" ">
                <label class="ctc-input-caption" for="ctc-year">Year</label>
            </div>
            <div class="ctc-field ctc-input-wrap" style="left:109px; top:82px; width:369px; height:42px;">
                <input type="text" class="ctc-input" id="ctc-place-of-issue" name="place_of_issue" placeholder=" ">
                <label class="ctc-input-caption" for="ctc-place-of-issue">Place of Issue (City/Mun./Prov.)</label>
            </div>
            <div class="ctc-field ctc-input-wrap ctc-date" style="left:478px; top:82px; width:172px; height:42px;">
                <input type="date" class="ctc-input" id="ctc-date-issued" name="date_issued">
                <label class="ctc-input-caption" for="ctc-date-issued">Date Issued</label>
            </div>

            <div class="ctc-field ctc-input-wrap" style="left:40px; top:124px; width:610px; height:42px;">
                <input type="text" class="ctc-input" id="ctc-company-name" name="company_name" placeholder=" ">
                <label class="ctc-input-caption" for="ctc-company-name">Company's Full Name</label>
            </div>

            <div class="ctc-field ctc-field--tin-compact" style="left:650px; top:124px; width:272px; height:42px;">
                <p class="ctc-tin-label">TIN (if Any)</p>
                <div class="ctc-tin-group">
                    @for ($group = 0; $group < 5; $group++)
                        <div class="ctc-tin-cell-group">
                            @for ($cell = 0; $cell < 3; $cell++)
                                @php $tinIndex = $group * 3 + $cell; @endphp
                                <input type="text" inputmode="numeric" maxlength="1" class="ctc-tin-cell" name="tin[]" data-tin-index="{{ $tinIndex }}">
                            @endfor
                        </div>
                    @endfor
                </div>
            </div>

            <div class="ctc-field ctc-input-wrap" style="left:40px; top:166px; width:700px; height:42px;">
                <input type="text" class="ctc-input" id="ctc-address" name="address" placeholder=" ">
                <label class="ctc-input-caption" for="ctc-address">Address of Principal Place of Business</label>
            </div>
            <div class="ctc-field ctc-input-wrap ctc-date" style="left:740px; top:166px; width:182px; height:84px;">
                <input type="date" class="ctc-input" id="ctc-date-of-registration" name="date_of_registration">
                <label class="ctc-input-caption ctc-caption-multiline" for="ctc-date-of-registration">Date of registration<br>/ incorporation</label>
            </div>

            <div class="ctc-field ctc-civil-row" style="left:40px; top:208px; width:700px; height:42px;">
                <p class="ctc-radio-label">Kind of<br>Organization</p>
                <label class="ctc-radio-group">
                    <input type="radio" name="kind_of_organization" class="ctc-radio" value="Corporation">
                    <p>Corporation</p>
                </label>
                <label class="ctc-radio-group">
                    <input type="radio" name="kind_of_organization" class="ctc-radio" value="Association">
                    <p>Association</p>
                </label>
                <label class="ctc-radio-group">
                    <input type="radio" name="kind_of_organization" class="ctc-radio" value="Partnership">
                    <p>Partnership</p>
                </label>
            </div>

            <div class="ctc-field ctc-input-wrap" style="left:40px; top:250px; width:610px; height:42px;">
                <input type="text" class="ctc-input" id="ctc-nature-of-business" name="nature_of_business" placeholder=" ">
                <label class="ctc-input-caption" for="ctc-nature-of-business">Kind / Nature of Business</label>
            </div>
            <div class="ctc-field ctc-col-header" style="left:650px; top:250px; width:136px; height:42px;">
                <div>
                    <p>Taxable</p>
                    <p>amount</p>
                </div>
            </div>
            <div class="ctc-field ctc-col-header" style="left:786px; top:250px; width:136px; height:42px;">
                <div>
                    <p>Community</p>
                    <p>Tax due</p>
                </div>
            </div>

            <div class="ctc-field ctc-tax-row" style="left:40px; top:292px; width:610px; height:42px;">
                <p>A. Basic Community Tax (&#8369; 5.00)</p>
            </div>
            <div class="ctc-field ctc-cell-grey" style="left:650px; top:292px; width:136px; height:42px;"></div>
            <div class="ctc-field ctc-amount-cell" style="left:786px; top:292px; width:136px; height:42px;">
                <span class="ctc-peso-prefix">&#8369;</span>
                <input type="number" step="0.01" min="0" class="ctc-amount-input" id="ctc-a-ctd" name="a_community_tax_due" placeholder="0.00">
            </div>

            <div class="ctc-field ctc-tax-row" style="left:40px; top:334px; width:610px; height:42px;">
                <p>B. Additional Community Tax ( Tax not to exceed &#8369; 10,000.00 )</p>
            </div>
            <div class="ctc-field ctc-cell-grey" style="left:650px; top:334px; width:136px; height:42px;"></div>
            <div class="ctc-field ctc-cell-grey" style="left:786px; top:334px; width:136px; height:42px;"></div>

            <div class="ctc-field ctc-tax-item" style="left:40px; top:376px; width:610px; height:42px;">
                <ol start="1">
                    <li>Assessed value of real property owned in the Philippines (&#8369;2.00 for every &#8369;5,000.00)</li>
                </ol>
            </div>
            <div class="ctc-field ctc-amount-cell" style="left:650px; top:376px; width:136px; height:42px;">
                <span class="ctc-peso-prefix">&#8369;</span>
                <input type="number" step="0.01" min="0" class="ctc-amount-input" id="ctc-1-taxable" name="item1_taxable_amount" placeholder="0.00">
            </div>
            <div class="ctc-field ctc-amount-cell" style="left:786px; top:376px; width:136px; height:42px;">
                <input type="number" step="0.01" min="0" class="ctc-amount-input" id="ctc-1-ctd" name="item1_community_tax_due" placeholder="0.00">
            </div>

            <div class="ctc-field ctc-tax-item" style="left:40px; top:418px; width:610px; height:42px;">
                <ol start="2">
                    <li>Gross receipts, including dividends / Earnings derived from business in the Philippines during the preceding year (&#8369;2.00 for every &#8369;5,000.00)</li>
                </ol>
            </div>
            <div class="ctc-field ctc-amount-cell" style="left:650px; top:418px; width:136px; height:42px;">
                <input type="number" step="0.01" min="0" class="ctc-amount-input" id="ctc-2-taxable" name="item2_taxable_amount" placeholder="0.00">
            </div>
            <div class="ctc-field ctc-amount-cell" style="left:786px; top:418px; width:136px; height:42px;">
                <input type="number" step="0.01" min="0" class="ctc-amount-input" id="ctc-2-ctd" name="item2_community_tax_due" placeholder="0.00">
            </div>

            <div class="ctc-field ctc-signature-box" style="left:40px; top:460px; width:610px; height:84px;">
                <p>Signature / Position of Authorized Officer</p>
            </div>
            <div class="ctc-field ctc-cell-label" style="left:650px; top:460px; width:136px; height:42px;">
                Total
            </div>
            <div class="ctc-field ctc-amount-cell" style="left:786px; top:460px; width:136px; height:42px;">
                <span class="ctc-peso-prefix">&#8369;</span>
                <input type="number" step="0.01" min="0" class="ctc-amount-input" id="ctc-total" name="total_community_tax_due" placeholder="0.00">
            </div>

            <div class="ctc-field ctc-cell-label" style="left:650px; top:502px; width:136px; height:42px;">
                Interest
            </div>
            <div class="ctc-field ctc-amount-cell" style="left:786px; top:502px; width:136px; height:42px;">
                <input type="number" step="0.01" min="0" class="ctc-amount-input" id="ctc-interest" name="interest" placeholder="0.00">
            </div>

            <div class="ctc-field ctc-sidebar-amount" style="left:960px; top:40px; width:412px; height:84px;">
                <p class="ctc-sidebar-label">Total Amount Paid</p>
                <div class="ctc-sidebar-amount-value">
                    <span>&#8369;</span>
                    <input type="text" class="ctc-sidebar-input ctc-sidebar-amount-input" id="ctc-amount-paid" name="amount_paid" value="0.00" readonly>
                </div>
            </div>
            <div class="ctc-field ctc-sidebar-words" style="left:960px; top:124px; width:412px; height:84px;">
                <p class="ctc-sidebar-label">Amount in Words</p>
                <input type="text" class="ctc-sidebar-input ctc-sidebar-words-input" id="ctc-amount-in-words" name="amount_in_words" value="Zero pesos only" readonly>
            </div>
            <div class="ctc-field ctc-sidebar-treasurer" style="left:960px; top:208px; width:412px; height:126px;">
                <p class="ctc-sidebar-label">Municipal / City Treasurer</p>
                <div class="ctc-sidebar-treasurer-content">
                    <input type="text" class="ctc-sidebar-treasurer-name-input" id="ctc-treasurer-name" name="treasurer_name" value="Gemma D. Ferrer">
                    <p class="ctc-sidebar-treasurer-title">Municipal Treasurer</p>
                </div>
            </div>

            <div class="ctc-payment-block" style="position:absolute; left:40px; top:620px; width:1332px;">
                @include('collection-management.transaction-entry.partials.payment-section')
            </div>

            <button type="submit" class="ctc-proceed-btn">Proceed</button>
        </form>
    </div>

    @include('collection-management.transaction-entry.partials.ctc-corporation-preview-modal')
    @include('partials.select-enhancer')

    <script>
        (function () {
            const form = document.getElementById('ctcForm');
            if (!form) return;

            // Floating-label captions: mark wrapper as "filled" when the input has a value.
            const toggleFilled = (input) => {
                const wrap = input.closest('.ctc-input-wrap');
                if (wrap) wrap.classList.toggle('filled', input.value.trim() !== '');
            };

            form.querySelectorAll('.ctc-input-wrap .ctc-input').forEach((input) => {
                toggleFilled(input);
                input.addEventListener('input', () => toggleFilled(input));
            });

            // Shade empty fields slightly darker so unfilled inputs stand out from filled ones.
            const toggleEmpty = (input) => {
                const field = input.closest('.ctc-field');
                if (field) field.classList.toggle('is-empty', input.value.trim() === '');
            };

            form.querySelectorAll('.ctc-input, .ctc-amount-input, .ctc-cert-no-prefix-input, .ctc-cert-no-input, .ctc-sidebar-input, .ctc-sidebar-treasurer-name-input').forEach((input) => {
                toggleEmpty(input);
                input.addEventListener('input', () => toggleEmpty(input));
            });

            // Certificate prefix/number: size each input to its content so the
            // two fields render as one continuous string with no gap between them.
            const autosizeCert = (input) => {
                input.style.width = Math.max(input.value.length, 1) + 'ch';
            };

            form.querySelectorAll('.ctc-cert-no-prefix-input, .ctc-cert-no-input').forEach((input) => {
                autosizeCert(input);
                input.addEventListener('input', () => autosizeCert(input));
            });

            // TIN boxes: auto-advance focus between the 15 single-digit inputs.
            const tinCells = Array.from(form.querySelectorAll('.ctc-tin-cell'));
            tinCells.forEach((cell, index) => {
                cell.addEventListener('input', () => {
                    cell.value = cell.value.replace(/[^0-9]/g, '').slice(0, 1);
                    if (cell.value && index < tinCells.length - 1) {
                        tinCells[index + 1].focus();
                    }
                });
                cell.addEventListener('keydown', (event) => {
                    if (event.key === 'Backspace' && !cell.value && index > 0) {
                        tinCells[index - 1].focus();
                    }
                });
            });

            // Total / Interest: auto-computed from the tax table, but still manually editable.
            const totalInput = document.getElementById('ctc-total');
            const interestInput = document.getElementById('ctc-interest');

            // Total Amount Paid = Total + Interest; Amount in Words is derived from it.
            const amountPaidInput = document.getElementById('ctc-amount-paid');
            const amountInWordsInput = document.getElementById('ctc-amount-in-words');

            const NUM_ONES = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
            const NUM_TENS = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
            const NUM_SCALES = ['', 'thousand', 'million', 'billion'];

            const threeToWords = (n) => {
                let w = '';
                if (n >= 100) { w += NUM_ONES[Math.floor(n / 100)] + ' hundred'; n %= 100; if (n) w += ' '; }
                if (n >= 20) { w += NUM_TENS[Math.floor(n / 10)]; if (n % 10) w += '-' + NUM_ONES[n % 10]; }
                else if (n > 0) { w += NUM_ONES[n]; }
                return w;
            };

            const intToWords = (num) => {
                if (num === 0) return 'zero';
                const groups = [];
                while (num > 0) { groups.push(num % 1000); num = Math.floor(num / 1000); }
                let w = '';
                for (let i = groups.length - 1; i >= 0; i--) {
                    if (!groups[i]) continue;
                    w += threeToWords(groups[i]) + (NUM_SCALES[i] ? ' ' + NUM_SCALES[i] : '') + ' ';
                }
                return w.trim();
            };

            const amountToWords = (amount) => {
                const pesos = Math.floor(amount);
                const centavos = Math.round((amount - pesos) * 100);
                let w = intToWords(pesos) + ' peso' + (pesos === 1 ? '' : 's');
                if (centavos > 0) w += ' and ' + intToWords(centavos) + ' centavo' + (centavos === 1 ? '' : 's');
                w += ' only';
                return w.charAt(0).toUpperCase() + w.slice(1);
            };

            const recalcAmountPaid = () => {
                const amount = (parseFloat(totalInput.value) || 0) + (parseFloat(interestInput.value) || 0);
                amountPaidInput.value = amount.toFixed(2);
                amountInWordsInput.value = amountToWords(amount);
                toggleEmpty(amountPaidInput);
                toggleEmpty(amountInWordsInput);
            };

            const sumOf = (ids) => ids.reduce((sum, id) => {
                const el = document.getElementById(id);
                return sum + (parseFloat(el?.value) || 0);
            }, 0);

            const recalcTotal = () => {
                totalInput.value = sumOf(['ctc-a-ctd', 'ctc-1-ctd', 'ctc-2-ctd']).toFixed(2);
                recalcAmountPaid();
            };

            const recalcInterest = () => {
                interestInput.value = sumOf(['ctc-1-taxable', 'ctc-2-taxable']).toFixed(2);
                recalcAmountPaid();
            };

            ['ctc-a-ctd', 'ctc-1-ctd', 'ctc-2-ctd'].forEach((id) => {
                document.getElementById(id).addEventListener('input', recalcTotal);
            });

            ['ctc-1-taxable', 'ctc-2-taxable'].forEach((id) => {
                document.getElementById(id).addEventListener('input', recalcInterest);
            });

            // Recompute the amount when Total/Interest are edited directly, and once on load.
            [totalInput, interestInput].forEach((el) => el.addEventListener('input', recalcAmountPaid));
            recalcAmountPaid();

            // Proceed: validate required fields, save via AJAX, then redirect to Collection Management.
            const requiredIds = ['ctc-company-name', 'ctc-amount-paid'];

            // Preview modal: shows a print-preview of the certificate before saving.
            const previewOverlay = document.getElementById('ctcPreviewOverlay');
            const previewCloseBtn = document.getElementById('ctcPreviewCloseBtn');
            const previewPrintBtn = document.getElementById('ctcPreviewPrintBtn');

            const radioLabel = (name) => {
                const checked = form.querySelector(`input[name="${name}"]:checked`);
                return checked ? checked.closest('.ctc-radio-group').querySelector('p').textContent.trim() : '';
            };

            const populatePreview = () => {
                const previewCertPrefix = document.getElementById('ctcPreviewCertPrefix');
                if (previewCertPrefix) previewCertPrefix.textContent = document.getElementById('ctc-cert-prefix').value;

                previewOverlay.querySelectorAll('[data-preview]').forEach((el) => {
                    const input = form.querySelector(`[name="${el.dataset.preview}"]`);
                    el.textContent = input ? input.value : '';
                });

                previewOverlay.querySelectorAll('[data-preview-amount]').forEach((el) => {
                    const input = form.querySelector(`[name="${el.dataset.previewAmount}"]`);
                    const value = parseFloat(input?.value) || 0;
                    el.textContent = value ? value.toFixed(2) : '';
                });

                previewOverlay.querySelectorAll('[data-preview-tin]').forEach((el) => {
                    el.textContent = tinCells[Number(el.dataset.previewTin)]?.value || '';
                });

                const kindLabel = radioLabel('kind_of_organization');

                previewOverlay.querySelectorAll('[data-preview-radio="kind_of_organization"]').forEach((el) => {
                    el.classList.toggle('checked', el.dataset.value === kindLabel);
                });
            };

            const openPreview = () => previewOverlay.classList.add('open');
            const closePreview = () => previewOverlay.classList.remove('open');

            previewCloseBtn.addEventListener('click', closePreview);

            previewOverlay.addEventListener('click', function (event) {
                if (event.target === previewOverlay) closePreview();
            });

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                let hasError = false;
                requiredIds.forEach((id) => {
                    const input = document.getElementById(id);
                    const field = input.closest('.ctc-field');
                    const isEmpty = input.value.trim() === '';
                    if (field) field.classList.toggle('has-error', isEmpty);
                    if (isEmpty) hasError = true;
                });

                if (hasError) return;

                populatePreview();
                openPreview();
            });

            previewPrintBtn.addEventListener('click', function () {
                fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form),
                })
                    .then((response) => response.json().then((data) => ({ ok: response.ok, data })))
                    .then(({ ok, data }) => {
                        if (!ok) {
                            showToast('Action could not be completed', data.message || 'Something went wrong while saving. Please try again.', 'error');
                            return;
                        }

                        closePreview();
                        window.print();
                        window.location.href = data.redirect;
                    })
                    .catch(() => {
                        showToast('Action could not be completed', 'Something went wrong while saving. Please try again.', 'error');
                    });
            });
        })();
    </script>
</x-layout>
