@extends('templates.voter')

@section('content')
    <div class="row">
        <div class="col-md-offset-1 col-md-10 col-md-offset-1 w-100">
            <div class="panel panel-headline">
                <div class="panel-heading">
                    <h3 class="panel-title p-3 pb-0">Step 1 :  Select your Election</h3>
                </div>
                <div class="panel-body">
                    <input type="hidden" class="vote-item" value="NaN">
                    <form action="{{ url('verify.now') }}" method="get">
                        <div class="row p-2">
                            @foreach ($data as $item)
                                <div class="col-md-12 my-2 p-3 rounded-4">
                                    <div class="panel-vote-item" data-id="{{ $item['id'] }}">
                                        <div class="profile-header rounded-4">
                                            <div class="overlay"></div>
                                            <div class="profile-main text-body-emphasis rounded-4 p-4"
                                                id="profile-main-bg"
                                                style='background-image: url('{{ $item->backgroundUrl() }}'); height:150px;'>
                                                <h3 class="name">{{ $item['title'] }}</h3>
                                                <span class="online-status status-available">Done</span>
                                            </div>
                                        </div>
                                        <div class="vote-radio mt-2">
                                            <input class="vote-radio-item" name="vote_item" value="{{ $item['id'] }}" type="radio" style="height: 18px; width: 18px;">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @empty($data)
                                <h3>No Election Ended yet</h3>
                            @endempty

                        </div>
                        <div class="button-right d-flex justify-content-end px-3">
                            <input type="submit" class="btn btn-primary verify-next-step p-2 px-4" value="Next Step" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
