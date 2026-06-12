<x-layout>
    @php 
        $tmpRoute = route('reporting-abstract'); 
        $routeName = 'reporting-abstract';
    @endphp

    <x-header title="Reporting & Abstract"
        :tmpRoute="$tmpRoute"
        :routeName="$routeName"
    />

</x-layout>