<div class="{{ $frameClasses }}">
    <div
        @if ($fullBleed) id="top-banner-container" @endif
        class="{{ $surfaceClasses }}"
        style="
            --top-banner-bg-url: @if ($baseVariant['url'] !== '') url('{{ $baseVariant['url'] }}') @else none @endif;
            --top-banner-bg-x: {{ $baseVariant['position']['x'] }};
            --top-banner-bg-y: {{ $baseVariant['position']['y'] }};
            --top-banner-bg-size: {{ $baseVariant['size'] }};
            --top-banner-bg-url-sm: @if ($smVariant['url'] !== '') url('{{ $smVariant['url'] }}') @else none @endif;
            --top-banner-bg-x-sm: {{ $smVariant['position']['x'] }};
            --top-banner-bg-y-sm: {{ $smVariant['position']['y'] }};
            --top-banner-bg-size-sm: {{ $smVariant['size'] }};
            --top-banner-bg-url-lg: @if ($lgVariant['url'] !== '') url('{{ $lgVariant['url'] }}') @else none @endif;
            --top-banner-bg-x-lg: {{ $lgVariant['position']['x'] }};
            --top-banner-bg-y-lg: {{ $lgVariant['position']['y'] }};
            --top-banner-bg-size-lg: {{ $lgVariant['size'] }};
            --top-banner-overlay-opacity: {{ $background['overlay'] ?? 0.68 }};
        "
    >
        <div class="top-banner-profile">
            <div class="top-banner-avatar">
                <img alt="Zachary Craig" src="{{ asset('img/profile.jpg') }}" class="top-banner-avatar-image">
            </div>
            <div class="top-banner-copy">
                <div class="business-card">
                    <h1 class="top-banner-title font-heavy">Zachary Craig</h1>
                    <h2>Senior Software Engineer - Backend-Focused Full Stack</h2>
                    <h3>St. Petersburg, Florida</h3>
                    <p>
                        Outside of work, I enjoy tinkering with electronics and photography.
                    </p>
                    <b>Nice to meet you.</b>
                </div>
            </div>
        </div>
        <div id="{{ $contactId }}" class="top-banner-contact" aria-labelledby="{{ $contactHeadingId }}">
            <h2 id="{{ $contactHeadingId }}" tabindex="-1" class="top-banner-contact-heading">
                <span class="text-2xl">&#128205;</span> Places you can find me
            </h2>
            <div class="top-banner-socials">
                <a target="_blank"
                   rel="noopener noreferrer"
                   class="btn-social bg-gray-900 hover:bg-black"
                   href="https://github.com/zack6849"
                >
                    <i class="fab fa-github" aria-hidden="true"></i> GitHub
                </a>
                <a target="_blank"
                   rel="noopener noreferrer"
                   class="btn-social bg-blue-700 hover:bg-blue-800"
                   href="https://www.linkedin.com/in/zack6849/"
                >
                    <i class="fab fa-linkedin" aria-hidden="true"></i> LinkedIn
                </a>
                <a target="_blank"
                   rel="noopener noreferrer"
                   class="btn-social bg-orange-600 hover:bg-orange-700"
                   href="https://stackoverflow.com/users/1932789/zack6849"
                >
                    <i class="fab fa-stack-overflow" aria-hidden="true"></i> StackOverflow
                </a>
                <a class="btn-social bg-slate-600 hover:bg-slate-700"
                   href="mailto:{{ config('app.contact_email') }}"
                >
                    <i class="fa fa-envelope" aria-hidden="true"></i> {{ config(('app.contact_email')) }}
                </a>
                <a href="{{ route('radio') }}"
                   title="Amateur radio band"
                   aria-label="Ham radio contact map"
                   class="btn-social bg-green-800 hover:bg-green-900"
                >
                    <i class="fa-solid fa-tower-cell" aria-hidden="true"></i> 20M
                </a>
            </div>
        </div>
        @if (!empty($background['description']))
            <div class="background-info">
                <button
                    type="button"
                    class="background-info-button"
                    aria-label="Background image details"
                    aria-describedby="{{ $infoDescriptionId }}"
                >
                    <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                </button>
                <div id="{{ $infoDescriptionId }}" class="background-info-tooltip" role="tooltip">
                    @if (!empty($background['title']))
                        <strong>{{ $background['title'] }}</strong>
                    @endif
                    <span>{{ $background['description'] }}</span>
                </div>
            </div>
        @endif
        @if (!$fullBleed && $emptyMessage !== null && $baseVariant['url'] === '')
            <div class="top-banner-empty-message">{{ $emptyMessage }}</div>
        @endif
    </div>
</div>
