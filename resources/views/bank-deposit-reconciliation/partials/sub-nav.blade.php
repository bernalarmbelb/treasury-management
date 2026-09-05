<div class="view-actions">
    <div class="view-tabs">
        <a href="{{ route('bank-deposit-reconciliation', [], false) }}" class="view-tab {{ request()->routeIs('bank-deposit-reconciliation') ? 'active' : '' }}">
            <svg class="view-tab-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
            Reconciliation Logs
        </a>
        <a href="{{ route('bank-deposit-reconciliation.incoming', [], false) }}" class="view-tab {{ request()->routeIs('bank-deposit-reconciliation.incoming') ? 'active' : '' }}">
            <svg class="view-tab-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
            Incoming
        </a>
        <a href="{{ route('bank-deposit-reconciliation.outgoing', [], false) }}" class="view-tab {{ request()->routeIs('bank-deposit-reconciliation.outgoing') ? 'active' : '' }}">
            <svg class="view-tab-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
            Outgoing
        </a>
    </div>
</div>
