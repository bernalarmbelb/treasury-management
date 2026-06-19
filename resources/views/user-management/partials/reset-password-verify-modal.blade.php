<div class="form-batch-modal-overlay" id="resetPasswordVerifyModalOverlay">
    <div class="form-batch-modal um-modal-card">
        <div class="form-batch-modal-header">
            <button type="button" class="form-batch-close-btn" id="resetPasswordVerifyCloseBtn" aria-label="Close">
                <x-bx-x class="icon" />
            </button>
        </div>

        <h2 class="form-batch-modal-title">User Verification</h2>

        <div class="orabf-export-divider"></div>

        <div class="um-modal-subject">
            <span class="um-modal-subject-name" id="resetVerifySubjectName"></span>
            <span class="um-modal-subject-role" id="resetVerifySubjectRole"></span>
        </div>

        <form id="resetPasswordVerifyForm" method="POST" action="">
            @csrf

            <div class="form-batch-field-group">
                <div class="um-modal-section-header">
                    <p>Please enter your password</p>
                </div>
                <input type="password" name="password" class="form-batch-input form-batch-input-full" placeholder="Password" required>
            </div>

            <div class="form-batch-actions um-modal-actions">
                <button type="submit" class="um-save-btn">Next</button>
            </div>
        </form>
    </div>
</div>
