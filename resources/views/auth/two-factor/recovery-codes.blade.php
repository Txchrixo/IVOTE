@extends('templates.public')

@section('title', __('Recovery codes'))
@section('description', __('Save these recovery codes somewhere safe'))

@section('content')
    <div class="left">
        <div class="content">
            <div class="header">
                <p class="lead">@yield('description')</p>
            </div>
            <p class="small">@lang('Each code can be used once if you lose access to your authenticator app. They will not be shown again.')</p>
            <ul class="list-unstyled font-monospace">
                @foreach ($codes as $code)
                    <li>{{ $code }}</li>
                @endforeach
            </ul>
            <a href="{{ route('auth.dashboard') }}" class="btn btn-primary btn-lg btn-block btn-login">@lang('Continue')</a>
        </div>
    </div>
    <div class="right">
        <div class="overlay"></div>
    </div>
    <div class="clearfix"></div>
@endsection
