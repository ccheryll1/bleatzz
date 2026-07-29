<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Bleatz — Kantin Kampus Digital')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('asset/LandingPage/favicon.png') }}">

    {{-- App CSS (Vite) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @auth
    <script>
        window.__cartCountUrl = @json(route('cart.count'));
    </script>
    @endauth

    {{-- Extra head content per-page --}}
    @stack('head')
</head>
<body>

    {{-- Fixed Navbar --}}
    @include('pages.landingpage.layout.navbar')

    {{-- Main content — padded top to clear fixed navbar --}}
    <main style="padding-top: 64px;">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('pages.landingpage.layout.footer')

    {{-- Global Modals --}}
    <x-landingpage.modal.modal-cart />

    {{-- Extra scripts per-page --}}
    @stack('scripts')

</body>
</html>
