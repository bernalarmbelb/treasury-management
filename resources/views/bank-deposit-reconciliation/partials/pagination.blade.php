<div class="pagination-bar">
    <div class="pagination-info-group">
        <p class="pagination-info">Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} entries</p>
        <form method="GET" class="per-page-form">
            @foreach (['search', 'date_start', 'date_end', 'sort', 'direction'] as $qp)
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
    <x-pagination-controls :paginator="$paginator" />
</div>
