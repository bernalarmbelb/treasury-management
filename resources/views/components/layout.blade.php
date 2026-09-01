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
			<div class="nav-datetime">
				<span id="live-date" class="nav-date"></span>
				<span id="live-time" class="nav-time"></span>
			</div>

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

			<div class="nav-user">
				<div class="nav-user-avatar">
					<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
					</svg>
				</div>
				<div class="nav-user-info">
					<span class="nav-user-name">{{ auth()->user()?->name }}</span>
					<div class="nav-user-meta">
						<span class="nav-user-role">{{ auth()->user()?->roles->first()?->name ?? 'User' }}</span>
						<form method="POST" action="{{ route('logout') }}" style="display:inline;">
							@csrf
							<button type="submit" class="nav-logout-btn">Logout</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</nav>
	<div class="nav-sticky-wrapper">
		<div class="" style="display:flex; width: 100%">
			<button class="nav-scroll-btn nav-scroll-left" id="scrollLeft">&#8249;</button>
			<nav class="navigation-bar" id="navigationBar">
				<p><a href="{{ route('collections') }}" class=" {{ request()->routeIs('collections') || request()->routeIs('collections.view') || request()->routeIs('transaction-entry*') ? 'active' : '' }} "> Collections Management </a></p>

				<p><a href="{{ route('official-receipts-accountable-forms') }}" class=" {{ request()->routeIs('official-receipts-accountable-forms') ? 'active' : '' }} ">Official Receipts & Accountable Forms</a></p>

				<p><a href="{{ route('reporting-abstract') }}" class=" {{ request()->routeIs('reporting-abstract') ? 'active' : '' }} ">Reporting & Abstract</a></p>

				<p><a href="{{ route('bank-deposit-reconciliation') }}" class=" {{ request()->routeIs('bank-deposit-reconciliation') ? 'active' : '' }} ">Banks Deposit & Reconciliation</a></p>
				
				<p><a href="{{ route('cheque-management') }}" class=" {{ request()->routeIs('cheque-management') ? 'active' : '' }} ">Cheque Management</a></p>

				<p><a href="{{ route('user-management') }}" class=" {{ request()->routeIs('user-management*') ? 'active' : '' }} ">User Management</a></p>

				<p><a href="{{ route('records') }}" class=" {{ request()->routeIs('records') ? 'active' : '' }} ">Records</a></p>

				<p><a href="{{ route('archives') }}" class=" {{ request()->routeIs('archives') ? 'active' : '' }} ">Archive</a></p>
			</nav>
			<button class="nav-scroll-btn nav-scroll-right" id="scrollRight">&#8250;</button>
		</div>
	</div>

	<main class="main-container">
		{{ $slot }}
	</main>

	@if (($quickEntryForms ?? collect())->isNotEmpty())
		<div class="quick-entry-bar" id="quickEntryBar">
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
		</div>
	@endif

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

	// ── Quick entry footer bar ───────────────────────────────────────────
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

		window.addEventListener('resize', applyPadding);
		window.addEventListener('load', applyPadding);
	})();
	</script>
</body>
</html>