# Official Receipt (Form 5IC) — Transaction Entry

This document tracks all changes made to the Official Receipt (Form 5IC)
Transaction Entry sub-page
(`resources/views/collection-management/transaction-entry/official-receipt.blade.php`
and related files: `partials/ctc-or-preview-modal.blade.php`,
`app/Models/OrTransaction.php`).

## 2026-06-14 — Add Official Receipt (Form 5IC) transaction entry page

Implemented a new Transaction Entry sub-page for the "Official Receipt" form
(`FormStock` id 6, `form_code` "Form 5IC", `form_name` "Official Receipt"),
following the Figma design for the main page
(https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=270-2126)
and the print-preview modal
(https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=456-2779).

- **Migration + model**: added `or_transactions` table
  (`database/migrations/2026_06_14_100000_create_or_transactions_table.php`)
  and `App\Models\OrTransaction` (`form_stock_id`, `certificate_number`,
  `date_issued`, `agency`, `fund`, `payor`, `items` JSON array of
  `{description, account_code, amount}`, `total`, `amount_in_words`,
  `payment_method`, `drawee_bank`, `check_number`, `check_date`). Added
  `FormStock::orTransactions()` relation.
- **Routes**: `transaction-entry.official-receipt` (GET) and
  `transaction-entry.official-receipt.store` (POST), following the same
  pattern as the OR RPT routes — certificate number generated server-side as
  `str_pad((OrTransaction::max('id') ?? 0) + 1, 7, '0', STR_PAD_LEFT)`, items
  amounts defaulted to 0, a `TransactionLog` row created on save
  (`serial_number` = "No. {certificate_number} U", `payee` = payor), and
  `FormStock.qty` decremented by 1. Wired the "Add Transaction" link for
  `Form 5IC` in `partials/form-stocks-table.blade.php`.
- **View** (`official-receipt.blade.php`): a two-column `.ctc-page--or` layout
  (451px columns, 38px gap), reusing the OR RPT module's floating-label
  (`.ctc-field`/`.ctc-input-wrap`/`.ctc-input-caption`), section, and button
  conventions, with adjacent borders overlapped via `margin-top: -1px` /
  `margin-left: -1px` to match the Figma "touching border" look:
  - **Left column**: logo placeholder box, "Official Receipt of the Republic
    of the Philippines" title, serial number badge ("No. {certificate} U"),
    Date Issued field, Agency/Fund row, Payor field, divider, and the "Nature
    of Collection" table (8 editable rows + auto-computed Total), styled like
    the Collection Management `.data-table` (`#333333` header, white
    uppercase labels, alternating row striping).
  - **Right column**: "Total Amount Paid" box (auto-computed, 24px,
    reusing the `.ctc-sidebar-amount` look), "Amount in Words" box (18px
    Aptos italic), payment-method radios (Cash/Check/Money Order, reusing
    `.ctc-radio`) alongside Drawee Bank/Number/Date fields, a signature box
    ("Gemma D. Ferrer" / "Municipal Treasurer" / "Collecting Officer"), a
    static note, and the "Proceed" button styled as `.ctc-add-entry-btn`
    (filled primary `#427AB5`), since it is the only button on the page.
- **Print-preview modal** (`partials/ctc-or-preview-modal.blade.php`,
  Figma node 456:2779): a single 451px-wide column reproducing the full
  receipt (read-only), with a red circular close (X) button
  (`.ctc-or-preview-close-btn`) and a "Print" button
  (`.ctc-or-preview-print-btn`) styled to match the OR RPT module's "Proceed"
  button (`.ctc-rpt-proceed-btn`: outline primary, `rgba(66,122,181,0.08)`
  background, `var(--primary,#427AB5)` text, Manrope 600 14px, 150x42px),
  positioned outside the modal at the bottom-right.
- **CSS** (`resources/css/app.css`): added the `.ctc-or-*` and `.ctcp-or-*`
  class set for the page layout, table, sidebar boxes, payment/drawee row,
  signature box, and preview modal.

### Verification

- Verified via Claude Preview on
  `/collections/transaction-entry/6/official-receipt`: confirmed the page
  renders with all fields/sections from the Figma design, the Nature of
  Collection table total and "Total Amount Paid" box auto-update as item
  amounts are entered, clicking "Proceed" validates the Payor field then
  opens the print-preview modal (populated from the form, including the
  table rows), and the "Print" button is 150x42px with the
  `.ctc-rpt-proceed-btn` outline-primary styling. Submitted the form
  end-to-end (AJAX POST returns `redirect: "/collections"`, page navigates to
  Collection Management). Test record was removed afterward (`OrTransaction`,
  `TransactionLog` rows, and `FormStock.qty` restored).

## 2026-06-14 — Redesign Official Receipt entry page (Figma node 930:6781)

Reworked the `official-receipt.blade.php` entry form (left as a separate
redesign on top of the 2026-06-14 "Add Official Receipt" entry above) to match
the section-bar + bordered-cell grid layout from
https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=930-6781,
per user feedback that the previous floating-label `.ctc-page--or` design was
"too messy". The print-preview modal (`ctc-or-preview-modal.blade.php`, Figma
node 456:2779) is unchanged.

- **CSS** (`resources/css/app.css`): added a new `.ctc-or2-*` class namespace
  (separate from `.ctc-or-*`/`.ctcp-or-*`, which remain in use by the
  unchanged preview modal): `.ctc-page--or2` (two 522px columns), `.ctc-or2-col`
  / `.ctc-or2-row` / `.ctc-or2-group` / `.ctc-or2-group--narrow` (flex
  containers, 8px gaps), `.ctc-or2-bar` (24px `#427AB5` section-header bar,
  white Aptos Bold 10px label), `.ctc-or2-cell` (white, `1px solid #333`
  border, 42px min-height, Obviously 10px `#686868` text/placeholder, with
  `--narrow`/`--amount`/`--readonly` variants), `.ctc-or2-checkboxes` /
  `.ctc-or2-checkbox` (18x18 `#D9D9D9` square radios styled as checkboxes,
  `appearance: none`), `.ctc-or2-note`, `.ctc-or2-actions`, and
  `.ctc-or2-proceed-btn` (`#BFBFBF` grey button, Obviously 14px). Also added
  `.ctc-or2-cell.has-error` (red border) for the Payor validation state.
- **View** (`official-receipt.blade.php`): rebuilt the form markup using the
  new classes, following the Figma layout:
  - **Left column**: Serial Number (read-only `{{ $certificateNumber }} U`)
    + Date Issued row, "Information Details" bar with Agency/Fund row and a
    full-width Payor cell, then "Input Form Details Here" bar followed by 8
    rows of Nature of Collection / Account Code / Amount cells.
  - **Right column**: Total (auto-computed, shows "₱ --" until an amount is
    entered) + Amount in Words row, Cash/Check/Money Order radio row styled as
    checkboxes, Drawee Bank/Number/Date row, "Received the amount stated
    above" / "Collecting Officer Position" read-only cells ("Gemma D.
    Ferrer" / "Municipal Treasurer"), a note cell, and the grey "Proceed"
    button (`.ctc-or2-proceed-btn`).
  - All original form field `name` attributes (`date_issued`, `agency`,
    `fund`, `payor`, `items[i][description|account_code|amount]`, `total`,
    `amount_in_words`, `payment_method`, `drawee_bank`, `check_number`,
    `check_date`) were preserved so the existing print-preview modal JS
    continues to work unchanged.
  - Simplified the page script: removed the old floating-label
    filled/empty-state toggling (no longer applicable), kept amount-input
    thousands-separator formatting and total recalculation (now writing
    "--" when the total is zero), and changed the required-field check for
    Payor to toggle `.has-error` on `.ctc-or2-cell` instead of `.ctc-field`.

### Verification

- Verified via Claude Preview on
  `/collections/transaction-entry/6/official-receipt`: the page renders per
  the Figma 930:6781 layout (Serial Number/Date Issued row, blue section
  bars, bordered input cells, 8-row Nature of Collection grid, checkboxes,
  Drawee/Received-amount rows, note, grey Proceed button).
- Entering an item amount live-updates the Total cell (e.g. "₱ 1,500.50")
  via the existing thousands-separator formatting.
- Submitting with Payor empty shows the `.has-error` red border on the Payor
  cell and does not open the preview modal; filling it in and resubmitting
  opens the unchanged print-preview modal with all fields populated
  correctly.
- Clicking "Print" in the preview saves via AJAX (`OrTransaction` created,
  `FormStock.qty` decremented), prints, and redirects. Test records were
  removed afterward (`OrTransaction` row, `TransactionLog` row, and
  `FormStock.qty` restored to 25).

## 2026-06-14 — Official Receipt entry page polish (gaps, field animation, Proceed button)

Follow-up fixes to the `.ctc-or2-*` redesign above, addressing feedback that
the "Input Form Details Here" rows had no spacing, the input fields didn't
match the OR RPT module's active-field look, and the Proceed button didn't
match OR RPT's "Add Entry" button.

- **CSS** (`resources/css/app.css`):
  - Added `.ctc-or2-rows` (`display: flex; flex-direction: column; gap: 8px`)
    and wrapped the 8 Nature-of-Collection rows in it
    (`official-receipt.blade.php`), giving the rows the same 8px vertical
    gap as the rest of the layout — previously they had none.
  - Reworked `.ctc-or2-cell` to match the OR RPT `.ctc-field`/`.ctc-input`
    active-field behavior: cells default to `#F5F1F1` (empty state), turn
    white via `.ctc-or2-cell:focus-within` / `.ctc-or2-cell.filled` once
    focused or filled, and inputs get a `1px solid #333` focus outline
    (`outline-offset: -1px`), with a `background-color` transition.
    `.ctc-or2-cell--readonly` (Serial Number, Total, signature cells) is
    forced white regardless of state.
  - Replaced `.ctc-or2-proceed-btn` styling to exactly match
    `.ctc-add-entry-btn` from OR RPT: filled `var(--primary, #427AB5)`
    background, white text, Manrope 600 14px, `gap: 10px`, hover
    `#355f8f` (previously a grey `#BFBFBF`/Obviously-font button).
- **View** (`official-receipt.blade.php`): added a small script block that
  toggles a `.filled` class on each `.ctc-or2-cell` based on whether its
  input has a value (mirroring the OR RPT `is-empty`/`filled` toggle), run on
  load and on every `input` event so pre-filled fields (e.g. Date Issued's
  default value) start in the "active" white state.

### Verification

- Verified via Claude Preview on `/collections/transaction-entry/6/official-receipt`:
  the 8 Nature-of-Collection rows now have visible 8px gaps between them,
  matching the Figma reference.
- Empty cells render with the `#F5F1F1` grey background; focusing/filling a
  cell (e.g. typing into an amount field) turns it white with a `1px solid
  #333` focus outline, matching the OR RPT field animation. The Date Issued
  cell (pre-filled with today's date) loads already in the white "filled"
  state.
- Typing `1234567.89` into an item amount field formats live to
  "1,234,567.89" and the Total cell updates to "₱ 1,234,567.89" with correct
  thousands separators.
- The "Proceed" button now renders identically to OR RPT's "Add Entry" button
  (filled blue `#427AB5`, white Manrope 600 14px text, `#355f8f` on hover).

## 2026-06-14 — Official Receipt floating-label fields + editable Serial Number

Reworked the Agency, Fund, Payor, and Nature of Collection/Account
Code/Amount input cells in `official-receipt.blade.php` to use a floating
caption pattern with three visual states, and made the Serial Number field
editable.

- **CSS** (`resources/css/app.css`): rebuilt `.ctc-or2-cell` as a
  `position: relative` container with an absolutely-positioned `<input>`
  (`inset: 0`) and three states:
  - **Empty/active** (default): `#F5F1F1` background, caption
    (`.ctc-or2-caption`) centered vertically in `var(--primary, #427AB5)`
    blue, uppercase 10px.
  - **Hover/selected** (`:focus-within`): `#fff` background, caption floats
    to the top-left at 8px and turns `#686868` grey, input gets a
    `1px solid #333` focus outline.
  - **Filled** (`.filled`, not focused): `#EDF3F9` light-blue background,
    caption stays floated/grey, input text becomes bold (`font-weight: 700`).
  - Added `.ctc-or2-cell--captioned` (extra top padding for the floated
    caption) and `.ctc-or2-caption`. `.ctc-or2-cell--readonly` cells
    (Total, signature boxes) keep their flex layout and stay white.
- **View** (`official-receipt.blade.php`):
  - Converted the Serial Number cell from a static read-only `<div>` to an
    `<input name="certificate_number" value="{{ $certificateNumber }} U">`,
    making it editable.
  - Added `.ctc-or2-caption` `<label>`s for Agency, Fund, Payor, and each
    item row's Nature of Collection / Account Code / Amount fields (with
    per-row unique ids `or-item-{i}-description|account-code|amount`),
    changing their placeholders to `" "` since the floating caption now
    carries the label.
  - The existing `.filled`-toggling script (added in the prior polish pass)
    now drives all three states for these fields.

### Verification

- Verified via Claude Preview on `/collections/transaction-entry/6/official-receipt`:
  empty captioned cells show centered blue uppercase labels (e.g. "AGENCY",
  "PAYOR", "NATURE OF COLLECTION") on `#F5F1F1`.
- Focusing an empty cell (e.g. an Account Code cell) turns it white, floats
  the caption to the top-left in grey, and shows the `1px solid #333` focus
  outline with a blinking cursor.
- Typing "Armbel Bernal" into Payor and blurring shows the floated grey
  "PAYOR" caption with bold "Armbel Bernal" text on an `#EDF3F9` light-blue
  background.
- Serial Number and Date Issued (pre-filled) load directly in the filled
  `#EDF3F9`/bold state; Serial Number's input is editable.
