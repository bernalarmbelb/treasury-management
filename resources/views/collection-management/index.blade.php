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
                <button type="submit" class="btn btn-light search-btn">
                    <x-bx-search class="icon" />
                    Search
                </button>
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
                    <p class="filter-panel-title">Filter By</p>
                    <p class="filter-panel-subtitle">Pto. Diaz Treasury Management System</p>
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

        <div id="transactions-table-container">
            @include('collection-management.partials.transactions-table')
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
            })();
        </script>
    @endpush
</x-layout>
