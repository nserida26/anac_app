{{-- Modification du numéro d'autorisation — réservé DTA / Administrateur (SRTA).

     Paramètres :
       $autorisation           instance App\Models\Autorisation (obligatoire)
       $part (optionnel)       'all' (défaut) | 'button' | 'modal'
                               Utiliser 'button' dans une cellule de tableau et 'modal'
                               juste après la ligne <tr> pour éviter que .table-responsive
                               ne rogne la fenêtre modale.
--}}
@php
    $canEditNumero = auth()->check() && auth()->user()->hasAnyRole(['dta', 'admin']);
    $part = $part ?? 'all';
@endphp

@if ($canEditNumero && !empty($autorisation) && $autorisation->id)
    @if ($part === 'all' || $part === 'button')
        <button type="button" class="btn btn-outline-primary btn-sm mb-1"
                data-toggle="modal" data-target="#editNumeroModal-{{ $autorisation->id }}">
            <i class="fas fa-hashtag"></i> @lang('trans.edit_autorisation_numero')
        </button>
    @endif

    @if ($part === 'all' || $part === 'modal')
        <div class="modal fade" id="editNumeroModal-{{ $autorisation->id }}" tabindex="-1" role="dialog"
             aria-labelledby="editNumeroModalLabel-{{ $autorisation->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('autorisations.update-numero', $autorisation->id) }}" method="POST">
                        @csrf
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="editNumeroModalLabel-{{ $autorisation->id }}">
                                <i class="fas fa-hashtag"></i> @lang('trans.edit_autorisation_numero')
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="code_autorisation-{{ $autorisation->id }}">
                                    @lang('trans.autorisation_numero')
                                </label>
                                <input type="text"
                                       name="code_autorisation"
                                       id="code_autorisation-{{ $autorisation->id }}"
                                       class="form-control"
                                       value="{{ $autorisation->code_autorisation }}"
                                       maxlength="50"
                                       autocomplete="off"
                                       required>
                            </div>
                            <p class="text-muted small mb-0">
                                <i class="fas fa-info-circle"></i> @lang('trans.edit_autorisation_numero_hint')
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                @lang('trans.cancel')
                            </button>
                            <button type="submit" class="btn btn-primary"
                                    onclick="return confirm('@lang('trans.edit_autorisation_numero_confirm')')">
                                <i class="fas fa-save"></i> @lang('trans.save')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endif
