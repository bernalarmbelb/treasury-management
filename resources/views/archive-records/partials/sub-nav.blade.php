@php $tab = $tab ?? 'collection-management'; @endphp

<div class="view-actions">
    <div class="view-tabs">
        <a href="{{ route('archives', ['tab' => 'collection-management']) }}" class="view-tab {{ $tab === 'collection-management' ? 'active' : '' }}">
            <svg class="view-tab-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 12v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-6"/><path d="M2 7h20v5H2z"/><path d="M12 12v10"/><path d="M12 7c-1.5-3-6-3-6 0s4.5 3 6 0Zm0 0c1.5-3 6-3 6 0s-4.5 3-6 0Z"/></svg>
            Collection Management
        </a>
        @unless (auth()->user()?->hasRole('collector'))
            <a href="{{ route('archives', ['tab' => 'user-management']) }}" class="view-tab {{ $tab === 'user-management' ? 'active' : '' }}">
                <svg class="view-tab-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                User Management
            </a>
        @endunless
    </div>
</div>
