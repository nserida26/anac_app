
@push('custom')
    <script>
        $(document).ready(function() {
            // Initialize date and time pickers
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true,
                language: 'fr'
            });

            $('.timepicker').timepicker({
                showMeridian: false,
                minuteStep: 1
            });

        });
    </script>
    <script>
            // Toggle edit forms
            function toggleEditForm(id, type) {
                $(`#${type}-${id}`).toggle();
                $(`#edit-form-${type}-${id}`).toggle();
            }
    </script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            console.log('Document ready - Initialisation Select2');

            // Initialiser Select2 pour les tags (immatriculations multiples)
            $('#immatriculations_select').select2({
                theme: 'bootstrap4',
                placeholder: "Tapez une immatriculation et appuyez sur Entrée",
                tags: true,
                tokenSeparators: [',', ' ', '\n'],
                allowClear: true,
                width: '100%',
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }

                    // Valider le format (lettres, chiffres, tirets)
                    if (!/^[A-Z0-9\-]+$/i.test(term)) {
                        return null;
                    }

                    return {
                        id: term.toUpperCase(),
                        text: term.toUpperCase(),
                        newTag: true
                    };
                },
                insertTag: function(data, tag) {
                    data.push(tag);
                },
                language: {
                    noResults: function() {
                        return @json(__('trans.no_results'));
                    },
                    searching: function() {
                        return @json(__('trans.searching'));
                    }
                }
            }).on('change', function() {
                console.log('Select2 change event');
                updatePreview();
            });

            // Initialiser Select2 pour les selects simples
            $('.select2-single').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: function() {
                    return $(this).data('placeholder') || @json(__('trans.select_placeholder'));
                }
            }).on('change', function() {
                console.log('Select2 single change event');
                updatePreview();
            });

            // Fonction de mise à jour de la prévisualisation
            function updatePreview() {
                const immatriculations = $('#immatriculations_select').val() || [];
                const typeId = $('#type_avion_id').val();
                const operatorId = $('#compagnie_aerienne_id').val();

                console.log('Preview update - Immatriculations:', immatriculations);
                console.log('Type ID:', typeId, 'Operator ID:', operatorId);

                if (immatriculations.length > 0 && typeId && operatorId) {
                    // Afficher le type sélectionné
                    const typeOption = $('#type_avion_id option:selected');
                    const typeText = typeOption.data('code') + ' (' + typeOption.data('capacite') + ' places)';
                    $('#selectedTypeDisplay').text(typeText);

                    // Afficher l'opérateur sélectionné
                    const operatorOption = $('#compagnie_aerienne_id option:selected');
                    const operatorText = operatorOption.data('code') ?
                        operatorOption.data('code') + ' ' + operatorOption.text() :
                        operatorOption.text();
                    $('#selectedOperatorDisplay').text(operatorText);

                    // Afficher les immatriculations
                    let preview = '';
                    immatriculations.forEach(function(imm) {
                        preview += '<span class="preview-badge">' + imm + '</span> ';
                    });

                    $('#immatriculationsPreview').html(preview);
                    $('#totalCount').text(immatriculations.length);
                    $('#previewSection').fadeIn(300);
                } else {
                    $('#previewSection').fadeOut(300);
                }
            }
            // Fonction de réinitialisation
            function resetAvionForm() {
                $('#avionForm')[0].reset();
                $('#avion_id').val('');
                $('#immatriculations_select').empty().trigger('change');
                $('#type_avion_id').val('').trigger('change');
                $('#compagnie_aerienne_id').val('').trigger('change');
                $('#formActionText').text(@json(__('trans.send')));

                $('#previewSection').fadeOut(300);

                // Supprimer les champs cachés ajoutés
                $('#avionForm input[name="immatriculations_list[]"]').remove();
            }

            // Initialisation des tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Test: Vérifier que Select2 est bien initialisé
            console.log('Select2 initialized:', $('#immatriculations_select').data('select2') ? 'Yes' : 'No');
        });
    </script>
@endpush
