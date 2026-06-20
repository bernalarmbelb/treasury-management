@php
    $sortLink = function (string $column) use ($sort, $direction, $tab) {
        $newDirection = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
        return request()->fullUrlWithQuery([
            'sort'      => $column,
            'direction' => $newDirection,
            'page'      => null,
            'tab'       => $tab,
        ]);
    };

    $sortIcon = function (string $column) use ($sort, $direction) {
        if ($sort !== $column) return ['sort-alt-2', ''];
        return $direction === 'asc' ? ['sort-up', 'active'] : ['sort-down', 'active'];
    };
@endphp

{{-- ── Bulk action bar ──────────────────────────────────────────────────── --}}
<div class="bulk-action-bar" id="bulkActionBar" style="display:none;">
    <span class="bulk-action-count" id="bulkActionCount">0 selected</span>
    <div class="bulk-action-btns">
        <button type="button" class="bulk-btn bulk-btn--archive" id="bulkUnarchiveBtn">Unarchive Selected</button>
        <button type="button" class="bulk-btn bulk-btn--clear"   id="bulkClearBtn">Clear Selection</button>
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
                    ['payee',         'Payee'],
                    ['transacted_at', 'Date'],
                    ['transacted_at', 'Time'],
                    ['form_type',     'Form Type'],
                    ['archived_at',   'Archived At'],
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
                        <input type="checkbox" class="tbl-checkbox row-checkbox" data-id="{{ $transaction->id }}">
                    </td>
                    <td>{{ $transaction->serial_number }}</td>
                    <td>{{ $transaction->payee }}</td>
                    <td>{{ $transaction->transacted_at->format('F j, Y') }}</td>
                    <td>{{ $transaction->transacted_at->format('h:i:s A') }}</td>
                    <td>{{ \App\Models\TransactionLog::formName($transaction->form_type) }}</td>
                    <td>{{ $transaction->archived_at->format('F j, Y') }}</td>
                    <td class="col-actions">
                        <div class="table-actions">
                            <a href="{{ route('collections.view', $transaction->id) }}"
                               class="action-btn action-view action-view--outline"
                               aria-label="View">
                                View
                            </a>
                            <button type="button"
                                class="action-btn action-unarchive"
                                data-id="{{ $transaction->id }}"
                                aria-label="Unarchive">
                                Unarchive
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="table-empty">No archived transactions found.</td>
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
            <input type="hidden" name="tab" value="{{ $tab }}">
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @foreach (request('form_type', []) as $ft)
                <input type="hidden" name="form_type[]" value="{{ $ft }}">
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

<script>
(function () {
    const container      = document.getElementById('archive-table-container');
    if (!container) return;
    const csrf           = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const bulkBar        = document.getElementById('bulkActionBar');
    const bulkCount      = document.getElementById('bulkActionCount');
    const bulkUnarchive  = document.getElementById('bulkUnarchiveBtn');
    const bulkClear      = document.getElementById('bulkClearBtn');
    const selectAll      = document.getElementById('selectAllCheckbox');

    const rowCheckboxes  = () => [...container.querySelectorAll('.row-checkbox')];
    const checkedBoxes   = () => rowCheckboxes().filter(cb => cb.checked);

    function updateBulkBar() {
        const n = checkedBoxes().length;
        if (bulkBar)   bulkBar.style.display = n > 0 ? 'flex' : 'none';
        if (bulkCount) bulkCount.textContent  = n === 1 ? '1 item selected' : `${n} items selected`;
        const all = rowCheckboxes();
        if (selectAll) {
            selectAll.indeterminate = n > 0 && n < all.length;
            selectAll.checked = all.length > 0 && n === all.length;
        }
    }

    container.addEventListener('change', e => { if (e.target.classList.contains('row-checkbox')) updateBulkBar(); });

    if (selectAll) {
        selectAll.addEventListener('change', () => {
            rowCheckboxes().forEach(cb => { cb.checked = selectAll.checked; });
            updateBulkBar();
        });
    }

    if (bulkClear) {
        bulkClear.addEventListener('click', () => {
            rowCheckboxes().forEach(cb => { cb.checked = false; });
            if (selectAll) { selectAll.checked = false; selectAll.indeterminate = false; }
            if (bulkBar) bulkBar.style.display = 'none';
        });
    }

    if (bulkUnarchive) {
        bulkUnarchive.addEventListener('click', function () {
            const ids = checkedBoxes().map(cb => parseInt(cb.dataset.id));
            if (!ids.length) return;
            if (!confirm(`Unarchive ${ids.length} transaction(s)? They will reappear in Collection Management.`)) return;

            bulkUnarchive.disabled = true;
            bulkUnarchive.textContent = 'Unarchiving…';

            Promise.all(ids.map(id =>
                fetch(`/archive-records/transactions/${id}/unarchive`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() },
                }).then(r => r.json())
            ))
            .then(() => { showToast(`${ids.length} transaction(s) unarchived.`); window.location.reload(); })
            .catch(() => showToast('Action could not be completed', 'Something went wrong. Please try again.', 'error'))
            .finally(() => { bulkUnarchive.disabled = false; bulkUnarchive.textContent = 'Unarchive Selected'; });
        });
    }

    // Row-level unarchive
    container.addEventListener('click', function (e) {
        const btn = e.target.closest('.action-unarchive');
        if (!btn) return;
        if (!confirm('Unarchive this transaction? It will reappear in Collection Management.')) return;

        btn.disabled = true;
        btn.textContent = 'Unarchiving…';

        fetch(`/archive-records/transactions/${btn.dataset.id}/unarchive`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() },
        })
        .then(r => r.json())
        .then(data => { showToast(data.message); btn.closest('tr')?.remove(); })
        .catch(() => showToast('Action could not be completed', 'Something went wrong. Please try again.', 'error'))
        .finally(() => { btn.disabled = false; btn.textContent = 'Unarchive'; });
    });
})();
</script>
