@props([
    'disabled' => false,
    'name' => null,
    'options' => [],
    'selected' => null,
    'darkmode' => false,
    'multiple' => false,
    'select2' => false,
])

@php
    $hasError = $name && $errors->has($name);
    $selectedValue = old($name, $selected);

    // Base classes: form-select provides chevron arrow, error states & dark mode via CSS
    // Tailwind classes add consistent sizing/matching with other inputs (.form-input)
    $baseClasses = 'form-select w-full px-4 py-3 rounded-xl text-sm appearance-none cursor-pointer transition-all duration-200';

    if ($darkmode) {
        $baseClasses .= ' form-select-dark';
    }

    if ($select2) {
        $baseClasses .= ' select2';
    }

    if ($hasError) {
        $baseClasses .= ' error';
    }

    $attrs = $attributes->merge(['class' => $baseClasses]);
    if ($select2) {
        $attrs = $attrs->merge(['data-toggle' => 'select2']);
    }
@endphp

<select
    name="{{ $name }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $multiple ? 'multiple' : '' }}
    {!! $attrs !!}
>
    @forelse($options as $optionValue => $optionLabel)
        @php
            $isSelected = is_array($selectedValue)
                ? in_array($optionValue, $selectedValue)
                : $selectedValue == $optionValue;
        @endphp
        <option value="{{ $optionValue }}" {{ $isSelected ? 'selected' : '' }}>
            {{ $optionLabel }}
        </option>
    @empty
        {{ $slot }}
    @endforelse
</select>
