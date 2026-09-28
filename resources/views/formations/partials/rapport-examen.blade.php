{{-- Qualité du formateur et rapport d'examen d'une formation (pages centre, détenteur, ANAC). --}}
@if ($formation->qualite_formateur)
    <span class="badge badge-{{ $formation->qualite_formateur === 'examinateur' ? 'success' : 'info' }}">
        @lang($formation->qualite_formateur === 'examinateur' ? 'trans.en_tant_qu_examinateur' : 'trans.en_tant_qu_instructeur')
    </span>
@endif
@if ($formation->rapport)
    <a href="{{ asset('/uploads/' . $formation->rapport) }}" target="_blank" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-file-medical-alt"></i> @lang('trans.rapport_examen')
    </a>
@endif
