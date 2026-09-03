<x-layout>
    @php
        $tmpRoute = route('reporting-abstract');
        $routeName = 'reporting-abstract';
    @endphp

    <div class="x-header-container sub-nav-sticky">
        <x-header title="Reporting & Abstract"
            :tmpRoute="$tmpRoute"
            :routeName="$routeName"
        />
    </div>

    <div class="collection-content">
        <div class="collection-toolbar">
            <form class="search-group" role="search" method="GET" id="reports-search-form">
                <input type="search" name="search" class="search-input" id="reports-search-input" placeholder="Search Form" value="{{ request('search') }}" autocomplete="off">
            </form>
        </div>

        <div id="reports-table-container">
            @include('reporting-abstract.partials.reports-table')
        </div>
    </div>

    @include('partials.select-enhancer')

    @push('scripts')
        <script>
            (function () {
                const container = document.getElementById('reports-table-container');
                const searchInput = document.getElementById('reports-search-input');
                const searchForm = document.getElementById('reports-search-form');
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
                            window.cqmEnhanceSelects?.(container);
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
                    const link = event.target.closest('.page-btn');

                    if (!link || link.tagName !== 'A') {
                        return;
                    }

                    event.preventDefault();

                    const linkUrl = new URL(link.href);
                    const params = new URLSearchParams(linkUrl.search);

                    fetchAndRender(params);
                });
            })();
        </script>
    @endpush
</x-layout>
