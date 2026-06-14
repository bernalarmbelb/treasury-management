<div class="ctc-or-preview-overlay" id="ctcOrPreviewOverlay">
    <div class="ctc-or-preview-wrap">
        <div class="ctc-or-preview-modal">
            <div class="ctc-or-preview-close-row">
                <button type="button" class="ctc-or-preview-close-btn" id="ctcOrPreviewCloseBtn" aria-label="Close preview">
                    <x-bx-x class="icon" />
                </button>
            </div>

            <div class="ctc-or-col">
                <div class="ctc-or-header">
                    <div class="ctc-or-logo">
                        <p>Logo<br>Here</p>
                    </div>
                    <div class="ctc-or-header-stack">
                        <div class="ctc-or-title">
                            <p>Official Receipt of the<br>Republic of the Philippines</p>
                        </div>
                        <div class="ctc-or-serial">
                            <p class="ctc-or-serial-label">No.</p>
                            <span class="ctc-or-serial-input">{{ $certificateNumber }}</span>
                            <p class="ctc-or-serial-label">U</p>
                        </div>
                        <div class="ctcp-or-field ctcp-or-field--full">
                            <p class="ctc-or-box-label">Date Issued</p>
                            <span class="ctcp-or-value" data-or-preview="date_issued"></span>
                        </div>
                    </div>
                </div>

                <div class="ctc-or-row">
                    <div class="ctcp-or-field ctcp-or-field--agency">
                        <p class="ctc-or-box-label">Agency</p>
                        <span class="ctcp-or-value" data-or-preview="agency"></span>
                    </div>
                    <div class="ctcp-or-field ctcp-or-field--fund">
                        <p class="ctc-or-box-label">Fund</p>
                        <span class="ctcp-or-value" data-or-preview="fund"></span>
                    </div>
                </div>

                <div class="ctc-or-row">
                    <div class="ctcp-or-field ctcp-or-field--full">
                        <p class="ctc-or-box-label">Payor</p>
                        <span class="ctcp-or-value" data-or-preview="payor"></span>
                    </div>
                </div>

                <div class="ctc-or-divider"></div>

                <div class="ctc-or-table-wrap">
                    <table class="ctc-or-table">
                        <colgroup>
                            <col style="width: 54.3%;">
                            <col style="width: 22.85%;">
                            <col style="width: 22.85%;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Nature of Collection</th>
                                <th>Account Code</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody id="ctcOrPreviewTableBody"></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Total</td>
                                <td>
                                    <div class="ctc-or-total-value">
                                        <span class="ctc-or-peso">₱</span>
                                        <span data-or-preview="total"></span>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="ctc-or-box ctc-or-box--amount">
                    <p class="ctc-or-box-label">Total Amount Paid</p>
                    <div class="ctc-or-amount-value">
                        <span class="ctc-or-peso">₱</span>
                        <span data-or-preview="total_amount_paid"></span>
                    </div>
                </div>

                <div class="ctc-or-box ctc-or-box--words">
                    <p class="ctc-or-box-label">Amount in Words</p>
                    <span class="ctcp-or-value" data-or-preview="amount_in_words"></span>
                </div>

                <div class="ctc-or-payment-row">
                    <div class="ctc-or-payment-methods">
                        <label class="ctc-radio-label">
                            <span class="ctcp-or-radio" data-or-preview-checked="payment_method" data-or-preview-value="cash"></span>
                            Cash
                        </label>
                        <label class="ctc-radio-label">
                            <span class="ctcp-or-radio" data-or-preview-checked="payment_method" data-or-preview-value="check"></span>
                            Check
                        </label>
                        <label class="ctc-radio-label">
                            <span class="ctcp-or-radio" data-or-preview-checked="payment_method" data-or-preview-value="money_order"></span>
                            Money Order
                        </label>
                    </div>
                    <div class="ctc-or-drawee">
                        <div class="ctc-or-row">
                            <div class="ctcp-or-field">
                                <p class="ctc-or-box-label">Drawee Bank</p>
                                <span class="ctcp-or-value" data-or-preview="drawee_bank"></span>
                            </div>
                            <div class="ctcp-or-field">
                                <p class="ctc-or-box-label">Number</p>
                                <span class="ctcp-or-value" data-or-preview="check_number"></span>
                            </div>
                            <div class="ctcp-or-field">
                                <p class="ctc-or-box-label">Date</p>
                                <span class="ctcp-or-value" data-or-preview="check_date"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ctc-or-box ctc-or-box--signature">
                    <p class="ctc-or-box-label">Received the amount stated above</p>
                    <p class="ctc-or-signature-name">Gemma D. Ferrer</p>
                    <p class="ctc-or-signature-title">Municipal Treasurer</p>
                    <div class="ctc-or-signature-divider"></div>
                    <p class="ctc-or-signature-title">Collecting Officer</p>
                </div>

                <div class="ctc-or-box ctc-or-box--note">
                    <p class="ctc-or-note">Note: <strong>Write the number and date of this receipt on the back of check or money order received.</strong></p>
                </div>
            </div>
        </div>

        <button type="button" class="ctc-or-preview-print-btn" id="ctcOrPreviewPrintBtn">Print</button>
    </div>
</div>
