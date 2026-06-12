<x-layout>
    @php 
        $tmpRoute = route('archives'); 
        $routeName = 'archives';
    @endphp

    <x-header title="Archives"
        :tmpRoute="$tmpRoute"
        :routeName="$routeName"
    />
</x-layout>