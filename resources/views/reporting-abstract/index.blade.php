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

    @include('reporting-abstract.partials.period-select-modal')
    @include('reporting-abstract.partials.report-preview-modal')

    <style>
        @media print {
            body * { visibility: hidden; }
            #ramPrintArea, #ramPrintArea * { visibility: visible; }
            #ramPrintArea {
                position: fixed; top: 0; left: 0; width: 100%;
                border: none !important; background: #fff !important;
            }
            #ramPrintArea .ram-report-doc-header,
            #ramPrintArea .ram-report-officer-row,
            #ramPrintArea .ram-report-table th,
            #ramPrintArea .ram-report-table td,
            #ramPrintArea .ram-report-total-row td {
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

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

                container.addEventListener('click', function (event) {
                    const btn = event.target.closest('[data-report-slug]');

                    if (!btn) {
                        return;
                    }

                    window.ramOpenPeriodModal(btn.dataset.reportSlug, btn.dataset.reportLabel);
                });
            })();
        </script>

        <script>
            (function () {
                const periodOverlay = document.getElementById('ramPeriodModalOverlay');
                const periodTitle = document.getElementById('ramPeriodModalTitle');
                const periodForm = document.getElementById('ramPeriodForm');
                const periodCloseBtn = document.getElementById('ramPeriodModalCloseBtn');

                const previewOverlay = document.getElementById('ramPreviewOverlay');
                const previewCloseBtn = document.getElementById('ramPreviewCloseBtn');
                const printBtn = document.getElementById('ramPreviewPrintBtn');
                const exportBtn = document.getElementById('ramPreviewExportBtn');
                const sectionsContainer = document.getElementById('ramSections');
                const titleEl = document.getElementById('ramTitle');
                const periodEl = document.getElementById('ramPeriod');
                const officerNameEl = document.getElementById('ramOfficerName');
                const designationEl = document.getElementById('ramDesignation');

                let currentSlug = null;
                let currentPeriodParams = null;

                window.ramOpenPeriodModal = function (slug, label) {
                    currentSlug = slug;
                    periodTitle.textContent = label;
                    periodOverlay.classList.add('open');
                };

                function closePeriodModal() {
                    periodOverlay.classList.remove('open');
                }

                periodCloseBtn.addEventListener('click', closePeriodModal);
                periodOverlay.addEventListener('click', function (event) {
                    if (event.target === periodOverlay) {
                        closePeriodModal();
                    }
                });

                function escapeHtml(value) {
                    return String(value ?? '').replace(/[&<>"']/g, function (char) {
                        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char];
                    });
                }

                function renderSections(sections) {
                    sectionsContainer.innerHTML = sections.map(function (section) {
                        const headingHtml = section.heading
                            ? `<p class="ram-report-section-heading">${escapeHtml(section.heading)}</p>`
                            : '';

                        let headHtml;
                        if (section.groups) {
                            const topRow = section.groups.map(function (group) {
                                const span = group.colspan > 1 ? ` colspan="${group.colspan}"` : ' rowspan="2"';
                                return `<th${span}>${escapeHtml(group.label)}</th>`;
                            }).join('');

                            const subRow = section.groups
                                .filter(function (group) { return group.colspan > 1; })
                                .map(function (group) {
                                    return group.subcolumns.map(function (label) {
                                        return `<th>${escapeHtml(label)}</th>`;
                                    }).join('');
                                }).join('');

                            headHtml = `<tr>${topRow}</tr><tr>${subRow}</tr>`;
                        } else {
                            headHtml = '<tr>' + section.columns.map(function (col) {
                                return `<th>${escapeHtml(col.label)}</th>`;
                            }).join('') + '</tr>';
                        }

                        let bodyHtml;
                        if (!section.rows || section.rows.length === 0) {
                            bodyHtml = `<tr><td colspan="${section.columns.length}" class="ram-report-empty">No records found for the selected period.</td></tr>`;
                        } else {
                            bodyHtml = section.rows.map(function (row) {
                                return '<tr>' + row.map(function (value, i) {
                                    const align = section.columns[i] && section.columns[i].align === 'right' ? ' rp-num' : '';
                                    return `<td class="${align}">${escapeHtml(value)}</td>`;
                                }).join('') + '</tr>';
                            }).join('');
                        }

                        let footHtml = '';
                        if (section.totals) {
                            footHtml = '<tfoot><tr class="ram-report-total-row">' + section.totals.map(function (value, i) {
                                const align = section.columns[i] && section.columns[i].align === 'right' ? ' rp-num' : '';
                                return `<td class="${align}">${escapeHtml(value)}</td>`;
                            }).join('') + '</tr></tfoot>';
                        }

                        return `
                            ${headingHtml}
                            <div class="ram-report-table-wrap">
                                <table class="ram-report-table">
                                    <thead>${headHtml}</thead>
                                    <tbody>${bodyHtml}</tbody>
                                    ${footHtml}
                                </table>
                            </div>
                        `;
                    }).join('');
                }

                periodForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const formData = new FormData(periodForm);
                    const params = new URLSearchParams(formData);
                    currentPeriodParams = params;

                    fetch(`/reporting-abstract/${currentSlug}/preview?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    })
                        .then(function (response) { return response.json(); })
                        .then(function (data) {
                            titleEl.textContent = data.title;
                            periodEl.textContent = data.period;
                            officerNameEl.textContent = '';
                            designationEl.textContent = '';
                            renderSections(data.sections);

                            closePeriodModal();
                            previewOverlay.classList.add('open');
                        });
                });

                function closePreviewModal() {
                    previewOverlay.classList.remove('open');
                }

                previewCloseBtn.addEventListener('click', closePreviewModal);
                previewOverlay.addEventListener('click', function (event) {
                    if (event.target === previewOverlay) {
                        closePreviewModal();
                    }
                });

                printBtn.addEventListener('click', function () {
                    window.print();
                });

                exportBtn.addEventListener('click', function () {
                    if (!currentPeriodParams) {
                        return;
                    }

                    const params = new URLSearchParams(currentPeriodParams);
                    params.set('officer_name', officerNameEl.textContent.trim());
                    params.set('designation', designationEl.textContent.trim());

                    window.location.href = `/reporting-abstract/${currentSlug}/export?${params.toString()}`;
                });
            })();
        </script>
    @endpush
</x-layout>
