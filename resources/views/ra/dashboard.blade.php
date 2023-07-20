@extends('templates.app')

@section('content')
    <input type="hidden" class="bitcoin-address" value="{{ $data['bitcoin_address'] }}">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-headline rounded-4">
                <div class="panel-heading">
                    <h3 class="panel-title">Overview</h3>
                    <p class="panel-subtitle">Welcome back, Registration Authority</p>
                </div>
                <div class="panel-body">
                    <div class="row">

                        <div class="col-lg-6 pt-2">
                            <div class="metric" style="border-radius: 15px;">
                                <div class="d-flex align-items-center gap-3 p-2">
                                    <img src="{{ Vite::image('bitcoin.png') }}" alt="" class="metric-icon">
                                    <div class="d-flex flex-column w-100 metric-body">
                                        <p class="d-flex flex-column align-items-end justify-content-center gap-2">
                                            <span class="number number-eaaddress">EA address</span>
                                            <span class="title text-break"> {{ $data['ea_address'] }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 pt-2">
                            <div class="metric" style="border-radius: 15px;">
                                <div class="d-flex align-items-center gap-3 p-2">
                                    <img src="{{ Vite::image('bitcoin.png') }}" alt="" class="metric-icon">
                                    <div class="d-flex flex-column w-100 metric-body">
                                        <p class="d-flex flex-column align-items-end justify-content-center gap-2">
                                            <span class="number">Voter numbers</span>
                                            <span class="title text-break">{{ $data['voters'] }}</span>
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Manage Key modal
    <div class="modal fade modal-manage-key" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Keys Pool</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Voters Numbers</th>
                                <th>Created Time</th>
                                <th>Operation</th>
                            </tr>
                        </thead>
                        <tbody>
                      
                            @empty($data)
                                <tr>
                                    <td>No data</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endempty
                        </tbody>
                    </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div> -->
@endsection
