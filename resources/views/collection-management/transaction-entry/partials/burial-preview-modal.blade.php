<div class="ctc-preview-overlay" id="burialPreviewOverlay">
    <div class="ctc-preview-wrap">
        <div class="ctc-preview-modal burial-preview-modal">
            <div class="ctc-preview-close-row">
                <button type="button" class="ctc-preview-close-btn" id="burialPreviewCloseBtn" aria-label="Close preview">
                    <x-bx-x class="icon" />
                </button>
            </div>

            <div class="burial-doc">
                <div class="burial-doc-meta">
                    <span>Accountable Form No. 51 · Revised 1993</span><span>ORIGINAL</span>
                </div>
                <div class="burial-doc-masthead">
                    <p class="burial-doc-or-title">OFFICIAL RECEIPT</p>
                    <p class="burial-doc-or-sub">of the Republic of the Philippines</p>
                    <p class="burial-doc-no">No. <span data-burial-preview="certificate_number"></span> <span data-burial-preview="series_letter"></span></p>
                </div>
                <div class="burial-doc-title-bar">City / Municipal Burial Permit and Fee Receipt</div>

                <p class="burial-doc-line">Mr. <b data-burial-preview="applicant_name"></b></p>
                <p class="burial-doc-line">To the City / Municipality of <b data-burial-preview="city_municipality"></b></p>
                <p class="burial-doc-line">Province of <b data-burial-preview="province"></b></p>

                <p class="burial-doc-line">Permission is hereby granted to <b data-burial-preview="permission_type"></b> the remains of —</p>

                <ol class="burial-doc-list">
                    <li>Name <b data-burial-preview="deceased_name"></b></li>
                    <li>Nationality <b data-burial-preview="nationality"></b></li>
                    <li>Age <b data-burial-preview="age"></b> years. Sex <b data-burial-preview="sex"></b></li>
                    <li>Date of death <b data-burial-preview="date_of_death"></b></li>
                    <li>Cause of death <b data-burial-preview="cause_of_death"></b></li>
                    <li>Name of cemetery <b data-burial-preview="cemetery_name"></b></li>
                </ol>
                <p class="burial-doc-note">* In case of disinterment—</p>
                <ol class="burial-doc-list" start="7">
                    <li>Infectious or non-infectious <b data-burial-preview="infectious"></b></li>
                    <li>Body embalmed or not embalmed <b data-burial-preview="embalmed"></b></li>
                    <li>Disposition of remains <b data-burial-preview="disposition"></b></li>
                    <li>Amount of the fee per city/municipal ordinance <b data-burial-preview="fee_amount"></b></li>
                </ol>

                <div class="burial-doc-fee-row">
                    <span>No. <b data-burial-preview="certificate_number"></b></span>
                    <span>Dated: <b data-burial-preview="date_issued"></b></span>
                </div>

                <p class="burial-doc-cert">
                    I hereby certify that I have this day issued this burial permit and have received the fee above stated in the amount of
                    <b data-burial-preview="fee_amount_words"></b>.
                </p>
                <div class="burial-doc-sig">
                    <b data-burial-preview="municipal_secretary"></b>
                    <span>City / Municipal Secretary / Treasurer</span>
                </div>
            </div>

            <div class="burial-preview-actions">
                <button type="button" class="ctc-preview-print-btn burial-print-btn" id="burialPreviewPrintBtn">Print</button>
                <button type="button" class="ctc-or2-proceed-btn" id="burialPreviewSaveBtn">Save</button>
            </div>
        </div>
    </div>
</div>
