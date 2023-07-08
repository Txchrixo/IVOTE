@extends('templates.app')

@section('content')
    <style>
        * {
            font-family: 'Helvetica Light';
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-headline" style="border-radius: 15px;">
                <div class="panel-heading">
                    <h3 class="panel-title">Balance List</h3>
                </div>
                <div class="panel-body">
                    <div class="row my-4">
                        <div class="col-md-10 mb-3">
                            <div class="form-group">
                                <select class="form-control input-election rounded-2 p-3" name="election_id"
                                    id="election_id">
                                    @foreach ($data as $item)
                                        <option value="{{ $item['id'] }}">{{ $item['title'] }}</option>
                                    @endforeach
                                    @empty($data)
                                        <option>Please add an election first</option>
                                    @endempty
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3 d-flex justify-content-center">
                            <button class="btn btn-primary btn-load-all-balance rounded-3 p-3 flex-grow-1"
                                type="button">Load
                                All</button>
                        </div>
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Bitcoin Addresss</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody class="table-balance">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
