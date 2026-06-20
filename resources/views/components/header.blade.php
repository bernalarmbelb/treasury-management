@props(['title' => 'Page Title', 'tmpRoute' => '', 'routeName' => '', 'parentTitle' => '', 'parentRoute' => '', 'parentRouteName' => '', 'extraTitle' => '', 'extraRoute' => '', 'extraRouteName' => ''])

<nav class="container-title">
	<div style="display: flex; flex-direction: column;">
		<span class="page-title">{{ $title ?? 'Page Title' }}</span>

		<p class="page-links">
			<a href="{{ route('home') }}" class=" {{ request()->routeIs('home') ? 'active' : '' }} "> Home </a> |
			@if ($parentTitle)
				<a href="{{ $parentRoute }}" class=" {{ request()->routeIs($parentRouteName) ? 'active' : '' }} "> {{ $parentTitle }} </a> |
			@endif
			@if ($extraTitle)
				@if ($extraRoute)
					<a href="{{ $extraRoute }}" class=" {{ request()->routeIs($extraRouteName) ? 'active' : '' }} "> {{ $extraTitle }} </a> |
				@else
					<span class="breadcrumb-static">{{ $extraTitle }}</span> |
				@endif
			@endif
			<a href="{{ $tmpRoute }}" class=" {{ request()->routeIs($routeName) ? 'active' : '' }} "> {{ $title }} </a>
		</p>
	</div>
</nav>

@isset($actions)
	<div class="um-header-actions">
		{{ $actions }}
	</div>
@endisset