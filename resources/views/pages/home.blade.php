@extends('layouts.app')

@section('title', 'Portfolio')

@push('scripts')
    @vite('resources/js/vue.js')
@endpush
@section('content')
    <div class="w-full">
        <x-backgrounds.home-banner :background="$background" />
        <div class="mt-4 md:mt-6">
            <showcase
                :categories='@json($techCategories)'
                :exploring='@json($exploringTech)'
                :projects='@json($projects)'
            ></showcase>
        </div>

        <div class="mx-auto">
            <div class="my-8 flex justify-end">
                <a href="#contact" class="btn-primary mt-2 inline-flex items-center gap-2 self-center whitespace-nowrap rounded-xl px-8 py-4 text-lg font-semibold shadow-sm transition-shadow hover:shadow-md md:mt-0 md:ml-6 md:px-10 md:py-5 md:text-xl">
                    <i class="fa fa-paper-plane"></i> Contact Me
                </a>
            </div>
        </div>
    </div>
@endsection
