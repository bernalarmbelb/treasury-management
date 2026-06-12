<x-layout>
    @php
        $tmpRoute = route('collections');
        $routeName = 'collections';

        $perPageOptions = [10, 25, 50, 100];
        $perPage = in_array((int) request('per_page'), $perPageOptions) ? (int) request('per_page') : 10;

        $transactions = \App\Models\TransactionLog::query()
            ->when(request('search'), function ($query, $search) {
                $query->where('serial_number', 'like', "%{$search}%")
                    ->orWhere('payee', 'like', "%{$search}%");
            })
            ->orderByDesc('transacted_at')
            ->paginate($perPage)
            ->withQueryString();
    @endphp

    <div class="x-header-container">
        <x-header title="Collection Management"
            :tmpRoute="$tmpRoute"
            :routeName="$routeName"
        />
        <div class="nav-sticky-wrapper">
            <div class="" style="display:flex; width: 100%">
                <button class="nav-scroll-btn nav-scroll-left" id="scrollLeft">&#8249;</button>
                <nav class="navigation-bar" id="navigationBar">
                    <p><a href="{{ route('collections') }}" class=" {{ request()->routeIs('collections') ? 'active' : '' }} "> Transaction Logs </a></p>

                    <p><a href="{{ route('transaction-entry') }}" class=" {{ request()->routeIs('transaction-entry') ? 'active' : '' }} ">Transaction Entry</a></p>
                </nav>
                <button class="nav-scroll-btn nav-scroll-right" id="scrollRight">&#8250;</button>
            </div>
        </div>
    </div>

    <div class="collection-content">
        <div class="collection-toolbar">
            <button type="button" class="filter-btn" aria-label="Filter">
                <x-bx-filter-alt class="icon" />
            </button>

            <form class="search-group" role="search" method="GET">
                <input type="search" name="search" class="search-input" placeholder="Search Transaction" value="{{ request('search') }}">
                <button type="submit" class="btn btn-light search-btn">
                    <x-bx-search class="icon" />
                    Search
                </button>
            </form>
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Serial Number</th>
                        <th>Payee</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Form Type</th>
                        <th>Status</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->serial_number }}</td>
                            <td>{{ $transaction->payee }}</td>
                            <td>{{ $transaction->transacted_at->format('F j, Y') }}</td>
                            <td>{{ $transaction->transacted_at->format('h:i:s A') }}</td>
                            <td>{{ $transaction->form_type }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($transaction->status) }}">
                                    {{ $transaction->status }}
                                </span>
                            </td>
                            <td class="col-actions">
                                <div class="table-actions">
                                    <button type="button" class="action-btn action-cancel" title="Cancel" aria-label="Cancel">
                                        <x-bx-x class="icon" />
                                    </button>
                                    <button type="button" class="action-btn action-view" title="View" aria-label="View">
                                        <x-bx-show class="icon" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-empty">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
                    <label for="per_page" class="per-page-label">Rows per page</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm per-page-select" onchange="this.form.submit()">
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
    </div>
</x-layout>
