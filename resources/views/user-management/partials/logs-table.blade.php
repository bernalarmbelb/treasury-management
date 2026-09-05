@php
    $sortLink = function (string $column) use ($sort, $direction) {
        $newDirection = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';

        return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $newDirection, 'page' => null]);
    };

    $sortIcon = function (string $column) use ($sort, $direction) {
        if ($sort !== $column) {
            return ['sort-alt-2', ''];
        }

        return $direction === 'asc' ? ['sort-up', 'active'] : ['sort-down', 'active'];
    };
@endphp

<div class="table-scroll-area">
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                @foreach ([
                    ['user_name', 'Name'],
                    ['action', 'Activity Log'],
                    ['created_at', 'Date'],
                ] as [$column, $label])
                    @php [$icon, $iconClass] = $sortIcon($column); @endphp
                    <th>
                        <a href="{{ $sortLink($column) }}" class="sortable-header">
                            {{ $label }}
                            <x-dynamic-component :component="'bx-' . $icon" class="sort-icon {{ $iconClass }}" />
                        </a>
                    </th>
                @endforeach
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->user_name }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->created_at->format('F d, Y') }}</td>
                    <td>{{ $log->created_at->format('h:i A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="table-empty">No activity logs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

<div class="pagination-bar">
    <div class="pagination-info-group">
        <p class="pagination-info">
            Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
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
        @if ($logs->onFirstPage())
            <span class="page-btn" aria-disabled="true">Previous</span>
        @else
            <a class="page-btn" href="{{ $logs->previousPageUrl() }}">Previous</a>
        @endif

        @foreach ($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
            <a class="page-btn {{ $page === $logs->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
        @endforeach

        @if ($logs->hasMorePages())
            <a class="page-btn" href="{{ $logs->nextPageUrl() }}">Next</a>
        @else
            <span class="page-btn" aria-disabled="true">Next</span>
        @endif
    </div>
</div>
