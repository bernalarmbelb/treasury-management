<div class="ram-preview-overlay" id="ramPreviewOverlay">
    <div class="ram-preview-container">

        {{-- Top bar --}}
        <div class="ram-preview-topbar">
            <button type="button" class="ram-preview-close-btn" id="ramPreviewCloseBtn" aria-label="Close">
                <x-bx-x class="icon" />
                Close
            </button>
            <div class="ram-preview-topbar-actions">
                <button type="button" class="ram-preview-print-btn" id="ramPreviewPrintBtn">
                    Print
                </button>
                <button type="button" class="ctc-add-entry-btn ram-preview-export-btn" id="ramPreviewExportBtn">
                    Export Excel
                </button>
            </div>
        </div>

        {{-- Paper document --}}
        <div class="ram-report-paper" id="ramPrintArea">

            {{-- Document header --}}
            <div class="ram-report-doc-header">
                <p class="ram-report-title" id="ramTitle"></p>
                <p class="ram-report-office">Province of Sorsogon, Municipality of Prieto-Diaz</p>
                <p class="ram-report-period-label">For the period of <strong id="ramPeriod"></strong></p>
            </div>

            {{-- Accountable officer row --}}
            <div class="ram-report-officer-row">
                <div class="ram-report-officer-col ram-report-officer-col--left">
                    <span
                        class="ram-report-officer-name"
                        id="ramOfficerName"
                        contenteditable="true"
                        data-placeholder="--- None ---"
                    ></span>
                    <span class="ram-report-officer-label">Accountable Officer</span>
                </div>
                <div class="ram-report-officer-col ram-report-officer-col--right">
                    <span
                        class="ram-report-officer-name"
                        id="ramDesignation"
                        contenteditable="true"
                        data-placeholder="--- None ---"
                    ></span>
                    <span class="ram-report-officer-label">Designation</span>
                </div>
            </div>

            {{-- Sections injected by JS: one .ram-report-table-wrap per section --}}
            <div id="ramSections"></div>

        </div>{{-- /.ram-report-paper --}}

    </div>
</div>
