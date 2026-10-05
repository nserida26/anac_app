                @if ($isDepouilleMortelle)
                    <div class="card">
                        <x-anac-card-header icon="fas fa-building" :title="__('trans.operator_represented')" />
                        <div class="card-body">
                            <p class="text-muted small">@lang('trans.operator_represented_hint')</p>
                            <div class="row align-items-end">
                                <div class="col-md-8">
                                    <div class="form-group mb-0">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="demande_compagnie_id" class="form-label">
                                                @lang('trans.operator') <span class="text-danger">*</span>
                                            </label>
                                            <button type="button" class="btn btn-sm anac-action anac-action--blue" id="addCompanyBtnDemande">
                                                <i class="fas fa-plus"></i> @lang('trans.add_action')
                                            </button>
                                        </div>
                                        <select class="form-control select2-single" id="demande_compagnie_id" required
                                            {{ $readonly ? 'disabled' : '' }}>
                                            <option value="">@lang('trans.select_operator')</option>
                                            @foreach ($compagnies as $compagnie)
                                                <option value="{{ $compagnie->id }}"
                                                    {{ $demandeAutorisation->compagnie_id == $compagnie->id ? 'selected' : '' }}>
                                                    @if (!empty($compagnie->code))
                                                        {{ $compagnie->code }} {{ $compagnie->nom_entreprise }}
                                                    @else
                                                        {{ $compagnie->nom_entreprise }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" id="saveOperateurBtn" class="anac-btn anac-btn--primary">
                                        <i class="fas fa-save"></i> @lang('trans.save')
                                    </button>
                                    @if ($demandeAutorisation->compagnie_id)
                                        <span class="badge badge--success ml-2" id="operateurSavedBadge">
                                            <i class="fas fa-check"></i> @lang('trans.saved')
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

@push('custom')
    <script>
        $(document).ready(function() {
            // Cas dépouille mortelle : ajouter un opérateur depuis la carte "Opérateur représenté"
            $('#addCompanyBtnDemande').click(function() {
                $('#companyModal').modal('show');
            });

            // Cas dépouille mortelle : enregistrer l'opérateur explicitement représenté par la demande
            $('#saveOperateurBtn').click(function() {
                const compagnieId = $('#demande_compagnie_id').val();
                if (!compagnieId) {
                    toastr.error("@lang('trans.select_operator')");
                    return;
                }

                const $btn = $(this);
                $btn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('user.autorisations.update-operateur', $demandeAutorisation->id) }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        compagnie_id: compagnieId
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        if ($('#operateurSavedBadge').length === 0) {
                            $btn.after(
                                '<span class="badge badge--success ml-2" id="operateurSavedBadge">' +
                                '<i class="fas fa-check"></i> @lang('trans.saved')' +
                                '</span>'
                            );
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || @json(__('trans.error_occurred')));
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
