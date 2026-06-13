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
