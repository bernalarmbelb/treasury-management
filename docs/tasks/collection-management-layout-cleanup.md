# Collection Management Layout Cleanup

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
   `<x-bx-edit-alt>`, and `<x-bx-show>`.
5. **Actions column** — `.col-actions` now uses `width: 1%` +
   `white-space: nowrap` so it shrinks to fit its content instead of
   stretching.
6. **Filter/search bar alignment** — Filter button, search input, and
   search button are now all `box-sizing: border-box` with a consistent
   `42px` height so they line up evenly.
7. Added zebra striping and row hover highlighting to the table for a
   cleaner "data table" look, plus an empty-state row.

## Follow-ups

- Wire up the Edit/View action buttons and the filter button to real
  functionality.
- Replace the temporary seeded rows with real transaction data once
  available.
