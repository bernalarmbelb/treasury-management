<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report of Checks Issued</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef1f5; font-family: 'Manrope', system-ui, sans-serif; color: #1f2733; padding: 24px; }
        .sheet { max-width: 980px; margin: 0 auto; }
        .toolbar { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; background: #fff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 14px 16px; margin-bottom: 18px; }
        .toolbar .fld { display: flex; flex-direction: column; gap: 4px; }
        .toolbar label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6b7685; }
        /* Custom dropdown — mirrors the app's .assigned-to-* idiom, scaled up. */
        .cqm-dd { position: relative; }
        .cqm-dd-trigger { display: flex; align-items: center; justify-content: space-between; gap: 10px; height: 40px; padding: 0 12px; border: 1px solid #d0d8e4; border-radius: 8px; background: #fff; font-family: inherit; font-size: 13.5px; color: #1f2733; cursor: pointer; transition: border-color .15s ease, box-shadow .15s ease; }
        .cqm-dd-trigger:hover { border-color: #427AB5; }
        .cqm-dd.open .cqm-dd-trigger { border-color: #427AB5; box-shadow: 0 0 0 3px rgba(66,122,181,.14); }
        .cqm-dd-value { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500; }
        .cqm-dd-chev { width: 15px; height: 15px; flex-shrink: 0; color: #8893a3; transition: transform .18s ease; }
        .cqm-dd.open .cqm-dd-chev { transform: rotate(180deg); }
        .cqm-dd-menu { position: absolute; top: calc(100% + 6px); left: 0; z-index: 30; display: none; flex-direction: column; gap: 2px; min-width: 100%; width: max-content; max-height: 280px; overflow-y: auto; padding: 6px; background: #fff; border: 1px solid #e1e7ef; border-radius: 10px; box-shadow: 0 12px 30px rgba(20,30,50,.16); }
        .cqm-dd.open .cqm-dd-menu { display: flex; }
        .cqm-dd-opt { display: flex; align-items: center; gap: 8px; width: 100%; border: none; background: none; padding: 9px 11px; border-radius: 7px; text-align: left; font-family: inherit; font-size: 13.5px; color: #1f2733; cursor: pointer; white-space: nowrap; }
        .cqm-dd-opt:hover { background: rgba(66,122,181,.08); }
        .cqm-dd-opt.sel { background: rgba(66,122,181,.12); font-weight: 600; }
        .cqm-dd-tick { width: 14px; height: 14px; flex-shrink: 0; color: #427AB5; opacity: 0; }
        .cqm-dd-opt.sel .cqm-dd-tick { opacity: 1; }
        .toolbar .go { height: 38px; padding: 0 18px; border: none; border-radius: 8px; background: #427AB5; color: #fff; font-family: inherit; font-weight: 600; font-size: 13.5px; cursor: pointer; }
        .toolbar .print { height: 38px; padding: 0 18px; border: 1px solid #427AB5; border-radius: 8px; background: rgba(66,122,181,.08); color: #427AB5; font-family: inherit; font-weight: 600; font-size: 13.5px; cursor: pointer; margin-left: auto; }
        .toolbar .export { height: 38px; padding: 0 20px; border: none; border-radius: 8px; background: #427AB5; color: #fff; font-family: inherit; font-weight: 700; font-size: 13.5px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; }
        .doc { background: #fff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 26px 28px; }
        .doc h1 { text-align: center; font-size: 18px; font-weight: 800; letter-spacing: .04em; margin: 0 0 14px; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 20px; font-size: 13px; margin-bottom: 14px; }
        .meta b { font-weight: 700; }
        table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        th, td { border: 1px solid #b9c2cf; padding: 6px 8px; }
        th { background: #f4f6f9; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: .02em; }
        td.num, th.num { text-align: right; font-variant-numeric: tabular-nums; }
        td.center { text-align: center; }
        tr.cancelled td { color: #a63b50; font-style: italic; }
        tfoot td { font-weight: 800; }
        .cert { margin-top: 22px; font-size: 12.5px; }
        .sign { display: flex; justify-content: flex-end; gap: 32px; margin-top: 40px; }
        .sign-block { display: inline-flex; flex-direction: column; align-items: center; min-width: 260px; }
        .sign-block:last-child { min-width: 140px; }
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
    <form class="toolbar" method="GET" action="{{ route('cheque-management.report') }}">
        @php
            $chev = '<svg class="cqm-dd-chev" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 7.5 10 12.5 15 7.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            $tick = '<svg class="cqm-dd-tick" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 10.5 8.5 15 16 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        @endphp

        <div class="fld">
            <label>Bank Account</label>
            <div class="cqm-dd" data-dd>
                <input type="hidden" name="bank_account_id" value="{{ $account?->id }}">
                <button type="button" class="cqm-dd-trigger" style="min-width:260px" aria-haspopup="listbox" aria-expanded="false">
                    <span class="cqm-dd-value">{{ $account ? $account->bank_name . ' · ' . $account->account_number : 'Select account…' }}</span>
                    {!! $chev !!}
                </button>
                <div class="cqm-dd-menu" role="listbox">
                    @foreach ($accounts as $acc)
                        <button type="button" class="cqm-dd-opt {{ $account && $account->id === $acc->id ? 'sel' : '' }}" data-value="{{ $acc->id }}">
                            {!! $tick !!}<span>{{ $acc->bank_name }} · {{ $acc->account_number }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="fld">
            <label>Month</label>
            <div class="cqm-dd" data-dd>
                <input type="hidden" name="month" value="{{ $month }}">
                <button type="button" class="cqm-dd-trigger" style="min-width:150px" aria-haspopup="listbox" aria-expanded="false">
                    <span class="cqm-dd-value">{{ \Illuminate\Support\Carbon::create()->month($month)->format('F') }}</span>
                    {!! $chev !!}
                </button>
                <div class="cqm-dd-menu" role="listbox">
                    @foreach (range(1, 12) as $m)
                        <button type="button" class="cqm-dd-opt {{ $m === $month ? 'sel' : '' }}" data-value="{{ $m }}">
                            {!! $tick !!}<span>{{ \Illuminate\Support\Carbon::create()->month($m)->format('F') }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="fld">
            <label>Year</label>
            <div class="cqm-dd" data-dd>
                <input type="hidden" name="year" value="{{ $year }}">
                <button type="button" class="cqm-dd-trigger" style="min-width:110px" aria-haspopup="listbox" aria-expanded="false">
                    <span class="cqm-dd-value">{{ $year }}</span>
                    {!! $chev !!}
                </button>
                <div class="cqm-dd-menu" role="listbox">
                    @foreach (range(now()->year, now()->year - 15) as $y)
                        <button type="button" class="cqm-dd-opt {{ $y === $year ? 'sel' : '' }}" data-value="{{ $y }}">
                            {!! $tick !!}<span>{{ $y }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
        <button class="go" type="submit">Generate</button>
        <button class="print" type="button" onclick="window.print()">Print</button>
        <a class="export" id="exportBtn" href="#">Export Excel</a>
    </form>

    <div class="doc">
        <h1>REPORTS OF CHECKS ISSUED</h1>
        <div class="meta">
            <span><b>Agency:</b> Municipality of Prieto Diaz, Sorsogon</span>
            <span><b>Period Covered:</b> {{ \Illuminate\Support\Carbon::create()->month($month)->format('F') }} {{ $year }}</span>
            <span><b>Bank Name:</b> {{ $account?->bank_name ?? '—' }}</span>
            <span><b>Account No.:</b> {{ $account?->account_number ?? '—' }}{{ $account?->fund ? ' (' . strtoupper($account->fund) . ')' : '' }}</span>
            <span><b>Report No.:</b>&nbsp;</span>
            <span><b>Sheet No.:</b>&nbsp;</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Check Date</th>
                    <th>Check No.</th>
                    <th>DV No. / Payroll</th>
                    <th>Responsibility Center Code</th>
                    <th>Payee</th>
                    <th>Nature of Payment</th>
                    <th class="num">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @forelse ($cheques as $cheque)
                    @php if ($cheque->status !== 'Cancelled') { $total += (float) $cheque->amount; } @endphp
                    <tr class="{{ $cheque->status === 'Cancelled' ? 'cancelled' : '' }}">
                        <td class="center">{{ $cheque->cheque_date->format('m.d.y') }}</td>
                        <td class="center">{{ $cheque->check_number }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ $cheque->status === 'Cancelled' ? 'cancelled' : ($cheque->pay_to_order_of ?: '—') }}</td>
                        <td>{{ $cheque->status === 'Cancelled' ? '' : ($cheque->nature_of_payment ?: '—') }}</td>
                        <td class="num">{{ $cheque->status === 'Cancelled' ? '' : number_format($cheque->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="center" style="color:#6b7685;padding:16px;">No cheques issued for this period.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align:right;">TOTAL</td>
                    <td class="num">{{ number_format($total, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <p class="cert">I hereby certify that this Report of Checks Issued in ___ sheet(s) is a full, true and correct statement of all checks released by me in payment for obligations for the period stated and shown in the attached disbursement vouchers.</p>

        <div class="sign">
            <div class="sign-block">
                <div class="sign-rule"></div>
                <input type="text" class="sign-name" id="treasurerName" value="Gemma D. Ferrer" aria-label="Municipal Treasurer name" autocomplete="off">
                <div class="sign-role">Municipal Treasurer</div>
                <div class="sign-hint">Click the name to edit</div>
            </div>
            <div class="sign-block">
                <div class="sign-rule"></div>
                <div class="sign-role">Date</div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const dds = Array.from(document.querySelectorAll('.cqm-dd'));

        function closeAll(except) {
            dds.forEach((dd) => {
                if (dd === except) return;
                dd.classList.remove('open');
                dd.querySelector('.cqm-dd-trigger')?.setAttribute('aria-expanded', 'false');
            });
        }

        dds.forEach((dd) => {
            const trigger = dd.querySelector('.cqm-dd-trigger');
            const menu = dd.querySelector('.cqm-dd-menu');
            const input = dd.querySelector('input[type="hidden"]');
            const valueLabel = dd.querySelector('.cqm-dd-value');

            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const willOpen = !dd.classList.contains('open');
                closeAll(dd);
                dd.classList.toggle('open', willOpen);
                trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
                if (willOpen) menu.querySelector('.cqm-dd-opt.sel')?.scrollIntoView({ block: 'nearest' });
            });

            menu.querySelectorAll('.cqm-dd-opt').forEach((opt) => {
                opt.addEventListener('click', () => {
                    input.value = opt.dataset.value;
                    valueLabel.textContent = opt.querySelector('span').textContent;
                    menu.querySelectorAll('.cqm-dd-opt').forEach((o) => o.classList.remove('sel'));
                    opt.classList.add('sel');
                    dd.classList.remove('open');
                    trigger.setAttribute('aria-expanded', 'false');
                });
            });
        });

        document.addEventListener('click', () => closeAll());
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAll(); });

        // Editable Municipal Treasurer name — defaults to Gemma D. Ferrer, remembers edits.
        const treasurer = document.getElementById('treasurerName');
        if (treasurer) {
            const saved = localStorage.getItem('cqm_treasurer_name');
            if (saved && saved.trim() !== '') treasurer.value = saved;
            treasurer.addEventListener('input', () => localStorage.setItem('cqm_treasurer_name', treasurer.value));
        }

        document.getElementById('exportBtn').addEventListener('click', function (e) {
            e.preventDefault();
            const params = new URLSearchParams({
                bank_account_id: @json((string) $account?->id),
                month: @json((string) $month),
                year: @json((string) $year),
                treasurer_name: treasurer ? treasurer.value.trim() : '',
            });
            window.location.href = @json(route('cheque-management.report.export', [], false)) + '?' + params.toString();
        });
    })();
</script>
</body>
</html>
