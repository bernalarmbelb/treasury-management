@props(['paginator'])

<div class="pagination-controls">
	@if ($paginator->onFirstPage())
		<span class="page-btn page-nav" aria-disabled="true">Previous</span>
	@else
		<a class="page-btn page-nav" href="{{ $paginator->previousPageUrl() }}">Previous</a>
	@endif

	<span class="page-jump-label">Page</span>
	<select class="page-jump-select js-cs" data-cs-inline aria-label="Jump to page" onchange="window.location.href = this.value">
		@foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
			<option value="{{ $url }}" @selected($page === $paginator->currentPage())>{{ $page }}</option>
		@endforeach
	</select>
	<span class="page-jump-label">of {{ $paginator->lastPage() }}</span>

	@if ($paginator->hasMorePages())
		<a class="page-btn page-nav" href="{{ $paginator->nextPageUrl() }}">Next</a>
	@else
		<span class="page-btn page-nav" aria-disabled="true">Next</span>
	@endif
</div>
