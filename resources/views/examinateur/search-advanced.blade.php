@extends('examinateur.layouts.app')
@section('title')
    @lang('trans.dashboard_examiner')
@endsection
@section('contentheader')
    @lang('trans.dashboard_examiner')
@endsection
@section('contentheaderlink')
    <a href="{{route('examinateur')}}">
        @lang('trans.dashboard_examiner') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_examiner')
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">🔍 Recherche avancée - Demandeur par Licence</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Barre de recherche principale avec autocomplete -->
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Recherche rapide</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                            <input type="text" 
                                   id="quick_search" 
                                   class="form-control form-control-lg" 
                                   placeholder="Rechercher par numéro de licence (ex: ATC-2024) ou par nom/prénom..."
                                   autocomplete="off">
                            <div class="input-group-append">
                                <select id="search_type" class="form-control" style="width: auto;">
                                    <option value="all">Tous</option>
                                    <option value="licence">Licence uniquement</option>
                                    <option value="name">Nom uniquement</option>
                                </select>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Tapez au moins 2 caractères - Suggestions en temps réel
                        </small>
                    </div>
                    
                    <!-- Résultats de recherche rapide -->
                    <div id="quick_results" style="display: none;">
                        <div id="quick_results_list" class="row"></div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Filtres avancés -->
                    <h5 class="mb-3">📋 Filtres avancés</h5>
                    <form id="advanced_search_form" class="mb-4">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Numéro de licence</label>
                                    <input type="text" name="licence_number" id="licence_number" class="form-control" placeholder="Ex: ATC, PNC...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nom / Prénom du demandeur</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Nom ou prénom...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Type de licence</label>
                                    <select name="licence_type" id="licence_type" class="form-control">
                                        <option value="all">Tous les types</option>
                                        @isset($licenceTypes)
                                            @foreach($licenceTypes as $type)
                                                <option value="{{ $type }}">{{ $type }}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fa fa-filter"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Résultats de recherche avancée -->
                    <div id="advanced_results">
                        <div class="text-center py-5" id="initial_message">
                            <i class="fa fa-search fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Utilisez la recherche rapide ou les filtres avancés pour trouver un demandeur</p>
                        </div>
                        <div id="results_container" style="display: none;"></div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

@push('custom')
<script>
$(document).ready(function() {
    let searchTimeout;
    
    // Recherche avec autocomplete
    $('#quick_search').on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val().trim();
        const searchType = $('#search_type').val();
        
        if (query.length < 2) {
            $('#quick_results').hide();
            $('#quick_results_list').empty();
            return;
        }
        
        searchTimeout = setTimeout(function() {
            performQuickSearch(query, searchType);
        }, 300);
    });
    
    $('#search_type').on('change', function() {
        const query = $('#quick_search').val().trim();
        if (query.length >= 2) {
            performQuickSearch(query, $(this).val());
        }
    });
    
    function performQuickSearch(query, type) {
        $('#quick_results').show();
        $('#quick_results_list').html('<div class="col-12 text-center"><div class="spinner-border text-primary"></div></div>');
        
        $.ajax({
            url: '{{ route("examinateur.search-autocomplete") }}',
            method: 'GET',
            data: {
                q: query,
                type: type
            },
            success: function(results) {
                if (results.length === 0) {
                    $('#quick_results_list').html(`
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Aucun résultat trouvé pour "${query}"
                            </div>
                        </div>
                    `);
                    return;
                }
                
                let html = '';
                results.forEach(function(result) {
                    const photoUrl = result.photo ? '/uploads/' + result.photo : '/images/default-avatar.png';
                    const badgeClass = result.type === 'licence' ? 'badge-info' : 'badge-success';
                    const icon = result.type === 'licence' ? '🔑' : '👤';
                    
                    html += `
                        <div class="col-md-6 mb-3">
                            <div class="card result-card hover-shadow">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 text-center">
                                            <img src="${photoUrl}" class="rounded-circle" width="70" height="70" style="object-fit: cover;">
                                        </div>
                                        <div class="col-md-9">
                                            <h6 class="mb-1">
                                                ${icon} ${result.np}
                                                <span class="badge ${badgeClass} ml-2">${result.type === 'licence' ? 'Licence' : 'Demandeur'}</span>
                                            </h6>
                                            <p class="mb-1 small">
                                                <strong>🔑 Licence:</strong> ${result.licence_number}<br>
                                                <strong>📋 Type:</strong> ${result.licence_type || 'N/A'}<br>
                                                <strong>📅 Naissance:</strong> ${result.date_naissance || 'N/A'}
                                            </p>
                                            <button class="btn btn-sm btn-success select-demandeur" 
                                                    data-id="${result.id}" 
                                                    data-licence="${result.licence_number}"
                                                    data-name="${result.np}">
                                                <i class="fa fa-stethoscope"></i> Attribuer examen
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                $('#quick_results_list').html(html);
                
                // Attacher les événements aux boutons
                $('.select-demandeur').on('click', function() {
                    const demandeurId = $(this).data('id');
                    const licenceNumber = $(this).data('licence');
                    window.location.href = '{{ url("examinateur/create") }}/' + demandeurId + '?licence=' + encodeURIComponent(licenceNumber);
                });
            },
            error: function() {
                $('#quick_results_list').html(`
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i> Une erreur est survenue lors de la recherche
                        </div>
                    </div>
                `);
            }
        });
    }
    
    // Recherche avancée
    $('#advanced_search_form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $('#initial_message').hide();
        $('#results_container').html('<div class="text-center"><div class="spinner-border text-primary"></div></div>').show();
        
        $.ajax({
            url: '{{ route("examinateur.advanced-search") }}',
            method: 'GET',
            data: formData,
            success: function(response) {
                if (response.demandeurs.data.length === 0) {
                    $('#results_container').html(`
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i> Aucun demandeur trouvé avec ces critères
                        </div>
                    `);
                    return;
                }
                
                let html = '<div class="table-responsive"><table class="table table-bordered table-hover"><thead><tr>';
                html += '<th>Photo</th><th>Nom complet</th><th>Date naissance</th><th>Numéro licence</th><th>Type licence</th><th>Actions</th>';
                html += '</tr></thead><tbody>';
                
                response.demandeurs.data.forEach(function(demandeur) {
                    const photoUrl = demandeur.photo ? '/uploads/' + demandeur.photo : '/images/default-avatar.png';
                    const licenceNumber = demandeur.licence ? demandeur.licence.numero_licence : 'Aucune licence';
                    const licenceType = demandeur.licence ? demandeur.licence.type_licence : 'N/A';
                    
                    html += `<tr>
                        <td class="text-center"><img src="${photoUrl}" width="50" height="50" class="rounded-circle" style="object-fit: cover;"></td>
                        <td><strong>${demandeur.np}</strong></td>
                        <td>${demandeur.date_naissance || 'N/A'}</td>
                        <td><code>${licenceNumber}</code></td>
                        <td><span class="badge badge-info">${licenceType}</span></td>
                        <td>
                            <a href="/examinateur/create/${demandeur.id}?licence=${encodeURIComponent(licenceNumber)}" 
                               class="btn btn-sm btn-success">
                                <i class="fa fa-stethoscope"></i> Attribuer examen
                            </a>
                        </td>
                    </tr>`;
                });
                
                html += '</tbody></table></div>';
                
                // Ajouter la pagination
                //if (response.demandeurs.links) {
                    //html += '<div class="d-flex justify-content-center">' + response.demandeurs.links + '</div>';
                //}
                
                $('#results_container').html(html);
            },
            error: function() {
                $('#results_container').html(`
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i> Une erreur est survenue lors de la recherche
                    </div>
                `);
            }
        });
    });
});
</script>
@endpush
@push('css')
<style>
.hover-shadow {
    transition: box-shadow 0.3s ease;
}
.hover-shadow:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}
.result-card {
    cursor: pointer;
    transition: all 0.3s ease;
}
.result-card:hover {
    background-color: #f8f9fa;
}
#quick_search {
    border-radius: 8px 0 0 8px;
}
</style>
@endpush
@endsection