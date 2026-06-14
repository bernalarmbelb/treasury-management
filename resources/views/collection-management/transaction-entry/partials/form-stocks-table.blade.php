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
                    ['qty', 'Qty.'],
                    ['form_name', 'Form Name'],
                    ['form_code', 'Form Code'],
                    ['added_date', 'Added Date'],
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
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($forms as $form)
                <tr>
                    <td class="{{ $form->qty == 0 ? 'qty-empty' : '' }}">{{ $form->qty }}</td>
                    <td>{{ $form->form_name }}</td>
                    <td>{{ $form->form_code }}</td>
                    <td>{{ $form->added_date->format('F j, Y') }}</td>
                    <td>{{ $form->added_by }}</td>
                    <td class="col-actions">
                        <div class="table-actions-entry">
                            @if ($form->qty == 0)
                                <button type="button" class="action-link js-add-receipt" aria-label="Add new receipt" data-form-stock-id="{{ $form->id }}" data-form-code="{{ $form->form_code }}">
                                    <span class="action-icon-btn action-add-receipt">
                                        <x-bx-archive class="icon" />
                                    </span>
                                    Add new receipt
                                </button>
                            @endif
                            @php
                                $addTransactionRoute = match ($form->form_code) {
                                    'BIR0016' => route('transaction-entry.individual-cedula', $form->id),
                                    'BIR0017' => route('transaction-entry.corporation-cedula', $form->id),
                                    'Form 56' => route('transaction-entry.or-rpt', $form->id),
                                    'Form 5IC' => route('transaction-entry.official-receipt', $form->id),
                                    default => '#',
                                };
                            @endphp
                            <a href="{{ $addTransactionRoute }}" class="action-link" aria-label="Add Transaction">
                                <span class="action-icon-btn action-add-transaction">
                                    <x-bx-edit-alt class="icon" />
                                </span>
                                Add Transaction
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="table-empty">No forms found.</td>
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
