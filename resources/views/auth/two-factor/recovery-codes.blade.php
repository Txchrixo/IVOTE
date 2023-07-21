@extends('templates.public')

@section('title', __('Recovery codes'))
@section('description', __('Recovery codes'))

@php
    $codesText = config('app.name') . ' - ' . __('Recovery codes') . ' (' . auth()->user()->username . ")\n"
        . now()->toDateString() . "\n\n"
        . implode("\n", $codes) . "\n";
@endphp

@section('content')
    <div class="left">
        <div class="content">
            <div class="header">
                <p class="lead">@yield('description')</p>
            </div>
            <p class="auth-help">@lang('Save these codes somewhere safe. Each one can be used once if you lose access to your authenticator app. They will not be shown again.')</p>
            <ul class="recovery-codes" id="recovery-codes">
                @foreach ($codes as $code)
                    <li>{{ $code }}</li>
                @endforeach
            </ul>
            <div class="recovery-actions">
                {{-- Generated in the page itself: the codes never travel again to the server --}}
                <a class="btn btn-light" download="ivote-recovery-codes.txt"
                    href="data:text/plain;charset=utf-8,{{ rawurlencode($codesText) }}">@lang('Download')</a>
                <button type="button" class="btn btn-light" id="copy-recovery-codes"
                    data-copied="@lang('Copied')">@lang('Copy')</button>
            </div>
            <a href="{{ route('auth.dashboard') }}" class="btn btn-primary btn-lg btn-block btn-login w-100 mt-3">@lang('Continue')</a>
        </div>
    </div>
    <div class="right">
        <div class="overlay"></div>
    </div>
@endsection

@push('scripts')
    <script nonce="{{ Vite::cspNonce() }}">
        document.getElementById('copy-recovery-codes').addEventListener('click', function () {
            var button = this;
            var codes = Array.from(document.querySelectorAll('#recovery-codes li'))
                .map(function (item) { return item.textContent.trim(); })
                .join('\n');

            navigator.clipboard.writeText(codes).then(function () {
                var label = button.textContent;
                button.textContent = button.dataset.copied;
                setTimeout(function () { button.textContent = label; }, 2000);
            });
        });
    </script>
@endpush
