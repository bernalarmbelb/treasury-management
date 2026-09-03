<x-layout>
    @php
        $tmpRoute = route('user-management.roles-permissions');
        $routeName = 'user-management.roles-permissions';
        $parentTitle = 'User Management';
        $parentRoute = route('user-management');
        $parentRouteName = 'user-management';

        $permissionFields = ['view', 'add', 'generate_report', 'print', 'export', 'request_admin_cancellation'];
        $umOnlyFields = ['reset_password', 'change_permission'];
        $fieldLabels = [
            'view' => 'View',
            'add' => 'Add',
            'generate_report' => 'Generate Report',
            'print' => 'Print',
            'export' => 'Export',
            'request_admin_cancellation' => 'Request for Admin Cancellation',
            'reset_password' => 'Reset Password',
            'change_permission' => 'Change Permission',
        ];

        $visibleRoles = $roles;
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
        <div class="um-matrix-toolbar">
            <div class="um-module-search">
                <x-bx-search class="icon" />
                <input type="text" id="umModuleSearch" placeholder="Search modules&hellip;" autocomplete="off">
            </div>
            <button type="submit" form="umPermissionsForm" class="um-save-btn">Save</button>
        </div>

        <form id="umPermissionsForm" method="POST" action="{{ route('user-management.roles-permissions.update', [], false) }}">
            @csrf

            <div class="um-matrix-card">
                <div class="um-matrix-scroll">
                    <table class="um-matrix-table">
                        <thead>
                            <tr>
                                <th class="um-matrix-col-module">Module / Permission</th>
                                @foreach ($visibleRoles as $role)
                                    <th>{{ $role->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($modules as $moduleSlug => $moduleLabel)
                                @php
                                    $isUserManagement = $moduleSlug === 'user-management';
                                    $rowFields = $isUserManagement ? array_merge($permissionFields, $umOnlyFields) : $permissionFields;
                                @endphp
                                <tr class="um-group-row" data-module="{{ $moduleSlug }}">
                                    <td class="um-matrix-col-module">
                                        <button type="button" class="um-group-toggle" aria-expanded="false">
                                            <x-bx-chevron-right class="icon um-group-chevron" />
                                            {{ $moduleLabel }}
                                        </button>
                                    </td>
                                    @foreach ($visibleRoles as $role)
                                        <td class="um-role-toggle-cell">
                                            <input type="checkbox" class="um-module-toggle" data-role="{{ $role->id }}" data-module="{{ $moduleSlug }}" aria-label="Toggle all {{ $moduleLabel }} permissions for {{ $role->name }}">
                                        </td>
                                    @endforeach
                                </tr>
                                @foreach ($rowFields as $field)
                                    <tr class="um-perm-row is-collapsed" data-module="{{ $moduleSlug }}">
                                        <td class="um-matrix-col-module">{{ $fieldLabels[$field] }}</td>
                                        @foreach ($visibleRoles as $role)
                                            @php $permission = $role->permissions->firstWhere('module', $moduleSlug); @endphp
                                            <td>
                                                <label class="um-permission-cell">
                                                    <input type="hidden" name="permissions[{{ $role->id }}][{{ $moduleSlug }}][{{ $field }}]" value="0">
                                                    <input type="checkbox" class="um-perm-checkbox" data-role="{{ $role->id }}" data-module="{{ $moduleSlug }}" name="permissions[{{ $role->id }}][{{ $moduleSlug }}][{{ $field }}]" value="1" @checked($permission?->$field)>
                                                </label>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
                const table = document.querySelector('.um-matrix-table');
                if (!table) {
                    return;
                }

                function permCheckboxesFor(role, moduleSlug) {
                    return Array.from(table.querySelectorAll('.um-perm-checkbox[data-role="' + role + '"][data-module="' + moduleSlug + '"]'));
                }

                function syncModuleToggle(role, moduleSlug) {
                    const toggle = table.querySelector('.um-module-toggle[data-role="' + role + '"][data-module="' + moduleSlug + '"]');
                    if (!toggle) {
                        return;
                    }

                    const boxes = permCheckboxesFor(role, moduleSlug);
                    const checkedCount = boxes.filter(function (box) { return box.checked; }).length;

                    toggle.checked = boxes.length > 0 && checkedCount === boxes.length;
                    toggle.indeterminate = checkedCount > 0 && checkedCount < boxes.length;
                }

                table.querySelectorAll('.um-module-toggle').forEach(function (toggle) {
                    const role = toggle.dataset.role;
                    const moduleSlug = toggle.dataset.module;

                    toggle.addEventListener('change', function () {
                        permCheckboxesFor(role, moduleSlug).forEach(function (box) {
                            box.checked = toggle.checked;
                        });
                    });

                    syncModuleToggle(role, moduleSlug);
                });

                table.querySelectorAll('.um-perm-checkbox').forEach(function (box) {
                    box.addEventListener('change', function () {
                        syncModuleToggle(box.dataset.role, box.dataset.module);
                    });
                });

                table.querySelectorAll('.um-group-toggle').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const row = button.closest('.um-group-row');
                        const moduleSlug = row.dataset.module;
                        const opening = !row.classList.contains('is-open');

                        row.classList.toggle('is-open', opening);
                        button.setAttribute('aria-expanded', opening ? 'true' : 'false');

                        table.querySelectorAll('.um-perm-row[data-module="' + moduleSlug + '"]').forEach(function (permRow) {
                            permRow.classList.toggle('is-collapsed', !opening);
                        });
                    });
                });

                const searchInput = document.getElementById('umModuleSearch');
                if (searchInput) {
                    searchInput.addEventListener('input', function () {
                        const query = searchInput.value.trim().toLowerCase();

                        table.querySelectorAll('.um-group-row').forEach(function (row) {
                            const moduleSlug = row.dataset.module;
                            const label = row.querySelector('.um-group-toggle').textContent.trim().toLowerCase();
                            const matches = !query || label.includes(query);
                            const permRows = table.querySelectorAll('.um-perm-row[data-module="' + moduleSlug + '"]');
                            const toggleButton = row.querySelector('.um-group-toggle');

                            row.classList.toggle('is-hidden', !matches);

                            if (query && matches) {
                                row.classList.add('is-open');
                                toggleButton.setAttribute('aria-expanded', 'true');
                                permRows.forEach(function (permRow) {
                                    permRow.classList.remove('is-collapsed');
                                    permRow.classList.remove('is-hidden');
                                });
                            } else if (!query) {
                                row.classList.remove('is-open');
                                toggleButton.setAttribute('aria-expanded', 'false');
                                permRows.forEach(function (permRow) {
                                    permRow.classList.add('is-collapsed');
                                    permRow.classList.remove('is-hidden');
                                });
                            } else {
                                permRows.forEach(function (permRow) {
                                    permRow.classList.add('is-hidden');
                                });
                            }
                        });
                    });
                }

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
