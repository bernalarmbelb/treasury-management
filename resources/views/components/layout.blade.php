<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Treasury Management System</title>
	 @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
	<nav class="navigation-header">
		<a href="{{ route('home') }}">
			<img src="{{ asset('images/logo.webp') }}" alt="Logo" class="logo-image">
		</a>
			<div class="d-flex flex-column gap-0">
				<span style="font-size: 12px; font-weight: 500; color: var(--fonts-black); margin: 0px;">Republic of the Philippines</span>
				<span style="font-size: 14px; font-weight: 900; color: var(--fonts-black); margin: 0px;">MUNICIPALITY OF PRIETO DIAZ</span>
			</div>
		<div class="d-flex flex-column gap-0 ms-auto">
			<span id="live-date" style="font-size: 12px; font-weight: 500; color: var(--fonts-black);"></span>
			<span id="live-time" style="font-size: 12px; font-weight: 600; color: var(--fonts-black);"></span>
		</div>
	</nav>
	<div class="nav-sticky-wrapper">
		<div class="" style="display:flex; width: 100%">
			<button class="nav-scroll-btn nav-scroll-left" id="scrollLeft">&#8249;</button>
			<nav class="navigation-bar" id="navigationBar">
				<p><a href="{{ route('collections') }}" class=" {{ request()->routeIs('collections') || request()->routeIs('transaction-entry*') ? 'active' : '' }} "> Collections Management </a></p>

				<p><a href="{{ route('official-receipts-accountable-forms') }}" class=" {{ request()->routeIs('official-receipts-accountable-forms') ? 'active' : '' }} ">Official Receipts & Accountable Forms</a></p>

				<p><a href="{{ route('reporting-abstract') }}" class=" {{ request()->routeIs('reporting-abstract') ? 'active' : '' }} ">Reporting & Abstract</a></p>

				<p><a href="{{ route('bank-deposit-reconciliation') }}" class=" {{ request()->routeIs('bank-deposit-reconciliation') ? 'active' : '' }} ">Banks Deposit & Reconciliation</a></p>
				
				<p><a href="{{ route('cheque-management') }}" class=" {{ request()->routeIs('cheque-management') ? 'active' : '' }} ">Cheque Management</a></p>

				<p><a href="{{ route('user-management') }}" class=" {{ request()->routeIs('user-management') ? 'active' : '' }} ">User Management</a></p>

				<p><a href="{{ route('records') }}" class=" {{ request()->routeIs('records') ? 'active' : '' }} ">Records</a></p>

				<p><a href="{{ route('archives') }}" class=" {{ request()->routeIs('archives') ? 'active' : '' }} ">Archive</a></p>
			</nav>
			<button class="nav-scroll-btn nav-scroll-right" id="scrollRight">&#8250;</button>
		</div>
	</div>

	<main class="main-container">
		{{ $slot }}
	</main>

	@stack('scripts')
</body>
</html>