<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Batch Accountability Report</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef1f5; font-family: 'Manrope', system-ui, sans-serif; color: #1f2733; padding: 24px; }
        .sheet { max-width: 1180px; margin: 0 auto; }
        .toolbar { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; background: #fff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 14px 16px; margin-bottom: 18px; }
        .toolbar .fld { display: flex; flex-direction: column; gap: 4px; }
        .toolbar label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6b7685; }
        /* Custom dropdown — namespaced copy of the app's .assigned-to-*/.cqm-dd
           idiom; not shared with cheque-management/report.blade.php since
           both are standalone non-layout pages. */
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
        .toolbar .print { height: 38px; padding: 0 18px; border: 1px solid #427AB5; border-radius: 8px; background: rgba(66,122,181,.08); color: #427AB5; font-family: inherit; font-weight: 600; font-size: 13.5px; cursor: pointer; margin-left: auto; }
        .doc { background: #fff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 26px 28px; overflow-x: auto; }
        .doc h1 { text-align: center; font-size: 18px; font-weight: 800; letter-spacing: .04em; margin: 0 0 14px; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 20px; font-size: 13px; margin-bottom: 14px; }
        .meta b { font-weight: 700; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; min-width: 900px; }
        th, td { border: 1px solid #b9c2cf; padding: 6px 7px; }
        th { background: #f4f6f9; font-weight: 700; font-size: 10.5px; text-transform: uppercase; letter-spacing: .02em; text-align: center; }
        td.num, th.num { text-align: right; font-variant-numeric: tabular-nums; }
        td.center { text-align: center; }
        tfoot td { font-weight: 800; }
        .cert { margin-top: 22px; font-size: 12.5px; }
        .sign { display: flex; justify-content: flex-end; margin-top: 40px; }
        .sign-block { display: inline-flex; flex-direction: column; align-items: center; min-width: 260px; }
        .sign-rule { width: 100%; border-top: 1px solid #333; }
        .sign-name { width: 100%; text-align: center; font-family: inherit; font-weight: 700; font-size: 14px; color: #1f2733; border: 1px solid transparent; border-radius: 6px; padding: 4px 6px; margin-top: 4px; background: transparent; }
        .sign-name:hover { border-color: #e1e7ef; }
        .sign-name:focus { outline: none; border-color: #427AB5; box-shadow: 0 0 0 3px rgba(66,122,181,.14); background: #fff; }
        .sign-role { font-size: 12px; color: #444; margin-top: 2px; }
        .sign-hint { font-size: 10.5px; color: #98a2b3; margin-top: 3px; }
        @media print { .sign-name { border-color: transparent !important; box-shadow: none !important; background: transparent !important; } .sign-hint { display: none; } }
        @media print { body { background: #fff; padding: 0; } .toolbar { display: none; } .doc { border: none; border-radius: 0; } }
    </style>
</head>
<body>
<div class="sheet">
    <form class="toolbar" method="GET" action="{{ route('reporting-abstract.my-batch-report') }}">
        @php
            $chev = '<svg class="ram-dd-chev" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 7.5 10 12.5 15 7.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            $tick = '<svg class="ram-dd-tick" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 10.5 8.5 15 16 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        @endphp

        <div class="fld">
            <label>Month</label>
            <div class="ram-dd" data-dd>
                <input type="hidden" name="month" value="{{ $month }}">
                <button type="button" class="ram-dd-trigger" style="min-width:150px" aria-haspopup="listbox" aria-expanded="false">
                    <span class="ram-dd-value">{{ \Illuminate\Support\Carbon::create()->month($month)->format('F') }}</span>
                    {!! $chev !!}
                </button>
                <div class="ram-dd-menu" role="listbox">
                    @foreach (range(1, 12) as $m)
                        <button type="button" class="ram-dd-opt {{ $m === $month ? 'sel' : '' }}" data-value="{{ $m }}">
                            {!! $tick !!}<span>{{ \Illuminate\Support\Carbon::create()->month($m)->format('F') }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="fld">
            <label>Year</label>
            <div class="ram-dd" data-dd>
                <input type="hidden" name="year" value="{{ $year }}">
                <button type="button" class="ram-dd-trigger" style="min-width:110px" aria-haspopup="listbox" aria-expanded="false">
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
        <button class="go" type="submit">Generate</button>
        <button class="print" type="button" onclick="window.print()">Print</button>
    </form>

    <div class="doc">
        <h1>MY BATCH ACCOUNTABILITY REPORT</h1>
        <div class="meta">
            <span><b>Agency:</b> Municipality of Prieto Diaz, Sorsogon</span>
            <span><b>Period Covered:</b> {{ \Illuminate\Support\Carbon::create()->month($month)->format('F') }} {{ $year }}</span>
            <span><b>Collector:</b> {{ $collectorName ?: '—' }}</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th rowspan="2">Forms</th>
                    <th colspan="2">On Hand Last Report</th>
                    <th colspan="2">Received Since</th>
                    <th colspan="2">Issued Since</th>
                    <th colspan="2">Remaining on Hand</th>
                    <th rowspan="2">Remarks</th>
                </tr>
                <tr>
                    <th>Quantity</th>
                    <th>Inclusive Serial No.</th>
                    <th>Quantity</th>
                    <th>Inclusive Serial No.</th>
                    <th>Quantity</th>
                    <th>Inclusive Serial No.</th>
                    <th>Quantity</th>
                    <th>Inclusive Serial No.</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td>{{ $row[0] }}</td>
                        <td class="num">{{ $row[1] }}</td>
                        <td class="center">{{ $row[2] }}</td>
                        <td class="num">{{ $row[3] }}</td>
                        <td class="center">{{ $row[4] }}</td>
                        <td class="num">{{ $row[5] }}</td>
                        <td class="center">{{ $row[6] }}</td>
                        <td class="num">{{ $row[7] }}</td>
                        <td class="center">{{ $row[8] }}</td>
                        <td>{{ $row[9] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="center" style="color:#6b7685;padding:16px;">No batches assigned to you for this period.</td></tr>
                @endforelse
            </tbody>
            @if (! empty($rows))
                <tfoot>
                    <tr>
                        <td>{{ $totals[0] }}</td>
                        <td class="num">{{ $totals[1] }}</td>
                        <td></td>
                        <td class="num">{{ $totals[3] }}</td>
                        <td></td>
                        <td class="num">{{ $totals[5] }}</td>
                        <td></td>
                        <td class="num">{{ $totals[7] }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>

        <p class="cert">I hereby certify that this report of accountability for accountable forms assigned to me is a full, true and correct statement for the period stated.</p>

        <div class="sign">
            <div class="sign-block">
                <div class="sign-rule"></div>
                <input type="text" class="sign-name" id="collectorName" value="{{ $collectorName }}" aria-label="Collector name" autocomplete="off">
                <div class="sign-role">Collector</div>
                <div class="sign-hint">Click the name to edit</div>
            </div>
        </div>
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

        // Editable Collector name — defaults to the signed-in collector's
        // name, remembers edits per-browser (mirrors the cheque report's
        // treasurer-name field, keyed separately).
        const nameInput = document.getElementById('collectorName');
        if (nameInput) {
            const saved = localStorage.getItem('ram_collector_signature_name');
            if (saved && saved.trim() !== '') nameInput.value = saved;
            nameInput.addEventListener('input', () => localStorage.setItem('ram_collector_signature_name', nameInput.value));
        }
    })();
</script>
</body>
</html>
