<x-layout>
    @php
        $tmpRoute = route('collections');
        $routeName = 'collections';
    @endphp

    <div class="x-header-container sub-nav-sticky">
        <x-header title="Collection Management"
            :tmpRoute="$tmpRoute"
            :routeName="$routeName"
        />
        <div style="display:flex; align-items: center; border: 0px solid red; margin: 0px;"> 
            <button class="nav-scroll-btn nav-scroll-left" id="scrollLeft">&#8249;</button>
            <nav class="navigation-bar" id="navigationBar">
                <p><a href="{{ route('collections') }}" class=" {{ request()->routeIs('collections') ? 'active' : '' }} "> Transaction Logs </a></p>

                <p><a href="{{ route('transaction-entry') }}" class=" {{ request()->routeIs('transaction-entry') ? 'active' : '' }} ">Transaction Entry</a></p>
            </nav>
            <button class="nav-scroll-btn nav-scroll-right" id="scrollRight">&#8250;</button>
        </div>
    </div>

    @php
        $formTypeOptions = [
            'left' => [
                ['Burial', 'Form 58'],
                ['Corporation Cedula', 'BIR0017'],
                ['Certificate of Ownership of Large Cattle', 'Form 53'],
                ['Certificate of Transfer of Large Cattle', 'Form 28A'],
            ],
            'right' => [
                ['Individual Cedula', 'BIR0016'],
                ['Marriage License', 'Form 10'],
                ['Official Receipt', 'Form 5IC'],
                ['OR RPT', 'Form 56'],
            ],
        ];
        $selectedFormTypes = request('form_type', []);

        $dateStart = request('date_start');
        $dateEnd = request('date_end');
        $dateFilterActive = $dateStart || $dateEnd;
    @endphp

    <div class="collection-content">
        <div class="collection-toolbar">
            <div class="filter-wrapper">
                <button type="button" class="filter-btn" id="filterToggleBtn" aria-label="Filter" aria-expanded="false">
                    <x-icons.filter-fill class="icon" />
                </button>
            </div>

            <form class="search-group" role="search" method="GET" id="transaction-search-form">
                <input type="search" name="search" class="search-input" id="transaction-search-input" placeholder="Search Payee" value="{{ request('search') }}" autocomplete="off">
            </form>

            <div class="date-filter-group {{ $dateFilterActive ? 'open' : '' }}" id="dateFilterGroup">
                <input type="date" class="date-filter-input" id="dateFilterStart" value="{{ $dateStart }}" placeholder="Start">
                <span class="date-filter-separator">-</span>
                <input type="date" class="date-filter-input" id="dateFilterEnd" value="{{ $dateEnd }}" placeholder="End">
                <button type="button" class="btn btn-light date-filter-btn" id="dateFilterBtn">Filter</button>
            </div>
        </div>

        <div class="filter-breadcrumbs" id="filterBreadcrumbs"></div>

        <div class="filter-modal-overlay" id="filterModalOverlay">
            <div class="filter-panel" id="filterPanel">
                <div class="filter-panel-header">
                    <div class="filter-panel-heading">
                        <p class="filter-panel-title">Filter By</p>
                        <p class="filter-panel-subtitle">Pto. Diaz Treasury Management System</p>
                    </div>
                    <button type="button" class="filter-panel-close-btn" id="filterCloseBtn" aria-label="Close">
                        <x-bx-x class="icon" />
                    </button>
                </div>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="filter_date" id="filterDate" data-filter-label="Date" @checked($dateFilterActive)>
                        <span>Date</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="filter_form_type" id="filterFormType" data-filter-label="Form Type" @checked(! empty($selectedFormTypes))>
                        <span>Form Type</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="status" value="Completed" data-filter-label="Completed" @checked(in_array('Completed', request('status', [])))>
                        <span>Completed</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="status" value="Cancelled" data-filter-label="Cancelled" @checked(in_array('Cancelled', request('status', [])))>
                        <span>Cancelled</span>
                    </label>
                </div>

                <div class="filter-select-form @if (! empty($selectedFormTypes)) open @endif" id="filterSelectForm">
                    <p class="filter-select-form-title">Select Form</p>
                    <div class="filter-form-columns">
                        @foreach ($formTypeOptions as $column)
                            <div class="filter-form-column">
                                @foreach ($column as [$name, $code])
                                    <label class="filter-form-option">
                                        <input type="checkbox" name="form_type" value="{{ $code }}" @checked(in_array($code, $selectedFormTypes))>
                                        <span class="filter-form-option-label">
                                            <span class="filter-form-option-name">{{ $name }}</span>
                                            <span class="filter-form-option-code">{{ $code }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="filter-apply-row">
                    <button type="button" class="filter-apply-btn" id="filterApplyBtn">Apply</button>
                </div>
            </div>
        </div>

        {{-- ── Bulk action bar (persists across AJAX table reloads) ─────────── --}}
        <div class="bulk-action-bar" id="bulkActionBar" style="display:none;">
            <span class="bulk-action-count" id="bulkActionCount">0 selected</span>
            <div class="bulk-action-btns">
                @if($isAdmin ?? false)
                    <button type="button" class="bulk-btn bulk-btn--cancel" id="bulkCancelBtn" style="display:none;">Cancel Selected</button>
                @else
                    <button type="button" class="bulk-btn bulk-btn--cancel" id="bulkRequestBtn" style="display:none;">Request Cancel</button>
                @endif
                <button type="button" class="bulk-btn bulk-btn--archive" id="bulkArchiveBtn" style="display:none;">Archive Selected</button>
                <button type="button" class="bulk-btn bulk-btn--clear" id="bulkClearBtn">Clear Selection</button>
            </div>
        </div>

        <div id="transactions-table-container">
            @include('collection-management.partials.transactions-table')
        </div>

        {{-- ── Cancel Request Modal (non-admin) — persists across reloads ───── --}}
        <div class="ctc-modal-overlay" id="tableCancelRequestOverlay">
            <div class="ctc-modal">
                <div class="ctc-modal-header">
                    <p class="ctc-modal-title">Request to Cancel Transaction</p>
                    <button type="button" class="ctc-modal-close-btn" id="tableCancelCloseBtn" aria-label="Close">
                        <x-bx-x class="icon" />
                    </button>
                </div>
                <div class="ctc-modal-body">
                    <div class="ctc-modal-info-row">
                        <span class="ctc-modal-info-label">Serial No.</span>
                        <span class="ctc-modal-info-value" id="tableCancelSerial"></span>
                    </div>
                    <div class="ctc-modal-info-row">
                        <span class="ctc-modal-info-label">Payee</span>
                        <span class="ctc-modal-info-value" id="tableCancelPayee"></span>
                    </div>
                    <div class="ctc-modal-field">
                        <label class="ctc-modal-field-label" for="tableCancelReason">Reason <span style="color:#999;">(optional)</span></label>
                        <textarea id="tableCancelReason" class="ctc-modal-textarea" rows="3" placeholder="Enter reason for cancellation..."></textarea>
                    </div>
                </div>
                <div class="ctc-modal-footer">
                    <button type="button" class="ctc-modal-btn ctc-modal-btn--secondary" id="tableCancelCancelBtn">Cancel</button>
                    <button type="button" class="ctc-modal-btn ctc-modal-btn--primary" id="tableCancelSubmitBtn">Submit Request</button>
                </div>
            </div>
        </div>

        {{-- ── Admin Cancel Confirmation Modal — persists across reloads ────── --}}
        <div class="ctc-modal-overlay" id="tableAdminCancelOverlay">
            <div class="ctc-modal">
                <div class="ctc-modal-header">
                    <p class="ctc-modal-title">Cancel Transaction</p>
                    <button type="button" class="ctc-modal-close-btn" id="tableAdminCancelCloseBtn" aria-label="Close">
                        <x-bx-x class="icon" />
                    </button>
                </div>
                <div class="ctc-modal-body">
                    <div class="ctc-modal-info-row">
                        <span class="ctc-modal-info-label">Serial No.</span>
                        <span class="ctc-modal-info-value" id="tableAdminCancelSerial"></span>
                    </div>
                    <div class="ctc-modal-info-row">
                        <span class="ctc-modal-info-label">Payee</span>
                        <span class="ctc-modal-info-value" id="tableAdminCancelPayee"></span>
                    </div>
                    <p class="ctc-modal-confirm-text">Are you sure you want to cancel this transaction? This action cannot be undone.</p>
                </div>
                <div class="ctc-modal-footer">
                    <button type="button" class="ctc-modal-btn ctc-modal-btn--secondary" id="tableAdminCancelCloseBtn2">Close</button>
                    <button type="button" class="ctc-modal-btn ctc-modal-btn--primary" id="tableAdminCancelConfirmBtn">Confirm Cancel</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const container = document.getElementById('transactions-table-container');
                const searchInput = document.getElementById('transaction-search-input');
                const searchForm = document.getElementById('transaction-search-form');
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
                            resetBulkSelection();
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

                const filterToggleBtn = document.getElementById('filterToggleBtn');
                const filterModalOverlay = document.getElementById('filterModalOverlay');
                const filterPanel = document.getElementById('filterPanel');
                const filterApplyBtn = document.getElementById('filterApplyBtn');
                const filterFormType = document.getElementById('filterFormType');
                const filterSelectForm = document.getElementById('filterSelectForm');
                const filterBreadcrumbs = document.getElementById('filterBreadcrumbs');
                const filterDate = document.getElementById('filterDate');
                const dateFilterGroup = document.getElementById('dateFilterGroup');
                const dateFilterStart = document.getElementById('dateFilterStart');
                const dateFilterEnd = document.getElementById('dateFilterEnd');
                const dateFilterBtn = document.getElementById('dateFilterBtn');

                function openFilterModal() {
                    filterModalOverlay.classList.add('open');
                    filterToggleBtn.setAttribute('aria-expanded', 'true');
                }

                function closeFilterModal() {
                    filterModalOverlay.classList.remove('open');
                    filterToggleBtn.setAttribute('aria-expanded', 'false');
                }

                filterToggleBtn.addEventListener('click', function () {
                    if (filterModalOverlay.classList.contains('open')) {
                        closeFilterModal();
                    } else {
                        openFilterModal();
                    }
                });

                filterModalOverlay.addEventListener('click', function (event) {
                    if (event.target === filterModalOverlay) {
                        closeFilterModal();
                    }
                });

                document.getElementById('filterCloseBtn')?.addEventListener('click', closeFilterModal);

                filterFormType.addEventListener('change', function () {
                    filterSelectForm.classList.toggle('open', filterFormType.checked);
                });

                filterDate.addEventListener('change', function () {
                    dateFilterGroup.classList.toggle('open', filterDate.checked);

                    if (!filterDate.checked) {
                        dateFilterStart.value = '';
                        dateFilterEnd.value = '';
                        applyFilters();
                    }
                });

                dateFilterBtn.addEventListener('click', function () {
                    applyFilters();
                });

                function applyFilters() {
                    const params = new URLSearchParams(window.location.search);

                    params.delete('status[]');
                    filterPanel.querySelectorAll('input[name="status"]:checked').forEach(function (checkbox) {
                        params.append('status[]', checkbox.value);
                    });

                    params.delete('form_type[]');
                    if (filterFormType.checked) {
                        filterPanel.querySelectorAll('input[name="form_type"]:checked').forEach(function (checkbox) {
                            params.append('form_type[]', checkbox.value);
                        });
                    } else {
                        filterPanel.querySelectorAll('input[name="form_type"]').forEach(function (checkbox) {
                            checkbox.checked = false;
                        });
                    }

                    if (filterDate.checked) {
                        if (dateFilterStart.value) {
                            params.set('date_start', dateFilterStart.value);
                        } else {
                            params.delete('date_start');
                        }

                        if (dateFilterEnd.value) {
                            params.set('date_end', dateFilterEnd.value);
                        } else {
                            params.delete('date_end');
                        }
                    } else {
                        params.delete('date_start');
                        params.delete('date_end');
                    }

                    if (searchInput.value) {
                        params.set('search', searchInput.value);
                    } else {
                        params.delete('search');
                    }

                    fetchAndRender(params);
                    updateBreadcrumbs();
                }

                function updateBreadcrumbs() {
                    filterBreadcrumbs.innerHTML = '';

                    const activeInputs = Array.from(filterPanel.querySelectorAll('[data-filter-label]')).filter(function (input) {
                        return input.checked;
                    });

                    activeInputs.forEach(function (input) {
                        const chip = document.createElement('button');
                        chip.type = 'button';
                        chip.className = 'filter-chip';

                        const label = document.createElement('span');
                        label.textContent = input.dataset.filterLabel;
                        chip.appendChild(label);

                        const remove = document.createElement('span');
                        remove.className = 'filter-chip-remove';
                        remove.textContent = '×';
                        chip.appendChild(remove);

                        chip.addEventListener('click', function () {
                            input.checked = false;

                            if (input === filterFormType) {
                                filterSelectForm.classList.remove('open');
                                filterPanel.querySelectorAll('input[name="form_type"]').forEach(function (checkbox) {
                                    checkbox.checked = false;
                                });
                            }

                            if (input === filterDate) {
                                dateFilterGroup.classList.remove('open');
                                dateFilterStart.value = '';
                                dateFilterEnd.value = '';
                            }

                            applyFilters();
                        });

                        filterBreadcrumbs.appendChild(chip);
                    });

                    if (activeInputs.length > 0) {
                        const clearBtn = document.createElement('button');
                        clearBtn.type = 'button';
                        clearBtn.className = 'filter-clear-btn';
                        clearBtn.textContent = 'Clear Filter';

                        clearBtn.addEventListener('click', function () {
                            filterPanel.querySelectorAll('[data-filter-label], input[name="form_type"]').forEach(function (input) {
                                input.checked = false;
                            });

                            filterSelectForm.classList.remove('open');
                            dateFilterGroup.classList.remove('open');
                            dateFilterStart.value = '';
                            dateFilterEnd.value = '';
                            applyFilters();
                        });

                        filterBreadcrumbs.appendChild(clearBtn);
                    }

                    filterBreadcrumbs.classList.toggle('visible', activeInputs.length > 0);
                }

                filterApplyBtn.addEventListener('click', function () {
                    applyFilters();
                    closeFilterModal();
                });

                updateBreadcrumbs();

                // ── Bulk selection + row actions ─────────────────────────────────
                // The bulk bar and modals live in the parent (persist across AJAX
                // table reloads); row/select-all events are delegated on the
                // persistent container so freshly-loaded rows keep working.
                const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
                const postJson  = (url, body = null) => fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken(),
                    },
                    body: body ? JSON.stringify(body) : null,
                });

                const bulkBar        = document.getElementById('bulkActionBar');
                const bulkCount      = document.getElementById('bulkActionCount');
                const bulkClearBtn   = document.getElementById('bulkClearBtn');
                const bulkCancelBtn  = document.getElementById('bulkCancelBtn');
                const bulkRequestBtn = document.getElementById('bulkRequestBtn');
                const bulkArchiveBtn = document.getElementById('bulkArchiveBtn');

                // Queried fresh each call — these live inside the reloaded container.
                const rowCheckboxes = () => [...container.querySelectorAll('.row-checkbox')];
                const checkedBoxes  = () => rowCheckboxes().filter(cb => cb.checked);
                const archivableIds = () => checkedBoxes()
                    .filter(cb => cb.dataset.status === 'Cancelled' && cb.dataset.archived === '0')
                    .map(cb => parseInt(cb.dataset.id));
                const completedIds  = () => checkedBoxes()
                    .filter(cb => cb.dataset.status === 'Completed')
                    .map(cb => parseInt(cb.dataset.id));

                function updateBulkBar() {
                    const checked = checkedBoxes();
                    const n = checked.length;
                    bulkBar.style.display = n > 0 ? 'flex' : 'none';
                    bulkCount.textContent = n === 1 ? '1 item selected' : `${n} items selected`;

                    const hasCompleted  = checked.some(cb => cb.dataset.status === 'Completed');
                    const hasArchivable = checked.some(cb => cb.dataset.status === 'Cancelled' && cb.dataset.archived === '0');

                    if (bulkCancelBtn)  bulkCancelBtn.style.display  = hasCompleted ? '' : 'none';
                    if (bulkRequestBtn) bulkRequestBtn.style.display = hasCompleted ? '' : 'none';
                    if (bulkArchiveBtn) bulkArchiveBtn.style.display = hasArchivable ? '' : 'none';

                    const selectAll = container.querySelector('#selectAllCheckbox');
                    const all = rowCheckboxes();
                    if (selectAll) {
                        selectAll.indeterminate = n > 0 && n < all.length;
                        selectAll.checked = all.length > 0 && n === all.length;
                    }
                }

                function resetBulkSelection() {
                    // New rows from a reload are unchecked; just refresh the bar state.
                    updateBulkBar();
                }

                // Delegated: row checkbox + select-all changes on the persistent container.
                container.addEventListener('change', (e) => {
                    if (e.target.id === 'selectAllCheckbox') {
                        rowCheckboxes().forEach(cb => { cb.checked = e.target.checked; });
                        updateBulkBar();
                    } else if (e.target.classList.contains('row-checkbox')) {
                        updateBulkBar();
                    }
                });

                if (bulkClearBtn) {
                    bulkClearBtn.addEventListener('click', () => {
                        rowCheckboxes().forEach(cb => { cb.checked = false; });
                        updateBulkBar();
                    });
                }

                if (bulkCancelBtn) {
                    bulkCancelBtn.addEventListener('click', function () {
                        const ids = completedIds();
                        if (!ids.length) return;
                        if (!confirm(`Cancel ${ids.length} selected transaction(s)? This cannot be undone.`)) return;
                        bulkCancelBtn.disabled = true;
                        bulkCancelBtn.textContent = 'Cancelling…';
                        postJson('/collections/bulk-cancel', { ids })
                            .then(r => r.json())
                            .then(data => { showToast(data.message); window.location.reload(); })
                            .catch(() => showToast('Action could not be completed', 'Something went wrong. Please try again.', 'error'))
                            .finally(() => { bulkCancelBtn.disabled = false; bulkCancelBtn.textContent = 'Cancel Selected'; });
                    });
                }

                if (bulkRequestBtn) {
                    bulkRequestBtn.addEventListener('click', function () {
                        const ids = completedIds();
                        if (!ids.length) return;
                        if (!confirm(`Submit cancel requests for ${ids.length} selected transaction(s)?`)) return;
                        bulkRequestBtn.disabled = true;
                        bulkRequestBtn.textContent = 'Submitting…';
                        postJson('/collections/bulk-cancel-request', { ids })
                            .then(r => r.json())
                            .then(data => { showToast(data.message); window.location.reload(); })
                            .catch(() => showToast('Action could not be completed', 'Something went wrong. Please try again.', 'error'))
                            .finally(() => { bulkRequestBtn.disabled = false; bulkRequestBtn.textContent = 'Request Cancel'; });
                    });
                }

                if (bulkArchiveBtn) {
                    bulkArchiveBtn.addEventListener('click', function () {
                        const ids = archivableIds();
                        if (!ids.length) return;
                        if (!confirm(`Archive ${ids.length} selected transaction(s)? This cannot be undone.`)) return;
                        bulkArchiveBtn.disabled = true;
                        bulkArchiveBtn.textContent = 'Archiving…';
                        postJson('/collections/bulk-archive', { ids })
                            .then(r => r.json())
                            .then(data => { showToast(data.message); window.location.reload(); })
                            .catch(() => showToast('Action could not be completed', 'Something went wrong. Please try again.', 'error'))
                            .finally(() => { bulkArchiveBtn.disabled = false; bulkArchiveBtn.textContent = 'Archive Selected'; });
                    });
                }

                // ── Per-row Cancel / Archive (delegated on persistent container) ──
                const reqOverlay   = document.getElementById('tableCancelRequestOverlay');
                const reqSerial    = document.getElementById('tableCancelSerial');
                const reqPayee     = document.getElementById('tableCancelPayee');
                const reqReason    = document.getElementById('tableCancelReason');
                let reqLogId = null;
                const openReqModal  = (id, serial, payee) => { reqLogId = id; reqSerial.textContent = serial; reqPayee.textContent = payee; reqReason.value = ''; reqOverlay.classList.add('open'); };
                const closeReqModal = () => { reqOverlay.classList.remove('open'); reqLogId = null; };
                document.getElementById('tableCancelCloseBtn').addEventListener('click', closeReqModal);
                document.getElementById('tableCancelCancelBtn').addEventListener('click', closeReqModal);
                reqOverlay.addEventListener('click', (e) => { if (e.target === reqOverlay) closeReqModal(); });
                document.getElementById('tableCancelSubmitBtn').addEventListener('click', function () {
                    if (!reqLogId) return;
                    const btn = this;
                    btn.disabled = true; btn.textContent = 'Submitting…';
                    postJson(`/collections/${reqLogId}/cancel-request`, { reason: reqReason.value.trim() })
                        .then(r => r.json())
                        .then(data => { closeReqModal(); showToast(data.message); })
                        .catch(() => showToast('Action could not be completed', 'Something went wrong. Please try again.', 'error'))
                        .finally(() => { btn.disabled = false; btn.textContent = 'Submit Request'; });
                });

                const adminOverlay = document.getElementById('tableAdminCancelOverlay');
                const adminSerial  = document.getElementById('tableAdminCancelSerial');
                const adminPayee   = document.getElementById('tableAdminCancelPayee');
                let adminLogId = null;
                const openAdminModal  = (id, serial, payee) => { adminLogId = id; adminSerial.textContent = serial; adminPayee.textContent = payee; adminOverlay.classList.add('open'); };
                const closeAdminModal = () => { adminOverlay.classList.remove('open'); adminLogId = null; };
                document.getElementById('tableAdminCancelCloseBtn').addEventListener('click', closeAdminModal);
                document.getElementById('tableAdminCancelCloseBtn2').addEventListener('click', closeAdminModal);
                adminOverlay.addEventListener('click', (e) => { if (e.target === adminOverlay) closeAdminModal(); });
                document.getElementById('tableAdminCancelConfirmBtn').addEventListener('click', function () {
                    if (!adminLogId) return;
                    const btn = this, targetId = adminLogId;
                    btn.disabled = true; btn.textContent = 'Cancelling…';
                    postJson(`/collections/${targetId}/cancel`)
                        .then(r => r.json())
                        .then(data => { closeAdminModal(); showToast(data.message); window.location.reload(); })
                        .catch(() => showToast('Action could not be completed', 'Something went wrong. Please try again.', 'error'))
                        .finally(() => { btn.disabled = false; btn.textContent = 'Confirm Cancel'; });
                });

                container.addEventListener('click', (e) => {
                    const cancelBtn = e.target.closest('.action-cancel');
                    if (cancelBtn) {
                        cancelBtn.dataset.cancelType === 'direct'
                            ? openAdminModal(cancelBtn.dataset.id, cancelBtn.dataset.serial, cancelBtn.dataset.payee)
                            : openReqModal(cancelBtn.dataset.id, cancelBtn.dataset.serial, cancelBtn.dataset.payee);
                        return;
                    }
                    const archiveBtn = e.target.closest('.action-archive');
                    if (archiveBtn) {
                        if (!confirm('Archive this transaction? This cannot be undone.')) return;
                        postJson(`/collections/${archiveBtn.dataset.id}/archive`)
                            .then(r => r.json())
                            .then(data => { showToast(data.message); archiveBtn.closest('tr')?.remove(); updateBulkBar(); })
                            .catch(() => showToast('Action could not be completed', 'Something went wrong. Please try again.', 'error'));
                    }
                });
            })();
        </script>
    @endpush
</x-layout>
