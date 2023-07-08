@push('css')
    <style>
        .lang-text {
            font-family: 'Helvetica', sans-serif !important;
        }

        .not-selected:hover {
            transform: scale(1.1);
        }
    </style>
@endpush
@foreach ($available_locales as $locale_name => $available_locale)
    @if ($available_locale === 'en')
        <span class="fi fi-us"></span>
    @else
        <span class="fi fi-fr"></span>
    @endif
    @if ($available_locale === $current_locale)
        <span class="lang-text ms-2 me-2 text-gray-700">{{ $locale_name }}</span>
    @else
        <a class="ms-1 text-decoration-none ms-2 me-2 not-selected" href="language/{{ $available_locale }}">
            <span class="lang-text">{{ $locale_name }}</span>
        </a>
    @endif
@endforeach
