<x-layout>
    @php
        $role = $user->roles->first();
    @endphp

    <div class="x-header-container sub-nav-sticky">
        <div class="container-title">
            <div style="display: flex; flex-direction: column;">
                <span class="page-title">Archives</span>
                <p class="page-links">
                    <a href="{{ route('home') }}">Home</a> |
                    <a href="{{ route('archives') }}">Archives</a> |
                    <a href="{{ route('archives', ['tab' => 'user-management']) }}">User Management</a> |
                    <span class="page-links-accent">{{ $user->name }}</span>
                </p>
            </div>
        </div>
    </div>

    <div class="collection-content">
        <div class="ctc-view-page">

            <div class="ctc-view-meta">
                <span class="status-badge" style="background:#6c757d; color:#fff; border-radius:4px; padding:3px 10px; font-size:12px; font-weight:600;">Archived</span>
                @if($user->archived_at)
                    <span class="ctc-view-meta-info">Archived on {{ $user->archived_at->format('F j, Y — h:i A') }}</span>
                @endif
            </div>

            <div class="ctc-view-paper" style="max-width:560px;">
                <table class="ctc-view-details-table">
                    <tbody>
                        <tr>
                            <th>Name</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td>{{ $user->username ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Mobile</th>
                            <td>{{ $user->mobile ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>{{ $role?->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Archived At</th>
                            <td>{{ $user->archived_at?->format('F j, Y') ?? '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="ctc-view-actions">
                <a href="{{ route('archives', ['tab' => 'user-management']) }}"
                   class="ctc-view-cancel-request-btn">
                    &larr; Back to Archives
                </a>
            </div>

        </div>
    </div>
</x-layout>
