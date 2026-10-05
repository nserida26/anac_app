    <!-- Add Aeroport Modal -->
    <div class="modal fade" id="addAeroportModal" tabindex="-1" role="dialog" aria-labelledby="addAeroportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAeroportModalLabel">@lang('trans.add_new_airport')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addAeroportForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom">@lang('trans.airport_name')</label>
                                    <input type="text" class="form-control" id="nom" name="nom"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="codeIATA">@lang('trans.iata_code')</label>
                                    <input type="text" class="form-control" id="codeIATA" name="codeIATA"
                                        maxlength="3">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="codeICAO">@lang('trans.icao_code')</label>
                                    <input type="text" class="form-control" id="codeICAO" name="codeICAO"
                                        maxlength="4">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pays_id">@lang('trans.country')</label>
                                    <select class="form-control select2-pays" id="pays_id" name="pays_id" required>
                                        <option value="">@lang('trans.select_country')</option>
                                        @foreach ($pays as $pay)
                                            <option value="{{ $pay->id }}">{{ $pay->nom }}
                                                ({{ $pay->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ville">@lang('trans.city')</label>
                                    <input type="text" class="form-control" id="ville" name="ville">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude">@lang('trans.latitude')</label>
                                    <input type="number" step="any" class="form-control" id="latitude"
                                        name="latitude">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude">@lang('trans.longitude')</label>
                                    <input type="number" step="any" class="form-control" id="longitude"
                                        name="longitude">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">@lang('trans.cancel')</button>
                        <button type="submit" class="btn btn-primary">@lang('trans.save_airport')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter une compagnie -->
    <div class="modal fade" id="companyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('trans.new_operator')</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="companyForm">
                        @csrf
                        <div class="mb-3">
                            <label for="nom" class="form-label">@lang('trans.name_operator') <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom_entreprise" name="nom_entreprise"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label">@lang('trans.code')</label>
                            <input type="text" class="form-control" id="code" name="code">
                        </div>
                        <div class="form-group">
                            <label for="email">@lang('trans.email')</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="telephone">@lang('trans.phone')</label>
                            <input type="text" class="form-control" id="telephone" name="telephone">
                        </div>
                        <div class="form-group">
                            <label for="adresse">@lang('trans.address')</label>
                            <input type="text" class="form-control" id="adresse" name="adresse">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('trans.close')</button>
                    <button type="button" class="btn btn-primary" id="saveCompanyBtn">@lang('trans.save')</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pour ajouter un type d'avion -->
    <div class="modal fade" id="typeAvionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('trans.new_aircraft_type')</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="typeAvionForm">
                        @csrf
                        <div class="mb-3">
                            <label for="code" class="form-label">@lang('trans.code') <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="code_type" name="code" required>
                        </div>
                        <div class="mb-3">
                            <label for="capacite" class="form-label">@lang('trans.passenger_capacity')</label>
                            <input type="number" class="form-control" id="capacite" name="capacite"
                                min="0" value="0">
                        </div>
                        <div class="mb-3">
                            <label for="charge_max" class="form-label">@lang('trans.max_load_kg')</label>
                            <input type="number" class="form-control" id="charge_max" name="charge_max"
                                min="0" value="0">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('trans.close')</button>
                    <button type="button" class="btn btn-primary" id="saveTypeAvionBtn">@lang('trans.save')</button>
                </div>
            </div>
        </div>
    </div>

@push('custom')
    <script>
        $(document).ready(function() {
            $('#addAeroportBtn').click(function() {
                $('#addAeroportModal').modal('show');
            });
            // Handle form submission
            $('#addAeroportForm').submit(function(e) {
                e.preventDefault();

                // Get form data
                var formData = $(this).serialize();

                // Show loading state
                var submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> ' + @json(__('trans.saving')));

                // AJAX request
                $.ajax({
                    url: "{{ route('user.store_aeroports') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Hide modal
                        $('#addAeroportModal').modal('hide');

                        // Show success message
                        toastr.success(@json(__('trans.airport_added_success')));

                        // Reset form
                        $('#addAeroportForm')[0].reset();

                        var nouvelAeroport = response.aeroport;

                        // Ajouter la nouvelle option aux selects de départ/arrivée
                        $('#aeroport_depart_id, #aeroport_arrivee_id').append($('<option>', {
                            value: nouvelAeroport.id,
                            text: nouvelAeroport.codeICAO
                        }));
                        $('#aeroport_depart_id, #aeroport_arrivee_id').trigger('change');

                        // Ajouter le nouvel aéroport au tableau utilisé pour les escales
                        if (typeof aeroports !== 'undefined') {
                            aeroports.push({
                                id: nouvelAeroport.id,
                                codeICAO: nouvelAeroport.codeICAO,
                                nom: nouvelAeroport.nom
                            });
                        }

                        // Ajouter aussi l'option aux escales déjà affichées sur la page
                        $('.escale-aeroport').append($('<option>', {
                            value: nouvelAeroport.id,
                            text: nouvelAeroport.codeICAO
                        }));
                    },
                    error: function(xhr) {
                        // Show error message
                        var errorMessage = xhr.responseJSON.message ||
                            @json(__('trans.error_occurred'));
                        toastr.error(errorMessage);

                        // Highlight error fields
                        if (xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key).after('<div class="invalid-feedback">' +
                                    value[0] + '</div>');
                            });
                        }
                    },
                    complete: function() {
                        // Reset button state
                        submitButton.prop('disabled', false).html(@json(__('trans.save_airport')));
                    }
                });
            });

            // Clear validation errors when modal is hidden
            $('#addAeroportModal').on('hidden.bs.modal', function() {
                $('#addAeroportForm input, #addAeroportForm select').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            });

            // Auto-uppercase for IATA and ICAO codes
            $('#codeIATA, #codeICAO').keyup(function() {
                $(this).val($(this).val().toUpperCase());
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Gestion de l'ajout de type d'avion
            $('#addTypeAvionBtn').click(function() {
                $('#typeAvionModal').modal('show');
            });

            $('#saveTypeAvionBtn').click(function() {
                $.ajax({
                    url: "{{ route('user.store_type_avions') }}", // À adapter selon votre route
                    type: 'POST',
                    data: $('#typeAvionForm').serialize(),
                    success: function(response) {
                        // Ajouter la nouvelle option au select
                        $('#type_avion_id').append($('<option>', {
                            value: response.id,
                            text: response.code + ' (' + (response.data.capacite ||
                                0) + ' places)',
                            selected: true,
                            'data-code': response.code,
                            'data-capacite': response.data.capacite || 0
                        }));

                        // Réinitialiser Select2 pour afficher la nouvelle valeur
                        $('#type_avion_id').trigger('change');

                        $('#typeAvionModal').modal('hide');
                        $('#typeAvionForm')[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: 'Type d\'avion ajouté avec succès',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = '';

                        $.each(errors, function(key, value) {
                            errorMsg += value + '<br>';
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            html: errorMsg
                        });
                    }
                });
            });

            // Gestion de l'ajout de compagnie (code existant)
            $('#addCompanyBtn').click(function() {
                $('#companyModal').modal('show');
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            $('#saveCompanyBtn').click(function() {
                $.ajax({
                    url: "{{ route('user.store_compagnies') }}",
                    type: 'POST',
                    data: $('#companyForm').serialize(),
                    success: function(response) {
                        var nouvelleCompagnie = response.data;
                        var texteOption = nouvelleCompagnie.code ?
                            nouvelleCompagnie.code + ' ' + nouvelleCompagnie.nom_entreprise :
                            nouvelleCompagnie.nom_entreprise;

                        // Ajouter la nouvelle option au(x) select(s) présent(s) sur la page
                        // (avion "compagnie_aerienne_id" ou demande "demande_compagnie_id" selon le type)
                        $('#compagnie_aerienne_id, #demande_compagnie_id').each(function() {
                            $(this).append($('<option>', {
                                value: nouvelleCompagnie.id,
                                text: texteOption,
                                selected: true,
                                'data-code': nouvelleCompagnie.code
                            })).trigger('change');
                        });

                        $('#companyModal').modal('hide');
                        $('#companyForm')[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: 'Opérateur ajoutée avec succès',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = '';

                        $.each(errors, function(key, value) {
                            errorMsg += value + '<br>';
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            html: errorMsg
                        });
                    }
                });
            });
        });
    </script>
@endpush
