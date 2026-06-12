<x-layout>
    @php 
        $tmpRoute = route('official-receipts-accountable-forms'); 
        $routeName = 'official-receipts-accountable-forms';
    @endphp

    <x-header title="Official Receipt & Accountable Forms"
        :tmpRoute="$tmpRoute"
        :routeName="$routeName"
    />
</x-layout>