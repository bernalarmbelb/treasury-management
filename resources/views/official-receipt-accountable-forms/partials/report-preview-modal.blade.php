<div class="orabf-preview-overlay" id="reportPreviewOverlay">
    <div class="orabf-preview-container">

        {{-- Top bar --}}
        <div class="orabf-preview-topbar">
            <button type="button" class="orabf-preview-close-btn" id="reportPreviewCloseBtn" aria-label="Close">
                <x-bx-x class="icon" />
                Close
            </button>
            <div class="orabf-preview-topbar-actions">
                <button type="button" class="orabf-preview-print-btn" id="reportPreviewPrintBtn">
                    Print
                </button>
                <button type="button" class="ctc-add-entry-btn orabf-preview-export-btn" id="reportPreviewExportBtn">
                    Export CSV
                </button>
            </div>
        </div>

        {{-- Paper document --}}
        <div class="orabf-report-paper" id="reportPrintArea">

            {{-- Document header --}}
            <div class="orabf-report-doc-header">
                <p class="orabf-report-title">Summary Report <span id="rpFormName"></span></p>
                <p class="orabf-report-office">Provincial or City Treasurer's Office</p>
                <p class="orabf-report-period-label">Month of <strong id="rpPeriod"></strong></p>
            </div>

            {{-- Accountable officer row --}}
            <div class="orabf-report-officer-row">
                <div class="orabf-report-officer-col orabf-report-officer-col--left">
                    <span
                        class="orabf-report-officer-name"
                        id="rpOfficerName"
                        contenteditable="true"
                        data-placeholder="--- None ---"
                    ></span>
                    <span class="orabf-report-officer-label">Accountable Officer</span>
                </div>
                <div class="orabf-report-officer-col orabf-report-officer-col--center">
                    <span
                        class="orabf-report-officer-name"
                        id="rpDesignation"
                        contenteditable="true"
                        data-placeholder="--- None ---"
                    ></span>
                    <span class="orabf-report-officer-label">Designation</span>
                </div>
                <div class="orabf-report-officer-col orabf-report-officer-col--right">
                    <span class="orabf-report-officer-name">Municipality of Prieto-Diaz</span>
                    <span class="orabf-report-officer-label">Province of Sorsogon</span>
                </div>
            </div>

            {{-- Table --}}
            <div class="orabf-report-table-wrap">
                <table class="orabf-report-table">
                    <thead>
                        <tr>
                            <th>Starting OR<br>Serial Number</th>
                            <th>Ending OR<br>Serial Number</th>
                            <th>Initial<br>Quantity</th>
                            <th>Used<br>Forms</th>
                            <th>Remaining<br>Forms</th>
                            <th>Added Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Added By</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="reportPreviewTableBody">
                        <tr id="reportPreviewEmptyRow">
                            <td colspan="10" class="orabf-report-empty">No records found for the selected period.</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="orabf-report-total-row">
                            <td colspan="4" class="orabf-report-total-label">Total Unused Forms</td>
                            <td class="orabf-report-total-value rp-num" id="rpTotalRemaining">0</td>
                            <td colspan="5"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>{{-- /.orabf-report-paper --}}

    </div>
</div>
