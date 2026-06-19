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

<div class="um-bulk-actions" id="umBulkActions">
    <span class="um-bulk-actions-count" id="umBulkActionsCount">0 selected</span>
    <button type="button" class="um-text-link js-bulk-action" data-action="activate">Activate</button>
    <button type="button" class="um-text-link um-text-link--danger js-bulk-action" data-action="disable">Disable</button>
    <button type="button" class="um-text-link um-text-link--danger js-bulk-action" data-action="archive">Archive</button>
</div>

<div class="table-scroll-area">
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th class="um-col-checkbox">
                    <input type="checkbox" id="umSelectAllUsers" aria-label="Select all users">
                </th>
                @foreach ([
                    ['name', 'Name'],
                    ['email', 'Email'],
                    ['mobile', 'Mobile'],
                ] as [$column, $label])
                    @php [$icon, $iconClass] = $sortIcon($column); @endphp
                    <th>
                        <a href="{{ $sortLink($column) }}" class="sortable-header">
                            {{ $label }}
                            <x-dynamic-component :component="'bx-' . $icon" class="sort-icon {{ $iconClass }}" />
                        </a>
                    </th>
                @endforeach
                <th>Account Type</th>
                @foreach ([
                    ['status', 'Status'],
                    ['created_at', 'Date Added'],
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
            @forelse ($users as $user)
                @php $role = $user->primaryRole(); @endphp
                <tr>
                    <td class="um-col-checkbox">
                        <input type="checkbox" class="um-row-checkbox" value="{{ $user->id }}" aria-label="Select {{ $user->name }}">
                    </td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->mobile ?? '-' }}</td>
                    <td>{{ $role?->name ?? '-' }}</td>
                    <td>
                        @if ($user->isActivated())
                            <span class="um-status-activated">Activated</span>
                        @else
                            <span class="um-status-disabled">Disable</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('F j, Y') }}</td>
                    <td>{{ $user->added_by ?? '-' }}</td>
                    <td class="col-actions">
                        <div class="um-actions-menu">
                            <button type="button" class="um-actions-trigger" aria-label="Actions" aria-expanded="false">
                                <x-bx-dots-vertical-rounded class="icon" />
                            </button>
                            <div class="um-actions-dropdown">
                                <button type="button" class="um-actions-item um-actions-item--view js-edit-user"
                                    data-id="{{ $user->id }}"
                                    data-username="{{ $user->username }}"
                                    data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}"
                                    data-mobile="{{ $user->mobile }}"
                                    data-status="{{ $user->status }}"
                                    data-role="{{ $role?->id }}"
                                    data-role-name="{{ $role?->name }}"
                                >Edit</button>
                                <button type="button" class="um-actions-item um-actions-item--export js-reset-password"
                                    data-id="{{ $user->id }}"
                                    data-name="{{ $user->name }}"
                                    data-role-name="{{ $role?->name }}"
                                >Reset Password</button>
                                @if ($user->isActivated())
                                    <button type="button" class="um-actions-item um-actions-item--cancel js-disable-user"
                                        data-action="disable"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-role-name="{{ $role?->name }}"
                                    >Disable</button>
                                @else
                                    <button type="button" class="um-actions-item um-actions-item--unarchive js-disable-user"
                                        data-action="activate"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-role-name="{{ $role?->name }}"
                                    >Activate</button>
                                @endif
                                <button type="button" class="um-actions-item um-actions-item--archive js-disable-user"
                                    data-action="archive"
                                    data-id="{{ $user->id }}"
                                    data-name="{{ $user->name }}"
                                    data-role-name="{{ $role?->name }}"
                                >Archive</button>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="table-empty">No users found.</td>
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
