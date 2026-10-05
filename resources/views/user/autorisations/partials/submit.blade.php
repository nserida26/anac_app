                <!-- Envoi de la demande -->
                @php
                    $etatDemande = $demandeAutorisation->etatDemande;
                    $documentCount = $demandeAutorisation->documents->count();
                    $canSubmit = $etatDemande && !$etatDemande->compagnie_cree_demande && $documentCount > 0;
                @endphp
                @if ($etatDemande && !$etatDemande->compagnie_cree_demande)
                    <div class="card card-success">
                        <div class="card-body text-center">
                            @if ($canSubmit)
                                <form action="{{ route('update-state', $demandeAutorisation->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <input type="hidden" name="action" value="compagnie_cree_demande">
                                    <input type="hidden" name="is_approved" value="1">
                                    <button type="submit" class="btn btn-success btn-lg"
                                        onclick="return confirm('@lang('trans.confirm_submission')')">
                                        <i class="fas fa-paper-plane"></i> @lang('trans.send')
                                        <span class="badge badge-light">{{ $documentCount }}</span>
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-secondary btn-lg" disabled title="@lang('trans.add_docs_first')">
                                    <i class="fas fa-paper-plane"></i> @lang('trans.send')
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
