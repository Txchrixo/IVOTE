@extends('templates.voter')

@section('title', __('page.veriff.title', ['election' => $election]))

@section('content')

    <input data-lang-input="@lang('page.veriff.form.input')" data-lang-btn="@lang('page.veriff.form.button')" data-redirect="{{ route('voter.veriffSubmitted') }}"
        hidden>
    <div class="d-flex h-100 flex-shrink-1">
        <div class="bf d-flex flex-column w-50 flex-grow-1 align-items-center justify-content-center pt-3"
            style="background-image: url({{ Vite::image('goodToVote.png') }}), linear-gradient(rgba(255, 255, 255), rgba(255, 255, 255)); background-size: 40%; background-repeat: no-repeat; background-position: top 15px right 15px; background-blend-mode: initial;">
            <div class="d-flex h-100 w-100  flex-grow-1 align-items-start p-4"
                style="font-family: 'Helvetica Rounded', sans-serif;">
                <h1 id="text">@lang('page.veriff.main-text', ['election' => $election])</h1>
            </div>
            <div class="d-flex flex-column justify-content-center align-items-center mt-auto position-absolute"
                style="top: 60%; left: 50%; transform: translate(-50%, -50%);">
                <img class="up-arrow position-fixed mt-5" width="65%" height="auto"
                    src="{{ Vite::image('up-arrow.png') }}" alt="up arrow pointing input" style="right: 95%">
                <div id="veriff-root"></div>
                <!--
                <form class="d-flex flex-column">
                    @csrf
                    <input class="email p-3 rounded-4 border w-100" type="email" name="email"
                        placeholder="@lang('page.veriff.form.input')" size="30" required>
                    <input class="get-verified p-3 rounded-4 border w-100 mt-2 text-white" type="submit"
                        value="@lang('page.veriff.form.button')" style="background-color: #7311F6; font-family: 'Helvetica';">
                </form>
                -->
            </div>
            <div class="d-flex mt-auto p-3 align-self-start logo">
                <a href="{{ route('voter.veriff', ['election' => $election]) }}"><img
                        src="{{ Vite::image('ivote-red.png') }}" alt="ivote logo with red color" height="40" width="auto"></a>
            </div>
        </div>
    </div>
@endsection
