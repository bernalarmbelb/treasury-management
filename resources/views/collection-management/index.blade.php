<x-layout>
    @php
        $tmpRoute = route('collections');
        $routeName = 'collections';

        $transactions = [
            ['serial' => '324-5678-1234-02', 'payee' => 'Smith, John A', 'date' => 'January 12, 2023', 'time' => '02:15:00 AM', 'form' => 'Form 01', 'status' => 'Completed'],
            ['serial' => '324-5678-1234-02', 'payee' => 'Johnson, Emily B', 'date' => 'December 19, 2021', 'time' => '05:45:00 PM', 'form' => 'Form 02', 'status' => 'Cancelled'],
            ['serial' => '324-5678-1234-02', 'payee' => 'Williams, Michael C', 'date' => 'March 1, 2022', 'time' => '05:00:00 PM', 'form' => 'Form 02', 'status' => 'Completed'],
            ['serial' => '324-5678-1234-02', 'payee' => 'Brown, Sarah D', 'date' => 'July 4, 2022', 'time' => '01:45:00 AM', 'form' => 'Form 02', 'status' => 'Completed'],
            ['serial' => '324-5678-1234-02', 'payee' => 'Jones, David E', 'date' => 'March 8, 2023', 'time' => '03:00:00 PM', 'form' => 'Form 02', 'status' => 'Completed'],
            ['serial' => '324-5678-1234-02', 'payee' => 'Garcia, Laura F', 'date' => 'February 29, 2020', 'time' => '10:30:00 AM', 'form' => 'Form 02', 'status' => 'Completed'],
            ['serial' => '324-5678-1234-02', 'payee' => 'Martinez, Carlos G', 'date' => 'September 14, 2022', 'time' => '08:15:00 PM', 'form' => 'Form 03', 'status' => 'Completed'],
            ['serial' => '324-5678-1234-02', 'payee' => 'Davis, Jennifer H', 'date' => 'October 11, 2022', 'time' => '07:00:00 AM', 'form' => 'Form 01', 'status' => 'Cancelled'],
            ['serial' => '324-5678-1234-02', 'payee' => 'Rodriguez, Daniel I', 'date' => 'April 30, 2020', 'time' => '12:45:00 PM', 'form' => 'Form 01', 'status' => 'Cancelled'],
            ['serial' => '324-5678-1234-02', 'payee' => 'Wilson, Jessica J', 'date' => 'May 15, 2023', 'time' => '09:30:00 PM', 'form' => 'Form 03', 'status' => 'Cancelled'],
            ['serial' => '324-5678-1234-02', 'payee' => 'Anderson, Brian K', 'date' => 'August 23, 2021', 'time' => '04:00:00 AM', 'form' => 'Form 01', 'status' => 'Completed'],
            ['serial' => '324-5678-1234-02', 'payee' => 'Thomas, Angela L', 'date' => 'June 30, 2021', 'time' => '11:15:00 PM', 'form' => 'Form 03', 'status' => 'Cancelled'],
            ['serial' => '324-5678-1234-02', 'payee' => 'Taylor, Kevin M', 'date' => 'November 5, 2022', 'time' => '06:00:00 AM', 'form' => 'Form 01', 'status' => 'Cancelled'],
            ['serial' => '324-5678-1234-02', 'payee' => 'Moore, Stephanie N', 'date' => 'August 18, 2023', 'time' => '01:30:00 AM', 'form' => 'Form 03', 'status' => 'Cancelled'],
        ];
    @endphp

    <div class="x-header-container">
        <x-header title="Collection Management"
            :tmpRoute="$tmpRoute"
            :routeName="$routeName"
        />
        <div class="nav-sticky-wrapper">
            <div class="" style="display:flex; width: 100%">
                <button class="nav-scroll-btn nav-scroll-left" id="scrollLeft">&#8249;</button>
                <nav class="navigation-bar" id="navigationBar">
                    <p><a href="{{ route('collections') }}" class=" {{ request()->routeIs('collections') ? 'active' : '' }} "> Transaction Logs </a></p>

                    <p><a href="{{ route('transaction-entry') }}" class=" {{ request()->routeIs('transaction-entry') ? 'active' : '' }} ">Transaction Entry</a></p>
                </nav>
                <button class="nav-scroll-btn nav-scroll-right" id="scrollRight">&#8250;</button>
            </div>
        </div>
    </div>

    <div class="collection-content">
        <div class="collection-toolbar">
            <button type="button" class="filter-btn" aria-label="Filter">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M4 5h16M7 12h10M10 19h4" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <form class="search-group" role="search" method="GET">
                <input type="search" name="search" class="search-input" placeholder="Search Transaction">
                <button type="submit" class="btn btn-light search-btn">Search</button>
            </form>
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Serial Number</th>
                        <th>Payee</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Form Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction['serial'] }}</td>
                            <td>{{ $transaction['payee'] }}</td>
                            <td>{{ $transaction['date'] }}</td>
                            <td>{{ $transaction['time'] }}</td>
                            <td>{{ $transaction['form'] }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($transaction['status']) }}">
                                    {{ $transaction['status'] }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <button type="button" class="action-btn action-edit" title="Edit" aria-label="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none">
                                            <path d="M16.474 5.408 18.592 7.526M4 20h4l10.6-10.6a2.5 2.5 0 0 0-3.535-3.536L4.464 16.464A2 2 0 0 0 4 17.879V20Z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                    <button type="button" class="action-btn action-view" title="View" aria-label="View">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none">
                                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <circle cx="12" cy="12" r="3" stroke="#fff" stroke-width="2"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-bar">
            <p class="pagination-info">Showing 1 to {{ count($transactions) }} of {{ count($transactions) }} entries</p>
            <div class="pagination-controls">
                <button type="button" class="page-btn" disabled>Previous</button>
                <button type="button" class="page-btn active">1</button>
                <button type="button" class="page-btn">Next</button>
            </div>
        </div>
    </div>
</x-layout>