<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>@yield('title')</title>

    <meta charset="utf-8">
    <base href="{{ asset('') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/js/index.js'])

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css" />
    <!-- GOOGLE FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    <!-- ICONS -->
    <link rel="apple-touch-icon" sizes="76x76" href="{{ Vite::image('apple-icon.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ Vite::image('favicon.png') }}">
</head>

<body
    style="background-image: url('{{ Vite::image('login-bg.png') }}'); background-size: 100% auto; background-size: cover;">
    <div class="d-flex justify-content-center pt-8 justify-content-sm-start bg-light p-1 align-items-center">
        @include('templates.partials.language_switcher')
    </div>
    <div id="wrapper">
        <div class="vertical-align-wrap">
            <div class="vertical-align-middle">
                <!--<div class="logo text-center"><img src="{{ Vite::image('logo-dark.svg') }}" alt="Logo"></div>-->
                <div class="auth-box ">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>

</html>
