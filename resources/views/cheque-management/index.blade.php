<x-layout>
    @php
        $tmpRoute = route('cheque-management');
        $routeName = 'cheque-management';
        $dateStart = request('date_start');
        $dateEnd = request('date_end');
        $dateFilterActive = $dateStart || $dateEnd;
    @endphp

    <div class="x-header-container sub-nav-sticky">
        <x-header title="Cheque Management" :tmpRoute="$tmpRoute" :routeName="$routeName">
            <x-slot:actions>
                @include('cheque-management.partials.sub-nav')
            </x-slot:actions>
        </x-header>
    </div>

    <style>
        /* Scoped to Cheque Management — additive, no shared-class edits. */
        .status-badge.status-issued { color: var(--success, #1e874b); }
        .cqm-amount { font-variant-numeric: tabular-nums; font-weight: 600; white-space: nowrap; }
        .cqm-print-btn { background-color: #fff; border: 1px solid var(--line, #E3E8EF); color: var(--muted, #6b7685); }
    </style>

    <div class="collection-content">
        <div class="collection-toolbar">
            <div class="filter-wrapper">
                <button type="button" class="filter-btn" id="filterToggleBtn" aria-label="Filter" aria-expanded="false">
                    <x-icons.filter-fill class="icon" />
                </button>
            </div>

            <form class="search-group" role="search" method="GET" id="cheque-search-form" action="{{ route('cheque-management') }}">
                <input type="search" name="search" class="search-input" id="cheque-search-input"
                    placeholder="Search payee or cheque no." value="{{ request('search') }}" autocomplete="off">
            </form>

            <div class="date-filter-group {{ $dateFilterActive ? 'open' : '' }}" id="dateFilterGroup">
                <input type="date" class="date-filter-input" id="dateFilterStart" value="{{ $dateStart }}">
                <span class="date-filter-separator">-</span>
                <input type="date" class="date-filter-input" id="dateFilterEnd" value="{{ $dateEnd }}">
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
                        <input type="checkbox" id="filterDate" data-filter-label="Date" @checked($dateFilterActive)>
                        <span>Date</span>
                    </label>
                </div>
                <div class="filter-apply-row">
                    <button type="button" class="filter-apply-btn" id="filterApplyBtn">Apply</button>
                </div>
            </div>
        </div>

        <div id="cheques-table-container">
            @include('cheque-management.partials.cheques-table')
        </div>

        @include('cheque-management.partials.select-enhancer')
    </div>

    @push('scripts')
    <script>
        (function () {
            const container = document.getElementById('cheques-table-container');
            const searchInput = document.getElementById('cheque-search-input');
            const searchForm = document.getElementById('cheque-search-form');
            const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            let debounce;

            function fetchAndRender(params) {
                params.delete('page');
                const base = searchForm.action.split('?')[0];
                const query = params.toString();
                const url = query ? `${base}?${query}` : base;
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then((r) => r.text())
                    .then((html) => { container.innerHTML = html; window.history.replaceState({}, '', url); window.cqmEnhanceSelects?.(container); });
            }

            function currentParams() {
                const params = new URLSearchParams(window.location.search);
                if (searchInput.value) params.set('search', searchInput.value); else params.delete('search');
                return params;
            }

            searchInput.addEventListener('input', function () {
                clearTimeout(debounce);
                debounce = setTimeout(() => fetchAndRender(currentParams()), 300);
            });
            searchForm.addEventListener('submit', function (e) { e.preventDefault(); fetchAndRender(currentParams()); });

            // Sorting via delegated clicks on freshly-loaded headers.
            container.addEventListener('click', function (e) {
                const link = e.target.closest('.sortable-header');
                if (!link) return;
                e.preventDefault();
                const linkUrl = new URL(link.href);
                const params = currentParams();
                params.set('sort', linkUrl.searchParams.get('sort'));
                params.set('direction', linkUrl.searchParams.get('direction'));
                fetchAndRender(params);
            });

            // Filter panel + date range.
            const filterToggleBtn = document.getElementById('filterToggleBtn');
            const filterModalOverlay = document.getElementById('filterModalOverlay');
            const filterDate = document.getElementById('filterDate');
            const dateFilterGroup = document.getElementById('dateFilterGroup');
            const dateFilterStart = document.getElementById('dateFilterStart');
            const dateFilterEnd = document.getElementById('dateFilterEnd');

            filterToggleBtn.addEventListener('click', () => filterModalOverlay.classList.toggle('open'));
            filterModalOverlay.addEventListener('click', (e) => { if (e.target === filterModalOverlay) filterModalOverlay.classList.remove('open'); });
            document.getElementById('filterCloseBtn')?.addEventListener('click', () => filterModalOverlay.classList.remove('open'));
            filterDate.addEventListener('change', function () {
                dateFilterGroup.classList.toggle('open', filterDate.checked);
                if (!filterDate.checked) { dateFilterStart.value = ''; dateFilterEnd.value = ''; applyFilters(); }
            });

            function applyFilters() {
                const params = currentParams();
                if (filterDate.checked && dateFilterStart.value) params.set('date_start', dateFilterStart.value); else params.delete('date_start');
                if (filterDate.checked && dateFilterEnd.value) params.set('date_end', dateFilterEnd.value); else params.delete('date_end');
                fetchAndRender(params);
            }
            document.getElementById('dateFilterBtn').addEventListener('click', applyFilters);
            document.getElementById('filterApplyBtn').addEventListener('click', () => { applyFilters(); filterModalOverlay.classList.remove('open'); });

            // Row actions: cancel (Issued) / archive (Cancelled).
            container.addEventListener('click', function (e) {
                const cancelBtn = e.target.closest('.cqm-cancel');
                const archiveBtn = e.target.closest('.cqm-archive');
                if (cancelBtn) {
                    if (!confirm(`Cancel cheque No. ${cancelBtn.dataset.number}? This cannot be undone.`)) return;
                    postAction(`/cheque-management/${cancelBtn.dataset.id}/cancel`, 'Cheque cancelled.');
                } else if (archiveBtn) {
                    postAction(`/cheque-management/${archiveBtn.dataset.id}/archive`, 'Cheque archived.');
                }
            });

            function postAction(url, okMsg) {
                fetch(url, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() } })
                    .then((r) => r.json().then((d) => ({ ok: r.ok, d })))
                    .then(({ ok, d }) => {
                        if (!ok) throw new Error(d.message || 'error');
                        if (window.showToast) showToast('Success', okMsg, 'success');
                        fetchAndRender(currentParams());
                    })
                    .catch((err) => { if (window.showToast) showToast('Action failed', err.message, 'error'); });
            }
        })();
    </script>
    @endpush
</x-layout>
