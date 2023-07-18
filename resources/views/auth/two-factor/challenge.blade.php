@extends('templates.public')

@section('title', __('Two-factor authentication'))
@section('description', __('Enter the 6-digit code from your authenticator app'))

@section('content')
    <div class="left">
        <div class="content">
            <div class="header">
                <p class="lead">@yield('description')</p>
            </div>
            <form class="form-auth-small" action="{{ route('auth.two-factor.postChallenge') }}" method="post">
                @csrf
                @include('templates.partials.flash')
                <div class="form-group mb-2">
                    <input type="text" class="form-control login-input @error('code') mb-0 is-invalid @enderror"
                        name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="10" autofocus
                        placeholder="123456">
                    @error('code')
                        <span class="text-white p-1 m-3" style="background-color: #DD090B">{{ $message }}</span>
                    @enderror
                </div>
                <details class="mt-2 mb-2">
                    <summary>@lang('Use a recovery code')</summary>
                    <input type="text" class="form-control login-input mt-2" name="recovery_code" autocomplete="off"
                        maxlength="32" placeholder="XXXXX-XXXXX">
                </details>
                <button type="submit" class="btn btn-primary btn-lg btn-block btn-login">@lang('form.btn.login')</button>
            </form>
        </div>
    </div>
    <div class="right">
        <div class="overlay"></div>
    </div>
    <div class="clearfix"></div>
@endsection
