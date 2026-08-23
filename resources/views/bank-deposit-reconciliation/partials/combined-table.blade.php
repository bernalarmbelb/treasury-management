<div class="table-scroll-area">
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Date &amp; Time</th>
                <th>Type</th>
                <th>Reference</th>
                <th>Payee / Payor</th>
                <th>Amount</th>
                <th>Status</th>
                <th class="col-actions text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ledger as $row)
                <tr>
                    <td>{{ $row->date?->format('F j, Y · h:i A') ?? '—' }}</td>
                    <td><span class="bdr-type bdr-type--{{ strtolower($row->type) }}">{{ $row->type }}</span></td>
                    <td>{{ $row->reference }}</td>
                    <td>{{ $row->party }}</td>
                    <td style="font-variant-numeric:tabular-nums; white-space:nowrap;">₱ {{ number_format($row->amount, 2) }}</td>
                    <td><span class="bdr-status bdr-status--{{ strtolower($row->status) }}">{{ $row->status }}</span></td>
                    <td class="col-actions">
                        <div class="table-actions">
                            <a href="{{ $row->view_url }}" class="action-btn action-view" aria-label="View">View</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="table-empty">No transactions found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

<div class="pagination-bar">
    <div class="pagination-info-group">
        <p class="pagination-info">Showing {{ $ledger->firstItem() ?? 0 }} to {{ $ledger->lastItem() ?? 0 }} of {{ $ledger->total() }} entries</p>
        <form method="GET" class="per-page-form">
            @foreach (['search', 'date_start', 'date_end'] as $qp)
                @if (request($qp))<input type="hidden" name="{{ $qp }}" value="{{ request($qp) }}">@endif
            @endforeach
            <label for="per_page" class="per-page-label">Rows per page</label>
            <select name="per_page" id="per_page" class="form-select form-select-sm per-page-select js-cs" data-cs-inline onchange="this.form.submit()">
                @foreach ($perPageOptions as $option)
                    <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="pagination-controls">
        @if ($ledger->onFirstPage())
            <span class="page-btn" aria-disabled="true">Previous</span>
        @else
            <a class="page-btn" href="{{ $ledger->previousPageUrl() }}">Previous</a>
        @endif
        @foreach ($ledger->getUrlRange(1, $ledger->lastPage()) as $page => $url)
            <a class="page-btn {{ $page === $ledger->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
        @endforeach
        @if ($ledger->hasMorePages())
            <a class="page-btn" href="{{ $ledger->nextPageUrl() }}">Next</a>
        @else
            <span class="page-btn" aria-disabled="true">Next</span>
        @endif
    </div>
</div>
