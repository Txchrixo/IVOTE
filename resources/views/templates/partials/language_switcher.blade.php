{{-- Auth and voter pages use $floating to pin the picker to the top left. --}}
@php
    $flags = ['en' => 'gb', 'fr' => 'fr'];
    $currentName = array_search($current_locale, $available_locales, true) ?: strtoupper($current_locale);
    $floating = $floating ?? false;
@endphp
<div class="ui-dropdown {{ $floating ? 'lang-float' : '' }}" data-dropdown data-align="{{ $floating ? 'start' : 'end' }}">
    <button type="button" class="ui-dropdown-trigger lang-trigger" aria-haspopup="menu" aria-expanded="false"
        aria-label="@lang('Language'): {{ $currentName }}">
        <span class="fi fi-{{ $flags[$current_locale] ?? $current_locale }} lang-flag" aria-hidden="true"></span>
        <span class="lang-name">{{ $currentName }}</span>
        <svg class="ui-dropdown-chevron" width="12" height="8" viewBox="0 0 12 8" aria-hidden="true">
            <path d="M1 1l5 5 5-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    <ul class="ui-dropdown-menu" role="menu">
        @foreach ($available_locales as $locale_name => $available_locale)
            <li role="none">
                <a role="menuitem" class="ui-dropdown-item" href="{{ url('language/' . $available_locale) }}"
                    hreflang="{{ $available_locale }}" lang="{{ $available_locale }}"
                    @if ($available_locale === $current_locale) aria-current="true" @endif>
                    <span class="fi fi-{{ $flags[$available_locale] ?? $available_locale }} lang-flag" aria-hidden="true"></span>
                    <span class="lang-name">{{ $locale_name }}</span>
                    <svg class="ui-dropdown-check" width="14" height="14" viewBox="0 0 14 14" aria-hidden="true">
                        <path d="M2.5 7.5l3 3 6-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </li>
        @endforeach
    </ul>
</div>
