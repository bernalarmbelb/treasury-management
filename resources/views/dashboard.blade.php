<x-layout>
    <div class="x-header-container">
        <x-header title="Dashboard" :tmpRoute="route('home')" routeName="home">
            <x-slot:actions>
                <div class="dash-filter" role="group" aria-label="Dashboard period filter">
                    <a href="{{ route('home', ['range' => 'today']) }}" class="dash-filter-btn {{ $range === 'today' ? 'active' : '' }}">Today</a>
                    <a href="{{ route('home', ['range' => 'week']) }}" class="dash-filter-btn {{ $range === 'week' ? 'active' : '' }}">This Week</a>
                    <a href="{{ route('home', ['range' => 'month']) }}" class="dash-filter-btn {{ $range === 'month' ? 'active' : '' }}">This Month</a>
                </div>
            </x-slot:actions>
        </x-header>
    </div>

    @php
        $lowRows = collect($forms['rows'])->filter(fn ($r) => $r['remaining'] < 50)->values();
        $utilizationPct = ($forms['totalRegistered'] ?? 0) > 0
            ? round(($forms['usedThisPeriod'] / $forms['totalRegistered']) * 100)
            : 0;

        $paymentColors = [
            'cash'   => 'var(--primary)',
            'cheque' => 'var(--accent)',
            'online' => 'var(--success)',
        ];
        $paymentTotalCount = collect($payments['methods'])->sum('count');

        $maxChannelCount = collect($payments['channels'])->max('count') ?: 0;
        $onlineTxns = collect($payments['channels'])->sum('count');

        $moduleIcons = [
            'Collections'  => '&#65291;',
            'Cheque Mgmt'  => '&#127974;',
            'Bank Recon'   => '&#10003;',
        ];
    @endphp

    <div class="dash-wrap">

        <div class="dash-kpis">
            <div class="dash-kpi">
                <div class="dash-chip dash-chip-blue">&#127974;</div>
                <div>
                    <div class="dash-num">&#8369;{{ number_format($cash['total'], 2) }}</div>
                    <div class="dash-lbl">Cash Position</div>
                    <div class="dash-sub" style="color:var(--fonts-black-50)">{{ $cash['accounts'] }} {{ Str::plural('account', $cash['accounts']) }}</div>
                </div>
            </div>

            <div class="dash-kpi">
                <div class="dash-chip dash-chip-green">&#128181;</div>
                <div>
                    <div class="dash-num">&#8369;{{ number_format($collections['total'], 2) }}</div>
                    <div class="dash-lbl">Collections</div>
                    @if (is_null($collections['deltaPct']))
                        <div class="dash-sub" style="color:var(--fonts-black-50)">{{ $collections['count'] }} Transactions</div>
                    @else
                        <div class="dash-sub">
                            <span class="dash-dot" style="background:{{ $collections['deltaPct'] >= 0 ? 'var(--success)' : 'var(--danger)' }}"></span>
                            <span style="color:{{ $collections['deltaPct'] >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                                {{ $collections['deltaPct'] >= 0 ? '▲' : '▼' }} {{ number_format(abs($collections['deltaPct']), 1) }}%
                            </span>
                            &middot; {{ $collections['count'] }} Transactions
                        </div>
                    @endif
                </div>
            </div>

            <div class="dash-kpi">
                <div class="dash-chip dash-chip-gold">&#129534;</div>
                <div>
                    <div class="dash-num">&#8369;{{ number_format($disbursed['total'], 2) }}</div>
                    <div class="dash-lbl">Disbursed</div>
                    <div class="dash-sub" style="color:var(--fonts-black-50)">{{ $disbursed['count'] }} {{ Str::plural('cheque', $disbursed['count']) }} issued</div>
                </div>
            </div>

            <div class="dash-kpi">
                <div class="dash-chip dash-chip-red">&#9888;</div>
                <div>
                    <div class="dash-num">{{ $exceptions['count'] }}</div>
                    <div class="dash-lbl">Exceptions</div>
                    @if ($exceptions['count'] > 0)
                        <div class="dash-sub" style="color:var(--danger);font-weight:600">needs review</div>
                    @else
                        <div class="dash-sub" style="color:var(--fonts-black-50)">all clear</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="dash-grid">
            <div class="dash-stack">

                <div class="dash-card">
                    <h3><span>Collections Trend</span><span class="dash-m">Daily &middot; {{ ucfirst($range) }}</span></h3>
                    <div id="dash-trend" class="dash-chart-area"></div>
                    <div class="dash-mini-stats">
                        <span>Total <b>&#8369;{{ number_format($trend['total'], 2) }}</b></span>
                        <span>Daily Avg <b>&#8369;{{ number_format($trend['avg'], 2) }}</b></span>
                        @if (!empty($trend['peak']['date']))
                            <span>Peak <b>&#8369;{{ number_format($trend['peak']['amount'], 2) }}</b> ({{ \Carbon\Carbon::parse($trend['peak']['date'])->format('M d') }})</span>
                        @endif
                        @if (!is_null($collections['deltaPct']))
                            <span style="color:{{ $collections['deltaPct'] >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                                {{ $collections['deltaPct'] >= 0 ? '▲' : '▼' }} {{ number_format(abs($collections['deltaPct']), 1) }}% vs previous period
                            </span>
                        @endif
                    </div>
                </div>

                <div class="dash-charts-2">
                    <div class="dash-card">
                        <h3><span>Payment Methods</span></h3>
                        <div class="dash-donut-wrap">
                            <div id="dash-payments" class="dash-donut-chart"></div>
                            <div class="dash-legend-list">
                                @forelse ($payments['methods'] as $method)
                                    @php
                                        $key = strtolower($method['method'] ?? '');
                                        $pct = $paymentTotalCount > 0 ? round(($method['count'] / $paymentTotalCount) * 100) : 0;
                                        $color = $paymentColors[$key] ?? 'var(--fonts-black-40)';
                                    @endphp
                                    <div class="row">
                                        <span><span class="dash-dot" style="background:{{ $color }}"></span>{{ ucfirst($method['method']) }}</span>
                                        <span><b>{{ $pct }}%</b> <span class="dash-amt">&#8369;{{ number_format($method['amount'], 2) }}</span></span>
                                    </div>
                                @empty
                                    <div class="row"><span style="color:var(--fonts-black-50)">No payment data for this period.</span></div>
                                @endforelse
                            </div>
                        </div>

                        <div class="dash-subsection">
                            <div class="cap">Online Channels &middot; {{ $onlineTxns }} Transactions</div>
                            @forelse ($payments['channels'] as $channel)
                                @php $chanPct = $maxChannelCount > 0 ? round(($channel['count'] / $maxChannelCount) * 100) : 0; @endphp
                                <div class="dash-chan">
                                    <span class="nm">{{ $channel['channel'] }}</span>
                                    <div class="track"><span style="width:{{ $chanPct }}%"></span></div>
                                    <span class="ct"><b>{{ $channel['count'] }}</b></span>
                                </div>
                            @empty
                                <div class="dash-chan"><span style="color:var(--fonts-black-50)">No online Transactions yet.</span></div>
                            @endforelse
                        </div>
                    </div>

                    <div class="dash-card" style="display:flex; flex-direction:column;">
                        <h3><span>Reconciliation Status</span></h3>
                        <div class="dash-stackbar">
                            <div class="row">
                                <div class="top"><span class="lbl">Deposits</span><span><b>{{ $reconciliation['depositsMatchedPct'] }}%</b> matched</span></div>
                                <div class="track">
                                    <span style="width:{{ $reconciliation['depositsMatchedPct'] }}%; background:var(--success);"></span>
                                    <span style="width:{{ 100 - $reconciliation['depositsMatchedPct'] }}%; background:var(--danger);"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="top"><span class="lbl">Cheques</span><span><b>{{ $reconciliation['chequesMatchedPct'] }}%</b> matched</span></div>
                                <div class="track">
                                    <span style="width:{{ $reconciliation['chequesMatchedPct'] }}%; background:var(--success);"></span>
                                    <span style="width:{{ 100 - $reconciliation['chequesMatchedPct'] }}%; background:var(--danger);"></span>
                                </div>
                            </div>
                        </div>

                        <div class="dash-exc">
                            <div class="cap">
                                <span>Needs Review &middot; {{ $exceptions['count'] }}</span>
                                <a href="{{ route('bank-deposit-reconciliation') }}">View all &rarr;</a>
                            </div>
                            @forelse ($exceptions['items'] as $item)
                                @php
                                    $isBounced = $item['type'] === 'bounced-cheque';
                                    $tagClass = $isBounced ? 'bounce' : 'unmatched';
                                    $tagLabel = $isBounced ? 'BOUNCED' : 'UNMATCHED';
                                @endphp
                                <div class="item">
                                    <span class="tag {{ $tagClass }}">{{ $tagLabel }}</span>
                                    <span class="desc">{{ $item['label'] }}</span>
                                    <span class="amt">&#8369;{{ number_format($item['amount'], 2) }}</span>
                                </div>
                            @empty
                                <div class="item"><span class="desc" style="color:var(--fonts-black-50)">Nothing needs review right now.</span></div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

            <div class="dash-stack">
                <div class="dash-card">
                    <h3>Quick Actions</h3>
                    <a href="{{ route('reporting-abstract') }}" class="dash-qbtn primary"><span class="ic">&#128202;</span>Reporting &amp; Abstract</a>
                    <a href="{{ route('transaction-entry') }}" class="dash-qbtn"><span class="ic">&#129534;</span>New Collection Entry</a>
                    <a href="{{ route('cheque-management.create') }}" class="dash-qbtn"><span class="ic">&#127974;</span>New Cheque</a>
                </div>

                <div class="dash-card" style="flex:1; display:flex; flex-direction:column;">
                    <h3><span>Recent Activity</span><span class="dash-m">recent</span></h3>
                    <div class="dash-feed">
                        @forelse ($activity as $item)
                            @php
                                $dt = \Carbon\Carbon::parse($item['at']);
                                $when = $dt->isToday() ? $dt->format('g:i A') : $dt->format('M d, g:i A');
                                $icon = $moduleIcons[$item['module']] ?? '&#8226;';
                            @endphp
                            <div class="item">
                                <div class="ic">{!! $icon !!}</div>
                                <div>
                                    <div class="act">{{ $item['label'] }}</div>
                                    <div class="meta">{{ $item['module'] }} &middot; {{ is_null($item['amount']) ? '—' : '₱' . number_format($item['amount'], 2) }} &middot; {{ $when }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="item"><div><div class="meta" style="color:var(--fonts-black-50)">No recent activity.</div></div></div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        <div class="dash-card dash-forms">
            <h3>Accountable Forms Utilization</h3>
            <div class="dash-insights">
                <div class="dash-ins">
                    <div class="n">{{ number_format($forms['totalRegistered']) }}</div>
                    <div class="l">Total Registered</div>
                    <div class="s" style="color:var(--fonts-black-50)">across {{ count($forms['rows']) }} form types</div>
                </div>
                <div class="dash-ins">
                    <div class="n">{{ number_format($forms['usedThisPeriod']) }}</div>
                    <div class="l">Used (to date)</div>
                    <div class="s" style="color:var(--success)">{{ $utilizationPct }}% utilization</div>
                </div>
                <div class="dash-ins">
                    <div class="n">{{ $forms['lowStock'] }}</div>
                    <div class="l">Low Stock Alerts</div>
                    @if ($forms['lowStock'] > 0)
                        <div class="s dash-low">{{ $lowRows->first()['name'] }} &middot; {{ $lowRows->first()['remaining'] }} left</div>
                    @else
                        <div class="s" style="color:var(--fonts-black-50)">All forms sufficiently stocked</div>
                    @endif
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Form Type</th>
                        <th>Registered</th>
                        <th>Used</th>
                        <th>Void</th>
                        <th>Remaining</th>
                        <th>Stock level</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($forms['rows'] as $row)
                        @php
                            $usedPct = $row['registered'] > 0 ? round(($row['used'] / $row['registered']) * 100, 1) : 0;
                            $voidPct = $row['registered'] > 0 ? round(($row['void'] / $row['registered']) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ number_format($row['registered']) }}</td>
                            <td>{{ number_format($row['used']) }}</td>
                            <td>{{ number_format($row['void']) }}</td>
                            <td class="{{ $row['remaining'] < 50 ? 'dash-low' : '' }}">{{ number_format($row['remaining']) }}</td>
                            <td>
                                <div class="dash-bar">
                                    <span style="width:{{ $usedPct }}%; background:var(--primary);"></span>
                                    <span style="width:{{ $voidPct }}%; background:var(--danger);"></span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="color:var(--fonts-black-50)">No accountable forms registered yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    @push('scripts')
    <script>
        window.__dash = {
            trend: @json($trend['points']),
            payments: @json($payments['methods']),
        };

        document.addEventListener('DOMContentLoaded', function () {
            if (!window.ApexCharts) return;

            var trendPoints = window.__dash.trend || [];
            var trendEl = document.querySelector('#dash-trend');
            if (trendEl) {
                new ApexCharts(trendEl, {
                    chart: { type: 'area', height: 230, toolbar: { show: false }, zoom: { enabled: false } },
                    series: [{
                        name: 'Collections',
                        data: trendPoints.map(function (p) { return { x: p.date, y: p.amount, txns: p.txns }; }),
                    }],
                    colors: ['#427AB5'],
                    stroke: { curve: 'smooth', width: 2.5 },
                    fill: {
                        type: 'gradient',
                        gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 90, 100] },
                    },
                    dataLabels: { enabled: false },
                    grid: { show: false, padding: { left: 8, right: 8 } },
                    xaxis: {
                        type: 'datetime',
                        labels: { style: { fontSize: '10px' } },
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                    },
                    yaxis: { show: false },
                    tooltip: {
                        x: { format: 'MMM dd' },
                        y: {
                            formatter: function (val) { return '₱' + Number(val).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },
                        },
                        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                            var point = trendPoints[dataPointIndex];
                            if (!point) return '';
                            var amt = '₱' + Number(point.amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            var date = new Date(point.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                            return '<div style="padding:6px 10px; font-size:11px; font-family:Manrope,sans-serif;">' +
                                '<div style="font-weight:700;">' + date + ' &middot; ' + amt + '</div>' +
                                '<div style="color:#888;">' + point.txns + ' Transaction' + (point.txns === 1 ? '' : 's') + '</div>' +
                                '</div>';
                        },
                    },
                }).render();
            }

            var payMethods = window.__dash.payments || [];
            var payEl = document.querySelector('#dash-payments');
            if (payEl) {
                var colorMap = { cash: '#427AB5', cheque: '#F7DD7D', online: '#0FA958' };
                new ApexCharts(payEl, {
                    chart: { type: 'donut', height: 150 },
                    series: payMethods.map(function (m) { return m.count; }),
                    labels: payMethods.map(function (m) { return m.method ? (m.method.charAt(0).toUpperCase() + m.method.slice(1)) : 'Unknown'; }),
                    colors: payMethods.map(function (m) { return colorMap[(m.method || '').toLowerCase()] || '#B7BBC1'; }),
                    legend: { show: false },
                    dataLabels: { enabled: false },
                    stroke: { width: 0 },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '74%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Transactions',
                                        fontSize: '8px',
                                        formatter: function (w) {
                                            return w.globals.seriesTotals.reduce(function (a, b) { return a + b; }, 0);
                                        },
                                    },
                                    value: { fontSize: '13px', fontWeight: 800 },
                                },
                            },
                        },
                    },
                    tooltip: {
                        y: { formatter: function (val) { return val + ' Transactions'; } },
                    },
                }).render();
            }
        });
    </script>
    @endpush
</x-layout>
