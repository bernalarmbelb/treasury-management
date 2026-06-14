# Collection Management — Transaction Entry

This document tracks all changes made to the Transaction Entry tab of the
Collection Management page
(`resources/views/collection-management/transaction-entry/index.blade.php`
and related files).

## 2026-06-13 — Implement Transaction Entry tab

Implemented the Transaction Entry tab per the Figma design
(https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=104-1633),
reusing the Collection Management page layout
(`docs/tasks/collection-management.md`, "Layout summary" section).

- **Sticky page header** (`.x-header-container.sub-nav-sticky`): same as
  Collection Management — "Collection Management" title + breadcrumb
  ("Home | Transactions Entry"), with the "Transaction Logs | Transaction
  Entry" tab pill, sticking below the main nav bar.
- **Toolbar** (`.collection-toolbar`): a single search form ("Search Form"
  placeholder + "Search" button), matching the Figma toolbar for this tab
  (no filter button or date filter, per the Figma design).
- **Data table** (`.table-scroll-area` / `.table-wrapper` / `.data-table`):
  sortable columns Qty., Form Name, Form Code, Added Date, Added By, with
  the same sort-direction icon treatment as the Transaction Logs table.
  Live search via AJAX (debounced ~300ms), reusing the same
  `fetchAndRender` pattern.
- **Pagination bar**: "Showing X to Y of Z entries" + "Rows per page"
  selector (10/25/50/100) + Previous/page numbers/Next, same as Transaction
  Logs.
- **Actions column**:
  - "Add Transaction" — green (`#0fa958`) rounded icon button with
    `<x-bx-edit-alt>` (matches Figma `basil:edit-outline`,
    https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=194-1690)
    + "Add Transaction" label.
  - "Add new receipt" — red (`#e84e46`) rounded icon button with
    `<x-bx-archive>` (matches Figma `vuesax/linear/archive-book`,
    https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=200-1884)
    + "Add new receipt" label. Shown alongside "Add Transaction" only for
    rows where `qty == 0` (out-of-stock forms), per the Figma design. The
    `Qty.` value is shown in red (`#f0284a`) for those rows.
  - New CSS classes in `resources/css/app.css`: `.table-actions-entry`,
    `.action-link`, `.action-icon-btn`, `.action-add-transaction`,
    `.action-add-receipt`, `.qty-empty`.

### Data

- New `form_stocks` table (migration
  `database/migrations/2026_06_13_120000_create_form_stocks_table.php`,
  model `app/Models/FormStock.php`): `qty`, `form_name`, `form_code`,
  `added_date`, `added_by`.
- Seeder `database/seeders/FormStockSeeder.php` (registered in
  `DatabaseSeeder`) with the 8 form types from the Figma design (Individual
  Cedula, Corporation Cedula, Certificate of Ownership of Large Cattle,
  Certificate of Transfer of Large Cattle, Marriage License, Official
  Receipt, OR RPT, Burial). "Official Receipt" is seeded with `qty = 0` to
  exercise the "Add new receipt" action.

### Backend

- `routes/web.php`: `/collections/transaction-entry` now queries
  `FormStock`, with `search` (matches `form_name`), `sort`/`direction`
  (whitelisted to `qty`, `form_name`, `form_code`, `added_date`,
  `added_by`; default `form_name` asc), and `per_page` (10/25/50/100),
  paginated and `withQueryString()`. AJAX requests
  (`X-Requested-With: XMLHttpRequest`) return just the table partial
  (`resources/views/collection-management/transaction-entry/partials/form-stocks-table.blade.php`).

## 2026-06-13 — Fix Actions column layout (row direction)

- `.table-actions-entry` (`resources/css/app.css`) now uses
  `flex-direction: row` and `flex-wrap: nowrap` so "Add new receipt" and
  "Add Transaction" sit side by side on out-of-stock rows, matching the
  Figma design. Previously `flex-wrap: wrap` caused the two actions to wrap
  onto separate lines because the cell's intrinsic width settled to a
  single item's width.
- Verified visually via Claude Preview against
  `http://treasury-management.test/collections/transaction-entry`.

## 2026-06-13 — "Add new receipt" → Add new batch modal

Implemented the "Add new batch of Form {code}" modal per the Figma design
(https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=353-5173),
wired to the "Add new receipt" action (shown only when `qty == 0`).

### Data

- New `form_batches` table (migration
  `database/migrations/2026_06_13_130000_create_form_batches_table.php`,
  model `app/Models/FormBatch.php`, `belongsTo` `FormStock`): `form_stock_id`,
  `registration_date`, `purchase_date`, `starting_serial_number`,
  `ending_serial_number`, `added_by`.
- `app/Models/FormStock.php`: added `batches()` `hasMany(FormBatch::class)`
  relation.

### Frontend

- New partial
  `resources/views/collection-management/transaction-entry/partials/add-batch-modal.blade.php`
  — overlay/modal markup matching the Figma spec exactly (530px modal,
  `#EDF3F9` background, "Date of Registration" / "Date of Purchase"
  Month/Day/Year rows, "Starting OR Serial Number" / "Ending OR Serial
  Number" full-width inputs, red circular close button, "Save" button).
  Included once on `transaction-entry/index.blade.php`.
- New CSS in `resources/css/app.css`: `.form-batch-modal-overlay` (+ `.open`),
  `.form-batch-modal`, `.form-batch-modal-header`, `.form-batch-close-btn`,
  `.form-batch-modal-title`, `.form-batch-field-group`,
  `.form-batch-fields-stack`, `.form-batch-field-header`,
  `.form-batch-field-row`, `.form-batch-input` (+ `.form-batch-input-full`),
  `.form-batch-actions`, `.form-batch-save-btn`, and `#formBatchForm` (flex
  column, 12px gap, matching the Figma field spacing).
- "Add new receipt" button (`form-stocks-table.blade.php`) now has a
  `js-add-receipt` class plus `data-form-stock-id` / `data-form-code`
  attributes used to open the modal and target the right form.
- JS in `index.blade.php`'s script block: delegated click on
  `#form-stocks-table-container` opens the modal, sets the title to "Add new
  batch of {form_code}" and the form's POST action; close button and
  click-outside-overlay close it; form submit posts via AJAX (preserving the
  current search/sort/page query string) and replaces the table partial with
  the response, then closes the modal.

### Backend

- New route `POST /collections/transaction-entry/{formStock}/batches`
  (`transaction-entry.batches.store`): validates registration/purchase
  date parts and starting/ending OR serial numbers, creates a `FormBatch`
  record, computes the batch quantity from the last 3 digits of the
  starting/ending serial numbers (`ending - starting + 1`), adds it to
  `form_stocks.qty`, updates `added_date` to the purchase date, and returns
  the refreshed `form-stocks-table` partial.

### Verification

- Verified via Claude Preview: opened the modal for "Official Receipt" (Form
  5IC, qty = 0), confirmed it matches the Figma screenshot (field headers,
  spacing, colors, "Save" button), submitted a batch
  (registration 6/10/2026, purchase 6/12/2026, serial `OR-2026-001`–`025`),
  and confirmed the table updates: qty 0 → 25, Added Date → June 12, 2026,
  and the row now shows only "Add Transaction" (no longer qty = 0).

## 2026-06-13 — Add new batch modal polish (borders, date fields, success alert)

- Fixed `.form-batch-input-full` (Starting/Ending OR Serial Number inputs)
  missing its left border — `border-left-width: 0` from `.form-batch-input`
  was only restored for `.form-batch-field-row .form-batch-input:first-child`,
  which these full-width inputs aren't part of. Added an explicit
  `border-left-width: 1px` override so all input borders are uniform.
- Added `.form-batch-input:focus { outline: 1px solid #333; outline-offset:
  -1px }` so focused fields get a thin border highlight instead of the
  browser's default (thicker) focus ring.
- "Month" fields (registration/purchase) are now `<select>` dropdowns
  (January–December), styled via `select.form-batch-input`. "Day" and
  "Year" inputs default to the current day/year (`now()->day` /
  `now()->year`) but remain editable, and the month select defaults to the
  current month.
- Added a "Successfully Added!" toast matching the Figma alert
  (https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=353-5238):
  `#formBatchSuccessAlert` (`.form-batch-alert-success` + `.show`), fixed
  top-right, green `rgba(210,249,229,0.85)` background, check icon
  (`<x-bx-check>`), "Successfully Added!" + "Form {code}" subtitle. Shown for
  3s after a successful batch save.
- Verified via Claude Preview: borders now uniform on all four input rows,
  Month renders as a dropdown defaulting to June 2026, Day/Year prefilled
  with the current date, and the success toast appears with "Form 5IC" after
  saving.

## 2026-06-13 — Individual Cedula "Add Transaction" page (Community Tax Certificate)

Implemented the "Add Transaction" destination for the Individual Cedula
(`BIR0016`) row per the Figma design
(https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=116-2258).

### Frontend

- New view
  `resources/views/collection-management/transaction-entry/individual-cedula.blade.php`
  — full-page "Community Tax Certificate" entry form, reusing `<x-layout>`
  for the shared header/main nav.
  - Page header (`x-header-container sub-nav-sticky`): "COLLECTION
    MANAGEMENT" title with breadcrumb "Home | Transactions Entry |
    Individual Cedula {form_code}" (last segment in red `#E84E46` via new
    `.page-links-accent` class), plus a "Transactions Log | New Entry" tab
    pair (`.ctc-tabs-row` / `.ctc-tab` / `.ctc-tab.active`) matching the
    Figma tab styling (`#406AAF` background, active tab has a 5px `#333`
    bottom border).
  - Form body (`.ctc-page`, 1412×668px, absolutely positioned `.ctc-field`
    cells matching the exact Figma coordinates): "COMMUNITY TAX CERTIFICATE"
    title, "INDIVIDUAL" badge, certificate number ("CCI2022 13476955") +
    "TAXPAYER'S COPY", Year / Place of Issue / Date Issued, Name (Surname /
    First / Middle), TIN 12-digit box grid, second Date Issued row, Sex
    radios (Male/Female), Citizenship / ICR No. / Place of Birth / Height,
    Civil Status radios (Single/Married/Divorced/Widow-Widower-Legally
    Separated), Weight / Date of Birth, Profession/Occupation/Business,
    Taxable Amount / Community Tax Due columns with the A./B. community tax
    rows (1-3 itemized list), Right Thumb Print / Taxpayer's Signature
    boxes, and Total/Interest rows.
  - Right sidebar: "TOTAL AMOUNT PAID ₱500.00", "AMOUNT IN WORDS - Five
    hundred pesos only", "Municipal/City Treasurer - GEMMA D. FERRER /
    Municipal Treasurer", and a "Proceed" button (`.ctc-proceed-btn`,
    `#BFBFBF` soft-dark style matching the existing "Save" button
    convention).
  - New CSS in `resources/css/app.css`: `.ctc-tabs-row`, `.ctc-tab` (+
    `.active`), `.page-links-accent`, `.ctc-page`, `.ctc-field` (+
    variants `--title`, `ctc-badge`, `ctc-cert-no`, `ctc-divider`,
    `ctc-copy-label`, `ctc-tax-row`, `ctc-tax-item`, `ctc-col-header`,
    `ctc-cell-grey`, `ctc-cell-peso`, `ctc-cell-label`, `ctc-signature-box`,
    `ctc-sex-row`, `ctc-civil-row`, `ctc-radio`, `ctc-radio-group`,
    `ctc-tin-*`, `ctc-sidebar-*`, `ctc-proceed-btn`). All field boxes use a
    uniform `border: 1px solid #333` (the same "uniform border" fix applied
    to the add-batch modal).

### Backend

- New route `GET /collections/transaction-entry/{formStock}/individual-cedula`
  (`transaction-entry.individual-cedula`), returning the new view with the
  `FormStock` record (used for the `form_code` in the breadcrumb).
- `form-stocks-table.blade.php`: "Add Transaction" now links to this route
  when `form_code === 'BIR0016'` (Individual Cedula); other rows keep the
  placeholder `#` link pending future implementation.

### Verification

- Verified via Claude Preview: navigated to
  `/collections/transaction-entry/1/individual-cedula`, confirmed the page
  header, breadcrumb (red "Individual Cedula BIR0016"), "Transactions
  Log | New Entry" tabs, and the full CTC form grid render with uniform
  1px `#333` borders matching the Figma layout (1412×668px form,
  certificate number, TIN boxes, Sex/Civil Status radios, tax computation
  table, signature boxes, and the right sidebar with Proceed button).

## 2026-06-13 — Individual Cedula "New Entry" form fields converted to inputs

Converted the Individual Cedula CTC form
(`resources/views/collection-management/transaction-entry/individual-cedula.blade.php`)
from static display-only cells to a real, editable `<form>`, per the Figma
prototype (https://www.figma.com/proto/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=116-2258).

- **`.ctc-page` is now a `<form>`** wrapping all fields.
- **All personal/registration fields are real inputs**: Year, Place of
  Issue, Date Issued (×2, `type="date"`), Name (Surname) / First / Middle
  Name, Citizenship, ICR No., Place of Birth, Height, Weight, Date of Birth
  (`type="date"`), and Profession/Occupation/Business — each using a new
  `.ctc-input-wrap` + `.ctc-input` + `.ctc-input-caption` pattern.
- **Floating-label "Figma animation"**: each `.ctc-input-caption` label sits
  centered/large by default and shrinks to a small top-left caption
  (8px, uppercase) on focus (`:focus-within`) or once filled (`.filled`
  class, toggled via JS on `input`). Date fields (`.ctc-date`) always show
  the shrunk caption since native date inputs don't have an empty state.
- **Thin focus borders**: `.ctc-input:focus`, `.ctc-amount-input:focus`,
  `.ctc-tin-cell:focus`, `.ctc-cert-no-input:focus`, `.ctc-sidebar-input:focus`,
  and `.ctc-radio:focus` all use `outline: 1px solid #333` (matching the
  existing `.form-batch-input:focus` convention) instead of the browser's
  default thicker focus ring.
- **TIN box rebuilt as 15 individual single-digit inputs** (previously 12
  non-input cells in 4 groups of 3): now 5 groups of 3
  `<input maxlength="1" inputmode="numeric">` cells (`.ctc-tin-cell`), with
  JS auto-advance to the next box on entry and auto-back on backspace.
- **Certificate number** ("CCI2022 13476955") is now an editable
  `.ctc-cert-no-input` text input, defaulting to "CCI2022 13476955" (future:
  default should come from the last form record in the database — not yet
  implemented).
- **Tax computation table**: A row's Community Tax Due, and Items 1-3's
  Taxable Amount + Community Tax Due cells are now `.ctc-amount-cell`
  number inputs (`step="0.01"`), with a `₱` prefix where the Figma design
  shows one. B row and A row's Taxable Amount remain grey/disabled cells.
- **Total and Interest are auto-computed but editable**: `#ctc-total` sums
  the Community Tax Due column (A + items 1-3); `#ctc-interest` sums the
  Taxable Amount column for items 1-3. Both recompute on `input` of their
  dependencies via inline JS, but remain plain number inputs the user can
  overwrite manually at any time.
- **Sidebar "Total Amount Paid"** is now an input (`#ctc-amount-paid`,
  default `500.00`) with a static `₱` prefix.
- **Sidebar "Amount in Words"** is now a text input (`#ctc-amount-in-words`,
  default "Five hundred pesos only").
- **Right Thumb Print / Taxpayer's Signature boxes are unchanged** (left as
  static display boxes, per explicit instruction to exclude them from this
  conversion).
- New/updated CSS in `resources/css/app.css`: `.ctc-input-wrap`,
  `.ctc-input`, `.ctc-input-caption`, `.ctc-date` variant, `.ctc-amount-cell`,
  `.ctc-peso-prefix`, `.ctc-amount-input`, `.ctc-cert-no-input`,
  `.ctc-sidebar-input` (+ `-amount-input` / `-words-input`), reworked
  `.ctc-tin-cell` / `.ctc-tin-group`, and `.ctc-radio:focus`.

### Verification

- Verified via Claude Preview: confirmed 15 TIN input cells render, the
  floating-label caption for "Year" shrinks from centered 10px to top-left
  8px on focus (`:focus-within`), and the Total/Interest auto-calc updates
  correctly when typing into the Item 1 Taxable Amount / Community Tax Due
  fields (Total → sum of Community Tax Due column, Interest → sum of
  Taxable Amount column for items 1-3).

## 2026-06-13 — Individual Cedula form polish (defaults, radios, empty-field shading)

Follow-up polish pass on the Individual Cedula CTC form
(`resources/views/collection-management/transaction-entry/individual-cedula.blade.php`
and `resources/css/app.css`):

- **Year** (`#ctc-year`) now defaults to the current year (`now()->year`)
  instead of being blank.
- **Sex / Civil Status radios**: `.ctc-radio:checked` now renders a `#333333`
  inner dot (`radial-gradient`) instead of relying on the browser's default
  checked indicator.
- **Civil Status label** (`.ctc-radio-label`, "Civil<br>Status"):
  `line-height: 1.1` so the two stacked words sit closer together.
- **"Gemma D. Ferrer"** (Municipal/City Treasurer) is now an editable input
  (`.ctc-sidebar-treasurer-name-input`, `#ctc-treasurer-name`, default
  "Gemma D. Ferrer"), styled identically to the previous static `<p>` (24px,
  Obviously, weight 700).
- **Certificate number** split into a static bold "CCI2022" prefix
  (`.ctc-cert-no-prefix`, weight 600) and a separate, non-bold, editable
  serial-number input (`.ctc-cert-no-input`, weight 400, default
  "13476955"), matching the original Figma weight contrast between the year
  code and serial number.
- **Empty-field shading**: all editable fields (`.ctc-input`,
  `.ctc-amount-input`, `.ctc-cert-no-input`, `.ctc-sidebar-input`,
  `.ctc-sidebar-treasurer-name-input`) now get a light `#F5F1F1` background
  via `.ctc-field.is-empty` when their value is empty — the same tint
  already used for empty TIN cells — and turn white on focus
  (`.ctc-field.is-empty:focus-within`) or once filled. JS in
  `individual-cedula.blade.php` toggles `.is-empty` on the parent `.ctc-field`
  on load and on every `input` event.

### Verification

- Verified via Claude Preview (computed styles): `#ctc-year` defaults to
  `2026`; checked radios render the `#333333` radial-gradient dot; the
  cert-no prefix/input have `font-weight: 600`/`400` respectively; empty
  fields (e.g. "Place of Issue", "Total") have `background-color:
  rgb(245, 241, 241)` which turns white on focus or once a value is
  entered.

## 2026-06-13 — Individual Cedula "Proceed" save + centered print-preview modal

Implemented the "Proceed" submission flow for the Individual Cedula CTC form
(`resources/views/collection-management/transaction-entry/individual-cedula.blade.php`),
including a new centered print-preview modal per the Figma design
(https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=485-6687).

### Data

- New `ctc_individual_transactions` table (migration
  `database/migrations/2026_06_13_140000_create_ctc_individual_transactions_table.php`,
  model `app/Models/CtcIndividualTransaction.php`, `belongsTo` `FormStock`):
  stores every field on the CTC form (`certificate_number`, `year`,
  `place_of_issue`, `date_issued`/`date_issued_2`, name fields, `tin`, `sex`,
  `citizenship`, `icr_no`, `place_of_birth`, `height`, `civil_status`,
  `weight`, `date_of_birth`, `profession`, the A/Item1-3 taxable/community
  tax due amounts, `total_community_tax_due`, `interest`, `amount_paid`,
  `amount_in_words`, `treasurer_name`). Amount columns default to `0`.
- `app/Models/FormStock.php`: added `ctcIndividualTransactions()`
  `hasMany(CtcIndividualTransaction::class)` relation.

### Frontend — print-preview modal

- New partial
  `resources/views/collection-management/transaction-entry/partials/ctc-preview-modal.blade.php`
  — a brand-new, pixel-accurate 882×672px portrait "Community Tax
  Certificate" layout (Figma node `485:6687`), shown centered on screen when
  "Proceed" is clicked (after validation passes). Reuses the existing
  `.ctc-field` / `.ctc-input-wrap` / `.ctc-input-caption` / `.ctc-radio-group`
  base classes plus new read-only `.ctcp-*` classes (`.ctcp-value`,
  `.ctcp-amount-value`, `.ctcp-radio` + `.checked`, `.ctcp-tin-cell`,
  `.ctcp-treasurer`, `.ctcp-treasurer-divider`).
- New `.ctc-preview-*` modal-chrome classes in `resources/css/app.css`:
  `.ctc-preview-overlay` (+ `.open`, fixed fullscreen flex-center backdrop),
  `.ctc-preview-wrap`, `.ctc-preview-modal` (`#EDF3F9`, 930px), red circular
  `.ctc-preview-close-btn` (`<x-bx-x>`), `.ctc-preview-caption` (BIR Form /
  DOP captions), `.ctc-preview-print-btn` (dark button outside the modal,
  bottom-right).
- Also added `.ctc-field.has-error { border-color: #E04F4F; }` for
  highlighting required-but-empty fields.
- `individual-cedula.blade.php`: form submit (`#ctcForm`) now validates the
  required fields (`surname`, `first_name`, `amount_paid`) — highlighting
  empty ones with `.has-error` — then, if valid, populates the preview modal
  (`populatePreview()`: text values, amounts, TIN cells, and checked state
  for Sex/Civil Status radios) and opens it centered. The modal's Close (X)
  button (and clicking the overlay backdrop) closes it without saving.
  Clicking "Print" in the modal performs the AJAX save (see below), then
  closes the modal, resets the form, shows the success alert, and calls
  `window.print()`.

### Backend

- New route `POST /collections/transaction-entry/{formStock}/individual-cedula`
  (`transaction-entry.individual-cedula.store`): validates all CTC fields
  (required: `certificate_number`, `year`, `surname`, `first_name`,
  `amount_paid`; `tin` validated as an array of 15 single-character cells
  and joined into one string before saving; amount fields default to `0`
  when empty), creates a `CtcIndividualTransaction` record via
  `$formStock->ctcIndividualTransactions()->create(...)`, decrements
  `form_stocks.qty` by 1 (floored at 0), and returns
  `{ message, qty }` as JSON.
- `individual-cedula.blade.php`'s form `action` uses
  `route('transaction-entry.individual-cedula.store', $form->id, false)` —
  the `false` third argument is required to generate a relative URL, since an
  absolute URL (based on `APP_URL`) breaks the AJAX POST when the page is
  viewed through a different host/port (e.g. the Claude Preview proxy).

### Verification

- Verified via Claude Preview: clicking "Proceed" with required fields
  filled opens the print-preview modal centered in the viewport (confirmed
  via `getBoundingClientRect()` — modal center matches viewport center) with
  all entered values, TIN digits, and Sex/Civil Status radio selections
  correctly reflected; the Close button and backdrop click both close it
  without saving. Clicking "Print" saves the record (`ctc_individual_transactions`
  row created, `form_stocks.qty` decremented from 26 → 25), closes the
  modal, resets the form to its defaults, shows the "Successfully Added!"
  -style success alert with "CCI2022 {certificate_number}", and calls
  `window.print()`.
- Fixed a validation bug found during verification: the 15 TIN inputs are
  submitted as `tin[]` (an array), but the original validation rule expected
  a plain string, causing every save to fail with a 422 (silently redirected
  back to the form). Fixed by validating `tin` as `['nullable', 'array']` /
  `tin.*` as a single-character string, then `implode()`-ing it before
  saving.

## 2026-06-13 — Print-preview modal layout fixes (Figma alignment)

Follow-up fixes to the print-preview modal
(`resources/views/collection-management/transaction-entry/partials/ctc-preview-modal.blade.php`
and `resources/css/app.css`) to match the Figma layout (node `485:6687`)
exactly:

- **Treasurer box font sizes**: `.ctcp-treasurer .ctc-sidebar-treasurer-name`
  and `.ctcp-treasurer .ctc-sidebar-treasurer-title` now override the shared
  24px/14px form-input sizes down to the Figma preview sizes (14px bold name,
  10px titles), so "GEMMA D. FERRER" / "Municipal Treasurer" / "Municipal /
  City Treasurer" fit within the 412×84px signature box without overflow.
- **Certificate number alignment**: `.ctcp-page .ctc-cert-no-input` now uses
  `height: auto; flex: none` instead of inheriting `height: 100%` from the
  shared `.ctc-cert-no-input` form-input style. Previously the serial number
  span stretched to the full 58px cell height and rendered its text at the
  top, while the "CCI2022" prefix stayed vertically centered — now both sit
  on the same centered baseline ("CCI2022 13476955").
- **Caption alignment**: `.ctc-preview-modal` changed from `align-items:
  center` to `align-items: flex-start`, so the "BIR Form 0016 (December,
  2014)" and "DOP: 05.14.2021" captions left-align with the `.ctcp-page`'s
  left edge (per the Figma `items-start` layout), instead of being centered
  across the modal width.
- **Print button position**: `.ctc-preview-print-btn` moved from `right:
  -150px; bottom: 24px` to `right: -174px; bottom: 0`, giving a 24px gap
  between the modal's right edge and the button, and aligning the button's
  bottom edge with the modal's bottom edge.

### Verification

- Verified via Claude Preview (computed styles/`getBoundingClientRect()`):
  treasurer name/title render at 14px/10px; "CCI2022" prefix and serial
  number both render on the same row (top/bottom 113px/149px); both preview
  captions left-align at the same x-position as `.ctcp-page` (51px in the
  test viewport); the print button sits with a 24px gap to the right of the
  modal and its bottom edge matches the modal's bottom edge (668px).

## 2026-06-13 — Corporation Cedula "Add Transaction" page (Figma node 213:2338)

Implemented the "Add Transaction" page for the Corporation Cedula form type
(`BIR0017`), matching the Individual Cedula (`BIR0016`) structure/patterns
and the Figma design at node `213:2338`.

### Database

- New migration
  `database/migrations/2026_06_13_150000_create_ctc_corporation_transactions_table.php`
  creating `ctc_corporation_transactions`: `form_stock_id` (FK, cascade
  delete), `certificate_number`, `year`, `place_of_issue`, `date_issued`,
  `company_name`, `tin`, `date_of_registration`, `address`,
  `kind_of_organization`, `nature_of_business`, `a_community_tax_due`,
  `item1_taxable_amount`, `item1_community_tax_due`, `item2_taxable_amount`,
  `item2_community_tax_due`, `total_community_tax_due`, `interest`,
  `amount_paid`, `amount_in_words`, `treasurer_name`, timestamps.
- New model `app/Models/CtcCorporationTransaction.php` with `$fillable`
  matching the migration and `belongsTo(FormStock::class)`.
- Added `FormStock::ctcCorporationTransactions(): HasMany` relation in
  [FormStock.php](../../app/Models/FormStock.php).

### Frontend

- New view
  `resources/views/collection-management/transaction-entry/corporation-cedula.blade.php`,
  reusing the `.ctc-field`/`.ctc-input-wrap`/`.ctc-tin-*`/`.ctc-radio*`/
  `.ctc-amount-cell`/`.ctc-sidebar-*`/`.ctc-proceed-btn` classes and the
  floating-label/TIN-auto-advance/empty-shading/total-and-interest-autocalc
  JS from Individual Cedula, with fields positioned per Figma node `213:2338`
  (title/badge/certificate number, year/place of issue/date issued, company
  name, TIN grid, address, date of registration, "Kind of Organization"
  radio row (Corporation/Association/Partnership), nature of business, the
  A/B basic & additional community tax rows, items 1–2 taxable
  amount/community tax due rows, signature box, Total/Interest cells, and
  sidebar amount paid/amount in words/treasurer name).
- New partial
  `resources/views/collection-management/transaction-entry/partials/ctc-corporation-preview-modal.blade.php`
  — print-preview modal (`.ctcp-page.ctcp-page--corporation`) mirroring the
  main form fields at coordinates shifted by -40px, with `data-preview*`
  attributes consumed by the form's `populatePreview()`.
- New CSS modifier classes in `resources/css/app.css`:
  `.ctc-page--corporation { height: 584px; }` and
  `.ctcp-page--corporation { height: 630px; }`.
- `recalcTotal` sums `a_community_tax_due` + `item1_community_tax_due` +
  `item2_community_tax_due` (no item 3, unlike Individual Cedula);
  `recalcInterest` sums `item1_taxable_amount` + `item2_taxable_amount`;
  required fields are `company_name` and `amount_paid`; the preview radio
  group reflects `kind_of_organization`.

### Backend

- New routes:
  - `GET /collections/transaction-entry/{formStock}/corporation-cedula`
    (`transaction-entry.corporation-cedula`).
  - `POST /collections/transaction-entry/{formStock}/corporation-cedula`
    (`transaction-entry.corporation-cedula.store`): validates required
    `certificate_number`, `year`, `company_name`, `amount_paid`; `tin`
    validated as an array of single-character cells and imploded; amount
    fields default to `0` when empty; creates a `CtcCorporationTransaction`
    via `$formStock->ctcCorporationTransactions()->create(...)`, decrements
    `form_stocks.qty` by 1 (floored at 0), and returns `{ message, qty }`.
- [form-stocks-table.blade.php](../../resources/views/collection-management/transaction-entry/partials/form-stocks-table.blade.php):
  the "Add Transaction" link now resolves per `form_code` —
  `BIR0016` → individual-cedula route, `BIR0017` → corporation-cedula route,
  others → `#` placeholder.

### Verification

- Verified via Claude Preview: the page renders with zero console errors and
  all 35 `.ctc-field` elements match the Figma coordinates from node
  `213:2338`.
- Verified the save flow end-to-end: submitting the form POSTs to
  `transaction-entry.corporation-cedula.store`, which returns
  `{"message":"Transaction saved successfully.","qty":24}`; confirmed via
  `php artisan tinker` that `CtcCorporationTransaction::count()` went from 0
  to 1 and `FormStock::find(2)->qty` decremented from 25 to 24.

## 2026-06-13 — Corporation Cedula Figma-alignment fixes + editable cert. prefix

Follow-up fixes after reviewing the Corporation Cedula page against Figma
node `213:2338`, plus applying the same certificate-number editability fix to
Individual Cedula.

- **"CORPORATION" badge size**: added `.ctc-badge--corporation p { font-size:
  18px; }` in `resources/css/app.css` (the shared `.ctc-badge p` is 24px,
  which is correct for Individual Cedula's "Individual" badge but too large
  for Corporation Cedula's badge per Figma) and applied the
  `ctc-badge--corporation` modifier class in `corporation-cedula.blade.php`
  and `ctc-corporation-preview-modal.blade.php`.
- **Tax-row/item label wording**: replaced the "P 5.00" / "P 2.00 for every
  P 5,000.00" style text with the actual peso sign (`&#8369;`) to match
  Figma exactly — "A. Basic Community Tax (₱ 5.00)", "B. Additional
  Community Tax ( Tax not to exceed ₱ 10,000.00 )", and items 1–2 now read
  "(₱2.00 for every ₱5,000.00)" — in both the main form and the preview
  modal.
- **Editable certificate-number prefix**: the certificate number field
  (e.g. "CCC2021 00259338" / "CCI2022 13476955") previously only made the
  serial number (`#ctc-cert-no`) editable, with the prefix as static text
  (`.ctc-cert-no-prefix` span). Both the prefix and serial are now editable
  `<input>`s:
  - New `.ctc-cert-no-prefix-input` class in `resources/css/app.css`
    (mirrors `.ctc-cert-no-prefix`'s typography but as a borderless,
    transparent, `7ch`-wide input with a focus outline).
  - `corporation-cedula.blade.php` and `individual-cedula.blade.php`: the
    prefix span is now `<input id="ctc-cert-prefix">` (not submitted with
    the form — display/edit only); `resetForm()` restores it to its default
    ("CCC2021"/"CCI2022"); `populatePreview()` copies its value into the
    preview modal's `#ctcPreviewCertPrefix` span; the success-alert message
    now reads `${prefix} ${serial}` instead of a hardcoded prefix.
  - `ctc-corporation-preview-modal.blade.php` and `ctc-preview-modal.blade.php`:
    the static prefix `<span class="ctc-cert-no-prefix">` is now
    `<span class="ctc-cert-no-prefix-input" id="ctcPreviewCertPrefix">`,
    populated by `populatePreview()`.

### Verification

- Verified via Claude Preview at 1600×900: Corporation Cedula renders with
  the "CORPORATION" badge at 18px, "CCC2021"/"00259338" both as `<input>`
  elements, and the updated tax-row/item labels with peso signs; Individual
  Cedula renders with "CCI2022"/"13476955" both as `<input>` elements with no
  layout shift.

## 2026-06-13 — Corporation Cedula TIN centering, date-of-registration label, modal cert. number resize

Follow-up fixes after another Figma review pass on Corporation Cedula (main
form and print-preview modal).

- **TIN field centering**: the TIN (if Any) field is 42px tall, but the
  15-cell TIN grid (`.ctc-tin-group`) was positioned with `top: 24px`, plus
  its 20px cell height, causing it to overflow/touch the field's bottom
  border by 2px. Added `.ctc-field--tin-compact .ctc-tin-group { top: 10px;
  }` in `resources/css/app.css` and applied the new `ctc-field--tin-compact`
  modifier class to the TIN field `<div>` in both
  `corporation-cedula.blade.php` and `ctc-corporation-preview-modal.blade.php`
  — the grid is now vertically centered (~11px top/bottom margin) within the
  field. (Individual Cedula's equivalent TIN field is 55px tall and unaffected,
  so the fix is scoped to this new modifier rather than the shared
  `.ctc-tin-group` rule.)
- **"Date of Registration / Incorporation" label**: changed to match Figma's
  two-line caption — "Date of registration" / "/ incorporation" at 10px.
  Added `.ctc-input-wrap.ctc-date .ctc-input-caption.ctc-caption-multiline {
  font-size: 10px; line-height: 1.3; white-space: normal; text-overflow:
  clip; }` in `resources/css/app.css`, and updated the label markup to
  `Date of registration<br>/ incorporation` with the new
  `ctc-caption-multiline` class, in both the main form and the preview modal.
- **Preview-modal certificate number resize**: "CCC2021 00259338" was
  rendering oversized (24px) and overflowing its 272px-wide field in the
  print-preview modal. Added `.ctcp-page .ctc-cert-no-prefix-input,
  .ctcp-page .ctc-cert-no-input { font-size: 16px; }` in
  `resources/css/app.css` to bring it in line with the rest of the modal's
  scaled-down typography.

### Verification

- Verified via Claude Preview (`preview_eval` bounding-rect checks + a
  screenshot of the populated print-preview modal): the TIN grid now sits
  with ~11px top/bottom margins (no overflow) in both the main form and the
  modal; the date-of-registration caption renders as two lines at 10px in
  both; the modal's "CCC2021 00259338" certificate number at 16px fits fully
  within its 272px field with no overflow.

## 2026-06-13 — Save Transaction Log entry on Print, redirect to Collection Management

When "Print" is pressed on the Individual Cedula or Corporation Cedula "Add
Transaction" pages, a corresponding row is now created in `transaction_logs`
(the table backing the Collection Management page), and the user is
redirected there.

- **`certificate_prefix` now submitted with the form**: the editable
  certificate-prefix `<input id="ctc-cert-prefix">` (e.g. "CCI2022"/"CCC2021")
  now has `name="certificate_prefix"` in both `individual-cedula.blade.php`
  and `corporation-cedula.blade.php`, and is validated as `nullable|string`
  in both POST handlers.
- **`routes/web.php`** — both `transaction-entry.individual-cedula.store` and
  `transaction-entry.corporation-cedula.store`, after creating the CTC
  transaction and decrementing `FormStock.qty`, now also create a
  `TransactionLog`:
  - `serial_number` — `"{certificate_prefix} {certificate_number}"` (e.g.
    "CCI2022 13476955").
  - `payee` — Individual Cedula: `"{surname}, {first_name} {middle_name}"`
    (matching the seeded "Surname, First M" format); Corporation Cedula:
    `company_name`.
  - `transacted_at` — `now()` (date + time of saving).
  - `form_type` — `$formStock->form_code` (e.g. "BIR0016"/"BIR0017").
  - `status` — `"Completed"`.
  - The JSON response now also includes a relative `redirect` URL
    (`route('collections', [], false)`).
- **Frontend (`individual-cedula.blade.php` / `corporation-cedula.blade.php`)**:
  the "Print" button's success handler now calls `window.print()` then
  `window.location.href = data.redirect` to navigate to Collection
  Management. The now-dead `resetForm()` / `showSuccessAlert()` helpers and
  the `#ctcSuccessAlert` success-toast markup (no longer reachable, since the
  page navigates away) were removed from both pages.

### Verification

- Verified via Claude Preview: submitted both forms end-to-end (filled
  required fields, opened the print-preview modal, clicked "Print" with
  `window.print` stubbed), confirmed the POST returns the JSON with
  `redirect: "/collections"`, the page navigates to Collection Management,
  and the new row appears at the top of the Transaction Logs table with the
  correct serial number, payee, date/time, form type ("BIR0016"/"BIR0017"),
  and "Completed" status. Test records were removed afterward
  (`TransactionLog`, `CtcIndividualTransaction`/`CtcCorporationTransaction`
  rows, and `FormStock.qty` restored).

## 2026-06-13 — Fix breadcrumb spacing and add "Collections Management" segment on cedula pages

Fixed two issues on the Individual Cedula and Corporation Cedula "Add
Transaction" pages
(`resources/views/collection-management/transaction-entry/individual-cedula.blade.php`
and `corporation-cedula.blade.php`):

- **Breadcrumb too far from title**: the inline `.container-title` markup had
  `.page-title` and `.page-links` as direct flex children of
  `.container-title`, which has `gap: 10px`. Wrapped both in an inner
  `<div style="display: flex; flex-direction: column;">` (matching the
  structure used by `<x-header>`), eliminating the extra gap so the
  breadcrumb sits directly under the title.
- **Breadcrumb segments**: changed from "Home | Transactions Entry | {Form
  Name} {form_code}" to "Home | Collections Management | Transactions Entry |
  {Form Name} {form_code}", adding a link to `route('collections')` as the
  new second segment.

### Verification

- Verified via Claude Preview on `/collections/transaction-entry/1/individual-cedula`
  and `/collections/transaction-entry/2/corporation-cedula`: measured the gap
  between `.page-title` and `.page-links` bounding rects (now ~0px, down from
  10px), and confirmed the breadcrumb text reads "Home | Collections
  Management | Transactions Entry | Individual Cedula BIR0016" /
  "... | Corporation Cedula BIR0017".

## Follow-ups

- Official Receipt (Form 5IC) changes are now tracked in
  [official-receipt.md](official-receipt.md).
- OR RPT (Form 56) changes are now tracked in [or-rpt.md](or-rpt.md).
- "Add Transaction" action for forms other than Individual Cedula
  (`BIR0016`) and Corporation Cedula (`BIR0017`) is still a placeholder
  link/button — not yet wired to any functionality.
- The certificate number prefix ("CCI2022"/"CCC2021") and default serial
  numbers should eventually be sourced from the last form record in the
  database instead of being hardcoded.
