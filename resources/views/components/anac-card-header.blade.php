@props([
    'icon' => 'fas fa-folder-open',
    'title' => null,
    'count' => null,
    'countIcon' => 'fas fa-paperclip',
])

@php
    $hasActions = isset($actions) && trim((string) $actions) !== '';
@endphp

<div {{ $attributes->merge(['class' => 'card-header']) }}>
    <div class="anac-card-head">
        <span class="anac-card-head__icon"><i class="{{ $icon }}"></i></span>
        <h3 class="anac-card-head__title">{{ $title ?? $slot }}</h3>
    </div>

    @if (!is_null($count) || $hasActions)
        <div class="anac-card-head__actions">
            @if (!is_null($count))
                <span class="anac-card-head__count">
                    <i class="{{ $countIcon }}"></i> {{ $count }}
                </span>
            @endif

            @if ($hasActions)
                {{ $actions }}
            @endif
        </div>
    @endif
</div>
