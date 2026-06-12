<x-layout>
    @php 
        $tmpRoute = route('collections'); 
        $routeName = 'collections';
    @endphp

    <x-header title="Collection Management"
        :tmpRoute="$tmpRoute"
        :routeName="$routeName"
    />

</x-layout>