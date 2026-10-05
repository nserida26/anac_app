@push('css')
    <style>
        /* Formulaire vol : blocs Départ / Arrivée adoucis */
        #volForm .vol-block {
            padding: 1rem;
            margin: 0 0 1rem;
            border: 1px solid var(--anac-gray-200);
            border-left: 4px solid var(--anac-accent);
            border-radius: var(--anac-radius-sm);
            background: var(--anac-gray-50);
        }

        #volForm .vol-block--arrivee {
            border-left-color: var(--anac-primary-light);
        }

        /* Sous-bloc « Aéroports intermédiaires » */
        #volForm .vol-subcard {
            border: 1px solid var(--anac-gray-200);
            border-radius: var(--anac-radius-sm);
            overflow: hidden;
        }

        #volForm .vol-subcard__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            padding: 0.85rem 1rem;
            background: var(--anac-white);
            border-bottom: 1px solid var(--anac-gray-200);
        }

        #volForm .vol-subcard__title {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--anac-primary);
        }

        #volForm .vol-subcard__title i {
            color: var(--anac-accent);
        }

        #volForm .vol-subcard__body {
            padding: 1rem;
            background: var(--anac-gray-50);
        }

        #escalesContainer hr {
            margin: 0.75rem 0;
            border-top: 1px solid var(--anac-gray-200);
        }

        #escalesContainer hr:last-child {
            display: none;
        }

        /* Barre d'actions du formulaire */
        #volForm .vol-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        #aeroport_depart_id+.select2-container,
        #aeroport_arrivee_id+.select2-container {
            width: 100% !important;
            min-width: 100%;
        }

        @media (max-width: 575.98px) {
            #volForm .vol-actions {
                flex-direction: column-reverse;
            }

            #volForm .vol-actions .anac-btn {
                width: 100%;
            }
        }
    </style>
@endpush

<!-- Information sur le vol -->
<div class="card">
    <x-anac-card-header icon="fas fa-plane" :title="__('trans.flight_info')"
        :count="isset($vols) && $vols->isNotEmpty() ? $vols->count() : null" count-icon="fas fa-plane-departure">
        <x-slot name="actions">
            <button type="button" class="anac-btn anac-btn--primary" id="showVolFormBtn">
                <i class="fas fa-plus"></i> @lang('trans.add_flight')
            </button>
        </x-slot>
    </x-anac-card-header>

    <div class="card-body">
        <!-- Formulaire (caché par défaut) -->
        <form method="POST" id="volForm" style="display: none;">
            @csrf
            <input type="hidden" name="vol_id" id="vol_id" value="">
            <input type="hidden" name="demande_autorisation_id" id="demande_autorisation_id"
                value="{{ $demandeAutorisation->id }}">

            <div class="auth-alert auth-alert--info align-items-center mb-3">
                <i class="fas fa-info-circle"></i>
                <span class="flex-grow-1">@lang('trans.airport_not_listed_hint')</span>
                <button type="button" class="btn btn-sm anac-action anac-action--blue" id="addAeroportBtn">
                    <i class="fas fa-plus"></i> @lang('trans.add_action')
                </button>
            </div>

            <div class="row">
                <!-- Numéro de vol -->
                {{-- Toujours affiché, y compris pour Block Permit (type 3) où c'est le numéro de vol
                     qui doit figurer, pas le nombre de passagers. --}}
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="numero_vol" class="form-label">@lang('trans.flight_number')</label>
                        <input type="text" class="form-control" id="numero_vol" name="numero_vol">
                        <div class="invalid-feedback" id="numero_vol_error"></div>
                    </div>
                </div>
                @if (in_array($demandeAutorisation->type->id, [2, 4, 5, 7]))
                    <!-- Nombre de passagers -->
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="nbr_passagers" id="nbr_passagers_label"
                                class="form-label">@lang('trans.nb_passagers')</label>
                            <input type="number" class="form-control" id="nbr_passagers" name="nbr_passagers"
                                min="0">
                            <div class="invalid-feedback" id="nbr_passagers_error"></div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="row vol-block">
                <!-- Aéroport de départ -->
                <div class="col-lg-6 col-md-5 mb-3">
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-1">
                            <label class="form-label mb-0">@lang('trans.start_aeroport') <span
                                    class="text-danger">*</span></label>
                            <div class="anac-segmented">
                                <div class="form-check">
                                    <input class="form-check-input lieu-type-radio" type="radio"
                                        name="type_lieu_depart" id="type_lieu_depart_aeroport" value="aeroport"
                                        data-field="depart" checked>
                                    <label class="form-check-label" for="type_lieu_depart_aeroport">
                                        <i class="fas fa-plane"></i> @lang('trans.airport')
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input lieu-type-radio" type="radio"
                                        name="type_lieu_depart" id="type_lieu_depart_piste" value="piste"
                                        data-field="depart">
                                    <label class="form-check-label" for="type_lieu_depart_piste">
                                        <i class="fas fa-road"></i> @lang('trans.runway')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="aeroport_depart_wrapper">
                            <select class="form-control select2_aeroports" id="aeroport_depart_id"
                                name="aeroport_depart_id" required>
                                @foreach ($aeroports as $aeroport)
                                    <option value="{{ $aeroport->id }}">{{ $aeroport->codeICAO }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="aeroport_depart_id_error"></div>
                        </div>
                        <div id="piste_depart_wrapper" style="display:none;">
                            <input type="text" class="form-control" id="nom_piste_depart" name="nom_piste_depart"
                                maxlength="255" placeholder="{{ __('trans.runway_name') }}">
                            <div class="invalid-feedback" id="nom_piste_depart_error"></div>
                        </div>
                    </div>
                </div>

                <!-- Heure de départ -->
                <div class="col-lg-6 col-md-3 mb-3">
                    <div class="form-group">
                        <label for="date_depart" class="form-label">@lang('trans.departure_time') <span
                                class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="date_depart" name="date_depart" required>
                        <div class="invalid-feedback" id="date_depart_error"></div>
                    </div>
                </div>
            </div>

            <div class="row vol-block vol-block--arrivee">
                <!-- Aéroport d'arrivée -->
                <div class="col-lg-6 col-md-5 mb-3">
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-1">
                            <label class="form-label mb-0">@lang('trans.end_aeroport')<span
                                    class="text-danger">*</span></label>
                            <div class="anac-segmented">
                                <div class="form-check">
                                    <input class="form-check-input lieu-type-radio" type="radio"
                                        name="type_lieu_arrivee" id="type_lieu_arrivee_aeroport" value="aeroport"
                                        data-field="arrivee" checked>
                                    <label class="form-check-label" for="type_lieu_arrivee_aeroport">
                                        <i class="fas fa-plane-arrival"></i> @lang('trans.airport')
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input lieu-type-radio" type="radio"
                                        name="type_lieu_arrivee" id="type_lieu_arrivee_piste" value="piste"
                                        data-field="arrivee">
                                    <label class="form-check-label" for="type_lieu_arrivee_piste">
                                        <i class="fas fa-road"></i> @lang('trans.runway')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="aeroport_arrivee_wrapper">
                            <select class="form-control select2_aeroports" id="aeroport_arrivee_id"
                                name="aeroport_arrivee_id" required>
                                @foreach ($aeroports as $aeroport)
                                    <option value="{{ $aeroport->id }}">{{ $aeroport->codeICAO }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="aeroport_arrivee_id_error"></div>
                        </div>
                        <div id="piste_arrivee_wrapper" style="display:none;">
                            <input type="text" class="form-control" id="nom_piste_arrivee" name="nom_piste_arrivee"
                                maxlength="255" placeholder="{{ __('trans.runway_name') }}">
                            <div class="invalid-feedback" id="nom_piste_arrivee_error"></div>
                        </div>
                    </div>
                </div>

                <!-- Heure d'arrivée -->
                <div class="col-lg-6 col-md-4 mb-3">
                    <div class="form-group">
                        <label for="date_arrivee" class="form-label">@lang('trans.arrival_time') <span
                                class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="date_arrivee" name="date_arrivee" required>
                        <div class="invalid-feedback" id="date_arrivee_error"></div>
                    </div>
                </div>
            </div>

            <!-- Section pour les escales intermédiaires -->
            <div class="row">
                <div class="col-md-12">
                    <div class="vol-subcard mb-4">
                        <div class="vol-subcard__head">
                            <h3 class="vol-subcard__title">
                                <i class="fas fa-route"></i> @lang('trans.intermediate_airports')
                            </h3>
                            <button type="button" class="btn btn-sm anac-action anac-action--blue" id="addEscaleBtn">
                                <i class="fas fa-plus"></i> @lang('trans.add_intermediate_airport')
                            </button>
                        </div>
                        <div class="vol-subcard__body" id="escalesContainer">
                            <!-- Les escales seront ajoutées dynamiquement ici -->
                            <div class="auth-alert auth-alert--info mb-0" id="noEscalesAlert">
                                @lang('trans.no_intermediate_airports')
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="vol-actions">
                        <button type="button" class="anac-btn anac-btn--ghost" id="cancelVolFormBtn">
                            <i class="fas fa-times"></i> @lang('trans.cancel')
                        </button>
                        <button type="submit" class="anac-btn anac-btn--primary" id="submitVolBtn">
                            <i class="fas fa-save"></i> <span id="volFormAction">@lang('trans.save')</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Tableau des vols existants -->
        @if (isset($vols) && $vols->isNotEmpty())
            <div class="mt-4" id="volsTableContainer">
                <div class="table-responsive">
                    <table class="table" id="volsTable">
                        <thead>
                            <tr>
                                <th>@lang('trans.flight_number')</th>
                                <th>@lang('trans.start_aeroport')</th>
                                <th>@lang('trans.end_aeroport')</th>
                                <th>@lang('trans.departure_time')</th>
                                <th>@lang('trans.arrival_time')</th>
                                <th>@lang('trans.nb_passagers')</th>
                                <th>@lang('trans.itinerary')</th>
                                <th>@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vols as $volItem)
                                @php
                                    // Récupérer les escales pour ce vol
                                    $escales = $volItem->escales()->orderBy('ordre')->get();
                                    $routeString =
                                        optional($volItem->aeroportDepart)->codeICAO ??
                                        ($volItem->nom_piste_depart ?? __('trans.not_available'));
                                    if ($escales->isNotEmpty()) {
                                        foreach ($escales as $escale) {
                                            $routeString .= ' → ' . $escale->aeroport->codeICAO;
                                        }
                                    }
                                    $routeString .=
                                        ' → ' .
                                        (optional($volItem->aeroportArrivee)->codeICAO ??
                                            ($volItem->nom_piste_arrivee ?? __('trans.not_available')));
                                @endphp
                                <tr id="vol-{{ $volItem->id }}">
                                    <td>{{ $volItem->numero_vol }}</td>
                                    <td>{{ optional($volItem->aeroportDepart)->codeICAO ?? ($volItem->nom_piste_depart ?? __('trans.not_available')) }}
                                    </td>
                                    <td>{{ optional($volItem->aeroportArrivee)->codeICAO ?? ($volItem->nom_piste_arrivee ?? __('trans.not_available')) }}
                                    </td>
                                    <td>{{ date('H:i', strtotime($volItem->date_depart)) }}</td>
                                    <td>{{ date('H:i', strtotime($volItem->date_arrivee)) }}</td>
                                    <td>{{ $volItem->nbr_passagers }}</td>
                                    <td>
                                        <small class="text-muted">{{ $routeString }}</small><br>
                                        <small>
                                            @if ($escales->isNotEmpty())
                                                @foreach ($escales as $escale)
                                                    {{ date('H:i', strtotime($escale->date_arrivee)) }}
                                                    {{ $escale->aeroport->codeICAO }}
                                                    {{ date('H:i', strtotime($escale->date_depart)) }}
                                                    @if (!$loop->last)
                                                        →
                                                    @endif
                                                @endforeach
                                            @else
                                                @lang('trans.no_intermediate_airports')
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <div class="anac-actions">
                                            <button class="btn btn-sm anac-action anac-action--yellow edit-vol"
                                                data-id="{{ $volItem->id }}"
                                                data-numero_vol="{{ $volItem->numero_vol }}"
                                                data-aeroport_depart_id="{{ $volItem->aeroport_depart_id }}"
                                                data-aeroport_arrivee_id="{{ $volItem->aeroport_arrivee_id }}"
                                                data-nom_piste_depart="{{ $volItem->nom_piste_depart }}"
                                                data-nom_piste_arrivee="{{ $volItem->nom_piste_arrivee }}"
                                                data-date_depart="{{ $volItem->date_depart }}"
                                                data-date_arrivee="{{ $volItem->date_arrivee }}"
                                                data-nbr_passagers="{{ $volItem->nbr_passagers }}"
                                                data-objet="{{ $volItem->objet_vol }}"
                                                data-escales="{{ json_encode($volItem->escales) }}">
                                                <i class="fas fa-edit"></i> @lang('trans.edit')
                                            </button>
                                            <button class="btn btn-sm anac-action anac-action--red delete-vol"
                                                data-id="{{ $volItem->id }}">
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
        @else
            <div class="auth-alert auth-alert--info mb-0" id="noVolsAlert">
                <i class="fas fa-info-circle"></i>
                @lang('trans.no_flights_registered')
            </div>
        @endif
    </div>
</div>

@push('custom')
    <script>
        $(document).ready(function() {
            // Dans votre script, modifiez la fonction updatePassengerField
            function updatePassengerField() {
                // Récupérer le type de vol depuis le sélecteur ou depuis les données PHP
                let typeVol = null;

                // Si nous sommes en édition, essayer de récupérer depuis les données PHP
                @php
                    $typeVolId = null;
                    if ($demandeAutorisation->type_demande_autorisation_id == 3) {
                        $firstTypeVol = $demandeAutorisation->type_vols_list->first();
                        $typeVolId = $firstTypeVol ? $firstTypeVol->id : null;
                    } else {
                        $typeVolId = $demandeAutorisation->typeVol->id ?? null;
                    }
                @endphp

                const initialTypeVol = {!! json_encode($typeVolId) !!};
                const passagerField = $('#nbr_passagers');
                const passagerFieldLabel = $('#nbr_passagers_label');


                // Si nous avons un champ type_vol_id dans le formulaire (pour l'édition)
                if ($('#type_vol_id').length && $('#type_vol_id').val()) {
                    typeVol = $('#type_vol_id').val();
                } else if (initialTypeVol) {
                    typeVol = initialTypeVol;
                }

                if (typeVol && [2, 8, 10, 9, 7].includes(parseInt(typeVol))) {
                    passagerField.show();
                    passagerFieldLabel.show();
                    passagerField.prop('required', true);
                } else {
                    passagerField.hide();
                    passagerFieldLabel.hide();
                    passagerField.prop('required', false);
                    passagerField.val('');
                }
            }

            updatePassengerField();
        });
    </script>
    <script>
        // Déclarer les aéroports en JavaScript
        @php
            $aeroportsJs = $aeroports->map(function ($aeroport) {
                return [
                    'id' => $aeroport->id,
                    'codeICAO' => $aeroport->codeICAO,
                    'nom' => $aeroport->nom,
                ];
            });
        @endphp
        const aeroports = @json($aeroportsJs);
        $(document).ready(function() {

            // Variables globales
            let escaleCounter = 0;
            let currentEscalesData = []; // Pour garder une trace des escales existantes

            // Fonction pour formater la time 
            function formatTime(timeString) {
                return timeString ? timeString.substring(0, 5) : '';
            }

            // Template pour une escale
            function getEscaleTemplate(counter, escaleData = null) {
                const isEditMode = escaleData !== null;
                const aeroportId = isEditMode ? escaleData.aeroport_id : '';
                const dateArrivee = isEditMode ? formatTime(escaleData.date_arrivee) : '';
                const dateDepart = isEditMode ? formatTime(escaleData.date_depart) : '';
                const escaleId = isEditMode ? escaleData.id : '';

                // Construire les options des aéroports
                let options = '<option value="">{{ __('trans.select_placeholder') }}</option>';
                if (typeof aeroports !== 'undefined') {
                    aeroports.forEach(aeroport => {
                        const selected = isEditMode && aeroportId == aeroport.id ? 'selected' : '';
                        options +=
                            `<option value="${aeroport.id}" ${selected}>${aeroport.codeICAO}</option>`;
                    });
                }

                return `
            <div class="row escale-row" id="escale-row-${counter}" data-escale-id="${escaleId}">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="escale_aeroport_${counter}">{{ __('trans.aeroport') }}</label>
                        <select class="form-control select2 escale-aeroport" 
                                id="escale_aeroport_${counter}" 
                                name="escales[${counter}][aeroport_id]"
                                data-counter="${counter}" required>
                            ${options}
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="escale_arrivee_${counter}">{{ __('trans.arrival_time') }}</label>
                        <input type="time" class="form-control escale-arrivee" 
                               id="escale_arrivee_${counter}" 
                               name="escales[${counter}][date_arrivee]"
                               value="${dateArrivee}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="escale_depart_${counter}">{{ __('trans.departure_time') }}</label>
                        <input type="time" class="form-control escale-depart" 
                               id="escale_depart_${counter}" 
                               name="escales[${counter}][date_depart]"
                               value="${dateDepart}" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-sm anac-action anac-action--red w-100 remove-escale" 
                                data-counter="${counter}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                ${isEditMode && escaleId ? `<input type="hidden" name="escales[${counter}][id]" value="${escaleId}">` : ''}
            </div>
            <hr>
        `;
            }

            // Ajouter une escale
            $('#addEscaleBtn').click(function() {
                $('#noEscalesAlert').hide();
                escaleCounter++;
                const template = getEscaleTemplate(escaleCounter);
                $('#escalesContainer').append(template);

                // Initialiser Select2 pour le nouvel élément
                setTimeout(() => {
                    $(`#escale_aeroport_${escaleCounter}`).select2({
                        theme: 'bootstrap4',
                        placeholder: @json(__('trans.select_airport')),
                        allowClear: true
                    });
                }, 50);
            });

            // Supprimer une escale
            $(document).on('click', '.remove-escale', function() {
                const counter = $(this).data('counter');
                $(`#escale-row-${counter}`).next('hr').remove();
                $(`#escale-row-${counter}`).remove();

                // Si plus d'escales, afficher le message
                if ($('.escale-row').length === 0) {
                    $('#noEscalesAlert').show();
                }
            });

            // Fonction pour charger les escales lors de l'édition
            function loadEscalesForEdit(escalesData) {
                $('#escalesContainer').empty();
                escaleCounter = 0;
                currentEscalesData = escalesData || [];

                if (currentEscalesData.length > 0) {
                    $('#noEscalesAlert').hide();
                    currentEscalesData.forEach((escale, index) => {
                        escaleCounter = index + 1;
                        const template = getEscaleTemplate(escaleCounter, {
                            id: escale.id,
                            aeroport_id: escale.aeroport_id,
                            date_arrivee: escale.date_arrivee,
                            date_depart: escale.date_depart
                        });
                        $('#escalesContainer').append(template);

                        // Initialiser Select2
                        setTimeout(() => {
                            $(`#escale_aeroport_${escaleCounter}`).select2({
                                theme: 'bootstrap4',
                                placeholder: @json(__('trans.select_airport')),
                                allowClear: true
                            });
                        }, 50);
                    });
                } else {
                    $('#noEscalesAlert').show();
                }
            }

            // Fonction pour formater les données des escales
            function getEscalesData() {
                const escalesData = [];
                $('.escale-row').each(function(index) {
                    const escaleId = $(this).data('escale-id');
                    const aeroportId = $(this).find('.escale-aeroport').val();
                    const dateArrivee = $(this).find('.escale-arrivee').val();
                    const dateDepart = $(this).find('.escale-depart').val();

                    if (aeroportId && dateArrivee && dateDepart) {
                        escalesData.push({
                            id: escaleId || null,
                            aeroport_id: aeroportId,
                            date_arrivee: dateArrivee,
                            date_depart: dateDepart,
                            ordre: index + 1
                        });
                    }
                });
                return escalesData;
            }

            // Validation des heures des escales
            function validateEscales() {
                let isValid = true;
                let previousTime = $('#date_depart').val();

                if (!previousTime) {
                    toastr.error(@json(__('trans.fill_departure_time_first')));
                    return false;
                }

                // Valider les escales dans l'ordre
                $('.escale-row').each(function() {
                    const arrivee = $(this).find('.escale-arrivee').val();
                    const depart = $(this).find('.escale-depart').val();
                    const aeroport = $(this).find('.escale-aeroport').val();

                    if (!aeroport) {
                        toastr.error(@json(__('trans.select_airport_for_stopovers')));
                        isValid = false;
                        return false;
                    }

                    if (!arrivee || !depart) {
                        toastr.error(@json(__('trans.fill_all_stopover_times')));
                        isValid = false;
                        return false;
                    }

                    //if (arrivee <= previousTime) {
                    //  toastr.error('L\'heure d\'arrivée de l\'escale doit être après l\'heure précédente');
                    //isValid = false;
                    //return false;
                    //}

                    //if (depart <= arrivee) {
                    //  toastr.error('L\'heure de départ de l\'escale doit être après l\'heure d\'arrivée');
                    //isValid = false;
                    //return false;
                    //}

                    previousTime = depart;
                });

                // Vérifier que l'heure d'arrivée finale est après la dernière escale
                //const finalArrival = $('#date_arrivee').val();
                //if (finalArrival && finalArrival <= previousTime) {
                //  toastr.error('L\'heure d\'arrivée finale doit être après la dernière escale');
                //isValid = false;
                //}

                return isValid;
            }

            // Bascule Aéroport / Piste pour le départ et l'arrivée
            function setLieuMode(field, mode) {
                const aeroportWrapper = $('#aeroport_' + field + '_wrapper');
                const pisteWrapper = $('#piste_' + field + '_wrapper');
                const aeroportSelect = $('#aeroport_' + field + '_id');
                const nomPisteInput = $('#nom_piste_' + field);

                if (mode === 'piste') {
                    aeroportWrapper.hide();
                    pisteWrapper.show();
                    aeroportSelect.prop('required', false).val(null).trigger('change');
                    nomPisteInput.prop('required', true);
                } else {
                    pisteWrapper.hide();
                    aeroportWrapper.show();
                    aeroportSelect.prop('required', true);
                    nomPisteInput.prop('required', false).val('');
                }
            }

            $(document).on('change', '.lieu-type-radio', function() {
                setLieuMode($(this).data('field'), $(this).val());
            });

            // Afficher le formulaire quand on clique sur "Ajouter un vol"

            $('#showVolFormBtn').click(function() {
                $('#volForm').show();
                $('#volsTableContainer, #noVolsAlert').hide();
                $('#showVolFormBtn').hide();
                $('#vol_id').val('');
                $('#volFormAction').text(@json(__('trans.save')));
                $('#volForm')[0].reset();
                $('.invalid-feedback').text('');
                $('.is-invalid').removeClass('is-invalid');

                // Réinitialiser le choix Aéroport/Piste
                $('#type_lieu_depart_aeroport, #type_lieu_arrivee_aeroport').prop('checked', true);
                setLieuMode('depart', 'aeroport');
                setLieuMode('arrivee', 'aeroport');

                // Réinitialiser les escales
                $('#escalesContainer').empty();
                escaleCounter = 0;
                currentEscalesData = [];
                $('#noEscalesAlert').show();

                // Réinitialiser Select2
                $('.select2').val(null).trigger('change');
            });

            // Cacher le formulaire
            $('#cancelVolFormBtn').click(function() {
                $('#volForm').hide();
                $('#volsTableContainer, #noVolsAlert').show();
                $('#showVolFormBtn').show();
            });

            // Quand on clique sur Modifier pour un vol existant
            $(document).on('click', '.edit-vol', function() {
                const volId = $(this).data('id');

                // Remplir le formulaire
                $('#vol_id').val(volId);
                $('#numero_vol').val($(this).data('numero_vol'));

                // Départ : Aéroport ou Piste
                const departAeroportId = $(this).data('aeroport_depart_id');
                if (departAeroportId) {
                    $('#type_lieu_depart_aeroport').prop('checked', true);
                    setLieuMode('depart', 'aeroport');
                    $('#aeroport_depart_id').val(departAeroportId).trigger('change');
                } else {
                    $('#type_lieu_depart_piste').prop('checked', true);
                    setLieuMode('depart', 'piste');
                    $('#nom_piste_depart').val($(this).data('nom_piste_depart'));
                }

                // Arrivée : Aéroport ou Piste
                const arriveeAeroportId = $(this).data('aeroport_arrivee_id');
                if (arriveeAeroportId) {
                    $('#type_lieu_arrivee_aeroport').prop('checked', true);
                    setLieuMode('arrivee', 'aeroport');
                    $('#aeroport_arrivee_id').val(arriveeAeroportId).trigger('change');
                } else {
                    $('#type_lieu_arrivee_piste').prop('checked', true);
                    setLieuMode('arrivee', 'piste');
                    $('#nom_piste_arrivee').val($(this).data('nom_piste_arrivee'));
                }

                $('#date_depart').val(formatTime($(this).data('date_depart')));
                $('#date_arrivee').val(formatTime($(this).data('date_arrivee')));
                $('#nbr_passagers').val($(this).data('nbr_passagers'));
                $('#objet').val($(this).data('objet'));

                // Charger les escales
                let escalesData = [];
                try {
                    const escalesJson = $(this).attr('data-escales');
                    escalesData = escalesJson ? JSON.parse(escalesJson) : [];
                } catch (e) {
                    console.error('Erreur parsing escales:', e);
                    escalesData = [];
                }

                loadEscalesForEdit(escalesData);

                // Afficher le formulaire
                $('#volForm').show();
                $('#volsTableContainer, #noVolsAlert').hide();
                $('#showVolFormBtn').hide();
                $('#volFormAction').text(@json(__('trans.update')));

                // Scroller vers le formulaire
                $('html, body').animate({
                    scrollTop: $('#volForm').offset().top
                }, 500);
            });

            $('#volForm').submit(function(e) {
                e.preventDefault();

                console.log('=== DÉBUT SOUMISSION ===');

                // Valider les escales
                if (!validateEscales()) {
                    return false;
                }

                // Récupérer les escales
                const escalesData = getEscalesData();
                console.log('Escales récupérées:', escalesData);

                // Créer FormData
                const formData = new FormData(this);

                // Ajouter le token CSRF
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                // Pour PUT
                const volId = $('#vol_id').val();
                if (volId) {
                    formData.append('_method', 'PUT');
                }

                // SUPPRIMER les anciennes entrées escales du FormData
                // (car elles sont ajoutées automatiquement par les inputs du formulaire)
                const entries = Array.from(formData.entries());
                entries.forEach(([key, value]) => {
                    if (key.startsWith('escales[')) {
                        formData.delete(key);
                    }
                });

                console.log('FormData après suppression des anciennes escales:');
                Array.from(formData.entries()).forEach(([key, value]) => {
                    console.log(`${key}: ${value}`);
                });

                // Ajouter les nouvelles escales
                escalesData.forEach((escale, index) => {
                    if (escale.id) {
                        formData.append(`escales[${index}][id]`, escale.id);
                    }
                    formData.append(`escales[${index}][aeroport_id]`, escale.aeroport_id);
                    formData.append(`escales[${index}][date_arrivee]`, escale.date_arrivee);
                    formData.append(`escales[${index}][date_depart]`, escale.date_depart);
                    formData.append(`escales[${index}][ordre]`, escale.ordre || (index + 1));
                });

                console.log('FormData final avec escales:');
                Array.from(formData.entries()).forEach(([key, value]) => {
                    console.log(`${key}: ${value}`);
                });

                const url = volId ? '/user/vols/' + volId : '/user/vols';

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#submitVolBtn').prop('disabled', true).html(
                            '<i class="fas fa-spinner fa-spin"></i> ' + @json(__('trans.saving')));
                    },
                    success: function(response) {
                        console.log('Réponse réussie:', response);
                        toastr.success(response.message || @json(__('trans.saved_success')));
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        console.error('Erreur AJAX:', xhr);
                        console.error('Réponse:', xhr.responseJSON);

                        if (xhr.status === 419) {
                            toastr.error(
                                @json(__('trans.session_expired'))
                            );
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            console.error('Erreurs de validation:', errors);
                            $.each(errors, function(key, value) {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#${key}_error`).text(value[0]);
                            });
                            toastr.error(@json(__('trans.fix_form_errors')));
                        } else {
                            toastr.error(xhr.responseJSON?.message ||
                                @json(__('trans.error_occurred')));
                        }
                    },
                    complete: function() {
                        $('#submitVolBtn').prop('disabled', false).html(
                            '<i class="fas fa-save"></i> ' + $('#volFormAction').text());
                    }
                });

                console.log('=== FIN SOUMISSION ===');
            });


            // Suppression d'un vol
            $(document).on('click', '.delete-vol', function() {
                const volId = $(this).data('id');

                Swal.fire({
                    title: @json(__('trans.confirm_delete_title')),
                    text: @json(__('trans.confirm_delete_text')),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: @json(__('trans.confirm_delete_yes')),
                    cancelButtonText: @json(__('trans.cancel'))
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/user/vols/' + volId,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire(
                                    @json(__('trans.success')),
                                    @json(__('trans.deleted_success')),
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: @json(__('trans.error')),
                                    text: xhr.responseJSON.message ||
                                        @json(__('trans.delete_error'))
                                });
                            }
                        });
                    }
                });
            });



            // Initialiser Select2
            $('.select2').select2();
            $('.select2_aeroports').select2({
                width: '100%',
                matcher: function(params, data) {
                    if (!params.term || params.term.trim() === '') {
                        return data;
                    }
                    const searchTerm = params.term.trim().toUpperCase();
                    const optionText = data.text.toUpperCase();
                    if (optionText.startsWith(searchTerm)) {
                        return data;
                    }
                    return null;
                },
                minimumInputLength: 1
            });
            $('.select2-pays').select2({
                width: '100%',
                dropdownParent: $('#addAeroportModal')
            });
        });
    </script>@endpush
