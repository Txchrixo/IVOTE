@extends('templates.voter')

@section('title', 'Election results')

@section('content')
    <h1 class="h3">{{ $election->title }}</h1>
    @if ($candidates)
        <table class="table">
            <thead><tr><th scope="col">Candidate</th><th scope="col">Counted ballots</th></tr></thead>
            <tbody>
                @foreach ($candidates as $candidate)
                    <tr><th scope="row">{{ $candidate['name'] }}</th><td>{{ $counts[$candidate['id']] ?? 0 }}</td></tr>
                @endforeach
            </tbody>
        </table>
        @if ($pending)
            <p>Ballots awaiting Bitcoin confirmation: {{ $pending }}</p>
        @endif
        <div class="d-flex gap-3">
            <a href="{{ route('voter.ballots.ledger', $election) }}">Download public ballot data</a>
            <a href="{{ route('verify.now', ['vote_item' => $election->id]) }}">Verify a ballot receipt</a>
        </div>
    @else
        <p>No recorded ballot data is available for this election.</p>
    @endif
@endsection
