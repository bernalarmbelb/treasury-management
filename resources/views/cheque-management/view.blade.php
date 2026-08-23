<x-layout>
    <div class="x-header-container sub-nav-sticky">
        <div class="container-title">
            <div style="display: flex; flex-direction: column;">
                <span class="page-title">Cheque Management</span>
                <p class="page-links">
                    <a href="{{ route('home') }}">Home</a> |
                    <a href="{{ route('cheque-management') }}">Cheque Management</a> |
                    <span class="page-links-accent">View Cheque</span>
                </p>
            </div>
        </div>
    </div>

    <style>
        .cqm-view-wrap { max-width: 760px; margin: 0 auto; }
        .cqm-view-card { background: #fff; border: 1px solid var(--line, #E3E8EF); border-radius: 12px; overflow: hidden; }
        .cqm-view-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; border-bottom: 1px solid var(--line, #E3E8EF); }
        .cqm-view-head .t { font-family: 'Manrope', sans-serif; font-weight: 700; font-size: 17px; }
        .cqm-detail { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: var(--line, #E3E8EF); }
        .cqm-cell { background: #fff; padding: 15px 22px; display: flex; flex-direction: column; gap: 4px; }
        .cqm-cell.full { grid-column: 1 / -1; }
        .cqm-cell .l { font-size: 11px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--muted, #6b7685); }
        .cqm-cell .v { font-size: 15px; font-weight: 600; color: var(--ink, #1f2733); font-variant-numeric: tabular-nums; }
        .cqm-view-actions { display: flex; gap: 12px; padding: 18px 22px; border-top: 1px solid var(--line, #E3E8EF); }
        .cqm-vbtn { height: 42px; padding: 0 20px; border-radius: 8px; font-family: 'Manrope', sans-serif; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border: 1px solid transparent; cursor: pointer; }
        .cqm-vbtn-blue { background: var(--btn-primary, #1877f2); color: #fff; }
        .cqm-vbtn-ghost { background: rgba(66,122,181,0.08); border-color: var(--primary, #427AB5); color: var(--primary, #427AB5); }
        @media (max-width: 640px) { .cqm-detail { grid-template-columns: 1fr; } }
    </style>

    <div class="collection-content">
        <div class="cqm-view-wrap">
            <div class="cqm-view-card">
                <div class="cqm-view-head">
                    <span class="t">Cheque No. {{ $cheque->check_number }}</span>
                    <span class="status-badge status-{{ strtolower($cheque->status) }}" style="font-weight:700;color:{{ $cheque->status === 'Cancelled' ? 'var(--danger,#db7788)' : 'var(--success,#1e874b)' }}">{{ $cheque->status }}</span>
                </div>
                <div class="cqm-detail">
                    <div class="cqm-cell"><span class="l">Account Number</span><span class="v">{{ $cheque->bankAccount->account_number }}</span></div>
                    <div class="cqm-cell"><span class="l">Account Name</span><span class="v">{{ $cheque->account_name }}</span></div>
                    <div class="cqm-cell"><span class="l">Date</span><span class="v">{{ $cheque->cheque_date->format('m / d / y') }}</span></div>
                    <div class="cqm-cell"><span class="l">Check Number</span><span class="v">{{ $cheque->check_number }}</span></div>
                    <div class="cqm-cell full"><span class="l">Pay to the Order of</span><span class="v">{{ $cheque->pay_to_order_of ?: '—' }}</span></div>
                    <div class="cqm-cell"><span class="l">Amount</span><span class="v">₱ {{ number_format($cheque->amount, 2) }}</span></div>
                    <div class="cqm-cell"><span class="l">Nature of Payment</span><span class="v">{{ $cheque->nature_of_payment ?: '—' }}</span></div>
                    <div class="cqm-cell full"><span class="l">Amount in Words</span><span class="v" style="font-variant-numeric:normal">{{ $cheque->amount_in_words ?: '—' }}</span></div>
                </div>
                <div class="cqm-view-actions">
                    <a href="{{ route('cheque-management.print', $cheque->id) }}" target="_blank" class="cqm-vbtn cqm-vbtn-blue">Print Cheque</a>
                    <a href="{{ route('cheque-management.duplicate', $cheque->id) }}" target="_blank" class="cqm-vbtn cqm-vbtn-blue">Print Duplicate Copy</a>
                    <a href="{{ route('cheque-management') }}" class="cqm-vbtn cqm-vbtn-ghost">Back to Logs</a>
                </div>
            </div>
        </div>
    </div>
</x-layout>
