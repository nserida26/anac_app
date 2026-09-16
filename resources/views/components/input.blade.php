@props([
    'name',
    'label' => null,
    'type' => 'text',
    'icon' => null,
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'autofocus' => false,
    'error' => null,
    'hint' => null,
])

@php
    $inputId = $attributes->get('id', $name);
    $oldValue = old($name, $value);
@endphp

<div class="form-group">
    @if($label)
        <label for="{{ $inputId }}">{{ $label }}</label>
    @endif

    @if($type === 'select')
        <div class="input-icon">
            @if($icon)
                <i class="{{ $icon }} icon"></i>
            @endif
            <select name="{{ $name }}" id="{{ $inputId }}"
                    class="form-control {{ $error ? 'is-invalid' : '' }}"
                    {{ $required ? 'required' : '' }}
                    {{ $autofocus ? 'autofocus' : '' }}
                    {{ $attributes->merge(['class' => 'form-control']) }}>
                {{ $slot }}
            </select>
        </div>
    @else
        <div class="input-icon">
            @if($icon)
                <i class="{{ $icon }} icon"></i>
            @endif
            <input type="{{ $type }}"
                   name="{{ $name }}"
                   id="{{ $inputId }}"
                   class="form-control {{ $error ? 'is-invalid' : '' }}"
                   placeholder="{{ $placeholder }}"
                   value="{{ $oldValue }}"
                   {{ $required ? 'required' : '' }}
                   {{ $autofocus ? 'autofocus' : '' }}
                   {{ $attributes->merge(['class' => 'form-control']) }}>
        </div>
    @endif

    @if($hint)
        <div class="field-hint">{{ $hint }}</div>
    @endif

    @if($error)
        <span class="text-danger">{{ $error }}</span>
    @endif
</div>
