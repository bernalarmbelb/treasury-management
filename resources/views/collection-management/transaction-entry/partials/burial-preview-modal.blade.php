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
                    <span>Accountable Form No. 51</span><span>Revised 1993</span><span>ORIGINAL</span>
                </div>

                <div class="burial-doc-masthead">
                    <div class="burial-doc-mast-top">
                        <div class="burial-doc-mast-title">OFFICIAL RECEIPT</div>
                        <div class="burial-doc-mast-sub">OF THE REPUBLIC OF THE PHILIPPINES</div>
                    </div>
                    <div class="burial-doc-mast-bottom">
                        <div class="burial-doc-seal"><span class="burial-doc-seal-circle">SEAL</span></div>
                        <div class="burial-doc-no">N<sup>o</sup> <span class="bval" data-burial-preview="certificate_number"></span> <span class="bval" data-burial-preview="series_letter"></span></div>
                    </div>
                </div>

                <div class="burial-doc-title">CITY/MUNICIPAL BURIAL PERMIT AND<br>FEE RECEIPT</div>

                <div class="burial-doc-mr">Mr. <span class="bfill" data-burial-preview="applicant_name"></span></div>

                <div class="burial-doc-dr">
                    <div class="burial-doc-dr-label">Dr.</div>
                    <div class="burial-doc-dr-fields">
                        <div class="burial-doc-dr-row">To the City/Municipality of: <span class="bval" data-burial-preview="city_municipality"></span></div>
                        <div class="burial-doc-dr-row">Province of: <span class="bval" data-burial-preview="province"></span></div>
                    </div>
                </div>

                <div class="burial-doc-perm">
                    <span>Permission is hereby granted</span>
                    <div class="burial-doc-perm-box">
                        <div class="burial-doc-perm-opt" data-perm-value="Inter">Inter</div>
                        <div class="burial-doc-perm-opt" data-perm-value="Disinter">Disinter</div>
                        <div class="burial-doc-perm-opt" data-perm-value="Remove">Remove</div>
                    </div>
                    <span>the remains of&mdash;</span>
                </div>

                <table class="burial-doc-items">
                    <tr><td>1.</td><td>Name <span class="bfill" data-burial-preview="deceased_name"></span></td></tr>
                    <tr><td>2.</td><td>Nationality <span class="bfill" data-burial-preview="nationality"></span></td></tr>
                    <tr><td>3.</td><td>Age <span class="bfill" data-burial-preview="age"></span> years. Sex <span class="bfill" data-burial-preview="sex"></span></td></tr>
                    <tr><td>4.</td><td>Date of death <span class="bfill" data-burial-preview="date_of_death"></span></td></tr>
                    <tr><td>5.</td><td>Cause of death <span class="bfill" data-burial-preview="cause_of_death"></span></td></tr>
                    <tr><td>6.</td><td>Name of cemetery <span class="bfill" data-burial-preview="cemetery_name"></span></td></tr>
                    <tr class="burial-doc-disinter"><td></td><td>* In case of disinterment&mdash;</td></tr>
                    <tr><td>*7.</td><td>Infectious or non-infectious <span class="bfill" data-burial-preview="infectious"></span></td></tr>
                    <tr><td>*8.</td><td>Body embalmed or not embalmed <span class="bfill" data-burial-preview="embalmed"></span></td></tr>
                    <tr><td>*9.</td><td>Disposition of remains <span class="bfill" data-burial-preview="disposition"></span></td></tr>
                    <tr><td>10.</td><td>Amount of the fee per city/municipal ordinance</td></tr>
                </table>

                <div class="burial-doc-footgrid">
                    <div class="burial-doc-footgrid-row">
                        <div class="burial-doc-footgrid-cell burial-doc-footgrid-cell--l">No. <span class="bval" data-burial-preview="certificate_number"></span></div>
                        <div class="burial-doc-footgrid-cell burial-doc-footgrid-cell--r">Dated: <span class="bval" data-burial-preview="date_issued"></span></div>
                    </div>
                    <div class="burial-doc-footgrid-row">
                        <div class="burial-doc-footgrid-cell burial-doc-footgrid-cell--l">&#8369; <span class="bval" data-burial-preview="fee_amount"></span></div>
                        <div class="burial-doc-footgrid-cell burial-doc-footgrid-cell--r"></div>
                    </div>
                    <div class="burial-doc-footgrid-row">
                        <div class="burial-doc-footgrid-cell burial-doc-footgrid-cell--l">City/Municipality</div>
                        <div class="burial-doc-footgrid-cell burial-doc-footgrid-cell--r">Province</div>
                    </div>
                    <div class="burial-doc-footgrid-row">
                        <div class="burial-doc-footgrid-cell burial-doc-footgrid-cell--full">Date <span class="bval">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> , 20<span class="bval" data-burial-preview="date_issued_yy"></span></div>
                    </div>
                </div>

                <p class="burial-doc-cert">
                    <span class="burial-doc-cert-sc">I hereby certify</span> that I have this day issued this burial permit and have received the fee above stated in the amount of &#8369; <span class="bfill" data-burial-preview="fee_amount"></span>
                </p>

                <div class="burial-doc-sig">
                    <div class="burial-doc-sig-inner">
                        <div class="burial-doc-sig-city">City <span class="bfill" data-burial-preview="municipal_secretary"></span></div>
                        <div class="burial-doc-sig-title">Municipal Secretary</div>
                    </div>
                </div>
            </div>

            <div class="burial-preview-actions">
                <button type="button" class="ctc-preview-print-btn burial-print-btn" id="burialPreviewPrintBtn">Print</button>
                <button type="button" class="ctc-or2-proceed-btn" id="burialPreviewSaveBtn">Save</button>
            </div>
        </div>
    </div>
</div>
