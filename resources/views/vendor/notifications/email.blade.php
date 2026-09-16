@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# Oups !
@else
# Bienvenue !
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
@component('mail::button', ['url' => $actionUrl, 'color' => $level === 'error' ? 'error' : 'primary'])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
Cordialement,<br>
<strong>{{ config('app.name') }}</strong>
@endif

{{-- Subcopy --}}
@isset($actionText)
@slot('subcopy')
@lang(
    "Si le bouton \":actionText\" ne fonctionne pas, copiez et collez l'URL ci-dessous dans votre navigateur :",
    [
        'actionText' => $actionText,
    ]
) <span class="break-all" style="word-break: break-all; color: #0d2137;">{{ $displayableActionUrl }}</span>
@endslot
@endisset
@endcomponent
