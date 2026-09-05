<x-layout>
    @php
        $tmpRoute = route('records');
        $routeName = 'records';
    @endphp

    <div class="x-header-container sub-nav-sticky">
        <x-header title="Records"
            :tmpRoute="$tmpRoute"
            :routeName="$routeName"
        />
    </div>

    <div class="collection-content">
        <div class="um-toolbar collection-toolbar">
            <form class="search-group" role="search" method="GET" id="records-search-form">
                <input type="search" name="search" class="search-input" id="records-search-input" placeholder="Search User or Activity" value="{{ request('search') }}" autocomplete="off">
            </form>

            <select name="module" id="records-module-select" class="module-filter-select js-cs">
                <option value="">All Modules</option>
                @foreach ($modules as $option)
                    <option value="{{ $option }}" @selected($module === $option)>{{ $option }}</option>
                @endforeach
            </select>

            <a href="{{ route('records.export', request()->only(['search', 'module']), false) }}" class="um-export-btn" id="records-export-link">Export Log</a>
        </div>

        <div id="records-table-container">
            @include('records.partials.records-table')
        </div>
    </div>

    @include('partials.select-enhancer')

    @push('scripts')
        <script>
            (function () {
                const container = document.getElementById('records-table-container');
                const searchInput = document.getElementById('records-search-input');
                const searchForm = document.getElementById('records-search-form');
                const moduleSelect = document.getElementById('records-module-select');
                const exportLink = document.getElementById('records-export-link');
                let debounceTimer;

                function exportBaseUrl() {
                    return exportLink.href.split('?')[0];
                }

                function updateExportLink(params) {
                    const query = params.toString();
                    exportLink.href = query ? `${exportBaseUrl()}?${query}` : exportBaseUrl();
                }

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
                            updateExportLink(params);
                        });
                }

                function reloadTable() {
                    const params = new URLSearchParams(window.location.search);

                    if (searchInput.value) {
                        params.set('search', searchInput.value);
                    } else {
                        params.delete('search');
                    }

                    if (moduleSelect.value) {
                        params.set('module', moduleSelect.value);
                    } else {
                        params.delete('module');
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

                moduleSelect.addEventListener('change', reloadTable);

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

                    if (moduleSelect.value) {
                        params.set('module', moduleSelect.value);
                    } else {
                        params.delete('module');
                    }

                    fetchAndRender(params);
                });
            })();
        </script>
    @endpush
</x-layout>
