@extends('layouts.app')
@section('title', 'Radio QSOs')
@push('styles')
    <link
        href="https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.css"
        rel="stylesheet"
    />
@endpush

@section('content')
    <div class="container-fluid pb-5">
        <div class="container mx-auto">
            <h1 class="text-3xl">Radio QSO Map</h1>
            <qso-map
                mapbox-key="{{config('services.mapbox.token')}}"
            />
        </div>
    </div>
@endsection
