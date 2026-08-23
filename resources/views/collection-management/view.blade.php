<x-layout>
    @php
        $t = $transaction;
        $hasPendingRequest = (bool) ($pendingRequest ?? null);

        $fmt = fn($v) => $v ? number_format((float) $v, 2) : '';
        $fmtDate = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('F j, Y') : '';
        $tinChars = $t ? str_split(str_pad($t->tin ?? '', 15, ' ')) : [];

        $paperMaxWidth = match($log->form_type) {
            'Form 10'            => '675px',
            'Form 56'            => '812px',
            'Form 5IC'           => '451px',
            'Form 58'            => '540px',
            'BIR0016', 'BIR0017' => '882px',
            default              => '900px',
        };
    @endphp

    <style>
        /* Clean read-only screen view (print keeps the receipt replica below). */
        @media screen { .cm-print { display: none; } }
        @media print {
            .cm-screen { display: none !important; }
            .ctc-view-meta, .cqm-view-payment, .ctc-cancel-request-card, .ctc-view-actions { display: none !important; }
        }
        .cm-screen { display: flex; flex-direction: column; gap: 16px; width: 100%; max-width: 820px; margin: 0 auto; }
        .cm-card { background: #fff; border: 1px solid var(--line, #E3E8EF); border-radius: 12px; box-shadow: 0 1px 2px rgba(16,32,56,.05), 0 8px 22px rgba(16,32,56,.05); overflow: hidden; }
        .cm-summary { display: flex; align-items: center; justify-content: space-between; gap: 18px; flex-wrap: wrap; padding: 20px 22px; }
        .cm-sum-l { font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: var(--muted, #6b7685); }
        .cm-sum-n { font-size: 22px; font-weight: 800; letter-spacing: -.01em; color: var(--ink, #1f2733); }
        .cm-sum-m { font-size: 13px; color: var(--muted, #6b7685); margin-top: 2px; }
        .cm-sum-amt { text-align: right; }
        .cm-sum-al { font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: var(--muted, #6b7685); }
        .cm-sum-av { font-size: 26px; font-weight: 800; font-variant-numeric: tabular-nums; color: var(--ink, #1f2733); }
        .cm-h { display: flex; align-items: baseline; justify-content: space-between; gap: 10px; padding: 14px 18px; border-bottom: 1px solid var(--line, #E3E8EF); background: var(--surface-2, #F7F9FB); }
        .cm-k { font-size: 11px; font-weight: 800; letter-spacing: .09em; text-transform: uppercase; color: var(--primary, #427AB5); }
        .cm-sub { font-size: 12.5px; color: var(--muted, #6b7685); }
        .cm-b { padding: 18px; }
        .cm-stack { display: flex; flex-direction: column; gap: 16px; }
        .cm-kv { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; background: var(--line, #E3E8EF); border: 1px solid var(--line, #E3E8EF); border-radius: 10px; overflow: hidden; }
        .cm-cell { background: #fff; padding: 13px 15px; display: flex; flex-direction: column; gap: 3px; }
        .cm-l { font-size: 11px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--muted, #6b7685); }
        .cm-v { font-size: 14.5px; font-weight: 600; color: var(--ink, #1f2733); }
        .cm-tbl-wrap { overflow-x: auto; }
        .cm-tbl { width: 100%; border-collapse: collapse; font-size: 14px; min-width: 420px; }
        .cm-tbl thead th { text-align: left; font-size: 11px; letter-spacing: .05em; text-transform: uppercase; color: var(--muted, #6b7685); font-weight: 700; padding: 11px 16px; border-bottom: 1px solid var(--line, #E3E8EF); background: var(--surface-2, #F7F9FB); }
        .cm-tbl th.num, .cm-tbl td.num { text-align: right; font-variant-numeric: tabular-nums; }
        .cm-tbl tbody td { padding: 13px 16px; border-bottom: 1px solid var(--line, #E3E8EF); color: var(--ink, #1f2733); }
        .cm-tbl td.num { font-weight: 600; }
        .cm-tbl tbody tr:last-child td { border-bottom: none; }
        .cm-tbl tfoot td { padding: 13px 16px; font-weight: 800; font-variant-numeric: tabular-nums; border-top: 2px solid var(--line, #E3E8EF); }
        .cm-lbl { text-align: right; color: var(--muted, #6b7685); font-weight: 700; text-transform: uppercase; font-size: 11px; letter-spacing: .05em; }
        .cm-empty { color: var(--muted, #6b7685); font-style: italic; text-align: center; }
        .cm-words { font-size: 14px; color: var(--ink, #1f2733); }
        .cm-words .cm-l { display: block; margin-bottom: 3px; }
        .cm-payrow { display: flex; flex-wrap: wrap; gap: 12px 40px; }
        .cm-payrow > div { display: flex; flex-direction: column; gap: 3px; }
        @media (max-width: 640px) { .cm-kv { grid-template-columns: 1fr 1fr; } .cm-summary .cm-sum-amt { text-align: left; } }
    </style>

    <div class="x-header-container sub-nav-sticky">
        <div class="container-title">
            <div style="display: flex; flex-direction: column;">
                <span class="page-title">Collection Management</span>
                <p class="page-links">
                    @if ($log->archived_at)
                        <a href="{{ route('home') }}">Home</a> |
                        <a href="{{ route('archives') }}">Archives</a> |
                        <a href="{{ route('archives', ['tab' => 'collection-management']) }}">Collection Management</a> |
                        <span class="page-links-accent">{{ $log->payee }} - {{ $log->serial_number }}</span>
                    @else
                        <a href="{{ route('home') }}">Home</a> |
                        <a href="{{ route('collections') }}">Collections Management</a> |
                        <a href="{{ route('transaction-entry') }}">Transactions Entry</a> |
                        <span>{{ $formName }}</span> |
                        <span>View</span> |
                        <span class="page-links-accent">{{ $log->payee }}</span>
                    @endif
                </p>
            </div>
        </div>
        {{-- Tabs hidden on view page --}}
    </div>

    <div class="collection-content">
        <div class="ctc-view-page">

            {{-- ── Status banner ──────────────────────────────────────────── --}}
            <div class="ctc-view-meta">
                <span class="status-badge status-{{ strtolower($log->status) }}">{{ $log->status }}</span>
                <span class="ctc-view-meta-info">{{ $log->transacted_at->format('F j, Y — h:i A') }}</span>
                @if($log->archived_at)
                    <span class="ctc-view-archived-badge">Archived</span>
                @endif
                @if($hasPendingRequest)
                    <span class="ctc-view-pending-badge">Cancel Request Pending</span>
                @endif
            </div>

            {{-- ── Cancel request detail (admin view) ─────────────────────── --}}
            @if($hasPendingRequest && ($isAdmin ?? false) && isset($pendingRequest))
            <div class="ctc-cancel-request-card">
                <div class="ctc-cancel-request-card-header">
                    <span class="ctc-cancel-request-card-label">Cancel Request</span>
                    <span class="ctc-cancel-request-card-meta">
                        by <strong>{{ $pendingRequest->requestedByUser?->name ?? 'Unknown' }}</strong>
                        &nbsp;·&nbsp;
                        {{ $pendingRequest->created_at->diffForHumans() }}
                    </span>
                </div>
                <div class="ctc-cancel-request-card-reason">
                    @if($pendingRequest->reason)
                        <span class="ctc-cancel-request-card-reason-label">Reason:</span>
                        <span class="ctc-cancel-request-card-reason-text">{{ $pendingRequest->reason }}</span>
                    @else
                        <span class="ctc-cancel-request-card-reason-none">No reason provided.</span>
                    @endif
                </div>
            </div>
            @endif

            {{-- ── Form content (per form type) ──────────────────────────── --}}
            @if(!$t)
                {{-- No linked transaction data — show basic info --}}
                <div class="ctc-view-generic">
                    <div class="ctc-view-generic-row">
                        <span class="ctc-view-generic-label">Serial Number</span>
                        <span class="ctc-view-generic-value">{{ $log->serial_number }}</span>
                    </div>
                    <div class="ctc-view-generic-row">
                        <span class="ctc-view-generic-label">Payee</span>
                        <span class="ctc-view-generic-value">{{ $log->payee }}</span>
                    </div>
                    <div class="ctc-view-generic-row">
                        <span class="ctc-view-generic-label">Form Type</span>
                        <span class="ctc-view-generic-value">{{ $formName }} ({{ $log->form_type }})</span>
                    </div>
                    <div class="ctc-view-generic-row">
                        <span class="ctc-view-generic-label">Date</span>
                        <span class="ctc-view-generic-value">{{ $log->transacted_at->format('F j, Y') }}</span>
                    </div>
                    <div class="ctc-view-generic-row">
                        <span class="ctc-view-generic-label">Time</span>
                        <span class="ctc-view-generic-value">{{ $log->transacted_at->format('h:i:s A') }}</span>
                    </div>
                </div>
            @else
                {{-- ══ Clean read-only screen view ══════════════════════════ --}}
                <div class="cm-screen">
                    <div class="cm-card cm-summary">
                        <div>
                            <div class="cm-sum-l">{{ $formName }} · {{ $log->serial_number }}</div>
                            <div class="cm-sum-n">{{ $log->payee }}</div>
                            <div class="cm-sum-m">Issued {{ $log->transacted_at->format('F j, Y') }}</div>
                        </div>
                        <div class="cm-sum-amt">
                            <div class="cm-sum-al">Amount</div>
                            <div class="cm-sum-av">₱ {{ number_format((float) $log->amount, 2) }}</div>
                        </div>
                    </div>

                    @if($log->payment_method)
                    <div class="cm-card">
                        <div class="cm-h"><span class="cm-k">Payment</span></div>
                        <div class="cm-b">
                            <div class="cm-payrow">
                                <div><span class="cm-l">Method</span><span class="cm-v">{{ ucwords(str_replace('_', ' ', $log->payment_method)) }}</span></div>
                                <div><span class="cm-l">Amount</span><span class="cm-v" style="font-variant-numeric:tabular-nums">₱ {{ number_format((float) $log->amount, 2) }}</span></div>
                                @if($log->payment_method === 'cheque')
                                    <div><span class="cm-l">Bank</span><span class="cm-v">{{ $log->payer_bank_name ?: '—' }}</span></div>
                                    <div><span class="cm-l">Cheque No.</span><span class="cm-v">{{ $log->payment_reference ?: '—' }}</span></div>
                                    <div><span class="cm-l">Cheque Date</span><span class="cm-v">{{ optional($log->payment_reference_date)->format('m/d/Y') ?: '—' }}</span></div>
                                @elseif($log->payment_method === 'online')
                                    <div><span class="cm-l">Channel</span><span class="cm-v">{{ $log->payment_channel ?: '—' }}</span></div>
                                    <div><span class="cm-l">Reference</span><span class="cm-v">{{ $log->payment_reference ?: '—' }}</span></div>
                                    <div><span class="cm-l">Date</span><span class="cm-v">{{ optional($log->payment_reference_date)->format('m/d/Y') ?: '—' }}</span></div>
                                @elseif($log->payment_method === 'money_order')
                                    <div><span class="cm-l">MO No.</span><span class="cm-v">{{ $log->payment_reference ?: '—' }}</span></div>
                                    <div><span class="cm-l">Date</span><span class="cm-v">{{ optional($log->payment_reference_date)->format('m/d/Y') ?: '—' }}</span></div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    @switch($log->form_type)
                        @case('Form 5IC')
                            <div class="cm-card">
                                <div class="cm-h"><span class="cm-k">Collection Details</span></div>
                                <div class="cm-tbl-wrap"><table class="cm-tbl">
                                    <thead><tr><th>Nature of Collection</th><th>Account Code</th><th class="num">Amount</th></tr></thead>
                                    <tbody>
                                        @forelse(collect($t->items ?? [])->filter(fn ($i) => ($i['description'] ?? '') !== '' || (float) ($i['amount'] ?? 0) > 0) as $item)
                                            <tr><td>{{ $item['description'] ?? '—' }}</td><td>{{ $item['account_code'] ?? '—' }}</td><td class="num">{{ number_format((float) ($item['amount'] ?? 0), 2) }}</td></tr>
                                        @empty
                                            <tr><td colspan="3" class="cm-empty">No items.</td></tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot><tr><td class="cm-lbl" colspan="2">Total</td><td class="num">₱ {{ number_format((float) $t->total, 2) }}</td></tr></tfoot>
                                </table></div>
                            </div>
                            <div class="cm-card">
                                <div class="cm-h"><span class="cm-k">Receipt Information</span></div>
                                <div class="cm-b cm-stack">
                                    <div class="cm-kv">
                                        <div class="cm-cell"><span class="cm-l">Payor</span><span class="cm-v">{{ $t->payor ?: '—' }}</span></div>
                                        <div class="cm-cell"><span class="cm-l">Agency</span><span class="cm-v">{{ $t->agency ?: '—' }}</span></div>
                                        <div class="cm-cell"><span class="cm-l">Fund</span><span class="cm-v">{{ $t->fund ?: '—' }}</span></div>
                                        <div class="cm-cell"><span class="cm-l">Receipt No.</span><span class="cm-v">{{ $t->certificate_number }}</span></div>
                                        <div class="cm-cell"><span class="cm-l">Date Issued</span><span class="cm-v">{{ $fmtDate($t->date_issued) ?: '—' }}</span></div>
                                        <div class="cm-cell"><span class="cm-l">Form Type</span><span class="cm-v">{{ $formName }} · {{ $log->form_type }}</span></div>
                                    </div>
                                    @if($t->amount_in_words)<div class="cm-words"><span class="cm-l">Amount in Words</span>{{ $t->amount_in_words }}</div>@endif
                                </div>
                            </div>
                            @break

                        @case('BIR0016')
                            <div class="cm-card">
                                <div class="cm-h"><span class="cm-k">Certificate Holder</span></div>
                                <div class="cm-b"><div class="cm-kv">
                                    <div class="cm-cell"><span class="cm-l">Name</span><span class="cm-v">{{ trim($t->surname . ', ' . $t->first_name . ' ' . ($t->middle_name ?? '')) }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">TIN</span><span class="cm-v">{{ $t->tin ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Sex</span><span class="cm-v">{{ $t->sex ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Civil Status</span><span class="cm-v">{{ $t->civil_status ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Citizenship</span><span class="cm-v">{{ $t->citizenship ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Date of Birth</span><span class="cm-v">{{ $fmtDate($t->date_of_birth) ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Place of Birth</span><span class="cm-v">{{ $t->place_of_birth ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Profession</span><span class="cm-v">{{ $t->profession ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Height / Weight</span><span class="cm-v">{{ $t->height ?: '—' }} / {{ $t->weight ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Place of Issue</span><span class="cm-v">{{ $t->place_of_issue ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Year</span><span class="cm-v">{{ $t->year ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Date Issued</span><span class="cm-v">{{ $fmtDate($t->date_issued) ?: '—' }}</span></div>
                                </div></div>
                            </div>
                            <div class="cm-card">
                                <div class="cm-h"><span class="cm-k">Community Tax</span></div>
                                <div class="cm-tbl-wrap"><table class="cm-tbl">
                                    <thead><tr><th>Basis</th><th class="num">Taxable Amount</th><th class="num">Tax Due</th></tr></thead>
                                    <tbody>
                                        <tr><td>Basic Community Tax</td><td class="num">—</td><td class="num">{{ $fmt($t->a_community_tax_due) ?: '0.00' }}</td></tr>
                                        @if($t->item1_taxable_amount)<tr><td>Additional — Item 1</td><td class="num">{{ $fmt($t->item1_taxable_amount) }}</td><td class="num">{{ $fmt($t->item1_community_tax_due) }}</td></tr>@endif
                                        @if($t->item2_taxable_amount)<tr><td>Additional — Item 2</td><td class="num">{{ $fmt($t->item2_taxable_amount) }}</td><td class="num">{{ $fmt($t->item2_community_tax_due) }}</td></tr>@endif
                                        @if($t->item3_taxable_amount)<tr><td>Additional — Item 3</td><td class="num">{{ $fmt($t->item3_taxable_amount) }}</td><td class="num">{{ $fmt($t->item3_community_tax_due) }}</td></tr>@endif
                                        @if($t->interest)<tr><td>Interest</td><td class="num">—</td><td class="num">{{ $fmt($t->interest) }}</td></tr>@endif
                                    </tbody>
                                    <tfoot><tr><td class="cm-lbl" colspan="2">Total Tax Due</td><td class="num">₱ {{ $fmt($t->total_community_tax_due) ?: $fmt($t->amount_paid) }}</td></tr></tfoot>
                                </table></div>
                            </div>
                            @break

                        @case('BIR0017')
                            <div class="cm-card">
                                <div class="cm-h"><span class="cm-k">Corporation Details</span></div>
                                <div class="cm-b"><div class="cm-kv">
                                    <div class="cm-cell"><span class="cm-l">Company Name</span><span class="cm-v">{{ $t->company_name ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">TIN</span><span class="cm-v">{{ $t->tin ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Address</span><span class="cm-v">{{ $t->address ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Date of Registration</span><span class="cm-v">{{ $fmtDate($t->date_of_registration) ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Kind of Organization</span><span class="cm-v">{{ $t->kind_of_organization ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Nature of Business</span><span class="cm-v">{{ $t->nature_of_business ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Place of Issue</span><span class="cm-v">{{ $t->place_of_issue ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Year</span><span class="cm-v">{{ $t->year ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Date Issued</span><span class="cm-v">{{ $fmtDate($t->date_issued) ?: '—' }}</span></div>
                                </div></div>
                            </div>
                            <div class="cm-card">
                                <div class="cm-h"><span class="cm-k">Community Tax</span></div>
                                <div class="cm-tbl-wrap"><table class="cm-tbl">
                                    <thead><tr><th>Basis</th><th class="num">Taxable Amount</th><th class="num">Tax Due</th></tr></thead>
                                    <tbody>
                                        <tr><td>Basic Community Tax</td><td class="num">—</td><td class="num">{{ $fmt($t->a_community_tax_due) ?: '0.00' }}</td></tr>
                                        @if($t->item1_taxable_amount)<tr><td>Additional — Item 1</td><td class="num">{{ $fmt($t->item1_taxable_amount) }}</td><td class="num">{{ $fmt($t->item1_community_tax_due) }}</td></tr>@endif
                                        @if($t->item2_taxable_amount)<tr><td>Additional — Item 2</td><td class="num">{{ $fmt($t->item2_taxable_amount) }}</td><td class="num">{{ $fmt($t->item2_community_tax_due) }}</td></tr>@endif
                                        @if($t->interest)<tr><td>Interest</td><td class="num">—</td><td class="num">{{ $fmt($t->interest) }}</td></tr>@endif
                                    </tbody>
                                    <tfoot><tr><td class="cm-lbl" colspan="2">Total Tax Due</td><td class="num">₱ {{ $fmt($t->total_community_tax_due) ?: $fmt($t->amount_paid) }}</td></tr></tfoot>
                                </table></div>
                            </div>
                            @break

                        @case('Form 56')
                            <div class="cm-card">
                                <div class="cm-h"><span class="cm-k">Properties</span><span class="cm-sub">{{ $t->entries->count() }} property(ies)</span></div>
                                <div class="cm-tbl-wrap"><table class="cm-tbl" style="min-width:640px">
                                    <thead><tr><th>Declared Owner</th><th>TD No.</th><th>Location</th><th class="num">Assessed</th><th class="num">Tax Due</th><th class="num">Amount</th></tr></thead>
                                    <tbody>
                                        @forelse($t->entries as $e)
                                            <tr>
                                                <td>{{ $e->property->declared_owner ?? '—' }}</td>
                                                <td>{{ $e->property->tax_declaration_number ?? '—' }}</td>
                                                <td>{{ $e->property->location ?? '—' }}</td>
                                                <td class="num">{{ $fmt($e->property->assessed_value_total ?? 0) }}</td>
                                                <td class="num">{{ $fmt($e->tax_due) }}</td>
                                                <td class="num">{{ $fmt($e->amount) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="cm-empty">No properties.</td></tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot><tr><td class="cm-lbl" colspan="5">Amount Paid</td><td class="num">₱ {{ $fmt($t->amount_paid) }}</td></tr></tfoot>
                                </table></div>
                            </div>
                            <div class="cm-card">
                                <div class="cm-h"><span class="cm-k">Receipt Information</span></div>
                                <div class="cm-b cm-stack">
                                    <div class="cm-kv">
                                        <div class="cm-cell"><span class="cm-l">Client</span><span class="cm-v">{{ $t->client_name ?: '—' }}</span></div>
                                        <div class="cm-cell"><span class="cm-l">Municipality / Province</span><span class="cm-v">{{ $t->municipality_province ?: '—' }}</span></div>
                                        <div class="cm-cell"><span class="cm-l">City</span><span class="cm-v">{{ $t->city ?: '—' }}</span></div>
                                        <div class="cm-cell"><span class="cm-l">Receipt No.</span><span class="cm-v">{{ $t->certificate_number }}</span></div>
                                        <div class="cm-cell"><span class="cm-l">Prev. Receipt</span><span class="cm-v">{{ $t->previous_receipt_number ?: '—' }}</span></div>
                                        <div class="cm-cell"><span class="cm-l">Date</span><span class="cm-v">{{ $fmtDate($t->transaction_date) ?: '—' }}</span></div>
                                    </div>
                                    @if($t->payment_in_words)<div class="cm-words"><span class="cm-l">Payment in Words</span>{{ $t->payment_in_words }}</div>@endif
                                </div>
                            </div>
                            @break

                        @case('Form 10')
                            <div class="cm-card">
                                <div class="cm-h"><span class="cm-k">Marriage Details</span></div>
                                <div class="cm-b"><div class="cm-kv">
                                    <div class="cm-cell"><span class="cm-l">Husband</span><span class="cm-v">{{ $t->husband_name ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Husband Age</span><span class="cm-v">{{ $t->husband_age_years ? $t->husband_age_years . ' yrs' : '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Husband Address</span><span class="cm-v">{{ $t->husband_address ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Wife</span><span class="cm-v">{{ $t->wife_name ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Wife Age</span><span class="cm-v">{{ $t->wife_age_years ? $t->wife_age_years . ' yrs' : '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Wife Address</span><span class="cm-v">{{ $t->wife_address ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Registry Number</span><span class="cm-v">{{ $t->registry_number ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Local Civil Registrar</span><span class="cm-v">{{ $t->local_civil_registrar_of ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Certificate No.</span><span class="cm-v">{{ $t->certificate_number }}</span></div>
                                </div></div>
                            </div>
                            @break

                        @case('Form 58')
                            <div class="cm-card">
                                <div class="cm-h"><span class="cm-k">Burial Permit Details</span></div>
                                <div class="cm-b"><div class="cm-kv">
                                    <div class="cm-cell"><span class="cm-l">Deceased</span><span class="cm-v">{{ $t->deceased_name ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Nationality</span><span class="cm-v">{{ $t->nationality ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Age / Sex</span><span class="cm-v">{{ $t->age ?: '—' }} / {{ $t->sex ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Date of Death</span><span class="cm-v">{{ $fmtDate($t->date_of_death) ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Cause of Death</span><span class="cm-v">{{ $t->cause_of_death ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Cemetery</span><span class="cm-v">{{ $t->cemetery_name ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Applicant</span><span class="cm-v">{{ $t->applicant_name ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Permission</span><span class="cm-v">{{ $t->permission_type ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">City / Municipality</span><span class="cm-v">{{ $t->city_municipality ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Province</span><span class="cm-v">{{ $t->province ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Date Issued</span><span class="cm-v">{{ $fmtDate($t->date_issued) ?: '—' }}</span></div>
                                    <div class="cm-cell"><span class="cm-l">Signed By</span><span class="cm-v">{{ $t->municipal_secretary ?: '—' }}</span></div>
                                </div></div>
                            </div>
                            @break
                    @endswitch
                </div>

                {{-- ══ Print-only receipt replica (unchanged; used for printing) ══ --}}
                <div class="cm-print">
                @switch($log->form_type)

                    {{-- ── BIR0016 — Individual Cedula ──────────────────── --}}
                    @case('BIR0016')
                        <div class="ctc-view-doc-wrap">
                            <p class="ctc-preview-caption">BIR Form 0016 (December, 2014)</p>
                            <div class="ctcp-page ctcp-page--individual ctcp-page--view">
                                <div class="ctc-field ctc-field--title" style="left:0;top:0;width:438px;height:42px;">
                                    <p>Community Tax Certificate</p>
                                </div>
                                <div class="ctc-field ctc-badge" style="left:438px;top:0;width:172px;height:42px;">
                                    <p>Individual</p>
                                </div>
                                <div class="ctc-field ctc-cert-no" style="left:610px;top:0;width:272px;height:58px;">
                                    <span class="ctc-cert-no-prefix-input">{{ $t->certificate_prefix }}</span>
                                    <span class="ctc-cert-no-input">{{ $t->certificate_number }}</span>
                                </div>

                                <div class="ctc-field ctc-input-wrap filled" style="left:0;top:42px;width:69px;height:42px;">
                                    <label class="ctc-input-caption">Year</label>
                                    <span class="ctcp-value">{{ $t->year }}</span>
                                </div>
                                <div class="ctc-field ctc-input-wrap filled" style="left:69px;top:42px;width:369px;height:42px;">
                                    <label class="ctc-input-caption">Place of Issue (City/Mun./Prov.)</label>
                                    <span class="ctcp-value">{{ $t->place_of_issue }}</span>
                                </div>
                                <div class="ctc-field ctc-input-wrap filled" style="left:438px;top:42px;width:172px;height:42px;">
                                    <label class="ctc-input-caption">Date Issued</label>
                                    <span class="ctcp-value">{{ $fmtDate($t->date_issued) }}</span>
                                </div>

                                <div class="ctc-field ctc-divider" style="left:610px;top:58px;width:272px;height:5px;"></div>
                                <div class="ctc-field ctc-copy-label" style="left:610px;top:63px;width:272px;height:21px;">
                                    <p>Taxpayer's Copy</p>
                                </div>

                                <div class="ctc-field ctc-input-wrap filled" style="left:0;top:84px;width:203.333px;height:42px;">
                                    <label class="ctc-input-caption">Name (Surname)</label>
                                    <span class="ctcp-value">{{ $t->surname }}</span>
                                </div>
                                <div class="ctc-field ctc-input-wrap filled" style="left:203.33px;top:84px;width:203.333px;height:42px;">
                                    <label class="ctc-input-caption">First Name</label>
                                    <span class="ctcp-value">{{ $t->first_name }}</span>
                                </div>
                                <div class="ctc-field ctc-input-wrap filled" style="left:406.67px;top:84px;width:203.333px;height:42px;">
                                    <label class="ctc-input-caption">Middle Name</label>
                                    <span class="ctcp-value">{{ $t->middle_name }}</span>
                                </div>

                                <div class="ctc-field" style="left:610px;top:84px;width:272px;height:55px;">
                                    <p class="ctc-tin-label">TIN (if Any)</p>
                                    <div class="ctc-tin-group">
                                        @for ($group = 0; $group < 5; $group++)
                                            <div class="ctc-tin-cell-group">
                                                @for ($cell = 0; $cell < 3; $cell++)
                                                    @php $tinIndex = $group * 3 + $cell; @endphp
                                                    <div class="ctc-tin-cell">{{ trim($tinChars[$tinIndex] ?? '') }}</div>
                                                @endfor
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="ctc-field ctc-input-wrap filled" style="left:0;top:126px;width:610px;height:42px;">
                                    <label class="ctc-input-caption">Date Issued</label>
                                    <span class="ctcp-value">{{ $fmtDate($t->date_issued_2) }}</span>
                                </div>

                                <div class="ctc-field ctc-sex-row" style="left:610px;top:139px;width:272px;height:29px;">
                                    <p class="ctc-radio-label">Sex</p>
                                    @foreach(['Male','Female'] as $sexOpt)
                                        <label class="ctc-radio-group">
                                            <span class="ctcp-radio {{ $t->sex === $sexOpt ? 'checked' : '' }}"></span>
                                            <p>{{ $sexOpt }}</p>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="ctc-field ctc-input-wrap filled" style="left:0;top:168px;width:198px;height:42px;">
                                    <label class="ctc-input-caption">Citizenship</label>
                                    <span class="ctcp-value">{{ $t->citizenship }}</span>
                                </div>
                                <div class="ctc-field ctc-input-wrap filled" style="left:198px;top:168px;width:198px;height:42px;">
                                    <label class="ctc-input-caption">ICR No. (if any)</label>
                                    <span class="ctcp-value">{{ $t->icr_no }}</span>
                                </div>
                                <div class="ctc-field ctc-input-wrap filled" style="left:396px;top:168px;width:350px;height:42px;">
                                    <label class="ctc-input-caption">Place of Birth</label>
                                    <span class="ctcp-value">{{ $t->place_of_birth }}</span>
                                </div>
                                <div class="ctc-field ctc-input-wrap filled" style="left:746px;top:168px;width:136px;height:42px;">
                                    <label class="ctc-input-caption">Height</label>
                                    <span class="ctcp-value">{{ $t->height }}</span>
                                </div>

                                <div class="ctc-field ctc-civil-row" style="left:0;top:210px;width:610px;height:42px;">
                                    <p class="ctc-radio-label">Civil<br>Status</p>
                                    @foreach(['Single','Married','Divorced','Widow / Widower / Legally Separated'] as $cs)
                                        <label class="ctc-radio-group">
                                            <span class="ctcp-radio {{ $t->civil_status === $cs ? 'checked' : '' }}"></span>
                                            <p>{{ $cs }}</p>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="ctc-field ctc-input-wrap filled" style="left:610px;top:210px;width:136px;height:42px;">
                                    <label class="ctc-input-caption">Date of Birth</label>
                                    <span class="ctcp-value">{{ $fmtDate($t->date_of_birth) }}</span>
                                </div>
                                <div class="ctc-field ctc-input-wrap filled" style="left:746px;top:210px;width:136px;height:42px;">
                                    <label class="ctc-input-caption">Weight</label>
                                    <span class="ctcp-value">{{ $t->weight }}</span>
                                </div>

                                <div class="ctc-field ctc-input-wrap filled" style="left:0;top:252px;width:610px;height:42px;">
                                    <label class="ctc-input-caption">Profession / Occupation / Business</label>
                                    <span class="ctcp-value">{{ $t->profession }}</span>
                                </div>
                                <div class="ctc-field ctc-col-header" style="left:610px;top:252px;width:136px;height:42px;"><div><p>Taxable</p><p>amount</p></div></div>
                                <div class="ctc-field ctc-col-header" style="left:746px;top:252px;width:136px;height:42px;"><div><p>Community</p><p>Tax due</p></div></div>

                                <div class="ctc-field ctc-tax-row" style="left:0;top:294px;width:610px;height:42px;">
                                    <p>A. Basic Community Tax (P 5.00) Voluntary or Exempted (P 1.00)</p>
                                </div>
                                <div class="ctc-field" style="left:610px;top:294px;width:136px;height:42px;"></div>
                                <div class="ctc-field ctc-amount-cell" style="left:746px;top:294px;width:136px;height:42px;">
                                    <span class="ctc-peso-prefix">&#8369;</span>
                                    <span class="ctcp-amount-value">{{ $fmt($t->a_community_tax_due) }}</span>
                                </div>

                                <div class="ctc-field ctc-tax-row" style="left:0;top:336px;width:610px;height:42px;">
                                    <p>B. Additional Community Tax (Tax not to exceed P 5.00)</p>
                                </div>
                                <div class="ctc-field" style="left:610px;top:336px;width:136px;height:42px;"></div>
                                <div class="ctc-field" style="left:746px;top:336px;width:136px;height:42px;"></div>

                                <div class="ctc-field ctc-tax-item" style="left:0;top:378px;width:610px;height:42px;">
                                    <ol start="1"><li>Gross receipts or earnings derived from business during the preceding year (P 1.00 for every P 1,000.00)</li></ol>
                                </div>
                                <div class="ctc-field ctc-amount-cell" style="left:610px;top:378px;width:136px;height:42px;">
                                    <span class="ctc-peso-prefix">&#8369;</span>
                                    <span class="ctcp-amount-value">{{ $fmt($t->item1_taxable_amount) }}</span>
                                </div>
                                <div class="ctc-field ctc-amount-cell" style="left:746px;top:378px;width:136px;height:42px;">
                                    <span class="ctcp-amount-value">{{ $fmt($t->item1_community_tax_due) }}</span>
                                </div>

                                <div class="ctc-field ctc-tax-item" style="left:0;top:420px;width:610px;height:42px;">
                                    <ol start="2"><li>Salaries or gross receipt or earnings derived from exercise of profession or pursuit of any occupation (P 100.00 for every P 1,000.00)</li></ol>
                                </div>
                                <div class="ctc-field ctc-amount-cell" style="left:610px;top:420px;width:136px;height:42px;">
                                    <span class="ctcp-amount-value">{{ $fmt($t->item2_taxable_amount) }}</span>
                                </div>
                                <div class="ctc-field ctc-amount-cell" style="left:746px;top:420px;width:136px;height:42px;">
                                    <span class="ctcp-amount-value">{{ $fmt($t->item2_community_tax_due) }}</span>
                                </div>

                                <div class="ctc-field ctc-tax-item" style="left:0;top:462px;width:610px;height:42px;">
                                    <ol start="3"><li>Income from real property (P 1.00 for every P 1,000.00)</li></ol>
                                </div>
                                <div class="ctc-field ctc-amount-cell" style="left:610px;top:462px;width:136px;height:42px;">
                                    <span class="ctcp-amount-value">{{ $fmt($t->item3_taxable_amount) }}</span>
                                </div>
                                <div class="ctc-field ctc-amount-cell" style="left:746px;top:462px;width:136px;height:42px;">
                                    <span class="ctcp-amount-value">{{ $fmt($t->item3_community_tax_due) }}</span>
                                </div>

                                <div class="ctc-field ctc-signature-box" style="left:0;top:504px;width:198px;height:168px;"><p>Right Thumb Print</p></div>
                                <div class="ctc-field ctc-signature-box" style="left:198px;top:504px;width:412px;height:84px;"><p>Taxpayer's Signature</p></div>
                                <div class="ctc-field ctc-cell-label" style="left:610px;top:504px;width:136px;height:42px;">Total</div>
                                <div class="ctc-field ctc-amount-cell" style="left:746px;top:504px;width:136px;height:42px;">
                                    <span class="ctc-peso-prefix">&#8369;</span>
                                    <span class="ctcp-amount-value">{{ $fmt($t->total_community_tax_due) }}</span>
                                </div>

                                <div class="ctc-field ctc-cell-label" style="left:610px;top:546px;width:136px;height:42px;">Interest</div>
                                <div class="ctc-field ctc-amount-cell" style="left:746px;top:546px;width:136px;height:42px;">
                                    <span class="ctcp-amount-value">{{ $fmt($t->interest) }}</span>
                                </div>

                                <div class="ctc-field" style="left:198px;top:588px;width:412px;height:84px;">
                                    <div class="ctcp-treasurer">
                                        <p class="ctc-sidebar-treasurer-name">{{ $t->treasurer_name }}</p>
                                        <p class="ctc-sidebar-treasurer-title">Municipal Treasurer</p>
                                        <div class="ctcp-treasurer-divider"></div>
                                        <p class="ctc-sidebar-treasurer-title">Municipal / City Treasurer</p>
                                    </div>
                                </div>
                                <div class="ctc-field ctc-col-header" style="left:610px;top:588px;width:136px;height:42px;"><div><p>Total</p><p>Amount Paid</p></div></div>
                                <div class="ctc-field ctc-amount-cell" style="left:746px;top:588px;width:136px;height:42px;">
                                    <span class="ctc-peso-prefix">&#8369;</span>
                                    <span class="ctcp-amount-value">{{ $fmt($t->amount_paid) }}</span>
                                </div>

                                <div class="ctc-field" style="left:610px;top:630px;width:272px;height:42px;align-items:flex-start;padding:10px;">
                                    <p class="ctc-sidebar-words-value" style="margin:0;">{{ $t->amount_in_words }}</p>
                                </div>
                            </div>
                            <p class="ctc-preview-caption">DOP: 05.14.2021</p>
                        </div>
                        @break

                    {{-- ── BIR0017 — Corporation Cedula ─────────────────── --}}
                    @case('BIR0017')
                        @php $tinChars = str_split(str_pad($t->tin ?? '', 15, ' ')); @endphp
                        <div class="ctc-view-doc-wrap">
                            <p class="ctc-preview-caption">BIR Form 0017 (December, 2014)</p>
                            <div class="ctcp-page ctcp-page--corporation ctcp-page--view">
                                <div class="ctc-field ctc-field--title" style="left:0;top:0;width:438px;height:42px;"><p>Community Tax Certificate</p></div>
                                <div class="ctc-field ctc-badge ctc-badge--corporation" style="left:438px;top:0;width:172px;height:42px;"><p>Corporation</p></div>
                                <div class="ctc-field ctc-cert-no" style="left:610px;top:0;width:272px;height:58px;">
                                    <span class="ctc-cert-no-prefix-input">{{ $t->certificate_prefix }}</span>
                                    <span class="ctc-cert-no-input">{{ $t->certificate_number }}</span>
                                </div>

                                <div class="ctc-field ctc-input-wrap filled" style="left:0;top:42px;width:69px;height:42px;">
                                    <label class="ctc-input-caption">Year</label>
                                    <span class="ctcp-value">{{ $t->year }}</span>
                                </div>
                                <div class="ctc-field ctc-input-wrap filled" style="left:69px;top:42px;width:369px;height:42px;">
                                    <label class="ctc-input-caption">Place of Issue (City/Mun./Prov.)</label>
                                    <span class="ctcp-value">{{ $t->place_of_issue }}</span>
                                </div>
                                <div class="ctc-field ctc-input-wrap filled" style="left:438px;top:42px;width:172px;height:42px;">
                                    <label class="ctc-input-caption">Date Issued</label>
                                    <span class="ctcp-value">{{ $fmtDate($t->date_issued) }}</span>
                                </div>

                                <div class="ctc-field ctc-divider" style="left:610px;top:58px;width:272px;height:5px;"></div>
                                <div class="ctc-field ctc-copy-label" style="left:610px;top:63px;width:272px;height:21px;"><p>Taxpayer's Copy</p></div>

                                <div class="ctc-field ctc-input-wrap filled" style="left:0;top:84px;width:610px;height:42px;">
                                    <label class="ctc-input-caption">Company's Full Name</label>
                                    <span class="ctcp-value">{{ $t->company_name }}</span>
                                </div>
                                <div class="ctc-field ctc-field--tin-compact" style="left:610px;top:84px;width:272px;height:42px;">
                                    <p class="ctc-tin-label">TIN (if Any)</p>
                                    <div class="ctc-tin-group">
                                        @for ($group = 0; $group < 5; $group++)
                                            <div class="ctc-tin-cell-group">
                                                @for ($cell = 0; $cell < 3; $cell++)
                                                    @php $tinIndex = $group * 3 + $cell; @endphp
                                                    <div class="ctc-tin-cell">{{ trim($tinChars[$tinIndex] ?? '') }}</div>
                                                @endfor
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="ctc-field ctc-input-wrap filled" style="left:0;top:126px;width:700px;height:42px;">
                                    <label class="ctc-input-caption">Address of Principal Place of Business</label>
                                    <span class="ctcp-value">{{ $t->address }}</span>
                                </div>
                                <div class="ctc-field ctc-input-wrap ctc-date filled" style="left:700px;top:126px;width:182px;height:84px;">
                                    <label class="ctc-input-caption ctc-caption-multiline">Date of registration<br>/ incorporation</label>
                                    <span class="ctcp-value">{{ $fmtDate($t->date_of_registration) }}</span>
                                </div>

                                <div class="ctc-field ctc-civil-row" style="left:0;top:168px;width:700px;height:42px;">
                                    <p class="ctc-radio-label">Kind of<br>Organization</p>
                                    @foreach(['Corporation','Association','Partnership'] as $ko)
                                        <label class="ctc-radio-group">
                                            <span class="ctcp-radio {{ $t->kind_of_organization === $ko ? 'checked' : '' }}"></span>
                                            <p>{{ $ko }}</p>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="ctc-field ctc-input-wrap filled" style="left:0;top:210px;width:610px;height:42px;">
                                    <label class="ctc-input-caption">Kind / Nature of Business</label>
                                    <span class="ctcp-value">{{ $t->nature_of_business }}</span>
                                </div>
                                <div class="ctc-field ctc-col-header" style="left:610px;top:210px;width:136px;height:42px;"><div><p>Taxable</p><p>amount</p></div></div>
                                <div class="ctc-field ctc-col-header" style="left:746px;top:210px;width:136px;height:42px;"><div><p>Community</p><p>Tax due</p></div></div>

                                <div class="ctc-field ctc-tax-row" style="left:0;top:252px;width:610px;height:42px;">
                                    <p>A. Basic Community Tax (P 500.00)</p>
                                </div>
                                <div class="ctc-field" style="left:610px;top:252px;width:136px;height:42px;"></div>
                                <div class="ctc-field ctc-amount-cell" style="left:746px;top:252px;width:136px;height:42px;">
                                    <span class="ctc-peso-prefix">&#8369;</span>
                                    <span class="ctcp-amount-value">{{ $fmt($t->a_community_tax_due) }}</span>
                                </div>

                                <div class="ctc-field ctc-tax-row" style="left:0;top:294px;width:610px;height:42px;">
                                    <p>B. Additional Community Tax (Tax not to exceed P 10,000.00)</p>
                                </div>
                                <div class="ctc-field" style="left:610px;top:294px;width:136px;height:42px;"></div>
                                <div class="ctc-field" style="left:746px;top:294px;width:136px;height:42px;"></div>

                                <div class="ctc-field ctc-tax-item" style="left:0;top:336px;width:610px;height:42px;">
                                    <ol start="1"><li>Dividends received from all sources (P 2.00 for every P 5,000.00)</li></ol>
                                </div>
                                <div class="ctc-field ctc-amount-cell" style="left:610px;top:336px;width:136px;height:42px;">
                                    <span class="ctc-peso-prefix">&#8369;</span>
                                    <span class="ctcp-amount-value">{{ $fmt($t->item1_taxable_amount) }}</span>
                                </div>
                                <div class="ctc-field ctc-amount-cell" style="left:746px;top:336px;width:136px;height:42px;">
                                    <span class="ctcp-amount-value">{{ $fmt($t->item1_community_tax_due) }}</span>
                                </div>

                                <div class="ctc-field ctc-tax-item" style="left:0;top:378px;width:610px;height:42px;">
                                    <ol start="2"><li>Gross receipts or earnings derived from its businesses during the preceding year (P 2.00 for every P 5,000.00)</li></ol>
                                </div>
                                <div class="ctc-field ctc-amount-cell" style="left:610px;top:378px;width:136px;height:42px;">
                                    <span class="ctcp-amount-value">{{ $fmt($t->item2_taxable_amount) }}</span>
                                </div>
                                <div class="ctc-field ctc-amount-cell" style="left:746px;top:378px;width:136px;height:42px;">
                                    <span class="ctcp-amount-value">{{ $fmt($t->item2_community_tax_due) }}</span>
                                </div>

                                <div class="ctc-field ctc-signature-box" style="left:0;top:420px;width:198px;height:168px;"><p>Right Thumb Print</p></div>
                                <div class="ctc-field ctc-signature-box" style="left:198px;top:420px;width:412px;height:84px;"><p>Authorized Signatory</p></div>
                                <div class="ctc-field ctc-cell-label" style="left:610px;top:420px;width:136px;height:42px;">Total</div>
                                <div class="ctc-field ctc-amount-cell" style="left:746px;top:420px;width:136px;height:42px;">
                                    <span class="ctc-peso-prefix">&#8369;</span>
                                    <span class="ctcp-amount-value">{{ $fmt($t->total_community_tax_due) }}</span>
                                </div>

                                <div class="ctc-field ctc-cell-label" style="left:610px;top:462px;width:136px;height:42px;">Interest</div>
                                <div class="ctc-field ctc-amount-cell" style="left:746px;top:462px;width:136px;height:42px;">
                                    <span class="ctcp-amount-value">{{ $fmt($t->interest) }}</span>
                                </div>

                                <div class="ctc-field" style="left:198px;top:504px;width:412px;height:84px;">
                                    <div class="ctcp-treasurer">
                                        <p class="ctc-sidebar-treasurer-name">{{ $t->treasurer_name }}</p>
                                        <p class="ctc-sidebar-treasurer-title">Municipal Treasurer</p>
                                        <div class="ctcp-treasurer-divider"></div>
                                        <p class="ctc-sidebar-treasurer-title">Municipal / City Treasurer</p>
                                    </div>
                                </div>
                                <div class="ctc-field ctc-col-header" style="left:610px;top:504px;width:136px;height:42px;"><div><p>Total</p><p>Amount Paid</p></div></div>
                                <div class="ctc-field ctc-amount-cell" style="left:746px;top:504px;width:136px;height:42px;">
                                    <span class="ctc-peso-prefix">&#8369;</span>
                                    <span class="ctcp-amount-value">{{ $fmt($t->amount_paid) }}</span>
                                </div>

                                <div class="ctc-field" style="left:610px;top:546px;width:272px;height:42px;align-items:flex-start;padding:10px;">
                                    <p class="ctc-sidebar-words-value" style="margin:0;">{{ $t->amount_in_words }}</p>
                                </div>
                            </div>
                            <p class="ctc-preview-caption">DOP: 05.14.2021</p>
                        </div>
                        @break

                    {{-- ── Form 56 — OR RPT ─────────────────────────────── --}}
                    @case('Form 56')
                        @php $calYear = $t->transaction_date ? \Carbon\Carbon::parse($t->transaction_date)->format('y') : ''; @endphp
                        <div class="ctc-view-doc-wrap ctc-view-doc-wrap--rpt">
                            <div class="ctcp-rpt-receipt">
                                <div class="ctcp-rpt-receipt-logo"><p>Logo</p><p>Here</p></div>
                                <div class="ctcp-rpt-receipt-header">
                                    <p class="ctcp-rpt-receipt-prev">
                                        Previous Tax Receipt No.
                                        <span class="ctcp-rpt-blank">{{ $t->previous_receipt_number }}</span>
                                        dated
                                        <span class="ctcp-rpt-blank">{{ $t->previous_receipt_date }}</span>
                                        for the year 20<span class="ctcp-rpt-blank ctcp-rpt-blank--sm">{{ $t->previous_receipt_year }}</span>
                                    </p>
                                    <p class="ctcp-rpt-receipt-title">Official Receipt of the Republic of the Philippines</p>
                                    <p class="ctcp-rpt-receipt-subtitle">Provincial or City Treasurer's Real Property Tax Receipt</p>
                                </div>
                                <div class="ctcp-rpt-receipt-no">
                                    <span>No.&nbsp;</span><span>{{ $t->certificate_number }}</span>
                                </div>
                                <div class="ctcp-rpt-receipt-fields">
                                    <div class="ctcp-rpt-receipt-field ctcp-rpt-receipt-field--municipality">
                                        <label>Municipality / Province</label>
                                        <span>{{ $t->municipality_province }}</span>
                                    </div>
                                    <div class="ctcp-rpt-receipt-field ctcp-rpt-receipt-field--city">
                                        <label>City</label>
                                        <span>{{ $t->city }}</span>
                                    </div>
                                    <div class="ctcp-rpt-receipt-field ctcp-rpt-receipt-field--date">
                                        <label>Date</label>
                                        <span>{{ $fmtDate($t->transaction_date) }}</span>
                                    </div>
                                </div>
                                <div class="ctcp-rpt-receipt-body">
                                    <p class="ctcp-rpt-receipt-body-text">
                                        Received from
                                        <span class="ctcp-rpt-blank ctcp-rpt-blank--wide">{{ $t->client_name }}</span>
                                        the sum of
                                        <span class="ctcp-rpt-blank ctcp-rpt-blank--wide">{{ $t->payment_in_words }}</span>
                                        pesos (P
                                        <span class="ctcp-rpt-blank">{{ $t->amount_paid }}</span>)
                                        <span class="ctcp-rpt-receipt-body-flex">Philippine Currency, in full or as installment payment of REAL PROPERTY TAX for the Calendar Year 20<span class="ctcp-rpt-blank ctcp-rpt-blank--sm">{{ $calYear }}</span> Upon property described in the Assessment Rolls as follows:
                                            <span class="ctcp-rpt-receipt-checkboxes">
                                                <label>
                                                    <span class="ctcp-rpt-receipt-checkbox {{ $t->basic_tax ? 'checked' : '' }}"></span>
                                                    Basic Tax
                                                </label>
                                                <label>
                                                    <span class="ctcp-rpt-receipt-checkbox {{ $t->special_education_fund ? 'checked' : '' }}"></span>
                                                    Special Education Fund
                                                </label>
                                            </span>
                                        </span>
                                    </p>
                                </div>
                                <div class="ctcp-rpt-receipt-table-wrap">
                                    <table class="ctcp-rpt-receipt-table">
                                        <colgroup>
                                            <col style="width:10.41%"><col style="width:11.78%"><col style="width:11.78%"><col style="width:11.78%">
                                            <col style="width:6.33%"><col style="width:6.33%"><col style="width:6.33%"><col style="width:5.56%">
                                            <col style="width:2.97%"><col style="width:7.36%"><col style="width:6.46%"><col style="width:6.46%"><col style="width:6.46%">
                                        </colgroup>
                                        <thead>
                                            <tr>
                                                <th rowspan="2">Name of Declared Owner</th>
                                                <th rowspan="2">Location Number and Street or Barangay</th>
                                                <th rowspan="2">Lot and Block Number</th>
                                                <th rowspan="2">Tax Declaration Number</th>
                                                <th colspan="3">Assessed Value</th>
                                                <th rowspan="2">Tax Due</th>
                                                <th colspan="2">Installment</th>
                                                <th rowspan="2">Full Payment</th>
                                                <th rowspan="2">Penalty per Cent</th>
                                                <th rowspan="2">Total</th>
                                            </tr>
                                            <tr><th>Land</th><th>Improv'nt</th><th>Total</th><th>No.</th><th>Payment</th></tr>
                                        </thead>
                                        <tbody><tr><td colspan="13" style="text-align:center;color:#999;font-size:12px;padding:8px;">— Table entries are not stored —</td></tr></tbody>
                                    </table>
                                </div>
                                <p class="ctcp-rpt-receipt-total-line">Total taxes paid by Money Order, Treasury Warrant or Check No _____________ dated _________, 20 ____</p>
                                <div class="ctcp-rpt-receipt-divider ctcp-rpt-receipt-divider--1"></div>
                                <div class="ctcp-rpt-receipt-divider ctcp-rpt-receipt-divider--2"></div>
                                <p class="ctcp-rpt-receipt-footnote"><span class="ctcp-rpt-receipt-footnote-mark">*</span>Payment without penalty be made within the periods stated below if by installment</p>
                                <p class="ctcp-rpt-receipt-installment ctcp-rpt-receipt-installment--1">1st Installment - January 1 to March 31, of the year</p>
                                <p class="ctcp-rpt-receipt-installment ctcp-rpt-receipt-installment--2">2nd Installment - April 1 to June 30, of the year</p>
                                <p class="ctcp-rpt-receipt-installment ctcp-rpt-receipt-installment--3">3rd Installment - July 1 to September. 30, of the year</p>
                                <p class="ctcp-rpt-receipt-installment ctcp-rpt-receipt-installment--4">4th Installment - October 1 to December 31, of the year</p>
                                <div class="ctcp-rpt-receipt-signature">
                                    <p>Provincial or City Treasurer</p>
                                    <div class="ctcp-rpt-receipt-signature-name-group">
                                        <p class="ctcp-rpt-receipt-signature-name">{{ $t->treasurer_deputy }}</p>
                                        <p>Deputy</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @break

                    {{-- ── Form 5IC — Official Receipt ──────────────────── --}}
                    @case('Form 5IC')
                        <div class="ctc-view-doc-wrap ctc-view-doc-wrap--or">
                            <div class="ctc-or-col">
                                <div class="ctc-or-header">
                                    <div class="ctc-or-logo"><p>Logo<br>Here</p></div>
                                    <div class="ctc-or-header-stack">
                                        <div class="ctc-or-title"><p>Official Receipt of the<br>Republic of the Philippines</p></div>
                                        <div class="ctc-or-serial">
                                            <p class="ctc-or-serial-label">No.</p>
                                            <span class="ctc-or-serial-input">{{ $t->certificate_number }}</span>
                                            <p class="ctc-or-serial-label">U</p>
                                        </div>
                                        <div class="ctcp-or-field ctcp-or-field--full">
                                            <p class="ctc-or-box-label">Date Issued</p>
                                            <span class="ctcp-or-value">{{ $fmtDate($t->date_issued) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ctc-or-row">
                                    <div class="ctcp-or-field ctcp-or-field--agency">
                                        <p class="ctc-or-box-label">Agency</p>
                                        <span class="ctcp-or-value">{{ $t->agency }}</span>
                                    </div>
                                    <div class="ctcp-or-field ctcp-or-field--fund">
                                        <p class="ctc-or-box-label">Fund</p>
                                        <span class="ctcp-or-value">{{ $t->fund }}</span>
                                    </div>
                                </div>
                                <div class="ctc-or-row">
                                    <div class="ctcp-or-field ctcp-or-field--full">
                                        <p class="ctc-or-box-label">Payor</p>
                                        <span class="ctcp-or-value">{{ $t->payor }}</span>
                                    </div>
                                </div>
                                <div class="ctc-or-divider"></div>
                                <div class="ctc-or-table-wrap">
                                    <table class="ctc-or-table">
                                        <colgroup>
                                            <col style="width:54.3%;">
                                            <col style="width:22.85%;">
                                            <col style="width:22.85%;">
                                        </colgroup>
                                        <thead>
                                            <tr>
                                                <th>Nature of Collection</th>
                                                <th>Account Code</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($t->items ?? [] as $item)
                                                <tr>
                                                    <td>{{ $item['description'] ?? '' }}</td>
                                                    <td>{{ $item['account_code'] ?? '' }}</td>
                                                    <td>{{ isset($item['amount']) ? '₱' . number_format($item['amount'], 2) : '' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2">Total</td>
                                                <td>
                                                    <div class="ctc-or-total-value">
                                                        <span class="ctc-or-peso">₱</span>
                                                        <span>{{ $fmt($t->total) }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="ctc-or-box ctc-or-box--amount">
                                    <p class="ctc-or-box-label">Total Amount Paid</p>
                                    <div class="ctc-or-amount-value">
                                        <span class="ctc-or-peso">₱</span>
                                        <span>{{ $fmt($t->total) }}</span>
                                    </div>
                                </div>
                                <div class="ctc-or-box ctc-or-box--words">
                                    <p class="ctc-or-box-label">Amount in Words</p>
                                    <span class="ctcp-or-value">{{ $t->amount_in_words }}</span>
                                </div>
                                <div class="ctc-or-payment-row">
                                    <div class="ctc-or-payment-methods">
                                        @foreach(['cash' => 'Cash','check' => 'Check','money_order' => 'Money Order'] as $val => $label)
                                            <label class="ctc-radio-label">
                                                <span class="ctcp-or-radio {{ $t->payment_method === $val ? 'checked' : '' }}"></span>
                                                {{ $label }}
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="ctc-or-drawee">
                                        <div class="ctc-or-row">
                                            <div class="ctcp-or-field"><p class="ctc-or-box-label">Drawee Bank</p><span class="ctcp-or-value">{{ $t->drawee_bank }}</span></div>
                                            <div class="ctcp-or-field"><p class="ctc-or-box-label">Number</p><span class="ctcp-or-value">{{ $t->check_number }}</span></div>
                                            <div class="ctcp-or-field"><p class="ctc-or-box-label">Date</p><span class="ctcp-or-value">{{ $fmtDate($t->check_date) }}</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="ctc-or-box ctc-or-box--signature">
                                    <p class="ctc-or-box-label">Received the amount stated above</p>
                                    <p class="ctc-or-signature-name">Gemma D. Ferrer</p>
                                    <p class="ctc-or-signature-title">Municipal Treasurer</p>
                                    <div class="ctc-or-signature-divider"></div>
                                    <p class="ctc-or-signature-title">Collecting Officer</p>
                                </div>
                                <div class="ctc-or-box ctc-or-box--note">
                                    <p class="ctc-or-note">Note: <strong>Write the number and date of this receipt on the back of check or money order received.</strong></p>
                                </div>
                            </div>
                        </div>
                        @break

                    {{-- ── Form 10 — Marriage License ───────────────────── --}}
                    @case('Form 10')
                        <div class="ctc-view-doc-wrap ctc-view-doc-wrap--mc">
                            <div class="mc-paper">
                                <p class="mc-doc-line mc-doc-line--sm mc-doc-line--center">Form No. 10</p>
                                <p class="mc-doc-line mc-doc-line--bold mc-doc-line--center mc-doc-line--lg">REPUBLIC OF THE PHILIPPINES</p>
                                <p class="mc-doc-line mc-doc-line--sm mc-doc-line--center">CITY OR MUNICIPALITY OF PRIETO-DIAZ</p>
                                <p class="mc-doc-line mc-doc-line--sm mc-doc-line--center">PROVINCE OF SORSOGON</p>
                                <div class="mc-doc-no">
                                    <span>No.&nbsp;</span><span>{{ $t->certificate_number }}</span>
                                </div>
                                <div class="mc-doc-title-block">
                                    <div class="mc-doc-title-rule"></div>
                                    <div class="mc-doc-title-text"><p>MARRIAGE LICENSE AND FEE RECEIPT</p><p>OF TWO PESOS</p></div>
                                    <div class="mc-doc-title-rule"></div>
                                </div>
                                <p class="mc-doc-body">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;THIS IS TO CERTIFY that <span class="mc-doc-fill">{{ $t->husband_name }}</span>, aged <span class="mc-doc-fill mc-doc-fill--sm">{{ $t->husband_age_years }}</span> years and <span class="mc-doc-fill mc-doc-fill--sm">{{ $t->husband_age_months }}</span> months, and resident of <span class="mc-doc-fill">{{ $t->husband_address }}</span>, may legally contract marriage with <span class="mc-doc-fill">{{ $t->wife_name }}</span>, aged <span class="mc-doc-fill mc-doc-fill--sm">{{ $t->wife_age_years }}</span> years and <span class="mc-doc-fill mc-doc-fill--sm">{{ $t->wife_age_months }}</span> months, and resident of <span class="mc-doc-fill">{{ $t->wife_address }}</span>, he having paid the license fee of P200 prescribed under Article 65 of Republic Act No. 386
                                </p>
                                <p class="mc-doc-body">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This license shall be valid in any part of the Philippines, but it shall be good for no more than one hundred and twenty days from the date on which issued and shall be deemed cancelled at the expiration of said period if the interested parties have not made use of it.
                                </p>
                                <p class="mc-doc-body mc-doc-body--nojustify">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;As integral parts of this license, there are herewith attached copies of the applications of the contracting parties, and <span style="white-space:nowrap;">copies of the <span class="mc-doc-fill mc-doc-fill--inline-block" style="width:160px;"></span></span>
                                </p>
                                <p class="mc-doc-body">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IN WITNESS WHEREOFF, I have signed and issued this license this <span class="mc-doc-fill mc-doc-fill--sm">{{ $t->witness_day }}</span> day of <span class="mc-doc-fill">{{ $t->witness_month }}</span>, 20<span class="mc-doc-fill mc-doc-fill--sm">{{ $t->witness_year }}</span>
                                </p>
                                <div class="mc-doc-bottom">
                                    <div class="mc-doc-sig-row">
                                        <div class="mc-doc-sig-left">
                                            <p class="mc-doc-sig-reg"><em>Register No. <span>{{ $t->registry_number }}</span></em></p>
                                        </div>
                                        <div class="mc-doc-sig-right">
                                            <p>........................................................................<em>Local Civil Registrar of <span>{{ $t->local_civil_registrar_of }}</span></em></p>
                                        </div>
                                    </div>
                                    <div class="mc-doc-double-rule"></div>
                                    <p class="mc-doc-instruction-title">INSTRUCTION</p>
                                    <span class="mc-doc-instruction-dash">&#8594;</span>
                                    <p class="mc-doc-body mc-doc-body--sm">
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;If males above 20 but under 25 years of age, or females above 18 but under 23 years of age, did not obtain advice of their parents or guardian, or if it be unfavorable, a note above the signature of the Local Civil Registrar should be indicated in the following manner: "Note: The advice upon the intended marriage of <span class="mc-doc-fill">{{ $t->husband_name }}</span> with <span class="mc-doc-fill">{{ $t->wife_name }}</span> not having been obtained or having been refused, the marriage shall not take place till after three months following completion of the publication, on <span class="mc-doc-fill mc-doc-fill--sm">{{ $t->instructions_day }}</span> <span class="mc-doc-fill">{{ $t->instructions_month }}</span>, 20<span class="mc-doc-fill mc-doc-fill--sm">{{ $t->instructions_year }}</span> of the application for the marriage license."
                                    </p>
                                </div>
                            </div>
                        </div>
                        @break

                    {{-- ── Form 58 — Burial Permit ──────────────────────── --}}
                    @case('Form 58')
                        <div class="ctc-view-doc-wrap">
                            <div class="burial-doc burial-doc--view">
                                <div class="burial-doc-meta">
                                    <span>Accountable Form No. 51</span><span>Revised 1993</span><span>ORIGINAL</span>
                                </div>

                                <div class="burial-doc-masthead">
                                    <div class="burial-doc-mast-top">
                                        <div class="burial-doc-mast-title">OFFICIAL RECEIPT</div>
                                        <div class="burial-doc-mast-sub">OF THE REPUBLIC OF THE PHILIPPINES</div>
                                    </div>
                                    <div class="burial-doc-mast-bottom">
                                        <div class="burial-doc-seal"><span class="burial-doc-seal-circle">SEAL</span></div>
                                        <div class="burial-doc-no">N<sup>o</sup> <span class="bval">{{ $t->certificate_number }}</span> <span class="bval">{{ $t->series_letter }}</span></div>
                                    </div>
                                </div>

                                <div class="burial-doc-title">CITY/MUNICIPAL BURIAL PERMIT AND<br>FEE RECEIPT</div>

                                <div class="burial-doc-mr">Mr. <span class="bfill">{{ $t->applicant_name }}</span></div>

                                <div class="burial-doc-dr">
                                    <div class="burial-doc-dr-label">Dr.</div>
                                    <div class="burial-doc-dr-fields">
                                        <div class="burial-doc-dr-row">To the City/Municipality of: <span class="bval">{{ $t->city_municipality }}</span></div>
                                        <div class="burial-doc-dr-row">Province of: <span class="bval">{{ $t->province }}</span></div>
                                    </div>
                                </div>

                                <div class="burial-doc-perm">
                                    <span>Permission is hereby granted</span>
                                    <div class="burial-doc-perm-box">
                                        <div @class(['burial-doc-perm-opt', 'is-selected' => $t->permission_type === 'Inter'])>Inter</div>
                                        <div @class(['burial-doc-perm-opt', 'is-selected' => $t->permission_type === 'Disinter'])>Disinter</div>
                                        <div @class(['burial-doc-perm-opt', 'is-selected' => $t->permission_type === 'Remove'])>Remove</div>
                                    </div>
                                    <span>the remains of&mdash;</span>
                                </div>

                                <table class="burial-doc-items">
                                    <tr><td>1.</td><td>Name <span class="bfill">{{ $t->deceased_name }}</span></td></tr>
                                    <tr><td>2.</td><td>Nationality <span class="bfill">{{ $t->nationality }}</span></td></tr>
                                    <tr><td>3.</td><td>Age <span class="bfill">{{ $t->age }}</span> years. Sex <span class="bfill">{{ $t->sex }}</span></td></tr>
                                    <tr><td>4.</td><td>Date of death <span class="bfill">{{ $fmtDate($t->date_of_death) }}</span></td></tr>
                                    <tr><td>5.</td><td>Cause of death <span class="bfill">{{ $t->cause_of_death }}</span></td></tr>
                                    <tr><td>6.</td><td>Name of cemetery <span class="bfill">{{ $t->cemetery_name }}</span></td></tr>
                                    <tr class="burial-doc-disinter"><td></td><td>* In case of disinterment&mdash;</td></tr>
                                    <tr><td>*7.</td><td>Infectious or non-infectious <span class="bfill">{{ $t->infectious }}</span></td></tr>
                                    <tr><td>*8.</td><td>Body embalmed or not embalmed <span class="bfill">{{ $t->embalmed }}</span></td></tr>
                                    <tr><td>*9.</td><td>Disposition of remains <span class="bfill">{{ $t->disposition }}</span></td></tr>
                                    <tr><td>10.</td><td>Amount of the fee per city/municipal ordinance</td></tr>
                                </table>

                                <div class="burial-doc-footgrid">
                                    <div class="burial-doc-footgrid-row">
                                        <div class="burial-doc-footgrid-cell burial-doc-footgrid-cell--l">No. <span class="bval">{{ $t->certificate_number }}</span></div>
                                        <div class="burial-doc-footgrid-cell burial-doc-footgrid-cell--r">Dated: <span class="bval">{{ $fmtDate($t->date_issued) }}</span></div>
                                    </div>
                                    <div class="burial-doc-footgrid-row">
                                        <div class="burial-doc-footgrid-cell burial-doc-footgrid-cell--l">&#8369; <span class="bval">{{ $fmt($t->fee_amount) }}</span></div>
                                        <div class="burial-doc-footgrid-cell burial-doc-footgrid-cell--r"></div>
                                    </div>
                                    <div class="burial-doc-footgrid-row">
                                        <div class="burial-doc-footgrid-cell burial-doc-footgrid-cell--l">City/Municipality</div>
                                        <div class="burial-doc-footgrid-cell burial-doc-footgrid-cell--r">Province</div>
                                    </div>
                                    <div class="burial-doc-footgrid-row">
                                        <div class="burial-doc-footgrid-cell burial-doc-footgrid-cell--full">Date <span class="bval">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> , 20<span class="bval">{{ $t->date_issued ? \Carbon\Carbon::parse($t->date_issued)->format('y') : '' }}</span></div>
                                    </div>
                                </div>

                                <p class="burial-doc-cert">
                                    <span class="burial-doc-cert-sc">I hereby certify</span> that I have this day issued this burial permit and have received the fee above stated in the amount of &#8369; <span class="bfill">{{ $fmt($t->fee_amount) }}</span>
                                </p>

                                <div class="burial-doc-sig">
                                    <div class="burial-doc-sig-inner">
                                        <div class="burial-doc-sig-city">City <span class="bfill">{{ $t->municipal_secretary }}</span></div>
                                        <div class="burial-doc-sig-title">Municipal Secretary</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @break

                    {{-- ── Default — generic info ───────────────────────── --}}
                    @default
                        <div class="ctc-view-generic">
                            <div class="ctc-view-generic-row">
                                <span class="ctc-view-generic-label">Serial Number</span>
                                <span class="ctc-view-generic-value">{{ $log->serial_number }}</span>
                            </div>
                            <div class="ctc-view-generic-row">
                                <span class="ctc-view-generic-label">Payee</span>
                                <span class="ctc-view-generic-value">{{ $log->payee }}</span>
                            </div>
                            <div class="ctc-view-generic-row">
                                <span class="ctc-view-generic-label">Form Type</span>
                                <span class="ctc-view-generic-value">{{ $formName }} ({{ $log->form_type }})</span>
                            </div>
                            <div class="ctc-view-generic-row">
                                <span class="ctc-view-generic-label">Date</span>
                                <span class="ctc-view-generic-value">{{ $log->transacted_at->format('F j, Y') }}</span>
                            </div>
                        </div>
                        @break
                @endswitch
                </div>{{-- /.cm-print --}}
            @endif

            {{-- ── Action buttons ─────────────────────────────────────────── --}}
            <div class="ctc-view-actions" style="max-width: 820px; margin-left: auto; margin-right: auto;">
                <button type="button" class="ctc-view-print-btn" onclick="window.print()">Print</button>
                @if($log->archived_at)
                    <button type="button" class="ctc-view-cancel-request-btn" id="unarchiveBtn"
                        style="border-color:#198754; background:rgba(25,135,84,0.08); color:#198754;">
                        Unarchive
                    </button>
                @endif
                @if($log->status !== 'Cancelled')
                    @if($isAdmin ?? false)
                        @if($hasPendingRequest)
                            <button type="button" class="ctc-view-reject-request-btn" id="rejectRequestBtn">
                                Reject Request
                            </button>
                        @endif
                        <button type="button" class="ctc-view-cancel-request-btn" id="cancelRequestOpenBtn">
                            {{ $hasPendingRequest ? 'Approve Request' : 'Cancel Transaction' }}
                        </button>
                    @elseif(!$hasPendingRequest)
                        <button type="button" class="ctc-view-cancel-request-btn" id="cancelRequestOpenBtn">
                            Request to Cancel
                        </button>
                    @else
                        <button type="button" class="ctc-view-cancel-request-btn" disabled style="opacity:.5;cursor:not-allowed;">
                            Cancel Request Pending
                        </button>
                    @endif
                @endif
            </div>

        </div>{{-- end .ctc-view-page --}}
    </div>

    {{-- ── Cancel Modal ────────────────────────────────────────────────────── --}}
    <div class="ctc-modal-overlay" id="cancelRequestOverlay">
        <div class="ctc-modal">
            <div class="ctc-modal-header">
                <p class="ctc-modal-title">
                    @if($isAdmin ?? false)
                        Cancel Transaction
                    @else
                        Request to Cancel Transaction
                    @endif
                </p>
                <button type="button" class="ctc-modal-close-btn" id="cancelRequestCloseBtn" aria-label="Close">
                    <x-bx-x class="icon" />
                </button>
            </div>
            <div class="ctc-modal-body">
                <div class="ctc-modal-info-row">
                    <span class="ctc-modal-info-label">Serial No.</span>
                    <span class="ctc-modal-info-value">{{ $log->serial_number }}</span>
                </div>
                <div class="ctc-modal-info-row">
                    <span class="ctc-modal-info-label">Payee</span>
                    <span class="ctc-modal-info-value">{{ $log->payee }}</span>
                </div>
                @if($isAdmin ?? false)
                    <p class="ctc-modal-confirm-text">Are you sure you want to cancel this transaction? This action cannot be undone.</p>
                @else
                    <div class="ctc-modal-field">
                        <label class="ctc-modal-field-label" for="cancelReason">Reason <span style="color:#999;">(optional)</span></label>
                        <textarea id="cancelReason" class="ctc-modal-textarea" rows="3" placeholder="Enter reason for cancellation..."></textarea>
                    </div>
                @endif
            </div>
            <div class="ctc-modal-footer">
                <button type="button" class="ctc-modal-btn ctc-modal-btn--secondary" id="cancelRequestCancelBtn">
                    @if($isAdmin ?? false) Close @else Cancel @endif
                </button>
                <button type="button" class="ctc-modal-btn ctc-modal-btn--primary" id="cancelRequestSubmitBtn">
                    @if($isAdmin ?? false) Confirm Cancel @else Submit Request @endif
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <style>
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

                /* Form 58 — Burial permit: faithful replica, centered on the sheet, B/W. */
                .quick-entry-bar { display: none !important; }
                .burial-doc--view {
                    width: 4.5in !important;
                    margin: 0.7in auto !important;
                    padding: 0 !important;
                    border: none !important;
                    border-radius: 0 !important;
                    box-shadow: none !important;
                    background: #fff !important;
                }
                .burial-doc--view,
                .burial-doc--view * { color: #000 !important; }
                .burial-doc--view .burial-doc-perm-opt.is-selected {
                    background: #000 !important;
                    color: #fff !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
            }
        </style>
    @endpush

    @if($log->archived_at)
    @push('scripts')
        <script>
        (function () {
            const btn  = document.getElementById('unarchiveBtn');
            if (!btn) return;
            const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            btn.addEventListener('click', function () {
                if (!confirm('Unarchive this transaction? It will reappear in Collection Management.')) return;
                btn.disabled = true;
                btn.textContent = 'Unarchiving…';

                fetch('/archive-records/transactions/{{ $log->id }}/unarchive', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() },
                })
                .then(r => r.json())
                .then(data => {
                    showToast(data.message);
                    window.location.href = '{{ route('archives', ['tab' => 'collection-management']) }}';
                })
                .catch(() => { showToast('Action could not be completed', 'Something went wrong.', 'error'); btn.disabled = false; btn.textContent = 'Unarchive'; });
            });
        })();
        </script>
    @endpush
    @endif

    @push('scripts')
        <script>
            (function () {
                const overlay   = document.getElementById('cancelRequestOverlay');
                const openBtn   = document.getElementById('cancelRequestOpenBtn');
                const closeBtn  = document.getElementById('cancelRequestCloseBtn');
                const cancelBtn = document.getElementById('cancelRequestCancelBtn');
                const submitBtn = document.getElementById('cancelRequestSubmitBtn');

                const open  = () => overlay.classList.add('open');
                const close = () => overlay.classList.remove('open');

                if (openBtn) openBtn.addEventListener('click', open);
                closeBtn.addEventListener('click', close);
                cancelBtn.addEventListener('click', close);
                overlay.addEventListener('click', (e) => { if (e.target === overlay) close(); });

                submitBtn.addEventListener('click', function () {
                    @if($isAdmin ?? false)
                    const isAdmin = true;
                    @else
                    const isAdmin = false;
                    @endif

                    submitBtn.disabled = true;
                    submitBtn.textContent = isAdmin ? 'Cancelling…' : 'Submitting…';

                    const url     = isAdmin
                        ? '{{ route('collections.cancel', $log->id, false) }}'
                        : '{{ route('collections.cancel-request', $log->id, false) }}';
                    const headers = { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '' };
                    const fetchOptions = isAdmin
                        ? { method: 'POST', headers }
                        : { method: 'POST', headers: { ...headers, 'Content-Type': 'application/json' }, body: JSON.stringify({ reason: document.getElementById('cancelReason')?.value.trim() ?? '' }) };

                    fetch(url, fetchOptions)
                    .then((r) => r.json())
                    .then((data) => {
                        close();
                        showToast(data.message);
                        if (isAdmin) {
                            if (openBtn) {
                                openBtn.disabled = true;
                                openBtn.textContent = 'Cancelled';
                                openBtn.style.opacity = '0.5';
                                openBtn.style.cursor = 'not-allowed';
                            }
                            const badge = document.querySelector('.status-badge');
                            if (badge) {
                                badge.className = 'status-badge status-cancelled';
                                badge.textContent = 'Cancelled';
                            }
                            const rejectBtn = document.getElementById('rejectRequestBtn');
                            if (rejectBtn) rejectBtn.remove();
                            const pendingBadge = document.querySelector('.ctc-view-pending-badge');
                            if (pendingBadge) pendingBadge.remove();
                        } else {
                            if (openBtn) {
                                openBtn.disabled = true;
                                openBtn.textContent = 'Cancel Request Pending';
                                openBtn.style.opacity = '0.5';
                                openBtn.style.cursor = 'not-allowed';
                            }
                        }
                    })
                    .catch(() => showToast('Action could not be completed', 'Something went wrong. Please try again.', 'error'))
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.textContent = isAdmin ? 'Confirm Cancel' : 'Submit Request';
                    });
                });

                // ── Reject Request (admin only) ──────────────────────────
                const rejectBtn = document.getElementById('rejectRequestBtn');
                if (rejectBtn) {
                    rejectBtn.addEventListener('click', function () {
                        if (!confirm('Reject this cancel request? The transaction will remain as Completed.')) return;

                        rejectBtn.disabled = true;
                        rejectBtn.textContent = 'Rejecting…';

                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
                        fetch('{{ route('collections.reject-cancel-request', $log->id, false) }}', {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
                        })
                        .then(r => r.json())
                        .then(data => {
                            showToast(data.message);
                            // Remove pending indicators and update buttons
                            rejectBtn.remove();
                            const pendingBadge = document.querySelector('.ctc-view-pending-badge');
                            if (pendingBadge) pendingBadge.remove();
                            if (openBtn) openBtn.textContent = 'Cancel Transaction';
                        })
                        .catch(() => showToast('Action could not be completed', 'Something went wrong. Please try again.', 'error'))
                        .finally(() => {
                            if (rejectBtn.isConnected) {
                                rejectBtn.disabled = false;
                                rejectBtn.textContent = 'Reject Request';
                            }
                        });
                    });
                }
            })();
        </script>
    @endpush
</x-layout>
