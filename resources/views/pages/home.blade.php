@extends('layouts.app')

@section('title', 'Portfolio')

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
                        <h2>Senior Full-Stack Engineer &mdash; Laravel / PHP</h2>
                        <h3>St. Petersburg, Florida</h3>
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
                <div class="my-2 flex flex-wrap justify-evenly gap-2">
                    <a target="_blank"
                       class="btn-social bg-gray-900 hover:bg-black"
                       href="https://github.com/zack6849"
                    >
                        <i class="fab fa-github" aria-hidden="true"></i> GitHub
                    </a>
                    <a target="_blank"
                       class="btn-social bg-blue-700 hover:bg-blue-800"
                       href="https://www.linkedin.com/in/zack6849/"
                    >
                        <i class="fab fa-linkedin" aria-hidden="true"></i> LinkedIn
                    </a>
                    <a target="_blank"
                       class="btn-social bg-orange-600 hover:bg-orange-700"
                       href="https://stackoverflow.com/users/1932789/zack6849"
                    >
                        <i class="fab fa-stack-overflow" aria-hidden="true"></i> StackOverflow
                    </a>
                    <a class="btn-social bg-slate-600 hover:bg-slate-700"
                       href="mailto:{{config('app.contact_email')}}"
                    >
                        <i class="fa fa-envelope" aria-hidden="true"></i> {{config(('app.contact_email'))}}
                    </a>
                    <a href="{{route('radio')}}"
                       title="Amateur radio band"
                       aria-label="Ham radio contact map"
                       class="btn-social bg-green-800 hover:bg-green-900"
                    >
                        <i class="fa-solid fa-tower-cell" aria-hidden="true"></i> 20M
                    </a>
                </div>
            </div>
        </div>
        <div class="md:m-8">
            <showcase :categories='@json($techCategories)' :projects='@json($projects)'></showcase>
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
