@props([
    'type' => 'info',
    'icon' => null,
    'preLine' => false,
    'closable' => false,
])

@php
    $icons = [
        'error'   => 'fas fa-exclamation-circle',
        'success' => 'fas fa-check-circle',
        'info'    => 'fas fa-info-circle',
    ];
    $iconClass = $icon ?? ($icons[$type] ?? $icons['info']);
@endphp

<div class="auth-alert auth-alert--{{ $type }}" {{ $preLine ? 'style="white-space: pre-line;"' : '' }}>
    <i class="{{ $iconClass }}"></i>
    @if($preLine)
        <span>{!! nl2br(e($slot)) !!}</span>
    @else
        {{ $slot }}
    @endif

    @if ($closable)
        <button type="button" class="auth-alert__close"
                onclick="this.closest('.auth-alert').remove();"
                aria-label="@lang('trans.close')">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
    @endif
</div>
