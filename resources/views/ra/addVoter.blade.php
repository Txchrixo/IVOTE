@extends('templates.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-headline rounded-4">
                <div class="panel-heading h3 mb-0">Voter Registration</div>
                <div class="panel-body">
                    <div class="form-group my-3">
                        <label for="firstname" class="mb-1">First Name</label>
                        <div class="input-group rounded-3 p-1">
                            <span class="input-group-addon p-3 d-flex justify-content-center align-items-center" style="border-bottom-left-radius: 0.5rem; border-top-left-radius: 0.5rem;"><i class="fa fa-user"></i></span>
                            <input id="firstname" class="form-control input-name p-3" placeholder="Yifan" name="firstname" type="text" style="border-bottom-right-radius: 0.5rem; border-top-right-radius: 0.5rem;">
                        </div>
                    </div>

                    <div class="form-group my-3">
                        <label for="lastname" class="mb-1">Last Name</label>
                        <div class="input-group rounded-3 p-1">
                            <span class="input-group-addon p-3 d-flex justify-content-center align-items-center" style="border-bottom-left-radius: 0.5rem; border-top-left-radius: 0.5rem;"><i class="fa fa-user"></i></span>
                            <input id="lastname" class="form-control input-name p-3" placeholder="Wu" name="lastname" type="text" style="border-bottom-right-radius: 0.5rem; border-top-right-radius: 0.5rem;">
                        </div>
                    </div>
                    <div class="form-group my-3">
                        <label for="email" class="mb-1">Email</label>
                        <div class="input-group rounded-3 p-1">
                            <span class="input-group-addon p-3 d-flex justify-content-center align-items-center" style="border-bottom-left-radius: 0.5rem; border-top-left-radius: 0.5rem;"><i class="fa fa-envelope""></i></span>
                            <input id="email" class="form-control voter-email p-3" placeholder="voter@gmail.com" name="email" type="text" style="border-bottom-right-radius: 0.5rem; border-top-right-radius: 0.5rem;">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="vote_id">Vote For</label>
                        <select class="form-control vote-id p-3 rounded-3" name="vote_id" id="vote_id">
                            @foreach ($data as $item)
                                <option value="{{ $item['id'] }}">{{ $item['title'] }}</option>
                            @endforeach
                            @empty($data)
                                <option>Please add a vote first</option>
                            @endempty
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-email p-2 px-5 rounded-3">Send An Email</button>
                </div>
            </div>
        </div>
    </div>
@endsection
