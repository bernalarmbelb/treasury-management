<div class="ctcp-page ctcp-page--or-rpt">
    <div class="ctcp-rpt-top">
        <div class="ctcp-rpt-header-block">
            <div class="ctcp-rpt-prev-receipt">
                Previous Tax Receipt No.
                <span data-preview="previous_receipt_number"></span>
                dated
                <span data-preview="previous_receipt_date"></span>
                for the year 20<span data-preview="previous_receipt_year"></span>
            </div>

            <p class="ctcp-rpt-title">Official Receipt of the Republic of the Philippines</p>
            <p class="ctcp-rpt-subtitle">Provincial or City Treasurer's Real Property Tax Receipt</p>
        </div>

        <div class="ctcp-rpt-no">
            <span class="ctcp-rpt-no-label">No.</span>
            <input type="text" class="ctcp-rpt-no-input" name="certificate_number" value="{{ $certificateNumber }}">
            <input type="text" class="ctcp-rpt-no-suffix-input" name="certificate_suffix" value="Z" maxlength="2">
        </div>
    </div>

    <div class="ctcp-rpt-table-wrap">
        <table class="ctcp-rpt-table">
            <colgroup>
                <col style="width:90px">
                <col style="width:91px">
                <col style="width:56px">
                <col style="width:91px">
                <col style="width:55px">
                <col style="width:55px">
                <col style="width:55px">
                <col style="width:55px">
                <col style="width:23px">
                <col style="width:55px">
                <col style="width:82px">
                <col style="width:82px">
                <col style="width:82px">
                <col style="width:70px">
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
                    <th rowspan="2">Action</th>
                </tr>
                <tr>
                    <th>Land</th>
                    <th>Improv'nt</th>
                    <th>Total</th>
                    <th>No.</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < 6; $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="ctcp-rpt-col-center">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="ctcp-rpt-col-amount">&nbsp;</td>
                        <td class="ctcp-rpt-col-amount">&nbsp;</td>
                        <td class="ctcp-rpt-col-amount">&nbsp;</td>
                        <td class="ctcp-rpt-col-amount">&nbsp;</td>
                        <td class="ctcp-rpt-col-amount">&nbsp;</td>
                        <td class="ctcp-rpt-col-amount">&nbsp;</td>
                        <td class="ctcp-rpt-col-amount">&nbsp;</td>
                        <td class="ctcp-rpt-col-amount">&nbsp;</td>
                        <td class="ctcp-rpt-col-amount">&nbsp;</td>
                        <td class="ctcp-rpt-action">
                            <button type="button" class="ctcp-rpt-action-btn ctcp-rpt-action-edit">Edit</button>
                            <span class="ctcp-rpt-action-sep">|</span>
                            <button type="button" class="ctcp-rpt-action-btn ctcp-rpt-action-remove">Remove</button>
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <div class="pagination-bar">
        <div class="pagination-info-group">
            <p class="pagination-info">Showing 1 to 6 of 6 entries</p>
        </div>
        <div class="pagination-controls">
            <span class="page-btn" aria-disabled="true">Previous</span>
            <span class="page-btn active">1</span>
            <span class="page-btn" aria-disabled="true">Next</span>
        </div>
    </div>
</div>
