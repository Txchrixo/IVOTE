@extends('templates.voter')

@section('title', $election->title)

@section('content')
    <h1 class="h3">{{ $election->title }}</h1>
    <p>Use the code issued by the registration authority to register your voting key.</p>
    <div>
        <a class="btn btn-primary" href="{{ route('voter.generateKeys', ['election' => $election->id]) }}">Register to vote</a>
    </div>
@endsection
