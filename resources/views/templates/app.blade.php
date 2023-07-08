<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>@yield('title')</title>

    <meta charset="utf-8">
    <base href="{{ asset('') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/main.css') }}">
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/admin.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css" />
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="76x76" href="{{ Vite::image('apple-icon.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ Vite::image('favicon.png') }}">
</head>

<body>
    <div id="wrapper">
        @include('templates.partials.header')
        @include('templates.partials.navigation')
        <div class="main">
            <!-- MAIN CONTENT -->
            <div class="main-content">
                <div class="container-fluid">
                    @include('templates.partials.flash')
                    @yield('content')
                </div>
            </div>
            <!-- END MAIN CONTENT -->
        </div>
    </div>


    <!-- Javascript -->
    @if (Route::currentRouteName() == \App\Enums\RouteApp::$raDashboard)
        <script  type="module" src="{{ Vite::asset('resources/js/scripts/ra-dashboard.js') }}"></script>
    @endif

    @if (Route::currentRouteName() == \App\Enums\RouteApp::$raCandidates)
        <script  type="module" src="{{ Vite::asset('resources/js/scripts/ra-candidates.js') }}"></script>
    @endif
    @if (Route::currentRouteName() == \App\Enums\RouteApp::$raAddCandidate)
        <script  type="module" src="{{ Vite::asset('resources/js/scripts/ra-addCandidate.js') }}"></script>
    @endif
    @if (Route::currentRouteName() == \App\Enums\RouteApp::$eaDashboard)
        @vite(['resources/js/scripts/ea-dashboard.js'])
    @endif
    @if (Route::currentRouteName() == \App\Enums\RouteApp::$eaElection)
        <script  type="module" src="{{ Vite::asset('resources/js/scripts/ea-election.js') }}"></script>
    @endif
    @if (Route::currentRouteName() == \App\Enums\RouteApp::$eaAddElection)
        <script  type="module" src="{{ Vite::asset('resources/js/scripts/ea-add-election.js') }}"></script>
    @endif
    @if (Route::currentRouteName() == \App\Enums\RouteApp::$eaBalance)
        <script  type="module" src="{{ Vite::asset('resources/js/scripts/ea-balance.js') }}"></script>
    @endif
    @if (Route::currentRouteName() == \App\Enums\RouteApp::$eaFee)
        <script  type="module" src="{{ Vite::asset('resources/js/scripts/ea-fee.js') }}"></script>
    @endif


</body>

</html>
