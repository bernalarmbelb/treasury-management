<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Duplicate Copy · {{ $cheque->check_number }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef1f5; font-family: 'Manrope', system-ui, sans-serif; color: #1f2733; padding: 28px; }
        .toolbar { max-width: 640px; margin: 0 auto 16px; display: flex; align-items: center; justify-content: space-between; }
        .toolbar .note { font-size: 12.5px; color: #6b7685; }
        .toolbar button { height: 38px; padding: 0 18px; border: none; border-radius: 8px; background: #1877f2; color: #fff; font-family: inherit; font-weight: 600; font-size: 13px; cursor: pointer; }
        .paper { max-width: 640px; margin: 0 auto; background: #fff; border: 1px solid #E3E8EF; border-radius: 8px; padding: 30px 34px; }
        .head { text-align: center; border-bottom: 1px solid #E3E8EF; padding-bottom: 12px; margin-bottom: 16px; }
        .head .org { font-weight: 800; font-size: 15px; letter-spacing: .02em; }
        .head .sub { font-size: 12px; color: #6b7685; margin-top: 2px; }
        .grid { display: grid; grid-template-columns: 170px 1fr; gap: 10px 16px; font-size: 13.5px; }
        .grid .k { color: #6b7685; }
        .grid .v { font-weight: 600; font-variant-numeric: tabular-nums; }
        .stub { margin-top: 22px; display: flex; justify-content: space-between; font-size: 12px; color: #6b7685; }
        @media print { body { background: #fff; padding: 0; } .toolbar { display: none; } .paper { border: none; max-width: none; margin: 0; } }
    </style>
</head>
<body>
    <div class="toolbar">
        <span class="note">Duplicate Copy — full-detail office file stub (plain paper).</span>
        <button onclick="window.print()">Print</button>
    </div>

    <div class="paper">
        <div class="head">
            <div class="org">MUNICIPALITY OF PRIETO DIAZ</div>
            <div class="sub">Cheque — Duplicate Copy @if($cheque->status === 'Cancelled') · <b style="color:#db7788">CANCELLED</b> @endif</div>
        </div>
        <div class="grid">
            <span class="k">Account Number</span><span class="v">{{ $cheque->bankAccount->account_number }}</span>
            <span class="k">Account Name</span><span class="v">{{ $cheque->account_name }}</span>
            <span class="k">Bank</span><span class="v">{{ $cheque->bankAccount->bank_name }}</span>
            <span class="k">Date</span><span class="v">{{ $cheque->cheque_date->format('m / d / y') }}</span>
            <span class="k">Check Number</span><span class="v">{{ $cheque->check_number }}</span>
            <span class="k">Pay to the order of</span><span class="v">{{ $cheque->pay_to_order_of ?: '—' }}</span>
            <span class="k">Amount</span><span class="v">₱ {{ number_format($cheque->amount, 2) }}</span>
            <span class="k">Amount in Words</span><span class="v" style="font-variant-numeric:normal">{{ $cheque->amount_in_words ?: '—' }}</span>
            <span class="k">Nature of Payment</span><span class="v" style="font-variant-numeric:normal">{{ $cheque->nature_of_payment ?: '—' }}</span>
        </div>
        <div class="stub">
            <span>Prepared by: {{ $cheque->created_by ?: '—' }}</span>
            <span>Recorded: {{ $cheque->created_at->format('M j, Y g:i A') }}</span>
        </div>
    </div>
</body>
</html>
