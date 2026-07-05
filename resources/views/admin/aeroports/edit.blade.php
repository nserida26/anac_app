
@extends('layouts.admin')
@section('title')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheaderlink')
    <a href="">
        @lang('trans.dashboard_admin') </a>
@endsection
@section('contentheaderactive')
   Modifier Aéroport: {{ $aeroport->nom }}
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Modifier l'aéroport
                        </h4>
                        <a href="{{ route('aeroports.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('aeroports.update', $aeroport) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')
                        
                        @include('admin.aeroports.partials._form')
                        
                        <div class="card-footer bg-transparent border-top-0 pt-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="button" class="btn btn-danger" 
                                            onclick="confirmDelete({{ $aeroport->id }})">
                                        <i class="fas fa-trash me-1"></i> Supprimer
                                    </button>
                                </div>
                                <div>
                                    <a href="{{ route('aeroports.show', $aeroport) }}" class="btn btn-secondary me-2">
                                        <i class="fas fa-times me-1"></i> Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Mettre à jour
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i> Confirmation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'aéroport <strong>{{ $aeroport->nom }}</strong> ?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    Cette action est irréversible !
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Annuler
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(aeroportId) {
        // Mettre à jour l'action du formulaire de suppression
        $('#deleteForm').attr('action', '/aeroports/' + aeroportId);
        $('#deleteModal').modal('show');
    }
    
    // Validation du formulaire
    $(document).ready(function() {
        $('#editForm').on('submit', function(e) {
            const codeIATA = $('#codeIATA').val().trim();
            const codeICAO = $('#codeICAO').val().trim();
            
            if (codeIATA.length !== 3) {
                toastr.error('Le code IATA doit comporter exactement 3 caractères');
                e.preventDefault();
                return false;
            }
            
            if (codeICAO.length !== 4) {
                toastr.error('Le code ICAO doit comporter exactement 4 caractères');
                e.preventDefault();
                return false;
            }
            
            // Convertir en majuscules
            $('#codeIATA').val(codeIATA.toUpperCase());
            $('#codeICAO').val(codeICAO.toUpperCase());
            
            return true;
        });
        
        // Auto-majuscules
        $('#codeIATA, #codeICAO').on('input', function() {
            this.value = this.value.toUpperCase();
        });
        
        // Géo-localisation
        $('#getLocationBtn').click(function() {
            const ville = $('#ville').val();
            const pays = $('#pays_id option:selected').text();
            
            if (!ville) {
                toastr.error('Veuillez saisir la ville d\'abord');
                return;
            }
            
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Recherche...');
            
            const query = encodeURIComponent(`${ville}, ${pays} airport`);
            
            $.ajax({
                url: `https://nominatim.openstreetmap.org/search?q=${query}&format=json&limit=1`,
                method: 'GET',
                headers: { 'User-Agent': 'AeroportApp/1.0' },
                success: function(data) {
                    if (data && data[0]) {
                        $('#latitude').val(parseFloat(data[0].lat).toFixed(6));
                        $('#longitude').val(parseFloat(data[0].lon).toFixed(6));
                        toastr.success('Coordonnées mises à jour !');
                    } else {
                        toastr.warning('Aucune coordonnée trouvée');
                    }
                },
                error: function() {
                    toastr.error('Erreur lors de la recherche');
                },
                complete: function() {
                    $('#getLocationBtn').prop('disabled', false)
                        .html('<i class="fas fa-map-marker-alt me-1"></i> Obtenir coordonnées');
                }
            });
        });
    });
</script>
@endpush