

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
    Nouvel Aéroport
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>Nouvel Aéroport
                        </h4>
                        <a href="{{ route('aeroports.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('aeroports.store') }}" method="POST" id="createForm">
                        @csrf
                        
                        @include('admin.aeroports.partials._form')
                        
                        <div class="card-footer bg-transparent border-top-0 pt-4">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('aeroports.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-times me-1"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Enregistrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Validation spécifique pour la création
        $('#createForm').on('submit', function(e) {
            // Vérifier l'unicité du code IATA via AJAX si nécessaire
            const codeIATA = $('#codeIATA').val().trim().toUpperCase();
            
            if (codeIATA.length !== 3) {
                toastr.error('Le code IATA doit comporter exactement 3 caractères');
                e.preventDefault();
                return false;
            }
            
            return true;
        });
    });
</script>
@endpush