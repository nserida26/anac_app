@props(['disabled' => false, 'name' => null, 'type' => 'text', 'darkmode' => false, 'withError' => true])

@php

    $isCheckbox = in_array($type, ['checkbox', 'radio']);
    $isFile = $type === 'file';
    $baseClass = $isCheckbox
        ? ($darkmode ? 'form-checkbox-dark' : 'form-checkbox')
        : ($darkmode ? 'form-input-dark' : 'form-input w-full px-4 py-3 rounded-xl text-sm transition-all duration-200' . ($darkmode ? ' form-input-dark' : ''));

    // Automatically retain old input value on validation errors for text-like inputs
    $supportsOldValue = !$isCheckbox && !$isFile && $name;
    if ($supportsOldValue) {
        $resolvedValue = old($name, $attributes->get('value'));
        $attributes = $attributes->except('value');
    }

@endphp

<input type="{{ $type }}" name="{{ $name }}"
    {{ $disabled ? 'disabled' : '' }}
    @if($supportsOldValue) value="{{ $resolvedValue }}" @endif
    {!! $attributes->merge(['class' => $baseClass . ($name && $errors->has($name) && !$isCheckbox ? ' error' : '')]) !!}>

@if($withError && $name)
    <x-form.error :field="$name" class="mt-1" />
@endif
