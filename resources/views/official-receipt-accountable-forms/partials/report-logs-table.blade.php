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
                <th>Starting Qty.</th>
                @foreach ([
                    ['starting_serial_number', 'Starting OR Serial Number'],
                    ['ending_serial_number', 'Ending OR Serial Number'],
                ] as [$column, $label])
                    @php [$icon, $iconClass] = $sortIcon($column); @endphp
                    <th>
                        <a href="{{ $sortLink($column) }}" class="sortable-header">
                            {{ $label }}
                            <x-dynamic-component :component="'bx-' . $icon" class="sort-icon {{ $iconClass }}" />
                        </a>
                    </th>
                @endforeach
                <th>Used</th>
                <th>Remaining</th>
                @foreach ([
                    ['created_at', 'Added Date'],
                    ['created_at', 'Added Time'],
                ] as [$column, $label])
                    @php [$icon, $iconClass] = $sortIcon($column); @endphp
                    <th>
                        <a href="{{ $sortLink($column) }}" class="sortable-header">
                            {{ $label }}
                            <x-dynamic-component :component="'bx-' . $icon" class="sort-icon {{ $iconClass }}" />
                        </a>
                    </th>
                @endforeach
                <th>Status</th>
                @php [$icon, $iconClass] = $sortIcon('added_by'); @endphp
                <th>
                    <a href="{{ $sortLink('added_by') }}" class="sortable-header">
                        Added By
                        <x-dynamic-component :component="'bx-' . $icon" class="sort-icon {{ $iconClass }}" />
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($batches as $batch)
                <tr>
                    <td>{{ $batch->startingQty() }}</td>
                    <td>{{ $batch->starting_serial_number }}</td>
                    <td>{{ $batch->displayEndingSerialNumber() }}</td>
                    <td>{{ $batch->usedQty() }}</td>
                    <td>{{ $batch->remainingQty() }}</td>
                    <td>{{ $batch->created_at->format('F j, Y') }}</td>
                    <td>{{ $batch->created_at->format('h:i A') }}</td>
                    <td>
                        <span class="status-badge {{ $batch->status() === 'Complete' ? 'status-complete' : 'status-incomplete' }}">
                            {{ $batch->status() }}
                        </span>
                    </td>
                    <td>{{ $batch->added_by }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="table-empty">No batches found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

<div class="pagination-bar">
    <div class="pagination-info-group">
        <p class="pagination-info">
            Showing {{ $batches->firstItem() ?? 0 }} to {{ $batches->lastItem() ?? 0 }} of {{ $batches->total() }} entries
        </p>
        <form method="GET" class="per-page-form">
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            <label for="per_page" class="per-page-label">Rows per page</label>
            <select name="per_page" id="per_page" class="form-select form-select-sm per-page-select" onchange="this.form.submit()">
                @foreach ($perPageOptions as $option)
                    <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="pagination-controls">
        @if ($batches->onFirstPage())
            <span class="page-btn" aria-disabled="true">Previous</span>
        @else
            <a class="page-btn" href="{{ $batches->previousPageUrl() }}">Previous</a>
        @endif

        @foreach ($batches->getUrlRange(1, $batches->lastPage()) as $page => $url)
            <a class="page-btn {{ $page === $batches->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
        @endforeach

        @if ($batches->hasMorePages())
            <a class="page-btn" href="{{ $batches->nextPageUrl() }}">Next</a>
        @else
            <span class="page-btn" aria-disabled="true">Next</span>
        @endif
    </div>
</div>
