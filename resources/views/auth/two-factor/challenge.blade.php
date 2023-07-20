@extends('templates.public')

@section('title', __('Two-factor authentication'))
@section('description', __('Verification code'))

@section('content')
    <div class="left">
        <div class="content">
            <div class="header">
                <p class="lead">@yield('description')</p>
            </div>
            <p class="auth-help">@lang('Enter the 6-digit code from your authenticator app.')</p>
            <form class="form-auth-small" action="{{ route('auth.two-factor.postChallenge') }}" method="post">
                @csrf
                @include('templates.partials.flash')
                <div class="form-group mb-2">
                    <input type="text" class="form-control login-input @error('code') mb-0 is-invalid @enderror"
                        name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="10" autofocus
                        placeholder="123456" aria-label="@lang('Authentication code')">
                    @error('code')
                        <span class="text-white p-1 d-inline-block mt-1" style="background-color: #DD090B">{{ $message }}</span>
                    @enderror
                </div>
                <details class="mt-3 mb-2">
                    <summary>@lang('Use a recovery code')</summary>
                    <input type="text" class="form-control login-input mt-2" name="recovery_code" autocomplete="off"
                        maxlength="32" placeholder="XXXXX-XXXXX" aria-label="@lang('Recovery code')">
                </details>
                <button type="submit" class="btn btn-primary btn-lg btn-block btn-login w-100">@lang('form.btn.login')</button>
            </form>
        </div>
    </div>
    <div class="right">
        <div class="overlay"></div>
    </div>
@endsection
