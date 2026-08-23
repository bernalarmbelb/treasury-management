@php
    $sortLink = function (string $column) use ($sort, $direction) {
        $newDirection = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $newDirection, 'page' => null]);
    };
    $sortIcon = function (string $column) use ($sort, $direction) {
        if ($sort !== $column) return ['sort-alt-2', ''];
        return $direction === 'asc' ? ['sort-up', 'active'] : ['sort-down', 'active'];
    };
@endphp

<div class="table-scroll-area">
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                @foreach ([
                    ['transacted_at', 'Date & Time'],
                    ['payee', 'Payee'],
                    ['form_type', 'Form'],
                    ['payment_method', 'Payment Method'],
                    ['amount', 'Amount'],
                    [null, 'Status'],
                ] as [$column, $label])
                    @if ($column === null)
                        <th>{{ $label }}</th>
                    @else
                        @php [$icon, $iconClass] = $sortIcon($column); @endphp
                        <th><a href="{{ $sortLink($column) }}" class="sortable-header">{{ $label }} <x-dynamic-component :component="'bx-' . $icon" class="sort-icon {{ $iconClass }}" /></a></th>
                    @endif
                @endforeach
                <th class="col-actions text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                @php $st = $log->status === 'Cancelled' ? 'Void' : ucfirst($log->recon_status ?: 'pending'); @endphp
                <tr>
                    <td>{{ $log->transacted_at->format('F j, Y · h:i A') }}</td>
                    <td>{{ $log->payee }}</td>
                    <td>{{ \App\Models\TransactionLog::formName($log->form_type) }}</td>
                    <td>{{ $log->payment_method ? ucwords(str_replace('_', ' ', $log->payment_method)) : '—' }}</td>
                    <td style="font-variant-numeric:tabular-nums; white-space:nowrap;">₱ {{ number_format((float) $log->amount, 2) }}</td>
                    <td><span class="bdr-status bdr-status--{{ strtolower($st) }}">{{ $st }}</span></td>
                    <td class="col-actions">
                        <div class="table-actions">
                            <a href="{{ route('collections.view', $log->id) }}" class="action-btn action-view">View</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="table-empty">No incoming transactions found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

@include('bank-deposit-reconciliation.partials.pagination', ['paginator' => $logs])
