@props(['field'])

@error($field)
    <span {{ $attributes->merge(['class' => 'form-error']) }}>
        {{ $message }}
    </span>
@enderror

