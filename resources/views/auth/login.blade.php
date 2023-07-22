@extends('templates.public')

@section('title', __('form.type.login'))
@section('description', __('Login to your account'))

@section('content')
    <div class="left">
        <div class="content">
            <div class="header">
                <p class="lead">@yield('description')</p>
            </div>
            <form class="form-auth-small" action="{{ route('auth.postLogin') }}" method="post">
                @csrf
                <div class="form-group mb-2">
                    <input type="input" class="form-control login-input @error('username') mb-0 is-invalid @enderror"
                        name="username" value="{{ old('username') }}" placeholder="@lang('form.input.username')">
                    @error('username')
                        <span class="text-white p-1 d-inline-block mt-1" style="background-color: #DD090B">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mt-2">
                    <input type="password" class="form-control login-input @error('password') mb-0 is-invalid @enderror"
                        name="password" value="" placeholder="@lang('form.input.password')">
                    @error('password')
                        <span class="text-white p-1 d-inline-block mt-1" style="background-color: #DD090B">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary btn-lg btn-block btn-login">@lang('form.btn.login')</button>
            </form>
        </div>
    </div>
    @include('auth.partials.brand-panel', [
        'heading' => __('Every vote counts. Every vote checks out.'),
        'text' => __('Anonymous ballots, results anyone can verify.'),
    ])
@endsection

