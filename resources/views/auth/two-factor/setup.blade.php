@extends('templates.public')

@section('title', __('Set up two-factor authentication'))
@section('description', __('Secure your account'))

@section('content')
    <div class="left">
        <div class="content">
            <div class="header">
                <p class="lead">@yield('description')</p>
            </div>
            <div class="auth-help">
                <p class="mb-2">@lang('Two-factor authentication is required for this account.')</p>
                <ol>
                    <li>@lang('Scan this QR code with an authenticator app (Aegis, Google Authenticator, 1Password...).')</li>
                    <li>@lang('Enter the 6-digit code shown by the app.')</li>
                </ol>
            </div>
            {{-- SVG generated server-side by bacon/bacon-qr-code from the otpauth URL --}}
            <div class="auth-qr">{!! $qrCode !!}</div>
            <p class="auth-help text-center">
                @lang('Cannot scan? Enter this key:')<br>
                <span class="auth-secret">{{ $secret }}</span>
            </p>
            <form class="form-auth-small" action="{{ route('auth.two-factor.postSetup') }}" method="post">
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
                <button type="submit" class="btn btn-primary btn-lg btn-block btn-login w-100">@lang('Confirm')</button>
            </form>
            <form action="{{ route('auth.two-factor.cancel') }}" method="post" class="auth-back">
                @csrf
                <button type="submit" class="auth-link">
                    <svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true"><path d="M9 2L4 7l5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @lang('Back to sign in')
                </button>
            </form>
        </div>
    </div>
    @include('auth.partials.brand-panel', [
        'heading' => __('Lock it down.'),
        'text' => __('Your phone becomes the key to this account.'),
    ])
@endsection
