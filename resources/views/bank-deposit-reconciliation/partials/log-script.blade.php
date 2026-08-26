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

        // ── Reconciliation per-row actions (admin) ───────────────────────────
        const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        function postAction(url, okMsg) {
            fetch(url, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() } })
                .then((r) => r.json().then((d) => ({ ok: r.ok, d })))
                .then(({ ok, d }) => { if (!ok) throw new Error(d.message || 'error'); if (window.showToast) showToast('Success', okMsg, 'success'); fetchAndRender(currentParams()); })
                .catch((err) => { if (window.showToast) showToast('Action failed', err.message, 'error'); });
        }
        container.addEventListener('click', function (e) {
            const clear = e.target.closest('.bdr-clear');
            const bounce = e.target.closest('.bdr-bounce');
            const bounceIn = e.target.closest('.bdr-bounce-in');
            const confirmOnline = e.target.closest('.bdr-confirm');
            if (clear) { if (window.confirm('Mark this cheque cleared?')) postAction(`/bank-deposit-reconciliation/cheques/${clear.dataset.id}/clear`, 'Cheque marked cleared.'); }
            else if (bounce) { if (window.confirm('Mark this cheque bounced?')) postAction(`/bank-deposit-reconciliation/cheques/${bounce.dataset.id}/bounce`, 'Cheque marked bounced.'); }
            else if (bounceIn) { if (window.confirm('Mark this incoming cheque bounced?')) postAction(`/bank-deposit-reconciliation/incoming/${bounceIn.dataset.id}/bounce`, 'Marked bounced.'); }
            else if (confirmOnline) { postAction(`/bank-deposit-reconciliation/incoming/${confirmOnline.dataset.id}/confirm`, 'Online payment confirmed.'); }
        });
    })();
</script>
