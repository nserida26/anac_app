@props([
    'type' => 'info',
    'icon' => null,
    'preLine' => false,
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
</div>
