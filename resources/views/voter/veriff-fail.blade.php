@extends('templates.voter')

@section('title', __('page.veriff.veriffFail'))

@section('content')
    <div class="d-flex h-100 flex-shrink-1 justify-content-center align-items-center">
            <div class="d-flex flex-column align-items-center justify-content-center p-3 logo text-center gap-5 w-100">
                <a href="{{ route('voter.veriff', ['election' => $election]) }}"><img
                        src="{{ Vite::image('ivote-red.png') }}" alt="ivote logo with red color" height="40" width="auto"></a>
                        <h1 id="text text-center w-100">@lang('page.veriffFail.main-text', ['election' => $election])</h1>
            </div>
      
    </div>
@endsection
