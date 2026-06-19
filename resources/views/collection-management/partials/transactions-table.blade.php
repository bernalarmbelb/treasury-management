@php
    $sortLink = function (string $column) use ($sort, $direction) {
        $newDirection = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';

        return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $newDirection, 'page' => null]);
    };

    $sortIcon = function (string $column) use ($sort, $direction) {
        if ($sort !== $column) {
            return ['sort-alt-2', ''];
        }

        return $direction === 'asc' ? ['sort-up', 'active'] : ['sort-down', 'active'];
    };
@endphp

{{-- ── Bulk action bar (visible when ≥1 row checked) ───────────────────── --}}
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

<div class="table-scroll-area">
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th class="col-check">
                    <input type="checkbox" class="tbl-checkbox" id="selectAllCheckbox" title="Select all">
                </th>
                @foreach ([
                    ['serial_number', 'Serial Number'],
                    ['payee', 'Payee'],
                    ['transacted_at', 'Date'],
                    ['transacted_at', 'Time'],
                    ['form_type', 'Form Type'],
                    ['status', 'Status'],
                ] as [$column, $label])
                    @php [$icon, $iconClass] = $sortIcon($column); @endphp
                    <th>
                        <a href="{{ $sortLink($column) }}" class="sortable-header">
                            {{ $label }}
                            <x-dynamic-component :component="'bx-' . $icon" class="sort-icon {{ $iconClass }}" />
                        </a>
                    </th>
                @endforeach
                <th class="col-actions text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td class="col-check">
                        <input type="checkbox" class="tbl-checkbox row-checkbox"
                            data-id="{{ $transaction->id }}"
                            data-status="{{ $transaction->status }}"
                            data-archived="{{ $transaction->archived_at ? '1' : '0' }}">
                    </td>
                    <td>{{ $transaction->serial_number }}</td>
                    <td>{{ $transaction->payee }}</td>
                    <td>{{ $transaction->transacted_at->format('F j, Y') }}</td>
                    <td>{{ $transaction->transacted_at->format('h:i:s A') }}</td>
                    <td>{{ $transaction->form_type }}</td>
                    <td>
                        <span class="status-badge status-{{ strtolower($transaction->status) }}">
                            {{ $transaction->status }}
                        </span>
                    </td>
                    <td class="col-actions">
                        <div class="table-actions">
                            @if($transaction->status === 'Completed')
                                <button type="button"
                                    class="action-btn action-cancel"
                                    data-id="{{ $transaction->id }}"
                                    data-serial="{{ $transaction->serial_number }}"
                                    data-payee="{{ $transaction->payee }}"
                                    data-cancel-type="{{ ($isAdmin ?? false) ? 'direct' : 'request' }}"
                                    aria-label="Cancel">
                                    Cancel
                                </button>
                            @elseif($transaction->status === 'Cancelled' && !$transaction->archived_at)
                                <button type="button"
                                    class="action-btn action-archive"
                                    data-id="{{ $transaction->id }}"
                                    aria-label="Archive">
                                    Archive
                                </button>
                            @endif
                            <a href="{{ route('collections.view', $transaction->id) }}"
                               class="action-btn action-view"
                               aria-label="View">
                                View
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="table-empty">No transactions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

<div class="pagination-bar">
    <div class="pagination-info-group">
        <p class="pagination-info">
            Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} entries
        </p>
        <form method="GET" class="per-page-form">
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @foreach (request('status', []) as $status)
                <input type="hidden" name="status[]" value="{{ $status }}">
            @endforeach
            @foreach (request('form_type', []) as $formType)
                <input type="hidden" name="form_type[]" value="{{ $formType }}">
            @endforeach
            @if (request('date_start'))
                <input type="hidden" name="date_start" value="{{ request('date_start') }}">
            @endif
            @if (request('date_end'))
                <input type="hidden" name="date_end" value="{{ request('date_end') }}">
            @endif
            <label for="per_page" class="per-page-label">Rows per page</label>
            <select name="per_page" id="per_page" class="form-select form-select-sm per-page-select" onchange="this.form.submit()">
                @foreach ($perPageOptions as $option)
                    <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="pagination-controls">
        @if ($transactions->onFirstPage())
            <span class="page-btn" aria-disabled="true">Previous</span>
        @else
            <a class="page-btn" href="{{ $transactions->previousPageUrl() }}">Previous</a>
        @endif

        @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
            <a class="page-btn {{ $page === $transactions->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
        @endforeach

        @if ($transactions->hasMorePages())
            <a class="page-btn" href="{{ $transactions->nextPageUrl() }}">Next</a>
        @else
            <span class="page-btn" aria-disabled="true">Next</span>
        @endif
    </div>
</div>

{{-- ── Cancel Request Modal (non-admin) ────────────────────────────────── --}}
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

{{-- ── Admin Cancel Confirmation Modal ─────────────────────────────────── --}}
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

<script>
(function () {
    const container = document.getElementById('transactions-table-container');
    if (!container) return;

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

    // ── Checkbox / bulk selection ────────────────────────────────────────────
    const bulkBar        = document.getElementById('bulkActionBar');
    const bulkCount      = document.getElementById('bulkActionCount');
    const selectAll      = document.getElementById('selectAllCheckbox');
    const bulkClearBtn   = document.getElementById('bulkClearBtn');
    const bulkCancelBtn  = document.getElementById('bulkCancelBtn');
    const bulkRequestBtn = document.getElementById('bulkRequestBtn');
    const bulkArchiveBtn = document.getElementById('bulkArchiveBtn');

    const rowCheckboxes   = () => [...container.querySelectorAll('.row-checkbox')];
    const checkedBoxes    = () => rowCheckboxes().filter(cb => cb.checked);
    const selectedIds     = () => checkedBoxes().map(cb => parseInt(cb.dataset.id));
    const selectedIdsByStatus = (status, archived = null) => checkedBoxes()
        .filter(cb => cb.dataset.status === status && (archived === null || cb.dataset.archived === archived))
        .map(cb => parseInt(cb.dataset.id));

    function updateBulkBar() {
        const checked = checkedBoxes();
        const n = checked.length;
        bulkBar.style.display = n > 0 ? 'flex' : 'none';
        bulkCount.textContent = n === 1 ? '1 item selected' : `${n} items selected`;

        // Cancel/Request: show when any Completed rows are selected
        const hasCompleted = checked.some(cb => cb.dataset.status === 'Completed');
        // Archive: show only when Cancelled+unarchived rows are selected
        const hasArchivable = checked.some(cb => cb.dataset.status === 'Cancelled' && cb.dataset.archived === '0');

        if (bulkCancelBtn)  bulkCancelBtn.style.display  = hasCompleted ? '' : 'none';
        if (bulkRequestBtn) bulkRequestBtn.style.display = hasCompleted ? '' : 'none';
        if (bulkArchiveBtn) bulkArchiveBtn.style.display = hasArchivable ? '' : 'none';

        // Sync select-all state
        const all = rowCheckboxes();
        if (selectAll) {
            selectAll.indeterminate = n > 0 && n < all.length;
            selectAll.checked = all.length > 0 && n === all.length;
        }
    }

    container.addEventListener('change', (e) => {
        if (e.target.classList.contains('row-checkbox')) updateBulkBar();
    });

    if (selectAll) {
        selectAll.addEventListener('change', () => {
            rowCheckboxes().forEach(cb => { cb.checked = selectAll.checked; });
            updateBulkBar();
        });
    }

    if (bulkClearBtn) {
        bulkClearBtn.addEventListener('click', () => {
            rowCheckboxes().forEach(cb => { cb.checked = false; });
            if (selectAll) { selectAll.checked = false; selectAll.indeterminate = false; }
            bulkBar.style.display = 'none';
        });
    }

    // ── Bulk cancel (admin) ──────────────────────────────────────────────────
    if (bulkCancelBtn) {
        bulkCancelBtn.addEventListener('click', function () {
            const ids = selectedIdsByStatus('Completed');
            if (!ids.length) return;
            if (!confirm(`Cancel ${ids.length} selected transaction(s)? This cannot be undone.`)) return;

            bulkCancelBtn.disabled = true;
            bulkCancelBtn.textContent = 'Cancelling…';

            postJson('/collections/bulk-cancel', { ids })
            .then(r => r.json())
            .then(data => { alert(data.message); window.location.reload(); })
            .catch(() => alert('Something went wrong. Please try again.'))
            .finally(() => { bulkCancelBtn.disabled = false; bulkCancelBtn.textContent = 'Cancel Selected'; });
        });
    }

    // ── Bulk cancel-request (non-admin) ──────────────────────────────────────
    if (bulkRequestBtn) {
        bulkRequestBtn.addEventListener('click', function () {
            const ids = selectedIdsByStatus('Completed');
            if (!ids.length) return;
            if (!confirm(`Submit cancel requests for ${ids.length} selected transaction(s)?`)) return;

            bulkRequestBtn.disabled = true;
            bulkRequestBtn.textContent = 'Submitting…';

            postJson('/collections/bulk-cancel-request', { ids })
            .then(r => r.json())
            .then(data => { alert(data.message); window.location.reload(); })
            .catch(() => alert('Something went wrong. Please try again.'))
            .finally(() => { bulkRequestBtn.disabled = false; bulkRequestBtn.textContent = 'Request Cancel'; });
        });
    }

    // ── Bulk archive ─────────────────────────────────────────────────────────
    if (bulkArchiveBtn) {
        bulkArchiveBtn.addEventListener('click', function () {
            const ids = checkedBoxes().filter(cb => cb.dataset.status === 'Cancelled' && cb.dataset.archived === '0').map(cb => parseInt(cb.dataset.id));
            if (!ids.length) return;
            if (!confirm(`Archive ${ids.length} selected transaction(s)? This cannot be undone.`)) return;

            bulkArchiveBtn.disabled = true;
            bulkArchiveBtn.textContent = 'Archiving…';

            postJson('/collections/bulk-archive', { ids })
            .then(r => r.json())
            .then(data => { alert(data.message); window.location.reload(); })
            .catch(() => alert('Something went wrong. Please try again.'))
            .finally(() => { bulkArchiveBtn.disabled = false; bulkArchiveBtn.textContent = 'Archive Selected'; });
        });
    }

    // ── Cancel Request Modal (non-admin) ────────────────────────────────────
    const reqOverlay   = document.getElementById('tableCancelRequestOverlay');
    const reqCloseBtn  = document.getElementById('tableCancelCloseBtn');
    const reqCancelBtn = document.getElementById('tableCancelCancelBtn');
    const reqSubmitBtn = document.getElementById('tableCancelSubmitBtn');
    const reqSerial    = document.getElementById('tableCancelSerial');
    const reqPayee     = document.getElementById('tableCancelPayee');
    const reqReason    = document.getElementById('tableCancelReason');

    let reqLogId = null;

    const openReqModal  = (id, serial, payee) => { reqLogId = id; reqSerial.textContent = serial; reqPayee.textContent = payee; reqReason.value = ''; reqOverlay.classList.add('open'); };
    const closeReqModal = () => { reqOverlay.classList.remove('open'); reqLogId = null; };

    reqCloseBtn.addEventListener('click', closeReqModal);
    reqCancelBtn.addEventListener('click', closeReqModal);
    reqOverlay.addEventListener('click', (e) => { if (e.target === reqOverlay) closeReqModal(); });

    reqSubmitBtn.addEventListener('click', function () {
        if (!reqLogId) return;
        const reason = reqReason.value.trim();
        reqSubmitBtn.disabled = true;
        reqSubmitBtn.textContent = 'Submitting…';

        postJson(`/collections/${reqLogId}/cancel-request`, { reason })
        .then(r => r.json())
        .then(data => { closeReqModal(); alert(data.message); })
        .catch(() => alert('Something went wrong. Please try again.'))
        .finally(() => { reqSubmitBtn.disabled = false; reqSubmitBtn.textContent = 'Submit Request'; });
    });

    // ── Admin Cancel Confirmation Modal ─────────────────────────────────────
    const adminOverlay    = document.getElementById('tableAdminCancelOverlay');
    const adminCloseBtn   = document.getElementById('tableAdminCancelCloseBtn');
    const adminCloseBtn2  = document.getElementById('tableAdminCancelCloseBtn2');
    const adminConfirmBtn = document.getElementById('tableAdminCancelConfirmBtn');
    const adminSerial     = document.getElementById('tableAdminCancelSerial');
    const adminPayee      = document.getElementById('tableAdminCancelPayee');

    let adminLogId = null;

    const openAdminModal  = (id, serial, payee) => { adminLogId = id; adminSerial.textContent = serial; adminPayee.textContent = payee; adminOverlay.classList.add('open'); };
    const closeAdminModal = () => { adminOverlay.classList.remove('open'); adminLogId = null; };

    adminCloseBtn.addEventListener('click', closeAdminModal);
    adminCloseBtn2.addEventListener('click', closeAdminModal);
    adminOverlay.addEventListener('click', (e) => { if (e.target === adminOverlay) closeAdminModal(); });

    adminConfirmBtn.addEventListener('click', function () {
        if (!adminLogId) return;
        const targetId = adminLogId;
        adminConfirmBtn.disabled = true;
        adminConfirmBtn.textContent = 'Cancelling…';

        postJson(`/collections/${targetId}/cancel`)
        .then(r => r.json())
        .then(data => { closeAdminModal(); alert(data.message); window.location.reload(); })
        .catch(() => alert('Something went wrong. Please try again.'))
        .finally(() => { adminConfirmBtn.disabled = false; adminConfirmBtn.textContent = 'Confirm Cancel'; });
    });

    // ── Cancel button click router ───────────────────────────────────────────
    container.addEventListener('click', (e) => {
        const btn = e.target.closest('.action-cancel');
        if (!btn) return;
        btn.dataset.cancelType === 'direct'
            ? openAdminModal(btn.dataset.id, btn.dataset.serial, btn.dataset.payee)
            : openReqModal(btn.dataset.id, btn.dataset.serial, btn.dataset.payee);
    });

    // ── Archive buttons ──────────────────────────────────────────────────────
    container.addEventListener('click', (e) => {
        const btn = e.target.closest('.action-archive');
        if (!btn) return;
        if (!confirm('Archive this transaction? This cannot be undone.')) return;

        postJson(`/collections/${btn.dataset.id}/archive`)
        .then(r => r.json())
        .then(data => { alert(data.message); btn.closest('tr')?.remove(); })
        .catch(() => alert('Something went wrong. Please try again.'));
    });
})();
</script>
