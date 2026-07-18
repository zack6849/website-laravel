<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('meta')
    <title>@yield('title') - {{ config('app.site_name', config('app.name', 'Laravel')) }}</title>


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    @livewireStyles
    @vite('resources/css/app.css')
    @stack('styles')
</head>
<body class="h-screen">
<div id="app">
    <div id="header">
        <x-navigation.header/>
    </div>
    <div id="content" class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
        @yield('content')
    </div>
    <div id="footer">
        <x-navigation.footer/>
    </div>
</div>
@livewireScripts
@vite('resources/js/app.js')
@stack('scripts')
</body>
</html>
