# Official Receipt & Accountable Forms

## 2026-06-15 — Initial module implementation (Figma 514-7976 & 688-5120)

### Database
- New migration `2026_06_15_100000_add_added_time_to_form_stocks_table` adds a
  nullable `added_time` column to `form_stocks` (after `added_date`) and
  backfills existing rows with default times per form code.
- `database/seeders/FormStockSeeder.php` updated to seed `added_time` for all
  8 form stock rows.

### Backend
- `App\Models\FormStock`:
  - Added `added_time` to `$fillable`.
  - Added `applyBatch(array $validated, ?string $addedBy = null): void` which
    creates the `FormBatch` record, computes the batch quantity from the
    serial number range, and updates `qty`, `added_date`, and `added_time`.
- `routes/web.php`:
  - Refactored `transaction-entry.batches.store` to call
    `$formStock->applyBatch()` instead of duplicating the logic inline.
  - `official-receipts-accountable-forms` GET route now supports
    `search`/`sort`/`direction`/`per_page`, pagination, and returns the
    `forms-table` partial for AJAX (`X-Requested-With: XMLHttpRequest`)
    requests. Sortable columns: `qty`, `form_name`, `form_code`,
    `added_date`, `added_time`, `added_by`.
  - New `official-receipts-accountable-forms.batches.store` POST route reuses
    `FormStock::applyBatch()` and returns the updated `forms-table` partial.

### Frontend
- `resources/views/official-receipt-accountable-forms/index.blade.php`
  rebuilt: header/breadcrumb, search toolbar, data table container, the
  shared "Add new batch" modal (reused from Transaction Entry), and the new
  Export modal. AJAX search/sort + batch modal submit follow the same
  `fetchAndRender`/`reloadTable` pattern as Transaction Entry.
- `resources/views/official-receipt-accountable-forms/partials/forms-table.blade.php`
  (new): table matching the Collection Management `.data-table` styling with
  columns Qty., Form Name, Form Code, Added Date, Added Time, Added By,
  Actions. Qty. is colored green/yellow/red based on stock level
  (`.qty-success` / `.qty-warning` / `.qty-danger`). Actions column shows
  "View | Add New Batch" links for every row, plus "Export" (only for
  Individual Cedula `BIR0016` and Corporation Cedula `BIR0017`), matching the
  Figma action-link colors (`#1877f2` / `#0fa958` / `#70b6c1`) in Manrope.
- `resources/views/official-receipt-accountable-forms/partials/export-modal.blade.php`
  (new): "Date Range for Report" modal (Figma node 688-5120) with From/To
  month selects and a Year input styled like the existing add-batch field
  groups, and a "Next" button styled with `.ctc-add-entry-btn` (OR RPT Add
  Entry button styling). Submitting currently just closes the modal — no
  further export flow was specified in Figma.
- `resources/css/app.css`: added `.qty-success/.qty-warning/.qty-danger`,
  `.orabf-actions/.orabf-link*` (action link colors), and
  `.orabf-export-modal/.orabf-export-divider/.orabf-export-field-row` for the
  export modal layout.

### Verification
- Verified via Claude Preview: search, sort, pagination, "Add New Batch"
  modal (reusing the Transaction Entry batch flow) and the Export modal
  open/close with no layout overlaps.

## 2026-06-15 — Actions redesign + Report Logs page (Figma 644-7252)

### Backend
- `App\Models\FormBatch`: added `startingQty()`, `usedQty()`, `remainingQty()`,
  and `status()` helpers. Starting quantity is computed from the trailing
  digits of the starting/ending serial numbers (e.g. `CCI2022 13476951` to
  `CCI2022 13477051` => 100). `usedQty()` is a placeholder returning `0` until
  usage tracking is implemented; `remainingQty()` = startingQty - usedQty;
  `status()` is `Complete` when remaining is `0`, otherwise `Incomplete`.
- `routes/web.php`: new
  `official-receipts-accountable-forms.report-logs` GET route
  (`/official-receipts-accountable-forms/{formStock}/report-logs`) with
  `search` (matches `starting_serial_number`/`ending_serial_number`),
  `sort`/`direction` (`starting_serial_number`, `ending_serial_number`,
  `created_at`, `added_by`), pagination, and an AJAX partial response.

### Frontend
- `resources/views/official-receipt-accountable-forms/report-logs.blade.php`
  (new): per-form Report Logs page with breadcrumb
  "Home | Official Receipt & Accountable Forms | Report Logs", a
  "Search Batch Number" search box, and the same AJAX
  search/sort/pagination pattern as the index page.
- `resources/views/official-receipt-accountable-forms/partials/report-logs-table.blade.php`
  (new): table with columns Starting Qty., Starting OR Serial Number, Ending
  OR Serial Number, Used, Remaining, Added Date, Added Time, Status, Added By
  — sourced from `$formStock->batches`. Status is shown as a colored
  `.status-badge` (`Complete` green / `Incomplete` red).
- `resources/views/official-receipt-accountable-forms/partials/forms-table.blade.php`:
  - "Actions" header is now centered (`col-actions text-center`).
  - Actions cell redesigned to match Collection Management's
    `.table-actions`/`.action-btn` pill pattern: "View" (`#8895c6`, navigates
    to the new Report Logs page instead of opening a transaction entry),
    "Add New Batch" (`#0fa958`), and "Export" (`#70b6c1`, only for
    `BIR0016`/`BIR0017`).
- `resources/css/app.css`: added `.text-center`, `.action-batch`,
  `.action-export` (new `.action-btn` color variants), and
  `.status-complete`/`.status-incomplete` for the Report Logs status column.
  Removed the now-unused `.orabf-actions`/`.orabf-link*`/`.orabf-separator`
  classes.

### Verification
- Verified via Claude Preview: Actions pills render correctly with the
  centered "Actions" header, "View" navigates to the Report Logs page,
  the Report Logs table shows correctly computed Starting Qty./Used/
  Remaining/Status, search by batch number works, and no layout overlaps.

## 2026-06-15 — Bug fixes: startingQty, Remaining count, ending serial display, View underline, PH time

### Backend
- `App\Models\FormBatch`:
  - `startingQty()` is now inclusive (`end - start + 1`), fixing the
    off-by-one where a `2026-00001`–`2026-00005` range reported 4 instead of
    5.
  - `usedQty()` is no longer a hardcoded `0`. It now counts how many serial
    numbers within the batch's range have a matching CMTE transaction
    (`CtcIndividualTransaction` for `BIR0016`, `CtcCorporationTransaction` for
    `BIR0017`, `OrRptTransaction` for `Form 56`, `OrTransaction` for
    `Form 5IC`), so `remainingQty()`/`status()` now update as transactions are
    recorded.
  - New `displayEndingSerialNumber()`: formats the ending serial number to
    match the starting serial number's prefix/padding (e.g. starting
    `2026-00001` with an ending input of `005` now displays as `2026-00005`).
  - New `nextAvailableSerialNumber()`: returns the oldest unused serial number
    within the batch's range, formatted to match the starting serial's
    padding, or `null` if the batch is fully used.
- `App\Models\FormStock`: new `nextAvailableSerialNumber()` walks the form's
  batches oldest-first and returns the first `FormBatch::nextAvailableSerialNumber()`
  result — used to default the Individual Cedula serial number field (see
  `collection-management-transaction-entry.md`).
- `config/app.php`: `timezone` changed from `UTC` to `Asia/Manila`, so
  `now()` (and thus all "Added Time"/"Added Date" values across every module)
  reflect Philippine time.

### Frontend
- `resources/views/official-receipt-accountable-forms/partials/report-logs-table.blade.php`:
  Ending OR Serial Number column now renders `$batch->displayEndingSerialNumber()`
  instead of the raw stored value.
- `resources/css/app.css`: `.action-btn` now sets `text-decoration: none`, so
  the "View" pill (an `<a>` tag) no longer renders with an underline.

### Verification
- Verified via Claude Preview: the `2026-00001`–`005` batch row in Report
  Logs now shows Starting Qty. `5`, Ending OR Serial Number `2026-00005`,
  Used `1`, Remaining `4`, Status `Incomplete`; the "View" pill has
  `text-decoration-line: none`.

### Task Lists
    List all task here
### Steps:
1. Implement this design from Figma.
@https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=514-7976&m=dev
2. **Add New Batch** button will open modal same as the **Add new receipt** from **Transaction Entry** module.
3. **Export** button will open
	>Implement this design from Figma.
	@https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=688-5120&m=dev
4. Change the ACTIONS button design on the OFFICIAL RECEIPT & ACCOUNTABLE
   FORMS (ORAF) to match Collection Management, center the "Actions" header,
   and make the "View" button open a per-form Report Logs page (Figma
   644-7252) instead of a new entry.
5. Remove the underline on the "View" button text; fix the Report Logs
   `startingQty()` off-by-one bug; make ending OR serial number display match
   the starting serial's prefix/padding; make the "Remaining" count update
   based on real CMTE transactions; and display "Added Time" in Philippine
   time across all modules.

## 2026-06-15 — Appended source task notes (Dynamic certificate prefix)

The CM Individual Cedula (BIR0016) and Corporation Cedula (BIR0017) "Add
Transaction" certificate prefix/number fields are now derived from this
module's `FormBatch` records instead of being hardcoded, transactions are
validated against available `FormBatch` stock before saving, and the Report
Logs "Remaining" count no longer decrements for transactions saved under a
mismatched prefix. Full implementation details are in
[collection-management-transaction-entry.md](collection-management-transaction-entry.md)
("2026-06-15 — Dynamic certificate prefix, stock validation, and
Remaining-count fix").

### Backend
- `App\Models\FormBatch`: new `serialPrefix()`, `expectedCertificatePrefix()`,
  `matchesCertificate()`; `serialRange()` is now `public`;
  `transactionSerialNumbers()` for `BIR0016`/`BIR0017` now only counts
  transactions whose `certificate_prefix` matches
  `expectedCertificatePrefix()`.
- `App\Models\FormStock`: new `nextAvailableBatch()` and
  `hasAvailableSerial()`, replacing the old `nextAvailableSerialNumber()`.

### Data cleanup
- Deleted two stray `FormBatch` rows for `form_stock_id = 1` (`id 8`
  `"TEST001"`, `id 11` `"CCI2026-00006"`) left over from earlier
  dev/verification sessions — both had malformed `starting_serial_number`
  values incompatible with the new `expectedCertificatePrefix()` scheme. Only
  `id 10` (`"2026-00001"`–`"005"`) remains.

### Verification
- Verified via Claude Preview: `/official-receipts-accountable-forms/1/report-logs`
  for the `2026-00001`–`2026-00005` batch now shows `Used: 1`, `Remaining: 4`
  after a matching `CCI2026-00001` transaction was saved from the CM
  Individual Cedula page (previously `Used: 0`/`Remaining: 5`, since the
  legacy transactions with empty `certificate_prefix` no longer match
  `expectedCertificatePrefix() = "CCI2026-"`).

## 2026-06-15 — Appended source task notes (CM Serial Number Default + PROCEED button)

# Tasks:
- Collection Management:
	- Serial Number Field Default Value.
	- PROCEED BUTTON style and design update

- Official Receipts & Accountable Forms:
	- The View button should not have an uderline in the text.
	- in report logs, ending serial number data should match the starting serial. for example, if I input 005 in the ending OR serial number from adding batch. the data in the table should be 2026-00005, since it only differs from the last 3 digit.

- At finished task, append this Tasks, Description / Scenario / Events / Steps, and Notes and the last part of ORAF MD file and CM MD file.

## Description / Scenario / Events / Steps:
	1. CM -> We are going to make changes in the CM's Transaction Entry -> Individual Cedula BIR0016 serial number field. the serial number field default value must be the oldest Serial number that is not used. however, that field is still editable for the field collector who holds different batch of receipt.
	2. CM ->We should alert the user if the Serial number that they are using / inputting is already taken.
	3. CM ->Update the PROCEED BUTTON to match OR RPT add entry button.
	4. ORAF Report logs -> there was a bug on the computation of starting qty. from 2026-00001 and 005, there should be 5 available serial number not 4.
	5. ORAF Report logs -> Remaining count is not updating.
	6. And also, in all module, added time should be in Philippine time.
	7. ORAF -> View button, remove underline.

## Notes
	1. Serial number default value comes from the available serial number receipts from ORAF.
	2. since in TRANSACTION ENTRY (CMTE) has "add new receipt", this should also be recorded in the report logs in ORAF.

**Resolution**: All items above are implemented — see the
"2026-06-15 — Bug fixes: startingQty, Remaining count, ending serial display,
View underline, PH time" section above for the ORAF-side changes, and
[collection-management-transaction-entry.md](collection-management-transaction-entry.md)
for the CM-side changes (serial number default value, duplicate-serial alert,
PROCEED button restyle).

## 2026-06-15 — Qty. reconciliation (availableQty) + Add New Batch overlap validation

The ORAF index table's Qty. column and the "Add New Batch" modal now stay in
sync with the per-batch totals shown in Report Logs and with serial numbers
already recorded in CM Transaction Logs (CMTL). Full implementation details
are in
[collection-management-transaction-entry.md](collection-management-transaction-entry.md)
("2026-06-15 — Certificate prefix/number gap fix, availableQty(),
batch-overlap validation").

### Backend
- `App\Models\FormStock::availableQty()`: sums each `FormBatch::remainingQty()`
  when the form has batches, otherwise falls back to the stored `qty` column.
- `resources/views/official-receipt-accountable-forms/partials/forms-table.blade.php`:
  the Qty. column now renders `$form->availableQty()` (previously the raw
  `qty` column), so e.g. BIR0016 now shows `8` (sum of its three batches'
  remaining counts) instead of the stale `2`.
- `App\Models\FormBatch::conflictingCertificate()` /
  `App\Models\FormStock::conflictingCertificate()`: before
  `official-receipts-accountable-forms.batches.store` calls `applyBatch()`, it
  checks whether the submitted SSN/ESN range overlaps a certificate already
  recorded in `ctc_individual_transactions`/`ctc_corporation_transactions`
  (CMTL). If so, the request returns `422` with
  `{"message": "ALERT: {certificate} is already in used, change batch
  receipt."}` and no `FormBatch` is created. Only applies to
  `BIR0016`/`BIR0017`.

### Frontend
- `resources/views/official-receipt-accountable-forms/index.blade.php`: the
  "Add New Batch" modal's submit handler now checks `response.ok`; on `422` it
  `alert()`s the message and keeps the modal open with the entered values, so
  the user can correct the range and resubmit. On success, behavior is
  unchanged.

### Verification
- Verified via Claude Preview at `/official-receipts-accountable-forms`:
  BIR0016 Qty. now shows `8` (was `2`), matching Report Logs' three batch
  rows' combined `remainingQty()`.
- Verified submitting "Add New Batch" for BIR0016 with SSN `2026-00001` / ESN
  `002` (overlaps the existing `CCI2026-00001` CMTL transaction) returns `422`
  and triggers `alert("ALERT: CCI2026-00001 is already in used, change batch
  receipt.")`, with the modal staying open. Submitting a non-overlapping range
  (SSN `2026-00050` / ESN `052`) succeeds, creates a new `FormBatch` (id `13`),
  refreshes the table (Qty. `5` → `8`), and closes the modal.

### Notes
- Per the source task's item 2 constraint, no `TransactionLog`/`FormBatch`/
  transaction test data was removed — the Qty. fix is a computed-display
  change only.

## 2026-06-15 — Appended source task notes (Certificate display gap, Qty reconciliation, batch overlap validation)

# Tasks:
- Collection Management (CM):

- Official Receipts & Accountable Forms (ORAF):

- At finished task, append this Tasks, Description / Scenario / Events / Steps / Abbreviation, and Notes and the last part of ORAF MD file and CM MD file.

## Abbreviation
1. Collection Management -> CM
2. Transaction Entry -> CMTE
3. Transaction Logs -> CMTL
4. Official Receipts & Accountable Forms -> ORAF
5. Starting Serial Number -> SSN
6. Ending Serial Number -> ESN

## Description / Scenario / Events / Steps:
1. fix the visual display of the serial number in the Home | Collections Management | Transactions Entry | Individual Cedula BIR0016 -> the suffix is too far away from the suffix, it looks like this "CCI2026-	00002".
2. the QTY of individual cedula in CMTE and ORAF is 2, however when you view it, it has a starting qty = 5 & 2 and remaining 4 & 2 -> data does not match across the system. also, if we are testing, do not remove the files in the log.
3. in the CM Transaction Logs (CMTL) if the serial number is used, it cannot be added again through ORAF add new batch, always make sure to cross check the data in all logs if the serial is used. for example, in CMTL there is a CCI2026-00007, if I add a batch receipt that has SSN CCI2026-00006 and ESN CCI2026-00008. the system should warn the user and the user cannot continue adding the batch except by fixing the input. warn can be "ALERT: CCI2026-00007 is already in used, change batch receipt."

## Notes
	1. Serial number default value comes from the available serial number receipts from ORAF.
	2. since in TRANSACTION ENTRY (CMTE) has "add new receipt", this should also be recorded in the report logs in ORAF.

**Resolution**: All three items are implemented — see the
"2026-06-15 — Qty. reconciliation (availableQty) + Add New Batch overlap
validation" section above for the ORAF-side changes, and
[collection-management-transaction-entry.md](collection-management-transaction-entry.md)
("2026-06-15 — Certificate prefix/number gap fix, availableQty(),
batch-overlap validation") for the CM-side changes (certificate display fix,
`availableQty()`, `conflictingCertificate()`, and the shared modal-validation
wiring). Note 2 remains informational and not yet scoped as an actionable
item — left for a future task, as in prior rounds.

## 2026-06-15 — Appended source task notes (User Management Module)

# Tasks:
- Creation of USER MANAGEMENT MODULE (UM)

- At finished task, append this Tasks, Description / Scenario / Events /
  Steps / Abbreviation, and Notes and the last part of UM MD file and CM MD
  file.

## Abbreviation
1. Collection Management -> CM
2. Transaction Entry -> CMTE
3. Transaction Logs -> CMTL
4. Official Receipts & Accountable Forms -> ORAF
5. Starting Serial Number -> SSN
6. Ending Serial Number -> ESN
7. User Management -> UM

## Description / Scenario / Events / Steps:
1. User Management landing page -> Implement this design from Figma.
   https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=555-10271&m=dev

2. When **Edit** button is click -> Modal -> Implement this design from
   Figma.
   https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=567-10983&m=dev

3. When **Reset Password** is click -> Modal -> Implement this design from
   Figma.
   https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=567-11030&m=dev
   -> This is a user verification for the current logged in user. just to
   confirm he wants to edit user.
   -> After User Verification -> Implement this design from Figma.
   https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=567-11111&m=dev
   -> Email notification of reset password will be sent to the user (we can
   ignore this for now as we are focusing on FE design and few BE).

4. When **Disable / Activate** button is click -> Modal -> Implement this
   design from Figma.
   https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=572-11244&m=dev

5. **UM Logs (UML)** -> Implement this design from Figma.
   https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=602-6178&m=dev

6. **UM Roles & Permission (UMRP)** -> Implement this design from Figma.
   https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=586-3799&m=dev

7. **UM Add User (UMAU)** -> Implement this design from Figma.
   https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=584-11378&m=dev

8. Make sure that the tables and button across whole system is uniform.

9. Make sure that Breadcrumbs in navagation match the exact location and
   uniform within across the system.

## Notes
1. Serial number default value comes from the available serial number
   receipts from ORAF.
2. since in TRANSACTION ENTRY (CMTE) has "add new receipt", this should also
   be recorded in the report logs in ORAF.
3. Starting Qty = is auto calculated from the batch upload.
   - say for example, Starting OR Serial Number is CCI2022 13476951 and ending
     is CCI2022 13477051 the starting qty. is 100.
4. Used = determines how many forms are used in the batch series from
   13476951 to 13477051.
5. Remaining = Starting Qty - Used.
6. Status = Complete if Remaining is 0.

**Resolution**: The new User Management Module (UM) — user list, Add/Edit
User, Reset Password, Disable/Activate/Archive, UM Logs (+CSV export), and
Roles & Permission matrix — is implemented as a standalone module; see
[user-management.md](user-management.md) for the full breakdown of all 8
Figma screens, schema, routes, and verification. The Roles & Permission
matrix's "User Management" row is also where the `reset_password` /
`change_permission` columns (used only by the `user-management` module) live
in `role_module_permissions`. The only ORAF-side change is that the
`official-receipts-accountable-forms.batches.store` "Add New Batch" route now
also calls `ActivityLog::record('Collection Management - Add Entry')`, so
batch additions made from the ORAF page appear in the new UM Logs page
alongside those made from CM Transaction Entry. No other ORAF behavior
changed. Notes 1–6 remain covered by the ORAF-side sections above (startingQty
fix, Remaining count, `availableQty()`, batch-overlap validation); Note 2
remains informational and not yet scoped, as in prior rounds.

---

## 2026-06-16 — ORAF Export for All Forms

### Description / Scenario / Events / Steps

1. **Add Export button to all forms** — previously only BIR0016 and BIR0017 showed an Export action. Removed the `@if (in_array($form->form_code, [...]))` guard so the Export button appears on every row.

2. **Export downloads report log CSV** — clicking Export opens the existing date-range modal (From month, To month, Year). On submit the browser navigates to the export route, which streams a CSV of all `FormBatch` records for that form within the selected month/year range.

3. **Year cannot exceed current year** — export modal year input changed from `max="2100"` to `max="{{ now()->year }}"`.

### Files Modified

- `resources/views/official-receipt-accountable-forms/partials/forms-table.blade.php` — removed `@if` condition; added `data-form-stock-id="{{ $form->id }}"` to the Export button.
- `resources/views/official-receipt-accountable-forms/partials/export-modal.blade.php` — year input `max` changed to `{{ now()->year }}`.
- `resources/views/official-receipt-accountable-forms/index.blade.php` — JS: on export button click, sets `exportForm.action` to `/official-receipts-accountable-forms/{id}/export`; submit handler closes modal without `preventDefault` so the GET form submits naturally and triggers the file download.
- `routes/web.php` — added `GET /official-receipts-accountable-forms/{formStock}/export` route (named `official-receipts-accountable-forms.export`): validates `from_month`, `to_month`, `year`; queries `FormBatch` records filtered by year + month range; streams CSV download with columns: Starting Qty., Starting OR Serial Number, Ending OR Serial Number, Used, Remaining, Added Date, Added Time, Status, Added By.

### Abbreviations

- ORAF — Official Receipts & Accountable Forms
- ORAF-RL — Official Receipts & Accountable Forms - Report Logs

---

## 2026-06-17 — ORAF Export Bug Fix + Two-Step Preview Flow

### Root Cause (original bug)

The export submit handler called `closeExportModal()` (which calls `exportForm.reset()`) before the browser had a chance to build the GET query string from the form values. The form fields were cleared to empty/defaults, causing the server's `required` validation to fail and the CSV download never triggered.

### Flow (updated per task)

1. User clicks **Export** on an ORAF row → date-range modal opens (From / To month + year, defaulting to current month).
2. User selects range → clicks **View** → JS fetches `GET /preview?params` → populates paper preview modal.
3. Preview modal shows the batch data in a paper document (Figma 688-4849 style: landscape gray paper, `border-[#333]`, Archivo font, 42px headers + 21px data rows).
4. User clicks **Export CSV** in the preview modal → `window.location.href` to `/export?params` → CSV downloads → modal closes.

### Changes

#### `routes/web.php`
- **New**: `GET /official-receipts-accountable-forms/{formStock}/preview` (`official-receipts-accountable-forms.preview`) — returns JSON `{form_name, form_code, period, batches}`.
- **Export route**: Replaced single `year` param with `from_year` + `to_year`. Query uses `whereBetween` with Carbon date ranges. CSV columns updated to match Figma: Starting OR Serial Number, Ending OR Serial Number, Initial Quantity, Used Forms, Remaining, Added Date, Status, Added By, Remarks.

#### `resources/views/official-receipt-accountable-forms/partials/export-modal.blade.php`
- Redesigned date range selector: two columns (From / To), each with Month select + Year select (dropdown, 2020–current). Both default to current month/year via `now()->month` / `now()->year`.
- Button changed from "Next" → **"View"** (eye icon).

#### `resources/views/official-receipt-accountable-forms/partials/report-preview-modal.blade.php` (new)
- Full-screen dark overlay with scrollable container.
- Top bar: Close button (light/ghost) + Export CSV button (`.ctc-add-entry-btn`).
- Paper document (`.orabf-report-paper`): gray `#d9d9d9` background, `border: 1px solid #333`, matching Figma 688-4849.
  - Header section: Republic of the Philippines / Municipality of Prieto-Diaz, Sorsogon / report title / form name + period.
  - Table: Starting OR Serial Number, Ending OR Serial Number, Initial Quantity, Used Forms, Remaining, Added Date, Status, Added By, Remarks — populated by JS from preview JSON.

#### `resources/views/official-receipt-accountable-forms/index.blade.php`
- Added `@include('official-receipt-accountable-forms.partials.report-preview-modal')`.
- Removed old `exportForm.action` pattern. Export button click now stores `formStockId` on `exportForm.dataset`.
- "View" submit: `preventDefault` → AJAX fetch `/preview` → populate preview modal → `closeExportModal()` → show preview.
- "Export CSV" in preview modal: `window.location.href` to export URL → `closePreviewModal()`.

#### `resources/css/app.css`
- Added `.orabf-preview-overlay`, `.orabf-preview-container`, `.orabf-preview-topbar`, `.orabf-preview-close-btn`, `.orabf-preview-export-btn`, `.orabf-report-paper`, `.orabf-report-doc-header`, `.orabf-report-gov-label`, `.orabf-report-lgu-name`, `.orabf-report-title`, `.orabf-report-meta-row`, `.orabf-report-table`, `.orabf-report-empty`.

### Abbreviations (sorted)

- CM — Collection Management
- CMTE — Transaction Entry
- CMTL — Transaction Logs
- ESN — Ending Serial Number
- LM — Login Module
- ORAF — Official Receipts & Accountable Forms
- ORAF-RL — Official Receipts & Accountable Forms - Report Logs
- RA — Reporting and Abstract
- SSN — Starting Serial Number
- UM — User Management
- UMAU — User Management Add User
- UMLP — User Management Landing Page
- UML — User Management Logs
- UMRP — User Management Roles and Permission

---

## 2026-06-17 — Print CSS Fix: `@page` Rule & Per-Module Print Isolation

### Description / Scenario / Events / Steps

1. **ORAF print was broken after global CSS refactor** — the ORAF `<style>` block had `@page` nested inside `@media print {}`. Browsers silently ignore `@page` when it is not at the top level of a `<style>` block, so the landscape orientation and margin settings were never applied. The result was portrait orientation and large browser-default margins.

2. **Fix: moved `@page` to top level** — `@page { size: 11in 8.5in landscape; margin: 0.4in; }` is now declared at the top of the `<style>` block (above `@media print {}`), making orientation and margins apply correctly.

3. **Per-module isolation** — ORAF's print CSS is entirely self-contained inside `index.blade.php` via `@push('scripts')`. The global `app.css` print block no longer contains any ORAF-specific rules, so changes to `app.css` cannot break ORAF print output.

### Files Modified

- `resources/views/official-receipt-accountable-forms/index.blade.php` — moved `@page` from inside `@media print {}` to the top level of the `<style>` block.

### ORAF Print CSS (`index.blade.php` — `@push('scripts')`)

```css
@page { size: 11in 8.5in landscape; margin: 0.4in; }

@media print {
    body * { visibility: hidden; }
    #reportPrintArea, #reportPrintArea * { visibility: visible; }
    #reportPrintArea {
        position: fixed; top: 0; left: 0; width: 100%;
        border: none !important; background: #fff !important;
    }
    #reportPrintArea .orabf-report-doc-header,
    #reportPrintArea .orabf-report-officer-row,
    #reportPrintArea .orabf-report-table th,
    #reportPrintArea .orabf-report-table td,
    #reportPrintArea .orabf-report-total-row td {
        background: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
```

### Abbreviations

- ORAF — Official Receipts & Accountable Forms

### Notes

- `@page` must be at the top level of a `<style>` block — nesting it inside `@media print {}` is invalid CSS and browsers ignore it silently. This was the root cause of the wrong paper orientation and oversized margins.
- The `body * { visibility: hidden } + #reportPrintArea { position: fixed }` isolation pattern is used here (matching the ORAF report preview approach) because ORAF's print target is a single overlay element, not the full-page container chain used by CM view and CMTE-MC.
