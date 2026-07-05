@extends('user.layouts.app')
@section('title', trans('trans.assign_training'))
@section('contentheader', trans('trans.assign_training'))
@section('contentheaderlink')
    <a href="{{ route('demandeur.dashboard') }}">@lang('trans.dashboard')</a>
@endsection
@section('contentheaderactive', trans('trans.assign_training'))

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
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
    
    .search-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 20px;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
    }
    
    .search-result-item {
        padding: 15px;
        border-bottom: 1px solid #dee2e6;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .search-result-item:hover {
        background: #e9ecef;
    }
    
    .search-results {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-top: 15px;
    }
    
    .required-field::after {
        content: " *";
        color: red;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <form action="{{ route('demandeur.store.formation') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-md-12">
                <div class="search-section">
                    <div class="section-title">
                        <i class="fas fa-user-graduate"></i>
                        @lang('trans.select_trainee')
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        @lang('trans.assign_training_info')
                    </div>
                    
                    <div class="form-group">
                        <label class="required-field">@lang('trans.search_by_licence_or_name')</label>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="search_input" 
                                   placeholder="@lang('trans.enter_licence_number_or_name')">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="search_btn">
                                    <i class="fas fa-search"></i> @lang('trans.search')
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="search_results_container" style="display: none;">
                        <h6 class="mt-3">@lang('trans.search_results'):</h6>
                        <div class="search-results" id="search_results"></div>
                    </div>
                    
                    <div id="selected_demandeur_display" style="display: none;">
                        <div class="demandeur-card" id="demandeur_card"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <input type="hidden" name="demandeur_id" id="demandeur_id">
        
        <div id="training_form_section" style="display: none;">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-success text-white">
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
                                        <select class="form-control select2" name="type_formation_id" id="type_formation_id" required>
                                            <option value="">@lang('trans.select_training_type')</option>
                                            @foreach($typeFormations as $type)
                                                <option value="{{ $type->id }}">{{ $type->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('trans.licence_type')</label>
                                        <select class="form-control select2" name="type_licence_id" id="type_licence_id">
                                            <option value="">@lang('trans.select_licence_type')</option>
                                            @foreach($typeLicences as $licence)
                                                <option value="{{ $licence->id }}">{{ $licence->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>@lang('trans.training_title')</label>
                                <input type="text" class="form-control" name="intitule_formation" 
                                       placeholder="@lang('trans.enter_training_title')">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="required-field">@lang('trans.training_date')</label>
                                        <input type="date" name="date_formation" class="form-control" id="date_formation" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('trans.location')</label>
                                        <input type="text" class="form-control" name="lieu" 
                                               placeholder="@lang('trans.enter_location')">
                                    </div>
                                </div>
                            </div>
                            
                            @if(isset($centres) && $centres->count() > 0)
                            <div class="form-group">
                                <label>@lang('trans.training_center')</label>
                                <select class="form-control" name="centre_formation_id">
                                    <option value="">@lang('trans.select_center')</option>
                                    <option value=""></option>
                                    @foreach($centres as $centre)
                                        <option value="{{ $centre->id }}">{{ $centre->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            
                            <div class="form-group">
                                <label class="required-field">@lang('trans.certificate')</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="attestation" name="attestation" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <label class="custom-file-label" for="attestation">@lang('trans.choose_file')</label>
                                </div>
                                <small>@lang('trans.accepted_format_pdf_image', ['max' => '10MB'])</small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> @lang('trans.save_training')
                            </button>
                            <a href="{{ route('demandeur.dashboard') }}" class="btn btn-secondary btn-lg">
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
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<script>
$(document).ready(function() {
    let selectedDemandeurId = null;
    
    $('.select2').select2();
    
    $('#search_btn').on('click', function() {
        performSearch();
    });
    
    $('#search_input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            performSearch();
        }
    });
    
    function performSearch() {
        var searchTerm = $('#search_input').val().trim();
        
        if (searchTerm.length < 2) {
            toastr.warning('@lang("trans.enter_at_least_2_characters")');
            return;
        }
        
        $.ajax({
            url: '{{ route("demandeur.search.by.licence") }}',
            type: 'GET',
            data: { licence_number: searchTerm },
            beforeSend: function() {
                $('#search_btn').html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
            },
            success: function(response) {
                if (response.success && response.demandeurs.length > 0) {
                    displaySearchResults(response.demandeurs);
                } else {
                    toastr.warning('@lang("trans.no_demandeur_found")');
                    $('#search_results_container').hide();
                }
            },
            error: function() {
                toastr.error('@lang("trans.error_searching")');
            },
            complete: function() {
                $('#search_btn').html('<i class="fas fa-search"></i> @lang("trans.search")').prop('disabled', false);
            }
        });
    }
    
    function displaySearchResults(demandeurs) {
        var $container = $('#search_results');
        $container.empty();
        
        demandeurs.forEach(function(demandeur) {
            var $item = $('<div class="search-result-item">' +
                '<div class="d-flex justify-content-between align-items-center">' +
                    '<div>' +
                        '<strong>' + demandeur.np + '</strong><br>' +
                        '<small><i class="fas fa-id-card"></i> ' + demandeur.licence_number + '</small><br>' +
                        '<small><i class="fas fa-calendar"></i> ' + (demandeur.date_naissance || 'N/A') + '</small>' +
                    '</div>' +
                    '<button class="btn btn-success btn-sm select-demandeur" data-id="' + demandeur.id + '">' +
                        '<i class="fas fa-check"></i> @lang("trans.select")' +
                    '</button>' +
                '</div>' +
            '</div>');
            
            $container.append($item);
        });
        
        $('.select-demandeur').on('click', function() {
            var demandeurId = $(this).data('id');
            loadDemandeurDetails(demandeurId);
        });
        
        $('#search_results_container').show();
    }
    
    function loadDemandeurDetails(demandeurId) {
        $.ajax({
            url: '{{ route("demandeur.get.details") }}',
            type: 'GET',
            data: { demandeur_id: demandeurId },
            success: function(response) {
                if (response.success) {
                    displaySelectedDemandeur(response.demandeur);
                    $('#demandeur_id').val(demandeurId);
                    $('#training_form_section').slideDown();
                    $('#search_results_container').hide();
                    $('#search_input').val('');
                    
                    // Activer les champs requis
                    $('#type_formation_id').prop('required', true);
                    $('#date_formation').prop('required', true);
                    $('#attestation').prop('required', true);
                    
                    toastr.success('@lang("trans.demandeur_selected")');
                }
            },
            error: function() {
                toastr.error('@lang("trans.error_loading_details")');
            }
        });
    }
    
    function displaySelectedDemandeur(demandeur) {
        var $card = $('#demandeur_card');
        var initials = demandeur.np.split(' ').map(n => n[0]).join('').toUpperCase();
        
        var licenceHtml = '';
        if (demandeur.licence) {
            licenceHtml = '<div class="licence-item">' +
                '<i class="fas fa-certificate"></i> <strong>' + demandeur.licence.numero_licence + '</strong>' +
                (demandeur.licence.type_licence ? '<br><small>Type: ' + demandeur.licence.type_licence + '</small>' : '') +
                (demandeur.licence.categorie_licence ? '<br><small>Catégorie: ' + demandeur.licence.categorie_licence + '</small>' : '') +
                (demandeur.licence.date_expiration ? '<br><small>Expire le: ' + demandeur.licence.date_expiration + '</small>' : '') +
            '</div>';
        } else {
            licenceHtml = '<div class="text-warning"><i class="fas fa-exclamation-triangle"></i> Aucune licence trouvée</div>';
        }
        
        var photoHtml = demandeur.photo ? 
            '<img src="' + demandeur.photo + '" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">' : 
            '<span style="font-size:32px;">' + initials + '</span>';
        
        $card.html(`
            <div class="row">
                <div class="col-md-2 text-center">
                    <div class="demandeur-avatar-large">${photoHtml}</div>
                </div>
                <div class="col-md-6">
                    <h4>${demandeur.np}</h4>
                    <div><i class="fas fa-calendar"></i> Naissance: ${demandeur.date_naissance || 'N/A'}</div>
                    <div><i class="fas fa-map-marker-alt"></i> Lieu: ${demandeur.lieu_naissance || 'N/A'}</div>
                    <div><i class="fas fa-flag"></i> Nationalité: ${demandeur.nationalite || 'N/A'}</div>
                    <div><i class="fas fa-envelope"></i> Email: ${demandeur.user?.email || 'N/A'}</div>
                </div>
                <div class="col-md-4">
                    <div class="licence-list p-3 bg-white bg-opacity-10 rounded">
                        <h6><i class="fas fa-id-card"></i> Licence</h6>
                        ${licenceHtml}
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-light" id="change_demandeur">
                        <i class="fas fa-exchange-alt"></i> Changer
                    </button>
                </div>
            </div>
        `);
        
        $('#selected_demandeur_display').show();
        
        $('#change_demandeur').on('click', function() {
            clearSelectedDemandeur();
        });
    }
    
    function clearSelectedDemandeur() {
        selectedDemandeurId = null;
        $('#demandeur_id').val('');
        $('#selected_demandeur_display').hide();
        $('#training_form_section').slideUp();
        $('#search_input').val('').focus();
        $('#search_results_container').hide();
        
        $('#type_formation_id').prop('required', false);
        $('#date_formation').prop('required', false);
        $('#attestation').prop('required', false);
    }
    
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });
    
    //$('#date_formation').attr('min', new Date().toISOString().split('T')[0]);
});
</script>
@endpush