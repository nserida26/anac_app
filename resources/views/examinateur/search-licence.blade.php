@extends('examinateur.layouts.app')
@section('title')
    @lang('trans.dashboard_examiner')
@endsection
@section('contentheader')
    @lang('trans.dashboard_examiner')
@endsection
@section('contentheaderlink')
    <a href="{{ route('examinateur') }}">
        @lang('trans.dashboard_examiner') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_examiner')
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>🔍 Rechercher un demandeur par numéro de licence</h4>
                </div>
                <div class="card-body">
                    <div class="form-group mb-4">
                        <label for="licence_number">Numéro de licence</label>
                        <div class="input-group">
                            <input type="text" 
                                   id="licence_number" 
                                   class="form-control form-control-lg" 
                                   placeholder="Ex: ANAC-2024-00123"
                                   autocomplete="off">
                            <div class="input-group-append">
                                <button type="button" id="search_btn" class="btn btn-primary btn-lg">
                                    <i class="fa fa-search"></i> Rechercher
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Entrez le numéro de licence complet pour retrouver le demandeur
                        </small>
                    </div>

                    <div id="loading" style="display: none;" class="text-center my-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Chargement...</span>
                        </div>
                        <p class="mt-2">Recherche en cours...</p>
                    </div>

                    <div id="result_area" style="display: none;">
                        <div id="error_message" class="alert alert-danger" style="display: none;"></div>
                        
                        <div id="demandeur_info" class="card mt-3" style="display: none;">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">✓ Demandeur trouvé</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 text-center">
                                        <img id="demandeur_photo" src="" alt="Photo" 
                                             class="img-fluid rounded-circle mb-2" 
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                    <div class="col-md-9">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="30%">Nom complet</th>
                                                <td id="demandeur_np"></td>
                                            </tr>
                                            <tr>
                                                <th>Date de naissance</th>
                                                <td id="demandeur_dob"></td>
                                            </tr>
                                            <tr>
                                                <th>Lieu de naissance</th>
                                                <td id="demandeur_pob"></td>
                                            </tr>
                                            <tr>
                                                <th>Adresse</th>
                                                <td id="demandeur_address"></td>
                                            </tr>
                                            <tr>
                                                <th>Nationalité</th>
                                                <td id="demandeur_nationalite"></td>
                                            </tr>
                                            <tr>
                                                <th>Numéro licence</th>
                                                <td id="licence_num"></td>
                                            </tr>
                                            <tr>
                                                <th>Type licence</th>
                                                <td id="licence_type"></td>
                                            </tr>
                                            <tr>
                                                <th>Catégorie</th>
                                                <td id="licence_categorie"></td>
                                            </tr>
                                            <tr>
                                                <th>Date expiration</th>
                                                <td id="licence_expiration"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <button type="button" id="proceed_btn" class="btn btn-success btn-lg">
                                        📋 Attribuer un examen médical
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('custom')
<script>
$(document).ready(function() {
    let currentDemandeurId = null;
    let currentLicenceNumber = null;
    
    $('#search_btn').on('click', function() {
        const licenceNumber = $('#licence_number').val().trim();
        
        if (!licenceNumber) {
            showError('Veuillez entrer un numéro de licence');
            return;
        }
        
        // Afficher le chargement
        $('#loading').show();
        $('#result_area').hide();
        $('#demandeur_info').hide();
        $('#error_message').hide();
        
        // Effectuer la recherche AJAX
        $.ajax({
            url: '{{ route("examinateur.search-by-licence") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                numero_licence: licenceNumber
            },
            success: function(response) {
                $('#loading').hide();
                $('#result_area').show();
                
                if (response.found) {
                    // Afficher les informations du demandeur
                    currentDemandeurId = response.demandeur.id;
                    currentLicenceNumber = response.demandeur.licence.numero;
                    
                    $('#demandeur_np').text(response.demandeur.np);
                    $('#demandeur_dob').text(response.demandeur.date_naissance || 'Non renseigné');
                    $('#demandeur_pob').text(response.demandeur.lieu_naissance || 'Non renseigné');
                    $('#demandeur_address').text(response.demandeur.adresse || 'Non renseigné');
                    $('#demandeur_nationalite').text(response.demandeur.nationalite || 'Non renseignée');
                    $('#licence_num').text(response.demandeur.licence.numero);
                    $('#licence_type').text(response.demandeur.licence.type || 'Non spécifié');
                    $('#licence_categorie').text(response.demandeur.licence.categorie || 'Non spécifiée');
                    $('#licence_expiration').text(response.demandeur.licence.date_expiration || 'Non renseignée');
                    
                    if (response.demandeur.photo) {
                        $('#demandeur_photo').attr('src', '/uploads/' + response.demandeur.photo);
                    } else {
                        $('#demandeur_photo').attr('src', '/images/default-avatar.png');
                    }
                    
                    $('#demandeur_info').show();
                    $('#error_message').hide();
                } else {
                    showError(response.message || 'Aucun demandeur trouvé avec ce numéro de licence');
                }
            },
            error: function(xhr) {
                $('#loading').hide();
                $('#result_area').show();
                let errorMsg = 'Une erreur est survenue lors de la recherche';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                showError(errorMsg);
            }
        });
    });
    
    $('#proceed_btn').on('click', function() {
        if (currentDemandeurId) {
            // Stocker le numéro de licence en session ou le passer en paramètre
            window.location.href = '{{ url("examinateur/create") }}/' + currentDemandeurId + '?licence=' + encodeURIComponent(currentLicenceNumber);
        }
    });
    
    $('#licence_number').on('keypress', function(e) {
        if (e.which === 13) {
            $('#search_btn').click();
        }
    });
    
    function showError(message) {
        $('#error_message').text(message).show();
        $('#demandeur_info').hide();
    }
});
</script>
@endpush

@push('styles')
<style>
    #demandeur_photo {
        border: 3px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .table th {
        background-color: #f8f9fa;
    }
</style>
@endpush
@endsection