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
                    ['name',        'Name'],
                    ['email',       'Email'],
                    ['archived_at', 'Archived At'],
                ] as [$column, $label])
                    @php [$icon, $iconClass] = $sortIcon($column); @endphp
                    <th>
                        <a href="{{ $sortLink($column) }}" class="sortable-header">
                            {{ $label }}
                            <x-dynamic-component :component="'bx-' . $icon" class="sort-icon {{ $iconClass }}" />
                        </a>
                    </th>
                @endforeach
                <th>Role</th>
                <th class="col-actions text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td class="col-check">
                        <input type="checkbox" class="tbl-checkbox row-checkbox" data-id="{{ $user->id }}">
                    </td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->archived_at?->format('F j, Y') ?? '—' }}</td>
                    <td>{{ $user->roles->first()?->name ?? '—' }}</td>
                    <td class="col-actions">
                        <div class="table-actions">
                            <a href="{{ route('archives.users.view', $user->id) }}"
                               class="action-btn action-view action-view--outline"
                               aria-label="View">
                                View
                            </a>
                            <button type="button"
                                class="action-btn action-unarchive"
                                data-id="{{ $user->id }}"
                                aria-label="Unarchive">
                                Unarchive
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="table-empty">No archived users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

<div class="pagination-bar">
    <div class="pagination-info-group">
        <p class="pagination-info">
            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries
        </p>
        <form method="GET" class="per-page-form">
            <input type="hidden" name="tab" value="{{ $tab }}">
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
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
        @if ($users->onFirstPage())
            <span class="page-btn" aria-disabled="true">Previous</span>
        @else
            <a class="page-btn" href="{{ $users->previousPageUrl() }}">Previous</a>
        @endif

        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
            <a class="page-btn {{ $page === $users->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
        @endforeach

        @if ($users->hasMorePages())
            <a class="page-btn" href="{{ $users->nextPageUrl() }}">Next</a>
        @else
            <span class="page-btn" aria-disabled="true">Next</span>
        @endif
    </div>
</div>

<script>
(function () {
    const container     = document.getElementById('archive-table-container');
    if (!container) return;
    const csrf          = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const bulkBar       = document.getElementById('bulkActionBar');
    const bulkCount     = document.getElementById('bulkActionCount');
    const bulkUnarchive = document.getElementById('bulkUnarchiveBtn');
    const bulkClear     = document.getElementById('bulkClearBtn');
    const selectAll     = document.getElementById('selectAllCheckbox');

    const rowCheckboxes = () => [...container.querySelectorAll('.row-checkbox')];
    const checkedBoxes  = () => rowCheckboxes().filter(cb => cb.checked);

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
            if (!confirm(`Unarchive ${ids.length} user(s)? Their accounts will be reactivated.`)) return;

            bulkUnarchive.disabled = true;
            bulkUnarchive.textContent = 'Unarchiving…';

            Promise.all(ids.map(id =>
                fetch(`/archive-records/users/${id}/unarchive`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() },
                }).then(r => r.json())
            ))
            .then(() => { alert(`${ids.length} user(s) unarchived and reactivated.`); window.location.reload(); })
            .catch(() => alert('Something went wrong. Please try again.'))
            .finally(() => { bulkUnarchive.disabled = false; bulkUnarchive.textContent = 'Unarchive Selected'; });
        });
    }

    // Row-level unarchive
    container.addEventListener('click', function (e) {
        const btn = e.target.closest('.action-unarchive');
        if (!btn) return;
        if (!confirm('Unarchive this user? Their account will be reactivated.')) return;

        btn.disabled = true;
        btn.textContent = 'Unarchiving…';

        fetch(`/archive-records/users/${btn.dataset.id}/unarchive`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() },
        })
        .then(r => r.json())
        .then(data => { alert(data.message); btn.closest('tr')?.remove(); })
        .catch(() => alert('Something went wrong. Please try again.'))
        .finally(() => { btn.disabled = false; btn.textContent = 'Unarchive'; });
    });
})();
</script>
