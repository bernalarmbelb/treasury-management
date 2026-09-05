<div class="view-actions">
    <div class="view-tabs">
        <a href="{{ route('collections') }}" class="view-tab {{ request()->routeIs('collections') ? 'active' : '' }}">
            <svg class="view-tab-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
            Transaction Logs
        </a>
        <a href="{{ route('transaction-entry') }}" class="view-tab {{ request()->routeIs('transaction-entry') ? 'active' : '' }}">
            <svg class="view-tab-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Transaction Entry
        </a>
    </div>
</div>
