<div class="table-scroll-area">
<div class="table-wrapper">
    <table class="data-table ra-reports-table">
        <thead>
            <tr>
                <th>Abstract</th>
                <th class="col-actions text-start">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $report)
                <tr>
                    <td>{{ $report['label'] }}</td>
                    <td class="col-actions text-center">
                        @if (! empty($report['url']))
                            <button
                                type="button"
                                class="action-btn action-view"
                                onclick="window.open('{{ $report['url'] }}', '_blank', 'noopener')"
                            >
                                Generate Report
                            </button>
                        @elseif ($report['slug'])
                            <button
                                type="button"
                                class="action-btn action-view"
                                data-report-slug="{{ $report['slug'] }}"
                                data-report-label="{{ $report['label'] }}"
                            >
                                Generate Report
                            </button>
                        @else
                            <span class="ra-coming-soon-tag">Coming Soon</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="table-empty">No reports found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

<div class="pagination-bar">
    <div class="pagination-info-group">
        <p class="pagination-info">
            Showing {{ $reports->firstItem() ?? 0 }} to {{ $reports->lastItem() ?? 0 }} of {{ $reports->total() }} entries
        </p>
        <form method="GET" class="per-page-form">
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            <label for="per_page" class="per-page-label">Rows per page</label>
            <select name="per_page" id="per_page" class="form-select form-select-sm per-page-select js-cs" data-cs-inline onchange="this.form.submit()">
                @foreach ($perPageOptions as $option)
                    <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="pagination-controls">
        @if ($reports->onFirstPage())
            <span class="page-btn" aria-disabled="true">Previous</span>
        @else
            <a class="page-btn" href="{{ $reports->previousPageUrl() }}">Previous</a>
        @endif

        @foreach ($reports->getUrlRange(1, $reports->lastPage()) as $page => $url)
            <a class="page-btn {{ $page === $reports->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
        @endforeach

        @if ($reports->hasMorePages())
            <a class="page-btn" href="{{ $reports->nextPageUrl() }}">Next</a>
        @else
            <span class="page-btn" aria-disabled="true">Next</span>
        @endif
    </div>
</div>
