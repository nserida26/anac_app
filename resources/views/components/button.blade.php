@props(['type' => 'submit'])

<button {{ $attributes->merge(['type' => $type, 'class' => 'btn-gold px-6 py-2.5 rounded-xl text-sm font-bold tracking-wide']) }}>
    {{ $slot }}
</button>