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
    <div id="content" class="mx-auto w-full px-4 sm:px-6 lg:px-8">
        @yield('content')
    </div>
    <div id="footer">
        <x-navigation.footer/>
    </div>
</div>
@livewireScripts
@vite('resources/js/app.js')
@stack('scripts')
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{config('services.ga.property_id')}}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '{{config('services.ga.property_id')}}');
</script>
</body>
</html>
