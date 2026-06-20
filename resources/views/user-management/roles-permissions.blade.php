<x-layout>
    @php
        $tmpRoute = route('user-management.roles-permissions');
        $routeName = 'user-management.roles-permissions';
        $parentTitle = 'User Management';
        $parentRoute = route('user-management');
        $parentRouteName = 'user-management';

        $permissionFields = ['view', 'add', 'generate_report', 'print', 'export', 'request_admin_cancellation'];
        $umOnlyFields = ['reset_password', 'change_permission'];
    @endphp

    <div class="x-header-container sub-nav-sticky">
        <x-header title="Roles & Permission"
            :tmpRoute="$tmpRoute"
            :routeName="$routeName"
            :parentTitle="$parentTitle"
            :parentRoute="$parentRoute"
            :parentRouteName="$parentRouteName"
        >
            <x-slot:actions>
                @include('user-management.partials.sub-nav', ['active' => 'roles-permissions'])
            </x-slot:actions>
        </x-header>
    </div>

    <div class="collection-content">
        <div class="um-role-tabs">
            <a href="{{ route('user-management.roles-permissions', ['role' => 'all'], false) }}" class="um-role-tab {{ $activeRole === 'all' ? 'active' : '' }}">All</a>
            @foreach ($roles as $role)
                <a href="{{ route('user-management.roles-permissions', ['role' => $role->slug], false) }}" class="um-role-tab {{ $activeRole === $role->slug ? 'active' : '' }}">{{ $role->name }}</a>
            @endforeach
        </div>

        <form id="umPermissionsForm" method="POST" action="{{ route('user-management.roles-permissions.update', [], false) }}">
            @csrf

            @foreach ($roles as $role)
                @continue ($activeRole !== 'all' && $activeRole !== $role->slug)

                <div class="um-permission-section">
                    <table class="um-permission-table">
                        <thead>
                            <tr>
                                <th>{{ $role->name }}</th>
                                <th colspan="8">Permissions</th>
                            </tr>
                            <tr>
                                <th class="um-permission-module">Modules</th>
                                <th>View</th>
                                <th>Add</th>
                                <th>Generate Report</th>
                                <th>Print</th>
                                <th>Export</th>
                                <th>Request for Admin Cancellation</th>
                                <th>Reset Password</th>
                                <th>Change Permission</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($modules as $moduleSlug => $moduleLabel)
                                @php
                                    $permission = $role->permissions->firstWhere('module', $moduleSlug);
                                    $isUserManagement = $moduleSlug === 'user-management';
                                @endphp
                                <tr>
                                    <td class="um-permission-module">
                                        <label class="um-permission-cell um-permission-cell--module">
                                            <input type="checkbox" class="js-row-toggle">
                                            {{ $moduleLabel }}
                                        </label>
                                    </td>
                                    @foreach ($permissionFields as $field)
                                        <td>
                                            <label class="um-permission-cell">
                                                <input type="hidden" name="permissions[{{ $role->id }}][{{ $moduleSlug }}][{{ $field }}]" value="0">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][{{ $moduleSlug }}][{{ $field }}]" value="1" @checked($permission?->$field)>
                                            </label>
                                        </td>
                                    @endforeach
                                    @foreach ($umOnlyFields as $field)
                                        <td>
                                            @if ($isUserManagement)
                                                <label class="um-permission-cell">
                                                    <input type="hidden" name="permissions[{{ $role->id }}][{{ $moduleSlug }}][{{ $field }}]" value="0">
                                                    <input type="checkbox" name="permissions[{{ $role->id }}][{{ $moduleSlug }}][{{ $field }}]" value="1" @checked($permission?->$field)>
                                                </label>
                                            @else
                                                <span class="um-permission-cell um-permission-cell--disabled">&mdash;</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

            <div class="um-permission-save-row">
                <button type="submit" class="um-save-btn">Save</button>
            </div>
        </form>
    </div>

    <div class="form-batch-alert-success" id="umPermissionsSuccessAlert" role="status">
        <span class="form-batch-alert-icon">
            <x-bx-check class="icon" />
        </span>
        <div class="form-batch-alert-text">
            <p class="form-batch-alert-title">Saved!</p>
            <p class="form-batch-alert-subtitle">Permissions updated successfully.</p>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                document.querySelectorAll('.um-permission-table tbody tr').forEach(function (row) {
                    const toggle = row.querySelector('.js-row-toggle');
                    const permissionInputs = Array.from(row.querySelectorAll('input[type="checkbox"]')).filter(function (checkbox) {
                        return checkbox !== toggle;
                    });

                    function syncToggle() {
                        toggle.checked = permissionInputs.length > 0 && permissionInputs.every(function (checkbox) {
                            return checkbox.checked;
                        });
                    }

                    toggle.addEventListener('change', function () {
                        permissionInputs.forEach(function (checkbox) {
                            checkbox.checked = toggle.checked;
                        });
                    });

                    permissionInputs.forEach(function (checkbox) {
                        checkbox.addEventListener('change', syncToggle);
                    });

                    syncToggle();
                });

                const form = document.getElementById('umPermissionsForm');
                const successAlert = document.getElementById('umPermissionsSuccessAlert');
                let successAlertTimer;

                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    fetch(form.action, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: new FormData(form),
                    })
                        .then((response) => {
                            if (!response.ok) {
                                return response.json().then((data) => {
                                    showToast('Action could not be completed', data.message || 'Something went wrong. Please try again.', 'error');
                                    throw new Error(data.message);
                                });
                            }

                            successAlert.classList.add('show');
                            clearTimeout(successAlertTimer);
                            successAlertTimer = setTimeout(() => {
                                successAlert.classList.remove('show');
                            }, 3000);
                        })
                        .catch(() => {});
                });
            })();
        </script>
    @endpush
</x-layout>
