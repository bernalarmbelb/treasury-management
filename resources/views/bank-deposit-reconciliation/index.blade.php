<x-layout>
    @php
        $tmpRoute = route('bank-deposit-reconciliation');
        $routeName = 'bank-deposit-reconciliation';
        $dateStart = request('date_start');
        $dateEnd = request('date_end');
        $dateFilterActive = $dateStart || $dateEnd;
    @endphp

    <div class="x-header-container sub-nav-sticky">
        <x-header title="Bank Deposit &amp; Reconciliation" :tmpRoute="$tmpRoute" :routeName="$routeName">
            <x-slot:actions>
                @include('bank-deposit-reconciliation.partials.sub-nav')
            </x-slot:actions>
        </x-header>
    </div>

    @include('bank-deposit-reconciliation.partials.styles')

    <div class="collection-content">
        <div class="collection-toolbar">
            <div class="filter-wrapper">
                <button type="button" class="filter-btn" id="filterToggleBtn" aria-label="Filter" aria-expanded="false">
                    <x-icons.filter-fill class="icon" />
                </button>
            </div>

            <form class="search-group" role="search" method="GET" id="bdr-search-form" action="{{ route('bank-deposit-reconciliation') }}">
                <input type="search" name="search" class="search-input" id="bdr-search-input" placeholder="Search payee or reference" value="{{ request('search') }}" autocomplete="off">
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
                    <button type="button" class="filter-panel-close-btn" id="filterCloseBtn" aria-label="Close"><x-bx-x class="icon" /></button>
                </div>
                <div class="filter-options">
                    <label class="filter-option"><input type="checkbox" id="filterDate" data-filter-label="Date" @checked($dateFilterActive)><span>Date</span></label>
                    @foreach (['Incoming', 'Outgoing'] as $type)
                        <label class="filter-option"><input type="checkbox" name="type" value="{{ $type }}" data-filter-label="{{ $type }}" @checked(in_array($type, request('type', [])))><span>{{ $type }}</span></label>
                    @endforeach
                    @foreach (['Pending', 'Completed', 'Failed', 'Void'] as $st)
                        <label class="filter-option"><input type="checkbox" name="status" value="{{ $st }}" data-filter-label="{{ $st }}" @checked(in_array($st, request('status', [])))><span>{{ $st }}</span></label>
                    @endforeach
                </div>
                <div class="filter-apply-row"><button type="button" class="filter-apply-btn" id="filterApplyBtn">Apply</button></div>
            </div>
        </div>

        <div id="bdr-table-container">
            @include('bank-deposit-reconciliation.partials.combined-table')
        </div>

        @include('partials.select-enhancer')
    </div>

    @push('scripts')
    <script>
        (function () {
            const container = document.getElementById('bdr-table-container');
            const searchInput = document.getElementById('bdr-search-input');
            const searchForm = document.getElementById('bdr-search-form');
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

            searchInput.addEventListener('input', function () { clearTimeout(debounce); debounce = setTimeout(() => fetchAndRender(currentParams()), 300); });
            searchForm.addEventListener('submit', function (e) { e.preventDefault(); fetchAndRender(currentParams()); });

            const filterToggleBtn = document.getElementById('filterToggleBtn');
            const filterModalOverlay = document.getElementById('filterModalOverlay');
            const filterPanel = document.getElementById('filterPanel');
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
                params.delete('type'); params.delete('status');
                filterPanel.querySelectorAll('input[name="type"]:checked').forEach((c) => params.append('type[]', c.value));
                filterPanel.querySelectorAll('input[name="status"]:checked').forEach((c) => params.append('status[]', c.value));
                if (filterDate.checked && dateFilterStart.value) params.set('date_start', dateFilterStart.value); else params.delete('date_start');
                if (filterDate.checked && dateFilterEnd.value) params.set('date_end', dateFilterEnd.value); else params.delete('date_end');
                fetchAndRender(params);
            }
            document.getElementById('dateFilterBtn').addEventListener('click', applyFilters);
            document.getElementById('filterApplyBtn').addEventListener('click', () => { applyFilters(); filterModalOverlay.classList.remove('open'); });
        })();
    </script>
    @endpush
</x-layout>
