<x-layout>
    @php
        $tmpRoute = route('official-receipts-accountable-forms');
        $routeName = 'official-receipts-accountable-forms';
    @endphp

    <div class="x-header-container sub-nav-sticky">
        <x-header title="Official Receipt & Accountable Forms"
            :tmpRoute="$tmpRoute"
            :routeName="$routeName"
        />
    </div>

    <div class="collection-content">
        <div class="collection-toolbar">
            <form class="search-group" role="search" method="GET" id="form-stock-search-form">
                <input type="search" name="search" class="search-input" id="form-stock-search-input" placeholder="Search Form" value="{{ request('search') }}" autocomplete="off">
            </form>
        </div>

        <div id="form-stocks-table-container">
            @include('official-receipt-accountable-forms.partials.forms-table')
        </div>

        @unless (auth()->user()?->hasRole('collector'))
            @include('collection-management.transaction-entry.partials.add-batch-modal')
        @endunless
        @if (auth()->user()?->hasRole('collector'))
            @include('official-receipt-accountable-forms.partials.batch-request-modal')
        @endif
        @include('official-receipt-accountable-forms.partials.export-modal')
        @include('official-receipt-accountable-forms.partials.report-preview-modal')
    </div>

    @push('scripts')
        <style>
            @page { size: 11in 8.5in landscape; margin: 0.4in; }

            @media print {
                body * { visibility: hidden; }
                #reportPrintArea, #reportPrintArea * { visibility: visible; }
                #reportPrintArea {
                    position: fixed; top: 0; left: 0; width: 100%;
                    border: none !important; background: #fff !important;
                }
                #reportPrintArea .orabf-report-doc-header,
                #reportPrintArea .orabf-report-officer-row,
                #reportPrintArea .orabf-report-table th,
                #reportPrintArea .orabf-report-table td,
                #reportPrintArea .orabf-report-total-row td {
                    background: #fff !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
            }
        </style>
        <script>
            (function () {
                const container = document.getElementById('form-stocks-table-container');
                const searchInput = document.getElementById('form-stock-search-input');
                const searchForm = document.getElementById('form-stock-search-form');
                let debounceTimer;

                function fetchAndRender(params) {
                    params.delete('page');

                    const baseUrl = searchForm.action.split('?')[0];
                    const query = params.toString();
                    const url = query ? `${baseUrl}?${query}` : baseUrl;

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then((response) => response.text())
                        .then((html) => {
                            container.innerHTML = html;
                            window.history.replaceState({}, '', url);
                        });
                }

                function reloadTable() {
                    const params = new URLSearchParams(window.location.search);

                    if (searchInput.value) {
                        params.set('search', searchInput.value);
                    } else {
                        params.delete('search');
                    }

                    fetchAndRender(params);
                }

                searchInput.addEventListener('input', function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(reloadTable, 300);
                });

                searchForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    clearTimeout(debounceTimer);
                    reloadTable();
                });

                container.addEventListener('click', function (event) {
                    const link = event.target.closest('.sortable-header');

                    if (!link) {
                        return;
                    }

                    event.preventDefault();

                    const linkUrl = new URL(link.href);
                    const params = new URLSearchParams(window.location.search);

                    params.set('sort', linkUrl.searchParams.get('sort'));
                    params.set('direction', linkUrl.searchParams.get('direction'));

                    if (searchInput.value) {
                        params.set('search', searchInput.value);
                    } else {
                        params.delete('search');
                    }

                    fetchAndRender(params);
                });

                if (document.getElementById('formBatchModalOverlay')) {
                    const modalOverlay = document.getElementById('formBatchModalOverlay');
                    const modalTitle = document.getElementById('formBatchModalTitle');
                    const modalForm = document.getElementById('formBatchForm');
                    const successAlert = document.getElementById('formBatchSuccessAlert');
                    const successAlertSubtitle = document.getElementById('formBatchSuccessAlertSubtitle');
                    let successAlertTimer;
                    let currentFormCode = '';

                    var showSuccessAlert = function (formCode) {
                        successAlertSubtitle.textContent = formCode;
                        successAlert.classList.add('show');

                        clearTimeout(successAlertTimer);
                        successAlertTimer = setTimeout(() => {
                            successAlert.classList.remove('show');
                        }, 3000);
                    };

                    var openBatchModal = function (formStockId, formCode, nextSerial) {
                        currentFormCode = formCode;
                        modalTitle.textContent = `Add new batch of ${formCode}`;
                        modalForm.action = `/official-receipts-accountable-forms/${formStockId}/batches`;

                        const ssnInput = modalForm.querySelector('[name="starting_serial_number"]');
                        if (ssnInput) {
                            ssnInput.value = nextSerial || '';
                        }

                        modalOverlay.classList.add('open');
                    };

                    var closeBatchModal = function () {
                        modalOverlay.classList.remove('open');
                        modalForm.reset();
                    };

                    container.addEventListener('click', function (event) {
                        const trigger = event.target.closest('.js-add-batch');

                        if (!trigger) {
                            return;
                        }

                        event.preventDefault();
                        openBatchModal(trigger.dataset.formStockId, trigger.dataset.formCode, trigger.dataset.nextSerial);
                    });

                    document.getElementById('formBatchCloseBtn').addEventListener('click', closeBatchModal);

                    modalForm.addEventListener('submit', function (event) {
                        event.preventDefault();

                        const submitUrl = modalForm.action + window.location.search;

                        fetch(submitUrl, {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            body: new FormData(modalForm),
                        })
                            .then((response) => {
                                if (!response.ok) {
                                    return response.json().then((data) => {
                                        showToast('Action could not be completed', data.message, 'error');
                                        throw new Error(data.message);
                                    });
                                }

                                return response.text();
                            })
                            .then((html) => {
                                container.innerHTML = html;
                                closeBatchModal();
                                showSuccessAlert(currentFormCode);
                            })
                            .catch(() => {});
                    });
                }

                // ── Request New Batch modal (Collector) ──────────────────────────
                const requestModalOverlay = document.getElementById('batchRequestModalOverlay');
                if (requestModalOverlay) {
                    const requestModalTitle = document.getElementById('batchRequestModalTitle');
                    const requestForm = document.getElementById('batchRequestForm');
                    const requestSuccessAlert = document.getElementById('batchRequestSuccessAlert');
                    const requestSuccessAlertSubtitle = document.getElementById('batchRequestSuccessAlertSubtitle');
                    let requestSuccessAlertTimer;

                    function showRequestSuccessAlert(formCode) {
                        requestSuccessAlertSubtitle.textContent = formCode;
                        requestSuccessAlert.classList.add('show');

                        clearTimeout(requestSuccessAlertTimer);
                        requestSuccessAlertTimer = setTimeout(() => {
                            requestSuccessAlert.classList.remove('show');
                        }, 3000);
                    }

                    function openRequestModal(formStockId, formCode) {
                        requestModalTitle.textContent = `Request new batch of ${formCode}`;
                        requestForm.action = `/official-receipts-accountable-forms/${formStockId}/batch-requests`;
                        requestModalOverlay.classList.add('open');
                    }

                    function closeRequestModal() {
                        requestModalOverlay.classList.remove('open');
                        requestForm.reset();
                    }

                    container.addEventListener('click', function (event) {
                        const trigger = event.target.closest('.js-request-batch');
                        if (!trigger) return;

                        event.preventDefault();
                        openRequestModal(trigger.dataset.formStockId, trigger.dataset.formCode);
                    });

                    document.getElementById('batchRequestCloseBtn').addEventListener('click', closeRequestModal);

                    requestForm.addEventListener('submit', function (event) {
                        event.preventDefault();
                        const formCode = requestModalTitle.textContent.replace('Request new batch of ', '');

                        fetch(requestForm.action, {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            body: new FormData(requestForm),
                        })
                            .then((response) => response.json().then((data) => ({ ok: response.ok, data })))
                            .then(({ ok, data }) => {
                                if (!ok) {
                                    showToast('Action could not be completed', data.message, 'error');
                                    return;
                                }
                                closeRequestModal();
                                showRequestSuccessAlert(formCode);
                            })
                            .catch(() => showToast('Action could not be completed', 'Something went wrong. Please try again.', 'error'));
                    });
                }

                // ── Export date-range modal ──────────────────────────────────────
                const exportModalOverlay = document.getElementById('exportModalOverlay');
                const exportForm         = document.getElementById('exportForm');
                let currentFormStockId   = null;

                function closeExportModal() {
                    exportModalOverlay.classList.remove('open');
                    exportForm.reset();
                }

                container.addEventListener('click', function (event) {
                    const trigger = event.target.closest('.js-open-export');
                    if (!trigger) return;
                    event.preventDefault();
                    currentFormStockId = trigger.dataset.formStockId;
                    exportForm.dataset.formStockId = currentFormStockId;
                    exportModalOverlay.classList.add('open');
                });

                document.getElementById('exportModalCloseBtn').addEventListener('click', closeExportModal);

                exportModalOverlay.addEventListener('click', function (event) {
                    if (event.target === exportModalOverlay) closeExportModal();
                });

                // "View" — fetch preview JSON and show report preview modal
                exportForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    const params    = new URLSearchParams(new FormData(exportForm));
                    const fsId      = exportForm.dataset.formStockId;
                    const previewUrl = `/official-receipts-accountable-forms/${fsId}/preview?${params}`;

                    const submitBtn     = exportForm.querySelector('[type="submit"]');
                    const originalHTML  = submitBtn.innerHTML;
                    submitBtn.disabled  = true;
                    submitBtn.textContent = 'Loading…';

                    fetch(previewUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then((r) => r.json())
                        .then((data) => {
                            // Populate preview modal
                            document.getElementById('rpFormName').textContent = data.form_name;
                            document.getElementById('rpPeriod').textContent   = data.period;

                            const tbody    = document.getElementById('reportPreviewTableBody');
                            const emptyRow = document.getElementById('reportPreviewEmptyRow');

                            // Remove old data rows (keep empty row for now)
                            [...tbody.querySelectorAll('tr.rp-data-row')].forEach((r) => r.remove());

                            if (data.batches.length === 0) {
                                emptyRow.style.display = '';
                            } else {
                                emptyRow.style.display = 'none';
                                data.batches.forEach((b) => {
                                    const tr = document.createElement('tr');
                                    tr.className = 'rp-data-row';
                                    tr.innerHTML = `
                                        <td>${b.starting_serial}</td>
                                        <td>${b.ending_serial}</td>
                                        <td class="rp-num">${b.initial_qty}</td>
                                        <td class="rp-num">${b.used}</td>
                                        <td class="rp-num">${b.remaining}</td>
                                        <td>${b.added_date}</td>
                                        <td>${b.added_time}</td>
                                        <td>${b.status}</td>
                                        <td>${b.added_by}</td>
                                        <td>${b.remarks}</td>
                                    `;
                                    tbody.appendChild(tr);
                                });
                            }

                            document.getElementById('rpTotalRemaining').textContent = data.total_remaining ?? 0;

                            // Store export params on export button for CSV download
                            const exportBtn = document.getElementById('reportPreviewExportBtn');
                            exportBtn.dataset.exportUrl = `/official-receipts-accountable-forms/${fsId}/export?${params}`;

                            closeExportModal();
                            document.getElementById('reportPreviewOverlay').classList.add('open');
                        })
                        .catch(() => showToast('Action could not be completed', 'Failed to load preview. Please try again.', 'error'))
                        .finally(() => {
                            submitBtn.disabled  = false;
                            submitBtn.innerHTML = originalHTML;
                        });
                });

                // ── Report preview modal ─────────────────────────────────────────
                const previewOverlay = document.getElementById('reportPreviewOverlay');

                function closePreviewModal() {
                    previewOverlay.classList.remove('open');
                }

                document.getElementById('reportPreviewCloseBtn').addEventListener('click', closePreviewModal);

                previewOverlay.addEventListener('click', function (event) {
                    if (event.target === previewOverlay) closePreviewModal();
                });

                document.getElementById('reportPreviewExportBtn').addEventListener('click', function () {
                    let url = this.dataset.exportUrl;
                    if (url) {
                        const officerName = (document.getElementById('rpOfficerName').textContent || '').trim();
                        const designation = (document.getElementById('rpDesignation').textContent || '').trim();
                        const extra = new URLSearchParams({ officer_name: officerName, designation: designation });
                        window.location.href = url + '&' + extra.toString();
                        closePreviewModal();
                    }
                });

                document.getElementById('reportPreviewPrintBtn').addEventListener('click', function () {
                    window.print();
                });
            })();
        </script>
    @endpush
</x-layout>
