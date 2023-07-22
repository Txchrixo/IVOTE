@extends('templates.public')

@section('title', __('form.type.register'))
@section('description', __('New registration authority'))

@section('content')
    <div class="left">
        <div class="content">
            <div class="header">
                <p class="lead">@yield('description')</p>
            </div>
            <p class="auth-help">@lang('The password needs at least 12 characters with upper and lower case letters, a number and a symbol.')</p>
            <form class="form-auth-small" action="{{ route('auth.postRegister') }}" method="post">
                @csrf
                @include('templates.partials.flash')
                <div class="form-group mb-2">
                    <input type="input" class="form-control login-input  @error('username') mb-0 is-invalid @enderror"
                        name="username" value="{{ old('username') }}" placeholder="@lang('form.input.username')">
                    @error('username')
                        <span class="text-white p-1 d-inline-block mt-1" style="background-color: #DD090B">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mt-2">
                    <input type="password" class="form-control login-input @error('password') mb-0 is-invalid @enderror"
                        name="password" autocomplete="new-password" placeholder="@lang('form.input.password')">
                    @error('password')
                    <span class="text-white p-1 d-inline-block mt-1" style="background-color: #DD090B">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mt-2">
                    <input type="password" class="form-control login-input"
                        name="password_confirmation" autocomplete="new-password" placeholder="@lang('form.input.password') (confirmation)">
                </div>
                <button type="submit" class="btn btn-primary btn-lg btn-block btn-login">@lang('form.btn.register')</button>
            </form>
            <div class="auth-back">
            <a href="{{ route('ea.dashboard') }}" class="auth-link">
                <svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true"><path d="M9 2L4 7l5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                @lang('Back to dashboard')
            </a>
            </div>
        </div>
    </div>
    @include('auth.partials.brand-panel', [
        'heading' => __('Build your election team.'),
        'text' => __('Registration authorities manage candidates and voters.'),
    ])
@endsection
