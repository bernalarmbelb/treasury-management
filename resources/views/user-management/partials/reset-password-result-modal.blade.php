<div class="form-batch-modal-overlay" id="resetPasswordResultModalOverlay">
    <div class="form-batch-modal um-modal-card">
        <div class="form-batch-modal-header">
            <button type="button" class="form-batch-close-btn" id="resetPasswordResultCloseBtn" aria-label="Close">
                <x-bx-x class="icon" />
            </button>
        </div>

        <h2 class="form-batch-modal-title">Password Reset for:</h2>

        <div class="orabf-export-divider"></div>

        <div class="um-modal-subject">
            <span class="um-modal-subject-name" id="resetResultSubjectName"></span>
            <span class="um-modal-subject-role" id="resetResultSubjectRole"></span>
        </div>

        <div class="um-reset-result-row">
            <div class="um-reset-result-col">
                <div class="um-reset-result-header">New Password</div>
                <div class="um-reset-result-value">
                    <input type="text" id="resetResultPassword" value="" placeholder="Enter new password" autocomplete="new-password">
                </div>
            </div>
        </div>

        <div class="um-reset-result-row">
            <div class="um-reset-result-col">
                <div class="um-reset-result-header">Send Email To</div>
                <div class="um-reset-result-value">
                    <input type="email" id="resetResultEmail" value="" readonly>
                </div>
            </div>
            <div class="um-reset-result-col">
                <div class="um-reset-result-header">Optional</div>
                <div class="um-reset-result-value">
                    <input type="text" id="resetResultMobile" placeholder="Enter mobile number">
                </div>
            </div>
        </div>

        <div class="form-batch-actions um-modal-actions">
            <button type="button" class="um-save-btn" id="resetResultSaveBtn">Save</button>
        </div>
    </div>
</div>
