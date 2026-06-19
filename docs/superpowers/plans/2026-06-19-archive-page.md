# Archive Page Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the Archive page that groups all system-wide archived records into module tabs — Collection Management archived transactions and User Management archived users — with search, sort, pagination, filter, and AJAX partial loading per tab.

**Architecture:** Single route `GET /archive-records` handles both tabs via a `?tab=` URL parameter. Full-page requests render `archive-records.index` with the active tab's data loaded; AJAX requests return only the active tab's table partial. Tab switching is a full navigation (preserves shareable URLs); search/sort/pagination within a tab are AJAX (no full-page reload). This mirrors the existing Collections and User Management pattern exactly.

**Tech Stack:** Laravel 11, Blade components, vanilla JS `fetch` + `history.replaceState`, existing CSS classes from `app.css`.

---

## File Map

| Action   | File                                                              | Responsibility                                  |
|----------|-------------------------------------------------------------------|-------------------------------------------------|
| Modify   | `routes/web.php` (line 1299 — `Route::get('archive-records'...)`) | Query logic for both tabs + AJAX routing        |
| Create   | `resources/views/archive-records/partials/transactions-table.blade.php` | CM archived transactions table partial |
| Create   | `resources/views/archive-records/partials/users-table.blade.php`  | UM archived users table partial                |
| Replace  | `resources/views/archive-records/index.blade.php`                 | Full page: tabs, toolbar, filter, table container + JS |

No new CSS needed — all structural classes (`collection-content`, `collection-toolbar`, `data-table`, `filter-panel`, etc.) are already defined in `app.css`.

---

## Data Sources

| Tab                  | Query                                                              | Key columns displayed                            |
|----------------------|--------------------------------------------------------------------|--------------------------------------------------|
| Collection Management | `TransactionLog::whereNotNull('archived_at')`                    | serial_number, payee, transacted_at, form_type, archived_at |
| User Management      | `User::where('status', User::STATUS_ARCHIVED)->with('roles')`     | name, email, archived_at, role                   |

Both tabs support: search, sort, per-page select, pagination. CM tab also has form_type filter and date filter (on `archived_at`).

---

## Task 1: Update the `/archive-records` route

**Files:**
- Modify: `routes/web.php` — replace line 1299 stub

- [ ] **Step 1: Replace the stub route with the full handler**

Find this line in `routes/web.php` (last line, ~line 1299):
```php
Route::get('archive-records', function () { return view('archive-records.index'); })->name('archives');
```

Replace it with:
```php
Route::get('/archive-records', function (\Illuminate\Http\Request $request) {
    $tab = in_array($request->input('tab'), ['collection-management', 'user-management'])
        ? $request->input('tab')
        : 'collection-management';

    $perPageOptions = [10, 25, 50, 100];
    $perPage = in_array((int) $request->input('per_page'), $perPageOptions)
        ? (int) $request->input('per_page') : 10;

    if ($tab === 'collection-management') {
        $sortable  = ['serial_number', 'payee', 'transacted_at', 'form_type', 'archived_at'];
        $sort      = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'archived_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $formTypeOptions = ['Form 58', 'BIR0017', 'Form 53', 'Form 28A', 'BIR0016', 'Form 10', 'Form 5IC', 'Form 56'];
        $formTypes = array_intersect((array) $request->input('form_type', []), $formTypeOptions);
        $dateStart = $request->input('date_start');
        $dateEnd   = $request->input('date_end');

        $transactions = \App\Models\TransactionLog::query()
            ->whereNotNull('archived_at')
            ->when($request->input('search'), function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('serial_number', 'like', "%{$search}%")
                      ->orWhere('payee', 'like', "%{$search}%");
                });
            })
            ->when($formTypes, fn ($q, $ft) => $q->whereIn('form_type', $ft))
            ->when($dateStart, fn ($q, $d) => $q->whereDate('archived_at', '>=', $d))
            ->when($dateEnd,   fn ($q, $d) => $q->whereDate('archived_at', '<=', $d))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        $data = [
            'tab'            => $tab,
            'transactions'   => $transactions,
            'perPageOptions' => $perPageOptions,
            'perPage'        => $perPage,
            'sort'           => $sort,
            'direction'      => $direction,
        ];

        if ($request->ajax()) {
            return view('archive-records.partials.transactions-table', $data);
        }

        return view('archive-records.index', $data);
    }

    // tab === 'user-management'
    $sortable  = ['name', 'email', 'archived_at'];
    $sort      = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'archived_at';
    $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

    $users = \App\Models\User::query()
        ->with('roles')
        ->where('status', \App\Models\User::STATUS_ARCHIVED)
        ->when($request->input('search'), function ($q, $search) {
            $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        })
        ->orderBy($sort, $direction)
        ->paginate($perPage)
        ->withQueryString();

    $data = [
        'tab'            => $tab,
        'users'          => $users,
        'perPageOptions' => $perPageOptions,
        'perPage'        => $perPage,
        'sort'           => $sort,
        'direction'      => $direction,
    ];

    if ($request->ajax()) {
        return view('archive-records.partials.users-table', $data);
    }

    return view('archive-records.index', $data);
})->name('archives');
```

- [ ] **Step 2: Verify the route works manually**

Open `http://treasury-management.test/archive-records` in a browser.
Expected: page loads without 500 error (even with the stub view still in place).

- [ ] **Step 3: Commit**

```bash
git add routes/web.php
git commit -m "feat: archive page — update route to serve CM + UM archived data"
```

---

## Task 2: Create the CM archived transactions table partial

**Files:**
- Create: `resources/views/archive-records/partials/transactions-table.blade.php`

- [ ] **Step 1: Create the file**

```blade
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

<div class="table-scroll-area">
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
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
                    <td>{{ $transaction->serial_number }}</td>
                    <td>{{ $transaction->payee }}</td>
                    <td>{{ $transaction->transacted_at->format('F j, Y') }}</td>
                    <td>{{ $transaction->transacted_at->format('h:i:s A') }}</td>
                    <td>{{ \App\Models\TransactionLog::formName($transaction->form_type) }}</td>
                    <td>{{ $transaction->archived_at->format('F j, Y') }}</td>
                    <td class="col-actions">
                        <div class="table-actions">
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
                    <td colspan="7" class="table-empty">No archived transactions found.</td>
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
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/archive-records/partials/transactions-table.blade.php
git commit -m "feat: archive page — CM archived transactions table partial"
```

---

## Task 3: Create the UM archived users table partial

**Files:**
- Create: `resources/views/archive-records/partials/users-table.blade.php`

- [ ] **Step 1: Create the file**

```blade
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

<div class="table-scroll-area">
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
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
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->archived_at?->format('F j, Y') ?? '—' }}</td>
                    <td>{{ $user->roles->first()?->name ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="table-empty">No archived users found.</td>
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
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/archive-records/partials/users-table.blade.php
git commit -m "feat: archive page — UM archived users table partial"
```

---

## Task 4: Rebuild `archive-records/index.blade.php`

**Files:**
- Replace: `resources/views/archive-records/index.blade.php`

This is the main page view. It renders the tab bar, the context-appropriate toolbar (with filter for CM tab, search-only for UM tab), and delegates table rendering to the partials. All AJAX search/sort/pagination JS lives here via `@push('scripts')`.

- [ ] **Step 1: Replace the file contents**

```blade
<x-layout>
    @php
        $tmpRoute  = route('archives');
        $routeName = 'archives';
        $tab = $tab ?? 'collection-management';
    @endphp

    <div class="x-header-container sub-nav-sticky">
        <x-header title="Archives"
            :tmpRoute="$tmpRoute"
            :routeName="$routeName"
        />
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
                    <button type="submit" class="btn btn-light search-btn">
                        <x-bx-search class="icon" />
                        Search
                    </button>
                </form>
            </div>

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
                const chip   = document.createElement('button');
                chip.type    = 'button';
                chip.className = 'filter-chip';
                const label  = document.createElement('span');
                label.textContent = input.dataset.filterLabel;
                chip.appendChild(label);
                const remove = document.createElement('span');
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
                const clearBtn        = document.createElement('button');
                clearBtn.type         = 'button';
                clearBtn.className    = 'filter-clear-btn';
                clearBtn.textContent  = 'Clear Filter';
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
```

- [ ] **Step 2: Open the archive page in a browser and verify both tabs**

1. Go to `http://treasury-management.test/archive-records`
   Expected: "Collection Management" tab is active, table shows CM archived rows (or "No archived transactions found." if none exist yet).

2. Click the "User Management" tab.
   Expected: URL changes to `?tab=user-management`, table shows archived users (or "No archived users found.").

3. On CM tab: type in search box.
   Expected: table updates via AJAX without full-page reload.

4. On CM tab: click the Filter button, select a Form Type, click Apply.
   Expected: table filters to that form type.

5. Click a sort column header.
   Expected: table sorts, `tab` param is preserved in the URL.

- [ ] **Step 3: Commit**

```bash
git add resources/views/archive-records/index.blade.php
git commit -m "feat: archive page — full index view with CM + UM tabs, search, filter, AJAX"
```

---

## Self-Review

**Spec coverage:**
- "Create the archive page" → Tasks 1–4 ✓
- "Group all archive items into their respective module" → CM tab (TransactionLog.archived_at) + UM tab (User.status=archived) ✓
- "All archives will be put here" → both archivable data sources are covered ✓

**Placeholder scan:** No TBDs, no "add appropriate handling" phrases, no partial code blocks. All steps contain full implementation code.

**Type consistency:**
- `$tab` (string): set in route, passed to view, passed to both partials via `@include` — consistent ✓
- `$transactions` (LengthAwarePaginator): used in CM partial, never referenced in UM branch ✓
- `$users` (LengthAwarePaginator): used in UM partial, never referenced in CM branch ✓
- `$sort`, `$direction`, `$perPage`, `$perPageOptions`: passed for both tabs ✓
- `route('archives')` with `['tab' => ...]` param: `archives` is the correct route name (confirmed in `routes/web.php` line 1299) ✓
- `$sortLink` closure captures `$tab` via `use ($sort, $direction, $tab)` in both partials — sort links preserve the `tab` param ✓
- Per-page form `<input type="hidden" name="tab" value="{{ $tab }}">` present in both partials so per-page change preserves the tab ✓
