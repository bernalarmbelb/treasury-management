<div class="ctc-preview-overlay" id="ctcPreviewOverlay">
    <div class="ctc-preview-wrap">
        <div class="ctc-preview-modal">
            <div class="ctc-preview-close-row">
                <button type="button" class="ctc-preview-close-btn" id="ctcPreviewCloseBtn" aria-label="Close preview">
                    <x-bx-x class="icon" />
                </button>
            </div>
            <p class="ctc-preview-caption">BIR Form 0017 (December, 2014)</p>

            <div class="ctcp-page ctcp-page--corporation">
                <div class="ctc-field ctc-field--title" style="left:0; top:0; width:438px; height:42px;">
                    <p>Community Tax Certificate</p>
                </div>
                <div class="ctc-field ctc-badge ctc-badge--corporation" style="left:438px; top:0; width:172px; height:42px;">
                    <p>Corporation</p>
                </div>
                <div class="ctc-field ctc-cert-no" style="left:610px; top:0; width:272px; height:58px;">
                    <span class="ctc-cert-no-prefix-input" id="ctcPreviewCertPrefix"></span>
                    <span class="ctc-cert-no-input" data-preview="certificate_number"></span>
                </div>

                <div class="ctc-field ctc-input-wrap filled" style="left:0; top:42px; width:69px; height:42px;">
                    <label class="ctc-input-caption">Year</label>
                    <span class="ctcp-value" data-preview="year"></span>
                </div>
                <div class="ctc-field ctc-input-wrap filled" style="left:69px; top:42px; width:369px; height:42px;">
                    <label class="ctc-input-caption">Place of Issue (City/Mun./Prov.)</label>
                    <span class="ctcp-value" data-preview="place_of_issue"></span>
                </div>
                <div class="ctc-field ctc-input-wrap filled" style="left:438px; top:42px; width:172px; height:42px;">
                    <label class="ctc-input-caption">Date Issued</label>
                    <span class="ctcp-value" data-preview="date_issued"></span>
                </div>

                <div class="ctc-field ctc-divider" style="left:610px; top:58px; width:272px; height:5px;"></div>
                <div class="ctc-field ctc-copy-label" style="left:610px; top:63px; width:272px; height:21px;">
                    <p>Taxpayer's Copy</p>
                </div>

                <div class="ctc-field ctc-input-wrap filled" style="left:0; top:84px; width:610px; height:42px;">
                    <label class="ctc-input-caption">Company's Full Name</label>
                    <span class="ctcp-value" data-preview="company_name"></span>
                </div>
                <div class="ctc-field ctc-field--tin-compact" style="left:610px; top:84px; width:272px; height:42px;">
                    <p class="ctc-tin-label">TIN (if Any)</p>
                    <div class="ctc-tin-group">
                        @for ($group = 0; $group < 5; $group++)
                            <div class="ctc-tin-cell-group">
                                @for ($cell = 0; $cell < 3; $cell++)
                                    @php $tinIndex = $group * 3 + $cell; @endphp
                                    <div class="ctc-tin-cell ctcp-tin-cell" data-preview-tin="{{ $tinIndex }}"></div>
                                @endfor
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="ctc-field ctc-input-wrap filled" style="left:0; top:126px; width:700px; height:42px;">
                    <label class="ctc-input-caption">Address of Principal Place of Business</label>
                    <span class="ctcp-value" data-preview="address"></span>
                </div>
                <div class="ctc-field ctc-input-wrap ctc-date filled" style="left:700px; top:126px; width:182px; height:84px;">
                    <label class="ctc-input-caption ctc-caption-multiline">Date of registration<br>/ incorporation</label>
                    <span class="ctcp-value" data-preview="date_of_registration"></span>
                </div>

                <div class="ctc-field ctc-civil-row" style="left:0; top:168px; width:700px; height:42px;">
                    <p class="ctc-radio-label">Kind of<br>Organization</p>
                    <label class="ctc-radio-group">
                        <span class="ctcp-radio" data-preview-radio="kind_of_organization" data-value="Corporation"></span>
                        <p>Corporation</p>
                    </label>
                    <label class="ctc-radio-group">
                        <span class="ctcp-radio" data-preview-radio="kind_of_organization" data-value="Association"></span>
                        <p>Association</p>
                    </label>
                    <label class="ctc-radio-group">
                        <span class="ctcp-radio" data-preview-radio="kind_of_organization" data-value="Partnership"></span>
                        <p>Partnership</p>
                    </label>
                </div>

                <div class="ctc-field ctc-input-wrap filled" style="left:0; top:210px; width:610px; height:42px;">
                    <label class="ctc-input-caption">Kind / Nature of Business</label>
                    <span class="ctcp-value" data-preview="nature_of_business"></span>
                </div>
                <div class="ctc-field ctc-col-header" style="left:610px; top:210px; width:136px; height:42px;">
                    <div>
                        <p>Taxable</p>
                        <p>amount</p>
                    </div>
                </div>
                <div class="ctc-field ctc-col-header" style="left:746px; top:210px; width:136px; height:42px;">
                    <div>
                        <p>Community</p>
                        <p>Tax due</p>
                    </div>
                </div>

                <div class="ctc-field ctc-tax-row" style="left:0; top:252px; width:610px; height:42px;">
                    <p>A. Basic Community Tax (&#8369; 5.00)</p>
                </div>
                <div class="ctc-field ctc-cell-grey" style="left:610px; top:252px; width:136px; height:42px;"></div>
                <div class="ctc-field ctc-amount-cell" style="left:746px; top:252px; width:136px; height:42px;">
                    <span class="ctc-peso-prefix">&#8369;</span>
                    <span class="ctcp-amount-value" data-preview-amount="a_community_tax_due"></span>
                </div>

                <div class="ctc-field ctc-tax-row" style="left:0; top:294px; width:610px; height:42px;">
                    <p>B. Additional Community Tax ( Tax not to exceed &#8369; 10,000.00 )</p>
                </div>
                <div class="ctc-field ctc-cell-grey" style="left:610px; top:294px; width:136px; height:42px;"></div>
                <div class="ctc-field ctc-cell-grey" style="left:746px; top:294px; width:136px; height:42px;"></div>

                <div class="ctc-field ctc-tax-item" style="left:0; top:336px; width:610px; height:42px;">
                    <ol start="1">
                        <li>Assessed value of real property owned in the Philippines (&#8369;2.00 for every &#8369;5,000.00)</li>
                    </ol>
                </div>
                <div class="ctc-field ctc-amount-cell" style="left:610px; top:336px; width:136px; height:42px;">
                    <span class="ctc-peso-prefix">&#8369;</span>
                    <span class="ctcp-amount-value" data-preview-amount="item1_taxable_amount"></span>
                </div>
                <div class="ctc-field ctc-amount-cell" style="left:746px; top:336px; width:136px; height:42px;">
                    <span class="ctcp-amount-value" data-preview-amount="item1_community_tax_due"></span>
                </div>

                <div class="ctc-field ctc-tax-item" style="left:0; top:378px; width:610px; height:42px;">
                    <ol start="2">
                        <li>Gross receipts, including dividends / Earnings derived from business in the Philippines during the preceding year (&#8369;2.00 for every &#8369;5,000.00)</li>
                    </ol>
                </div>
                <div class="ctc-field ctc-amount-cell" style="left:610px; top:378px; width:136px; height:42px;">
                    <span class="ctcp-amount-value" data-preview-amount="item2_taxable_amount"></span>
                </div>
                <div class="ctc-field ctc-amount-cell" style="left:746px; top:378px; width:136px; height:42px;">
                    <span class="ctcp-amount-value" data-preview-amount="item2_community_tax_due"></span>
                </div>

                <div class="ctc-field ctc-signature-box" style="left:0; top:420px; width:610px; height:84px;">
                    <p>Signature / Position of Authorized Officer</p>
                </div>
                <div class="ctc-field ctc-cell-label" style="left:610px; top:420px; width:136px; height:42px;">
                    Total
                </div>
                <div class="ctc-field ctc-amount-cell" style="left:746px; top:420px; width:136px; height:42px;">
                    <span class="ctc-peso-prefix">&#8369;</span>
                    <span class="ctcp-amount-value" data-preview-amount="total_community_tax_due"></span>
                </div>

                <div class="ctc-field ctc-cell-label" style="left:610px; top:462px; width:136px; height:42px;">
                    Interest
                </div>
                <div class="ctc-field ctc-amount-cell" style="left:746px; top:462px; width:136px; height:42px;">
                    <span class="ctcp-amount-value" data-preview-amount="interest"></span>
                </div>

                <div class="ctc-field" style="left:0; top:504px; width:610px; height:126px;">
                    <div class="ctcp-treasurer">
                        <p class="ctc-sidebar-treasurer-name" data-preview="treasurer_name"></p>
                        <p class="ctc-sidebar-treasurer-title">Municipal Treasurer</p>
                        <div class="ctcp-treasurer-divider"></div>
                        <p class="ctc-sidebar-treasurer-title">Municipal / City Treasurer</p>
                    </div>
                </div>
                <div class="ctc-field ctc-col-header" style="left:610px; top:504px; width:136px; height:42px;">
                    <div>
                        <p>Total</p>
                        <p>Amount Paid</p>
                    </div>
                </div>
                <div class="ctc-field ctc-amount-cell" style="left:746px; top:504px; width:136px; height:42px;">
                    <span class="ctc-peso-prefix">&#8369;</span>
                    <span class="ctcp-amount-value" data-preview-amount="amount_paid"></span>
                </div>

                <div class="ctc-field" style="left:610px; top:546px; width:272px; height:84px; align-items: flex-start; padding: 10px;">
                    <p class="ctc-sidebar-words-value" data-preview="amount_in_words" style="margin: 0;"></p>
                </div>
            </div>

            <p class="ctc-preview-caption">DOP: 05.14.2021</p>
        </div>

        <button type="button" class="ctc-preview-print-btn" id="ctcPreviewPrintBtn">Print</button>
    </div>
</div>
