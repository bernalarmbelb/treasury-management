# Login Module (LM)

## 2026-06-16 — Creation of LOGIN MODULE (LM)

# Tasks:
- Creation of LOGIN MODULE (LM)
- UM -> Update of YES Button in the modal of DISABLE, ALL "YES" Button should
  stay blue, even in ARCHIVE modal. (documented separately in
  `user-management.md`, "Correction: 'Yes' button color reverted to
  always-blue")

## Abbreviation
1. Collection Management -> CM
2. Transaction Entry -> CMTE
3. Transaction Logs -> CMTL
4. Official Receipts & Accountable Forms -> ORAF
5. Starting Serial Number -> SSN
6. Ending Serial Number -> ESN
7. User Management -> UM
8. User Management Landing Page -> UMLP
9. User Management Logs -> UML
10. User Management Roles and Permission -> UMRP
11. User Management Add User -> UMAU
12. Login Module -> LM

## Description / Scenario / Events / Steps:
1. Create the login page implementing the Figma design at node `25:958`
   (`https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=25-958&m=dev`),
   maintaining the Figma design and layout (per rule 10).
2. Extract Figma assets (illustrations, seal, avatar) into
   `resources/assets/login/` per rule 17 — SVG where possible, PNG otherwise.
3. Add `.login-*` styles to `resources/css/app.css` and a standalone
   `resources/views/login.blade.php` (not using the main `x-layout`, since the
   login page has its own full-page design).
4. Add a `/login` named route.

## Update
1. Fix visible cutout artifacts (white/gray rectangles) in the 6 exported
   login illustration SVGs (`cabinet.svg`, `shadow.svg`, `streetlight.svg`,
   `speech-bubble.svg`, `character.svg`, `plant.svg`).
2. Change the fonts inside the login fields (`.login-input`) and button
   (`.login-submit`) to Manrope.
3. Fix a blue square visible behind the seal/logo image
   (`resources/assets/login/seal.png` had an opaque blue-gradient background
   baked into the flattened Figma export).
4. Fix login page centering/layout: final requirement is a full-bleed,
   no-letterbox layout where `.login-left` is exactly 50% width of the
   viewport — "half blue half white", matching the Figma design's color
   split, at any viewport size (no white space on the sides).

## Notes
- A TEMPORARY `/logout` route (`routes/web.php`) and "Logout" link
  (`resources/views/components/layout.blade.php`, top-right of the nav
  header) were added so the login page can be reached for QA, since the
  `AutoLogin` middleware (`app/Http/Middleware/AutoLogin.php`) auto-logs in
  seeded user id=1 on every request and there is currently no real auth flow.
  Both are marked `// TEMPORARY` / `{{-- TEMPORARY --}}` in source and should
  be removed once a real login flow (`POST /login` handling the
  `.login-card` form) is implemented.
- The "Sign In" button (`.login-submit`) currently links to `route('home')`
  as a placeholder — no authentication logic is wired up yet.

## Resolution

**Figma source**: node `25:958`
(`https://www.figma.com/design/zKN3sT9cEm13slzJrAD5XU/Prototype?node-id=25-958&m=dev`).

**Route** (`routes/web.php`):
```php
Route::get('/login', function () { return view('login'); })->name('login');
```

**View** (`resources/views/login.blade.php`): standalone Blade file (own
`<html>`/`<head>`/`<body>`, loads `app.css`/`app.js` via `@vite` directly —
does not use `x-layout`), containing:
- `.login-wrapper` > `.login-page` (full-bleed page container)
- `.login-left` (blue panel, 50% width): `.login-seal`, `.login-heading` >
  `.login-title` / `.login-subtitle`
- 6 `.login-illustration` elements (cabinet, shadow, streetlight, speech,
  character, plant) positioned per Figma
- `.login-card` (white card, right side): `.login-avatar`, two
  `.login-input` fields (username/password), `.login-remember` /
  `.login-checkbox`, `.login-submit` ("Sign In" CTA)

**Assets** (`resources/assets/login/`): `seal.png`, `avatar.png`,
`cabinet.svg`, `shadow.svg`, `streetlight.svg`, `speech-bubble.svg`,
`character.svg`, `plant.svg` — extracted from Figma via `download_assets`.

**CSS** (`resources/css/app.css`, "Login page (LM)" section): all
`.login-*` rules added, including:
- `.login-wrapper { position: fixed; inset: 0; background-color: #fff; }` /
  `.login-page { position: relative; width: 100%; height: 100%; overflow:
  hidden; background-color: #fff; }` — full-bleed, no letterbox; combined
  with `.login-left { width: 50%; }` this produces an exact half-blue/
  half-white split at any viewport size, per the user's final layout
  instruction (superseding an earlier Figma-1440:900-aspect-ratio letterbox
  attempt that produced unwanted whitespace).
- `.login-input` / `.login-submit`: `font-family: 'Manrope', sans-serif`
  (per "## Update" item 2).

**Bug fixes applied during this round**:
- Removed two extraneous `<rect>` elements (a `#CCCCCC` 50%-opacity
  selection-highlight rect and a `1440x900 white` background rect) from each
  of the 6 illustration SVGs — these were Figma export artifacts causing
  visible white/gray box "cutouts" (per "## Update" item 1).
- Replaced `seal.png` with the raw Figma source image (transparent
  background) instead of the flattened export (which had an opaque
  blue-gradient background baked in), fixing a visible blue square behind the
  seal/logo (per "## Update" item 3).
- Iterated on `.login-wrapper`/`.login-page` layout (3 revisions) to arrive
  at the final full-bleed 50/50 split (per "## Update" item 4).

**Verification**: Verified via Claude Preview (`preview_eval` — computed
styles, bounding rects, asset fetch status codes; `preview_console_logs` for
errors). `.login-left` measured at exactly 50.00% width at 1440x900 and
1920x1080. All 6 SVG assets confirmed free of export artifacts.
`preview_screenshot` continued to time out (pre-existing tooling issue), so
visual confirmation relied on the user's own screenshots plus
`preview_eval`/fetch checks.

**Resolution**: LOGIN MODULE (LM) creation, the "## Update" cutout/font/seal
fixes, and the final full-bleed layout — confirmed by the user ("confirmed").

## Tasks (append per rule 11)
- [x] Download Figma login page assets to `resources/assets/login`
- [x] Build `login.blade.php` matching Figma layout
- [x] Add login CSS to `app.css`
- [x] Add `/login` route
- [x] Fix SVG cutout artifacts (6 illustrations)
- [x] Change `.login-input`/`.login-submit` fonts to Manrope
- [x] Fix blue square behind seal image
- [x] Fix login page centering -> final full-bleed 50/50 layout
- [x] Verify login page via Claude Preview
- [ ] Remove temporary `/logout` route and "Logout" nav link once real auth
      flow is implemented
