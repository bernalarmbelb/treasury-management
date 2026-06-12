<x-layout>
    @php 
        $tmpRoute = route('home'); 
        $routeName = 'home';
    @endphp

    <x-header title="Dashboard"
        :tmpRoute="$tmpRoute"
        :routeName="$routeName"
    />

    <div class="d-flex flex-column mb-4">
        <p class='page-title'> Page Title</p>
        <p class='page-links'> Page Subtitle</p>
    </div>

    <div class="d-flex flex-column gap-1";>
        <button class="btn btn-primary">Primary Button</button>
        <button class="btn btn-secondary">Secondary Button</button>
        <button class="btn btn-accent">Accent Button</button>   
        <button class="btn btn-success">Success Button</button>
        <button class="btn btn-danger">Danger Button</button>
        <button class="btn btn-warning">Warning Button</button>
        <button class="btn btn-info">Info Button</button>
        <button class="btn btn-light">Light Button</button>
        <button class="btn btn-dark">Dark Button</button>
    </div>
</x-layout>