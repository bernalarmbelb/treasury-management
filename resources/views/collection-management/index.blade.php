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

    <div class="collection-content">
        <div class="collection-toolbar">
            <button type="button" class="filter-btn" aria-label="Filter">
                <x-bx-filter-alt class="icon" />
            </button>

            <form class="search-group" role="search" method="GET" id="transaction-search-form">
                <input type="search" name="search" class="search-input" id="transaction-search-input" placeholder="Search Transaction" value="{{ request('search') }}" autocomplete="off">
                <button type="submit" class="btn btn-light search-btn">
                    <x-bx-search class="icon" />
                    Search
                </button>
            </form>
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

                function reloadTable() {
                    const params = new URLSearchParams(window.location.search);

                    if (searchInput.value) {
                        params.set('search', searchInput.value);
                    } else {
                        params.delete('search');
                    }

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

                searchInput.addEventListener('input', function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(reloadTable, 300);
                });

                searchForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    clearTimeout(debounceTimer);
                    reloadTable();
                });
            })();
        </script>
    @endpush
</x-layout>
