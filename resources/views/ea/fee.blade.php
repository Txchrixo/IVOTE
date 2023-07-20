@extends('templates.app')

@section('content')
    <input type="hidden" class="bitcoin-address" value="{{ $user['bitcoin_address'] }}">

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-headline" style="border-radius: 15px;">
                <div class="panel-heading">
                    <h3 class="panel-title">Balance List</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-btc"></i></span>
                                <p>
                                    <span class="number number-btc">Loading...</span>
                                    <span class="title">Balance</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-globe"></i></span>
                                <p>
                                    <span class="number number-network">Loading..</span>
                                    <span class="title">Network</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-btc"></i></span>
                                <p>
                                    <span class="number number-unspent">Loading...</span>
                                    <span class="title">Unspent TX</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-globe"></i></span>
                                <p>
                                    <span class="number number-addresses">Loading..</span>
                                    <span class="title">Addresses</span>
                                </p>
                            </div>
                        </div>
                        <div class="row my-4 mx-0">
                            <div class="col-md-10 pb-2">
                                <div class="form-group">
                                    <select class="form-control input-election p-3 rounded-2" name="election_id" id="election_id">
                                        @foreach ($data as $item)
                                            <option value="{{ $item['id'] }}">{{ $item['title'] }}</option>
                                        @endforeach
                                        @if (empty($data))
                                            <option>Please add an Election first</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 pb-2 d-flex justify-content-center">
                                <button class="btn btn-primary btn-pay-now p-3 rounded-2 flex-grow-1" type="button">PayNow</button>
                            </div>
                        </div>

                    </div>
                    <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Bitcoin Addresss</th>
                                <th>Transaction ID</th>
                                <th>Operation</th>
                            </tr>
                        </thead>
                        <tbody class="table-fee">
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
