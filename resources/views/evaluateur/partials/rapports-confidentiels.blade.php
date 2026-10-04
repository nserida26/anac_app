{{--
    Tableau confidentiel des rapports médicaux, réservé au médecin évaluateur de l'ANAC.
    $aTraiter : transmis, pas encore validés par l'évaluateur ; $traites : validés par l'évaluateur.
    Les fichiers passent par la route sécurisée examens-medicaux.document (ExamenMedicalPolicy).
--}}
@php
    $lien = fn ($examen, $document) => route('examens-medicaux.document', ['examen' => $examen, 'document' => $document]);
    $libellesAvis = ['valide' => 'trans.avis_valide', 'reserve' => 'trans.avis_reserve', 'suggestion' => 'trans.avis_suggestion'];
@endphp

<div class="card card-danger card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-medical mr-2"></i> @lang('trans.rapports_medicaux')</h3>
    </div>
    <div class="card-body">
        <div class="alert alert-danger text-center font-weight-bold">
            <i class="fas fa-user-shield mr-1"></i> @lang('trans.formulaire_confidentiel_evaluateur')
        </div>

        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#rapports-a-traiter" role="tab">
                    @lang('trans.a_traiter') <span class="badge badge-warning">{{ $aTraiter->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#rapports-traites" role="tab">
                    @lang('trans.traites') <span class="badge badge-secondary">{{ $traites->count() }}</span>
                </a>
            </li>
        </ul>

        <div class="tab-content">
            @foreach (['rapports-a-traiter' => $aTraiter, 'rapports-traites' => $traites] as $onglet => $liste)
                @php $editable = $onglet === 'rapports-a-traiter'; @endphp
                <div class="tab-pane fade {{ $editable ? 'show active' : '' }}" id="{{ $onglet }}" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>N°</th>
                                    <th>@lang('trans.name')</th>
                                    <th>@lang('trans.licence')</th>
                                    <th>@lang('trans.contacts')</th>
                                    <th>@lang('trans.employeur')</th>
                                    <th>CEMPA/MEA</th>
                                    <th>@lang('trans.attestation_medicale')</th>
                                    <th>@lang('trans.rapport_medical')</th>
                                    <th style="min-width: 150px">@lang('trans.avis_de_l_evaluateur')</th>
                                    <th style="min-width: 220px">@lang('trans.observations')</th>
                                    <th>@lang('trans.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($liste as $examen)
                                    @php $formulaire = 'avis-' . $examen->id; @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $examen->demandeur->np ?? '-' }}
                                            <br><small class="text-muted">{{ $examen->date_examen }} — {{ $examen->aptitude }}</small>
                                        </td>
                                        <td>
                                            @forelse (optional($examen->demandeur)->licences ?? [] as $licence)
                                                <div class="text-nowrap">{{ $licence->type_licence }} — {{ $licence->numero_licence }}</div>
                                            @empty
                                                -
                                            @endforelse
                                        </td>
                                        <td>
                                            @if ($compte = optional($examen->demandeur)->user)
                                                <div><i class="fas fa-envelope"></i> {{ $compte->email }}</div>
                                                @if ($compte->whatsapp)
                                                    <div><i class="fab fa-whatsapp"></i> {{ $compte->whatsapp }}</div>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ optional($examen->demandeur)->employeur ?? '-' }}</td>
                                        <td>
                                            @if ($examen->centre_medical_id)
                                                {{ $examen->centreMedical->libelle ?? '-' }}
                                                <br><small class="text-muted">{{ optional($examen->examinateur)->nom_complet }}</small>
                                            @else
                                                MEA : {{ optional($examen->examinateur)->nom_complet ?? '-' }}
                                            @endif
                                        </td>
                                        @foreach (['attestation', 'rapport'] as $document)
                                            <td class="text-center">
                                                @if ($examen->{$document})
                                                    <a href="{{ $lien($examen, $document) }}" target="_blank" class="btn btn-primary btn-sm" title="@lang('trans.view')">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endforeach
                                        @if ($editable)
                                            <td>
                                                <select name="avis_evaluateur" form="{{ $formulaire }}" class="form-control form-control-sm" required>
                                                    <option value="">--</option>
                                                    @foreach ($libellesAvis as $valeur => $libelle)
                                                        <option value="{{ $valeur }}" {{ $examen->avis_evaluateur === $valeur ? 'selected' : '' }}>@lang($libelle)</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <textarea name="observations_evaluateur" form="{{ $formulaire }}" class="form-control form-control-sm" rows="2"
                                                          placeholder="@lang('trans.observations_obligatoires_reserve')">{{ $examen->observations_evaluateur }}</textarea>
                                            </td>
                                            <td class="text-nowrap">
                                                <form id="{{ $formulaire }}" action="{{ route('evaluateur.avis', $examen) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" title="@lang('trans.save')"><i class="fas fa-save"></i></button>
                                                </form>
                                                <a href="{{ route('evaluateur.edit', $examen) }}" class="btn btn-outline-secondary btn-sm" title="@lang('trans.avis_detaille')">
                                                    <i class="fas fa-sliders-h"></i>
                                                </a>
                                                @if ($examen->avis_evaluateur)
                                                    <form action="{{ route('evaluateur.valider', ['table' => 'examens_medicaux', 'id' => $examen->id]) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm(@json(__('trans.confirmer_transmission_sma')))">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-warning btn-sm" title="@lang('trans.transmettre_sma')"><i class="fas fa-paper-plane"></i></button>
                                                    </form>
                                                @endif
                                            </td>
                                        @else
                                            <td>@include('examens_medicaux.partials.badge-avis', ['examen' => $examen])</td>
                                            <td style="white-space: pre-line">{{ $examen->observations_evaluateur ?: '-' }}</td>
                                            <td>
                                                <a href="{{ route('evaluateur.show', $examen) }}" class="btn btn-info btn-sm" title="@lang('trans.view')"><i class="fas fa-eye"></i></a>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted p-3">@lang('trans.aucun_rapport_medical')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
