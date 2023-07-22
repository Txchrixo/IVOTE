@extends('templates.app')

@section('content')
    <div class="row d-flex justify-content-center align-items-center">
        <div class="col-md-5 col-md-offset-3">
            <div class="panel panel-headline" style="border-radius: 15px;">
                <h2 class="panel-heading">Change Password</h2>
                <div class="panel-body">
                    <form action="{{ route('auth.password.postChange') }}" method="post">
                        @csrf
                        <div class="form-group mb-2">
                            <label for="password_old">Current Password</label>
                            <input type="password" name="password_old" id="password_old" autocomplete="current-password"
                                class="form-control mt-1 rounded-2 @error('password_old') mb-0 is-invalid @enderror">
                            @error('password_old')
                                <span class="text-white p-1" style="background-color: #DD090B">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mt-2">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" autocomplete="new-password"
                                class="form-control mt-1 rounded-2 @error('password') mb-0 is-invalid @enderror">
                            @error('password')
                                <span class="text-white p-1" style="background-color: #DD090B">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mt-2">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                                class="form-control mt-1 rounded-2">
                        </div>
                        <div class="button-right mt-3">
                            <button type="submit" class="btn btn-primary rounded-2 p-2 px-3">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('templates.partials.footer')
@endsection
