{{-- Session messages shown as toasts (behaviour in resources/js/ui/toast.js) --}}
@php
    $toastTypes = ['success', 'info', 'warning', 'error'];
    $toasts = collect($toastTypes)->filter(fn ($type) => session()->has($type));
@endphp
<div class="ui-toasts" aria-live="polite" aria-relevant="additions">
    @foreach ($toasts as $type)
        <div class="ui-toast ui-toast-{{ $type }}" role="{{ $type === 'error' ? 'alert' : 'status' }}" data-toast
            data-timeout="{{ $type === 'error' ? 8000 : 5000 }}">
            <span class="ui-toast-icon" aria-hidden="true">
                @switch($type)
                    @case('success')
                        <svg width="18" height="18" viewBox="0 0 18 18"><path d="M4 9.5l3 3 7-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @break
                    @case('error')
                        <svg width="18" height="18" viewBox="0 0 18 18"><path d="M9 5v5M9 13h.01" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        @break
                    @case('warning')
                        <svg width="18" height="18" viewBox="0 0 18 18"><path d="M9 5v5M9 13h.01" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        @break
                    @default
                        <svg width="18" height="18" viewBox="0 0 18 18"><path d="M9 8v5M9 5h.01" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                @endswitch
            </span>
            <p class="ui-toast-text">{{ __(session($type)) }}</p>
            <button type="button" class="ui-toast-close" data-toast-close aria-label="@lang('Close')">
                <svg width="12" height="12" viewBox="0 0 12 12" aria-hidden="true"><path d="M2 2l8 8M10 2l-8 8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </button>
        </div>
    @endforeach
</div>
