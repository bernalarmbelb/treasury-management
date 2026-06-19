<div class="form-batch-modal-overlay" id="editUserModalOverlay">
    <div class="form-batch-modal um-modal-card">
        <div class="form-batch-modal-header">
            <button type="button" class="form-batch-close-btn" id="editUserCloseBtn" aria-label="Close">
                <x-bx-x class="icon" />
            </button>
        </div>

        <h2 class="form-batch-modal-title">Edit user</h2>

        <div class="orabf-export-divider"></div>

        <div class="um-modal-subject">
            <span class="um-modal-subject-name" id="editUserSubjectName"></span>
            <span class="um-modal-subject-role" id="editUserSubjectRole"></span>
        </div>

        <form id="editUserForm" method="POST" action="">
            @csrf

            <div class="form-batch-field-group">
                <div class="um-modal-section-header">
                    <p>Account Status</p>
                </div>
                <div class="um-field-box">
                    <div class="um-radio-group">
                        <label class="um-radio-option">
                            <input type="radio" name="status" value="activated">
                            Active
                        </label>
                        <label class="um-radio-option">
                            <input type="radio" name="status" value="disabled">
                            Deactivated
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-batch-fields-stack">
                <div class="form-batch-field-group">
                    <div class="um-modal-section-header">
                        <p>Username</p>
                    </div>
                    <input type="text" name="username" class="form-batch-input form-batch-input-full" placeholder="Username">
                </div>

                <div class="form-batch-field-group">
                    <div class="um-modal-section-header">
                        <p>Full Name</p>
                    </div>
                    <input type="text" name="name" class="form-batch-input form-batch-input-full" placeholder="Full Name" required>
                </div>

                <div class="form-batch-field-group">
                    <div class="um-modal-section-header">
                        <p>Email Address</p>
                    </div>
                    <input type="email" name="email" class="form-batch-input form-batch-input-full" placeholder="email" required>
                </div>

                <div class="form-batch-field-group">
                    <div class="um-modal-section-header">
                        <p>Mobile Number</p>
                    </div>
                    <input type="text" name="mobile" class="form-batch-input form-batch-input-full" placeholder="Mobile Number">
                </div>
            </div>

            <div class="form-batch-field-group">
                <div class="um-modal-section-header">
                    <p>Account Type</p>
                </div>
                <div class="um-field-box">
                    <div class="um-checkbox-group">
                        @foreach ($roles as $role)
                            <label class="um-checkbox-option">
                                <input type="checkbox" name="role" value="{{ $role->id }}" class="um-account-type-checkbox">
                                {{ $role->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="form-batch-actions um-modal-actions">
                <button type="submit" class="um-save-btn">Save</button>
            </div>
        </form>
    </div>
</div>
