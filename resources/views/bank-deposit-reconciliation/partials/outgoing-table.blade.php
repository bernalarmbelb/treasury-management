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
                    ['created_at', 'Date & Time'],
                    ['check_number', 'Cheque No.'],
                    ['pay_to_order_of', 'Pay to the Order of'],
                    [null, 'Account'],
                    ['amount', 'Amount'],
                    ['status', 'Status'],
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
            @forelse ($cheques as $cheque)
                @php $st = $cheque->status === 'Cancelled' ? 'Void' : 'Pending'; @endphp
                <tr>
                    <td>{{ $cheque->created_at->format('F j, Y · h:i A') }}</td>
                    <td>{{ $cheque->check_number }}</td>
                    <td>{{ $cheque->pay_to_order_of ?: '—' }}</td>
                    <td>{{ $cheque->bankAccount?->account_number ?? '—' }}</td>
                    <td style="font-variant-numeric:tabular-nums; white-space:nowrap;">{{ $cheque->status === 'Cancelled' && ! (float) $cheque->amount ? '—' : '₱ ' . number_format($cheque->amount, 2) }}</td>
                    <td><span class="bdr-status bdr-status--{{ strtolower($st) }}">{{ $st }}</span></td>
                    <td class="col-actions">
                        <div class="table-actions">
                            <a href="{{ route('cheque-management.view', $cheque->id) }}" class="action-btn action-view">View</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="table-empty">No outgoing cheques found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

@include('bank-deposit-reconciliation.partials.pagination', ['paginator' => $cheques])
