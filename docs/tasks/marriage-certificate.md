# Collection Management — Marriage Certificate (CMTE-MC)

This document tracks all changes made to the Marriage Certificate Transaction Entry module
(`resources/views/collection-management/transaction-entry/marriage-certificate.blade.php`
and related files).

---

## 2026-06-16 — Task 1: Create Marriage Certificate Transaction Entry Page

### Description / Scenario / Events / Steps

1. Create the page for Marriage Certificate (`CM → CMTE → CMTE-MC`) per Figma node 255:1054
   (https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=255-1054&m=dev).

2. The **Proceed** button opens a fullscreen document print preview per Figma node 485:7129
   (https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=485-7129&m=dev).

3. **Print & Save** inside the preview opens an optional email/message send modal per Figma
   node 489:7320
   (https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=489-7320&m=dev).

4. Closing the modal (Cancel or Send) saves the record to the database, decrements FormStock
   `qty`, logs the transaction and activity, triggers `window.print()`, then redirects to
   `/collections`.

### Files Created / Modified

- **Routes** (`routes/web.php`):
  - `GET /collections/transaction-entry/{formStock}/marriage-certificate` — named `transaction-entry.marriage-certificate`
  - `POST /collections/transaction-entry/{formStock}/marriage-certificate` — named `transaction-entry.marriage-certificate.store`; returns JSON `{message, redirect}`.

- **Views** (created):
  - `resources/views/collection-management/transaction-entry/marriage-certificate.blade.php` — main page (side-by-side form + live document preview, JS modal orchestration, print media query).
  - `resources/views/collection-management/transaction-entry/partials/mc-document.blade.php` — shared legal paper fragment (`data-mc-preview` spans for live binding); included in both the inline preview column and the print modal.
  - `resources/views/collection-management/transaction-entry/partials/mc-print-preview-modal.blade.php` — fullscreen overlay (`#mcPreviewOverlay`) with close button and "Print & Save" button.
  - `resources/views/collection-management/transaction-entry/partials/mc-send-modal.blade.php` — centered overlay (`#mcSendOverlay`) with optional email + message fields, Cancel and Send buttons.

- **CSS** (`resources/css/app.css`) — appended all `.mc-*` classes:
  - `.mc-page` (flex row), `.mc-form-col` (668px), `.mc-paper-col`, `.mc-paper` (675×936px).
  - `.mc-doc-body` (Times New Roman 12px, line-height 24px), `.mc-doc-fill` (underlined fill spans).
  - `.mc-preview-overlay` / `.mc-send-overlay` overlay patterns (z-index 1000/1100).
  - Print media query hides everything except `#mcPreviewOverlay`.

- **Model** (`app/Models/MarriageCertificateTransaction.php`) — created with all fillable fields and `formStock(): BelongsTo`.
- **Model** (`app/Models/FormStock.php`) — added `marriageCertificateTransactions(): HasMany`.
- **Migration** (`database/migrations/2026_06_16_100000_create_marriage_certificate_transactions_table.php`) — migrated; creates `marriage_certificate_transactions` table.
- **Partial** (`resources/views/collection-management/transaction-entry/partials/form-stocks-table.blade.php`) — added `'Form 10' => route('transaction-entry.marriage-certificate', $form->id)` to the match statement.

### Abbreviations Used

- CM — Collection Management
- CMTE — Transaction Entry
- CMTE-MC — Transaction Entry - Marriage Certificate

### Notes

- CSS borders used in place of SVG rule lines (Figma-exported SVGs had full-artboard boundary paths making them too large to inline).
- Cancel in the send modal also triggers save + print (per spec: "whether by Cancel or Send, save then open print preview").
- Certificate number is auto-incremented (`max(id) + 1`, zero-padded to 7 digits) and passed to the view on GET.

---

## 2026-06-16 — Task 2: Modal Fixes, Print Preview, ORAF-RL Remaining Count & Document Formatting

### Description / Scenario / Events / Steps

1. **Update CMTE-MC modal** — added `padding-bottom: 24px` to `.mc-preview-card`; changed Print & Save button from `position: absolute` to a flex sibling with `gap: 24px` on the overlay.

2. **Print preview not showing text/data** — root cause: `window.print()` was called after `closePreview()` which removed `.open`, so `<main>` (hidden by `body > * { display: none }`) cascaded its `display: none` to `#mcPreviewOverlay`. Fix: moved `window.print()` before `closePreview()`; rewrote `@media print` to hide `nav`, `.nav-sticky-wrapper`, `.x-header-container`, `.collection-content`, `#mcSendOverlay` individually instead of `body > *`.

3. **ORAF-RL remaining count not updating for Form 10** — `FormBatch::transactionSerialNumbers()` had `default => collect()` for Form 10, so `usedQty()` always returned 0. Fix: added `'Form 10' => $formStock->marriageCertificateTransactions->map(...)` to the match.

4. **Document formatting to match physical form** — applied across multiple iterations:
   - Body text: `font-style: italic`, `text-align: justify`, `text-align-last: left`, `line-height: 2`
   - Fill-in spans: changed from `solid` to `dotted` underline; `font-style: normal` so typed values stand out
   - Title block: `width: fit-content; padding: 0 16px` with `align-self: stretch` rules — hugs text width with 16px breathing room
   - Gap below title block: `margin-bottom: 24px`
   - INSTRUCTION section: wrapped in `.mc-doc-bottom` with `margin-top: auto` inside flex `.mc-paper` — pushed to bottom of page like the physical form
   - INSTRUCTION heading: small `→` arrow decoration below
   - Double rule: darkened to `#333`
   - "As integral parts…" paragraph: `.mc-doc-body--nojustify` (left-aligned) + `white-space: nowrap` on "copies of the [blank]" to keep the fill inline
   - Line height: `1.5` applied to `.mc-doc-line`, `.mc-doc-title-text`, `.mc-doc-sig-row p`, `.mc-doc-instruction-title`
   - Ordinal day suffix: `ordinal()` JS helper auto-appends "st/nd/rd/th" to `witness_day` and `instructions_day` preview fields
   - Print: `@page { size: 8.5in 13in; margin: 0 }`, `.mc-paper` overridden to `8.5in × 13in` with font sizes in `pt` (14pt header, 12pt body)

### Files Modified

- `app/Models/FormBatch.php` — added `'Form 10'` case to `transactionSerialNumbers()`
- `resources/views/collection-management/transaction-entry/marriage-certificate.blade.php` — JS print order (`window.print()` before `closePreview()`), `ordinal()` helper, rewritten `@media print` block with `@page` rule
- `resources/views/collection-management/transaction-entry/partials/mc-document.blade.php` — `.mc-doc-bottom` wrapper for sig-row + instruction section, `→` decoration, "As integral parts" nowrap fix
- `resources/views/collection-management/transaction-entry/partials/mc-print-preview-modal.blade.php` — close button inside card header, Print & Save as flex sibling
- `resources/views/collection-management/transaction-entry/partials/add-batch-modal.blade.php` — ending serial placeholder updated to "Input whole OR serial number"
- `resources/views/collection-management/transaction-entry/partials/form-stocks-table.blade.php` — Add Transaction hidden when `$availableQty == 0`
- `resources/css/app.css` — extensive `.mc-*` updates (overlay layout, card padding, print button, doc formatting, line heights, fill styles, double rule, instruction decoration, bottom wrapper)

### Notes

- The print blank page bug was caused by CSS inheritance: `body > * { display: none }` hid `<main>`, making all its descendants (including the overlay) invisible even when the overlay had `display: block !important`. The fix targets named elements instead.
- `window.print()` must be called while `#mcPreviewOverlay` still has `.open` (display: flex) so the browser renders the document in the print preview.
- `.mc-doc-bottom { margin-top: auto }` only works because `.mc-paper` is `display: flex; flex-direction: column`.
