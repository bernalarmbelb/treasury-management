<div class="ctc-preview-overlay" id="ctcRptPreviewOverlay">
    <div class="ctc-preview-wrap">
        <div class="ctc-preview-modal">
            <div class="ctc-preview-close-row">
                <button type="button" class="ctc-preview-close-btn" id="ctcRptPreviewCloseBtn" aria-label="Close preview">
                    <x-bx-x class="icon" />
                </button>
            </div>

            <div class="ctcp-rpt-receipt">
                <div class="ctcp-rpt-receipt-logo">
                    <p>Logo</p>
                    <p>Here</p>
                </div>

                <div class="ctcp-rpt-receipt-header">
                    <p class="ctcp-rpt-receipt-prev">
                        Previous Tax Receipt No.
                        <span class="ctcp-rpt-blank" data-rpt-preview="previous_receipt_number"></span>
                        dated
                        <span class="ctcp-rpt-blank" data-rpt-preview="previous_receipt_date"></span>
                        for the year 20<span class="ctcp-rpt-blank ctcp-rpt-blank--sm" data-rpt-preview="previous_receipt_year"></span>
                    </p>
                    <p class="ctcp-rpt-receipt-title">Official Receipt of the Republic of the Philippines</p>
                    <p class="ctcp-rpt-receipt-subtitle">Provincial or City Treasurer's Real Property Tax Receipt</p>
                </div>

                <div class="ctcp-rpt-receipt-no">
                    <span>No.&nbsp;</span><span data-rpt-preview="certificate_number"></span>&nbsp;<span class="ctcp-rpt-receipt-no-suffix" data-rpt-preview="certificate_suffix"></span>
                </div>

                <div class="ctcp-rpt-receipt-fields">
                    <div class="ctcp-rpt-receipt-field ctcp-rpt-receipt-field--municipality">
                        <label>Municipality / Province</label>
                        <span data-rpt-preview="municipality_province"></span>
                    </div>
                    <div class="ctcp-rpt-receipt-field ctcp-rpt-receipt-field--city">
                        <label>City</label>
                        <span data-rpt-preview="city"></span>
                    </div>
                    <div class="ctcp-rpt-receipt-field ctcp-rpt-receipt-field--date">
                        <label>Date</label>
                        <span data-rpt-preview="transaction_date"></span>
                    </div>
                </div>

                <div class="ctcp-rpt-receipt-body">
                    <p class="ctcp-rpt-receipt-body-text">
                        Received from
                        <span class="ctcp-rpt-blank ctcp-rpt-blank--wide" data-rpt-preview="client_name"></span>
                        the sum of
                        <span class="ctcp-rpt-blank ctcp-rpt-blank--wide" data-rpt-preview="payment_in_words"></span>
                        pesos (P
                        <span class="ctcp-rpt-blank" data-rpt-preview="amount_paid"></span>)
                        <span class="ctcp-rpt-receipt-body-flex">Philippine Currency, in full or as installment payment of REAL PROPERTY TAX for the Calendar Year 20<span class="ctcp-rpt-blank ctcp-rpt-blank--sm" data-rpt-preview="calendar_year"></span> Upon property described in the Assessment Rolls as follows:
                            <span class="ctcp-rpt-receipt-checkboxes">
                                <label>
                                    <span class="ctcp-rpt-receipt-checkbox" data-rpt-preview-checkbox="basic_tax"></span>
                                    Basic Tax
                                </label>
                                <label>
                                    <span class="ctcp-rpt-receipt-checkbox" data-rpt-preview-checkbox="special_education_fund"></span>
                                    Special Education Fund
                                </label>
                            </span>
                        </span>
                    </p>
                </div>

                <div class="ctcp-rpt-receipt-table-wrap">
                    <table class="ctcp-rpt-receipt-table">
                        <colgroup>
                            <col style="width:10.41%">
                            <col style="width:11.78%">
                            <col style="width:11.78%">
                            <col style="width:11.78%">
                            <col style="width:6.33%">
                            <col style="width:6.33%">
                            <col style="width:6.33%">
                            <col style="width:5.56%">
                            <col style="width:2.97%">
                            <col style="width:7.36%">
                            <col style="width:6.46%">
                            <col style="width:6.46%">
                            <col style="width:6.46%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th rowspan="2">Name of Declared Owner</th>
                                <th rowspan="2">Location Number and Street or Barangay</th>
                                <th rowspan="2">Lot and Block Number</th>
                                <th rowspan="2">Tax Declaration Number</th>
                                <th colspan="3">Assessed Value</th>
                                <th rowspan="2">Tax Due</th>
                                <th colspan="2">Installment</th>
                                <th rowspan="2">Full Payment</th>
                                <th rowspan="2">Penalty per Cent</th>
                                <th rowspan="2">Total</th>
                            </tr>
                            <tr>
                                <th>Land</th>
                                <th>Improv'nt</th>
                                <th>Total</th>
                                <th>No.</th>
                                <th>Payment</th>
                            </tr>
                        </thead>
                        <tbody id="ctcRptPreviewTableBody"></tbody>
                    </table>
                </div>

                <p class="ctcp-rpt-receipt-total-line">
                    Total taxes paid by Money Order, Treasury Warrant or Check No _____________ dated _________, 20 ____
                </p>
                <div class="ctcp-rpt-receipt-total-cell ctcp-rpt-receipt-total-cell--1"></div>
                <div class="ctcp-rpt-receipt-total-cell ctcp-rpt-receipt-total-cell--2"></div>
                <div class="ctcp-rpt-receipt-total-cell ctcp-rpt-receipt-total-cell--3"></div>
                <div class="ctcp-rpt-receipt-total-cell ctcp-rpt-receipt-total-cell--4"></div>
                <div class="ctcp-rpt-receipt-total-cell ctcp-rpt-receipt-total-cell--5"></div>

                <div class="ctcp-rpt-receipt-divider ctcp-rpt-receipt-divider--1"></div>
                <div class="ctcp-rpt-receipt-divider ctcp-rpt-receipt-divider--2"></div>

                <p class="ctcp-rpt-receipt-footnote">
                    <span class="ctcp-rpt-receipt-footnote-mark">*</span>Payment without penalty be made within the periods stated below if by installment
                </p>
                <p class="ctcp-rpt-receipt-installment ctcp-rpt-receipt-installment--1">1st Installment - January 1 to March 31, of the year</p>
                <p class="ctcp-rpt-receipt-installment ctcp-rpt-receipt-installment--2">2nd Installment - April 1 to June 30, of the year</p>
                <p class="ctcp-rpt-receipt-installment ctcp-rpt-receipt-installment--3">3rd Installment - July 1 to September. 30, of the year</p>
                <p class="ctcp-rpt-receipt-installment ctcp-rpt-receipt-installment--4">4th Installment - October 1 to December 31, of the year</p>

                <div class="ctcp-rpt-receipt-signature">
                    <p>Provincial or City Treasurer</p>
                    <div class="ctcp-rpt-receipt-signature-name-group">
                        <p class="ctcp-rpt-receipt-signature-name" data-rpt-preview="treasurer_deputy"></p>
                        <p>Deputy</p>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="ctc-preview-print-btn" id="ctcRptPreviewPrintBtn">Print</button>
    </div>
</div>
