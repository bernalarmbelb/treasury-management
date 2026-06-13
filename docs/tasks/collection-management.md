# Collection Management

This document tracks all changes made to the Collection Management page
(`resources/views/collection-management/index.blade.php` and related files).

## Summary

Cleaned up the Transaction Logs table on the Collection Management page
(`resources/views/collection-management/index.blade.php`) and moved its
data from a hardcoded array to a database table.

## Changes

1. **Data table** — Transaction Logs table now reads from the
   `transaction_logs` database table via `App\Models\TransactionLog`,
   with search and pagination (10 rows per page).
2. **Row height** — `tbody` rows are now a fixed `37px` tall
   (`resources/css/app.css`).
3. **Database for logs**
   - Migration: `database/migrations/2026_06_12_140401_create_transaction_logs_table.php`
   - Model: `app/Models/TransactionLog.php`
   - Seeder: `database/seeders/TransactionLogSeeder.php` (registered in
     `DatabaseSeeder`), seeded with the previous sample rows so the table
     isn't empty. Can be cleared/re-seeded later with
     `php artisan migrate:fresh --seed`.
4. **Icons** — Added `mallardduck/blade-boxicons` (Boxicons for Blade).
   Replaced inline SVGs with `<x-bx-filter-alt>`, `<x-bx-search>`,
   `<x-bx-x>` (Cancel action), and `<x-bx-show>`.
5. **Actions column** — `.col-actions` now uses `width: 1%` +
   `white-space: nowrap` so it shrinks to fit its content instead of
   stretching.
6. **Filter/search bar alignment** — Filter button, search input, and
   search button are now all `box-sizing: border-box` with a consistent
   `42px` height. The misalignment was actually caused by Bootstrap's
   default `margin-bottom: 16px` on the `<form class="search-group">`
   element, which made the toolbar row taller than the buttons and threw
   off `align-items: center`; fixed by setting `margin: 0` on
   `.search-group`.
7. Added zebra striping and row hover highlighting to the table for a
   cleaner "data table" look, plus an empty-state row.
8. **Cancel action** — the red action button is now "Cancel" (was
   mislabeled "Edit") with an X icon (`.action-cancel`).
9. **Rows per page** — added a "Rows per page" `<select>` (10/25/50/100)
   next to the pagination info, backed by a `per_page` query param.
10. **More seed data** — `TransactionLogSeeder` now also generates 60
    additional randomized rows via Faker (74 total), so pagination and
    the per-page selector can be exercised.

## Follow-ups

- Wire up the Cancel/View action buttons and the filter button to real
  functionality.
- Replace the temporary seeded rows with real transaction data once
  available.

## 2026-06-12 23:50 — Live search

- Fixed filter/search bar misalignment: caused by Bootstrap's default
  `margin-bottom: 16px` on `<form class="search-group">`; resolved with
  `margin: 0`.
- The Transaction Logs table now reloads automatically as the user types
  in the search box (debounced ~300ms), via an AJAX `fetch` to
  `/collections` that returns just the table + pagination partial
  (`resources/views/collection-management/partials/transactions-table.blade.php`).
  The route detects AJAX requests (`X-Requested-With: XMLHttpRequest`) and
  returns the partial instead of the full page
  (`routes/web.php`).
- Added `@stack('scripts')` to `resources/views/components/layout.blade.php`
  so pages can push page-specific JS.

## 2026-06-13 01:30 — Search bar reload investigation

- Diagnosed reports of the page reloading on every search keystroke: two
  duplicate `npm run dev` (Vite) processes were running at once, fighting
  over `public/hot` and triggering Vite's full-reload websocket
  repeatedly. Killed both, restarted a single clean `npm run dev`
  instance on port 5173.
- Verified the live-search AJAX logic itself in an isolated
  `php artisan serve` preview: typing a character fires exactly one
  `fetch` to `/collections?search=...`, updates the table via
  `replaceState`, and triggers zero page reloads.
- If reloads persist for the user, the likely cause is a browser tab
  still holding a stale connection to a now-dead Vite dev server
  instance — recommended closing all tabs and opening a fresh one.
- Renamed `docs/tasks/collection-management-layout-cleanup.md` to
  `docs/tasks/collection-management.md` — this file now tracks all
  Collection Management page changes going forward.

## 2026-06-13 01:35 — Sticky sub-navigation

- The "Transaction Logs | Transaction Entry" tab bar (together with the
  "COLLECTION MANAGEMENT" title and breadcrumb) now sticks as one row
  below the main blue "Collections Management" navigation bar when
  scrolling — same position/layout as before, just pinned.
- Added `.sub-nav-sticky` class to the existing `.x-header-container` in
  `resources/views/collection-management/index.blade.php`. Removed the
  redundant inner `.nav-sticky-wrapper` div (which had its own
  conflicting `position: sticky; top: 0`).
- Added `.sub-nav-sticky` to `resources/css/app.css`:
  `position: sticky; top: 50px;` (50px = main nav bar height),
  `z-index: 999` (below the main nav's `z-index: 1000`), with the page
  background color so content doesn't show through while stuck.
- Removed `position: sticky; top: 0` from `.data-table thead th`
  (`resources/css/app.css`). It was sticking to the top of the viewport
  underneath the new sticky page header, leaving an empty gap where the
  hidden table header used to sit. The page header now provides the
  sticky behavior, so the table header doesn't need its own.
- Added `align-items: center` to `.x-header-container`
  (`resources/css/app.css`). The title block and the "Transaction Logs |
  Transaction Entry" tab were stretched to equal heights, leaving a gap
  above the shorter tab pill; centering them removes that gap.

## 2026-06-13 01:45 — Reposition tab pill to match reference

- Per the provided prototype reference image, the "Transaction Logs |
  Transaction Entry" tab should sit at the bottom of the header area,
  aligned with the breadcrumb row, rather than vertically centered with
  the whole title block.
- Changed `align-items: center` to `align-items: flex-end` on
  `.x-header-container` (`resources/css/app.css`). Tab pill size/height
  unchanged, only its vertical position shifted to the bottom of the
  header row, matching the reference layout.
- Follow-up: user adjusted this further to `align-items: flex-start` on
  `.x-header-container` to get the final correct positioning.

## 2026-06-13 01:55 — Remove sub-nav sticky padding

- User feedback: tab positioning still wasn't right; suggested removing
  the padding on the sticky parent container so the tab pill sits flush
  with the title/breadcrumb container, removing the extra surrounding
  whitespace.
- Removed `padding: 10px 0` from `.sub-nav-sticky` (`resources/css/app.css`).
  The "Transaction Logs | Transaction Entry" tab now spans the full
  height of the sticky header row, flush against the toolbar below,
  matching the reference layout. Verified via preview (non-scrolled and
  scrolled views).

## 2026-06-13 02:05 — Sticky toolbar + table header, pagination spacing

- Filter button, search bar, and the data table header now stay in view
  while the table data is taller than the remaining viewport space.
- `.collection-toolbar` (`resources/css/app.css`) is now
  `position: sticky; top: 133px` (133px = main nav 50px + sub-header
  83px), with its own background so it stays visible above the table as
  the page scrolls.
- Added a new `.table-scroll-area` wrapper around `.table-wrapper`
  (`resources/views/collection-management/partials/transactions-table.blade.php`)
  with `max-height: calc(100vh - 290px); overflow: auto`. `.data-table
  thead th` is now `position: sticky; top: 0` relative to this scroll
  area, so the table header stays pinned while rows scroll underneath it.
  Pagination stays outside this scroll area so it's always visible.
- Added `margin-top: 12px` to `.pagination-bar` to add spacing between
  the table and the pagination controls.
- Verified via preview: with 10 rows per page the table fits without an
  inner scrollbar; with 50 rows per page the table area scrolls
  internally with the header pinned, while the toolbar and page header
  remain fixed and the pagination bar stays visible below with proper
  spacing.

## 2026-06-13 02:30 — Search placeholder text

- Changed the search input placeholder from "Search Transaction" to
  "Search Payee" (`resources/views/collection-management/index.blade.php`),
  since search matches against the payee field.

## 2026-06-13 02:40 — Filter icon to match Figma (first pass)

- Replaced the filter button icon `<x-bx-filter-alt>` (outline) with
  `<x-bxs-filter-alt>` (solid/filled funnel), and increased
  `.filter-btn .icon` size from `20px` to `24px`
  (`resources/css/app.css`).
- Superseded by the next entry: the Figma icon (`mage:filter-fill`,
  node `144:3082`/`144:3085` under
  https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=148-3094)
  is actually a "sliders/adjustments" icon (three horizontal bars with
  round handles), not a funnel.

## 2026-06-13 02:55 — Filter icon, exact Figma match

- Replaced `<x-bxs-filter-alt>` with a new custom icon component
  `resources/views/components/icons/filter-fill.blade.php`, an inline
  SVG matching the exact "mage:filter-fill" path data from the Figma
  filter button (node `148:3094`, "Filter" component, state=default).
  Boxicons has no exact equivalent (closest is `bx-slider-alt`, but it
  has 2 bars instead of 3).
- Used in the filter button as `<x-icons.filter-fill class="icon" />`
  (`resources/views/collection-management/index.blade.php`).
- Set `.filter-btn .icon { color: #fff }` (`resources/css/app.css`) since
  the Figma icon fill is white on the `#B7BBC1` button background (icon
  uses `fill="currentColor"`).
- Verified via preview: filter button now shows the white sliders icon
  on the gray `42x42` button, matching the Figma design.

## 2026-06-13 03:10 — Filter By dialog/modal

- Clicking the filter button now opens a "Filter By" panel matching the
  Figma "filter by" design
  (https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=157-1505),
  positioned as a dropdown below the filter button
  (`.filter-wrapper` / `.filter-panel` in
  `resources/views/collection-management/index.blade.php` and
  `resources/css/app.css`).
- Panel shows the "FILTER BY" title, "Pto. Diaz Treasury Management
  System" subtitle, and four checkboxes (Date, Form Type, Completed,
  Cancelled) plus an "Apply" button, styled per the Figma spec (colors,
  typography, spacing, soft-blue Apply button).
- Functionality: the "Completed" and "Cancelled" checkboxes filter the
  Transaction Logs table by `status` (via a new `status[]` query param,
  applied through the existing AJAX `fetchAndRender` mechanism on
  "Apply"). The filter is preserved across pagination, sorting, the
  per-page selector, and search.
- Backend (`routes/web.php`): `/collections` now accepts `status[]`
  (whitelisted to `Completed`/`Cancelled`) and applies `whereIn('status',
  ...)`. Also fixed a pre-existing query bug where the search
  `where(...)->orWhere(...)` wasn't grouped, which would have caused
  incorrect results when combined with the new status filter.
- The panel closes on "Apply" or on an outside click.
- "Date" and "Form Type" checkboxes are present per the Figma design but
  are not yet wired to filtering — follow-up once the corresponding
  Figma frames for those filters are available.
- Verified via preview: opening the panel, filtering by "Cancelled"
  reduces the table to 41 matching entries and the filter persists when
  navigating to page 2.

## 2026-06-13 03:40 — Filter By dialog refinements + Select Form + breadcrumbs

- Converted the "Filter By" panel from a dropdown to a centered modal
  dialog (`.filter-modal-overlay` / `.filter-panel` in
  `resources/views/collection-management/index.blade.php` and
  `resources/css/app.css`):
  - Backdrop is `#333333` at 25% opacity (`rgba(51, 51, 51, 0.25)`),
    covering the full viewport and darkening the page behind the dialog.
  - Dialog is centered via `position: fixed` flex overlay.
  - Corner radius changed from `20px` to `0px`.
  - Gap between "FILTER BY" and the subtitle reduced from `8px` to `4px`.
  - Checkboxes restyled as flat squares (`appearance: none`, `#D9D9D9`
    unchecked / `#1877F2` checked) to match the Figma look.
- **Select Form section**: checking the "Form Type" checkbox reveals a
  "SELECT FORM" section with two columns of form-type checkboxes (Burial
  /Form 58, Corporation Cedula/BIR0017, Certificate of Ownership of Large
  Cattle/Form 53, Certificate of Transfer of Large Cattle/Form 28A,
  Individual Cedula/BIR0016, Marriage License/Form 10, Official
  Receipt/Form 5IC, OR RPT/Form 56), per the provided reference image.
  Unchecking "Form Type" hides the section and clears its selections.
- **Filter breadcrumbs**: after clicking "Apply", a row of chips appears
  below the filter button showing each active top-level filter (Date,
  Form Type, Completed, Cancelled), each removable via an "×", plus a
  "Clear Filter" button that resets all filters at once
  (`#filterBreadcrumbs`).
- Backend (`routes/web.php`): `/collections` now also accepts
  `form_type[]` (whitelisted to the 8 codes above) and applies
  `whereIn('form_type', ...)`. Note: the seeded `transaction_logs` data
  only uses `Form 01`/`Form 02`/`Form 03`, so filtering by these new
  Figma-defined form types currently returns no rows — this is a data
  follow-up, not a UI bug.
- "Date" checkbox remains a placeholder (chip + breadcrumb support is
  wired, but no date-range UI yet — follow-up once that Figma frame is
  available).
- Verified via preview: modal centers with darkened backdrop, "Select
  Form" section toggles correctly, Apply applies `form_type` filter and
  shows the "Form Type" breadcrumb chip, and "Clear Filter" resets to all
  74 entries.

## 2026-06-13 03:55 — Seed data updated to match new Form Type filters

- `database/seeders/TransactionLogSeeder.php`: replaced the placeholder
  `form_type` values (`Form 01`/`Form 02`/`Form 03`) with the 8 codes
  used by the "Select Form" filter (`Form 58`, `BIR0017`, `Form 53`,
  `Form 28A`, `BIR0016`, `Form 10`, `Form 5IC`, `Form 56`), for both the
  14 hardcoded rows and the 60 Faker-generated rows.
- Ran `php artisan migrate:fresh --seed` to reset and reseed with the
  new data (74 rows total).
- Verified via preview: the "Form Type" column now shows the new codes,
  and applying the "Form Type" filter with "Burial / Form 58" checked
  correctly returns only matching rows (previously returned "No
  transactions found" since no seeded row used `Form 58`).

## 2026-06-13 04:10 — Date filter (Start/End range)

- Checking "Date" in the Filter By modal now reveals a date filter group
  inline in the toolbar, next to the search bar (per the provided
  reference image): "Start"/"End" date inputs and a "Filter" button
  (`.date-filter-group` in
  `resources/views/collection-management/index.blade.php` and
  `resources/css/app.css`).
- Backend (`routes/web.php`): `/collections` now accepts `date_start` and
  `date_end` query params, applied via
  `whereDate('transacted_at', '>=' / '<=')`. Both are optional and can be
  used independently or together.
- Unchecking "Date" (or removing the "Date" breadcrumb chip, or "Clear
  Filter") clears the Start/End inputs, hides the group, and removes the
  filter. The group and its values persist across pagination via hidden
  inputs in
  `resources/views/collection-management/partials/transactions-table.blade.php`.
- Verified via preview: filtering by Start `2026-01-01` / End
  `2026-12-31` correctly reduces the table to the 7 entries dated in 2026,
  and "Clear Filter" restores all 74 entries.
- A "Month" dropdown was initially included alongside Start/End, then
  removed per user feedback in favor of Start/End only.

## 2026-06-13 02:20 — Sortable columns and text-only action buttons

- Table headers (Serial Number, Payee, Date, Time, Form Type, Status) are
  now clickable and sort the table via the existing AJAX live-search
  mechanism, with a sort-direction arrow icon (`bx-sort-alt-2` /
  `bx-sort-up` / `bx-sort-down`) next to each label. Clicking toggles
  ascending/descending; Date and Time both sort by `transacted_at`.
- Backend (`routes/web.php`): `/collections` now accepts `sort` and
  `direction` query params (whitelisted to `serial_number`, `payee`,
  `transacted_at`, `form_type`, `status`; direction `asc`/`desc`,
  default `transacted_at` desc), applied via `orderBy()`. Both values are
  passed to the view and preserved across pagination via
  `withQueryString()`.
- Frontend
  (`resources/views/collection-management/partials/transactions-table.blade.php`):
  added a `$sortLink`/`$sortIcon` helper per column and wrapped each
  header label in an `<a class="sortable-header">`.
- JS (`resources/views/collection-management/index.blade.php`):
  refactored the AJAX reload into a shared `fetchAndRender(params)`, and
  added a delegated click handler on the table container for
  `.sortable-header` links that merges the clicked column's `sort`/
  `direction` into the current query params (preserving search) before
  reloading.
- Action buttons in the Actions column are now text-only ("Cancel" /
  "View", `.action-btn`), icons removed for a cleaner look.
- Verified via preview: clicking "Payee" sorts ascending then descending
  with the arrow icon updating accordingly, and sorting combines
  correctly with an active search term.

## 2026-06-13 04:20 — Layout summary

Current layout implemented for the Collection Management page
(`resources/views/collection-management/index.blade.php` +
`resources/views/collection-management/partials/transactions-table.blade.php`):

**1. Sticky page header (`.x-header-container.sub-nav-sticky`)**
- "Collection Management" title + "Home | Transaction Logs" breadcrumb
- "Transaction Logs | Transaction Entry" tab pill, aligned flush at the
  bottom-right of the header
- Sticks below the main blue nav bar while scrolling

**2. Toolbar (`.collection-toolbar`, sticky)**
- Filter button (`#filterToggleBtn`, sliders icon matching Figma
  `mage:filter-fill`)
- Search form: "Search Payee" input + "Search" button
- Date filter group (`#dateFilterGroup`, hidden until "Date" is checked in
  Filter By): Start date input, "-", End date input, "Filter" button

**3. Filter breadcrumbs (`#filterBreadcrumbs`)**
- Row of removable chips for active filters (Date, Form Type, Completed,
  Cancelled) + "Clear Filter" button, shown only when filters are active

**4. Filter By modal (`.filter-modal-overlay` / `.filter-panel`)**
- Centered overlay dialog, `#333` @ 25% backdrop, 0px corner radius
- Header: "FILTER BY" title + "Pto. Diaz Treasury Management System"
  subtitle
- Checkbox row: Date, Form Type, Completed, Cancelled (flat-square custom
  checkboxes)
- "Select Form" section (revealed when "Form Type" checked): two-column
  grid of form-type checkboxes (name + code)
- "Apply" button, right-aligned

**5. Data table (`.table-scroll-area` / `.table-wrapper` / `.data-table`)**
- Sortable column headers (Serial Number, Payee, Date, Time, Form Type,
  Status) with sort-direction icons, sticky header row
- Zebra-striped rows, fixed 37px row height, hover highlight
- Actions column: "Cancel" / "View" text buttons, centered header,
  shrink-to-fit width
- Empty state row ("No transactions found")
- Internal scroll area so toolbar/header stay visible when rows overflow
  viewport

**6. Pagination bar (`.pagination-bar`)**
- Left: "Showing X to Y of Z entries" + "Rows per page" selector
  (10/25/50/100)
- Right: Previous/page numbers/Next controls
- All filter state (search, status[], form_type[], date_start, date_end,
  sort/direction) preserved via hidden inputs and `withQueryString()`

**JS behavior**: AJAX live search/sort/filter via shared
`fetchAndRender()`, replacing `#transactions-table-container` and updating
the URL without full page reloads.
