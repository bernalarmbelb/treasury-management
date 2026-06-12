@props(['title' => 'Page Title', 'tmpRoute' => '', 'routeName' => ''])

<nav class="container-title">
	<div style="display: flex; flex-direction: column;">
		<span class="page-title">{{ $title ?? 'Page Title' }}</span>
		
		<p class="page-links">
			<a href="{{ route('home') }}" class=" {{ request()->routeIs('home') ? 'active' : '' }} "> Home </a> |
			<a href="{{ $tmpRoute }}" class=" {{ request()->routeIs($routeName) ? 'active' : '' }} "> {{ $title }} </a>
		</p>
	</div>
</nav>