<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@hasSection('title')
        @yield('title') | Bleatz
    @else
        Bleatz
    @endif</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    <div class="admin-app">
        @include('components.admin.layout.sidebar')

        <div class="admin-main-wrapper">
            @include('components.admin.layout.navbar')

            <main class="admin-content">
                @if (session('success'))
                    <div class="admin-alert admin-alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="admin-alert admin-alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content', $slot ?? '')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
