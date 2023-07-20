@extends('templates.public')

@section('title', __('Recovery codes'))
@section('description', __('Recovery codes'))

@section('content')
    <div class="left">
        <div class="content">
            <div class="header">
                <p class="lead">@yield('description')</p>
            </div>
            <p class="auth-help">@lang('Save these codes somewhere safe. Each one can be used once if you lose access to your authenticator app. They will not be shown again.')</p>
            <ul class="recovery-codes">
                @foreach ($codes as $code)
                    <li>{{ $code }}</li>
                @endforeach
            </ul>
            <a href="{{ route('auth.dashboard') }}" class="btn btn-primary btn-lg btn-block btn-login w-100">@lang('Continue')</a>
        </div>
    </div>
    <div class="right">
        <div class="overlay"></div>
    </div>
@endsection
