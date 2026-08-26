<x-layout>
    @php
        $tmpRoute = route('bank-deposit-reconciliation.incoming');
        $routeName = 'bank-deposit-reconciliation.incoming';
        $dateStart = request('date_start');
        $dateEnd = request('date_end');
        $dateFilterActive = $dateStart || $dateEnd;
    @endphp

    <div class="x-header-container sub-nav-sticky">
        <x-header title="Bank Deposit & Reconciliation" :tmpRoute="route('bank-deposit-reconciliation')" routeName="bank-deposit-reconciliation">
            <x-slot:actions>
                @include('bank-deposit-reconciliation.partials.sub-nav')
            </x-slot:actions>
        </x-header>
    </div>

    @include('bank-deposit-reconciliation.partials.styles')

    <div class="collection-content">
        <div class="collection-toolbar">
            <div class="filter-wrapper">
                <button type="button" class="filter-btn" id="filterToggleBtn" aria-label="Filter"><x-icons.filter-fill class="icon" /></button>
            </div>
            <form class="search-group" role="search" method="GET" id="bdr-search-form" action="{{ $tmpRoute }}">
                <input type="search" name="search" class="search-input" id="bdr-search-input" placeholder="Search payee or serial" value="{{ request('search') }}" autocomplete="off">
            </form>
            <div class="date-filter-group {{ $dateFilterActive ? 'open' : '' }}" id="dateFilterGroup">
                <input type="date" class="date-filter-input" id="dateFilterStart" value="{{ $dateStart }}">
                <span class="date-filter-separator">-</span>
                <input type="date" class="date-filter-input" id="dateFilterEnd" value="{{ $dateEnd }}">
                <button type="button" class="btn btn-light date-filter-btn" id="dateFilterBtn">Filter</button>
            </div>
        </div>

        <div id="bdr-table-container">
            @include('bank-deposit-reconciliation.partials.incoming-table')
        </div>

        @if ($isAdmin ?? false)
            @include('bank-deposit-reconciliation.partials.deposit-flow')
        @endif

        @include('partials.select-enhancer')
    </div>

    @push('scripts')
        @include('bank-deposit-reconciliation.partials.log-script')
    @endpush
</x-layout>
