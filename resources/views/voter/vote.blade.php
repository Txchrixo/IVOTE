@extends('templates.voter')

@section('title', __('page.vote.title'))

@section('content')
    @push('css')
        <style>
            * {
                font-family: 'Helvetica';
            }

            .main-content {
                height: auto;
            }

            .row .container-fluid {
                background-color: transparent !important;
                padding: 0px 10px !important;
            }

            @media (min-width: 768px) {

                .col-md-8 {
                    flex: 0 0 66.3%;
                }

                .col-md-4 {
                    flex: 0 0 32%;
                }
            }

            @media (max-width: 768px) {
                .row {
                    gap: 25px;
                }
            }

            .row {
                justify-content: space-between;
            }

            p {
                margin-bottom: 0.25rem;
            }
        </style>
    @endpush
    <div class="row bg-transparent">
        <!--<input type="hidden" class="eaaddress" value="">-->
        <div class="col-md-8 rounded-4 bg-white">
            <div class="col-12 panel-headline p-3">
                <div class="pb-3">
                    <h3 class="panel-title" style="font-family: 'Helvetica Rounded';"><i class="lnr lnr-inbox"></i></h3>
                    <p class="panel-subtitle" style="font-family: 'Helvetica Light';">@lang('page.vote.left-sub-title')</p>
                </div>
                <div class="pt-1">
                    <div class="row">
                        <input type="hidden" class="vote-candidate" value="" />
                        <div class="list-candidate">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 rounded-4 bg-white">
            <div class="col-12 panel-headline p-3">
                <input type="hidden" class="item_id" value="">
                <div class="pb-3">
                    <h3 class="panel-title" style="font-family: 'Helvetica Rounded';">@lang('page.vote.right-main-title')</h3>
                    <p class="panel-subtitle" style="font-family: 'Helvetica Light';">@lang('page.vote.right-sub-title')<span
                            class="bitcoin-address-numbers"></span></p>
                </div>
                <div class="pt-1">
                    @csrf
                    <p>@lang('page.vote.bitcoinAddress')</p>
                    <select class="form-control bitcoin-address-box rounded-4 p-3" style="background-color: #CED5E1;">
                    </select>
                    <br>
                    <p>@lang('page.vote.privateKey')</p>
                    <textarea class="form-control public-key-area rounded-4 p-3" style="background-color: #CED5E1;" placeholder=""
                        rows="5" readonly></textarea>
                    <br>
                    <p>@lang('page.vote.publicKey')</p>
                    <textarea class="form-control private-key-area rounded-4 p-3" style="background-color: #DBE5EA;" placeholder=""
                        rows="5"></textarea>
                    <br>
                    <div class="d-flex align-items-center justify-content-end">
                        <button type="button" class="btn btn-primary btn-start-vote"  data-bs-toggle="modal" data-bs-target="#processingModal"
                            style="padding: 0.5em 2em; border-radius: 12px;">@lang('page.vote.voteNow-btn')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="processingModal"  data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="processingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-4">
                <div class="modal-header justify-content-center p-4">
                    <h5 class="modal-title" id="processingModalLabel" style="font-family: 'Helvetica Bold', sans-serif;">Processing</h5>
                </div>
                <div class="modal-body">
                    <div class="rounded-4 bg-dark text-white text-center p-3"
                        style="background-color: rgba(0, 0, 0, 0.84);">
                        <p>Your processing content goes here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

