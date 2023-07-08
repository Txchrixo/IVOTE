@extends('templates.public')

@section('title', 'Home')

@section('content')
    <form class="form-auth-small" action="" method="post">
        @csrf
        <div class="form-group">
            <label for="username" class="control-label sr-only">@lang('form.input.username')</label>
            <input type="input" class="form-control  @error('email') is-invalid @enderror" id="username" name="username"
                value="username" placeholder="username">
            @error('email')
                <span class="hel-block alert alert-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="password" class="control-label sr-only">@lang('form.input.password')</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                name="password" value="password" placeholder="Password">

            @error('password')
                <span class="hel-block alert alert-danger">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary btn-lg btn-block">@lang('form.btn.login')</button>
    </form>
@endsection

