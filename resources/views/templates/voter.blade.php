<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>@yield('title')</title>

    <meta charset="utf-8">
    <base href="{{ asset('') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <!--
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/main.css') }}">
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/voter.css') }}">
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/verify.css') }}">
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/tally.css') }}">
    -->
    @if (Route::currentRouteName() == \App\Enums\RouteApp::$veriff)
        <link rel="stylesheet" href="{{ Vite::asset('resources/css/page/veriff.css') }}">
    @endif
    @if (Route::currentRouteName() == \App\Enums\RouteApp::$veriffSubmitted)
        <link rel="stylesheet" href="{{ Vite::asset('resources/css/page/veriffSubmitted.css') }}">
    @endif
    <!-- ICONS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ Vite::image('apple-icon.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ Vite::image('favicon.png') }}">
</head>
<style>
    .main-content {
        padding: 25px;
        height: 100%;
    }

    textarea {
        resize: none;
    }
</style>
@stack('css')
<body class="page-bg voter-body">
    @include('templates.partials.flash')
    <header class="voter-bar">
        <span class="voter-brand">
            <img src="{{ Vite::image('ivote-red.png') }}" alt="IVote" width="101" height="24">
        </span>
        @include('templates.partials.language_switcher')
    </header>
    <main class="voter-main">
        <div class="container-fluid bg-white rounded-4 p-3 d-flex flex-column">
            @yield('content')
        </div>
    </main>


    @if (in_array(Route::currentRouteName(), [\App\Enums\RouteApp::$generateKeys, \App\Enums\RouteApp::$wait]))
        <script type="module" src="{{ Vite::asset('resources/js/scripts/voter-keys.js') }}"></script>
    @endif
    @if (in_array(Route::currentRouteName(), [\App\Enums\RouteApp::$vote, 'verify.now']))
        <script type="module" src="{{ Vite::asset('resources/js/scripts/vote.js') }}"></script>
    @endif
    @if (Route::currentRouteName() == \App\Enums\RouteApp::$tallyHome)
        <script type="module" src="{{ Vite::asset('resources/js/scripts/tally-home.js') }}"></script>
    @endif

    @stack('scripts')

</body>

</html>
