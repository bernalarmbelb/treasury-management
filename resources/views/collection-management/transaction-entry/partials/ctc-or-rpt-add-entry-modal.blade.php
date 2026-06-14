<div class="ctc-add-entry-overlay" id="ctcAddEntryOverlay">
    <div class="ctc-add-entry-modal">
        <div class="ctc-add-entry-close-row">
            <button type="button" class="ctc-add-entry-close-btn" id="ctcAddEntryCloseBtn" aria-label="Close">
                <x-bx-x class="icon" />
            </button>
        </div>

        <input type="text" class="ctc-add-entry-field" name="entry_declared_owner" placeholder="Name of Declared Owner">
        <input type="text" class="ctc-add-entry-field" name="entry_location" placeholder="Location Number and Street or Barangay">
        <input type="text" class="ctc-add-entry-field" name="entry_lot_block_number" placeholder="Lot and Block Number">
        <input type="text" class="ctc-add-entry-field" name="entry_tax_declaration_number" placeholder="Tax Declaration Number">

        <div class="ctc-add-entry-group">
            <div class="ctc-section-header"><p>Assessed Value</p></div>
            <div class="ctc-add-entry-group-row">
                <input type="text" class="ctc-add-entry-field" name="entry_assessed_value_land" placeholder="Land">
                <input type="text" class="ctc-add-entry-field" name="entry_assessed_value_improvement" placeholder="Improvement">
                <input type="text" class="ctc-add-entry-field" name="entry_assessed_value_total" placeholder="Total" readonly>
            </div>
        </div>

        <input type="text" class="ctc-add-entry-field" name="entry_tax_due" placeholder="Tax Due">

        <div class="ctc-add-entry-group">
            <div class="ctc-section-header"><p>Installment</p></div>
            <div class="ctc-add-entry-group-row">
                <input type="text" class="ctc-add-entry-field ctc-add-entry-field--narrow" name="entry_installment_no" placeholder="No.">
                <input type="text" class="ctc-add-entry-field" name="entry_installment_payment" placeholder="Payment">
            </div>
        </div>

        <input type="text" class="ctc-add-entry-field" name="entry_full_payment" placeholder="Full Payment">
        <input type="text" class="ctc-add-entry-field" name="entry_penalty_per_cent" placeholder="Penalty per Cent">
        <input type="text" class="ctc-add-entry-field" name="entry_total" placeholder="Total">

        <div class="ctc-add-entry-actions">
            <button type="button" class="ctc-add-entry-action-btn" id="ctcAddAnotherEntryBtn">Add Another Entry</button>
            <button type="button" class="ctc-add-entry-action-btn" id="ctcAddEntrySaveBtn">Save</button>
        </div>
    </div>
</div>
