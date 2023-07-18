@extends('templates.public')

@section('title', __('Set up two-factor authentication'))
@section('description', __('Two-factor authentication is required for this account'))

@section('content')
    <div class="left">
        <div class="content">
            <div class="header">
                <p class="lead">@yield('description')</p>
            </div>
            <ol class="text-start small">
                <li>@lang('Scan this QR code with an authenticator app (Aegis, Google Authenticator, 1Password...).')</li>
                <li>@lang('Enter the 6-digit code shown by the app to confirm.')</li>
            </ol>
            {{-- SVG generated server-side by bacon/bacon-qr-code from the otpauth URL --}}
            <div class="d-flex justify-content-center my-3">{!! $qrCode !!}</div>
            <p class="small text-break">@lang('Setup key:') <code>{{ $secret }}</code></p>
            <form class="form-auth-small" action="{{ route('auth.two-factor.postSetup') }}" method="post">
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
                <button type="submit" class="btn btn-primary btn-lg btn-block btn-login">@lang('Confirm')</button>
            </form>
        </div>
    </div>
    <div class="right">
        <div class="overlay"></div>
    </div>
    <div class="clearfix"></div>
@endsection
