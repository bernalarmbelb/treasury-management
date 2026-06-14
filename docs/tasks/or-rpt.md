# OR RPT (Form 56) — Transaction Entry

This document tracks all changes made to the OR RPT (Form 56) Transaction
Entry sub-page
(`resources/views/collection-management/transaction-entry/or-rpt.blade.php`
and related files: `partials/ctc-or-rpt-preview.blade.php`,
`app/Models/OrRptTransaction.php`).

## 2026-06-13 — Add OR RPT (Form 56) transaction entry page

Implemented a new Transaction Entry sub-page for the "OR RPT" form (`FormStock`
id 7, `form_code` "Form 56", `form_name` "OR RPT"), following the Figma design
(https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=283-3178).

An initial modal-based version was built following the Individual/Corporation
Cedula pattern, but the user flagged that the layout was "too far from figma
design" — the Figma reference shows a side-by-side layout (input form + an
always-visible "short paper" certificate preview) with no modal/print step.
The page was rewritten to match.

- **Migration + model**: `or_rpt_transactions` table
  (`database/migrations/2026_06_13_160000_create_or_rpt_transactions_table.php`)
  and `App\Models\OrRptTransaction`, with a `FormStock::orRptTransactions()`
  `hasMany` relation. Columns: `certificate_number`,
  `previous_receipt_number`/`previous_receipt_date`/`previous_receipt_year`,
  `municipality_province`, `city`, `transaction_date`, `client_name`,
  `payment_in_words`, `amount_paid`, `treasurer_deputy`, `basic_tax`,
  `special_education_fund`. Unlike the cedula pages, there is no
  `certificate_prefix` — the Figma input form has no certificate-number field
  at all, so `certificate_number` is generated server-side
  (`str_pad((OrRptTransaction::max('id') ?? 0) + 1, 7, '0', STR_PAD_LEFT)`) for
  both the GET preview and the POST save, and used directly as
  `TransactionLog.serial_number`.
- **View** (`resources/views/collection-management/transaction-entry/or-rpt.blade.php`):
  breadcrumb "Home | Transactions Entry | OR RPT" (no "Collections Management"
  segment, no form code suffix — only "OR RPT" in the accent span), the
  `.ctc-tabs-row` (Transactions Log / New Entry tabs), and a
  `.ctc-page--or-rpt` `<form>` laid out as a flex row containing:
  - `.ctc-rpt-form` (522px input column): "Previous Tax Receipt Number" section
    header → row (Previous Tax Receipt No. / MMMM DD / Year(YY)) → "Input Form
    Details Here" section header → row (Municipality/Province / City / Date) →
    Client's name (full width) → row (Payment in words / P amount) →
    "Provincial or City Treasurer" section header → Deputy/Staff (full width)
    → Basic Tax / Special Education Fund checkboxes → "Add Entry" button.
  - the `ctc-or-rpt-preview` partial (812×612px certificate), rendered inline
    next to the form.
- **Preview partial** (`resources/views/collection-management/transaction-entry/partials/ctc-or-rpt-preview.blade.php`,
  replacing the old `or-rpt-preview-modal.blade.php`): the `.ctcp-page--or-rpt`
  "Official Receipt of the Republic of the Philippines — Provincial or City
  Treasurer's Real Property Tax Receipt" certificate, rendered directly on the
  page (no overlay/modal). Includes the previous-receipt header, cert no.,
  logo placeholder, municipality/city/date fields, the full "Received
  from... Philippine Currency, in full or as installment payment of REAL
  PROPERTY TAX for the Calendar Year 20__ Upon property described in the
  Assessment Rolls as follows:" paragraph with Basic Tax/SEF checkboxes, a
  13-column assessment table with a 2-row header (Assessed Value split into
  Land/Improv'nt/Total, Installment split into No./Payment) and 6 empty data
  rows, two `#D9D9D9` divider lines, a totals row, installment-period
  footnotes, and a "Provincial or City Treasurer / [name] / Deputy" signature
  block. All dynamic values use `data-preview`/`data-preview-amount`/
  `data-preview-checkbox` attributes and update live on input (no separate
  "Proceed" step).
- **CSS** (`resources/css/app.css`): `.ctc-page--or-rpt` is now a flex row
  (`gap:26px`, `padding:40px`) holding `.ctc-rpt-form` (522px flex column) and
  the certificate; added `.ctc-rpt-row`/`.ctc-rpt-field`/`--narrow`/`--amount`/
  `--full` for the input layout, `.ctc-add-entry-btn` (soft-dark `#BFBFBF` bg,
  `#333` text, matching Figma's "Add Entry" button), changed
  `.ctc-section-header` to `height:17px` and made it flow (not absolute), and
  changed `.ctc-checkbox-group p` color to `#686868`. Reworked
  `.ctcp-rpt-table` for the 2-row/13-column header via `<colgroup>`, added
  `.ctcp-rpt-divider`/`--1`/`--2`, replaced `.ctcp-rpt-footnotes` with
  `.ctcp-rpt-footnote-main` + `.ctcp-rpt-installment-list` (absolute-positioned
  per Figma), and reordered `.ctcp-rpt-signature` (label, name, "Deputy").
- **Routes** (`routes/web.php`): `transaction-entry.or-rpt` (GET) passes a
  generated `certificateNumber` to the view; `transaction-entry.or-rpt.store`
  (POST) validates input (no `certificate_prefix`/`certificate_number` from the
  client), generates `certificate_number` server-side, creates an
  `OrRptTransaction`, decrements `FormStock.qty`, creates a `TransactionLog`
  (serial number = generated certificate number, payee = `client_name`,
  `form_type` "Form 56", status "Completed"), and returns a relative redirect
  to `/collections`.
- **Add Transaction link**: `'Form 56' => route('transaction-entry.or-rpt', $form->id)`
  in `partials/form-stocks-table.blade.php`'s `$addTransactionRoute` match
  (unchanged from the initial implementation).

### Verification

- Verified via Claude Preview on `/collections/transaction-entry/7/or-rpt`:
  the page renders as a side-by-side input form + certificate preview matching
  the Figma screenshot (breadcrumb, section headers, field layout, "Add Entry"
  button, 2-row assessment table, dividers, footnotes, signature block).
- Typing into "Client's name" and "P (Amount in numbers)" live-updates the
  certificate preview's "Received from"/amount fields without any extra step.
- Clicking "Add Entry" saves via AJAX, creates an `OrRptTransaction` and a
  `TransactionLog` row (form type "Form 56", status "Completed"), decrements
  `FormStock.qty`, and redirects to `/collections`. Test records were removed
  afterward (`TransactionLog`, `OrRptTransaction` rows, and `FormStock.qty`
  restored to 25).
