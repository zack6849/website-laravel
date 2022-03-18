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
<script src="{{mix('/js/manifest.js')}}"></script>
<script src="{{mix('/js/vendor.js')}}"></script>
<script src="{{mix('js/app.js')}}" defer></script>
@livewireScripts
@stack('scripts')
</body>
</html>
