<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · IVote</title>
    @vite(['resources/js/index.js'])
    <link rel="icon" type="image/png" sizes="96x96" href="{{ Vite::image('favicon.png') }}">
</head>

@php
    $signedIn = auth()->check();
    $previous = url()->previous();
    $canGoBack = $previous && $previous !== url()->current() && str_starts_with($previous, url('/'));
@endphp

<body class="page-bg">
    <div id="wrapper">
        <div class="vertical-align-wrap">
            <div class="vertical-align-middle">
                <div class="auth-box auth-box-single">
                    <div class="left">
                        <div class="content">
                            <p class="error-code">@yield('code')</p>
                            <div class="header">
                                <p class="lead">@yield('title')</p>
                            </div>
                            <p class="auth-help">@yield('message')</p>
                            <div class="error-actions">
                                @hasSection('primary-action')
                                    @yield('primary-action')
                                @elseif ($signedIn)
                                    <a href="{{ route('auth.dashboard') }}" class="btn btn-login w-100">@lang('Back to dashboard')</a>
                                @else
                                    <a href="{{ route('auth.login') }}" class="btn btn-login w-100">@lang('Sign in')</a>
                                @endif
                                @if ($canGoBack)
                                    <div class="auth-back">
                                        <a href="{{ $previous }}" class="auth-link">
                                            <svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true"><path d="M9 2L4 7l5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            @lang('Previous page')
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
