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

{{-- Server-computed default Starting Serial for the next batch (re-rendered on each AJAX reload so it stays current). --}}
<span id="reportLogNextSerial" data-next-serial="{{ $formStock->nextBatchStartingSerial() }}" hidden></span>

@if (($pendingBatchRequests ?? collect())->isNotEmpty())
    <div class="batch-request-panel">
        <p class="batch-request-panel-title">Pending Batch Requests</p>
        @foreach ($pendingBatchRequests as $batchRequest)
            <div class="batch-request-row">
                <div class="batch-request-info">
                    <span class="batch-request-requester">{{ $batchRequest->requestedByUser?->name ?? 'Unknown' }}</span>
                    <span class="batch-request-qty">Qty: {{ $batchRequest->quantity }}</span>
                    @if ($batchRequest->note)
                        <span class="batch-request-note">{{ $batchRequest->note }}</span>
                    @endif
                    <span class="batch-request-date">{{ $batchRequest->created_at->format('M j, Y') }}</span>
                </div>
                <div class="table-actions">
                    <button type="button" class="action-btn action-view js-fulfill-batch-request" data-request-id="{{ $batchRequest->id }}" data-quantity="{{ $batchRequest->quantity }}">Fulfill</button>
                    <button type="button" class="action-btn action-export js-reject-batch-request" data-request-id="{{ $batchRequest->id }}">Reject</button>
                </div>
            </div>
        @endforeach
    </div>
@endif

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
                <th>Assigned To</th>
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
                    @php
                        $collectorNames = ($collectors ?? collect())->all();
                        $isOtherAssignee = $batch->assigned_to && ! in_array($batch->assigned_to, $collectorNames, true);
                    @endphp
                    <td class="assigned-to-cell">
                        @if (! auth()->user()?->hasRole('collector'))
                        <div class="assigned-to-control" data-batch-id="{{ $batch->id }}">
                            <button type="button" class="assigned-to-trigger {{ $isOtherAssignee ? 'is-hidden' : '' }}" data-batch-id="{{ $batch->id }}">
                                <span class="assigned-to-trigger-label">{{ $batch->assigned_to ?: 'Unassigned' }}</span>
                                <x-bx-chevron-down class="icon assigned-to-chevron" />
                            </button>
                            <div class="assigned-to-menu is-hidden" role="listbox">
                                <button type="button" class="assigned-to-option {{ ! $batch->assigned_to ? 'is-selected' : '' }}" data-value="">Unassigned</button>
                                @foreach ($collectors ?? [] as $collector)
                                    <button type="button" class="assigned-to-option {{ $batch->assigned_to === $collector ? 'is-selected' : '' }}" data-value="{{ $collector }}">{{ $collector }}</button>
                                @endforeach
                                <button type="button" class="assigned-to-option assigned-to-option-other {{ $isOtherAssignee ? 'is-selected' : '' }}" data-value="__other__">Other (specify)&hellip;</button>
                            </div>
                            <div class="assigned-to-other-wrap {{ $isOtherAssignee ? '' : 'is-hidden' }}">
                                <input
                                    type="text"
                                    class="assigned-to-other-input"
                                    data-batch-id="{{ $batch->id }}"
                                    placeholder="Collector or barangay name"
                                    value="{{ $isOtherAssignee ? $batch->assigned_to : '' }}"
                                >
                                <button type="button" class="assigned-to-other-back" data-batch-id="{{ $batch->id }}" aria-label="Choose from list">
                                    <x-bx-list-ul class="icon" />
                                </button>
                            </div>
                        </div>
                        @else
                        <span>{{ $batch->assigned_to ?: 'Unassigned' }}</span>
                        @endif
                    </td>
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
                    <td colspan="10" class="table-empty">No batches found.</td>
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
            <select name="per_page" id="per_page" class="form-select form-select-sm per-page-select js-cs" data-cs-inline onchange="this.form.submit()">
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
