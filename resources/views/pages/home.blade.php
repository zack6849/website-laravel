@extends('layouts.app')

@section('title', 'Portfolio')

@push('scripts')
    @vite('resources/js/vue.js')
@endpush
@section('content')
    @php
        $backgroundPosition = $background['position'] ?? ['x' => '50%', 'y' => '34%'];
    @endphp
    <div class="w-full">
        <div
            class="mx-auto flex flex-col gap-4 lg:flex-row lg:items-start"
            id="top-banner-container"
            style="--top-banner-bg-url: url('{{ $background['url'] }}'); --top-banner-bg-x: {{ $backgroundPosition['x'] ?? '50%' }}; --top-banner-bg-y: {{ $backgroundPosition['y'] ?? '34%' }}; --top-banner-overlay-opacity: {{ $background['overlay'] ?? 0.68 }}; --top-banner-bg-size: {{ $background['size'] ?? 'cover' }};"
        >
            <div class="profile-summary mb-2 flex max-w-full flex-col items-start gap-4 sm:flex-row sm:items-center sm:gap-6 lg:flex-1">
                <div class="profile-img-container shrink-0">
                    <img alt="Zachary Craig" src="{{asset('img/profile.jpg')}}" class="profile-image w-24 rounded-full sm:w-28 lg:w-40">
                </div>
                <div class="min-w-0 max-w-xl">
                    <div class="business-card">
                        <h1 class="font-heavy text-4xl leading-tight sm:text-5xl">Zachary Craig</h1>
                        <h2>Senior Full-Stack Engineer - Laravel / PHP</h2>
                        <h3>St. Petersburg, Florida</h3>
                        <p>
                            Outside of work, I enjoy tinkering with electronics and photography.
                        </p>
                        <b>Nice to meet you.</b>
                    </div>
                </div>
            </div>
            <div id="contact" class="w-full lg:mx-auto lg:w-auto" aria-labelledby="contact-heading">
                <h2 id="contact-heading" tabindex="-1" class="flex-row">
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
            @if(!empty($background['description']))
                <div class="background-info">
                    <button
                        type="button"
                        class="background-info-button"
                        aria-label="Background image details"
                        aria-describedby="background-info-description"
                    >
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                    </button>
                    <div id="background-info-description" class="background-info-tooltip" role="tooltip">
                        @if(!empty($background['title']))
                            <strong>{{ $background['title'] }}</strong>
                        @endif
                        <span>{{ $background['description'] }}</span>
                    </div>
                </div>
            @endif
        </div>
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
@push('styles')
    <style>
        #top-banner-container {
            --top-banner-bg-url: url('{{ asset('img/bg/pier_night.jpg') }}');
            --top-banner-bg-x: 50%;
            --top-banner-bg-y: 34%;
            --top-banner-overlay-opacity: 0.68;
            --top-banner-bg-size: cover;

            box-sizing: border-box;
            width: 100vw;
            max-width: none;
            min-height: 33vh;
            min-height: 33svh;
            margin-inline: calc(50% - 50vw);
            padding-inline: 1rem;
            padding-block: clamp(1.5rem, 5svh, 4rem);
            padding-bottom: clamp(2rem, 2svh, 5rem);
            overflow: hidden;
            color: #f8fafc;
            background-color: #020617;
            background-image:
                linear-gradient(
                    rgba(2, 6, 23, var(--top-banner-overlay-opacity)),
                    rgba(2, 6, 23, var(--top-banner-overlay-opacity))
                ),
                var(--top-banner-bg-url);
            background-attachment:
                scroll,
                fixed;
            background-position:
                center,
                var(--top-banner-bg-x) var(--top-banner-bg-y);
            background-repeat: no-repeat;
            background-size:
                100% 100%,
                var(--top-banner-bg-size);
        }

        #top-banner-container .business-card,
        #top-banner-container #contact-heading {
            text-shadow: 0 1px 2px rgba(2, 6, 23, 0.72);
        }

        .background-info {
            position: relative;
            display: flex;
            align-self: flex-end;
            flex: 0 0 auto;
            margin-top: auto;
            margin-left: auto;
        }

        .background-info-button {
            position: relative;
            z-index: 2;
            display: inline-flex;
            width: 2rem;
            height: 2rem;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 9999px;
            color: #f8fafc;
            background: rgba(2, 6, 23, 0.56);
            box-shadow: 0 10px 24px rgba(2, 6, 23, 0.26);
        }

        .background-info-button:focus-visible {
            outline: 2px solid #99f6e4;
            outline-offset: 3px;
        }

        .background-info-tooltip {
            position: absolute;
            right: calc(100% + 0.5rem);
            bottom: 0;
            width: min(18rem, calc(100vw - 4.5rem));
            padding: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 0.5rem;
            color: #f8fafc;
            background: rgba(2, 6, 23, 0.92);
            box-shadow: 0 18px 44px rgba(2, 6, 23, 0.34);
            font-size: 0.875rem;
            line-height: 1.4;
            opacity: 0;
            pointer-events: none;
            transform: translateX(0.25rem);
            transition: opacity 150ms ease, transform 150ms ease;
        }

        .background-info:hover .background-info-tooltip,
        .background-info:focus-within .background-info-tooltip {
            opacity: 1;
            pointer-events: auto;
            transform: translateX(0);
        }

        .background-info-tooltip strong {
            display: block;
            margin-bottom: 0.25rem;
            color: #99f6e4;
            font-size: 0.8rem;
        }

        @media (min-width: 640px) {
            #top-banner-container {
                padding-inline: 1.5rem;
            }
        }

        @media (min-width: 1024px) {
            #top-banner-container {
                padding-inline: 2rem;
            }
        }
    </style>
@endpush()
