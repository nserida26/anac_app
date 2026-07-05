@props(['for' => null, 'value' => null, 'check' => false, 'darkmode' => false])

<label {{ $attributes->merge(['for' => $for, 'class' => $darkmode ? 'form-label-dark' : 'form-label']) }}>
    {{ $value ?? $slot }}
</label>

