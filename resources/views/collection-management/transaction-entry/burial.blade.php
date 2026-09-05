<x-layout>
    <div class="x-header-container sub-nav-sticky">
        <div class="container-title">
            <div style="display: flex; flex-direction: column;">
                <span class="page-title">Collection Management</span>
                <p class="page-links">
                    <a href="{{ route('home') }}">Home</a> |
                    <a href="{{ route('collections') }}">Collections Management</a> |
                    <a href="{{ route('transaction-entry') }}">Transactions Entry</a> |
                    <span class="page-links-accent">Burial Permit</span>
                </p>
            </div>
        </div>
        @include('collection-management.transaction-entry.partials.form-tabs')
    </div>

    <div class="collection-content">
        <form class="burial-page" id="burialForm" method="POST" action="{{ route('transaction-entry.burial.store', $form->id, false) }}">
            @csrf

            {{-- Masthead --}}
            <div class="burial-meta">
                <span>Accountable Form No. 51 &nbsp;·&nbsp; Revised 1993</span>
                <span>ORIGINAL</span>
            </div>
            <div class="burial-masthead">
                <p class="burial-or-title">OFFICIAL RECEIPT</p>
                <p class="burial-or-sub">of the Republic of the Philippines</p>
                <div class="burial-no-row">
                    <span class="burial-seal"><x-bx-shield class="icon" /></span>
                    <span class="burial-no-label">No.</span>
                    <div class="burial-no-box">
                        <input type="text" id="burial-certificate-number" name="certificate_number" value="{{ $certificateNumber }}" autocomplete="off">
                    </div>
                    <input type="text" id="burial-series-letter" name="series_letter" class="burial-series" value="C" maxlength="5" aria-label="Series letter">
                </div>
                <p class="burial-serial-hint">Serial pulled from the Form 58 booklet · editable</p>
            </div>
            <div class="burial-title-bar">City / Municipal Burial Permit and Fee Receipt</div>

            {{-- Applicant / issuing LGU --}}
            <div class="ctc-or2-bar"><p>Applicant &amp; Issuing LGU</p></div>
            <div class="ctc-or2-row">
                <div class="ctc-or2-cell ctc-or2-cell--captioned">
                    <input type="text" id="burial-applicant-name" name="applicant_name" placeholder=" ">
                    <label class="ctc-or2-caption" for="burial-applicant-name">Mr. (Applicant)</label>
                </div>
                <div class="ctc-or2-cell ctc-or2-cell--captioned">
                    <input type="text" id="burial-city-municipality" name="city_municipality" placeholder=" ">
                    <label class="ctc-or2-caption" for="burial-city-municipality">To the City / Municipality of</label>
                </div>
            </div>
            <div class="ctc-or2-cell ctc-or2-cell--captioned">
                <input type="text" id="burial-province" name="province" placeholder=" ">
                <label class="ctc-or2-caption" for="burial-province">Province of</label>
            </div>

            {{-- Permission granted --}}
            <div class="burial-permission">
                <span class="burial-permission-label">Permission is hereby granted to</span>
                <div class="burial-permission-opts">
                    @foreach (['Inter', 'Disinter', 'Remove'] as $opt)
                        <label class="burial-radio">
                            <input type="radio" name="permission_type" value="{{ $opt }}" @checked($opt === 'Inter')>
                            <span class="burial-radio-dot"></span>{{ $opt }}
                        </label>
                    @endforeach
                </div>
                <span class="burial-permission-tail">the remains of —</span>
            </div>

            {{-- Deceased details (paper lines 1–6) --}}
            <div class="ctc-or2-bar"><p>Deceased Details</p></div>
            <div class="ctc-or2-cell ctc-or2-cell--captioned" id="burial-deceased-name-cell">
                <input type="text" id="burial-deceased-name" name="deceased_name" placeholder=" ">
                <label class="ctc-or2-caption" for="burial-deceased-name">1. Name</label>
            </div>
            <div class="ctc-or2-row">
                <div class="ctc-or2-cell ctc-or2-cell--captioned">
                    <input type="text" id="burial-nationality" name="nationality" placeholder=" ">
                    <label class="ctc-or2-caption" for="burial-nationality">2. Nationality</label>
                </div>
                <div class="ctc-or2-cell ctc-or2-cell--captioned">
                    <input type="number" id="burial-age" name="age" placeholder=" " min="0" max="200">
                    <label class="ctc-or2-caption" for="burial-age">3. Age (Years)</label>
                </div>
                <div class="ctc-or2-cell ctc-or2-cell--captioned">
                    <input type="text" id="burial-sex" name="sex" placeholder=" ">
                    <label class="ctc-or2-caption" for="burial-sex">Sex</label>
                </div>
            </div>
            <div class="ctc-or2-row">
                <div class="ctc-or2-cell ctc-or2-cell--captioned ctc-or2-cell--date filled">
                    <input type="date" id="burial-date-of-death" name="date_of_death">
                    <label class="ctc-or2-caption" for="burial-date-of-death">4. Date of Death</label>
                </div>
                <div class="ctc-or2-cell ctc-or2-cell--captioned">
                    <input type="text" id="burial-cause-of-death" name="cause_of_death" placeholder=" ">
                    <label class="ctc-or2-caption" for="burial-cause-of-death">5. Cause of Death</label>
                </div>
            </div>
            <div class="ctc-or2-cell ctc-or2-cell--captioned">
                <input type="text" id="burial-cemetery-name" name="cemetery_name" placeholder=" ">
                <label class="ctc-or2-caption" for="burial-cemetery-name">6. Name of Cemetery</label>
            </div>

            {{-- In case of disinterment (paper lines 7–9) --}}
            <p class="burial-disinter-note">* In case of disinterment — enabled only when “Disinter” is selected</p>
            <div class="burial-disinter-group" id="burialDisinterGroup">
                <div class="ctc-or2-row">
                    <div class="ctc-or2-cell ctc-or2-cell--captioned">
                        <input type="text" id="burial-infectious" name="infectious" placeholder=" " disabled>
                        <label class="ctc-or2-caption" for="burial-infectious">7. Infectious / Non-infectious</label>
                    </div>
                    <div class="ctc-or2-cell ctc-or2-cell--captioned">
                        <input type="text" id="burial-embalmed" name="embalmed" placeholder=" " disabled>
                        <label class="ctc-or2-caption" for="burial-embalmed">8. Body Embalmed / Not</label>
                    </div>
                </div>
                <div class="ctc-or2-cell ctc-or2-cell--captioned">
                    <input type="text" id="burial-disposition" name="disposition" placeholder=" " disabled>
                    <label class="ctc-or2-caption" for="burial-disposition">9. Disposition of Remains</label>
                </div>
            </div>

            {{-- Fee receipt --}}
            <div class="ctc-or2-bar"><p>Fee Receipt</p></div>
            <div class="ctc-or2-row">
                <div class="ctc-or2-cell ctc-or2-cell--captioned burial-cell--peso">
                    <input type="number" id="burial-fee-amount" name="fee_amount" placeholder=" " min="0" step="0.01">
                    <label class="ctc-or2-caption" for="burial-fee-amount">10. Amount of Fee (per Ordinance)</label>
                </div>
                <div class="ctc-or2-cell ctc-or2-cell--captioned ctc-or2-cell--date filled">
                    <input type="date" id="burial-date-issued" name="date_issued" value="{{ now()->toDateString() }}">
                    <label class="ctc-or2-caption" for="burial-date-issued">Date Issued</label>
                </div>
            </div>

            <div class="burial-cert">
                <p class="burial-cert-text">
                    I hereby certify that I have this day issued this burial permit and have received the fee above stated in the amount of
                    <span class="burial-cert-amount" data-burial-preview="fee_amount_words">₱ 0.00</span>.
                </p>
                <div class="ctc-or2-cell ctc-or2-cell--captioned burial-sig-input">
                    <input type="text" id="burial-municipal-secretary" name="municipal_secretary" placeholder=" " value="{{ auth()->user()?->name ?? '' }}">
                    <label class="ctc-or2-caption" for="burial-municipal-secretary">Municipal Secretary / Treasurer</label>
                </div>
            </div>

            @include('collection-management.transaction-entry.partials.payment-section')

            <div class="ctc-or2-actions">
                <a href="{{ route('transaction-entry') }}" class="burial-cancel-btn">Cancel</a>
                <button type="submit" class="ctc-or2-proceed-btn" id="burialProceedBtn">Proceed</button>
            </div>
        </form>
    </div>

    @include('partials.select-enhancer')

    @include('collection-management.transaction-entry.partials.burial-preview-modal')

    <script>
        (function () {
            const form = document.getElementById('burialForm');
            if (!form) return;

            // Floating-label "filled" state (matches the OR/RPT + Marriage forms).
            const updateCellState = (input) => {
                const cell = input.closest('.ctc-or2-cell');
                if (cell && !cell.classList.contains('ctc-or2-cell--date')) {
                    cell.classList.toggle('filled', input.value.trim() !== '');
                }
            };
            form.querySelectorAll('.ctc-or2-cell input').forEach((input) => {
                updateCellState(input);
                input.addEventListener('input', () => updateCellState(input));
            });

            // Fields 7–9 enable only for a disinterment.
            const disinterGroup = document.getElementById('burialDisinterGroup');
            const disinterInputs = disinterGroup.querySelectorAll('input');
            const applyPermission = () => {
                const isDisinter = form.querySelector('input[name="permission_type"]:checked')?.value === 'Disinter';
                disinterGroup.classList.toggle('is-disabled', !isDisinter);
                disinterInputs.forEach((input) => {
                    input.disabled = !isDisinter;
                    if (!isDisinter) {
                        input.value = '';
                        updateCellState(input);
                    }
                });
            };
            form.querySelectorAll('input[name="permission_type"]').forEach((r) => r.addEventListener('change', () => { applyPermission(); syncPreviews(); }));
            applyPermission();

            const num2 = (v) => {
                const n = parseFloat(v);
                return isNaN(n) ? '' : n.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            };
            const fmtDate = (v) => {
                if (!v) return '';
                const d = new Date(v + 'T00:00:00');
                return isNaN(d) ? v : d.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
            };

            // Mirror the form into the preview modal (faithful replica).
            const syncPreviews = () => {
                document.querySelectorAll('[data-burial-preview]').forEach((el) => {
                    const name = el.dataset.burialPreview;
                    if (name === 'fee_amount') {
                        el.textContent = num2(form.querySelector('[name="fee_amount"]').value);
                        return;
                    }
                    if (name === 'date_of_death' || name === 'date_issued') {
                        el.textContent = fmtDate(form.querySelector(`[name="${name}"]`)?.value);
                        return;
                    }
                    if (name === 'date_issued_yy') {
                        const v = form.querySelector('[name="date_issued"]').value;
                        el.textContent = v ? v.slice(2, 4) : '';
                        return;
                    }
                    const input = form.querySelector(`[name="${name}"]`);
                    if (input) el.textContent = input.value || '';
                });
                // Highlight the chosen permission in the Inter/Disinter/Remove box.
                const perm = form.querySelector('input[name="permission_type"]:checked')?.value;
                document.querySelectorAll('.burial-doc-perm-opt').forEach((opt) => {
                    opt.classList.toggle('is-selected', opt.dataset.permValue === perm);
                });
            };
            form.querySelectorAll('input').forEach((input) => input.addEventListener('input', syncPreviews));
            syncPreviews();

            // Preview modal.
            const previewOverlay = document.getElementById('burialPreviewOverlay');
            document.getElementById('burialPreviewCloseBtn').addEventListener('click', () => previewOverlay.classList.remove('open'));

            // Proceed: validate required, then preview.
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const nameInput = form.querySelector('#burial-deceased-name');
                const isEmpty = !nameInput.value.trim();
                nameInput.closest('.ctc-or2-cell').classList.toggle('has-error', isEmpty);
                if (isEmpty) { nameInput.focus(); return; }
                syncPreviews();
                previewOverlay.classList.add('open');
            });
            form.querySelector('#burial-deceased-name').addEventListener('input', function () {
                if (this.value.trim()) this.closest('.ctc-or2-cell').classList.remove('has-error');
            });

            // Save from the preview.
            const saveBtn = document.getElementById('burialPreviewSaveBtn');
            saveBtn.addEventListener('click', () => {
                saveBtn.disabled = true;
                const original = saveBtn.textContent;
                saveBtn.textContent = 'Saving…';
                fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form),
                })
                    .then(async (r) => {
                        const data = await r.json().catch(() => ({}));
                        if (!r.ok) {
                            const msg = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Something went wrong while saving. Please try again.');
                            throw new Error(msg);
                        }
                        return data;
                    })
                    .then((data) => { window.location.href = data.redirect; })
                    .catch((err) => {
                        saveBtn.disabled = false;
                        saveBtn.textContent = original;
                        showToast('Could not save', err.message || 'Something went wrong while saving. Please try again.', 'error');
                    });
            });

            document.getElementById('burialPreviewPrintBtn').addEventListener('click', () => window.print());
        })();
    </script>
</x-layout>
