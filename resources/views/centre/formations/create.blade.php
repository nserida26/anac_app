{{-- resources/views/centre/formations/create.blade.php --}}
@extends('centre.layouts.app')

@section('title')
    @lang('trans.add_training')
@endsection

@section('contentheader')
    @lang('trans.add_training')
@endsection

@section('contentheaderlink')
    <a href="{{ route('centre.index') }}">@lang('trans.dashboard_center')</a>
@endsection

@section('contentheaderactive')
    @lang('trans.add_training')
@endsection

@push('css')

<style>
    .demandeur-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 20px;
        color: white;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .demandeur-avatar-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: bold;
        border: 3px solid white;
    }
    
    .demandeur-info-item {
        margin-bottom: 5px;
    }
    
    .demandeur-info-item i {
        width: 20px;
        margin-right: 10px;
    }
    
    .licence-list {
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 15px;
        margin-top: 15px;
    }
    
    .licence-item {
        padding: 8px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    
    .licence-item:last-child {
        border-bottom: none;
    }
    
    .search-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
        border: 1px solid #dee2e6;
    }
    
    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: #333;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
    }
    
    .section-title i {
        margin-right: 10px;
        color: #007bff;
    }
    
    .required-field::after {
        content: " *";
        color: red;
    }
    
    .search-result-item {
        padding: 15px;
        border-bottom: 1px solid #dee2e6;
        cursor: pointer;
        transition: background 0.3s;
        background: white;
    }
    
    .search-result-item:hover {
        background: #e9ecef;
    }
    
    .search-result-item:last-child {
        border-bottom: none;
    }
    
    .search-results {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: white;
        margin-top: 15px;
    }
    
    .btn-select-demande {
        padding: 5px 15px;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <form action="{{ route('centre.store') }}" method="POST" enctype="multipart/form-data" id="trainingForm">
        @csrf
        <input type="hidden" name="centre_formation_id" value="{{ $centre->id }}">
        <input type="hidden" name="demandeur_id" id="selected_demandeur_id" value="{{ old('demandeur_id', request('demandeur_id')) }}">
        
        {{-- Section 1: Recherche du détenteur de licence --}}
        <div class="row">
            <div class="col-md-12">
                <div class="search-section">
                    <div class="section-title">
                        <i class="fas fa-user-graduate"></i>
                        @lang('trans.select_licence_holder')
                    </div>
                    
                    {{-- Recherche par numéro de licence --}}
                    <div class="form-group">
                        <label class="required-field">@lang('trans.search_by_licence_number')</label>
                        <div class="input-group input-group-lg">
                            <input type="text" 
                                   class="form-control" 
                                   id="search_licence_input" 
                                   placeholder="@lang('trans.enter_licence_number')"
                                   autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="btn_search_licence">
                                    <i class="fas fa-search"></i> @lang('trans.search')
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i>
                            @lang('trans.enter_licence_number_help')
                        </small>
                    </div>
                    
                    {{-- Résultats de recherche --}}
                    <div id="search_results_container" style="display: none;">
                        <h6 class="mt-3 mb-2">
                            <i class="fas fa-list"></i> @lang('trans.search_results'):
                        </h6>
                        <div class="search-results" id="search_results"></div>
                    </div>
                    
                    {{-- Demandeur sélectionné --}}
                    <div id="selected_demandeur_display" style="display: {{ request('demandeur_id') ? 'block' : 'none' }};">
                        <div class="demandeur-card" id="demandeur_card">
                            <!-- Le contenu sera chargé dynamiquement -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Section 2: Détails de la formation --}}
        <div id="training_details_section" style="display: {{ request('demandeur_id') ? 'block' : 'none' }};">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chalkboard"></i>
                                @lang('trans.training_details')
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="required-field">@lang('trans.training_type')</label>
                                        <select class="form-control select2" name="type_formation_id" id="type_formation_id">
                                            <option value="">@lang('trans.select_training_type')</option>
                                            @foreach ($typeFormations as $type)
                                                <option value="{{ $type->id }}" {{ old('type_formation_id') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('trans.licence_type')</label>
                                        <select class="form-control select2" name="type_licence_id" id="type_licence_id">
                                            <option value="">@lang('trans.select_licence_type')</option>
                                            @foreach ($typeLicences as $licence)
                                                <option value="{{ $licence->id }}" {{ old('type_licence_id') == $licence->id ? 'selected' : '' }}>
                                                    {{ $licence->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($typeLicences->isEmpty())
                                            <small class="text-warning">
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                @lang('trans.no_active_licences')
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>@lang('trans.training_title')</label>
                                <input type="text" 
                                       class="form-control" 
                                       name="intitule_formation" 
                                       value="{{ old('intitule_formation') }}"
                                       placeholder="@lang('trans.enter_training_title')">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('trans.instructor')</label>
                                        <select class="form-control select2" name="instructeur_id" id="instructeur_id">
                                            <option value="">@lang('trans.select_instructor')</option>
                                            @foreach ($instructeurs as $instructeur)
                                                <option value="{{ $instructeur->id }}" {{ old('instructeur_id') == $instructeur->id ? 'selected' : '' }}>
                                                    {{ $instructeur->nom_complet }} ({{ $instructeur->numero_licence }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($instructeurs->isEmpty())
                                            <small class="text-warning">
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                @lang('trans.no_instructors')
                                                <a href="{{ route('centre.instructeurs') }}" target="_blank">
                                                    @lang('trans.add_instructor')
                                                </a>
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('trans.examiner')</label>
                                        <select class="form-control select2" name="examinateur_id" id="examinateur_id">
                                            <option value="">@lang('trans.select_examiner')</option>
                                            @foreach ($examinateurs as $examinateur)
                                                <option value="{{ $examinateur->id }}" {{ old('examinateur_id') == $examinateur->id ? 'selected' : '' }}>
                                                    {{ $examinateur->nom_complet }} ({{ $examinateur->numero_licence_examinateur }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($examinateurs->isEmpty())
                                            <small class="text-warning">
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                @lang('trans.no_validated_examiners')
                                                <a href="{{ route('centre.examinateurs') }}" target="_blank">
                                                    @lang('trans.add_examiner')
                                                </a>
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>@lang('trans.training_device')</label>
                                <select class="form-control select2" name="dispositif_formation_id" id="dispositif_formation_id">
                                    <option value="">@lang('trans.select_device')</option>
                                    @foreach ($dispositifs as $dispositif)
                                        <option value="{{ $dispositif->id }}" {{ old('dispositif_formation_id') == $dispositif->id ? 'selected' : '' }}>
                                            {{ $dispositif->simulateur->libelle }} 
                                            (Certifié jusqu'au {{ $dispositif->date_expiration_certification->format('d/m/Y') }})
                                        </option>
                                    @endforeach
                                </select>
                                @if($dispositifs->isEmpty())
                                    <small class="text-warning">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        @lang('trans.no_operational_devices')
                                        <a href="{{ route('centre.dispositifs') }}" target="_blank">
                                            @lang('trans.add_device')
                                        </a>
                                    </small>
                                @endif
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="required-field">@lang('trans.training_date')</label>
                                        <input type="date" 
                                               name="date_formation" 
                                               class="form-control" 
                                               id="date_formation"
                                               value="{{ old('date_formation') }}">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('trans.location')</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="lieu" 
                                               value="{{ old('lieu') }}"
                                               placeholder="@lang('trans.enter_location')">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="required-field">@lang('trans.certificate')</label>
                                <div class="custom-file">
                                    <input type="file" 
                                           class="custom-file-input" 
                                           id="attestation" 
                                           name="attestation" 
                                           accept=".pdf">
                                    <label class="custom-file-label" for="attestation">
                                        @lang('trans.choose_file')
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    @lang('trans.accepted_format'): PDF (Max 10MB)
                                </small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> @lang('trans.save_training')
                            </button>
                            <a href="{{ route('centre.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> @lang('trans.cancel')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('script')


<script>
$(document).ready(function() {
    let selectedDemandeurData = null;
    let searchTimeout = null;
    
    // Vérifier que toastr est disponible
    if (typeof toastr === 'undefined') {
        console.error('Toastr n\'est pas chargé');
        window.toastr = {
            success: function(msg) { console.log('Success: ' + msg); },
            error: function(msg) { console.error('Error: ' + msg); },
            warning: function(msg) { console.warn('Warning: ' + msg); },
            info: function(msg) { console.info('Info: ' + msg); }
        };
    }
    
    // Initialiser Select2 pour les autres selects
    $('.select2').select2({
        placeholder: '@lang("trans.select_option")',
        allowClear: true
    });
    
    // Désactiver la validation HTML5 par défaut
    $('#type_formation_id').prop('required', false);
    $('#date_formation').prop('required', false);
    $('#attestation').prop('required', false);
    
    // Recherche par numéro de licence
    $('#btn_search_licence').on('click', function() {
        searchByLicence();
    });
    
    $('#search_licence_input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            searchByLicence();
        }
    });
    
    // Recherche en temps réel
    $('#search_licence_input').on('input', function() {
        clearTimeout(searchTimeout);
        var searchTerm = $(this).val();
        
        if (searchTerm.length >= 3) {
            searchTimeout = setTimeout(function() {
                searchByLicence();
            }, 500);
        } else if (searchTerm.length === 0) {
            $('#search_results_container').hide();
            $('#search_results').empty();
        }
    });
    
    function searchByLicence() {
        var searchTerm = $('#search_licence_input').val().trim();
        
        if (searchTerm.length < 3) {
            toastr.warning('@lang("trans.enter_at_least_3_characters")');
            return;
        }
        
        $.ajax({
            url: '{{ route("centre.search.by.licence") }}',
            type: 'GET',
            data: {
                licence_number: searchTerm
            },
            beforeSend: function() {
                $('#btn_search_licence').html('<i class="fas fa-spinner fa-spin"></i> @lang("trans.searching")...').prop('disabled', true);
            },
            success: function(response) {
                if (response.success && response.demandeurs) {
                    displaySearchResults(response.demandeurs);
                } else {
                    toastr.error(response.message || "@lang('trans.no_demandeur_found')");
                    $('#search_results_container').hide();
                }
            },
            error: function(xhr) {
                console.error('Erreur AJAX:', xhr);
                toastr.error("@lang('trans.error_searching_demandeur')");
            },
            complete: function() {
                $('#btn_search_licence').html('<i class="fas fa-search"></i> @lang("trans.search")').prop('disabled', false);
            }
        });
    }
    
    function displaySearchResults(demandeurs) {
        var $container = $('#search_results');
        $container.empty();
        
        // Vérifier que demandeurs est un tableau
        if (!Array.isArray(demandeurs)) {
            console.error('demandeurs n\'est pas un tableau:', demandeurs);
            $container.html('<div class="alert alert-danger p-3">@lang("trans.invalid_data_format")</div>');
            $('#search_results_container').show();
            return;
        }
        
        if (demandeurs.length === 0) {
            $container.html('<div class="alert alert-warning p-3">@lang("trans.no_demandeur_found")</div>');
            $('#search_results_container').show();
            return;
        }
        
        demandeurs.forEach(function(demandeur) {
            var $item = $(
                '<div class="search-result-item">' +
                    '<div class="d-flex justify-content-between align-items-center">' +
                        '<div class="flex-grow-1">' +
                            '<h6 class="mb-1">' + (demandeur.np || 'N/A') + '</h6>' +
                            '<div class="text-muted small">' +
                                '<i class="fas fa-id-card"></i> Licence: ' + (demandeur.licence_number || 'N/A') +
                            '</div>' +
                            '<div class="text-muted small">' +
                                '<i class="fas fa-calendar"></i> Né(e) le: ' + (demandeur.date_naissance || 'N/A') +
                            '</div>' +
                            '<div class="text-muted small">' +
                                '<i class="fas fa-flag"></i> ' + (demandeur.nationalite || 'N/A') +
                            '</div>' +
                        '</div>' +
                        '<div class="ml-3">' +
                            '<button class="btn btn-success btn-select-demande" data-id="' + demandeur.id + '">' +
                                '<i class="fas fa-check"></i> @lang("trans.select")' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
            
            $container.append($item);
        });
        
        // Gestionnaire pour les boutons de sélection
        $('.btn-select-demande').on('click', function() {
            var demandeurId = $(this).data('id');
            loadDemandeurDetails(demandeurId);
            $('#search_results_container').hide();
            $('#search_licence_input').val('');
        });
        
        $('#search_results_container').show();
    }
    
    function loadDemandeurDetails(demandeurId) {
        $.ajax({
            url: '{{ route("centre.demandeur.details") }}',
            type: 'GET',
            data: {
                demandeur_id: demandeurId
            },
            beforeSend: function() {
                toastr.info('@lang("trans.loading_demandeur_details")...');
            },
            success: function(response) {
                if (response.success && response.demandeur) {
                    selectedDemandeurData = response.demandeur;
                    displaySelectedDemandeur(response.demandeur);
                    
                    // Mettre à jour le champ caché
                    $('#selected_demandeur_id').val(response.demandeur.id);
                    
                    // Afficher la section des détails de formation
                    $('#training_details_section').slideDown();
                    
                    // Activer la validation des champs requis
                    $('#type_formation_id').prop('required', true);
                    $('#date_formation').prop('required', true);
                    $('#attestation').prop('required', true);
                    
                    toastr.success('@lang("trans.demandeur_selected_successfully")');
                    
                    // Scroller vers la section formation
                    $('html, body').animate({
                        scrollTop: $('#training_details_section').offset().top - 100
                    }, 500);
                    
                } else {
                    toastr.error(response.message || "@lang('trans.error_loading_demandeur')");
                }
            },
            error: function(xhr) {
                console.error('Erreur AJAX:', xhr);
                toastr.error("@lang('trans.error_loading_demandeur_details')");
            }
        });
    }
    
    // Dans la fonction displaySelectedDemandeur
function displaySelectedDemandeur(demandeur) {
    var $card = $('#demandeur_card');
    
    // Construire l'affichage de la licence (une seule licence)
    var licenceHtml = '';
    if (demandeur.licence) {
        var licence = demandeur.licence;
        licenceHtml += '<div class="licence-item">';
        licenceHtml += '<i class="fas fa-certificate"></i> ';
        licenceHtml += '<strong>' + (licence.numero_licence || 'N/A') + '</strong>';
        
        if (licence.categorie) {
            licenceHtml += ' <span class="badge badge-light">' + licence.categorie + '</span>';
        }
        
        if (licence.type_licence) {
            licenceHtml += '<br><small><i class="fas fa-tag"></i> Type: ' + licence.type_licence + '</small>';
        }
        
        if (licence.machine_licence) {
            licenceHtml += '<br><small><i class="fas fa-plane"></i> Machine: ' + licence.machine_licence + '</small>';
        }
        
        if (licence.date_deliverance) {
            licenceHtml += '<br><small><i class="fas fa-calendar-check"></i> Délivrée le: ' + licence.date_deliverance + '</small>';
        }
        
        if (licence.date_expiration) {
            var expirationClass = '';
            var expirationIcon = '<i class="far fa-calendar-alt"></i>';
            
            // Vérifier si la licence est expirée
            var dateParts = licence.date_expiration.split('/');
            var expDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
            var today = new Date();
            
            if (expDate < today) {
                expirationClass = 'text-danger';
                expirationIcon = '<i class="fas fa-exclamation-triangle"></i>';
            }
            
            licenceHtml += '<br><small class="' + expirationClass + '">' + expirationIcon + ' Expire le: ' + licence.date_expiration + '</small>';
        }
        
        licenceHtml += '</div>';
    } else {
        licenceHtml = '<div class="licence-item text-warning">';
        licenceHtml += '<i class="fas fa-exclamation-triangle"></i> Aucune licence trouvée pour ce détenteur';
        licenceHtml += '</div>';
    }
    
    // Initiales pour l'avatar
    var initials = demandeur.np ? demandeur.np.split(' ').map(n => n[0]).join('').toUpperCase() : '?';
    
    // Photo du demandeur si disponible
    var avatarContent = '';
    if (demandeur.photo) {
        avatarContent = '<img src="' + demandeur.photo + '" alt="Photo" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">';
    } else {
        avatarContent = '<span>' + initials + '</span>';
    }
    
    // Construire la carte
    var html = '<div class="row">';
    html += '<div class="col-md-2 text-center">';
    html += '<div class="demandeur-avatar-large">';
    html += avatarContent;
    html += '</div>';
    html += '</div>';
    
    html += '<div class="col-md-6">';
    html += '<h4 class="mb-3">' + (demandeur.np || 'N/A') + '</h4>';
    html += '<div class="demandeur-info-item">';
    html += '<i class="fas fa-calendar"></i> <strong>Date de naissance:</strong> ' + (demandeur.date_naissance || 'N/A');
    html += '</div>';
    html += '<div class="demandeur-info-item">';
    html += '<i class="fas fa-map-marker-alt"></i> <strong>Lieu de naissance:</strong> ' + (demandeur.lieu_naissance || 'N/A');
    html += '</div>';
    html += '<div class="demandeur-info-item">';
    html += '<i class="fas fa-flag"></i> <strong>Nationalité:</strong> ' + (demandeur.nationalite || 'N/A');
    html += '</div>';
    html += '<div class="demandeur-info-item">';
    html += '<i class="fas fa-envelope"></i> <strong>Email:</strong> ' + (demandeur.user?.email || 'N/A');
    html += '</div>';
    html += '<div class="demandeur-info-item">';
    html += '<i class="fas fa-home"></i> <strong>Adresse:</strong> ' + (demandeur.adresse || 'N/A');
    html += '</div>';
    
    if (demandeur.adresse_employeur) {
        html += '<div class="demandeur-info-item">';
        html += '<i class="fas fa-building"></i> <strong>Adresse employeur:</strong> ' + demandeur.adresse_employeur;
        html += '</div>';
    }
    html += '</div>';
    
    html += '<div class="col-md-4">';
    html += '<div class="licence-list">';
    html += '<h5 class="mb-3"><i class="fas fa-id-card"></i> Licence</h5>';
    html += licenceHtml;
    html += '</div>';
    html += '</div>';
    html += '</div>';
    
    html += '<div class="row mt-3">';
    html += '<div class="col-md-12 text-right">';
    html += '<button type="button" class="btn btn-light btn-sm" id="btn_change_demandeur">';
    html += '<i class="fas fa-exchange-alt"></i> Changer de détenteur';
    html += '</button>';
    html += '</div>';
    html += '</div>';
    
    $card.html(html);
    
    // Gestionnaire pour changer de demandeur
    $('#btn_change_demandeur').on('click', function() {
        clearSelectedDemandeur();
    });
    
    $('#selected_demandeur_display').show();
}
    
    function clearSelectedDemandeur() {
        selectedDemandeurData = null;
        $('#selected_demandeur_id').val('');
        $('#selected_demandeur_display').hide();
        $('#training_details_section').slideUp();
        $('#demandeur_card').empty();
        $('#search_licence_input').val('').focus();
        
        // Désactiver la validation des champs requis
        $('#type_formation_id').prop('required', false);
        $('#date_formation').prop('required', false);
        $('#attestation').prop('required', false);
        
        toastr.info('@lang("trans.select_new_demandeur")');
    }
    
    // Afficher le nom du fichier sélectionné
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName || '@lang("trans.choose_file")');
    });
    
    // Validation du formulaire
    $('#trainingForm').on('submit', function(e) {
        var demandeurId = $('#selected_demandeur_id').val();
        console.log(demandeurId);
        if (!demandeurId) {
            e.preventDefault();
            //toastr.error("@lang('trans.select_demandeur_first')");
            
            // Scroller vers la section de recherche
            $('html, body').animate({
                scrollTop: $('.search-section').offset().top - 100
            }, 500);
            
            $('#search_licence_input').focus();
            
            return false;
        }
        
        // Validation supplémentaire
        var typeFormation = $('#type_formation_id').val();
        var dateFormation = $('#date_formation').val();
        var attestation = $('#attestation').val();
        
        if (!typeFormation) {
            e.preventDefault();
            toastr.error("@lang('trans.select_training_type_required')");
            return false;
        }
        
        if (!dateFormation) {
            e.preventDefault();
            toastr.error("@lang('trans.training_date_required')");
            return false;
        }
        
        if (!attestation) {
            e.preventDefault();
            toastr.error("@lang('trans.certificate_required')");
            return false;
        }
        
        // Afficher un loader
        $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> @lang("trans.saving")...').prop('disabled', true);
        
        return true;
    });
    
    // Charger automatiquement si demandeur_id est présent dans l'URL
    @if(request('demandeur_id'))
        loadDemandeurDetails({{ request('demandeur_id') }});
    @endif
    
    // Focus sur le champ de recherche au chargement
    setTimeout(function() {
        $('#search_licence_input').focus();
    }, 500);
});
</script>
@endpush