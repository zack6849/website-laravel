<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
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
    <div id="content" class="container-fluid">
        @yield('content')
    </div>
    <div id="footer">
        <x-navigation.footer/>
    </div>
</div>
@livewireScripts
@stack('scripts')
@vite('resources/js/app.js')
</body>
</html>
