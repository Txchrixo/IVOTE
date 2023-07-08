@extends('templates.voter')

@section('title', __('page.generateKeys.title'))

@section('content')
    @push('css')
        <style>
            * {
                font-family: 'Helvetica Light';
            }
        </style>
    @endpush
    <div class="row">
        <div class="col-md-offset-1 col-md-10 col-md-offset-1 w-100 px-3 py-0">
            <div class="panel panel-headline">
                <div class="pb-3">
                    <h3 class="panel-title" style="font-family: 'Helvetica Rounded';">@lang('page.generateKeys.main-title')</h3>
                </div>
                <div class="bt-1">
                    <p>@lang('page.generateKeys.main-text')</p>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mt-3 mb-2" style="font-family: 'Helvetica';">@lang('page.generateKeys.publicKey')</p>
                            <textarea class="form-control public-key-area rounded-4 p-3" style="background-color: #D9D9D9;" placeholder=""
                                rows="14" readonly></textarea>
                        </div>
                        <div class="col-md-6">
                            <p class="mt-3 mb-2" style="font-family: 'Helvetica';">@lang('page.generateKeys.privateKey')</p>
                            <textarea class="form-control private-key-area rounded-4 p-3" style="background-color: #D9D9D9;"
                                placeholder="@lang('page.generateKeys.privateKey-placeholder')" rows="14" readonly></textarea>
                        </div>
                        <div class="d-flex justify-content-end align-items-center gap-3 mt-3"
                            style="font-family: 'Helvetica';">
                            <button type="button" class="btn btn-success btn-generate"
                                style="padding: 0.5em 2em; border-radius: 12px;">@lang('page.generateKeys.generate-btn')</button>
                            <button type="button" class="btn btn-primary btn-updatekey"
                                style="padding: 0.5em 2em; border-radius: 12px;">@lang('page.generateKeys.save-btn')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
