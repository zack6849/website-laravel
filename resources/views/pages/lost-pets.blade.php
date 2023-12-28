@extends('layouts.app')
@section('title', 'Lost Pets')
@push('styles')
    <style>
        .pet{
            max-width: 250px;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid pb-5">
        <div class="container mx-auto">
            <h1 class="text-3xl pb-2">Lost Pets</h1>
        </div>
        <div class="flex justify-between flex-wrap px-2">
            @foreach($pets as $pet)
                @include('components.partials.pet-card', ['pet' => $pet])
            @endforeach
        </div>
    </div>
@endsection
