<x-layout>
    @php
        $tmpRoute = route('official-receipts-accountable-forms.report-logs', $formStock);
        $routeName = 'official-receipts-accountable-forms.report-logs';
    @endphp

    <div class="x-header-container sub-nav-sticky">
        <x-header title="{{ $formStock->form_name }} - {{ $formStock->form_code }}"
            :tmpRoute="$tmpRoute"
            :routeName="$routeName"
            parentTitle="Official Receipt & Accountable Forms"
            :parentRoute="route('official-receipts-accountable-forms')"
            parentRouteName="official-receipts-accountable-forms"
            extraTitle="Report Logs"
        />
    </div>

    <div class="collection-content">
        <div class="collection-toolbar">
            <form class="search-group" role="search" method="GET" id="report-log-search-form">
                <input type="search" name="search" class="search-input" id="report-log-search-input" placeholder="Search Batch Number" value="{{ request('search') }}" autocomplete="off">
            </form>

            @if (auth()->user()?->hasRole('collector'))
                <button type="button" class="report-log-add-batch-btn" id="reportLogRequestBatchBtn">Request New Batch</button>
            @else
                <button type="button" class="report-log-add-batch-btn" id="reportLogAddBatchBtn">Add New Batch</button>
            @endif
        </div>

        <div id="report-logs-table-container">
            @include('official-receipt-accountable-forms.partials.report-logs-table')
        </div>

        @unless (auth()->user()?->hasRole('collector'))
            @include('collection-management.transaction-entry.partials.add-batch-modal')
        @endunless
        @if (auth()->user()?->hasRole('collector'))
            @include('official-receipt-accountable-forms.partials.batch-request-modal')
        @endif
    </div>

    @push('scripts')
        <script>
            (function () {
                const container = document.getElementById('report-logs-table-container');
                const searchInput = document.getElementById('report-log-search-input');
                const searchForm = document.getElementById('report-log-search-form');
                let debounceTimer;

                function fetchAndRender(params) {
                    closeAssignedToMenu();
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

                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                function saveAssignedTo(batchId, value) {
                    return fetch(`/official-receipts-accountable-forms/batches/${batchId}/assign`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ assigned_to: value }),
                    });
                }

                let openAssignedTo = null;

                function closeAssignedToMenu() {
                    if (!openAssignedTo) {
                        return;
                    }

                    const { menu, control, trigger } = openAssignedTo;
                    menu.classList.add('is-hidden');
                    menu.style.position = '';
                    menu.style.top = '';
                    menu.style.left = '';
                    menu.style.minWidth = '';
                    control.appendChild(menu);
                    trigger.classList.remove('is-open');
                    openAssignedTo = null;
                }

                function openAssignedToMenu(trigger) {
                    const control = trigger.closest('.assigned-to-control');
                    const menu = control.querySelector('.assigned-to-menu');

                    closeAssignedToMenu();

                    const rect = trigger.getBoundingClientRect();
                    document.body.appendChild(menu);
                    menu.classList.remove('is-hidden');
                    menu.style.position = 'fixed';
                    menu.style.minWidth = rect.width + 'px';
                    menu.style.left = rect.left + 'px';
                    menu.style.top = (rect.bottom + 4) + 'px';

                    const menuRect = menu.getBoundingClientRect();
                    if (menuRect.bottom > window.innerHeight - 8 && rect.top - menuRect.height - 4 > 0) {
                        menu.style.top = (rect.top - menuRect.height - 4) + 'px';
                    }

                    trigger.classList.add('is-open');
                    openAssignedTo = { menu, control, trigger };
                }

                function showDropdownMode(control) {
                    control.querySelector('.assigned-to-trigger').classList.remove('is-hidden');
                    control.querySelector('.assigned-to-other-wrap').classList.add('is-hidden');
                }

                function showOtherMode(control) {
                    control.querySelector('.assigned-to-trigger').classList.add('is-hidden');
                    control.querySelector('.assigned-to-other-wrap').classList.remove('is-hidden');
                }

                document.addEventListener('click', function (event) {
                    const trigger = event.target.closest('.assigned-to-trigger');
                    const option = event.target.closest('.assigned-to-option');
                    const backBtn = event.target.closest('.assigned-to-other-back');

                    if (trigger) {
                        const alreadyOpen = openAssignedTo && openAssignedTo.trigger === trigger;
                        closeAssignedToMenu();

                        if (!alreadyOpen) {
                            openAssignedToMenu(trigger);
                        }

                        return;
                    }

                    if (option) {
                        const menu = option.closest('.assigned-to-menu');
                        const control = (openAssignedTo && openAssignedTo.menu === menu)
                            ? openAssignedTo.control
                            : option.closest('.assigned-to-control');
                        const batchId = control.dataset.batchId;

                        menu.querySelectorAll('.assigned-to-option').forEach(function (opt) {
                            opt.classList.toggle('is-selected', opt === option);
                        });

                        closeAssignedToMenu();

                        if (option.dataset.value === '__other__') {
                            const otherInput = control.querySelector('.assigned-to-other-input');
                            showOtherMode(control);
                            otherInput.value = '';
                            otherInput.focus();
                            return;
                        }

                        control.querySelector('.assigned-to-trigger-label').textContent = option.dataset.value || 'Unassigned';
                        showDropdownMode(control);
                        saveAssignedTo(batchId, option.dataset.value);
                        return;
                    }

                    if (backBtn) {
                        const control = backBtn.closest('.assigned-to-control');
                        showDropdownMode(control);
                        return;
                    }

                    if (!event.target.closest('.assigned-to-control') && !event.target.closest('.assigned-to-menu')) {
                        closeAssignedToMenu();
                    }
                });

                container.addEventListener('focusout', function (event) {
                    const input = event.target.closest('.assigned-to-other-input');

                    if (!input) {
                        return;
                    }

                    saveAssignedTo(input.dataset.batchId, input.value.trim());
                });

                container.addEventListener('keydown', function (event) {
                    const input = event.target.closest('.assigned-to-other-input');

                    if (!input || event.key !== 'Enter') {
                        return;
                    }

                    event.preventDefault();
                    input.blur();
                });

                // ── Add batch modal ──────────────────────────────────────────────
                if (document.getElementById('formBatchModalOverlay')) {
                    const modalOverlay = document.getElementById('formBatchModalOverlay');
                    const modalTitle = document.getElementById('formBatchModalTitle');
                    const modalForm = document.getElementById('formBatchForm');
                    const successAlert = document.getElementById('formBatchSuccessAlert');
                    const successAlertSubtitle = document.getElementById('formBatchSuccessAlertSubtitle');
                    let successAlertTimer;

                    const formStockId = {{ $formStock->id }};
                    const formCode = @json($formStock->form_code);

                    var showSuccessAlert = function () {
                        successAlertSubtitle.textContent = formCode;
                        successAlert.classList.add('show');

                        clearTimeout(successAlertTimer);
                        successAlertTimer = setTimeout(() => {
                            successAlert.classList.remove('show');
                        }, 3000);
                    };

                    var openBatchModal = function () {
                        modalTitle.textContent = `Add new batch of ${formCode}`;
                        modalForm.action = `/official-receipts-accountable-forms/${formStockId}/batches`;

                        const nextEl = document.getElementById('reportLogNextSerial');
                        const ssnInput = modalForm.querySelector('[name="starting_serial_number"]');
                        if (ssnInput) {
                            ssnInput.value = (nextEl && nextEl.dataset.nextSerial) ? nextEl.dataset.nextSerial : '';
                        }

                        modalOverlay.classList.add('open');
                    };

                    var closeBatchModal = function () {
                        modalOverlay.classList.remove('open');
                        modalForm.reset();
                    };

                    document.getElementById('reportLogAddBatchBtn').addEventListener('click', function (event) {
                        event.preventDefault();
                        openBatchModal();
                    });

                    document.getElementById('formBatchCloseBtn').addEventListener('click', closeBatchModal);

                    modalForm.addEventListener('submit', function (event) {
                        event.preventDefault();

                        fetch(modalForm.action, {
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

                                closeBatchModal();
                                showSuccessAlert();
                                reloadTable();
                            })
                            .catch(() => {});
                    });
                }

                // ── Request New Batch modal (Collector) ──────────────────────────
                if (document.getElementById('batchRequestModalOverlay')) {
                    const requestModalOverlay = document.getElementById('batchRequestModalOverlay');
                    const requestModalTitle = document.getElementById('batchRequestModalTitle');
                    const requestForm = document.getElementById('batchRequestForm');
                    const requestSuccessAlert = document.getElementById('batchRequestSuccessAlert');
                    const requestSuccessAlertSubtitle = document.getElementById('batchRequestSuccessAlertSubtitle');
                    let requestSuccessAlertTimer;
                    const formStockId = {{ $formStock->id }};
                    const formCode = @json($formStock->form_code);

                    function showRequestSuccessAlert() {
                        requestSuccessAlertSubtitle.textContent = formCode;
                        requestSuccessAlert.classList.add('show');

                        clearTimeout(requestSuccessAlertTimer);
                        requestSuccessAlertTimer = setTimeout(() => {
                            requestSuccessAlert.classList.remove('show');
                        }, 3000);
                    }

                    function openRequestModal() {
                        requestModalTitle.textContent = `Request new batch of ${formCode}`;
                        requestForm.action = `/official-receipts-accountable-forms/${formStockId}/batch-requests`;
                        requestModalOverlay.classList.add('open');
                    }

                    function closeRequestModal() {
                        requestModalOverlay.classList.remove('open');
                        requestForm.reset();
                    }

                    document.getElementById('reportLogRequestBatchBtn').addEventListener('click', function (event) {
                        event.preventDefault();
                        openRequestModal();
                    });

                    document.getElementById('batchRequestCloseBtn').addEventListener('click', closeRequestModal);

                    requestForm.addEventListener('submit', function (event) {
                        event.preventDefault();

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
                                showRequestSuccessAlert();
                            })
                            .catch(() => showToast('Action could not be completed', 'Something went wrong. Please try again.', 'error'));
                    });
                }
            })();
        </script>
    @endpush
</x-layout>
