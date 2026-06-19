<x-layout>
    <div class="x-header-container sub-nav-sticky">
        <div class="container-title">
            <div style="display: flex; flex-direction: column;">
                <span class="page-title">Collection Management</span>
                <p class="page-links">
                    <a href="{{ route('home') }}">Home</a> |
                    <a href="{{ route('collections') }}">Collections Management</a> |
                    <a href="{{ route('transaction-entry') }}">Transactions Entry</a> |
                    <span class="page-links-accent">Marriage Certificate</span>
                </p>
            </div>
        </div>
        <div class="ctc-tabs-row">
            <a href="{{ route('transaction-entry') }}" class="ctc-tab">Transactions Log</a>
            <div class="ctc-tab active">New Entry</div>
        </div>
    </div>

    <div class="collection-content">
        <form class="mc-page" id="mcForm" method="POST" action="{{ route('transaction-entry.marriage-certificate.store', $form->id, false) }}">
            @csrf
            <input type="hidden" name="email" id="mcEmailHidden">
            <input type="hidden" name="message" id="mcMessageHidden">

            {{-- Left: Form panel --}}
            <div class="mc-form-col">

                <div class="ctc-or2-bar"><p>Certificate Number</p></div>
                <div class="ctc-or2-cell ctc-or2-cell--captioned">
                    <input type="text" id="mc-certificate-number" name="certificate_number" value="{{ $certificateNumber }}" placeholder=" ">
                    <label class="ctc-or2-caption" for="mc-certificate-number">Serial / Certificate No.</label>
                </div>

                <div class="ctc-or2-bar"><p>Husband's Details</p></div>
                <div class="ctc-or2-cell ctc-or2-cell--captioned" id="mc-husband-name-cell">
                    <input type="text" id="mc-husband-name" name="husband_name" placeholder=" ">
                    <label class="ctc-or2-caption" for="mc-husband-name">Husband's Full Name</label>
                </div>
                <div class="ctc-or2-row">
                    <div class="ctc-or2-cell ctc-or2-cell--captioned">
                        <input type="number" id="mc-husband-age-years" name="husband_age_years" placeholder=" " min="0" max="255">
                        <label class="ctc-or2-caption" for="mc-husband-age-years">Age (Years)</label>
                    </div>
                    <div class="ctc-or2-cell ctc-or2-cell--captioned">
                        <input type="number" id="mc-husband-age-months" name="husband_age_months" placeholder=" " min="0" max="11">
                        <label class="ctc-or2-caption" for="mc-husband-age-months">Age (Months)</label>
                    </div>
                </div>
                <div class="ctc-or2-cell ctc-or2-cell--captioned">
                    <input type="text" id="mc-husband-address" name="husband_address" placeholder=" ">
                    <label class="ctc-or2-caption" for="mc-husband-address">Husband's Address</label>
                </div>

                <div class="ctc-or2-bar"><p>Wife's Details</p></div>
                <div class="ctc-or2-cell ctc-or2-cell--captioned" id="mc-wife-name-cell">
                    <input type="text" id="mc-wife-name" name="wife_name" placeholder=" ">
                    <label class="ctc-or2-caption" for="mc-wife-name">Wife's Full Name</label>
                </div>
                <div class="ctc-or2-row">
                    <div class="ctc-or2-cell ctc-or2-cell--captioned">
                        <input type="number" id="mc-wife-age-years" name="wife_age_years" placeholder=" " min="0" max="255">
                        <label class="ctc-or2-caption" for="mc-wife-age-years">Age (Years)</label>
                    </div>
                    <div class="ctc-or2-cell ctc-or2-cell--captioned">
                        <input type="number" id="mc-wife-age-months" name="wife_age_months" placeholder=" " min="0" max="11">
                        <label class="ctc-or2-caption" for="mc-wife-age-months">Age (Months)</label>
                    </div>
                </div>
                <div class="ctc-or2-cell ctc-or2-cell--captioned">
                    <input type="text" id="mc-wife-address" name="wife_address" placeholder=" ">
                    <label class="ctc-or2-caption" for="mc-wife-address">Wife's Address</label>
                </div>

                <div class="ctc-or2-bar"><p>In Witness Whereof Details</p></div>
                <div class="ctc-or2-row">
                    <div class="ctc-or2-cell ctc-or2-cell--captioned">
                        <input type="text" id="mc-witness-day" name="witness_day" placeholder=" ">
                        <label class="ctc-or2-caption" for="mc-witness-day">Day</label>
                    </div>
                    <div class="ctc-or2-cell ctc-or2-cell--captioned">
                        <input type="text" id="mc-witness-month" name="witness_month" placeholder=" ">
                        <label class="ctc-or2-caption" for="mc-witness-month">Month</label>
                    </div>
                    <div class="ctc-or2-cell ctc-or2-cell--captioned">
                        <input type="text" id="mc-witness-year" name="witness_year" placeholder=" ">
                        <label class="ctc-or2-caption" for="mc-witness-year">Year (Last 2 Digits)</label>
                    </div>
                </div>

                <div class="ctc-or2-bar"><p>Instructions Details</p></div>
                <div class="ctc-or2-row">
                    <div class="ctc-or2-cell ctc-or2-cell--captioned">
                        <input type="text" id="mc-instructions-day" name="instructions_day" placeholder=" ">
                        <label class="ctc-or2-caption" for="mc-instructions-day">Day</label>
                    </div>
                    <div class="ctc-or2-cell ctc-or2-cell--captioned">
                        <input type="text" id="mc-instructions-month" name="instructions_month" placeholder=" ">
                        <label class="ctc-or2-caption" for="mc-instructions-month">Month</label>
                    </div>
                    <div class="ctc-or2-cell ctc-or2-cell--captioned">
                        <input type="text" id="mc-instructions-year" name="instructions_year" placeholder=" ">
                        <label class="ctc-or2-caption" for="mc-instructions-year">Year (Last 2 Digits)</label>
                    </div>
                </div>
                <div class="ctc-or2-row">
                    <div class="ctc-or2-cell ctc-or2-cell--captioned">
                        <input type="text" id="mc-registry-number" name="registry_number" placeholder=" ">
                        <label class="ctc-or2-caption" for="mc-registry-number">Registry Number</label>
                    </div>
                    <div class="ctc-or2-cell ctc-or2-cell--captioned">
                        <input type="text" id="mc-local-civil-registrar-of" name="local_civil_registrar_of" placeholder=" ">
                        <label class="ctc-or2-caption" for="mc-local-civil-registrar-of">Local Civil Registrar of</label>
                    </div>
                </div>

                <div class="ctc-or2-actions">
                    <button type="submit" class="ctc-or2-proceed-btn" id="mcProceedBtn">Proceed</button>
                </div>
            </div>

            {{-- Right: Live document preview --}}
            <div class="mc-paper-col">
                <div class="mc-paper-wrap">
                    @include('collection-management.transaction-entry.partials.mc-document')
                </div>
            </div>
        </form>
    </div>

    @include('collection-management.transaction-entry.partials.mc-print-preview-modal')
    @include('collection-management.transaction-entry.partials.mc-send-modal')

    <script>
        (function () {
            const form = document.getElementById('mcForm');
            if (!form) return;

            // OR RPT-style cell "filled" state for floating labels.
            const updateCellState = (input) => {
                const cell = input.closest('.ctc-or2-cell');
                if (cell) cell.classList.toggle('filled', input.value.trim() !== '');
            };

            form.querySelectorAll('.ctc-or2-cell input').forEach((input) => {
                updateCellState(input);
                input.addEventListener('input', () => updateCellState(input));
            });

            const ordinal = (n) => {
                const num = parseInt(n, 10);
                if (isNaN(num) || n === '') return n;
                const s = ['th', 'st', 'nd', 'rd'];
                const v = num % 100;
                return num + (s[(v - 20) % 10] || s[v] || s[0]);
            };

            // All [data-mc-preview] elements across both the inline preview and the print modal.
            const syncPreviews = () => {
                document.querySelectorAll('[data-mc-preview]').forEach((el) => {
                    const name = el.dataset.mcPreview;
                    const input = form.querySelector(`[name="${name}"]`);
                    if (!input) return;
                    const value = input.value || '';
                    el.textContent = name.endsWith('_day') ? ordinal(value) : value;
                });
            };

            form.querySelectorAll('.ctc-or2-cell input').forEach((input) => {
                input.addEventListener('input', syncPreviews);
            });

            syncPreviews();

            // Print preview modal.
            const previewOverlay = document.getElementById('mcPreviewOverlay');
            const previewCloseBtn = document.getElementById('mcPreviewCloseBtn');
            const previewPrintBtn = document.getElementById('mcPreviewPrintBtn');

            const openPreview = () => {
                syncPreviews();
                previewOverlay.classList.add('open');
            };
            const closePreview = () => previewOverlay.classList.remove('open');

            previewCloseBtn.addEventListener('click', closePreview);
            previewOverlay.addEventListener('click', (e) => {
                if (e.target === previewOverlay) closePreview();
            });

            // Proceed: validate required fields, then open print preview.
            form.addEventListener('submit', (e) => {
                e.preventDefault();

                const husbandInput = form.querySelector('#mc-husband-name');
                const wifeInput = form.querySelector('#mc-wife-name');
                let valid = true;

                [husbandInput, wifeInput].forEach((input) => {
                    const isEmpty = !input.value.trim();
                    const cell = input.closest('.ctc-or2-cell');
                    if (cell) cell.classList.toggle('has-error', isEmpty);
                    if (isEmpty) valid = false;
                });

                if (!valid) return;
                openPreview();
            });

            // Clear error state on input.
            [form.querySelector('#mc-husband-name'), form.querySelector('#mc-wife-name')].forEach((input) => {
                if (!input) return;
                input.addEventListener('input', () => {
                    const cell = input.closest('.ctc-or2-cell');
                    if (cell && input.value.trim()) cell.classList.remove('has-error');
                });
            });

            // Send modal.
            const sendOverlay = document.getElementById('mcSendOverlay');
            const sendCloseBtn = document.getElementById('mcSendCloseBtn');
            const sendCancelBtn = document.getElementById('mcSendCancelBtn');
            const sendSendBtn = document.getElementById('mcSendSendBtn');
            const emailHidden = document.getElementById('mcEmailHidden');
            const messageHidden = document.getElementById('mcMessageHidden');

            const openSend = () => sendOverlay.classList.add('open');
            const closeSend = () => sendOverlay.classList.remove('open');

            sendCloseBtn.addEventListener('click', closeSend);

            previewPrintBtn.addEventListener('click', openSend);

            sendSendBtn.addEventListener('click', () => {
                emailHidden.value = document.getElementById('mcSendEmail').value;
                messageHidden.value = document.getElementById('mcSendMessage').value;
                submitAndPrint();
            });

            sendCancelBtn.addEventListener('click', () => {
                emailHidden.value = '';
                messageHidden.value = '';
                closeSend();
                submitAndPrint();
            });

            const submitAndPrint = () => {
                const formData = new FormData(form);

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
                        closeSend();
                        window.print();
                        closePreview();
                        window.location.href = data.redirect;
                    })
                    .catch(() => {
                        alert('Something went wrong while saving. Please try again.');
                    });
            };
        })();
    </script>

    <style>
        @page {
            size: 8.5in 13in;
            margin: 0;
        }

        @media print {
            nav.navigation-header,
            .nav-sticky-wrapper,
            .x-header-container,
            .collection-content,
            #mcSendOverlay { display: none !important; }

            .main-container { padding: 0 !important; margin: 0 !important; }

            #mcPreviewOverlay {
                display: block !important;
                position: static !important;
                background: white !important;
                padding: 0 !important;
                inset: auto !important;
            }

            #mcPreviewOverlay .mc-preview-card {
                display: block !important;
                overflow: visible !important;
                height: auto !important;
                box-shadow: none !important;
                padding: 0 !important;
                width: 8.5in !important;
            }

            #mcPreviewOverlay .mc-paper {
                width: 8.5in !important;
                min-height: 13in !important;
                display: flex !important;
                flex-direction: column !important;
                border: none !important;
                background: white !important;
                padding: 0.5in !important;
                box-sizing: border-box !important;
                margin: 0 auto !important;
            }

            /* Font sizes and alignment for print */
            #mcPreviewOverlay .mc-doc-body { text-align: justify !important; }
            #mcPreviewOverlay .mc-doc-body--sm { text-align: justify !important; }
            #mcPreviewOverlay .mc-doc-title-rule { width: 100% !important; }
            #mcPreviewOverlay .mc-doc-title-block { width: 100% !important; }
            #mcPreviewOverlay .mc-doc-line--lg { font-size: 14pt !important; }
            #mcPreviewOverlay .mc-doc-line { font-size: 12pt !important; }
            #mcPreviewOverlay .mc-doc-title-text { font-size: 12pt !important; }
            #mcPreviewOverlay .mc-doc-title-text p { font-size: 12pt !important; }
            #mcPreviewOverlay .mc-doc-body { font-size: 12pt !important; }
            #mcPreviewOverlay .mc-doc-body--sm { font-size: 12pt !important; }
            #mcPreviewOverlay .mc-doc-instruction-title { font-size: 12pt !important; }
            #mcPreviewOverlay .mc-doc-fill { font-size: 12pt !important; }
            #mcPreviewOverlay .mc-doc-sig-right,
            #mcPreviewOverlay .mc-doc-sig-left { font-size: 12pt !important; }
            #mcPreviewOverlay .mc-doc-no { font-size: 14pt !important; }

            .mc-preview-card-header,
            .mc-preview-print-btn { display: none !important; }
        }
    </style>
</x-layout>
