@push('css')
    <style>
        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
            background-color: var(--anac-primary);
            color: var(--anac-white);
            border-color: var(--anac-primary);
            padding: 5px 10px;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 5px;
            border-right: 1px solid rgba(255, 255, 255, 0.3);
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ffc107;
            background: transparent;
        }

        .select2-container--bootstrap4 .select2-selection--multiple {
            min-height: 100px;
            border: 1px solid #ced4da;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-search__field {
            margin-top: 8px;
        }

        .preview-badge {
            font-size: 0.8rem;
            padding: 0.4em 0.85em;
            margin: 2px;
            border-radius: 999px;
            background-color: var(--anac-primary-light);
            color: var(--anac-white);
            display: inline-block;
        }
    </style>
@endpush

                @unless ($isDepouilleMortelle)
                    <div class="card">
                        <x-anac-card-header icon="fas fa-fighter-jet" :title="__('trans.plane_info')">
                            <x-slot name="actions">
                                <button type="button" class="anac-btn anac-btn--primary" id="showAvionFormBtn">
                                    <i class="fas fa-plus"></i> @lang('trans.add_planes')
                                </button>
                            </x-slot>
                        </x-anac-card-header>
                        <div class="card-body">
                            <!-- Formulaire avec Select2 Tags -->
                            <form method="POST" id="avionForm" style="display: none;">
                                @csrf
                                <input type="hidden" name="avion_id" id="avion_id" value="">
                                <input type="hidden" name="demande_autorisation_id" id="demande_autorisation_id"
                                    value="{{ $demandeAutorisation->id }}">

                                <div class="auth-alert auth-alert--info">
                                    <i class="fas fa-info-circle"></i>
                                    @lang('trans.registrations_help')
                                </div>

                                <div class="row">
                                    <!-- Immatriculations multiples avec Select2 Tags -->
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="immatriculations" class="form-label">
                                                @lang('trans.registrations') <span class="text-danger">*</span>
                                            </label>
                                            <!-- Champs caché pour Select2 -->
                                            <select class="form-control" id="immatriculations_select" name="immatriculations[]"
                                                multiple="multiple" style="width: 100%; height: 100px;">
                                                <!-- Les options seront ajoutées dynamiquement -->
                                            </select>
                                            <div class="invalid-feedback" id="immatriculations_error"></div>
                                            <small class="text-muted">@lang('trans.registration_input_help')</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Type d'avion -->
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label for="type_avion_id" class="form-label">@lang('trans.plane_type') <span
                                                        class="text-danger">*</span></label>
                                                <button type="button" class="btn btn-sm anac-action anac-action--blue" id="addTypeAvionBtn">
                                                    <i class="fas fa-plus"></i> @lang('trans.add_action')
                                                </button>
                                            </div>
                                            <select class="form-control select2-single" id="type_avion_id" name="type_avion_id"
                                                required>
                                                <option value="">@lang('trans.select_type')</option>
                                                @foreach ($type_avions as $type)
                                                    <option value="{{ $type->id }}" data-code="{{ $type->code }}"
                                                        data-capacite="{{ $type->capacite }}">
                                                        {{ $type->code }} ({{ $type->capacite }} @lang('trans.seats'))
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="type_avion_id_error"></div>
                                        </div>
                                    </div>

                                    <!-- Opérateur -->
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label for="compagnie_aerienne_id" class="form-label">@lang('trans.operator') <span
                                                        class="text-danger">*</span></label>
                                                <button type="button" class="btn btn-sm anac-action anac-action--blue" id="addCompanyBtn">
                                                    <i class="fas fa-plus"></i> @lang('trans.add_action')
                                                </button>
                                            </div>

                                            <select class="form-control select2-single" id="compagnie_aerienne_id"
                                                name="compagnie_aerienne_id" required>
                                                <option value="">@lang('trans.select_operator')</option>
                                                @foreach ($compagnies as $compagnie)
                                                    <option value="{{ $compagnie->id }}" data-code="{{ $compagnie->code }}">
                                                        @if (!empty($compagnie->code))
                                                            {{ $compagnie->code }} {{ $compagnie->nom_entreprise }}
                                                        @else
                                                            {{ $compagnie->nom_entreprise }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="compagnie_aerienne_id_error"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Prévisualisation -->
                                <div class="row" id="previewSection" style="display: none;">
                                    <div class="col-md-12">
                                        <div class="auth-alert auth-alert--success align-items-start">
                                            <i class="fas fa-check-circle"></i>
                                            <div class="flex-grow-1">
                                            <h6><i class="fas fa-plane"></i> @lang('trans.preview_planes_title')</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>@lang('trans.type'):</strong> <span
                                                            id="selectedTypeDisplay">-</span></p>
                                                    <p><strong>@lang('trans.operator'):</strong> <span
                                                            id="selectedOperatorDisplay">-</span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <div id="immatriculationsPreview" class="mt-2"></div>
                                                    <p class="mb-0"><strong>@lang('trans.total'):</strong> <span
                                                            id="totalCount">0</span>
                                                        @lang('trans.plane_unit')</p>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="anac-btn anac-btn--primary float-right"
                                            id="submitAvionBtn">
                                            <i class="fas fa-save"></i> <span id="formActionText">@lang('trans.save')</span>
                                        </button>
                                        <button type="button" class="anac-btn anac-btn--ghost float-right mr-2"
                                            id="cancelAvionFormBtn">
                                            <i class="fas fa-times"></i> @lang('trans.cancel')
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Tableau des avions existants -->
                            @if (isset($avions) && $avions->isNotEmpty())
                                <div class="table-responsive">
                                    <div class="row mt-4" id="avionsTableContainer">
                                        <div class="col-lg-12">
                                            <div class="table-responsive">
                                                <table class="table" id="avionsTable">
                                                    <thead>
                                                        <tr>
                                                            <th>@lang('trans.registration')</th>
                                                            <th>@lang('trans.type')</th>
                                                            <th>@lang('trans.operator')</th>
                                                            <th>@lang('trans.actions')</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($avions as $avionItem)
                                                            <tr id="avion-{{ $avionItem->id }}">
                                                                <td>{{ $avionItem->immatriculation }}</td>
                                                                <td>{{ $avionItem->type->code ?? __('trans.not_available') }}</td>
                                                                <td>{{ $avionItem->operateur_nom ?? __('trans.not_available') }}</td>
                                                                <td>
                                                                    <div class="anac-actions">
                                                                        <button class="btn btn-sm anac-action anac-action--yellow edit-avion"
                                                                            data-id="{{ $avionItem->id }}"
                                                                            data-immatriculation="{{ $avionItem->immatriculation }}"
                                                                            data-type_avion_id="{{ $avionItem->type_avion_id }}"
                                                                            data-compagnie_aerienne_id="{{ $avionItem->compagnie_aerienne_id }}">
                                                                            <i class="fas fa-edit"></i> @lang('trans.edit')
                                                                        </button>
                                                                        <button class="btn btn-sm anac-action anac-action--red delete-avion"
                                                                            data-id="{{ $avionItem->id }}">
                                                                            <i class="fas fa-trash"></i> @lang('trans.delete')
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="auth-alert auth-alert--info mb-0" id="noAvionsAlert">
                                    <i class="fas fa-info-circle"></i>
                                    @lang('trans.no_planes_registered')
                                </div>
                            @endif
                        </div>
                    </div>
                @endunless


@push('custom')
    <script>
        $(document).ready(function() {
            // Afficher le formulaire quand on clique sur "Ajouter un avion"
            $('#showAvionFormBtn').click(function() {
                $('#avionForm').show();
                $('#avionsTableContainer, #noAvionsAlert').hide();
                $('#showAvionFormBtn').hide();
                $('#avion_id').val(''); // Reset l'ID pour une nouvelle création
                $('#formActionText').text(@json(__('trans.save')));
                $('#avionForm')[0].reset(); // Reset le formulaire
                $('.invalid-feedback').text(''); // Effacer les messages d'erreur
                $('.is-invalid').removeClass('is-invalid'); // Enlever les classes d'erreur
            });

            // Cacher le formulaire quand on clique sur Annuler
            $('#cancelAvionFormBtn').click(function() {
                $('#avionForm').hide();
                $('#avionsTableContainer, #noAvionsAlert').show();
                $('#showAvionFormBtn').show();
            });

            // Quand on clique sur Modifier pour un avion existant
            $(document).on('click', '.edit-avion', function() {
                const avionId = $(this).data('id');
                const immatriculation = $(this).data('immatriculation');
                // Remplir le formulaire avec les données de l'avion
                $('#avion_id').val(avionId);

                // Vider et ajouter l'immatriculation dans le select
                $('#immatriculations_select').empty().trigger('change');
                const newOption = new Option(immatriculation, immatriculation, true, true);
                $('#immatriculations_select').append(newOption).trigger('change');
                $('#type_avion_id').val($(this).data('type_avion_id')).trigger('change');
                $('#compagnie_aerienne_id').val($(this).data('compagnie_aerienne_id')).trigger('change');

                // Afficher le formulaire
                $('#avionForm').show();
                $('#avionsTableContainer, #noAvionsAlert').hide();
                $('#showAvionFormBtn').hide();
                $('#formActionText').text(@json(__('trans.update')));

                // Scroller vers le formulaire
                $('html, body').animate({
                    scrollTop: $('#avionForm').offset().top
                }, 500);
            });
            // Suppression d'un avion
            $(document).on('click', '.delete-avion', function() {
                const avionId = $(this).data('id');

                Swal.fire({
                    title: @json(__('trans.confirm_delete_title')),
                    text: @json(__('trans.confirm_delete_text')),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: @json(__('trans.confirm_delete_yes')),
                    cancelButtonText: @json(__('trans.cancel'))
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/user/avions/' + avionId,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: @json(__('trans.success')),
                                    text: response.message,
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',                                        title: @json(__('trans.error')),
                                        text: xhr.responseJSON.message ||
                                        @json(__('trans.error_occurred'))
                                });
                            }
                        });
                    }
                });
            });
            // Après soumission réussie du formulaire
            $('#avionForm').submit(function(e) {
                e.preventDefault();

                const formData = $(this).serialize();
                const url = $('#avion_id').val() ? '/user/avions/' + $('#avion_id').val() :
                    '/user/avions';
                const method = $('#avion_id').val() ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        // Cacher le formulaire et recharger la liste
                        $('#avionForm').hide();
                        $('#showAvionFormBtn').show();
                        location.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#${key}_error`).text(value[0]);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: @json(__('trans.error')),
                                text: xhr.responseJSON.message ||
                                    @json(__('trans.error_occurred'))
                            });
                        }
                    }
                });
            });
        });
    </script>@endpush
