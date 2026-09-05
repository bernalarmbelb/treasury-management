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
                <th class="col-check">
                    <input type="checkbox" class="tbl-checkbox" id="selectAllCheckbox" title="Select all">
                </th>
                @foreach ([
                    ['serial_number', 'Serial Number'],
                    ['payee', 'Payee'],
                    ['transacted_at', 'Date'],
                    ['transacted_at', 'Time'],
                    ['form_type', 'Form Type'],
                    [null, 'Form Name'],
                    ['payment_method', 'Payment Method'],
                    ['amount', 'Amount'],
                    ['status', 'Status'],
                ] as [$column, $label])
                    @if ($column === null)
                        <th>{{ $label }}</th>
                    @else
                        @php [$icon, $iconClass] = $sortIcon($column); @endphp
                        <th>
                            <a href="{{ $sortLink($column) }}" class="sortable-header">
                                {{ $label }}
                                <x-dynamic-component :component="'bx-' . $icon" class="sort-icon {{ $iconClass }}" />
                            </a>
                        </th>
                    @endif
                @endforeach
                <th class="col-actions text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td class="col-check">
                        <input type="checkbox" class="tbl-checkbox row-checkbox"
                            data-id="{{ $transaction->id }}"
                            data-status="{{ $transaction->status }}"
                            data-archived="{{ $transaction->archived_at ? '1' : '0' }}">
                    </td>
                    <td>{{ $transaction->serial_number }}</td>
                    <td>{{ $transaction->payee }}</td>
                    <td>{{ $transaction->transacted_at->format('F j, Y') }}</td>
                    <td>{{ $transaction->transacted_at->format('h:i:s A') }}</td>
                    <td>{{ $transaction->form_type }}</td>
                    <td>{{ \App\Models\TransactionLog::formName($transaction->form_type) }}</td>
                    <td>{{ $transaction->payment_method ? ucwords(str_replace('_', ' ', $transaction->payment_method)) : '—' }}</td>
                    <td style="font-variant-numeric:tabular-nums; white-space:nowrap;">{{ $transaction->amount !== null ? '₱ ' . number_format($transaction->amount, 2) : '—' }}</td>
                    <td>
                        <span class="status-badge status-{{ strtolower($transaction->status) }}">
                            {{ $transaction->status }}
                        </span>
                    </td>
                    <td class="col-actions">
                        <div class="table-actions">
                            @if($transaction->status === 'Completed')
                                <button type="button"
                                    class="action-btn action-cancel"
                                    data-id="{{ $transaction->id }}"
                                    data-serial="{{ $transaction->serial_number }}"
                                    data-payee="{{ $transaction->payee }}"
                                    data-cancel-type="{{ ($isAdmin ?? false) ? 'direct' : 'request' }}"
                                    aria-label="Cancel">
                                    Cancel
                                </button>
                            @elseif($transaction->status === 'Cancelled' && !$transaction->archived_at)
                                <button type="button"
                                    class="action-btn action-archive"
                                    data-id="{{ $transaction->id }}"
                                    aria-label="Archive">
                                    Archive
                                </button>
                            @endif
                            <a href="{{ route('collections.view', $transaction->id) }}"
                               class="action-btn action-view"
                               aria-label="View">
                                View
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="table-empty">No transactions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

<div class="pagination-bar">
    <div class="pagination-info-group">
        <p class="pagination-info">
            Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} entries
        </p>
        <form method="GET" class="per-page-form">
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @foreach (request('status', []) as $status)
                <input type="hidden" name="status[]" value="{{ $status }}">
            @endforeach
            @foreach (request('form_type', []) as $formType)
                <input type="hidden" name="form_type[]" value="{{ $formType }}">
            @endforeach
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
        @if ($transactions->onFirstPage())
            <span class="page-btn" aria-disabled="true">Previous</span>
        @else
            <a class="page-btn" href="{{ $transactions->previousPageUrl() }}">Previous</a>
        @endif

        @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
            <a class="page-btn {{ $page === $transactions->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
        @endforeach

        @if ($transactions->hasMorePages())
            <a class="page-btn" href="{{ $transactions->nextPageUrl() }}">Next</a>
        @else
            <span class="page-btn" aria-disabled="true">Next</span>
        @endif
    </div>
</div>
