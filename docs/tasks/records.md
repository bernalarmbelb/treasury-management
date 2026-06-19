# Records (RC)

## 2026-06-19 — Module creation: system-wide activity log viewer

**Context**: `new-task.md` requested a "Records" module — described as "records all
activity within the system, somewhat like the UML [User Management Logs]" — with
detailed log entries (e.g. "USER MANAGEMENT - Archive User - name-of-user", or for
forms, the form name and code). The `/records` route already existed as an empty
stub (`view('records.index')`, no content) and the `ActivityLog` model already
recorded every action system-wide via `ActivityLog::record(string $action)`, but
every call site used a generic, undetailed action string.

### Records page (mirrors the UM Logs pattern, reuses the shared `.data-table`)
- `routes/web.php`: replaced the stub `/records` route with full
  paginated/sortable/searchable list logic (same shape as `/user-management/logs`),
  plus a **Module filter** — `$modules` is derived dynamically by splitting every
  distinct `activity_logs.action` on `" - "` and taking the prefix (currently
  yields "Collection Management" and "User Management"), so new modules that call
  `ActivityLog::record()` automatically appear as filter options with no code
  change needed. Added `/records/export` (named `records.export`).
- `resources/views/records/index.blade.php` — header, search box, Module filter
  `<select>`, AJAX table container, "Export Log" link. JS mirrors the UM Logs
  debounced-search/sortable-header/AJAX pattern, plus keeps the Export Log link's
  `href` in sync with the current search/module filters.
- `resources/views/records/partials/records-table.blade.php` — columns: Name,
  Activity Log, Date, Time. Sortable, paginated — identical structure to
  `user-management/partials/logs-table.blade.php`.
- `resources/css/app.css`: `.module-filter-select` (new, matches `.search-input`
  height/border/radius); `.um-export-btn` lightened from solid dark `#333` to the
  ghost-blue style already used system-wide (this also affects the existing UM
  Logs "Export Log" button, for consistency).

### Activity log detail retrofit (`routes/web.php`)
All 21 `ActivityLog::record()` call sites across Collection Management and User
Management were rewritten to append the specific target instead of a generic
action string:
- **CM "Add Entry"** (5 call sites — Individual Cedula, Corporation Cedula,
  OR RPT, Official Receipt, Marriage Certificate): now appends
  `\App\Models\TransactionLog::formName($formStock->form_code)` + the serial
  number, e.g. `'Collection Management - Add Entry - Individual Cedula - BIR0016 0042'`.
- **CM Archive / Cancel / Unarchive Transaction**, **Request Cancel**: now append
  the form name (via `TransactionLog::formName($log->form_type)`) + `$log->payee`
  + `$log->serial_number`.
- **CM Reject Cancel Request**: additionally appends `requested by {name}`,
  resolved from `$cancelRequest->requestedByUser?->name` (the `CancelRequest`
  model already had this `belongsTo` relation) — this was specifically flagged
  by the user after noticing the recorded entry didn't say who made the original
  request or for what transaction.
- **CM Bulk Archive / Bulk Cancel / Bulk Request Cancel**: append a count, e.g.
  `'... - 3 transaction(s)'`. (`Bulk Request Cancel` required reordering the
  `$submitted` count calculation to happen *before* the `record()` call, since it
  was previously computed afterward.)
- **UM Add/Edit/Disable/Activate/Archive/Unarchive User, Reset Password**: all
  append `$user->name`.
- **UM Change Permission**: appends the comma-separated names of every role that
  had at least one permission field actually updated (tracked via a new
  `$updatedRoleIds` array built inside the existing update loop), falling back to
  `'No changes'` if nothing was modified.
- **Caveat (communicated to user)**: rows already in `activity_logs` before this
  fix retain their old, generic action text — only new actions going forward use
  the richer format. The user accepted this and did not request a backfill.

### Export format: styled `.xlsx` instead of plain `.csv`
The user shared a screenshot of a CSV opened in Excel with a green header row
(bold white text + filter dropdowns) and alternating light-green row stripes, and
asked whether the Records/UM Logs export could be "pre-formatted" the same way.
Since plain CSV has no styling capability at all, this required switching the
export format:
- Added `phpoffice/phpspreadsheet` via Composer (confirmed with the user first,
  since it's a new dependency).
- `routes/web.php`: new shared helper `export_activity_log_xlsx(Collection $rows,
  string $filename)` — builds the header row with `538135` (green) fill + white
  bold text, an `AutoFilter` over the full data range, alternating `E2EFDA`
  (light green) fills on every even data row, auto-sized columns, and a frozen
  header row. Returns a `StreamedResponse` with the correct
  `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet` content
  type.
- Both `/records/export` and `/user-management/logs/export` now call this helper
  instead of hand-rolling `fputcsv`, downloading `records.xlsx` /
  `user-management-logs.xlsx` respectively.
- Date format in the export changed from `June 19, 2026` to `19-Jun-26`, and time
  stays `h:i A` (e.g. `1:17 PM`), matching the reference screenshot's style.

### Verification
- `php -l routes/web.php` — clean on every edit round.
- `php artisan route:list --name=records` / `--name=export` — confirmed
  `records`, `records.export`, and `user-management.logs.export` all registered.
- Tested `export_activity_log_xlsx()` directly via `php artisan tinker` with two
  sample rows, wrote the streamed output to a real `.xlsx` file, then re-opened it
  with `PhpOffice\PhpSpreadsheet\IOFactory::load()` and confirmed: header cell
  values, header fill color (`538135`), header bold (`true`), first data row's
  stripe fill color (`E2EFDA`), and the `AutoFilter` range all matched
  expectations. Test file deleted after verification.
- `preview_screenshot` was unavailable throughout (broken for this whole
  session) — all visual confirmation was done by the user directly in their
  browser.

## Tasks (append per rule 11)
- [x] Build `/records` as a system-wide activity log viewer reusing the shared
      `.data-table`, with search, sort, pagination, and a Module filter
- [x] Add `/records/export`
- [x] Retrofit all 21 `ActivityLog::record()` calls with specific detail
      (names, form types, serial numbers, counts, role names)
- [x] Add the original requester's name to "Reject Cancel Request" specifically,
      per user follow-up feedback
- [x] Lighten `.um-export-btn` to match the system's ghost-blue style
- [x] Add `phpoffice/phpspreadsheet` and switch both Records and UM Logs export
      from plain `.csv` to a styled `.xlsx` (green header + filters + row
      stripes), matching the user's reference screenshot
- [x] Verify the generated `.xlsx` round-trips correctly via PhpSpreadsheet

**Resolution**: Confirmed by the user ("done").
