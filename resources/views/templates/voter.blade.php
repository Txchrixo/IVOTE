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
<body class="page-bg">
    <div class="container-fluid min-vh-100">
        <div class="row">
            <!-- Contenu de la première div, taille du nav -->
            <div class="d-flex justify-content-center pt-8 justify-content-sm-start bg-light p-1 align-items-center">
                @include('templates.partials.language_switcher')
            </div>
        </div>
        <div class="row main-content">
            <div class="container-fluid bg-white rounded-4 p-3 d-flex flex-column">
                @yield('content')
            </div>
        </div>
    </div>


    @if (Route::currentRouteName() == \App\Enums\RouteApp::$veriff)
        <script src='https://cdn.veriff.me/sdk/js/1.5/veriff.min.js'></script>
        <script src='https://cdn.veriff.me/incontext/js/v1/veriff.js'></script>
        <script type="module" src="{{ Vite::asset('resources/js/scripts/veriff.js') }}"></script>
    @endif
    @if (Route::currentRouteName() == \App\Enums\RouteApp::$vote)
        <script type="module" src="{{ Vite::asset('resources/js/scripts/vote.js') }}"></script>
    @endif
    @if (Route::currentRouteName() == \App\Enums\RouteApp::$tallyHome)
        <script type="module" src="{{ Vite::asset('resources/js/scripts/tally-home.js') }}"></script>
    @endif

    @stack('scripts')

</body>

</html>
