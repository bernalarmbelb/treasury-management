<x-layout>
    <x-header title="Dashboard" :tmpRoute="route('home')" routeName="home" />
    <div class="dash-wrap">
        <h1 class="page-title" style="display:none">Dashboard</h1>
        <div>Collections: ₱{{ number_format($collections['total'], 2) }}</div>
    </div>
</x-layout>
