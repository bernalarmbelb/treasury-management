<x-layout>
    <div class="x-header-container sub-nav-sticky">
        <div class="container-title">
            <div style="display: flex; flex-direction: column;">
                <span class="page-title">Collection Management</span>
                <p class="page-links">
                    <a href="{{ route('home') }}">Home</a> |
                    <a href="{{ route('collections') }}">Collections Management</a> |
                    <a href="{{ route('transaction-entry') }}">Transactions Entry</a> |
                    <span class="page-links-accent">Individual Cedula {{ $form->form_code }}</span>
                </p>
            </div>
        </div>
        @include('collection-management.transaction-entry.partials.form-tabs')
    </div>

    <div class="collection-content">
        <form class="ctcm-page" id="ctcForm" method="POST" action="{{ route('transaction-entry.individual-cedula.store', $form->id, false) }}">
            @csrf

            <div class="ctcm-topbar">
                <div class="ctcm-title-group">
                    <p class="ctcm-title">Community Tax Certificate</p>
                    <span class="ctcm-badge">Individual</span>
                </div>
                <div class="ctcm-certno">
                    <div class="ctcm-certno-no">
                        <input type="text" class="ctc-cert-no-prefix-input" id="ctc-cert-prefix" name="certificate_prefix" value="{{ $nextSerialPrefix ?? 'CCI2022' }}">
                        <input type="text" class="ctc-cert-no-input" id="ctc-cert-no" name="certificate_number" value="{{ $nextSerialNumber ?? '13476955' }}">
                    </div>
                    <p class="ctcm-certno-copy">Taxpayer's Copy</p>
                </div>
            </div>

            <div class="ctcm-body">
                <div class="ctcm-section">
                    <div class="ctc-section-header"><p>Certificate Details</p></div>
                    <div class="ctc-rpt-row">
                        <div class="ctc-field ctc-input-wrap ctc-rpt-field ctc-rpt-field--narrow">
                            <input type="number" class="ctc-input" id="ctc-year" name="year" value="{{ now()->year }}" placeholder=" ">
                            <label class="ctc-input-caption" for="ctc-year">Year</label>
                        </div>
                        <div class="ctc-field ctc-input-wrap ctc-rpt-field">
                            <input type="text" class="ctc-input" id="ctc-place-of-issue" name="place_of_issue" placeholder=" ">
                            <label class="ctc-input-caption" for="ctc-place-of-issue">Place of Issue (City/Mun./Prov.)</label>
                        </div>
                        <div class="ctc-field ctc-input-wrap ctc-date ctc-rpt-field ctc-rpt-field--narrow">
                            <input type="date" class="ctc-input" id="ctc-date-issued-1" name="date_issued">
                            <label class="ctc-input-caption" for="ctc-date-issued-1">Date Issued</label>
                        </div>
                    </div>
                </div>

                <div class="ctcm-section">
                    <div class="ctc-section-header"><p>Personal Information</p></div>
                    <div class="ctc-rpt-row">
                        <div class="ctc-field ctc-input-wrap ctc-rpt-field">
                            <input type="text" class="ctc-input" id="ctc-surname" name="surname" placeholder=" ">
                            <label class="ctc-input-caption" for="ctc-surname">Name (Surname)</label>
                        </div>
                        <div class="ctc-field ctc-input-wrap ctc-rpt-field">
                            <input type="text" class="ctc-input" id="ctc-first-name" name="first_name" placeholder=" ">
                            <label class="ctc-input-caption" for="ctc-first-name">First Name</label>
                        </div>
                        <div class="ctc-field ctc-input-wrap ctc-rpt-field">
                            <input type="text" class="ctc-input" id="ctc-middle-name" name="middle_name" placeholder=" ">
                            <label class="ctc-input-caption" for="ctc-middle-name">Middle Name</label>
                        </div>
                    </div>
                    <div class="ctc-rpt-row">
                        <div class="ctc-field ctcm-tin ctc-rpt-field--full">
                            <span class="ctcm-tin-label">TIN (if any)</span>
                            <div class="ctcm-tin-cells">
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
                    </div>
                    <div class="ctc-rpt-row">
                        <div class="ctc-field ctc-input-wrap ctc-date ctc-rpt-field--full">
                            <input type="date" class="ctc-input" id="ctc-date-issued-2" name="date_issued_2">
                            <label class="ctc-input-caption" for="ctc-date-issued-2">Date Issued</label>
                        </div>
                    </div>
                    <div class="ctc-rpt-row">
                        <div class="ctc-field ctcm-radio-row ctc-rpt-field--full">
                            <p class="ctcm-radio-row-label">Sex</p>
                            <label class="ctc-radio-group">
                                <input type="radio" name="sex" class="ctc-radio" value="Male">
                                <p>Male</p>
                            </label>
                            <label class="ctc-radio-group">
                                <input type="radio" name="sex" class="ctc-radio" value="Female">
                                <p>Female</p>
                            </label>
                        </div>
                    </div>
                    <div class="ctc-rpt-row">
                        <div class="ctc-field ctc-input-wrap ctc-rpt-field">
                            <input type="text" class="ctc-input" id="ctc-citizenship" name="citizenship" placeholder=" ">
                            <label class="ctc-input-caption" for="ctc-citizenship">Citizenship</label>
                        </div>
                        <div class="ctc-field ctc-input-wrap ctc-rpt-field">
                            <input type="text" class="ctc-input" id="ctc-icr-no" name="icr_no" placeholder=" ">
                            <label class="ctc-input-caption" for="ctc-icr-no">ICR No. (if any)</label>
                        </div>
                        <div class="ctc-field ctc-input-wrap ctc-rpt-field">
                            <input type="text" class="ctc-input" id="ctc-place-of-birth" name="place_of_birth" placeholder=" ">
                            <label class="ctc-input-caption" for="ctc-place-of-birth">Place of Birth</label>
                        </div>
                        <div class="ctc-field ctc-input-wrap ctc-rpt-field ctc-rpt-field--narrow">
                            <input type="text" class="ctc-input" id="ctc-height" name="height" placeholder=" ">
                            <label class="ctc-input-caption" for="ctc-height">Height</label>
                        </div>
                    </div>
                    <div class="ctc-rpt-row">
                        <div class="ctc-field ctcm-radio-row ctc-rpt-field--full">
                            <p class="ctcm-radio-row-label">Civil Status</p>
                            <label class="ctc-radio-group">
                                <input type="radio" name="civil_status" class="ctc-radio" value="Single">
                                <p>Single</p>
                            </label>
                            <label class="ctc-radio-group">
                                <input type="radio" name="civil_status" class="ctc-radio" value="Married">
                                <p>Married</p>
                            </label>
                            <label class="ctc-radio-group">
                                <input type="radio" name="civil_status" class="ctc-radio" value="Divorced">
                                <p>Divorced</p>
                            </label>
                            <label class="ctc-radio-group">
                                <input type="radio" name="civil_status" class="ctc-radio" value="Widow / Widower / Legally Separated">
                                <p>Widow / Widower / Legally Separated</p>
                            </label>
                        </div>
                    </div>
                    <div class="ctc-rpt-row">
                        <div class="ctc-field ctc-input-wrap ctc-rpt-field">
                            <input type="text" class="ctc-input" id="ctc-weight" name="weight" placeholder=" ">
                            <label class="ctc-input-caption" for="ctc-weight">Weight</label>
                        </div>
                        <div class="ctc-field ctc-input-wrap ctc-date ctc-rpt-field">
                            <input type="date" class="ctc-input" id="ctc-date-of-birth" name="date_of_birth">
                            <label class="ctc-input-caption" for="ctc-date-of-birth">Date of Birth</label>
                        </div>
                    </div>
                    <div class="ctc-rpt-row">
                        <div class="ctc-field ctc-input-wrap ctc-rpt-field--full">
                            <input type="text" class="ctc-input" id="ctc-profession" name="profession" placeholder=" ">
                            <label class="ctc-input-caption" for="ctc-profession">Profession / Occupation / Business</label>
                        </div>
                    </div>
                </div>

                <div class="ctcm-section">
                    <div class="ctc-section-header"><p>Community Tax Computation</p></div>
                    <table class="ctcm-tax-table">
                        <thead>
                            <tr><th></th><th>Taxable Amount</th><th>Community Tax Due</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>A. Basic Community Tax (&#8369;5.00) Voluntary or Exempted (&#8369;1.00)</td>
                                <td class="ctcm-fixed">&mdash;</td>
                                <td class="ctcm-amt">
                                    <span class="ctcm-amt-inner">
                                        <span class="ctc-peso-prefix">&#8369;</span>
                                        <input type="number" step="0.01" min="0" class="ctc-amount-input" id="ctc-a-ctd" name="a_community_tax_due" placeholder="0.00">
                                    </span>
                                </td>
                            </tr>
                            <tr class="ctcm-grp">
                                <td colspan="3">B. Additional Community Tax ( Tax not to exceed &#8369;5.00 )</td>
                            </tr>
                            <tr>
                                <td>1. Gross receipts or earnings derived from business during the preceding year (&#8369;1.00 for every &#8369;1,000.00)</td>
                                <td class="ctcm-amt">
                                    <span class="ctcm-amt-inner">
                                        <span class="ctc-peso-prefix">&#8369;</span>
                                        <input type="number" step="0.01" min="0" class="ctc-amount-input" id="ctc-1-taxable" name="item1_taxable_amount" placeholder="0.00">
                                    </span>
                                </td>
                                <td class="ctcm-amt">
                                    <span class="ctcm-amt-inner">
                                        <span class="ctc-peso-prefix">&#8369;</span>
                                        <input type="number" step="0.01" min="0" class="ctc-amount-input" id="ctc-1-ctd" name="item1_community_tax_due" placeholder="0.00">
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>2. Salaries or gross receipt or earnings derived from exercise of profession or pursuit of any occupation (&#8369;100.00 for every &#8369;1,000.00)</td>
                                <td class="ctcm-amt">
                                    <span class="ctcm-amt-inner">
                                        <span class="ctc-peso-prefix">&#8369;</span>
                                        <input type="number" step="0.01" min="0" class="ctc-amount-input" id="ctc-2-taxable" name="item2_taxable_amount" placeholder="0.00">
                                    </span>
                                </td>
                                <td class="ctcm-amt">
                                    <span class="ctcm-amt-inner">
                                        <span class="ctc-peso-prefix">&#8369;</span>
                                        <input type="number" step="0.01" min="0" class="ctc-amount-input" id="ctc-2-ctd" name="item2_community_tax_due" placeholder="0.00">
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>3. Income from real property (&#8369;1.00 for every &#8369;1,000.00)</td>
                                <td class="ctcm-amt">
                                    <span class="ctcm-amt-inner">
                                        <span class="ctc-peso-prefix">&#8369;</span>
                                        <input type="number" step="0.01" min="0" class="ctc-amount-input" id="ctc-3-taxable" name="item3_taxable_amount" placeholder="0.00">
                                    </span>
                                </td>
                                <td class="ctcm-amt">
                                    <span class="ctcm-amt-inner">
                                        <span class="ctc-peso-prefix">&#8369;</span>
                                        <input type="number" step="0.01" min="0" class="ctc-amount-input" id="ctc-3-ctd" name="item3_community_tax_due" placeholder="0.00">
                                    </span>
                                </td>
                            </tr>
                            <tr class="ctcm-total">
                                <td>Total</td>
                                <td class="ctcm-fixed">&mdash;</td>
                                <td class="ctcm-amt">
                                    <span class="ctcm-amt-inner">
                                        <span class="ctc-peso-prefix">&#8369;</span>
                                        <input type="number" step="0.01" min="0" class="ctc-amount-input" id="ctc-total" name="total_community_tax_due" placeholder="0.00">
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>Interest</td>
                                <td class="ctcm-fixed">&mdash;</td>
                                <td class="ctcm-amt">
                                    <span class="ctcm-amt-inner">
                                        <span class="ctc-peso-prefix">&#8369;</span>
                                        <input type="number" step="0.01" min="0" class="ctc-amount-input" id="ctc-interest" name="interest" placeholder="0.00">
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="ctcm-section">
                    <div class="ctc-section-header"><p>Certification</p></div>
                    <div class="ctc-rpt-row">
                        <div class="ctcm-cert-box">
                            <span>Right Thumb Print</span>
                        </div>
                        <div class="ctcm-cert-box">
                            <span>Taxpayer's Signature</span>
                        </div>
                    </div>
                </div>

                <div class="ctcm-section">
                    <div class="ctc-section-header"><p>Total &amp; Treasurer</p></div>
                    <div class="ctc-rpt-row">
                        <div class="ctc-field ctc-input-wrap ctc-rpt-field">
                            <input type="text" class="ctc-input" id="ctc-amount-in-words" name="amount_in_words" value="Zero pesos only" readonly>
                            <label class="ctc-input-caption" for="ctc-amount-in-words">Amount in Words</label>
                        </div>
                        <div class="ctc-field ctc-input-wrap ctc-rpt-field ctc-rpt-field--amount">
                            <input type="text" class="ctc-input" id="ctc-amount-paid" name="amount_paid" value="0.00" readonly>
                            <label class="ctc-input-caption" for="ctc-amount-paid">Total Amount Paid (&#8369;)</label>
                        </div>
                    </div>
                    <div class="ctc-rpt-row">
                        <div class="ctc-field ctc-input-wrap ctc-rpt-field--full">
                            <input type="text" class="ctc-input" id="ctc-treasurer-name" name="treasurer_name" value="Gemma D. Ferrer">
                            <label class="ctc-input-caption" for="ctc-treasurer-name">Municipal / City Treasurer</label>
                        </div>
                    </div>
                    <p class="ctcm-role-note">Position: Municipal Treasurer</p>
                </div>
            </div>

            <div class="ctcm-section" style="padding: 0 24px;">
                @include('collection-management.transaction-entry.partials.payment-section')
            </div>

            <div class="ctcm-foot">
                <button type="submit" class="ctc-proceed-btn">Proceed</button>
            </div>
        </form>
    </div>

    @include('collection-management.transaction-entry.partials.ctc-preview-modal')
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

            form.querySelectorAll('.ctc-input, .ctc-amount-input, .ctc-cert-no-prefix-input, .ctc-cert-no-input').forEach((input) => {
                toggleEmpty(input);
                input.addEventListener('input', () => toggleEmpty(input));
            });

            // Certificate prefix/number: size each input to its content so the
            // two fields render as one continuous string with no gap between them.
            // Measured in real pixels (not `ch`) because bold uppercase letters
            // render wider than the digit-based `ch` unit, which was clipping
            // the prefix text against its own input box.
            const certMeasureCtx = document.createElement('canvas').getContext('2d');
            const autosizeCert = (input) => {
                const style = getComputedStyle(input);
                certMeasureCtx.font = `${style.fontWeight} ${style.fontSize} ${style.fontFamily}`;
                const width = certMeasureCtx.measureText(input.value || ' ').width;
                input.style.width = Math.ceil(width) + 2 + 'px';
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
                totalInput.value = sumOf(['ctc-a-ctd', 'ctc-1-ctd', 'ctc-2-ctd', 'ctc-3-ctd']).toFixed(2);
                recalcAmountPaid();
            };

            const recalcInterest = () => {
                interestInput.value = sumOf(['ctc-1-taxable', 'ctc-2-taxable', 'ctc-3-taxable']).toFixed(2);
                recalcAmountPaid();
            };

            ['ctc-a-ctd', 'ctc-1-ctd', 'ctc-2-ctd', 'ctc-3-ctd'].forEach((id) => {
                document.getElementById(id).addEventListener('input', recalcTotal);
            });

            ['ctc-1-taxable', 'ctc-2-taxable', 'ctc-3-taxable'].forEach((id) => {
                document.getElementById(id).addEventListener('input', recalcInterest);
            });

            // Recompute the amount when Total/Interest are edited directly, and once on load.
            [totalInput, interestInput].forEach((el) => el.addEventListener('input', recalcAmountPaid));
            recalcAmountPaid();

            // Proceed: validate required fields, save via AJAX, then redirect to Collection Management.
            const requiredIds = ['ctc-surname', 'ctc-first-name', 'ctc-amount-paid'];

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

                const sexLabel = radioLabel('sex');
                const civilLabel = radioLabel('civil_status');

                previewOverlay.querySelectorAll('[data-preview-radio="sex"]').forEach((el) => {
                    el.classList.toggle('checked', el.dataset.value === sexLabel);
                });

                previewOverlay.querySelectorAll('[data-preview-radio="civil_status"]').forEach((el) => {
                    el.classList.toggle('checked', el.dataset.value === civilLabel);
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
