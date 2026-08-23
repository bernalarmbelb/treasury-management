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
                    ['created_at', 'Date & Time Created'],
                    ['pay_to_order_of', 'Pay to the Order of'],
                    ['check_number', 'Cheque Number'],
                    ['amount', 'Amount'],
                    ['status', 'Status'],
                ] as [$column, $label])
                    @php [$icon, $iconClass] = $sortIcon($column); @endphp
                    <th>
                        <a href="{{ $sortLink($column) }}" class="sortable-header">
                            {{ $label }}
                            <x-dynamic-component :component="'bx-' . $icon" class="sort-icon {{ $iconClass }}" />
                        </a>
                    </th>
                @endforeach
                <th class="col-actions text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cheques as $cheque)
                <tr>
                    <td>{{ $cheque->created_at->format('F j, Y · h:i A') }}</td>
                    <td>{{ $cheque->pay_to_order_of ?: '—' }}</td>
                    <td>{{ $cheque->check_number }}</td>
                    <td class="cqm-amount">{{ $cheque->status === 'Cancelled' && ! (float) $cheque->amount ? '—' : '₱ ' . number_format($cheque->amount, 2) }}</td>
                    <td>
                        <span class="status-badge status-{{ strtolower($cheque->status) }}">
                            {{ $cheque->status }}
                        </span>
                    </td>
                    <td class="col-actions">
                        <div class="table-actions">
                            @if ($cheque->status === 'Issued')
                                <button type="button" class="action-btn action-cancel cqm-cancel"
                                    data-id="{{ $cheque->id }}" data-number="{{ $cheque->check_number }}" aria-label="Cancel">
                                    Cancel
                                </button>
                            @elseif ($cheque->status === 'Cancelled' && ! $cheque->archived_at)
                                <button type="button" class="action-btn action-archive cqm-archive"
                                    data-id="{{ $cheque->id }}" aria-label="Archive">
                                    Archive
                                </button>
                            @endif
                            <a href="{{ route('cheque-management.print', $cheque->id) }}" target="_blank"
                               class="action-btn cqm-print-btn" aria-label="Print">Print</a>
                            <a href="{{ route('cheque-management.view', $cheque->id) }}"
                               class="action-btn action-view" aria-label="View">View</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="table-empty">No cheques found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

<div class="pagination-bar">
    <div class="pagination-info-group">
        <p class="pagination-info">
            Showing {{ $cheques->firstItem() ?? 0 }} to {{ $cheques->lastItem() ?? 0 }} of {{ $cheques->total() }} entries
        </p>
        <form method="GET" class="per-page-form">
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if (request('date_start'))
                <input type="hidden" name="date_start" value="{{ request('date_start') }}">
            @endif
            @if (request('date_end'))
                <input type="hidden" name="date_end" value="{{ request('date_end') }}">
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
        @if ($cheques->onFirstPage())
            <span class="page-btn" aria-disabled="true">Previous</span>
        @else
            <a class="page-btn" href="{{ $cheques->previousPageUrl() }}">Previous</a>
        @endif

        @foreach ($cheques->getUrlRange(1, $cheques->lastPage()) as $page => $url)
            <a class="page-btn {{ $page === $cheques->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
        @endforeach

        @if ($cheques->hasMorePages())
            <a class="page-btn" href="{{ $cheques->nextPageUrl() }}">Next</a>
        @else
            <span class="page-btn" aria-disabled="true">Next</span>
        @endif
    </div>
</div>
