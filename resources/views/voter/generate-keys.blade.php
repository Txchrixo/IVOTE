@extends('templates.voter')

@section('title', 'Register to vote')

@section('content')
    <h1 class="h3">Register to vote</h1>
    @if ($election)
        <h2 class="h5">{{ $election->title }}</h2>
        @if (config('services.veriff.api_key'))
            <form method="post" action="{{ route('voter.identity', $election) }}" class="mb-4">
                @csrf
                <label for="identity-code" class="form-label">Registration code</label>
                <input id="identity-code" name="code" class="form-control" required autocomplete="off" maxlength="64">
                <button type="submit" class="btn btn-outline-primary mt-2">Start identity verification</button>
            </form>
            <p>After Veriff approves your identity, return here and use the same code.</p>
        @endif
        <form method="post" action="{{ route('voter.register', $election) }}" data-registration data-voter-election="{{ $election->id }}">
            @csrf
            <input type="hidden" name="proof">
            <label for="registration-code" class="form-label">Registration code</label>
            <input id="registration-code" name="code" class="form-control mb-3" required autocomplete="off" maxlength="64">
            @error('code') <p class="text-danger">{{ $message }}</p> @enderror

            <button type="button" class="btn btn-outline-primary btn-generate mb-3">Generate voting key</button>
            <label for="voter-public-key" class="form-label d-block">Public key</label>
            <input id="voter-public-key" name="public_key" class="form-control public-key-area mb-3" readonly>
            @error('public_key') <p class="text-danger">{{ $message }}</p> @enderror

            <label for="voter-secret-key" class="form-label">Private key</label>
            <input id="voter-secret-key" class="form-control private-key-area mb-2" readonly autocomplete="off">
            <p>Save this key privately before registering. You will need it to sign your ballot.</p>

            <button type="submit" class="btn btn-primary" disabled>Register key</button>
        </form>
    @else
        <ul>
            @forelse ($elections as $open)
                <li><a href="{{ route('voter.generateKeys', ['election' => $open->id]) }}">{{ $open->title }}</a></li>
            @empty
                <li>No election is accepting registrations.</li>
            @endforelse
        </ul>
    @endif
@endsection
