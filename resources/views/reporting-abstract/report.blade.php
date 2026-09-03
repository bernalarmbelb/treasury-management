<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef1f5; font-family: 'Manrope', system-ui, sans-serif; color: #1f2733; padding: 24px; }
        .sheet { max-width: 980px; margin: 0 auto; }
        .toolbar { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; background: #fff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 14px 16px; margin-bottom: 18px; }
        .toolbar .fld { display: flex; flex-direction: column; gap: 4px; }
        .toolbar label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6b7685; }
        .toolbar .range-arrow { display: flex; align-items: center; height: 40px; color: #9ca3af; }
        .toolbar .range-arrow svg { width: 16px; height: 16px; }

        /* Custom dropdown — mirrors the app's .assigned-to-* idiom, scaled up. */
        .ram-dd { position: relative; }
        .ram-dd-trigger { display: flex; align-items: center; justify-content: space-between; gap: 10px; height: 40px; padding: 0 12px; border: 1px solid #d0d8e4; border-radius: 8px; background: #fff; font-family: inherit; font-size: 13.5px; color: #1f2733; cursor: pointer; transition: border-color .15s ease, box-shadow .15s ease; }
        .ram-dd-trigger:hover { border-color: #427AB5; }
        .ram-dd.open .ram-dd-trigger { border-color: #427AB5; box-shadow: 0 0 0 3px rgba(66,122,181,.14); }
        .ram-dd-value { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500; }
        .ram-dd-chev { width: 15px; height: 15px; flex-shrink: 0; color: #8893a3; transition: transform .18s ease; }
        .ram-dd.open .ram-dd-chev { transform: rotate(180deg); }
        .ram-dd-menu { position: absolute; top: calc(100% + 6px); left: 0; z-index: 30; display: none; flex-direction: column; gap: 2px; min-width: 100%; width: max-content; max-height: 280px; overflow-y: auto; padding: 6px; background: #fff; border: 1px solid #e1e7ef; border-radius: 10px; box-shadow: 0 12px 30px rgba(20,30,50,.16); }
        .ram-dd.open .ram-dd-menu { display: flex; }
        .ram-dd-opt { display: flex; align-items: center; gap: 8px; width: 100%; border: none; background: none; padding: 9px 11px; border-radius: 7px; text-align: left; font-family: inherit; font-size: 13.5px; color: #1f2733; cursor: pointer; white-space: nowrap; }
        .ram-dd-opt:hover { background: rgba(66,122,181,.08); }
        .ram-dd-opt.sel { background: rgba(66,122,181,.12); font-weight: 600; }
        .ram-dd-tick { width: 14px; height: 14px; flex-shrink: 0; color: #427AB5; opacity: 0; }
        .ram-dd-opt.sel .ram-dd-tick { opacity: 1; }

        .toolbar .go { height: 38px; padding: 0 18px; border: none; border-radius: 8px; background: #427AB5; color: #fff; font-family: inherit; font-weight: 600; font-size: 13.5px; cursor: pointer; }
        .toolbar .spacer { flex: 1 1 auto; }
        .toolbar .print { height: 38px; padding: 0 18px; border: 1px solid #427AB5; border-radius: 8px; background: rgba(66,122,181,.08); color: #427AB5; font-family: inherit; font-weight: 600; font-size: 13.5px; cursor: pointer; }
        .toolbar .export { height: 38px; padding: 0 20px; border: none; border-radius: 8px; background: #427AB5; color: #fff; font-family: inherit; font-weight: 700; font-size: 13.5px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; }

        .doc { background: #fff; border: 1px solid #E3E8EF; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 48px rgba(15, 23, 35, .12); }
        .doc-header { padding: 24px 28px 18px; text-align: center; background: linear-gradient(180deg, rgba(66,122,181,.08), rgba(66,122,181,0) 90%); border-bottom: 1px solid #E3E8EF; }
        .doc-header .agency { font-size: 12px; color: #6b7685; margin: 0; line-height: 1.4; }
        .doc-header .agency:last-of-type { font-weight: 700; color: #1f2733; margin-bottom: 8px; }
        .doc-header .office-note { font-size: 11px; font-style: italic; color: #6b7685; margin: 0 0 10px; }
        .doc-header h1 { font-size: 16px; font-weight: 800; letter-spacing: .03em; text-transform: uppercase; margin: 0 0 5px; }
        .doc-header .office { font-size: 12px; color: #6b7685; margin: 0 0 3px; }
        .doc-header .period { font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; color: #427AB5; margin: 0; }

        .cert-block { padding: 22px 28px 6px; }
        .cert-label { font-size: 12.5px; color: #1f2733; margin: 0 0 10px; }
        .cert-name { display: block; font-family: inherit; font-weight: 700; font-size: 13.5px; color: #1f2733; border: 1px solid transparent; border-radius: 6px; padding: 3px 6px; margin: 0 0 2px -7px; background: transparent; min-width: 220px; }
        .cert-name:hover { border-color: #e1e7ef; }
        .cert-name:focus { outline: none; border-color: #427AB5; box-shadow: 0 0 0 3px rgba(66,122,181,.14); background: #fff; }
        .cert-role { font-size: 11.5px; color: #6b7685; }

        .officer-row { display: flex; border-bottom: 1px solid #E3E8EF; }
        .officer-col { flex: 1; padding: 12px 28px; display: flex; flex-direction: column; gap: 3px; }
        .officer-col + .officer-col { border-left: 1px solid #E3E8EF; align-items: flex-end; text-align: right; }
        .officer-name { font-family: inherit; font-size: 12.5px; font-weight: 700; color: #1f2733; width: 100%; border: none; border-bottom: 1px dashed #b9c2cf; background: transparent; padding: 0 0 3px; }
        .officer-col + .officer-col .officer-name { text-align: right; }
        .officer-name:focus { outline: none; border-bottom-color: #427AB5; }
        .officer-name::placeholder { color: #9aa5b4; font-style: italic; font-weight: 400; }
        .officer-label { font-size: 10px; color: #8893a3; text-transform: uppercase; letter-spacing: .04em; }

        .section-heading { margin: 18px 28px 8px; font-size: 11px; font-weight: 800; color: #6b7685; text-transform: uppercase; letter-spacing: .05em; }
        .table-wrap { overflow-x: auto; margin: 18px 28px 0; border: 1px solid #E3E8EF; border-radius: 8px; }
        .doc table { width: 100%; border-collapse: collapse; font-size: 11.5px; }
        .doc th, .doc td { border-bottom: 1px solid #E3E8EF; border-right: 1px solid #E3E8EF; padding: 8px; }
        .doc th:last-child, .doc td:last-child { border-right: none; }
        .doc th { background: #f4f6f9; color: #6b7685; font-weight: 700; font-size: 10.5px; text-transform: uppercase; letter-spacing: .03em; text-align: center; height: 36px; }
        .doc td { text-align: center; font-variant-numeric: tabular-nums; }
        .doc tbody tr:nth-child(even) td { background: #f8fafc; }
        .doc td.num, .doc th.num { text-align: right; }
        .doc td.left, .doc th.left { text-align: left; }
        .doc .empty-row td { text-align: center; color: #6b7685; font-style: italic; padding: 24px; }
        .doc tbody tr.marker-row td { font-weight: 700; font-style: italic; background: #f8fafc; }
        .doc tfoot td { font-weight: 800; background: rgba(66,122,181,.08) !important; }

        .doc-foot { height: 22px; }

        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .doc { border: none; border-radius: 0; box-shadow: none; }
            .officer-name { border-bottom-color: #333; }
            .cert-name { border-color: transparent !important; box-shadow: none !important; background: transparent !important; }
        }
    </style>
</head>
<body>
<div class="sheet">
    @php
        $isRaaf = $slug === 'raaf';
        $exportRouteUrl = $isRaaf
            ? route('reporting-abstract.raaf-export', [], false)
            : route('reporting-abstract.export', ['report' => $slug], false);
        $formActionUrl = $isRaaf
            ? route('reporting-abstract.raaf-report', [], false)
            : route('reporting-abstract.report', ['report' => $slug], false);
    @endphp
    <form class="toolbar" method="GET" action="{{ $formActionUrl }}">
        @php
            $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            $chev = '<svg class="ram-dd-chev" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 7.5 10 12.5 15 7.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            $tick = '<svg class="ram-dd-tick" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 10.5 8.5 15 16 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            $arrow = '<svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 10h11M10.5 5.5 15 10l-4.5 4.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        @endphp

        @if ($isRaaf)
            <div class="fld">
                <label>Officer</label>
                <div class="ram-dd" data-dd>
                    <input type="hidden" name="officer" value="{{ $officerName }}">
                    <button type="button" class="ram-dd-trigger" style="min-width:220px" aria-haspopup="listbox" aria-expanded="false">
                        <span class="ram-dd-value">{{ $officerName ?: 'Select officer…' }}</span>
                        {!! $chev !!}
                    </button>
                    <div class="ram-dd-menu" role="listbox">
                        @foreach ($officers as $o)
                            <button type="button" class="ram-dd-opt {{ $o === $officerName ? 'sel' : '' }}" data-value="{{ $o }}">
                                {!! $tick !!}<span>{{ $o }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="fld">
                <label>Month</label>
                <div class="ram-dd" data-dd>
                    <input type="hidden" name="month" value="{{ $month }}">
                    <button type="button" class="ram-dd-trigger" style="min-width:140px" aria-haspopup="listbox" aria-expanded="false">
                        <span class="ram-dd-value">{{ $months[$month - 1] }}</span>
                        {!! $chev !!}
                    </button>
                    <div class="ram-dd-menu" role="listbox">
                        @foreach ($months as $i => $m)
                            <button type="button" class="ram-dd-opt {{ ($i + 1) === $month ? 'sel' : '' }}" data-value="{{ $i + 1 }}">
                                {!! $tick !!}<span>{{ $m }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="fld">
                <label>Year</label>
                <div class="ram-dd" data-dd>
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button type="button" class="ram-dd-trigger" style="min-width:100px" aria-haspopup="listbox" aria-expanded="false">
                        <span class="ram-dd-value">{{ $year }}</span>
                        {!! $chev !!}
                    </button>
                    <div class="ram-dd-menu" role="listbox">
                        @foreach (range(now()->year, now()->year - 15) as $y)
                            <button type="button" class="ram-dd-opt {{ $y === $year ? 'sel' : '' }}" data-value="{{ $y }}">
                                {!! $tick !!}<span>{{ $y }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
        <div class="fld">
            <label>From</label>
            <div style="display:flex; gap:8px;">
                <div class="ram-dd" data-dd>
                    <input type="hidden" name="from_month" value="{{ $fromMonth }}">
                    <button type="button" class="ram-dd-trigger" style="min-width:140px" aria-haspopup="listbox" aria-expanded="false">
                        <span class="ram-dd-value">{{ $months[$fromMonth - 1] }}</span>
                        {!! $chev !!}
                    </button>
                    <div class="ram-dd-menu" role="listbox">
                        @foreach ($months as $i => $m)
                            <button type="button" class="ram-dd-opt {{ ($i + 1) === $fromMonth ? 'sel' : '' }}" data-value="{{ $i + 1 }}">
                                {!! $tick !!}<span>{{ $m }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="ram-dd" data-dd>
                    <input type="hidden" name="from_year" value="{{ $fromYear }}">
                    <button type="button" class="ram-dd-trigger" style="min-width:100px" aria-haspopup="listbox" aria-expanded="false">
                        <span class="ram-dd-value">{{ $fromYear }}</span>
                        {!! $chev !!}
                    </button>
                    <div class="ram-dd-menu" role="listbox">
                        @foreach (range(now()->year, now()->year - 15) as $y)
                            <button type="button" class="ram-dd-opt {{ $y === $fromYear ? 'sel' : '' }}" data-value="{{ $y }}">
                                {!! $tick !!}<span>{{ $y }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="range-arrow">{!! $arrow !!}</div>

        <div class="fld">
            <label>To</label>
            <div style="display:flex; gap:8px;">
                <div class="ram-dd" data-dd>
                    <input type="hidden" name="to_month" value="{{ $toMonth }}">
                    <button type="button" class="ram-dd-trigger" style="min-width:140px" aria-haspopup="listbox" aria-expanded="false">
                        <span class="ram-dd-value">{{ $months[$toMonth - 1] }}</span>
                        {!! $chev !!}
                    </button>
                    <div class="ram-dd-menu" role="listbox">
                        @foreach ($months as $i => $m)
                            <button type="button" class="ram-dd-opt {{ ($i + 1) === $toMonth ? 'sel' : '' }}" data-value="{{ $i + 1 }}">
                                {!! $tick !!}<span>{{ $m }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="ram-dd" data-dd>
                    <input type="hidden" name="to_year" value="{{ $toYear }}">
                    <button type="button" class="ram-dd-trigger" style="min-width:100px" aria-haspopup="listbox" aria-expanded="false">
                        <span class="ram-dd-value">{{ $toYear }}</span>
                        {!! $chev !!}
                    </button>
                    <div class="ram-dd-menu" role="listbox">
                        @foreach (range(now()->year, now()->year - 15) as $y)
                            <button type="button" class="ram-dd-opt {{ $y === $toYear ? 'sel' : '' }}" data-value="{{ $y }}">
                                {!! $tick !!}<span>{{ $y }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <button class="go" type="submit">Generate</button>
        <div class="spacer"></div>
        {{-- Print temporarily hidden per request; element + JS handler kept intact. --}}
        <button class="print" type="button" onclick="window.print()" style="display: none;">Print</button>
        <a class="export" id="exportBtn" href="#">Export Excel</a>
    </form>

    <div class="doc">
        <div class="doc-header">
            @foreach ($agencyLines ?? [] as $line)
                <p class="agency">{{ $line }}</p>
            @endforeach
            @if (!empty($officeNote))
                <p class="office-note">{{ $officeNote }}</p>
            @endif
            <h1>{{ $title }}</h1>
            @if (empty($agencyLines))
                <p class="office">Province of Sorsogon, Municipality of Prieto-Diaz</p>
            @endif
            <p class="period">{{ $periodPrefix ?? 'For the period of' }} {{ $period }}</p>
        </div>

        @if ($showOfficerRow ?? true)
            <div class="officer-row">
                <div class="officer-col">
                    <input type="text" class="officer-name" id="officerName" placeholder="&mdash; None &mdash;" autocomplete="off">
                    <span class="officer-label">Accountable Officer</span>
                </div>
                <div class="officer-col">
                    <input type="text" class="officer-name" id="designationField" placeholder="&mdash; None &mdash;" autocomplete="off">
                    <span class="officer-label">Designation</span>
                </div>
            </div>
        @endif

        @foreach ($sections as $section)
            @if (!empty($section['heading']))
                <p class="section-heading">{{ $section['heading'] }}</p>
            @endif
            <div class="table-wrap">
                <table>
                    <thead>
                        @if (!empty($section['groups']))
                            <tr>
                                @foreach ($section['groups'] as $group)
                                    @if (($group['colspan'] ?? 1) > 1)
                                        <th colspan="{{ $group['colspan'] }}">{{ $group['label'] }}</th>
                                    @else
                                        <th rowspan="2">{{ $group['label'] }}</th>
                                    @endif
                                @endforeach
                            </tr>
                            <tr>
                                @foreach ($section['groups'] as $group)
                                    @if (($group['colspan'] ?? 1) > 1)
                                        @foreach ($group['subcolumns'] as $sub)
                                            <th>{{ $sub }}</th>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tr>
                        @else
                            <tr>
                                @foreach ($section['columns'] as $col)
                                    <th class="{{ ($col['align'] ?? null) === 'left' ? 'left' : '' }}">{{ $col['label'] }}</th>
                                @endforeach
                            </tr>
                        @endif
                    </thead>
                    <tbody>
                        @forelse ($section['rows'] as $row)
                            @php
                                $isMarkerRow = in_array($row[0] ?? null, ['sub-total'], true)
                                    || in_array($row[2] ?? null, ['Sub Total', 'Balance Forwarded'], true)
                                    || in_array($row[3] ?? null, ['Opening Balance', 'Ending Balance'], true);
                            @endphp
                            <tr class="{{ $isMarkerRow ? 'marker-row' : '' }}">
                                @foreach ($row as $i => $value)
                                    <td class="{{ ($section['columns'][$i]['align'] ?? null) === 'right' ? 'num' : (($section['columns'][$i]['align'] ?? null) === 'left' ? 'left' : '') }}">{{ $value }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr class="empty-row"><td colspan="{{ count($section['columns']) }}">No records found for the selected period.</td></tr>
                        @endforelse
                    </tbody>
                    @if (!empty($section['totals']))
                        <tfoot>
                            <tr>
                                @foreach ($section['totals'] as $i => $value)
                                    <td class="{{ ($section['columns'][$i]['align'] ?? null) === 'right' ? 'num' : (($section['columns'][$i]['align'] ?? null) === 'left' ? 'left' : '') }}">{{ $value }}</td>
                                @endforeach
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        @endforeach

        @if (!empty($certification))
            <div class="cert-block">
                <p class="cert-label">{{ $certification['label'] }}</p>
                <input type="text" class="cert-name" id="certifiedByName" value="{{ $isRaaf ? $officerName : 'Gemma D. Ferrer' }}" autocomplete="off">
                <div class="cert-role">{{ $certification['role'] }}</div>
            </div>
        @endif

        <div class="doc-foot"></div>
    </div>
</div>

<script>
    (function () {
        const dds = Array.from(document.querySelectorAll('.ram-dd'));

        function closeAll(except) {
            dds.forEach((dd) => {
                if (dd === except) return;
                dd.classList.remove('open');
                dd.querySelector('.ram-dd-trigger')?.setAttribute('aria-expanded', 'false');
            });
        }

        dds.forEach((dd) => {
            const trigger = dd.querySelector('.ram-dd-trigger');
            const menu = dd.querySelector('.ram-dd-menu');
            const input = dd.querySelector('input[type="hidden"]');
            const valueLabel = dd.querySelector('.ram-dd-value');

            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const willOpen = !dd.classList.contains('open');
                closeAll(dd);
                dd.classList.toggle('open', willOpen);
                trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
                if (willOpen) menu.querySelector('.ram-dd-opt.sel')?.scrollIntoView({ block: 'nearest' });
            });

            menu.querySelectorAll('.ram-dd-opt').forEach((opt) => {
                opt.addEventListener('click', () => {
                    input.value = opt.dataset.value;
                    valueLabel.textContent = opt.querySelector('span').textContent;
                    menu.querySelectorAll('.ram-dd-opt').forEach((o) => o.classList.remove('sel'));
                    opt.classList.add('sel');
                    dd.classList.remove('open');
                    trigger.setAttribute('aria-expanded', 'false');
                });
            });
        });

        document.addEventListener('click', () => closeAll());
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAll(); });

        // Editable Officer/Designation — remembered per report so they don't
        // need retyping on every visit, mirroring the Cheque Management report.
        const slug = @json($slug);
        const isRaaf = @json($isRaaf);
        const officerField = document.getElementById('officerName');
        const designationField = document.getElementById('designationField');
        const certifiedByField = document.getElementById('certifiedByName');

        if (officerField) {
            const savedOfficer = localStorage.getItem('ram_officer_name_' + slug);
            if (savedOfficer) officerField.value = savedOfficer;
            officerField.addEventListener('input', () => localStorage.setItem('ram_officer_name_' + slug, officerField.value));
        }

        if (designationField) {
            const savedDesignation = localStorage.getItem('ram_designation_' + slug);
            if (savedDesignation) designationField.value = savedDesignation;
            designationField.addEventListener('input', () => localStorage.setItem('ram_designation_' + slug, designationField.value));
        }

        // RAAF's cert-name field already defaults to the selected officer
        // server-side, so it skips localStorage — restoring a stale value
        // here would silently override whichever officer was just picked.
        if (certifiedByField && !isRaaf) {
            const savedCertifiedBy = localStorage.getItem('ram_certified_by_' + slug);
            if (savedCertifiedBy) certifiedByField.value = savedCertifiedBy;
            certifiedByField.addEventListener('input', () => localStorage.setItem('ram_certified_by_' + slug, certifiedByField.value));
        }

        document.getElementById('exportBtn').addEventListener('click', function (e) {
            e.preventDefault();
            const params = isRaaf
                ? new URLSearchParams({
                    month: @json((string) ($month ?? '')),
                    year: @json((string) ($year ?? '')),
                    officer: @json($officerName ?? ''),
                    certified_by: certifiedByField ? certifiedByField.value.trim() : '',
                })
                : new URLSearchParams({
                    from_month: @json((string) ($fromMonth ?? '')),
                    from_year: @json((string) ($fromYear ?? '')),
                    to_month: @json((string) ($toMonth ?? '')),
                    to_year: @json((string) ($toYear ?? '')),
                    officer_name: officerField ? officerField.value.trim() : '',
                    designation: designationField ? designationField.value.trim() : '',
                    certified_by: certifiedByField ? certifiedByField.value.trim() : '',
                });
            window.location.href = @json($exportRouteUrl) + '?' + params.toString();
        });
    })();
</script>
</body>
</html>
