<nav class="um-subnav">
    <a href="{{ route('bank-deposit-reconciliation', [], false) }}" class="um-subnav-item {{ request()->routeIs('bank-deposit-reconciliation') ? 'active' : '' }}">Reconciliation Logs</a>
    <a href="{{ route('bank-deposit-reconciliation.incoming', [], false) }}" class="um-subnav-item {{ request()->routeIs('bank-deposit-reconciliation.incoming') ? 'active' : '' }}">Incoming</a>
    <a href="{{ route('bank-deposit-reconciliation.outgoing', [], false) }}" class="um-subnav-item {{ request()->routeIs('bank-deposit-reconciliation.outgoing') ? 'active' : '' }}">Outgoing</a>
</nav>
