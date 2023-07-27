@extends('templates.voter')

@section('title', 'Vote')

@section('content')
    <h1 class="h3">Vote</h1>
    @if (!$election)
        <ul>
            @forelse ($elections as $open)
                <li><a href="{{ route('voter.vote', ['election' => $open->id]) }}">{{ $open->title }}</a></li>
            @empty
                <li>No election is open for voting.</li>
            @endforelse
        </ul>
    @else
        <h2 class="h5">{{ $election->title }}</h2>
        @if (count($definition['ring']) < 2 || count($definition['ring']) > 128)
            <p>This election does not have a supported voter ring.</p>
        @else
            <form data-ballot-form data-submit-url="{{ route('voter.ballots.submit', $election) }}"
                data-reference="{{ $definition['reference'] }}" data-ring="{{ json_encode($definition['ring']) }}">
                <label for="candidate" class="form-label">Candidate</label>
                <select id="candidate" name="candidate" class="form-select mb-3" required>
                    <option value="">Choose a candidate</option>
                    @foreach ($definition['candidates'] as $candidate)
                        <option value="{{ $candidate['id'] }}">{{ $candidate['name'] }}</option>
                    @endforeach
                </select>

                <label for="voting-key" class="form-label">Private voting key</label>
                <input id="voting-key" type="password" class="form-control mb-3" autocomplete="off" required
                    minlength="64" maxlength="64" pattern="[a-fA-F0-9]{64}">

                <button type="submit" class="btn btn-primary" disabled>Sign and submit ballot</button>
                <p class="mt-3" data-ballot-status role="status"></p>
                <div data-ballot-receipt hidden>
                    <label for="ballot-receipt" class="form-label">Keep this receipt</label>
                    <input id="ballot-receipt" class="form-control" readonly>
                    <a href="{{ route('verify.home') }}">Verify after the election closes</a>
                </div>
            </form>
        @endif
    @endif
@endsection
