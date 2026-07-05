<x-layout>
    @php
        $tmpRoute  = route('archives');
        $routeName = 'archives';
        $tab = $tab ?? 'collection-management';
    @endphp

    <div class="x-header-container sub-nav-sticky">
        @if ($tab === 'collection-management')
            <x-header
                title="Collection Management"
                :tmpRoute="route('archives', ['tab' => 'collection-management'])"
                routeName="archives"
                parentTitle="Archives"
                :parentRoute="route('archives')"
                parentRouteName="archives"
            />
        @else
            <x-header
                title="User Management"
                :tmpRoute="route('archives', ['tab' => 'user-management'])"
                routeName="archives"
                parentTitle="Archives"
                :parentRoute="route('archives')"
                parentRouteName="archives"
            />
        @endif
        <div style="display:flex; align-items: center; border: 0px solid red; margin: 0px;">
            <button class="nav-scroll-btn nav-scroll-left" id="scrollLeft">&#8249;</button>
            <nav class="navigation-bar" id="navigationBar">
                <p><a href="{{ route('archives', ['tab' => 'collection-management']) }}"
                      class="{{ $tab === 'collection-management' ? 'active' : '' }}">
                    Collection Management
                </a></p>
                <p><a href="{{ route('archives', ['tab' => 'user-management']) }}"
                      class="{{ $tab === 'user-management' ? 'active' : '' }}">
                    User Management
                </a></p>
            </nav>
            <button class="nav-scroll-btn nav-scroll-right" id="scrollRight">&#8250;</button>
        </div>
    </div>

    <div class="collection-content">

        {{-- ── Collection Management tab ──────────────────────────────────────── --}}
        @if ($tab === 'collection-management')
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
                $dateStart         = request('date_start');
                $dateEnd           = request('date_end');
                $dateFilterActive  = $dateStart || $dateEnd;
            @endphp

            <div class="collection-toolbar">
                <div class="filter-wrapper">
                    <button type="button" class="filter-btn" id="filterToggleBtn" aria-label="Filter" aria-expanded="false">
                        <x-icons.filter-fill class="icon" />
                    </button>
                </div>

                <form class="search-group" role="search" method="GET" id="archive-search-form" action="{{ route('archives') }}">
                    <input type="hidden" name="tab" value="collection-management">
                    <input type="search" name="search" class="search-input" id="archive-search-input"
                           placeholder="Search Serial / Payee"
                           value="{{ request('search') }}" autocomplete="off">
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
                            <input type="checkbox" name="filter_date" id="filterDate" data-filter-label="Archived Date" @checked($dateFilterActive)>
                            <span>Archived Date</span>
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="filter_form_type" id="filterFormType" data-filter-label="Form Type" @checked(! empty($selectedFormTypes))>
                            <span>Form Type</span>
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

            @include('archive-records.partials.bulk-action-bar')

            <div id="archive-table-container">
                @include('archive-records.partials.transactions-table')
            </div>

        {{-- ── User Management tab ────────────────────────────────────────────── --}}
        @elseif ($tab === 'user-management')

            <div class="collection-toolbar">
                <form class="search-group" role="search" method="GET" id="archive-search-form" action="{{ route('archives') }}">
                    <input type="hidden" name="tab" value="user-management">
                    <input type="search" name="search" class="search-input" id="archive-search-input"
                           placeholder="Search Name / Email"
                           value="{{ request('search') }}" autocomplete="off">
                </form>
            </div>

            @include('archive-records.partials.bulk-action-bar')

            <div id="archive-table-container">
                @include('archive-records.partials.users-table')
            </div>

        @endif
    </div>

    @push('scripts')
    <script>
    (function () {
        const container   = document.getElementById('archive-table-container');
        const searchInput = document.getElementById('archive-search-input');
        const searchForm  = document.getElementById('archive-search-form');
        if (!container || !searchInput || !searchForm) return;

        let debounceTimer;
        const baseUrl = '{{ route('archives') }}';

        function fetchAndRender(params) {
            params.delete('page');
            const query = params.toString();
            const url   = query ? `${baseUrl}?${query}` : baseUrl;

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    container.innerHTML = html;
                    window.history.replaceState({}, '', url);
                    updateBulkBar();
                });
        }

        function reloadTable() {
            const params = new URLSearchParams(window.location.search);
            if (searchInput.value) params.set('search', searchInput.value);
            else params.delete('search');
            fetchAndRender(params);
        }

        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(reloadTable, 300);
        });

        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            clearTimeout(debounceTimer);
            reloadTable();
        });

        container.addEventListener('click', function (e) {
            const link = e.target.closest('.sortable-header');
            if (!link) return;
            e.preventDefault();
            const linkUrl = new URL(link.href);
            const params  = new URLSearchParams(window.location.search);
            params.set('sort',      linkUrl.searchParams.get('sort'));
            params.set('direction', linkUrl.searchParams.get('direction'));
            if (searchInput.value) params.set('search', searchInput.value);
            else params.delete('search');
            fetchAndRender(params);
        });

        // ── Bulk selection + row unarchive (persists across AJAX reloads) ────
        // Bulk bar lives outside the reloaded container; row/select-all events
        // are delegated on the persistent container. Endpoint + wording switch
        // by tab (collection transactions vs users).
        const unarchiveBase = '{{ $tab === 'user-management' ? '/archive-records/users' : '/archive-records/transactions' }}';
        const bulkNoun      = '{{ $tab === 'user-management' ? 'user' : 'transaction' }}';
        const bulkMsg       = '{{ $tab === 'user-management' ? 'Their accounts will be reactivated.' : 'They will reappear in Collection Management.' }}';
        const rowMsg        = '{{ $tab === 'user-management' ? 'Their account will be reactivated.' : 'It will reappear in Collection Management.' }}';
        const csrf          = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        const bulkBar       = document.getElementById('bulkActionBar');
        const bulkCount     = document.getElementById('bulkActionCount');
        const bulkUnarchive = document.getElementById('bulkUnarchiveBtn');
        const bulkClear     = document.getElementById('bulkClearBtn');

        const rowCheckboxes = () => [...container.querySelectorAll('.row-checkbox')];
        const checkedBoxes  = () => rowCheckboxes().filter(cb => cb.checked);

        function updateBulkBar() {
            const n = checkedBoxes().length;
            if (bulkBar)   bulkBar.style.display = n > 0 ? 'flex' : 'none';
            if (bulkCount) bulkCount.textContent = n === 1 ? '1 item selected' : `${n} items selected`;
            const selectAll = container.querySelector('#selectAllCheckbox');
            const all = rowCheckboxes();
            if (selectAll) {
                selectAll.indeterminate = n > 0 && n < all.length;
                selectAll.checked = all.length > 0 && n === all.length;
            }
        }

        container.addEventListener('change', function (e) {
            if (e.target.id === 'selectAllCheckbox') {
                rowCheckboxes().forEach(cb => { cb.checked = e.target.checked; });
                updateBulkBar();
            } else if (e.target.classList.contains('row-checkbox')) {
                updateBulkBar();
            }
        });

        if (bulkClear) {
            bulkClear.addEventListener('click', () => {
                rowCheckboxes().forEach(cb => { cb.checked = false; });
                updateBulkBar();
            });
        }

        if (bulkUnarchive) {
            bulkUnarchive.addEventListener('click', function () {
                const ids = checkedBoxes().map(cb => parseInt(cb.dataset.id));
                if (!ids.length) return;
                if (!confirm(`Unarchive ${ids.length} ${bulkNoun}(s)? ${bulkMsg}`)) return;
                bulkUnarchive.disabled = true;
                bulkUnarchive.textContent = 'Unarchiving…';
                Promise.all(ids.map(id =>
                    fetch(`${unarchiveBase}/${id}/unarchive`, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() },
                    }).then(r => r.json())
                ))
                .then(() => { showToast(`${ids.length} ${bulkNoun}(s) unarchived.`); window.location.reload(); })
                .catch(() => showToast('Action could not be completed', 'Something went wrong. Please try again.', 'error'))
                .finally(() => { bulkUnarchive.disabled = false; bulkUnarchive.textContent = 'Unarchive Selected'; });
            });
        }

        // Row-level unarchive (delegated on persistent container).
        container.addEventListener('click', function (e) {
            const btn = e.target.closest('.action-unarchive');
            if (!btn) return;
            if (!confirm(`Unarchive this ${bulkNoun}? ${rowMsg}`)) return;
            btn.disabled = true;
            btn.textContent = 'Unarchiving…';
            fetch(`${unarchiveBase}/${btn.dataset.id}/unarchive`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() },
            })
            .then(r => r.json())
            .then(data => { showToast(data.message); btn.closest('tr')?.remove(); updateBulkBar(); })
            .catch(() => showToast('Action could not be completed', 'Something went wrong. Please try again.', 'error'))
            .finally(() => { btn.disabled = false; btn.textContent = 'Unarchive'; });
        });

        @if ($tab === 'collection-management')
        // ── Filter panel (CM tab only) ──────────────────────────────────────
        const filterToggleBtn    = document.getElementById('filterToggleBtn');
        const filterModalOverlay = document.getElementById('filterModalOverlay');
        const filterPanel        = document.getElementById('filterPanel');
        const filterApplyBtn     = document.getElementById('filterApplyBtn');
        const filterFormType     = document.getElementById('filterFormType');
        const filterSelectForm   = document.getElementById('filterSelectForm');
        const filterBreadcrumbs  = document.getElementById('filterBreadcrumbs');
        const filterDate         = document.getElementById('filterDate');
        const dateFilterGroup    = document.getElementById('dateFilterGroup');
        const dateFilterStart    = document.getElementById('dateFilterStart');
        const dateFilterEnd      = document.getElementById('dateFilterEnd');
        const dateFilterBtn      = document.getElementById('dateFilterBtn');

        const openFilterModal  = () => { filterModalOverlay.classList.add('open');    filterToggleBtn.setAttribute('aria-expanded', 'true');  };
        const closeFilterModal = () => { filterModalOverlay.classList.remove('open'); filterToggleBtn.setAttribute('aria-expanded', 'false'); };

        filterToggleBtn.addEventListener('click', () =>
            filterModalOverlay.classList.contains('open') ? closeFilterModal() : openFilterModal());
        filterModalOverlay.addEventListener('click', e => { if (e.target === filterModalOverlay) closeFilterModal(); });
        document.getElementById('filterCloseBtn')?.addEventListener('click', closeFilterModal);

        filterFormType.addEventListener('change', () =>
            filterSelectForm.classList.toggle('open', filterFormType.checked));

        filterDate.addEventListener('change', function () {
            dateFilterGroup.classList.toggle('open', filterDate.checked);
            if (!filterDate.checked) {
                dateFilterStart.value = '';
                dateFilterEnd.value   = '';
                applyFilters();
            }
        });

        dateFilterBtn.addEventListener('click', applyFilters);

        function applyFilters() {
            const params = new URLSearchParams(window.location.search);
            params.set('tab', 'collection-management');

            params.delete('form_type[]');
            if (filterFormType.checked) {
                filterPanel.querySelectorAll('input[name="form_type"]:checked')
                    .forEach(cb => params.append('form_type[]', cb.value));
            } else {
                filterPanel.querySelectorAll('input[name="form_type"]')
                    .forEach(cb => { cb.checked = false; });
            }

            if (filterDate.checked) {
                if (dateFilterStart.value) params.set('date_start', dateFilterStart.value);
                else params.delete('date_start');
                if (dateFilterEnd.value) params.set('date_end', dateFilterEnd.value);
                else params.delete('date_end');
            } else {
                params.delete('date_start');
                params.delete('date_end');
            }

            if (searchInput.value) params.set('search', searchInput.value);
            else params.delete('search');

            fetchAndRender(params);
            updateBreadcrumbs();
        }

        function updateBreadcrumbs() {
            filterBreadcrumbs.innerHTML = '';
            const activeInputs = Array.from(filterPanel.querySelectorAll('[data-filter-label]'))
                .filter(i => i.checked);

            activeInputs.forEach(function (input) {
                const chip     = document.createElement('button');
                chip.type      = 'button';
                chip.className = 'filter-chip';
                const label    = document.createElement('span');
                label.textContent = input.dataset.filterLabel;
                chip.appendChild(label);
                const remove   = document.createElement('span');
                remove.className  = 'filter-chip-remove';
                remove.textContent = '×';
                chip.appendChild(remove);
                chip.addEventListener('click', function () {
                    input.checked = false;
                    if (input === filterFormType) {
                        filterSelectForm.classList.remove('open');
                        filterPanel.querySelectorAll('input[name="form_type"]').forEach(cb => { cb.checked = false; });
                    }
                    if (input === filterDate) {
                        dateFilterGroup.classList.remove('open');
                        dateFilterStart.value = '';
                        dateFilterEnd.value   = '';
                    }
                    applyFilters();
                });
                filterBreadcrumbs.appendChild(chip);
            });

            if (activeInputs.length > 0) {
                const clearBtn       = document.createElement('button');
                clearBtn.type        = 'button';
                clearBtn.className   = 'filter-clear-btn';
                clearBtn.textContent = 'Clear Filter';
                clearBtn.addEventListener('click', function () {
                    filterPanel.querySelectorAll('[data-filter-label], input[name="form_type"]')
                        .forEach(i => { i.checked = false; });
                    filterSelectForm.classList.remove('open');
                    dateFilterGroup.classList.remove('open');
                    dateFilterStart.value = '';
                    dateFilterEnd.value   = '';
                    applyFilters();
                });
                filterBreadcrumbs.appendChild(clearBtn);
            }

            filterBreadcrumbs.classList.toggle('visible', activeInputs.length > 0);
        }

        filterApplyBtn.addEventListener('click', function () { applyFilters(); closeFilterModal(); });
        updateBreadcrumbs();
        @endif
    })();
    </script>
    @endpush
</x-layout>
