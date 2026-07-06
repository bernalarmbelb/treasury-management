<div class="ctc-preview-overlay" id="ctcPreviewOverlay">
    <div class="ctc-preview-wrap">
        <div class="ctc-preview-modal">
            <div class="ctc-preview-close-row">
                <button type="button" class="ctc-preview-close-btn" id="ctcPreviewCloseBtn" aria-label="Close preview">
                    <x-bx-x class="icon" />
                </button>
            </div>
            <p class="ctc-preview-caption">BIR Form 0016 (December, 2014)</p>

            <div class="ctcp-page ctcp-page--individual">
                <div class="ctc-field ctc-field--title" style="left:0; top:0; width:438px; height:42px;">
                    <p>Community Tax Certificate</p>
                </div>
                <div class="ctc-field ctc-badge" style="left:438px; top:0; width:172px; height:42px;">
                    <p>Individual</p>
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

                <div class="ctc-field ctc-input-wrap filled" style="left:0; top:84px; width:203.333px; height:42px;">
                    <label class="ctc-input-caption">Name (Surname)</label>
                    <span class="ctcp-value" data-preview="surname"></span>
                </div>
                <div class="ctc-field ctc-input-wrap filled" style="left:203.33px; top:84px; width:203.333px; height:42px;">
                    <label class="ctc-input-caption">First Name</label>
                    <span class="ctcp-value" data-preview="first_name"></span>
                </div>
                <div class="ctc-field ctc-input-wrap filled" style="left:406.67px; top:84px; width:203.333px; height:42px;">
                    <label class="ctc-input-caption">Middle Name</label>
                    <span class="ctcp-value" data-preview="middle_name"></span>
                </div>

                <div class="ctc-field" style="left:610px; top:84px; width:272px; height:55px;">
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

                <div class="ctc-field ctc-input-wrap filled" style="left:0; top:126px; width:610px; height:42px;">
                    <label class="ctc-input-caption">Date Issued</label>
                    <span class="ctcp-value" data-preview="date_issued_2"></span>
                </div>

                <div class="ctc-field ctc-sex-row" style="left:610px; top:139px; width:272px; height:29px;">
                    <p class="ctc-radio-label">Sex</p>
                    <label class="ctc-radio-group">
                        <span class="ctcp-radio" data-preview-radio="sex" data-value="Male"></span>
                        <p>Male</p>
                    </label>
                    <label class="ctc-radio-group">
                        <span class="ctcp-radio" data-preview-radio="sex" data-value="Female"></span>
                        <p>Female</p>
                    </label>
                </div>

                <div class="ctc-field ctc-input-wrap filled" style="left:0; top:168px; width:198px; height:42px;">
                    <label class="ctc-input-caption">Citizenship</label>
                    <span class="ctcp-value" data-preview="citizenship"></span>
                </div>
                <div class="ctc-field ctc-input-wrap filled" style="left:198px; top:168px; width:198px; height:42px;">
                    <label class="ctc-input-caption">ICR No. (if any)</label>
                    <span class="ctcp-value" data-preview="icr_no"></span>
                </div>
                <div class="ctc-field ctc-input-wrap filled" style="left:396px; top:168px; width:350px; height:42px;">
                    <label class="ctc-input-caption">Place of Birth</label>
                    <span class="ctcp-value" data-preview="place_of_birth"></span>
                </div>
                <div class="ctc-field ctc-input-wrap filled" style="left:746px; top:168px; width:136px; height:42px;">
                    <label class="ctc-input-caption">Height</label>
                    <span class="ctcp-value" data-preview="height"></span>
                </div>

                <div class="ctc-field ctc-civil-row" style="left:0; top:210px; width:610px; height:42px;">
                    <p class="ctc-radio-label">Civil<br>Status</p>
                    <label class="ctc-radio-group">
                        <span class="ctcp-radio" data-preview-radio="civil_status" data-value="Single"></span>
                        <p>Single</p>
                    </label>
                    <label class="ctc-radio-group">
                        <span class="ctcp-radio" data-preview-radio="civil_status" data-value="Married"></span>
                        <p>Married</p>
                    </label>
                    <label class="ctc-radio-group">
                        <span class="ctcp-radio" data-preview-radio="civil_status" data-value="Divorced"></span>
                        <p>Divorced</p>
                    </label>
                    <label class="ctc-radio-group">
                        <span class="ctcp-radio" data-preview-radio="civil_status" data-value="Widow / Widower / Legally Separated"></span>
                        <p>Widow / Widower / Legally Separated</p>
                    </label>
                </div>
                <div class="ctc-field ctc-input-wrap filled" style="left:610px; top:210px; width:136px; height:42px;">
                    <label class="ctc-input-caption">Date of Birth</label>
                    <span class="ctcp-value" data-preview="date_of_birth"></span>
                </div>
                <div class="ctc-field ctc-input-wrap filled" style="left:746px; top:210px; width:136px; height:42px;">
                    <label class="ctc-input-caption">Weight</label>
                    <span class="ctcp-value" data-preview="weight"></span>
                </div>

                <div class="ctc-field ctc-input-wrap filled" style="left:0; top:252px; width:610px; height:42px;">
                    <label class="ctc-input-caption">Profession / Occupation / Business</label>
                    <span class="ctcp-value" data-preview="profession"></span>
                </div>
                <div class="ctc-field ctc-col-header" style="left:610px; top:252px; width:136px; height:42px;">
                    <div>
                        <p>Taxable</p>
                        <p>amount</p>
                    </div>
                </div>
                <div class="ctc-field ctc-col-header" style="left:746px; top:252px; width:136px; height:42px;">
                    <div>
                        <p>Community</p>
                        <p>Tax due</p>
                    </div>
                </div>

                <div class="ctc-field ctc-tax-row" style="left:0; top:294px; width:610px; height:42px;">
                    <p>A. Basic Community Tax (P 5.00) Voluntary or Exempted (P 1.00)</p>
                </div>
                <div class="ctc-field" style="left:610px; top:294px; width:136px; height:42px;"></div>
                <div class="ctc-field ctc-amount-cell" style="left:746px; top:294px; width:136px; height:42px;">
                    <span class="ctc-peso-prefix">&#8369;</span>
                    <span class="ctcp-amount-value" data-preview-amount="a_community_tax_due"></span>
                </div>

                <div class="ctc-field ctc-tax-row" style="left:0; top:336px; width:610px; height:42px;">
                    <p>B. Additional Community Tax (Tax not to exceed P 5.00)</p>
                </div>
                <div class="ctc-field" style="left:610px; top:336px; width:136px; height:42px;"></div>
                <div class="ctc-field" style="left:746px; top:336px; width:136px; height:42px;"></div>

                <div class="ctc-field ctc-tax-item" style="left:0; top:378px; width:610px; height:42px;">
                    <ol start="1">
                        <li>Gross receipts or earnings derived from business during the preceding year (P 1.00 for every P 1,000.00)</li>
                    </ol>
                </div>
                <div class="ctc-field ctc-amount-cell" style="left:610px; top:378px; width:136px; height:42px;">
                    <span class="ctc-peso-prefix">&#8369;</span>
                    <span class="ctcp-amount-value" data-preview-amount="item1_taxable_amount"></span>
                </div>
                <div class="ctc-field ctc-amount-cell" style="left:746px; top:378px; width:136px; height:42px;">
                    <span class="ctcp-amount-value" data-preview-amount="item1_community_tax_due"></span>
                </div>

                <div class="ctc-field ctc-tax-item" style="left:0; top:420px; width:610px; height:42px;">
                    <ol start="2">
                        <li>Salaries or gross receipt or earnings derived from exercise of profession or pursuit of any occupation (P 100.00 for every P 1,000.00)</li>
                    </ol>
                </div>
                <div class="ctc-field ctc-amount-cell" style="left:610px; top:420px; width:136px; height:42px;">
                    <span class="ctcp-amount-value" data-preview-amount="item2_taxable_amount"></span>
                </div>
                <div class="ctc-field ctc-amount-cell" style="left:746px; top:420px; width:136px; height:42px;">
                    <span class="ctcp-amount-value" data-preview-amount="item2_community_tax_due"></span>
                </div>

                <div class="ctc-field ctc-tax-item" style="left:0; top:462px; width:610px; height:42px;">
                    <ol start="3">
                        <li>Income from real property (P 1.00 for every P 1,000.00)</li>
                    </ol>
                </div>
                <div class="ctc-field ctc-amount-cell" style="left:610px; top:462px; width:136px; height:42px;">
                    <span class="ctcp-amount-value" data-preview-amount="item3_taxable_amount"></span>
                </div>
                <div class="ctc-field ctc-amount-cell" style="left:746px; top:462px; width:136px; height:42px;">
                    <span class="ctcp-amount-value" data-preview-amount="item3_community_tax_due"></span>
                </div>

                <div class="ctc-field ctc-signature-box" style="left:0; top:504px; width:198px; height:168px;">
                    <p>Right Thumb Print</p>
                </div>
                <div class="ctc-field ctc-signature-box" style="left:198px; top:504px; width:412px; height:84px;">
                    <p>Taxpayer's Signature</p>
                </div>
                <div class="ctc-field ctc-cell-label" style="left:610px; top:504px; width:136px; height:42px;">
                    Total
                </div>
                <div class="ctc-field ctc-amount-cell" style="left:746px; top:504px; width:136px; height:42px;">
                    <span class="ctc-peso-prefix">&#8369;</span>
                    <span class="ctcp-amount-value" data-preview-amount="total_community_tax_due"></span>
                </div>

                <div class="ctc-field ctc-cell-label" style="left:610px; top:546px; width:136px; height:42px;">
                    Interest
                </div>
                <div class="ctc-field ctc-amount-cell" style="left:746px; top:546px; width:136px; height:42px;">
                    <span class="ctcp-amount-value" data-preview-amount="interest"></span>
                </div>

                <div class="ctc-field" style="left:198px; top:588px; width:412px; height:84px;">
                    <div class="ctcp-treasurer">
                        <p class="ctc-sidebar-treasurer-name" data-preview="treasurer_name"></p>
                        <p class="ctc-sidebar-treasurer-title">Municipal Treasurer</p>
                        <div class="ctcp-treasurer-divider"></div>
                        <p class="ctc-sidebar-treasurer-title">Municipal / City Treasurer</p>
                    </div>
                </div>
                <div class="ctc-field ctc-col-header" style="left:610px; top:588px; width:136px; height:42px;">
                    <div>
                        <p>Total</p>
                        <p>Amount Paid</p>
                    </div>
                </div>
                <div class="ctc-field ctc-amount-cell" style="left:746px; top:588px; width:136px; height:42px;">
                    <span class="ctc-peso-prefix">&#8369;</span>
                    <span class="ctcp-amount-value" data-preview-amount="amount_paid"></span>
                </div>

                <div class="ctc-field" style="left:610px; top:630px; width:272px; height:42px; align-items: flex-start; padding: 10px;">
                    <p class="ctc-sidebar-words-value" data-preview="amount_in_words" style="margin: 0;"></p>
                </div>
            </div>

            <div class="ctc-preview-footer">
                <p class="ctc-preview-caption">DOP: 05.14.2021</p>
                <button type="button" class="ctc-preview-print-btn" id="ctcPreviewPrintBtn">Print</button>
            </div>
        </div>
    </div>
</div>
