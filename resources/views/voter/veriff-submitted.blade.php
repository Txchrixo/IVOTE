@extends('templates.voter')

@section('title', __('page.veriffSubmitted.title'))

@section('content')
    <div class="d-flex h-100 flex-shrink-1">
        <div class="bf d-flex flex-column w-50 flex-grow-1 align-items-center justify-content-center pt-3"
            style="background-image: url({{ Vite::image('makeYourVoteStick.png') }}), linear-gradient(rgba(255, 255, 255), rgba(255, 255, 255)); background-size: 40%; background-repeat: no-repeat; background-position: top 15px right 15px; background-blend-mode: initial;">
            <div class="d-flex h-100 w-100  flex-grow-1 align-items-start p-4"
                style="font-family: 'Helvetica Rounded', sans-serif;">
                <h1 id="text">@lang('page.veriffSubmitted.main-text')</h1>
            </div>
            <div class="d-flex mt-auto p-3 align-self-start logo">
                <a href="{{ route('voter.veriffSubmitted') }}"><img
                        src="{{ Vite::image('ivote-red.png') }}" alt="ivote logo with red color" height="40" width="auto"></a>
            </div>
        </div>
    </div>
@endsection
