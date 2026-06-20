<x-layout>
    @php
        $tmpRoute = route('transaction-entry');
        $routeName = 'transaction-entry';
    @endphp

    <div class="x-header-container sub-nav-sticky">
        <x-header title="Transaction Entry"
            :tmpRoute="$tmpRoute"
            :routeName="$routeName"
            parentTitle="Collections Management"
            :parentRoute="route('collections')"
            parentRouteName="collections"
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
            <form class="search-group" role="search" method="GET" id="form-stock-search-form">
                <input type="search" name="search" class="search-input" id="form-stock-search-input" placeholder="Search Form" value="{{ request('search') }}" autocomplete="off">
            </form>
        </div>

        <div id="form-stocks-table-container">
            @include('collection-management.transaction-entry.partials.form-stocks-table')
        </div>

        @include('collection-management.transaction-entry.partials.add-batch-modal')
    </div>

    @push('scripts')
        <script>
            (function () {
                const container = document.getElementById('form-stocks-table-container');
                const searchInput = document.getElementById('form-stock-search-input');
                const searchForm = document.getElementById('form-stock-search-form');
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

                const modalOverlay = document.getElementById('formBatchModalOverlay');
                const modalTitle = document.getElementById('formBatchModalTitle');
                const modalForm = document.getElementById('formBatchForm');
                const successAlert = document.getElementById('formBatchSuccessAlert');
                const successAlertSubtitle = document.getElementById('formBatchSuccessAlertSubtitle');
                let successAlertTimer;
                let currentFormCode = '';

                function showSuccessAlert(formCode) {
                    successAlertSubtitle.textContent = formCode;
                    successAlert.classList.add('show');

                    clearTimeout(successAlertTimer);
                    successAlertTimer = setTimeout(() => {
                        successAlert.classList.remove('show');
                    }, 3000);
                }

                function openBatchModal(formStockId, formCode) {
                    currentFormCode = formCode;
                    modalTitle.textContent = `Add new batch of ${formCode}`;
                    modalForm.action = `/collections/transaction-entry/${formStockId}/batches`;
                    modalOverlay.classList.add('open');
                }

                function closeBatchModal() {
                    modalOverlay.classList.remove('open');
                    modalForm.reset();
                }

                container.addEventListener('click', function (event) {
                    const trigger = event.target.closest('.js-add-receipt');

                    if (!trigger) {
                        return;
                    }

                    event.preventDefault();
                    openBatchModal(trigger.dataset.formStockId, trigger.dataset.formCode);
                });

                document.getElementById('formBatchCloseBtn').addEventListener('click', closeBatchModal);

                modalOverlay.addEventListener('click', function (event) {
                    if (event.target === modalOverlay) {
                        closeBatchModal();
                    }
                });

                modalForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const submitUrl = modalForm.action + window.location.search;

                    fetch(submitUrl, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: new FormData(modalForm),
                    })
                        .then((response) => {
                            if (!response.ok) {
                                return response.json().then((data) => {
                                    showToast('Action could not be completed', data.message, 'error');
                                    throw new Error(data.message);
                                });
                            }

                            return response.text();
                        })
                        .then((html) => {
                            container.innerHTML = html;
                            closeBatchModal();
                            showSuccessAlert(currentFormCode);
                        })
                        .catch(() => {});
                });
            })();
        </script>
    @endpush
</x-layout>
