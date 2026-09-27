{{-- Badge de l'avis de l'évaluateur sur un rapport médical (listes évaluateur et SMA). --}}
@switch($examen->avis_evaluateur)
    @case('valide')
        <span class="badge badge-success">@lang('trans.avis_valide')</span>
        @break
    @case('reserve')
        <span class="badge badge-warning">@lang('trans.avis_reserve')</span>
        @break
    @case('suggestion')
        <span class="badge badge-info">@lang('trans.avis_suggestion')</span>
        @break
    @default
        <span class="badge badge-secondary">@lang('trans.avis_en_attente')</span>
@endswitch
@if ($examen->validite_evaluateur && (int) $examen->validite_evaluateur < (int) $examen->validite)
    <span class="badge badge-warning">@lang('trans.validite_reduite')</span>
@endif
