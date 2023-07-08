@extends('templates.app')

@section('content')
    <form id='candidateForm' method="post">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-headline rounded-4">
                    <h3 class="panel-heading">Candidate Photo</h3>
                    <div class="panel-body">
                        <div class="form-group d-flex justify-content-center align-items-center">
                            <img src="{{ Vite::image('avatar.png') }}" width="80%" height="auto">
                        </div>
                        <div class="form-group">
                            <input type="file" name="photo" id="photo"
                                class="form-control p-3 @error('photo') is-invalid @enderror rounded-3 mt-2">
                            @error('photo')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- <div class="button-right"> --}}
                        {{-- <button type="button"  class="btn btn-info btn-upload">Upload</button> --}}
                        {{-- </div> --}}
                    </div>

                </div>
            </div>


            <div class="col-md-8">
                <div class="panel panel-headline rounded-4">
                    <div class="panel-heading">
                        <h3 class="panel-title">Candidate Registration</h3>
                    </div>
                    <div class="panel-body">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-block">{{ $message }}</div>
                        @endif
                        <div class="form-group my-3">
                            <label for="name" class="mb-1">Candidate Full Name</label>
                            <div class="input-group rounded-3 p-1">
                                <span
                                    class="input-group-addon p-3 d-flex justify-content-center align-items-center @error('name') is-invalid @enderror"
                                    style="border-bottom-left-radius: 0.5rem; border-top-left-radius: 0.5rem;"><i
                                        class="fa fa-user"></i></span>
                                <input class="form-control input-name p-3 @error('name') is-invalid @enderror "
                                    id="name" placeholder="Yifan" name="name" type="text"
                                    style="border-bottom-right-radius: 0.5rem; border-top-right-radius: 0.5rem;">

                            </div>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group my-3">
                            <label for="cni" class="mb-1">CNI Number</label>
                            <input class="form-control input-name rounded-2 p-3 @error('cni') is-invalid @enderror "
                                placeholder="5564999994994" id="cni" name="cni" type="text">
                            @error('cni')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group my-3">
                            <label for="des" class="mb-1">Description</label>
                            <textarea name="des" id="des" class="form-control input-des rounded-3 p-3 @error('des') is-invalid @enderror "
                                placeholder="Candidate short description" rows="5"></textarea>
                            @error('des')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group my-3">
                            <label for="vote_id" class="mb-1">Vote For</label>
                            <select class="form-control vote-id rounded-3 p-3 @error('vote_id') is-invalid @enderror"
                                name="vote_id" id="vote_id">
                                @foreach ($data as $item)
                                    <option value="{{ $item['id'] }}">{{ $item['title'] }}</option>
                                @endforeach
                                @empty($data)
                                    <option>Please add a vote first</option>
                                @endempty
                            </select>
                            @error('vote_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="button-right">
                            <input type="submit" value="Create"
                                class="btn btn-success btn-add-candidate rounded-3 p-2 px-5">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
@endsection
