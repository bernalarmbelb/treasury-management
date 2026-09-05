<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Treasury Management System</title>
	<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
	 @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
	<nav class="navigation-header">
		<a href="{{ route('home') }}" class="nav-branding">
			<img src="{{ asset('images/logo.webp') }}" alt="Logo" class="logo-image">
			<div class="nav-branding-text">
				<span class="nav-branding-sub">Republic of the Philippines</span>
				<span class="nav-branding-name">SOLEM IT & Digital Solutions Co.</span>
			</div>
		</a>

		<div class="nav-profile">
			<div class="nav-bell-wrapper" id="notifBellWrapper">
				<button type="button" class="nav-bell-btn" id="notifBellBtn" aria-label="Notifications" aria-expanded="false">
					<svg class="nav-bell-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
					</svg>
					<span class="nav-bell-badge" id="notifBadge" style="display:none;">0</span>
				</button>
				<div class="notif-dropdown" id="notifDropdown" aria-hidden="true">
					<div class="notif-dropdown-header">
						<span class="notif-dropdown-title">Notifications</span>
						<span class="notif-dropdown-count" id="notifDropdownCount"></span>
					</div>
					<div class="notif-list" id="notifList">
						<div class="notif-loading">Loading…</div>
					</div>
				</div>
			</div>

			<div class="nav-divider" aria-hidden="true"></div>

			@php
				$navUserName = auth()->user()?->name ?? '';
				$navNameParts = preg_split('/\s+/', trim($navUserName));
				$navInitials = strtoupper(substr($navNameParts[0] ?? '', 0, 1) . substr($navNameParts[count($navNameParts) - 1] ?? '', 0, 1));
				$navUserRole = ucfirst(auth()->user()?->roles->first()?->name ?? 'User');
			@endphp

			<div class="nav-user-wrapper" id="navUserWrapper">
				<button type="button" class="nav-user-chip" id="navUserChip" aria-haspopup="true" aria-expanded="false">
					<span class="nav-user-avatar">{{ $navInitials }}</span>
					<span class="nav-user-info">
						<span class="nav-user-name">{{ $navUserName }}</span>
						<span class="nav-user-role">{{ $navUserRole }}</span>
					</span>
					<svg class="nav-user-chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
				</button>

				<div class="nav-user-dropdown" id="navUserDropdown" aria-hidden="true">
					<div class="nav-user-dropdown-head">
						<span class="nav-user-dropdown-avatar">{{ $navInitials }}</span>
						<div>
							<div class="nav-user-dropdown-name">{{ $navUserName }}</div>
							<span class="nav-user-dropdown-role">{{ $navUserRole }}</span>
						</div>
					</div>
					<hr class="nav-user-dropdown-rule">
					<form method="POST" action="{{ route('logout') }}">
						@csrf
						<button type="submit" class="nav-user-dropdown-logout">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
							Log out
						</button>
					</form>
				</div>
			</div>
		</div>
	</nav>
	<div class="nav-sticky-wrapper">
		<div class="" style="display:flex; width: 100%">
			<button class="nav-scroll-btn nav-scroll-left" id="scrollLeft">&#8249;</button>
			<nav class="navigation-bar" id="navigationBar">
				<p><a href="{{ route('collections') }}" class=" {{ request()->routeIs('collections') || request()->routeIs('collections.view') || request()->routeIs('transaction-entry*') ? 'active' : '' }} ">
						<svg class="nav-link-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 12v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-6"/><path d="M2 7h20v5H2z"/><path d="M12 12v10"/><path d="M12 7c-1.5-3-6-3-6 0s4.5 3 6 0Zm0 0c1.5-3 6-3 6 0s-4.5 3-6 0Z"/></svg>
						Collections Management
					</a></p>

				<p><a href="{{ route('official-receipts-accountable-forms') }}" class=" {{ request()->routeIs('official-receipts-accountable-forms') ? 'active' : '' }} ">
						<svg class="nav-link-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2h9l3 3v17l-2-1.5-2 1.5-2-1.5-2 1.5-2-1.5-2 1.5V2Z"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>
						Official Receipts & Accountable Forms
					</a></p>

				<p><a href="{{ route('reporting-abstract') }}" class=" {{ request()->routeIs('reporting-abstract') ? 'active' : '' }} ">
						<svg class="nav-link-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3v18h18"/><path d="M8 17V10M13 17V6M18 17v-4"/></svg>
						Reporting & Abstract
					</a></p>

				@unless (auth()->user()?->hasRole('collector'))
					<p><a href="{{ route('bank-deposit-reconciliation') }}" class=" {{ request()->routeIs('bank-deposit-reconciliation') ? 'active' : '' }} ">
							<svg class="nav-link-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10 12 3l9 7"/><path d="M5 10v10M9 10v10M15 10v10M19 10v10"/><path d="M3 20h18"/></svg>
							Banks Deposit & Reconciliation
						</a></p>

					<p><a href="{{ route('cheque-management') }}" class=" {{ request()->routeIs('cheque-management') ? 'active' : '' }} ">
							<svg class="nav-link-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/></svg>
							Cheque Management
						</a></p>

					<p><a href="{{ route('user-management') }}" class=" {{ request()->routeIs('user-management*') ? 'active' : '' }} ">
							<svg class="nav-link-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
							User Management
						</a></p>
				@endunless

				<p><a href="{{ route('records') }}" class=" {{ request()->routeIs('records') ? 'active' : '' }} ">
						<svg class="nav-link-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19V6a2 2 0 0 1 2-2h5l2 2h5a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z"/></svg>
						Records
					</a></p>

				<p><a href="{{ route('archives') }}" class=" {{ request()->routeIs('archives') ? 'active' : '' }} ">
						<svg class="nav-link-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="5" rx="1"/><path d="M4 9v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9"/><path d="M10 13h4"/></svg>
						Archive
					</a></p>
			</nav>
			<button class="nav-scroll-btn nav-scroll-right" id="scrollRight">&#8250;</button>
		</div>
	</div>

	<main class="main-container">
		{{ $slot }}
	</main>

	<div class="quick-entry-bar" id="quickEntryBar">
		@if (($quickEntryForms ?? collect())->isNotEmpty())
			<button type="button" class="quick-entry-toggle" id="quickEntryToggle" aria-label="Toggle quick entry links" aria-expanded="true">
				<span class="qe-icon" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2 3 14h7v8l10-12h-7z"/></svg>
				</span>
				Quick Entry
				<span class="quick-entry-count">{{ $quickEntryForms->count() }}</span>
				<span class="quick-entry-chevron" aria-hidden="true">&#9662;</span>
			</button>
			<div class="quick-entry-links" id="quickEntryLinks">
				@foreach ($quickEntryForms as $qf)
					<a href="{{ $qf['url'] }}" class="quick-entry-link">
						{{ $qf['label'] }}
						<span class="quick-entry-badge {{ $qf['qty'] <= 5 ? 'low' : '' }}">{{ $qf['qty'] }} left</span>
					</a>
				@endforeach
			</div>
		@endif

		<div class="footer-datetime">
			<svg class="footer-datetime-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
			<span id="live-date" class="footer-date"></span>
			<span class="footer-datetime-sep" aria-hidden="true">&middot;</span>
			<span id="live-time" class="footer-time"></span>
		</div>
	</div>

	<div class="app-toast" id="appToast" role="status" aria-live="polite">
		<span class="app-toast-icon" id="appToastIcon" aria-hidden="true"></span>
		<div class="app-toast-text">
			<p class="app-toast-title" id="appToastTitle"></p>
			<p class="app-toast-subtitle" id="appToastSubtitle"></p>
		</div>
	</div>

	@stack('scripts')

	<script>
	// ── Global toast (success / error feedback) ───────────────────────────
	(function () {
		const ICONS = {
			success: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>',
			error: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>',
		};

		let toastTimer;

		window.showToast = function (title, subtitle = '', type = 'success') {
			const toast = document.getElementById('appToast');
			if (!toast) return;

			const titleEl = document.getElementById('appToastTitle');
			const subtitleEl = document.getElementById('appToastSubtitle');
			const iconEl = document.getElementById('appToastIcon');
			const variant = type === 'error' ? 'error' : 'success';

			titleEl.textContent = title;
			subtitleEl.textContent = subtitle || '';
			subtitleEl.style.display = subtitle ? '' : 'none';
			iconEl.innerHTML = ICONS[variant];

			toast.classList.remove('app-toast--success', 'app-toast--error');
			toast.classList.add('app-toast--' + variant, 'show');

			clearTimeout(toastTimer);
			toastTimer = setTimeout(() => toast.classList.remove('show'), 3500);
		};
	})();

	// ── Live clock ────────────────────────────────────────────────────────
	// The header clock is driven solely by updateDateTime() in resources/js/app.js
	// (the Vite-bundled script). A second inline clock used to run here too; both
	// wrote #live-date every second with different day formats ("July 04" vs
	// "July 4"), so they alternated and made the date jitter. Removed the duplicate.

	// ── Notification bell ────────────────────────────────────────────────
	(function () {
		const bellBtn    = document.getElementById('notifBellBtn');
		if (!bellBtn) return;

		const dropdown   = document.getElementById('notifDropdown');
		const badge      = document.getElementById('notifBadge');
		const list       = document.getElementById('notifList');
		const countLabel = document.getElementById('notifDropdownCount');
		const csrf       = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
		let isOpen = false;

		function updateBadge(count) {
			if (count > 0) {
				badge.textContent = count > 99 ? '99+' : count;
				badge.style.display = '';
				bellBtn.classList.add('has-unread');
			} else {
				badge.style.display = 'none';
				bellBtn.classList.remove('has-unread');
			}
			if (countLabel) {
				countLabel.textContent = count > 0 ? count + ' new' : '';
			}
		}

		function fetchCount() {
			fetch('/notifications/count', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
				.then(r => r.json())
				.then(d => updateBadge(d.count ?? 0))
				.catch(() => {});
		}

		function tagFor(type) {
			if (type === 'request_rejected' || type === 'batch_request_rejected') {
				return '<span class="notif-type-tag notif-type-tag--rejected">Request Rejected</span>';
			}
			if (type === 'batch_request') {
				return '<span class="notif-type-tag">Batch Request</span>';
			}
			return '<span class="notif-type-tag">Cancel Request</span>';
		}

		function actionFor(type) {
			if (type === 'request_rejected' || type === 'batch_request_rejected') return '<span class="notif-review-btn">View →</span>';
			return '<span class="notif-review-btn">Review →</span>';
		}

		function renderNotifications(items) {
			const hasUnseen = items.some(i => i.seen === false);
			const emptyText = {{ auth()->user()?->hasRole('admin') ? 'true' : 'false' }}
				? 'No pending requests'
				: 'No new notifications';

			if (!items.length) {
				list.innerHTML =
					'<div class="notif-empty">' +
						'<svg class="notif-empty-icon" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>' +
						'<span class="notif-empty-text">' + emptyText + '</span>' +
					'</div>';
				return;
			}

			list.innerHTML = items.map(function (item) {
				const unseenClass = (item.seen === false) ? ' notif-item--unseen' : '';
				return '<a href="' + item.url + '" class="notif-item' + unseenClass + '">' +
					'<div class="notif-item-top">' +
						tagFor(item.type) +
						'<span class="notif-time">' + item.created_at + '</span>' +
					'</div>' +
					'<div class="notif-item-body">' +
						'<div class="notif-desc">' +
							'<span class="notif-serial">' + (item.serial || '—') + '</span>' +
							'<span class="notif-payee">' + (item.payee || '—') + '</span>' +
						'</div>' +
						actionFor(item.type) +
					'</div>' +
				'</a>';
			}).join('');
		}

		function markSeen() {
			fetch('/notifications/mark-seen', {
				method: 'POST',
				headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() },
			})
			.then(() => updateBadge(0))
			.catch(() => {});
		}

		function loadNotifications() {
			list.innerHTML = '<div class="notif-loading">Loading…</div>';
			fetch('/notifications', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
				.then(r => r.json())
				.then(d => {
					const items = d.items ?? [];
					renderNotifications(items);
					// Non-admin: mark unseen rejections as seen after viewing
					const hasUnseen = items.some(i => i.seen === false);
					if (hasUnseen) markSeen();
				})
				.catch(() => { list.innerHTML = '<div class="notif-loading">Could not load notifications.</div>'; });
		}

		function openDropdown() {
			isOpen = true;
			document.getElementById('navUserDropdown')?.classList.remove('open');
			document.getElementById('navUserChip')?.classList.remove('open');
			dropdown.classList.add('open');
			bellBtn.setAttribute('aria-expanded', 'true');
			dropdown.setAttribute('aria-hidden', 'false');
			loadNotifications();
		}

		function closeDropdown() {
			isOpen = false;
			dropdown.classList.remove('open');
			bellBtn.setAttribute('aria-expanded', 'false');
			dropdown.setAttribute('aria-hidden', 'true');
		}

		bellBtn.addEventListener('click', function (e) {
			e.stopPropagation();
			isOpen ? closeDropdown() : openDropdown();
		});

		document.addEventListener('click', function (e) {
			if (isOpen && !dropdown.contains(e.target) && e.target !== bellBtn) closeDropdown();
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && isOpen) closeDropdown();
		});

		fetchCount();
		setInterval(fetchCount, 30000);
	})();

	// ── User menu dropdown ───────────────────────────────────────────────
	(function () {
		const chip = document.getElementById('navUserChip');
		if (!chip) return;

		const dropdown = document.getElementById('navUserDropdown');
		let isOpen = false;

		function closeNotifDropdown() {
			document.getElementById('notifDropdown')?.classList.remove('open');
			document.getElementById('notifBellBtn')?.setAttribute('aria-expanded', 'false');
		}

		function openDropdown() {
			isOpen = true;
			closeNotifDropdown();
			dropdown.classList.add('open');
			chip.classList.add('open');
			chip.setAttribute('aria-expanded', 'true');
			dropdown.setAttribute('aria-hidden', 'false');
		}

		function closeDropdown() {
			isOpen = false;
			dropdown.classList.remove('open');
			chip.classList.remove('open');
			chip.setAttribute('aria-expanded', 'false');
			dropdown.setAttribute('aria-hidden', 'true');
		}

		chip.addEventListener('click', function (e) {
			e.stopPropagation();
			isOpen ? closeDropdown() : openDropdown();
		});

		document.addEventListener('click', function (e) {
			if (isOpen && !dropdown.contains(e.target) && e.target !== chip) closeDropdown();
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && isOpen) closeDropdown();
		});
	})();

	// ── Footer bar (date/time + quick entry links) ───────────────────────
	(function () {
		const bar = document.getElementById('quickEntryBar');
		if (!bar) return;

		const toggle = document.getElementById('quickEntryToggle');
		const STORAGE_KEY = 'quickEntryCollapsed';

		// Reserve space at the bottom of the page so the fixed bar never
		// covers page content (tables, pagination, action buttons).
		function applyPadding() {
			document.body.style.paddingBottom = bar.offsetHeight + 'px';
		}

		window.addEventListener('resize', applyPadding);
		window.addEventListener('load', applyPadding);
		applyPadding();

		if (!toggle) return;

		function setCollapsed(collapsed) {
			bar.classList.toggle('collapsed', collapsed);
			toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
			applyPadding();
		}

		setCollapsed(localStorage.getItem(STORAGE_KEY) === '1');

		toggle.addEventListener('click', function () {
			const collapsed = !bar.classList.contains('collapsed');
			localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
			setCollapsed(collapsed);
		});
	})();
	</script>
</body>
</html>