<div class="mc-preview-overlay" id="mcPreviewOverlay">
    <div class="mc-preview-card">
        <div class="mc-preview-card-header">
            <button type="button" class="mc-preview-close-btn" id="mcPreviewCloseBtn" aria-label="Close preview">
                <x-bx-x class="icon" />
            </button>
        </div>

        @include('collection-management.transaction-entry.partials.mc-document')
    </div>

    <button type="button" class="mc-preview-print-btn" id="mcPreviewPrintBtn">Print &amp; Save</button>
</div>
