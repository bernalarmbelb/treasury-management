<x-layout>
    @php
        $tmpRoute = route('user-management.logs');
        $routeName = 'user-management.logs';
        $parentTitle = 'User Management';
        $parentRoute = route('user-management');
        $parentRouteName = 'user-management';
    @endphp

    <div class="x-header-container sub-nav-sticky">
        <x-header title="Logs"
            :tmpRoute="$tmpRoute"
            :routeName="$routeName"
            :parentTitle="$parentTitle"
            :parentRoute="$parentRoute"
            :parentRouteName="$parentRouteName"
        >
            <x-slot:actions>
                @include('user-management.partials.sub-nav', ['active' => 'logs'])
            </x-slot:actions>
        </x-header>
    </div>

    <div class="collection-content">
        <div class="um-toolbar collection-toolbar">
            <form class="search-group" role="search" method="GET" id="um-logs-search-form">
                <input type="search" name="search" class="search-input" id="um-logs-search-input" placeholder="Search User" value="{{ request('search') }}" autocomplete="off">
            </form>

            <a href="{{ route('user-management.logs.export', [], false) }}" class="um-export-btn">Export Log</a>
        </div>

        <div id="um-logs-table-container">
            @include('user-management.partials.logs-table')
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const container = document.getElementById('um-logs-table-container');
                const searchInput = document.getElementById('um-logs-search-input');
                const searchForm = document.getElementById('um-logs-search-form');
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
