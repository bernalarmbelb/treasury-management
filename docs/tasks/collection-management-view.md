# Collection Management — View, Archive & Cancel Request

This document tracks changes related to the CM transaction view page, archive action, and cancel-request flow.

## 2026-06-17 — View Page, Archive Button, Cancel Request

### Overview

Three features added to Collection Management Transaction Logs:

1. **View Page** — each row's View button navigates to a read-only view of the transaction, formatted per its form type and printable on 8.5×11 bond paper.
2. **Archive Button** — only shown for Cancelled transactions; archives the log (sets `archived_at`).
3. **Cancel Request** — non-admin users can submit a cancellation request from the table row or from the view page; stored in `cancel_requests` with `status = pending`.

---

### Migrations

| File | Purpose |
|------|---------|
| `2026_06_17_100000_add_polymorphic_and_archived_at_to_transaction_logs.php` | Adds `transaction_id`, `transaction_type` (polymorphic morph), and `archived_at` to `transaction_logs` |
| `2026_06_17_110000_create_cancel_requests_table.php` | Creates `cancel_requests` table |

### Models

| Model | Change |
|-------|--------|
| `TransactionLog` | Added `transaction()` (MorphTo), `cancelRequests()` (HasMany), `formName()` static helper; new fillable: `transaction_id`, `transaction_type`, `archived_at` |
| `CancelRequest` (new) | `transaction_log_id`, `requested_by`, `reason`, `status`, `reviewed_by`, `reviewed_at` |

### Routes

All 5 transaction store routes updated to capture the created model and save its id+type into TransactionLog (polymorphic link):
- `transaction-entry.individual-cedula.store` → links `CtcIndividualTransaction`
- `transaction-entry.corporation-cedula.store` → links `CtcCorporationTransaction`
- `transaction-entry.or-rpt.store` → links `OrRptTransaction`
- `transaction-entry.official-receipt.store` → links `OrTransaction`
- `transaction-entry.marriage-certificate.store` → links `MarriageCertificateTransaction`

New routes (placed after all `/collections/transaction-entry/*` to avoid parameter conflicts):
- `GET /collections/{log}` → `collections.view` — view page
- `POST /collections/{log}/archive` → `collections.archive`
- `POST /collections/{log}/cancel-request` → `collections.cancel-request`

### Views

| File | Change |
|------|--------|
| `collection-management/view.blade.php` (new) | View page; renders form-specific read-only layout via `@switch($log->form_type)` — handles BIR0016, BIR0017, Form 56, Form 5IC, Form 10, generic fallback; includes Print and Request to Cancel buttons |
| `collection-management/partials/transactions-table.blade.php` | View → links to `collections.view`; Cancel → opens cancel-request modal (data-id, data-serial, data-payee); Archive → shown only for Cancelled + not archived, fires POST to `/collections/{id}/archive` |
| `resources/css/app.css` | Added `.ctc-view-*`, `.ctc-modal-*`, `.action-archive`, `.ctcp-radio.checked`, print `@media` |

### Design

- **Print**: `@page { size: 8.5in 11in }`, header/actions hidden on print
- **Request to Cancel button**: `border: 1px solid var(--primary, #427AB5); background: rgba(66,122,181,0.08);`
- **Button font**: Manrope (per project button rule)
- **Actions column**: Completed → Cancel + View; Cancelled (unarchived) → Archive + View; Cancelled (archived) → View only

### Notes

- Old `TransactionLog` rows (before this migration) have `transaction_id = null` — view page falls back to a generic info card for those.
- Cancel request approval workflow (admin side) is a separate future task.
- OR RPT table rows are not persisted in the database (entered client-side only); view shows empty table with a note.

---

## 2026-06-17 — Print CSS Fix: Per-Module Isolation, Correct Paper Size & Font Sizes

### Description / Scenario / Events / Steps

1. **Global `app.css` print rules were destroying other modules** — the global `@media print` block in `app.css` contained paper-specific overrides (including `@page` and `.mc-paper` size rules) that conflicted with CMTE-MC and broke its print layout whenever the global CSS was updated.

2. **Root cause: `@page` inside `@media print {}` is invalid CSS** — browsers silently ignore `@page` when it is nested inside a `@media` rule. `@page` must be declared at the top level of a `<style>` block. This caused wrong paper size, wrong margins, and wrong orientation across modules.

3. **Fix: each module now owns its print CSS** — all paper-specific print rules removed from `app.css`. The global print block in `app.css` is now stripped to only truly global rules (hide nav bars and page chrome). Every module with printing declares its own `@page` and paper-specific rules in a per-page `<style>` block via `@push('scripts')`.

4. **CM view print approach mirrors CMTE-MC** — the view page uses `display: none` on unwanted elements (same as CMTE-MC, not the `visibility: hidden + position: fixed` approach), zeros the container chain (`main-container`, `ctc-view-page`, `ctc-view-doc-wrap`), and gives `.mc-paper` the exact same dimensions as CMTE-MC (`8.5in × 13in, padding 0.5in`). Font size rules match CMTE-MC exactly (`12pt` body/fill/sig, `14pt` for `--lg` and `.mc-doc-no`).

### Files Modified

- `resources/views/collection-management/view.blade.php` — added `@push('scripts')` block with a `<style>` tag containing the full per-module print CSS (see CSS block below).
- `resources/css/app.css` — stripped global `@media print` to only hide nav/chrome elements; removed all paper-specific rules.

### Global `app.css` Print Block (final state)

```css
@media print {
    .navigation-header,
    .nav-sticky-wrapper,
    .x-header-container,
    .collection-toolbar,
    .filter-breadcrumbs,
    .ctc-view-meta,
    .ctc-view-actions,
    .ctc-modal-overlay,
    .pagination-bar {
        display: none !important;
    }
    .collection-content,
    .ctc-view-page {
        padding: 0 !important;
        gap: 0 !important;
    }
}
```

### CM View Print CSS (`view.blade.php` — `@push('scripts')`)

```css
@page { size: 8.5in 13in; margin: 0; }

@media print {
    /* Zero the container chain — matches CMTE-MC's .main-container reset */
    .main-container {
        padding: 0 !important;
        margin: 0 !important;
        background: #fff !important;
        border: none !important;
    }

    .ctc-view-page {
        padding: 0 !important;
        gap: 0 !important;
    }

    .ctc-view-doc-wrap,
    .ctc-view-doc-wrap--mc,
    .ctc-view-doc-wrap--rpt,
    .ctc-view-doc-wrap--or {
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .ctc-preview-caption { display: none !important; }

    /* Form 10 — matches CMTE-MC mc-paper print rules exactly */
    .mc-paper {
        width: 8.5in !important;
        min-height: 13in !important;
        display: flex !important;
        flex-direction: column !important;
        border: none !important;
        background: #fff !important;
        padding: 0.5in !important;
        box-sizing: border-box !important;
        margin: 0 !important;
    }

    /* BIR / RPT / OR forms */
    .ctcp-page,
    .ctcp-rpt-receipt,
    .ctc-or-col {
        border: none !important;
        box-shadow: none !important;
        background: #fff !important;
    }

    /* Form 10 font sizes — identical to CMTE-MC print rules */
    .mc-doc-body { text-align: justify !important; font-size: 12pt !important; }
    .mc-doc-body--sm { text-align: justify !important; font-size: 12pt !important; }
    .mc-doc-title-rule { width: 100% !important; }
    .mc-doc-title-block { width: 100% !important; }
    .mc-doc-line--lg { font-size: 14pt !important; }
    .mc-doc-line { font-size: 12pt !important; }
    .mc-doc-title-text { font-size: 12pt !important; }
    .mc-doc-title-text p { font-size: 12pt !important; }
    .mc-doc-instruction-title { font-size: 12pt !important; }
    .mc-doc-fill { font-size: 12pt !important; }
    .mc-doc-sig-right,
    .mc-doc-sig-left { font-size: 12pt !important; }
    .mc-doc-no { font-size: 14pt !important; }
}
```

### Abbreviations

- CM — Collection Management
- CMTE-MC — Transaction Entry - Marriage Certificate (reference implementation for print)

### Notes

- `@page` must be at the top level of a `<style>` block — nesting it inside `@media print {}` is invalid CSS and browsers ignore it silently.
- The `display: none` approach (zeroing the container chain) is preferred over `body * { visibility: hidden } + position: fixed` — the latter breaks layout when content uses flex/block stacking.
- CMTE-MC (`marriage-certificate.blade.php`) is the reference print implementation for Form 10; CM view matches it exactly (same `@page` rule, same `.mc-paper` dimensions, same font sizes).
