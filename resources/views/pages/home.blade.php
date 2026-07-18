@extends('layouts.app')

@section('title', 'Zachary Craig')

@push('scripts')
    @vite('resources/js/vue.js')
@endpush
@section('content')
    <div class="w-full py-4 md:py-6">
        <div class="mx-auto flex flex-col gap-4 p-4 lg:flex-row lg:items-start">
            <div class="profile-summary mb-2 flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:gap-6 lg:mb-6 lg:flex-1 max-w-full">
                <div class="profile-img-container shrink-0">
                    <img alt="Zachary Craig" src="{{asset('img/profile.jpg')}}" class="profile-image rounded-full w-20 sm:w-24 md:w-32 lg:w-48">
                </div>
                <div class="min-w-0">
                    <div class="business-card">
                        <h1 class="font-heavy text-4xl leading-tight sm:text-6xl">Zachary Craig</h1>
                        <h2><i class="fa fa-briefcase" aria-hidden="true"></i>Senior Full-Stack Engineer &mdash; Laravel / PHP</h2>
                        <h3><i class="fa fa-thumb-tack" aria-hidden="true"></i>St. Petersburg, Florida</h3>
                        <p class="pt-2">
                            I like working on technical problems and building things.
                        </p>
                        <p>
                            In my spare time, I like to take photos and tinker with electronics
                        </p>
                        <b>Nice to meet you.</b>
                    </div>
                </div>
            </div>
            <div id="contact" class="w-full lg:mx-auto lg:w-auto">
                <h2 class="flex-row">
                    <span class="text-2xl">&#128205;</span> Places you can find me
                </h2>
                <div class="my-2 flex flex-wrap items-center gap-2">
                    <a target="_blank"
                       class="btn-social hover:bg-gray-900"
                       href="https://github.com/zack6849"
                    >
                        <i class="fab fa-github" aria-hidden="true"></i> GitHub
                    </a>
                    <a target="_blank"
                       class="btn-social hover:bg-gray-900"
                       href="https://www.linkedin.com/in/zack6849/"
                    >
                        <i class="fab fa-linkedin" aria-hidden="true"></i> LinkedIn
                    </a>
                    <a target="_blank"
                       class="btn-social hover:bg-gray-900"
                       href="https://stackoverflow.com/users/1932789/zack6849"
                    >
                        <i class="fab fa-stack-overflow" aria-hidden="true"></i> StackOverflow
                    </a>
                    <a class="btn-social hover:bg-gray-900"
                       href="mailto:web-contact@zcraig.me"
                    >
                        <i class="fa fa-envelope" aria-hidden="true"></i> web-contact@zcraig.me
                    </a>
                    <span
                       title="Amateur radio band"
                       class="btn-social"
                    >
                        <i class="fa-solid fa-tower-cell" aria-hidden="true"></i> 20M
                    </span>
                </div>
            </div>
        </div>
        <div class="md:m-8">
            <showcase :categories='@json($techCategories)' :projects='@json($projects)'></showcase>
        </div>

        <div class="mx-auto">
            <div class="flex flex-col items-center gap-4 md:flex-row md:items-center md:justify-between">
                <div class="w-full md:max-w-1/3 text-xs text-gray-400 text-center md:flex-1 md:text-left">
                    <span class="italic">"{{ $projects['testimonial']['quote'] ?? '' }}"</span>
                    <br>
                    <span class="text-gray-500 font-medium not-italic">{{ $projects['testimonial']['author'] ?? '' }}</span>
                </div>
                <a href="#contact" class="inline-block mt-2 self-center whitespace-nowrap text-2xl leading-none shadow bg-brand-700 hover:bg-brand-600 text-white rounded-sm p-4 shrink-0 sm:text-3xl md:mt-0 md:ml-6 md:text-5xl">
                    <i class="fa fa-paper-plane"></i> Contact Me
                </a>
            </div>
        </div>
    </div>
@endsection
