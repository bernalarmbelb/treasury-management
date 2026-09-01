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

    $qtyClass = function (int $qty) {
        if ($qty === 0) {
            return 'qty-danger';
        }

        return $qty <= 50 ? 'qty-warning' : 'qty-success';
    };
@endphp

<div class="table-scroll-area">
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                @foreach ([
                    ['qty', 'Qty.'],
                    ['form_name', 'Form Name'],
                    ['form_code', 'Form Code'],
                    ['added_date', 'Added Date'],
                    ['added_time', 'Added Time'],
                    ['added_by', 'Added By'],
                ] as [$column, $label])
                    @php [$icon, $iconClass] = $sortIcon($column); @endphp
                    <th>
                        <a href="{{ $sortLink($column) }}" class="sortable-header">
                            {{ $label }}
                            <x-dynamic-component :component="'bx-' . $icon" class="sort-icon {{ $iconClass }}" />
                        </a>
                    </th>
                @endforeach
                <th class="col-actions text-start">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($forms as $form)
                @php
                    $availableQty = auth()->user()?->hasRole('collector')
                        ? $form->availableQtyForCollector(auth()->user()->name)
                        : $form->availableQty();
                @endphp
                <tr>
                    <td class="{{ $qtyClass($availableQty) }}">{{ $availableQty }}</td>
                    <td>{{ $form->form_name }}</td>
                    <td>{{ $form->form_code }}</td>
                    <td>{{ $form->added_date->format('F j, Y') }}</td>
                    <td>{{ $form->added_time ? \Illuminate\Support\Carbon::parse($form->added_time)->format('h:i A') : '-' }}</td>
                    <td>{{ $form->added_by }}</td>
                    <td class="col-actions">
                        <div class="table-actions">
                            <a href="{{ route('official-receipts-accountable-forms.report-logs', $form->id) }}" class="action-btn action-view" aria-label="View">View</a>
                            @if (auth()->user()?->hasRole('collector'))
                                @php $myPendingRequest = ($myPendingBatchRequests ?? collect())->get($form->id); @endphp
                                @if ($myPendingRequest)
                                    <button type="button" class="action-btn action-export js-cancel-batch-request" data-request-id="{{ $myPendingRequest->id }}" aria-label="Cancel Pending Request">Cancel Pending Request (Qty {{ $myPendingRequest->quantity }})</button>
                                @else
                                    <button type="button" class="action-btn action-batch js-request-batch" data-form-stock-id="{{ $form->id }}" data-form-code="{{ $form->form_code }}" aria-label="Request New Batch">Request New Batch</button>
                                @endif
                            @else
                                <button type="button" class="action-btn action-batch js-add-batch" data-form-stock-id="{{ $form->id }}" data-form-code="{{ $form->form_code }}" data-next-serial="{{ $form->nextBatchStartingSerial() }}" aria-label="Add New Batch">Add New Batch</button>
                            @endif
                            <button type="button" class="action-btn action-export js-open-export" data-form-stock-id="{{ $form->id }}" data-form-code="{{ $form->form_code }}" aria-label="Export">Export</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="table-empty">No forms found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

<div class="pagination-bar">
    <div class="pagination-info-group">
        <p class="pagination-info">
            Showing {{ $forms->firstItem() ?? 0 }} to {{ $forms->lastItem() ?? 0 }} of {{ $forms->total() }} entries
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
        @if ($forms->onFirstPage())
            <span class="page-btn" aria-disabled="true">Previous</span>
        @else
            <a class="page-btn" href="{{ $forms->previousPageUrl() }}">Previous</a>
        @endif

        @foreach ($forms->getUrlRange(1, $forms->lastPage()) as $page => $url)
            <a class="page-btn {{ $page === $forms->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
        @endforeach

        @if ($forms->hasMorePages())
            <a class="page-btn" href="{{ $forms->nextPageUrl() }}">Next</a>
        @else
            <span class="page-btn" aria-disabled="true">Next</span>
        @endif
    </div>
</div>
