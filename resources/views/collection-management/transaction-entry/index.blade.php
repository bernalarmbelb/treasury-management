<x-layout>
    @php
        $tmpRoute = route('transaction-entry');
        $routeName = 'transaction-entry';
    @endphp

    <div class="x-header-container">
        <x-header title="Transaction Entry"
            :tmpRoute="$tmpRoute"
            :routeName="$routeName"
            parentTitle="Collections Management"
            :parentRoute="route('collections')"
            parentRouteName="collections"
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

</x-layout>