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

## Follow-ups

- "Add Transaction" action is still a placeholder link/button — not yet
  wired to any functionality.
