<x-layout>
    @php 
        $tmpRoute = route('user-management'); 
        $routeName = 'user-management';
    @endphp

    <x-header title="User Management"
        :tmpRoute="$tmpRoute"
        :routeName="$routeName"
    />
</x-layout>