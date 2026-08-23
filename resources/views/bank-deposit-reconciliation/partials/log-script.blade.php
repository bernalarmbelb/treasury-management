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

        // Sorting via delegated header clicks on freshly-loaded headers.
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

        // Date range filter.
        const filterToggleBtn = document.getElementById('filterToggleBtn');
        const dateFilterGroup = document.getElementById('dateFilterGroup');
        const dateFilterStart = document.getElementById('dateFilterStart');
        const dateFilterEnd = document.getElementById('dateFilterEnd');
        filterToggleBtn?.addEventListener('click', () => dateFilterGroup.classList.toggle('open'));
        document.getElementById('dateFilterBtn')?.addEventListener('click', function () {
            const params = currentParams();
            if (dateFilterStart.value) params.set('date_start', dateFilterStart.value); else params.delete('date_start');
            if (dateFilterEnd.value) params.set('date_end', dateFilterEnd.value); else params.delete('date_end');
            fetchAndRender(params);
        });
    })();
</script>
