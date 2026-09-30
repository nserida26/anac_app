{{-- resources/views/dir/demandeAutorisations/modals/issues.blade.php --}}
<div class="modal fade" id="issuesModal-{{ $demande->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content anac-modal anac-modal--danger">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-bug"></i> @lang('trans.issues_for') : <span class="anac-modal__code">{{ $demande->code }}</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="@lang('trans.close')"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                {{-- Rejeté par (DTA/DG), d'après etatDemande->dta_rejeter / dg_rejeter --}}
                @include('dir.demandeAutorisations.partials.rejected-by', ['demande' => $demande])

                {{-- Motifs d'invalidité (Technique) --}}
                @include('dir.demandeAutorisations.partials.invalid-components', ['demande' => $demande])

                {{-- Motifs de rejet (Administratif) --}}
                @include('dir.demandeAutorisations.partials.rejection-reasons-list', ['demande' => $demande])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('trans.close')</button>
                @if (auth()->user()->can('edit-demandes'))
                    <a href="{{ route('user.autorisations.edit', $demande->id) }}" class="btn btn-primary">
                        <i class="fas fa-tools"></i> @lang('trans.correct_issues')
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>