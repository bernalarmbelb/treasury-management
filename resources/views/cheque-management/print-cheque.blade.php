<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Cheque · {{ $cheque->check_number }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700&display=swap">
    <style>
        :root { --ink:#123a6b; --pre:#9aa4b2; --line:#c9d2dd; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef1f5; font-family: 'Manrope', system-ui, sans-serif; color: #1f2733; padding: 28px; }
        .toolbar { max-width: 720px; margin: 0 auto 16px; display: flex; gap: 10px; align-items: center; justify-content: space-between; }
        .toolbar .note { font-size: 12.5px; color: #6b7685; }
        .toolbar button { height: 38px; padding: 0 18px; border: none; border-radius: 8px; background: #1877f2; color: #fff; font-family: inherit; font-weight: 600; font-size: 13px; cursor: pointer; }
        /* The physical cheque (dashed = pre-printed paper); overlaid values are the only thing that prints on a real cheque. */
        .cheque { max-width: 720px; margin: 0 auto; background: #fff; border: 1.5px dashed var(--line); border-radius: 8px; padding: 26px 28px 30px; position: relative; }
        .pre { color: var(--pre); font-size: 11px; text-transform: uppercase; letter-spacing: .04em; }
        .ov { color: var(--ink); font-weight: 700; }
        .row { display: flex; justify-content: space-between; align-items: flex-start; }
        .date { text-align: right; }
        .ln { border-bottom: 1px solid var(--line); padding-bottom: 3px; }
        .amtbox { border: 1.4px solid var(--line); border-radius: 6px; padding: 7px 14px; font-weight: 800; color: var(--ink); font-variant-numeric: tabular-nums; white-space: nowrap; }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .cheque { border: none; max-width: none; margin: 0; padding: 40px; }
            .pre { color: #cfd6df; }  /* faint so it lands over pre-printed guides */
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <span class="note">Print Cheque — only Date, Payee, Amount &amp; Amount in Words print, aligned to a pre-printed cheque.</span>
        <button onclick="window.print()">Print</button>
    </div>

    <div class="cheque">
        <div class="row">
            <div><span class="pre">{{ $cheque->bankAccount->bank_name }}</span></div>
            <div class="date"><span class="pre">Date</span><br><span class="ov">{{ $cheque->cheque_date->format('m / d / y') }}</span></div>
        </div>

        <div style="margin-top: 26px;">
            <span class="pre">Pay to the order of</span>
            <div class="ln"><span class="ov">{{ $cheque->pay_to_order_of }}</span></div>
        </div>

        <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: flex-end; gap: 16px;">
            <div style="flex: 1;">
                <div class="ln"><span class="ov">{{ $cheque->amount_in_words }}</span></div>
            </div>
            <div class="amtbox">₱ {{ number_format($cheque->amount, 2) }}</div>
        </div>

        <div style="margin-top: 30px; display: flex; justify-content: space-between;">
            <span class="pre">Memo _______________</span>
            <span class="pre">_______________ Signature</span>
        </div>
    </div>
</body>
</html>
