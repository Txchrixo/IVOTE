@extends('templates.voter')

@section('title', __('page.wait.title'))

@section('content')
    @push('css')
        <style>
            * {
                font-family: 'Helvetica Light';
            }

            .main-content {
                height: auto;
            }
        </style>
    @endpush
    <div class="row">
        <div class="col-md-offset-1 col-md-10 col-md-offset-1 w-100 px-3 py-0">
            <div class="panel panel-headline">
                <div class="pb-3">
                    <h3 class="panel-title" style="font-family: 'Helvetica Rounded';">@lang('page.wait.main-title')</h3>
                </div>
                <div class="d-flex flex-column justify-content-center align-items-center w-100 h-100 p-4 flex-grow-1 rounded-4"
                    style="background-image: url({{ Vite::image('ready-bg.png') }}), linear-gradient(rgba(255, 255, 255), rgba(255, 255, 255)); background-size: cover; background-repeat: no-repeat; background-blend-mode: initial;">
                    <i class="fa fa-calendar-check-o fa-5x text-white"></i>
                    <h3 class="text-white">@lang('page.wait.vote-date', ['date' => '5, August 2023'])</h3>
                </div>
                <div class="mt-3">
                    <p class="ps-2">@lang('page.wait.main-text')</p>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="mt-1 mb-2" style="font-family: 'Helvetica';">@lang('page.wait.publicKey')</p>
                            <textarea class="form-control public-key-area rounded-4 p-3" style="background-color: #D9D9D9;" placeholder=""
                                rows="8" readonly></textarea>
                        </div>
                        <div class="d-flex justify-content-end align-items-center gap-3 mt-3"
                            style="font-family: 'Helvetica';">
                            <button type="button" class="btn btn-primary btn-updatekey" data-href="{{ route('voter.generateKeys') }}"
                                style="padding: 0.5em 2em; border-radius: 12px;">@lang('page.wait.renewingKeys-btn')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
