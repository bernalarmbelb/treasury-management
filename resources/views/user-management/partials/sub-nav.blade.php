@php $active = $active ?? null; @endphp

<div class="view-actions">
    <div class="view-tabs">
        <a href="{{ route('user-management', [], false) }}" class="view-tab {{ $active === null ? 'active' : '' }}">
            <svg class="view-tab-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19V6a2 2 0 0 1 2-2h5l2 2h5a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z"/></svg>
            Users
        </a>
        <a href="{{ route('user-management.logs', [], false) }}" class="view-tab {{ $active === 'logs' ? 'active' : '' }}">
            <svg class="view-tab-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
            Logs
        </a>
        <a href="{{ route('user-management.roles-permissions', [], false) }}" class="view-tab {{ $active === 'roles-permissions' ? 'active' : '' }}">
            <svg class="view-tab-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2 4 5v6c0 5 3.4 8.7 8 11 4.6-2.3 8-6 8-11V5l-8-3Z"/></svg>
            Roles &amp; Permission
        </a>
    </div>

    @if ($active === null)
        <div class="view-actions-divider"></div>
        <a href="#" class="view-btn-primary js-open-add-user">
            <svg class="view-tab-icon" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg>
            Add User
        </a>
    @elseif ($active === 'logs')
        <div class="view-actions-divider"></div>
        <a href="{{ route('user-management.logs.export', [], false) }}" class="view-btn-primary">
            <svg class="view-tab-icon" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M4 19h16"/></svg>
            Export Log
        </a>
    @endif
</div>
