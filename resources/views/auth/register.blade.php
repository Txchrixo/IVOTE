@extends('templates.public')

@section('title', __('form.type.register'))
@section('description', __('Create your account'))

@section('content')
    <div class="left float-end">
        <div class="content">
            <div class="header">
                <p class="lead">@yield('description')</p>
            </div>
            <form class="form-auth-small" action="{{ route('auth.postRegister') }}" method="post">
                @csrf
                <div class="form-group mb-2">
                    <input type="input" class="form-control login-input  @error('username') mb-0 is-invalid @enderror"
                        name="username" value="{{ old('email') }}" placeholder="@lang('form.input.username')">
                    @error('username')
                        <span class="text-white p-1 m-3" style="background-color: #DD090B">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mt-2">
                    <input type="password" class="form-control login-input @error('password') mb-0 is-invalid @enderror"
                        name="password" value="{{ old('password') }}" placeholder="@lang('form.input.password')">
                    @error('password')
                    <span class="text-white p-1" style="background-color: #DD090B">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary btn-lg btn-block btn-login">@lang('form.btn.register')</button>
            </form>
        </div>
    </div>
    <div class="right float-start">
        <div class="overlay"></div>
        <div class="content text">
            <h1 class="heading">@lang('The Web 3.0 Generation of Voting')</h1>
            <p>@lang('Based on Blockchain')</p>
        </div>
    </div>
@endsection
