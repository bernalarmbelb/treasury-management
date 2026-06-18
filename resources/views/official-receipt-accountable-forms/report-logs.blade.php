<x-layout>
    @php
        $tmpRoute = route('official-receipts-accountable-forms.report-logs', $formStock);
        $routeName = 'official-receipts-accountable-forms.report-logs';
    @endphp

    <div class="x-header-container sub-nav-sticky">
        <x-header title="Report Logs"
            :tmpRoute="$tmpRoute"
            :routeName="$routeName"
            parentTitle="Official Receipt & Accountable Forms"
            :parentRoute="route('official-receipts-accountable-forms')"
            parentRouteName="official-receipts-accountable-forms"
        />
    </div>

    <div class="collection-content">
        <div class="collection-toolbar">
            <form class="search-group" role="search" method="GET" id="report-log-search-form">
                <input type="search" name="search" class="search-input" id="report-log-search-input" placeholder="Search Batch Number" value="{{ request('search') }}" autocomplete="off">
            </form>
        </div>

        <div id="report-logs-table-container">
            @include('official-receipt-accountable-forms.partials.report-logs-table')
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const container = document.getElementById('report-logs-table-container');
                const searchInput = document.getElementById('report-log-search-input');
                const searchForm = document.getElementById('report-log-search-form');
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
            })();
        </script>
    @endpush
</x-layout>
