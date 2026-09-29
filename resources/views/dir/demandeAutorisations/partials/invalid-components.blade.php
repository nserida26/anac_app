{{-- resources/views/dir/demandeAutorisations/partials/invalid-components.blade.php --}}
@if(!empty($demande->invalid_reasons))
    @php
        // DemandeAutorisation::getInvalidComponents() renvoie un type au singulier
        // (avion, vol...), distinct du nom de la table réelle attendue par
        // AdminController::retirerRejet() (liste blanche REJECTABLE_AUTORISATION_TABLES).
        $invalidComponentTables = [
            'avion' => 'avions',
            'vol' => 'vols',
            'equipage' => 'equipe_vols',
            'fret' => 'fret_vols',
            'personne' => 'personne_deces',
            'mdn' => 'mdns',
            'receiving_party' => 'receiving_parties',
            'document' => 'document_autorisations',
        ];
        $canRetirerRejet = auth()->check() && auth()->user()->hasAnyRole(['dta', 'admin']);
        // Partiel partagé entre l'espace DTA (préfixe dir/) et l'espace admin (SRTA) :
        // chacun a sa propre route pour la même action (groupes de rôles différents).
        $retirerRejetRoute = auth()->check() && auth()->user()->hasRole('dta')
            ? 'dir.autorisations.retirer-rejet'
            : 'autorisations.retirer-rejet';
    @endphp
    <div class="alert alert-warning">
        <h6 class="mb-1"><i class="fas fa-exclamation-triangle"></i> @lang('trans.invalid_components')</h6>
        <ul class="mb-0">
            @foreach($demande->invalid_reasons as $reason)
                <li class="d-flex justify-content-between align-items-center flex-wrap">
                    <span>
                        <strong>{{ ucfirst(str_replace('_', ' ', $reason['type'] ?? '')) }}</strong>
                        @if(!empty($reason['identifier']))
                            ({{ $reason['identifier'] }})
                        @endif
                        :
                        {{ $reason['motif'] ?? 'N/A' }}
                        @if(!empty($reason['role']))
                            <span class="badge badge-secondary">{{ $reason['role'] }}</span>
                        @endif
                    </span>

                    @if ($canRetirerRejet && !empty($invalidComponentTables[$reason['type'] ?? '']))
                        <form action="{{ route($retirerRejetRoute) }}" method="POST" class="d-inline ml-2">
                            @csrf
                            <input type="hidden" name="table" value="{{ $invalidComponentTables[$reason['type']] }}">
                            <input type="hidden" name="id" value="{{ $reason['id'] }}">
                            <input type="hidden" name="demande_id" value="{{ $demande->id }}">
                            <button type="submit" class="btn btn-outline-success btn-sm"
                                    onclick="return confirm('@lang('trans.confirm_retirer_rejet')')">
                                <i class="fas fa-undo"></i> @lang('trans.retirer_rejet')
                            </button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif
