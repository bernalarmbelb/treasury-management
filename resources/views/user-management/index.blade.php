<x-layout>
    @php
        $tmpRoute = route('user-management');
        $routeName = 'user-management';
    @endphp

    <div class="x-header-container sub-nav-sticky">
        <x-header title="User Management"
            :tmpRoute="$tmpRoute"
            :routeName="$routeName"
        >
            <x-slot:actions>
                @include('user-management.partials.sub-nav', ['active' => null])
            </x-slot:actions>
        </x-header>
    </div>

    <div class="collection-content">
        <div class="collection-toolbar">
            <form class="search-group" role="search" method="GET" id="um-search-form">
                <input type="search" name="search" class="search-input" id="um-search-input" placeholder="Search User" value="{{ request('search') }}" autocomplete="off">
            </form>
        </div>

        <div id="um-users-table-container">
            @include('user-management.partials.users-table')
        </div>

        @include('user-management.partials.add-user-modal')
        @include('user-management.partials.edit-user-modal')
        @include('user-management.partials.reset-password-verify-modal')
        @include('user-management.partials.reset-password-result-modal')
        @include('user-management.partials.disable-activate-modal')
    </div>

    @push('scripts')
        <script>
            (function () {
                const container = document.getElementById('um-users-table-container');
                const searchInput = document.getElementById('um-search-input');
                const searchForm = document.getElementById('um-search-form');
                let debounceTimer;

                function formatErrors(data) {
                    if (data && data.errors) {
                        return Object.values(data.errors).flat().join('\n');
                    }

                    return (data && data.message) || 'Something went wrong. Please try again.';
                }

                function fetchAndRender(params) {
                    params.delete('page');

                    const baseUrl = searchForm.action.split('?')[0];
                    const query = params.toString();
                    const url = query ? `${baseUrl}?${query}` : baseUrl;

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then((response) => response.text())
                        .then((html) => {
                            container.innerHTML = html;
                            window.history.replaceState({}, '', url);
                        });
                }

                function reloadTable() {
                    const params = new URLSearchParams(window.location.search);

                    if (searchInput.value) {
                        params.set('search', searchInput.value);
                    } else {
                        params.delete('search');
                    }

                    fetchAndRender(params);
                }

                searchInput.addEventListener('input', function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(reloadTable, 300);
                });

                searchForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    clearTimeout(debounceTimer);
                    reloadTable();
                });

                container.addEventListener('click', function (event) {
                    const link = event.target.closest('.sortable-header');

                    if (!link) {
                        return;
                    }

                    event.preventDefault();

                    const linkUrl = new URL(link.href);
                    const params = new URLSearchParams(window.location.search);

                    params.set('sort', linkUrl.searchParams.get('sort'));
                    params.set('direction', linkUrl.searchParams.get('direction'));

                    if (searchInput.value) {
                        params.set('search', searchInput.value);
                    } else {
                        params.delete('search');
                    }

                    fetchAndRender(params);
                });

                /* ===== Bulk select & bulk actions ===== */
                function getRowCheckboxes() {
                    return Array.from(container.querySelectorAll('.um-row-checkbox'));
                }

                function updateBulkActionsBar() {
                    const rows = getRowCheckboxes();
                    const checked = rows.filter((cb) => cb.checked);
                    const bar = container.querySelector('#umBulkActions');

                    if (bar) {
                        bar.classList.toggle('show', checked.length > 0);
                        const countEl = bar.querySelector('#umBulkActionsCount');

                        if (countEl) {
                            countEl.textContent = checked.length + ' selected';
                        }
                    }

                    const selectAll = container.querySelector('#umSelectAllUsers');

                    if (selectAll) {
                        selectAll.checked = rows.length > 0 && checked.length === rows.length;
                        selectAll.indeterminate = checked.length > 0 && checked.length < rows.length;
                    }
                }

                container.addEventListener('change', function (event) {
                    if (event.target.id === 'umSelectAllUsers') {
                        getRowCheckboxes().forEach(function (cb) {
                            cb.checked = event.target.checked;
                        });
                        updateBulkActionsBar();
                        return;
                    }

                    if (event.target.classList.contains('um-row-checkbox')) {
                        updateBulkActionsBar();
                    }
                });

                container.addEventListener('click', function (event) {
                    const btn = event.target.closest('.js-bulk-action');

                    if (!btn) {
                        return;
                    }

                    event.preventDefault();

                    const ids = getRowCheckboxes().filter((cb) => cb.checked).map((cb) => cb.value);

                    if (ids.length === 0) {
                        return;
                    }

                    const action = btn.dataset.action;
                    const token = document.querySelector('#addUserForm input[name="_token"]').value;

                    Promise.all(ids.map((id) => fetch(`/user-management/users/${id}/${action}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token,
                        },
                    }))).then(reloadTable);
                });

                /* ===== Account Type: checkbox group that behaves like a single-select ===== */
                document.querySelectorAll('.um-account-type-checkbox').forEach(function (checkbox) {
                    checkbox.addEventListener('change', function () {
                        const group = this.closest('.um-checkbox-group');

                        if (this.checked) {
                            group.querySelectorAll('.um-account-type-checkbox').forEach(function (other) {
                                if (other !== checkbox) {
                                    other.checked = false;
                                }
                            });
                        } else if (!group.querySelector('.um-account-type-checkbox:checked')) {
                            this.checked = true;
                        }
                    });
                });

                /* ===== Add User modal ===== */
                const addUserModalOverlay = document.getElementById('addUserModalOverlay');
                const addUserForm = document.getElementById('addUserForm');

                function openAddUserModal() {
                    addUserForm.reset();
                    addUserModalOverlay.classList.add('open');
                }

                function closeAddUserModal() {
                    addUserModalOverlay.classList.remove('open');
                    addUserForm.reset();
                }

                document.querySelectorAll('.js-open-add-user').forEach(function (btn) {
                    btn.addEventListener('click', function (event) {
                        event.preventDefault();
                        openAddUserModal();
                    });
                });

                document.getElementById('addUserCloseBtn').addEventListener('click', closeAddUserModal);

                addUserModalOverlay.addEventListener('click', function (event) {
                    if (event.target === addUserModalOverlay) {
                        closeAddUserModal();
                    }
                });

                addUserForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const submitUrl = addUserForm.action + window.location.search;

                    fetch(submitUrl, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: new FormData(addUserForm),
                    })
                        .then((response) => {
                            if (!response.ok) {
                                return response.json().then((data) => {
                                    alert(formatErrors(data));
                                    throw new Error(data.message);
                                });
                            }

                            return response.text();
                        })
                        .then((html) => {
                            container.innerHTML = html;
                            closeAddUserModal();
                        })
                        .catch(() => {});
                });

                if (new URLSearchParams(window.location.search).get('open') === 'add') {
                    openAddUserModal();
                }

                /* ===== Edit User modal ===== */
                const editUserModalOverlay = document.getElementById('editUserModalOverlay');
                const editUserForm = document.getElementById('editUserForm');
                const editUserSubjectName = document.getElementById('editUserSubjectName');
                const editUserSubjectRole = document.getElementById('editUserSubjectRole');

                function openEditUserModal(trigger) {
                    editUserForm.action = `/user-management/users/${trigger.dataset.id}`;
                    editUserForm.elements['username'].value = trigger.dataset.username || '';
                    editUserForm.elements['name'].value = trigger.dataset.name || '';
                    editUserForm.elements['email'].value = trigger.dataset.email || '';
                    editUserForm.elements['mobile'].value = trigger.dataset.mobile || '';

                    editUserForm.querySelectorAll('input[name="status"]').forEach(function (radio) {
                        radio.checked = radio.value === trigger.dataset.status;
                    });

                    editUserForm.querySelectorAll('.um-account-type-checkbox').forEach(function (checkbox) {
                        checkbox.checked = checkbox.value === trigger.dataset.role;
                    });

                    editUserSubjectName.textContent = trigger.dataset.name;
                    editUserSubjectRole.textContent = trigger.dataset.roleName || '';

                    editUserModalOverlay.classList.add('open');
                }

                function closeEditUserModal() {
                    editUserModalOverlay.classList.remove('open');
                    editUserForm.reset();
                }

                container.addEventListener('click', function (event) {
                    const trigger = event.target.closest('.js-edit-user');

                    if (!trigger) {
                        return;
                    }

                    event.preventDefault();
                    openEditUserModal(trigger);
                });

                document.getElementById('editUserCloseBtn').addEventListener('click', closeEditUserModal);

                editUserModalOverlay.addEventListener('click', function (event) {
                    if (event.target === editUserModalOverlay) {
                        closeEditUserModal();
                    }
                });

                editUserForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const submitUrl = editUserForm.action + window.location.search;

                    fetch(submitUrl, {
                        method: 'PUT',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: new FormData(editUserForm),
                    })
                        .then((response) => {
                            if (!response.ok) {
                                return response.json().then((data) => {
                                    alert(formatErrors(data));
                                    throw new Error(data.message);
                                });
                            }

                            return response.text();
                        })
                        .then((html) => {
                            container.innerHTML = html;
                            closeEditUserModal();
                        })
                        .catch(() => {});
                });

                /* ===== Reset Password flow ===== */
                const resetVerifyModalOverlay = document.getElementById('resetPasswordVerifyModalOverlay');
                const resetVerifyForm = document.getElementById('resetPasswordVerifyForm');
                const resetVerifySubjectName = document.getElementById('resetVerifySubjectName');
                const resetVerifySubjectRole = document.getElementById('resetVerifySubjectRole');

                const resetResultModalOverlay = document.getElementById('resetPasswordResultModalOverlay');
                const resetResultSubjectName = document.getElementById('resetResultSubjectName');
                const resetResultSubjectRole = document.getElementById('resetResultSubjectRole');
                const resetResultPassword = document.getElementById('resetResultPassword');
                const resetResultEmail = document.getElementById('resetResultEmail');
                const resetResultMobile = document.getElementById('resetResultMobile');

                let resetTargetUserId = null;
                let resetTargetName = '';
                let resetTargetRole = '';

                function openResetVerifyModal(trigger) {
                    resetTargetUserId = trigger.dataset.id;
                    resetTargetName = trigger.dataset.name;
                    resetTargetRole = trigger.dataset.roleName || '';

                    resetVerifySubjectName.textContent = resetTargetName;
                    resetVerifySubjectRole.textContent = resetTargetRole;

                    resetVerifyForm.reset();
                    resetVerifyModalOverlay.classList.add('open');
                }

                function closeResetVerifyModal() {
                    resetVerifyModalOverlay.classList.remove('open');
                    resetVerifyForm.reset();
                }

                function closeResetResultModal() {
                    resetResultModalOverlay.classList.remove('open');
                }

                container.addEventListener('click', function (event) {
                    const trigger = event.target.closest('.js-reset-password');

                    if (!trigger) {
                        return;
                    }

                    event.preventDefault();
                    openResetVerifyModal(trigger);
                });

                document.getElementById('resetPasswordVerifyCloseBtn').addEventListener('click', closeResetVerifyModal);

                resetVerifyModalOverlay.addEventListener('click', function (event) {
                    if (event.target === resetVerifyModalOverlay) {
                        closeResetVerifyModal();
                    }
                });

                resetVerifyForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const formData = new FormData(resetVerifyForm);

                    fetch(`/user-management/users/${resetTargetUserId}/verify-password`, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData,
                    })
                        .then((response) => {
                            if (!response.ok) {
                                return response.json().then((data) => {
                                    alert(formatErrors(data));
                                    throw new Error(data.message);
                                });
                            }

                            return fetch(`/user-management/users/${resetTargetUserId}/reset-password`, {
                                method: 'POST',
                                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                                body: formData,
                            });
                        })
                        .then((response) => response.json())
                        .then((data) => {
                            closeResetVerifyModal();

                            resetResultSubjectName.textContent = resetTargetName;
                            resetResultSubjectRole.textContent = resetTargetRole;
                            resetResultPassword.value = data.password;
                            resetResultEmail.value = data.email;
                            resetResultMobile.value = '';

                            resetResultModalOverlay.classList.add('open');
                        })
                        .catch(() => {});
                });

                document.getElementById('resetPasswordResultCloseBtn').addEventListener('click', closeResetResultModal);
                document.getElementById('resetResultSaveBtn').addEventListener('click', closeResetResultModal);

                resetResultModalOverlay.addEventListener('click', function (event) {
                    if (event.target === resetResultModalOverlay) {
                        closeResetResultModal();
                    }
                });

                /* ===== Disable / Activate / Archive confirm modal ===== */
                const confirmModalOverlay = document.getElementById('umConfirmModalOverlay');
                const confirmForm = document.getElementById('umConfirmForm');
                const confirmAccent = document.getElementById('umConfirmAccent');
                const confirmSubjectName = document.getElementById('umConfirmSubjectName');
                const confirmSubjectRole = document.getElementById('umConfirmSubjectRole');
                const confirmActionLabels = {
                    disable: { label: 'Disable', accentClass: '' },
                    activate: { label: 'Activate', accentClass: 'um-confirm-title-accent--activate' },
                    archive: { label: 'Archive', accentClass: '' },
                };

                function openConfirmModal(trigger) {
                    const action = trigger.dataset.action;
                    const config = confirmActionLabels[action];

                    confirmAccent.textContent = config.label;
                    confirmAccent.className = 'um-confirm-title-accent ' + config.accentClass;
                    confirmSubjectName.textContent = trigger.dataset.name;
                    confirmSubjectRole.textContent = trigger.dataset.roleName || '';
                    confirmForm.action = `/user-management/users/${trigger.dataset.id}/${action}`;

                    confirmModalOverlay.classList.add('open');
                }

                function closeConfirmModal() {
                    confirmModalOverlay.classList.remove('open');
                }

                container.addEventListener('click', function (event) {
                    const trigger = event.target.closest('.js-disable-user');

                    if (!trigger) {
                        return;
                    }

                    event.preventDefault();
                    openConfirmModal(trigger);
                });

                document.getElementById('umConfirmCloseBtn').addEventListener('click', closeConfirmModal);
                document.getElementById('umConfirmNoBtn').addEventListener('click', closeConfirmModal);

                confirmModalOverlay.addEventListener('click', function (event) {
                    if (event.target === confirmModalOverlay) {
                        closeConfirmModal();
                    }
                });

                confirmForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const submitUrl = confirmForm.action + window.location.search;

                    fetch(submitUrl, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: new FormData(confirmForm),
                    })
                        .then((response) => response.text())
                        .then((html) => {
                            container.innerHTML = html;
                            closeConfirmModal();
                        })
                        .catch(() => {});
                });
            })();
        </script>
    @endpush
</x-layout>
