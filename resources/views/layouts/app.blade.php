<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zachary Craig - @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{mix('css/app.css')}}">
    @stack('styles')
</head>
<body>
<div id="header">
    @include('components.header')
</div>
<div id="content">
    <div id="app">
        @yield('content')
    </div>
</div>
<div id="footer">
    @include('components.footer')
</div>
<script src="{{mix('js/app.js')}}"></script>
@stack('scripts')
</body>
</html>
