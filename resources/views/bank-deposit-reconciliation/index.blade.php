<x-layout>
    @php 
        $tmpRoute = route('bank-deposit-reconciliation'); 
        $routeName = 'bank-deposit-reconciliation';
    @endphp

    <x-header title="Bank Deposit & Reconciliation"
        :tmpRoute="$tmpRoute"
        :routeName="$routeName"
    />
</x-layout>