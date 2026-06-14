<x-layout>
    <div class="x-header-container sub-nav-sticky">
        <div class="container-title">
            <div style="display: flex; flex-direction: column;">
                <span class="page-title">Collection Management</span>
                <p class="page-links">
                    <a href="{{ route('home') }}">Home</a> |
                    <a href="{{ route('collections') }}">Collections Management</a> |
                    <a href="{{ route('transaction-entry') }}">Transactions Entry</a> |
                    <span class="page-links-accent">OR RPT</span>
                </p>
            </div>
        </div>
        <div class="ctc-tabs-row">
            <a href="{{ route('transaction-entry') }}" class="ctc-tab">Transactions Log</a>
            <div class="ctc-tab active">New Entry</div>
        </div>
    </div>

    <div class="collection-content">
        <form class="ctc-page ctc-page--or-rpt" id="ctcForm" method="POST" action="{{ route('transaction-entry.or-rpt.store', $form->id, false) }}">
            @csrf

            <div class="ctc-rpt-form">
                <div class="ctc-section-header"><p>Serial Number</p></div>
                <div class="ctc-rpt-row">
                    <div class="ctc-field ctc-input-wrap ctc-rpt-field--full">
                        <input type="text" class="ctc-input" id="rpt-serial-number" name="serial_number" placeholder=" ">
                        <label class="ctc-input-caption" for="rpt-serial-number">Serial Number</label>
                    </div>
                </div>

                <div class="ctc-section-header"><p>Previous Tax Receipt Number</p></div>
                <div class="ctc-rpt-row">
                    <div class="ctc-field ctc-input-wrap ctc-rpt-field">
                        <input type="text" class="ctc-input" id="rpt-previous-receipt-number" name="previous_receipt_number" placeholder=" ">
                        <label class="ctc-input-caption" for="rpt-previous-receipt-number">Previous Tax Receipt No.</label>
                    </div>
                    <div class="ctc-field ctc-input-wrap ctc-rpt-field">
                        <input type="text" class="ctc-input" id="rpt-previous-receipt-date" name="previous_receipt_date" placeholder=" ">
                        <label class="ctc-input-caption" for="rpt-previous-receipt-date">MMMM DD</label>
                    </div>
                    <div class="ctc-field ctc-input-wrap ctc-rpt-field ctc-rpt-field--narrow">
                        <input type="text" class="ctc-input" id="rpt-previous-receipt-year" name="previous_receipt_year" placeholder=" ">
                        <label class="ctc-input-caption" for="rpt-previous-receipt-year">Year(YY)</label>
                    </div>
                </div>

                <div class="ctc-section-header"><p>Input Form Details Here</p></div>
                <div class="ctc-rpt-row">
                    <div class="ctc-field ctc-input-wrap ctc-rpt-field">
                        <input type="text" class="ctc-input" id="rpt-municipality-province" name="municipality_province" value="Prieto-Diaz, Sorsogon" placeholder=" ">
                        <label class="ctc-input-caption" for="rpt-municipality-province">Municipality / Province</label>
                    </div>
                    <div class="ctc-field ctc-input-wrap ctc-rpt-field">
                        <input type="text" class="ctc-input" id="rpt-city" name="city" value="Sorsogon" placeholder=" ">
                        <label class="ctc-input-caption" for="rpt-city">City</label>
                    </div>
                    <div class="ctc-field ctc-input-wrap ctc-date ctc-rpt-field ctc-rpt-field--narrow">
                        <input type="date" class="ctc-input" id="rpt-transaction-date" name="transaction_date" value="{{ now()->format('Y-m-d') }}">
                        <label class="ctc-input-caption" for="rpt-transaction-date">Date</label>
                    </div>
                </div>

                <div class="ctc-rpt-row">
                    <div class="ctc-field ctc-input-wrap ctc-rpt-field--full">
                        <input type="text" class="ctc-input" id="rpt-client-name" name="client_name" placeholder=" ">
                        <label class="ctc-input-caption" for="rpt-client-name">Client's name</label>
                    </div>
                </div>

                <div class="ctc-rpt-row">
                    <div class="ctc-field ctc-input-wrap ctc-rpt-field">
                        <input type="text" class="ctc-input" id="rpt-payment-in-words" name="payment_in_words" placeholder=" ">
                        <label class="ctc-input-caption" for="rpt-payment-in-words">Payment (Sum in words)</label>
                    </div>
                    <div class="ctc-field ctc-input-wrap ctc-rpt-field ctc-rpt-field--amount">
                        <input type="text" inputmode="decimal" class="ctc-input" id="rpt-amount-paid" name="amount_paid" placeholder=" ">
                        <label class="ctc-input-caption" for="rpt-amount-paid">P (Amount in numbers)</label>
                    </div>
                </div>

                <div class="ctc-section-header"><p>Provincial or City Treasurer</p></div>
                <div class="ctc-rpt-row">
                    <div class="ctc-field ctc-input-wrap ctc-rpt-field--full">
                        <input type="text" class="ctc-input" id="rpt-treasurer-deputy" name="treasurer_deputy" value="Gemma D. Ferrer" placeholder=" ">
                        <label class="ctc-input-caption" for="rpt-treasurer-deputy">Deputy / Staff</label>
                    </div>
                </div>

                <div class="ctc-field ctc-checkbox-row ctc-rpt-field--full">
                    <label class="ctc-checkbox-group">
                        <input type="checkbox" class="ctc-checkbox" id="rpt-basic-tax" name="basic_tax" value="1">
                        <p>Basic Tax</p>
                    </label>
                    <label class="ctc-checkbox-group">
                        <input type="checkbox" class="ctc-checkbox" id="rpt-sef" name="special_education_fund" value="1">
                        <p>Special Education Fund</p>
                    </label>
                </div>

                <button type="submit" class="ctc-add-entry-btn">Add Entry</button>
            </div>

            @include('collection-management.transaction-entry.partials.ctc-or-rpt-preview')
        </form>
    </div>

    @include('collection-management.transaction-entry.partials.ctc-or-rpt-add-entry-modal')

    <script>
        (function () {
            const form = document.getElementById('ctcForm');
            if (!form) return;

            const preview = form.querySelector('.ctcp-page--or-rpt');

            // Table row currently being edited via the "Edit" action (null when adding a new entry).
            let editingRow = null;

            // Floating-label captions: mark wrapper as "filled" when the input has a value.
            const toggleFilled = (input) => {
                const wrap = input.closest('.ctc-input-wrap');
                if (wrap) wrap.classList.toggle('filled', input.value.trim() !== '');
            };

            // Shade empty fields slightly darker so unfilled inputs stand out from filled ones.
            const toggleEmpty = (input) => {
                const field = input.closest('.ctc-field');
                if (field) field.classList.toggle('is-empty', input.value.trim() === '');
            };

            // Strip commas and any non-numeric characters, keeping only digits and a single decimal point.
            const unformatNumber = (value) => {
                const cleaned = String(value ?? '').replace(/[^0-9.]/g, '');
                const [integerPart, ...rest] = cleaned.split('.');
                return rest.length ? `${integerPart}.${rest.join('')}` : integerPart;
            };

            // Insert thousands separators into the integer part as the user types.
            const formatNumberInput = (value) => {
                const [integerPart, ...rest] = unformatNumber(value).split('.');
                const formattedInteger = (integerPart || '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                return rest.length ? `${formattedInteger}.${rest.join('')}` : formattedInteger;
            };

            const amountInput = form.querySelector('#rpt-amount-paid');
            if (amountInput) {
                amountInput.addEventListener('input', () => {
                    const cursorFromEnd = amountInput.value.length - amountInput.selectionStart;
                    amountInput.value = formatNumberInput(amountInput.value);
                    const newPosition = amountInput.value.length - cursorFromEnd;
                    amountInput.setSelectionRange(newPosition, newPosition);
                });
            }

            // Two-way sync between the "Serial Number" field and the "No. ___ Z" badge in the preview.
            const serialNumberInput = form.querySelector('#rpt-serial-number');
            const certificateNumberInput = preview.querySelector('.ctcp-rpt-no-input');
            if (serialNumberInput && certificateNumberInput) {
                if (!serialNumberInput.value) serialNumberInput.value = certificateNumberInput.value;
                certificateNumberInput.value = serialNumberInput.value;

                serialNumberInput.addEventListener('input', () => {
                    certificateNumberInput.value = serialNumberInput.value;
                });

                certificateNumberInput.addEventListener('input', () => {
                    serialNumberInput.value = certificateNumberInput.value;
                    toggleFilled(serialNumberInput);
                    toggleEmpty(serialNumberInput);
                });
            }

            const populatePreview = () => {
                preview.querySelectorAll('[data-preview]').forEach((el) => {
                    const name = el.dataset.preview;

                    const input = form.querySelector(`[name="${name}"]`);
                    if (!input) {
                        el.textContent = '';
                        return;
                    }

                    el.textContent = input.value;
                });
            };

            form.querySelectorAll('.ctc-input-wrap .ctc-input').forEach((input) => {
                toggleFilled(input);
                toggleEmpty(input);
                input.addEventListener('input', () => {
                    toggleFilled(input);
                    toggleEmpty(input);
                    populatePreview();
                });
            });

            form.querySelectorAll('.ctc-checkbox').forEach((input) => {
                input.addEventListener('change', populatePreview);
            });

            populatePreview();

            // Add Entry modal: opens on "Add Entry" click to capture a table-row entry.
            const addEntryOverlay = document.getElementById('ctcAddEntryOverlay');
            const addEntryModal = addEntryOverlay.querySelector('.ctc-add-entry-modal');
            const addEntryCloseBtn = document.getElementById('ctcAddEntryCloseBtn');
            const addAnotherEntryBtn = document.getElementById('ctcAddAnotherEntryBtn');
            const addEntrySaveBtn = document.getElementById('ctcAddEntrySaveBtn');
            const addEntryBtn = form.querySelector('.ctc-add-entry-btn');

            const openAddEntryModal = () => addEntryOverlay.classList.add('open');
            const closeAddEntryModal = () => {
                addEntryOverlay.classList.remove('open');
                editingRow = null;
            };

            if (addEntryBtn) {
                addEntryBtn.addEventListener('click', (event) => {
                    event.preventDefault();
                    editingRow = null;
                    resetAddEntryForm();
                    openAddEntryModal();
                });
            }

            addEntryCloseBtn.addEventListener('click', closeAddEntryModal);

            addEntryOverlay.addEventListener('click', (event) => {
                if (event.target === addEntryOverlay) closeAddEntryModal();
            });

            // Live-compute the Assessed Value "Total" field as Land + Improv't.
            const entryLandInput = addEntryModal.querySelector('[name="entry_assessed_value_land"]');
            const entryImprovementInput = addEntryModal.querySelector('[name="entry_assessed_value_improvement"]');
            const entryAssessedTotalInput = addEntryModal.querySelector('[name="entry_assessed_value_total"]');

            const formatAmount = (value) => {
                const num = parseFloat(unformatNumber(value));
                return num ? num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '';
            };

            const updateAssessedTotal = () => {
                const land = parseFloat(unformatNumber(entryLandInput.value)) || 0;
                const improvement = parseFloat(unformatNumber(entryImprovementInput.value)) || 0;
                const total = land + improvement;
                entryAssessedTotalInput.value = total ? formatAmount(total) : '';
            };

            entryLandInput.addEventListener('input', updateAssessedTotal);
            entryImprovementInput.addEventListener('input', updateAssessedTotal);

            // Save / Add Another Entry: write the entry's values into the next empty table row.
            const formatTableAmount = (value) => {
                const num = parseFloat(unformatNumber(value));
                return num ? `₱${num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : '';
            };

            // A row is empty when all of its data cells (everything before the Action column) are blank.
            const isRowEmpty = (row) => [...row.children].slice(0, -1).every((cell) => cell.textContent.trim() === '');

            const saveEntryToTable = () => {
                const rows = preview.querySelectorAll('.ctcp-rpt-table tbody tr');
                const targetRow = editingRow || [...rows].find(isRowEmpty);
                if (!targetRow) return false;

                const getValue = (name) => addEntryModal.querySelector(`[name="${name}"]`)?.value.trim() || '';

                const land = parseFloat(unformatNumber(getValue('entry_assessed_value_land'))) || 0;
                const improvement = parseFloat(unformatNumber(getValue('entry_assessed_value_improvement'))) || 0;

                const cellValues = [
                    getValue('entry_declared_owner'),
                    getValue('entry_location'),
                    getValue('entry_lot_block_number'),
                    getValue('entry_tax_declaration_number'),
                    formatTableAmount(land),
                    formatTableAmount(improvement),
                    formatTableAmount(land + improvement),
                    formatTableAmount(getValue('entry_tax_due')),
                    getValue('entry_installment_no'),
                    formatTableAmount(getValue('entry_installment_payment')),
                    formatTableAmount(getValue('entry_full_payment')),
                    formatTableAmount(getValue('entry_penalty_per_cent')),
                    formatTableAmount(getValue('entry_total')),
                ];

                cellValues.forEach((value, index) => {
                    targetRow.children[index].textContent = value || ' ';
                });

                editingRow = null;

                return true;
            };

            const resetAddEntryForm = () => {
                addEntryModal.querySelectorAll('input').forEach((input) => { input.value = ''; });
            };

            addAnotherEntryBtn.addEventListener('click', () => {
                saveEntryToTable();
                resetAddEntryForm();
            });

            addEntrySaveBtn.addEventListener('click', () => {
                saveEntryToTable();
                resetAddEntryForm();
                closeAddEntryModal();
            });

            // Populate the Add Entry modal's fields from an existing table row's values, for editing.
            const populateEntryFormFromRow = (row) => {
                const cells = row.children;
                const setValue = (name, value) => {
                    const input = addEntryModal.querySelector(`[name="${name}"]`);
                    if (input) input.value = value;
                };

                setValue('entry_declared_owner', cells[0].textContent.trim());
                setValue('entry_location', cells[1].textContent.trim());
                setValue('entry_lot_block_number', cells[2].textContent.trim());
                setValue('entry_tax_declaration_number', cells[3].textContent.trim());
                setValue('entry_assessed_value_land', unformatNumber(cells[4].textContent));
                setValue('entry_assessed_value_improvement', unformatNumber(cells[5].textContent));
                setValue('entry_assessed_value_total', unformatNumber(cells[6].textContent));
                setValue('entry_tax_due', unformatNumber(cells[7].textContent));
                setValue('entry_installment_no', cells[8].textContent.trim());
                setValue('entry_installment_payment', unformatNumber(cells[9].textContent));
                setValue('entry_full_payment', unformatNumber(cells[10].textContent));
                setValue('entry_penalty_per_cent', unformatNumber(cells[11].textContent));
                setValue('entry_total', unformatNumber(cells[12].textContent));
            };

            // Row actions: "Edit" opens the modal pre-filled with that row's data; "Remove" clears the row.
            preview.querySelector('.ctcp-rpt-table tbody').addEventListener('click', (event) => {
                const row = event.target.closest('tr');
                if (!row) return;

                if (event.target.classList.contains('ctcp-rpt-action-edit')) {
                    if (isRowEmpty(row)) return;
                    populateEntryFormFromRow(row);
                    editingRow = row;
                    openAddEntryModal();
                    return;
                }

                if (event.target.classList.contains('ctcp-rpt-action-remove')) {
                    [...row.children].slice(0, -1).forEach((cell) => { cell.textContent = ' '; });
                    if (editingRow === row) editingRow = null;
                }
            });

            // Add Entry: validate required fields, save via AJAX, then redirect to Collection Management.
            const requiredIds = ['rpt-client-name', 'rpt-amount-paid'];

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

                const formData = new FormData(form);
                if (amountInput) formData.set('amount_paid', unformatNumber(amountInput.value));

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
                        window.location.href = data.redirect;
                    })
                    .catch(() => {
                        alert('Something went wrong while saving. Please try again.');
                    });
            });
        })();
    </script>
</x-layout>
