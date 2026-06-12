<x-layout>
    @php 
        $tmpRoute = route('records'); 
        $routeName = 'records';
    @endphp

    <x-header title="Records"
        :tmpRoute="$tmpRoute"
        :routeName="$routeName"
    />
</x-layout>