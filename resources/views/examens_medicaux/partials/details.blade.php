{{--
    Détail d'un rapport médical, partagé par l'examinateur, le centre d'expertise médicale,
    l'évaluateur et la SMA. Les fichiers passent par la route sécurisée examens-medicaux.document.
--}}
@php
    $lienDocument = fn ($document) => route('examens-medicaux.document', ['examen' => $examen, 'document' => $document]);
    $libellesAvis = ['valide' => 'trans.avis_valide', 'reserve' => 'trans.avis_reserve', 'suggestion' => 'trans.avis_suggestion'];
    $couleursAvis = ['valide' => 'success', 'reserve' => 'warning', 'suggestion' => 'info'];
@endphp
<table class="table table-bordered table-striped">
    <tr>
        <th width="35%">@lang('trans.fl_name')</th>
        <td>{{ $examen->demandeur->np ?? '-' }}</td>
    </tr>
    <tr>
        <th>@lang('trans.dob')</th>
        <td>{{ $examen->demandeur->date_naissance ?? '-' }}</td>
    </tr>
    <tr>
        <th>@lang('trans.lieu_naissance')</th>
        <td>{{ $examen->demandeur->lieu_naissance ?? '-' }}</td>
    </tr>
    <tr>
        <th>@lang('trans.medical_examiner')</th>
        <td>{{ optional($examen->examinateur)->nom_complet ?? '-' }}</td>
    </tr>
    <tr>
        <th>@lang('trans.source_rapport')</th>
        <td>
            @if ($examen->centre_medical_id)
                <i class="fas fa-hospital"></i> {{ $examen->centreMedical->libelle ?? '-' }}
            @else
                <i class="fas fa-user-md"></i> @lang('trans.examinateur_individuel')
            @endif
        </td>
    </tr>
    <tr>
        <th>@lang('trans.exam_date')</th>
        <td>{{ $examen->date_examen ?? '-' }}</td>
    </tr>
    <tr>
        <th>@lang('trans.validite_mois')</th>
        <td>{{ $examen->validite ?? '-' }}</td>
    </tr>
    <tr>
        <th>@lang('trans.medical_fitness')</th>
        <td>{{ $examen->aptitude ?? '-' }}</td>
    </tr>
    @foreach (['rapport' => 'trans.report', 'attestation' => 'trans.certificate'] as $document => $libelle)
        <tr>
            <th>@lang($libelle)</th>
            <td>
                @if ($examen->{$document})
                    <a href="{{ $lienDocument($document) }}" target="_blank" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i> @lang('trans.view')
                    </a>
                @else
                    -
                @endif
            </td>
        </tr>
    @endforeach
    <tr>
        <th>@lang('trans.statut_circuit')</th>
        <td>
            <span class="badge badge-{{ $examen->valider_examinateur ? 'success' : 'secondary' }}">@lang('trans.transmis')</span>
            <span class="badge badge-{{ $examen->valider_evaluateur ? 'success' : 'secondary' }}">@lang('trans.evaluator')</span>
            <span class="badge badge-{{ $examen->valider_sma ? 'success' : 'secondary' }}">SMA</span>
        </td>
    </tr>
</table>

@if ($examen->avis_evaluateur)
    <h6 class="mt-3"><i class="fas fa-clipboard-check"></i> @lang('trans.avis_de_l_evaluateur')</h6>
    <table class="table table-bordered">
        <tr>
            <th width="35%">@lang('trans.avis')</th>
            <td>
                <span class="badge badge-{{ $couleursAvis[$examen->avis_evaluateur] ?? 'secondary' }}">
                    @lang($libellesAvis[$examen->avis_evaluateur] ?? $examen->avis_evaluateur)
                </span>
            </td>
        </tr>
        <tr>
            <th>@lang('trans.validity_evaluator')</th>
            <td>
                {{ $examen->validite_evaluateur }}
                @if ((int) $examen->validite_evaluateur < (int) $examen->validite)
                    <span class="badge badge-warning">@lang('trans.validite_reduite')</span>
                @endif
            </td>
        </tr>
        @if ($examen->observations_evaluateur)
            <tr>
                <th>@lang('trans.observations')</th>
                <td style="white-space: pre-line">{{ $examen->observations_evaluateur }}</td>
            </tr>
        @endif
        @if ($examen->rapport_evaluateur)
            <tr>
                <th>@lang('trans.report_by_evaluator')</th>
                <td>
                    <a href="{{ $lienDocument('rapport_evaluateur') }}" target="_blank" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i> @lang('trans.view')
                    </a>
                </td>
            </tr>
        @endif
    </table>
@endif
