@extends('templates.voter')

@section('title', __('page.ready.title'))

@section('content')
    @push('css')
        <style>
            .bf {
                transition: ease 0.5s;
            }

            .bf:hover {
                transition: ease 5s;
                background-blend-mode: difference !important;
            }

            div>h2 {
                font-family: 'Helvetica Rounded', sans-serif;
                transition: ease 0.5s;
            }

            div>h2:hover {
                transition: ease 0.5s;
                transform: translateX(5%) scale(1.1);
            }

            .bf>h2:hover {
                transition: ease 0.5s;
                transform: scale(1.1);
            }

            .icon-satisfaction {
                transition: ease 0.5s;
            }

            .icon-satisfaction:hover {
                transition: ease 0.5s;
                transform: scale(1.5);
            }

            .logo {
                transition: ease 0.7s;
            }

            .logo:hover {
                width: auto;
                transform: rotate(360deg);
                transition: ease 0.7s;
            }

            .btn {
                transition: ease 0.7s;
            }

            .btn:hover {
                transition: ease 0.4s;
                transform: scale(1.1);
                background-color: #30C2FD !important;
                border: none;
            }
        </style>
    @endpush
    <div class="d-flex h-100 flex-shrink-1">
        <div class="d-flex flex-column w-50 flex-grow-1 align-items-center justify-content-around bg-white">
            <div class="d-flex w-100 align-items-start m-1" style="font-family: 'Helvetica Rounded', sans-serif;">
                <h2>@lang('page.ready.main-title')</h2>
            </div>
            <div class="bf d-flex flex-column justify-content-around align-items-center mt-auto w-100 h-100 p-4 flex-grow-1 rounded-4 m-1"
                style="background-image: url({{ Vite::image('ready-bg.png') }}), linear-gradient(rgba(255, 255, 255), rgba(255, 255, 255)); background-size: cover; background-repeat: no-repeat; background-blend-mode: initial;">
                <img class="logo" width="150" height="auto" src="{{ Vite::image('logo.svg') }}" alt="logo ivote">
                <img class="icon-satisfaction" width="100" height="auto" src="{{ Vite::image('satisfaction.png') }}"
                    alt="icon satisfaction">
                <h2 class="text-white align-self-center text-center">@lang('page.ready.electionName', ['election' => 'election'])</h1>
            </div>
            <div class="d-flex m-1 align-self-end mt-3">
                <a href="{{ route('voter.generateKeys') }}"><input class="btn btn-primary" type="button"
                        value="@lang('page.ready.text-btn')" style="background-color: #1E98DD; padding : 0.7em;"></a>
            </div>
        </div>
    </div>
@endsection
