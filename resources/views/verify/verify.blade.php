@extends('templates.voter')

@section('title', 'Verify a ballot receipt')

@section('content')
    <h1 class="h3">{{ $election->title }}</h1>
    @if ($election->ballot_definition)
        <form data-verify-ballot data-receipt-url="{{ route('voter.ballots.receipt', [$election, 'RECEIPT']) }}">
            <label for="receipt" class="form-label">Ballot receipt</label>
            <input id="receipt" class="form-control mb-3" required minlength="64" maxlength="64" pattern="[a-fA-F0-9]{64}">
            <button type="submit" class="btn btn-primary" disabled>Verify signature</button>
            <p class="mt-3" data-verify-status role="status"></p>
        </form>
    @else
        <p>No recorded ballot data is available for this election.</p>
    @endif
@endsection
