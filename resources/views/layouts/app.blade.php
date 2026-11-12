<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' — ' : '' }}Умная теплица</title>
    <script>document.documentElement.dataset.theme = localStorage.getItem('greenhouse-theme') || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="gh-app">
        @include('layouts.navigation')
        <main>
            <div class="gh-container gh-page">
                @isset($header)
                    <header class="mb-6">{{ $header }}</header>
                @endisset
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
