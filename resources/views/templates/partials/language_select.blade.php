{{-- Floating language picker of the authentication pages (behaviour in resources/js/index.js) --}}
<div class="lang-float">
    <label for="locale-select" class="visually-hidden">@lang('Language')</label>
    <select id="locale-select" class="lang-select" data-locale-switcher>
        @foreach ($available_locales as $locale_name => $available_locale)
            <option value="{{ url('language/' . $available_locale) }}" @selected($available_locale === $current_locale)>
                {{ $locale_name }}
            </option>
        @endforeach
    </select>
</div>
