@extends('templates.app')

@section('content')
    <div class="panel panel-headline rounded-4 p-3">
        <h1 class="h3">Voter Registration</h1>
        <p>Issue one registration code and give it privately to the eligible voter.</p>
        <form action="{{ route('ra.addVoter.post') }}" method="post">
            @csrf
            @include('ra.electionSelect')
            <button type="submit" class="btn btn-primary mt-3">Issue code</button>
        </form>
    </div>
@endsection
