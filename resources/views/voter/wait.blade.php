@extends('templates.voter')

@section('title', 'Voting status')

@section('content')
    <h1 class="h3">Voting status</h1>
    @if ($election)
        <h2 class="h5">{{ $election->title }}</h2>
        <p>Election stage: {{ $election->currentStage()->label() }}</p>
        <label for="registered-public-key" class="form-label">Public key saved in this browser</label>
        <input id="registered-public-key" class="form-control public-key-area mb-3" data-voter-election="{{ $election->id }}" readonly>

        @if ($election->currentStage() === \App\Enums\ElectionStage::Ongoing)
            <div><a class="btn btn-primary" href="{{ route('voter.vote', ['election' => $election->id]) }}">Open ballot</a></div>
        @elseif ($election->currentStage() === \App\Enums\ElectionStage::Done)
            <div><a class="btn btn-primary" href="{{ route('verify.now', ['vote_item' => $election->id]) }}">Verify a ballot receipt</a></div>
        @else
            <p>Return to this link when the voting stage opens.</p>
        @endif
    @else
        <div><a class="btn btn-primary" href="{{ route('voter.vote') }}">Find an open ballot</a></div>
    @endif
@endsection
