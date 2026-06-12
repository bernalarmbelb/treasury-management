@props(['title' => 'Page Title', 'tmpRoute' => '', 'routeName' => '', 'parentTitle' => '', 'parentRoute' => '', 'parentRouteName' => ''])

<nav class="container-title">
	<div style="display: flex; flex-direction: column;">
		<span class="page-title">{{ $title ?? 'Page Title' }}</span>

		<p class="page-links">
			<a href="{{ route('home') }}" class=" {{ request()->routeIs('home') ? 'active' : '' }} "> Home </a> |
			@if ($parentTitle)
				<a href="{{ $parentRoute }}" class=" {{ request()->routeIs($parentRouteName) ? 'active' : '' }} "> {{ $parentTitle }} </a> |
			@endif
			<a href="{{ $tmpRoute }}" class=" {{ request()->routeIs($routeName) ? 'active' : '' }} "> {{ $title }} </a>
		</p>
	</div>
</nav>