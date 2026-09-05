@php
    $sortLink = function (string $column) use ($sort, $direction, $tab) {
        $newDirection = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
        return request()->fullUrlWithQuery([
            'sort'      => $column,
            'direction' => $newDirection,
            'page'      => null,
            'tab'       => $tab,
        ]);
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
                <th class="col-check">
                    <input type="checkbox" class="tbl-checkbox" id="selectAllCheckbox" title="Select all">
                </th>
                @foreach ([
                    ['name',        'Name'],
                    ['email',       'Email'],
                    ['archived_at', 'Archived At'],
                ] as [$column, $label])
                    @php [$icon, $iconClass] = $sortIcon($column); @endphp
                    <th>
                        <a href="{{ $sortLink($column) }}" class="sortable-header">
                            {{ $label }}
                            <x-dynamic-component :component="'bx-' . $icon" class="sort-icon {{ $iconClass }}" />
                        </a>
                    </th>
                @endforeach
                <th>Role</th>
                <th class="col-actions text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td class="col-check">
                        <input type="checkbox" class="tbl-checkbox row-checkbox" data-id="{{ $user->id }}">
                    </td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->archived_at?->format('F j, Y') ?? '—' }}</td>
                    <td>{{ $user->roles->first()?->name ?? '—' }}</td>
                    <td class="col-actions">
                        <div class="table-actions">
                            <a href="{{ route('archives.users.view', $user->id) }}"
                               class="action-btn action-view action-view--outline"
                               aria-label="View">
                                View
                            </a>
                            <button type="button"
                                class="action-btn action-unarchive"
                                data-id="{{ $user->id }}"
                                aria-label="Unarchive">
                                Unarchive
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="table-empty">No archived users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

<div class="pagination-bar">
    <div class="pagination-info-group">
        <p class="pagination-info">
            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries
        </p>
        <form method="GET" class="per-page-form">
            <input type="hidden" name="tab" value="{{ $tab }}">
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
        @if ($users->onFirstPage())
            <span class="page-btn" aria-disabled="true">Previous</span>
        @else
            <a class="page-btn" href="{{ $users->previousPageUrl() }}">Previous</a>
        @endif

        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
            <a class="page-btn {{ $page === $users->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
        @endforeach

        @if ($users->hasMorePages())
            <a class="page-btn" href="{{ $users->nextPageUrl() }}">Next</a>
        @else
            <span class="page-btn" aria-disabled="true">Next</span>
        @endif
    </div>
</div>
