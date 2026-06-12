<x-layout>
    @php 
        $tmpRoute = route('cheque-management'); 
        $routeName = 'cheque-management';
    @endphp

    <x-header title="Cheque Management"
        :tmpRoute="$tmpRoute"
        :routeName="$routeName"
    />
</x-layout>