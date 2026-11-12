<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Умная теплица</title>
        <script>document.documentElement.dataset.theme = localStorage.getItem('greenhouse-theme') || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');</script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="gh-auth">
            <section class="gh-auth-card">
                <div class="gh-auth-brand">
                    <a class="gh-brand" href="{{ url('/') }}"><span class="gh-mark">G</span><span>Умная теплица</span></a>
                </div>
                {{ $slot }}
            </section>
        </div>
    </body>
</html>
