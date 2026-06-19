@php $active = $active ?? null; @endphp

<nav class="um-subnav">
    <a href="{{ route('user-management.logs', [], false) }}" class="um-subnav-item {{ $active === 'logs' ? 'active' : '' }}">Logs</a>
    <a href="{{ route('user-management.roles-permissions', [], false) }}" class="um-subnav-item {{ $active === 'roles-permissions' ? 'active' : '' }}">Roles &amp; Permission</a>
    @if ($active === null)
        <a href="#" class="um-subnav-item js-open-add-user">Add User</a>
    @else
        <a href="{{ route('user-management', ['open' => 'add'], false) }}" class="um-subnav-item">Add User</a>
    @endif
</nav>
