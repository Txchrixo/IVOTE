@extends('templates.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-headline rounded-4">
                <div class="panel-heading">
                    <h3 class="panel-title">Candidates</h3>
                </div>
                <div class="panel-body">
                    <div class="row my-2">
                        <div class="col-md-10 mb-3">
                            <div class="form-group">
                                <select class="form-control input-election rounded-2 p-3" name="election_id"
                                    id="election_id">
                                    @foreach ($data as $item)
                                        <option value="{{ $item['id'] }}">{{ $item['title'] }}</option>
                                    @endforeach
                                    @empty($data)
                                        <option>Please add a vote first</option>
                                    @endempty
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3 d-flex justify-content-center">
                            <button class="btn btn-primary btn-refresh rounded-3 p-3 flex-grow-1"
                                type="button">Refresh</button>
                        </div>
                    </div>
                    <div class="table-striped">
                        <div class="row box-candidate w-100 text-center h4" role="status">
                            @for ($i = 0; $i < 3; $i++)
                                <div class="col-md-4 py-2" aria-hidden="true"><div class="skeleton-card"></div></div>
                            @endfor
                            <span class="visually-hidden">Loading</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <!-- Manage Candidate -->
    <div class="modal fade" id="candidateModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Manage Candidates</h4>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-4">

                            <div class="form-group">
                                <label for="title">Candidate</label>
                                <img src="{{ Vite::image('avatar.png') }}" width="256">

                            </div>
                            <!--<div class="button-right">
                                    {#<button type="button"  class="btn btn-info btn-upload">Upload</button>
                                </div>-->
                        </div>


                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="title">Candidate Name / Vote Item Name</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <input class="form-control input-name" placeholder="Yifan" name="firstname"
                                        type="text">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control input-des" placeholder="Candidate short description" rows="10"></textarea>
                            </div>
                            <input class="candidate-id" type="hidden" value="">
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success btn-edit-candidate">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection
