{{-- resources/views/centre/index.blade.php --}}
@extends('centre.layouts.app')

@section('title')
    @lang('trans.dashboard_center')
@endsection

@section('contentheader')
    @lang('trans.dashboard_center')
@endsection

@section('contentheaderlink')
    <a href="{{ route('centre.index') }}">@lang('trans.dashboard_center')</a>
@endsection

@section('contentheaderactive')
    @lang('trans.dashboard')
@endsection

@push('css')
<style>
    .stat-card {
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        color: white;
    }
    .stat-card i {
        font-size: 3rem;
        opacity: 0.7;
    }
    .stat-card .stat-number {
        font-size: 2rem;
        font-weight: bold;
    }
    .bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); color: #333; }
    .bg-gradient-warning { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%); color: #333; }
    
    /* Styles pour la recherche de demandeur */
    .search-demandeur-container {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
    }
    
    .demandeur-result {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        border: 1px solid #28a745;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .demandeur-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .demandeur-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #007bff;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: bold;
    }
    
    .demandeur-details {
        flex: 1;
    }
    
    .licence-badge {
        background: #007bff;
        color: white;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        display: inline-block;
    }
    
    .search-results {
        max-height: 300px;
        overflow-y: auto;
        margin-top: 10px;
    }
    
    .search-result-item {
        padding: 10px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .search-result-item:hover {
        background: #e9ecef;
    }
    
    .search-result-item.active {
        background: #007bff;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">


    {{-- Statistiques --}}
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card bg-gradient-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number">{{ $totalFormations ?? 0 }}</div>
                        <div>@lang('trans.total_trainings')</div>
                    </div>
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="stat-card bg-gradient-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number">{{ $totalInstructeurs ?? 0 }}</div>
                        <div>@lang('trans.active_instructors')</div>
                    </div>
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="stat-card bg-gradient-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number">{{ $totalExaminateurs ?? 0 }}</div>
                        <div>@lang('trans.validated_examiners')</div>
                    </div>
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="stat-card bg-gradient-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number">{{ $totalDispositifs ?? 0 }}</div>
                        <div>@lang('trans.operational_devices')</div>
                    </div>
                    <i class="fas fa-microchip"></i>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Liste des formations récentes --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-2"></i>
                        @lang('trans.recent_trainings')
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('centre.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> @lang('trans.add_training')
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('trans.demandeur')</th>
                                <th>@lang('trans.date')</th>
                                <th>@lang('trans.training_type')</th>
                                <th>@lang('trans.licence_type')</th>
                                <th>@lang('trans.instructor')</th>
                                <th>@lang('trans.examiner')</th>
                                <th>@lang('trans.device')</th>
                                <th>@lang('trans.location')</th>
                                <th>@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($formations as $formation)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $formation->demandeur->np ?? 'N/A' }}
                                    @if($formation->demandeur)
                                        <br>
                                        <small class="text-muted">
                                            {{ $formation->demandeur->user->email ?? '' }}
                                        </small>
                                    @endif
                                </td>
                                <td>{{ $formation->date_formation->format('d/m/Y') }}</td>
                                <td>{{ $formation->typeFormation->nom ?? 'N/A' }}</td>
                                <td>{{ $formation->typeLicence->nom ?? 'N/A' }}</td>
                                <td>{{ $formation->instructeur->nom_complet ?? 'N/A' }}</td>
                                <td>{{ $formation->examinateur->nom_complet ?? 'N/A' }}</td>
                                <td>{{ $formation->dispositifFormation->simulateur->libelle ?? 'N/A' }}</td>
                                <td>{{ $formation->lieu ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('centre.show', $formation->id) }}" class="btn btn-info btn-sm" title="@lang('trans.view')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ asset('/uploads/' . $formation->attestation) }}" class="btn btn-success btn-sm" target="_blank" title="@lang('trans.download_certificate')">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">
                                    <div class="alert alert-info m-3">
                                        <i class="fas fa-info-circle"></i> @lang('trans.no_trainings_found')
                                    </div>
                                    <a href="{{ route('centre.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> @lang('trans.add_first_training')
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($formations->hasPages())
                <div class="card-footer">
                    {{ $formations->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function() {
    let selectedDemandeur = null;
    let searchTimeout = null;
    
    // Initialiser Select2 pour la liste des demandeurs
    $('#select_demandeur').select2({
        placeholder: '@lang("trans.select_demandeur")',
        allowClear: true,
        ajax: {
            url: '{{ route("centre.search.demandeurs") }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function(data) {
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        },
        templateResult: formatDemandeurResult,
        templateSelection: formatDemandeurSelection
    });
    
    // Format pour les résultats Select2
    function formatDemandeurResult(demandeur) {
        if (demandeur.loading) {
            return demandeur.text;
        }
        
        var $container = $(
            "<div class='select2-result-demandeur'>" +
                "<div class='select2-result-demandeur__name'>" + demandeur.np + "</div>" +
                "<div class='select2-result-demandeur__licence'>" +
                    "<i class='fas fa-id-card'></i> " + (demandeur.licence_number || 'N/A') +
                "</div>" +
                "<div class='select2-result-demandeur__email'>" +
                    "<i class='fas fa-envelope'></i> " + (demandeur.email || 'N/A') +
                "</div>" +
            "</div>"
        );
        
        return $container;
    }
    
    function formatDemandeurSelection(demandeur) {
        return demandeur.np || demandeur.text;
    }
    
    // Quand un demandeur est sélectionné dans Select2
    $('#select_demandeur').on('select2:select', function(e) {
        var data = e.params.data;
        loadDemandeurDetails(data.id);
    });
    
    // Recherche par numéro de licence
    $('#btn_search_demandeur').on('click', function() {
        searchDemandeur();
    });
    
    $('#search_licence').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            searchDemandeur();
        }
    });
    
    // Recherche en temps réel avec debounce
    $('#search_licence').on('input', function() {
        clearTimeout(searchTimeout);
        var searchTerm = $(this).val();
        
        if (searchTerm.length >= 3) {
            searchTimeout = setTimeout(function() {
                searchDemandeur();
            }, 500);
        } else if (searchTerm.length === 0) {
            $('#search_results_container').hide();
            $('#search_results').empty();
        }
    });
    
    function searchDemandeur() {
        var searchTerm = $('#search_licence').val();
        
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
                $('#btn_search_demandeur').html('<i class="fas fa-spinner fa-spin"></i> @lang("trans.searching")...').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    displaySearchResults(response.demandeurs);
                } else {
                    toastr.error(response.message || "@lang('trans.no_demandeur_found')");
                    $('#search_results_container').hide();
                }
            },
            error: function(xhr) {
                toastr.error('@lang("trans.error_searching_demandeur")');
                console.error(xhr);
            },
            complete: function() {
                $('#btn_search_demandeur').html('<i class="fas fa-search"></i> @lang("trans.search")').prop('disabled', false);
            }
        });
    }
    
    function displaySearchResults(demandeurs) {
        var $container = $('#search_results');
        $container.empty();
        
        if (demandeurs.length === 0) {
            $container.html('<div class="alert alert-warning">@lang("trans.no_demandeur_found")</div>');
            $('#search_results_container').show();
            return;
        }
        
        demandeurs.forEach(function(demandeur) {
            var $item = $(
                '<div class="search-result-item" data-id="' + demandeur.id + '">' +
                    '<div class="d-flex justify-content-between align-items-center">' +
                        '<div>' +
                            '<strong>' + demandeur.np + '</strong><br>' +
                            '<small class="text-muted">' +
                                '<i class="fas fa-id-card"></i> ' + (demandeur.licence_number || 'N/A') +
                            '</small>' +
                        '</div>' +
                        '<div>' +
                            '<span class="badge badge-info">' + (demandeur.email || '') + '</span>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
            
            $item.on('click', function() {
                var demandeurId = $(this).data('id');
                loadDemandeurDetails(demandeurId);
                $('#search_results_container').hide();
                $('#search_licence').val('');
            });
            
            $container.append($item);
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
                // Show loading
            },
            success: function(response) {
                if (response.success) {
                    selectedDemandeur = response.demandeur;
                    displaySelectedDemandeur(response.demandeur);
                    
                    // Mettre à jour Select2
                    var newOption = new Option(response.demandeur.np, response.demandeur.id, true, true);
                    $('#select_demandeur').append(newOption).trigger('change');
                } else {
                    toastr.error(response.message || '@lang("trans.error_loading_demandeur")');
                }
            },
            error: function(xhr) {
                toastr.error('@lang("trans.error_loading_demandeur_details")');
                console.error(xhr);
            }
        });
    }
    
    function displaySelectedDemandeur(demandeur) {
        // Mettre à jour les informations
        $('#demandeur_name').text(demandeur.np);
        
        // Initiales pour l'avatar
        var initials = demandeur.np.split(' ').map(n => n[0]).join('').toUpperCase();
        $('#demandeur_initials').text(initials);
        
        // Numéro de licence (chercher dans les licences du demandeur)
        var licenceNumber = 'N/A';
        if (demandeur.licences && demandeur.licences.length > 0) {
            licenceNumber = demandeur.licences[0].numero_licence || demandeur.licences[0].num_licence || 'N/A';
        }
        $('#licence_number_display').text(licenceNumber);
        
        // Autres informations
        $('#demandeur_birth').text(demandeur.date_naissance || 'N/A');
        $('#demandeur_nationality').text(demandeur.nationalite || 'N/A');
        $('#demandeur_email').text(demandeur.user?.email || 'N/A');
        
        // Afficher le conteneur
        $('#selected_demandeur_container').show();
    }
    
    // Ajouter une formation pour le demandeur sélectionné
    $('#btn_add_training_for_demandeur').on('click', function() {
        if (selectedDemandeur) {
            window.location.href = '{{ route("centre.create") }}?demandeur_id=' + selectedDemandeur.id;
        } else {
            toastr.warning("@lang('trans.select_demandeur_first')");
        }
    });
    
    // Effacer la sélection
    $('#btn_clear_demandeur').on('click', function() {
        selectedDemandeur = null;
        $('#selected_demandeur_container').hide();
        $('#select_demandeur').val(null).trigger('change');
        $('#search_licence').val('');
        $('#search_results_container').hide();
    });
    
    // Si un demandeur_id est passé en paramètre, le charger automatiquement
    const urlParams = new URLSearchParams(window.location.search);
    const demandeurId = urlParams.get('demandeur_id');
    if (demandeurId) {
        loadDemandeurDetails(demandeurId);
    }
});
</script>

<style>
/* Styles pour Select2 */
.select2-result-demandeur {
    padding: 8px;
}

.select2-result-demandeur__name {
    font-weight: bold;
    margin-bottom: 4px;
}

.select2-result-demandeur__licence,
.select2-result-demandeur__email {
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
}

.select2-result-demandeur__licence i,
.select2-result-demandeur__email i {
    width: 16px;
    margin-right: 4px;
}

.select2-container--default .select2-results__option--highlighted .select2-result-demandeur__licence,
.select2-container--default .select2-results__option--highlighted .select2-result-demandeur__email {
    color: #e2e6ea;
}
</style>
@endpush