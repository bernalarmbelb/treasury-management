<x-layout>
    <div class="x-header-container sub-nav-sticky">
        <div class="container-title">
            <div style="display: flex; flex-direction: column;">
                <span class="page-title">Collection Management</span>
                <p class="page-links">
                    <a href="{{ route('home') }}">Home</a> |
                    <a href="{{ route('collections') }}">Collections Management</a> |
                    <a href="{{ route('transaction-entry') }}">Transactions Entry</a> |
                    <span class="page-links-accent">Official Receipt</span>
                </p>
            </div>
        </div>
        <div class="ctc-tabs-row">
            <a href="{{ route('transaction-entry') }}" class="ctc-tab">Transactions Log</a>
            <div class="ctc-tab active">New Entry</div>
        </div>
    </div>

    <div class="collection-content">
        <form class="ctc-page--or2" id="ctcOrForm" method="POST" action="{{ route('transaction-entry.official-receipt.store', $form->id, false) }}">
            @csrf
            <input type="hidden" name="total" id="or-total-input" value="0">

            <div class="ctc-or2-col">
                <div class="ctc-or2-row">
                    <div class="ctc-or2-group">
                        <div class="ctc-or2-bar"><p>Serial Number</p></div>
                        <div class="ctc-or2-cell">
                            <input type="text" id="or-serial-number" name="certificate_number" value="{{ $certificateNumber }} U">
                        </div>
                    </div>
                    <div class="ctc-or2-group">
                        <div class="ctc-or2-bar"><p>Date Issued</p></div>
                        <div class="ctc-or2-cell">
                            <input type="date" id="or-date-issued" name="date_issued" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div class="ctc-or2-bar"><p>Information Details</p></div>
                <div class="ctc-or2-row">
                    <div class="ctc-or2-cell ctc-or2-cell--captioned">
                        <input type="text" id="or-agency" name="agency" placeholder=" ">
                        <label class="ctc-or2-caption" for="or-agency">Agency</label>
                    </div>
                    <div class="ctc-or2-cell ctc-or2-cell--narrow ctc-or2-cell--captioned">
                        <input type="text" id="or-fund" name="fund" placeholder=" ">
                        <label class="ctc-or2-caption" for="or-fund">Fund</label>
                    </div>
                </div>

                <div class="ctc-or2-cell ctc-or2-cell--captioned" id="or-payor-cell">
                    <input type="text" id="or-payor" name="payor" placeholder=" ">
                    <label class="ctc-or2-caption" for="or-payor">Payor</label>
                </div>

                <div class="ctc-or2-bar"><p>Input Form Details Here</p></div>

                <div class="ctc-or2-rows" id="orItemsTableBody">
                    @for ($i = 0; $i < 8; $i++)
                        <div class="ctc-or2-row">
                            <div class="ctc-or2-cell ctc-or2-cell--captioned">
                                <input type="text" id="or-item-{{ $i }}-description" name="items[{{ $i }}][description]" placeholder=" ">
                                <label class="ctc-or2-caption" for="or-item-{{ $i }}-description">Nature of Collection</label>
                            </div>
                            <div class="ctc-or2-cell ctc-or2-cell--narrow ctc-or2-cell--captioned">
                                <input type="text" id="or-item-{{ $i }}-account-code" name="items[{{ $i }}][account_code]" placeholder=" ">
                                <label class="ctc-or2-caption" for="or-item-{{ $i }}-account-code">Account Code</label>
                            </div>
                            <div class="ctc-or2-cell ctc-or2-cell--narrow ctc-or2-cell--amount ctc-or2-cell--captioned">
                                <input type="text" inputmode="decimal" class="ctc-or-amount-input" id="or-item-{{ $i }}-amount" name="items[{{ $i }}][amount]" placeholder=" ">
                                <label class="ctc-or2-caption" for="or-item-{{ $i }}-amount">Amount</label>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="ctc-or2-col">
                <div class="ctc-or2-row">
                    <div class="ctc-or2-group ctc-or2-group--narrow">
                        <div class="ctc-or2-bar"><p>Total</p></div>
                        <div class="ctc-or2-cell ctc-or2-cell--amount ctc-or2-cell--readonly">
                            <span>₱</span><span id="orTotalValue">--</span>
                        </div>
                    </div>
                    <div class="ctc-or2-group">
                        <div class="ctc-or2-bar"><p>Amount In Words</p></div>
                        <div class="ctc-or2-cell">
                            <input type="text" id="or-amount-in-words" name="amount_in_words" placeholder="Amount in Words">
                        </div>
                    </div>
                </div>

                <div class="ctc-or2-checkboxes">
                    <label class="ctc-or2-checkbox-label">
                        <input type="radio" class="ctc-or2-checkbox" name="payment_method" value="cash" checked>
                        Cash
                    </label>
                    <label class="ctc-or2-checkbox-label">
                        <input type="radio" class="ctc-or2-checkbox" name="payment_method" value="check">
                        Check
                    </label>
                    <label class="ctc-or2-checkbox-label">
                        <input type="radio" class="ctc-or2-checkbox" name="payment_method" value="money_order">
                        Money Order
                    </label>
                </div>

                <div class="ctc-or2-row">
                    <div class="ctc-or2-group">
                        <div class="ctc-or2-bar"><p>Drawee Bank</p></div>
                        <div class="ctc-or2-cell">
                            <input type="text" id="or-drawee-bank" name="drawee_bank" placeholder="—">
                        </div>
                    </div>
                    <div class="ctc-or2-group">
                        <div class="ctc-or2-bar"><p>Number</p></div>
                        <div class="ctc-or2-cell">
                            <input type="text" id="or-check-number" name="check_number" placeholder="—">
                        </div>
                    </div>
                    <div class="ctc-or2-group">
                        <div class="ctc-or2-bar"><p>Date</p></div>
                        <div class="ctc-or2-cell">
                            <input type="date" id="or-check-date" name="check_date">
                        </div>
                    </div>
                </div>

                <div class="ctc-or2-row">
                    <div class="ctc-or2-group">
                        <div class="ctc-or2-bar"><p>Received the amount stated above</p></div>
                        <div class="ctc-or2-cell ctc-or2-cell--readonly">Gemma D. Ferrer</div>
                    </div>
                    <div class="ctc-or2-group">
                        <div class="ctc-or2-bar"><p>Collecting Officer Position</p></div>
                        <div class="ctc-or2-cell ctc-or2-cell--readonly">Municipal Treasurer</div>
                    </div>
                </div>

                <div class="ctc-or2-cell">
                    <p class="ctc-or2-note"><strong>Note:</strong> Write the number and date of this receipt on the back of check or money order received.</p>
                </div>

                <div class="ctc-or2-actions">
                    <button type="submit" class="ctc-or2-proceed-btn" id="ctcOrProceedBtn">Proceed</button>
                </div>
            </div>
        </form>
    </div>

    @include('collection-management.transaction-entry.partials.ctc-or-preview-modal')

    <script>
        (function () {
            const form = document.getElementById('ctcOrForm');
            if (!form) return;

            const unformatNumber = (value) => {
                const cleaned = String(value ?? '').replace(/[^0-9.]/g, '');
                const [integerPart, ...rest] = cleaned.split('.');
                return rest.length ? `${integerPart}.${rest.join('')}` : integerPart;
            };

            const formatNumberInput = (value) => {
                const [integerPart, ...rest] = unformatNumber(value).split('.');
                const formattedInteger = (integerPart || '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                return rest.length ? `${formattedInteger}.${rest.join('')}` : formattedInteger;
            };

            const formatAmount = (value) => {
                const num = parseFloat(unformatNumber(value)) || 0;
                return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            };

            // Active-field state: cells turn white once their input has a value.
            form.querySelectorAll('.ctc-or2-cell input').forEach((input) => {
                const cell = input.closest('.ctc-or2-cell');
                const toggleFilled = () => cell.classList.toggle('filled', input.value.trim() !== '');
                toggleFilled();
                input.addEventListener('input', toggleFilled);
            });

            // Format amount inputs with thousands separators as the user types.
            const amountInputs = form.querySelectorAll('.ctc-or-amount-input');
            const totalInput = document.getElementById('or-total-input');
            const totalValueEl = document.getElementById('orTotalValue');

            const recalculateTotal = () => {
                let total = 0;
                amountInputs.forEach((input) => {
                    total += parseFloat(unformatNumber(input.value)) || 0;
                });

                totalValueEl.textContent = total > 0 ? formatAmount(total) : '--';
                totalInput.value = total.toFixed(2);
            };

            amountInputs.forEach((input) => {
                input.addEventListener('input', () => {
                    const cursorFromEnd = input.value.length - input.selectionStart;
                    input.value = formatNumberInput(input.value);
                    const newPosition = input.value.length - cursorFromEnd;
                    input.setSelectionRange(newPosition, newPosition);
                    recalculateTotal();
                });
            });

            recalculateTotal();

            // Print-preview modal: shows the filled-out receipt before saving.
            const previewOverlay = document.getElementById('ctcOrPreviewOverlay');
            const previewCloseBtn = document.getElementById('ctcOrPreviewCloseBtn');
            const previewPrintBtn = document.getElementById('ctcOrPreviewPrintBtn');
            const previewTableBody = document.getElementById('ctcOrPreviewTableBody');

            const openPreview = () => previewOverlay.classList.add('open');
            const closePreview = () => previewOverlay.classList.remove('open');

            previewCloseBtn.addEventListener('click', closePreview);

            previewOverlay.addEventListener('click', (event) => {
                if (event.target === previewOverlay) closePreview();
            });

            const populatePreview = () => {
                previewOverlay.querySelectorAll('[data-or-preview]').forEach((el) => {
                    const name = el.dataset.orPreview;

                    if (name === 'total' || name === 'total_amount_paid') {
                        el.textContent = totalValueEl.textContent === '--' ? '0.00' : totalValueEl.textContent;
                        return;
                    }

                    if (name === 'certificate_number') {
                        return;
                    }

                    const input = form.querySelector(`[name="${name}"]`);
                    if (!input) {
                        el.textContent = '';
                        return;
                    }

                    el.textContent = input.value || ' ';
                });

                previewOverlay.querySelectorAll('[data-or-preview-checked]').forEach((el) => {
                    const input = form.querySelector(`[name="${el.dataset.orPreviewChecked}"][value="${el.dataset.orPreviewValue}"]`);
                    el.classList.toggle('checked', !!input?.checked);
                });

                previewTableBody.innerHTML = '';
                form.querySelectorAll('#orItemsTableBody .ctc-or2-row').forEach((row) => {
                    const description = row.querySelector('[name$="[description]"]').value;
                    const accountCode = row.querySelector('[name$="[account_code]"]').value;
                    const amount = row.querySelector('[name$="[amount]"]').value;

                    const tr = document.createElement('tr');

                    const descTd = document.createElement('td');
                    descTd.innerHTML = `<span class="ctcp-or-value">${description || '&nbsp;'}</span>`;

                    const codeTd = document.createElement('td');
                    codeTd.className = 'ctc-or-col-account';
                    codeTd.innerHTML = `<span class="ctcp-or-value" style="text-align:center;">${accountCode || '&nbsp;'}</span>`;

                    const amountTd = document.createElement('td');
                    amountTd.className = 'ctc-or-amount-cell';
                    amountTd.innerHTML = amount
                        ? `<span class="ctc-or-peso">₱</span><span class="ctcp-or-value" style="text-align:right;">${formatAmount(amount)}</span>`
                        : '&nbsp;';

                    tr.append(descTd, codeTd, amountTd);
                    previewTableBody.appendChild(tr);
                });
            };

            // Proceed: validate required fields, then show the print-preview modal.
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                const payorInput = document.getElementById('or-payor');
                const payorCell = document.getElementById('or-payor-cell');
                const isEmpty = payorInput.value.trim() === '';
                payorCell.classList.toggle('has-error', isEmpty);

                if (isEmpty) return;

                populatePreview();
                openPreview();
            });

            // Print: save the entry via AJAX, print the receipt, then redirect to Collection Management.
            previewPrintBtn.addEventListener('click', function () {
                const formData = new FormData(form);
                amountInputs.forEach((input) => {
                    formData.set(input.name, unformatNumber(input.value));
                });

                fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData,
                })
                    .then((response) => {
                        if (!response.ok) throw new Error('Save failed');
                        return response.json();
                    })
                    .then((data) => {
                        closePreview();
                        window.print();
                        window.location.href = data.redirect;
                    })
                    .catch(() => {
                        alert('Something went wrong while saving. Please try again.');
                    });
            });
        })();
    </script>
</x-layout>
