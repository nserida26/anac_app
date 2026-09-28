{{-- Qualité du formateur et rapport d'examen d'une formation (pages centre, détenteur, ANAC). --}}
@if ($formation->qualite_formateur)
    <span class="badge badge-{{ $formation->qualite_formateur === 'examinateur' ? 'success' : 'info' }}">
        @lang($formation->qualite_formateur === 'examinateur' ? 'trans.en_tant_qu_examinateur' : 'trans.en_tant_qu_instructeur')
    </span>
@endif
{{-- Rapport confidentiel : lien affiché seulement aux personnes autorisées (FormationPolicy) --}}
@if ($formation->rapport)
    @can('voirRapport', $formation)
        <a href="{{ route('formations.rapport', $formation) }}" target="_blank" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-file-medical-alt"></i> @lang('trans.rapport_examen')
        </a>
    @endcan
@endif
