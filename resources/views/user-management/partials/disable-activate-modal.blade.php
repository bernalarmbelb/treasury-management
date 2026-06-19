<div class="form-batch-modal-overlay" id="umConfirmModalOverlay">
    <div class="form-batch-modal um-modal-card um-confirm-modal">
        <div class="form-batch-modal-header">
            <button type="button" class="form-batch-close-btn" id="umConfirmCloseBtn" aria-label="Close">
                <x-bx-x class="icon" />
            </button>
        </div>

        <h2 class="um-confirm-title">
            <span class="um-confirm-title-accent" id="umConfirmAccent"></span> User?
        </h2>

        <div class="orabf-export-divider"></div>

        <div class="um-modal-subject">
            <span class="um-modal-subject-name" id="umConfirmSubjectName"></span>
            <span class="um-modal-subject-role" id="umConfirmSubjectRole"></span>
        </div>

        <form id="umConfirmForm" method="POST" action="">
            @csrf
            <div class="um-confirm-actions">
                <button type="button" class="um-confirm-btn um-confirm-btn--no" id="umConfirmNoBtn">No</button>
                <button type="submit" class="um-confirm-btn um-confirm-btn--yes">Yes</button>
            </div>
        </form>
    </div>
</div>
