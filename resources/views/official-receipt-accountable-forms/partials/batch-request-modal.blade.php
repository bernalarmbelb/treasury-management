<div class="form-batch-modal-overlay" id="batchRequestModalOverlay">
    <div class="form-batch-modal">
        <div class="form-batch-modal-header">
            <button type="button" class="form-batch-close-btn" id="batchRequestCloseBtn" aria-label="Close">
                <x-bx-x class="icon" />
            </button>
        </div>

        <h2 class="form-batch-modal-title" id="batchRequestModalTitle">Request new batch of Form</h2>

        <form id="batchRequestForm" method="POST" action="">
            @csrf

            <div class="form-batch-field-group">
                <div class="form-batch-field-header">
                    <p>Quantity Requested</p>
                </div>
                <input type="number" name="quantity" class="form-batch-input form-batch-input-full" min="1" max="100000" placeholder="e.g. 50" required>
            </div>

            <div class="form-batch-field-group">
                <div class="form-batch-field-header">
                    <p>Note (optional)</p>
                </div>
                <textarea name="note" class="form-batch-input form-batch-input-full" rows="3" maxlength="500" placeholder="e.g. Running low on current stock"></textarea>
            </div>

            <div class="form-batch-actions">
                <button type="submit" class="form-batch-save-btn">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<div class="form-batch-alert-success" id="batchRequestSuccessAlert" role="status">
    <span class="form-batch-alert-icon">
        <x-bx-check class="icon" />
    </span>
    <div class="form-batch-alert-text">
        <p class="form-batch-alert-title">Request Submitted!</p>
        <p class="form-batch-alert-subtitle" id="batchRequestSuccessAlertSubtitle"></p>
    </div>
</div>
