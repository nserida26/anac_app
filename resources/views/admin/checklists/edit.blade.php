
@extends('layouts.admin')
@section('title')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheaderlink')
    <a href="{{ route('demandes') }}">
        @lang('trans.dashboard_admin') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_admin')
@endsection


@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Checklist</h3>
                    <div class="float-right">
                        <a href="{{ route('checklists.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('checklists.update', $checklist) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="numero">Number <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="numero" 
                                   id="numero" 
                                   class="form-control @error('numero') is-invalid @enderror" 
                                   value="{{ old('numero', $checklist->numero) }}" 
                                   required>
                            @error('numero')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="index">Index</label>
                            <input type="text" 
                                   name="index" 
                                   id="index" 
                                   class="form-control @error('index') is-invalid @enderror" 
                                   value="{{ old('index', $checklist->index) }}">
                            @error('index')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="type">Type</label>
                            <input type="text" 
                                   name="type" 
                                   id="type" 
                                   class="form-control @error('type') is-invalid @enderror" 
                                   value="{{ old('type', $checklist->type) }}">
                            @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="libelle">Label <span class="text-danger">*</span></label>
                            <textarea name="libelle" 
                                      id="libelle" 
                                      rows="3" 
                                      class="form-control @error('libelle') is-invalid @enderror" 
                                      required>{{ old('libelle', $checklist->libelle) }}</textarea>
                            @error('libelle')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="section">Section</label>
                            <input type="text" 
                                   name="section" 
                                   id="section" 
                                   class="form-control @error('section') is-invalid @enderror" 
                                   value="{{ old('section', $checklist->section) }}">
                            @error('section')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="ordre">Order</label>
                            <input type="text" 
                                   name="ordre" 
                                   id="ordre" 
                                   class="form-control @error('ordre') is-invalid @enderror" 
                                   value="{{ old('ordre', $checklist->ordre ?? 0) }}">
                            @error('ordre')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="type_demande_id">Type Demande</label>
                            <select name="type_demande_id" 
                                    id="type_demande_id" 
                                    class="form-control @error('type_demande_id') is-invalid @enderror">
                                <option value="">-- Select Type Demande --</option>
                                @foreach($typeDemandes as $typeDemande)
                                    <option value="{{ $typeDemande->id }}" 
                                        {{ old('type_demande_id', $checklist->type_demande_id) == $typeDemande->id ? 'selected' : '' }}>
                                        {{ $typeDemande->nom_fr }} ({{ $typeDemande->nom_en }})
                                    </option>
                                @endforeach
                            </select>
                            @error('type_demande_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="type_licence_id">Type Licence</label>
                            <select name="type_licence_id" 
                                    id="type_licence_id" 
                                    class="form-control @error('type_licence_id') is-invalid @enderror">
                                <option value="">-- Select Type Licence --</option>
                                @foreach($typeLicences as $typeLicence)
                                    <option value="{{ $typeLicence->id }}" 
                                        {{ old('type_licence_id', $checklist->type_licence_id) == $typeLicence->id ? 'selected' : '' }}>
                                        {{ $typeLicence->nom ?? $typeLicence->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type_licence_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Checklist
                            </button>
                            <a href="{{ route('checklists.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <a href="{{ route('checklists.show', $checklist) }}" class="btn btn-info">
                                View Details
                            </a>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer text-muted">
                    <small>
                        <strong>Created:</strong> {{ $checklist->created_at->format('Y-m-d H:i:s') }} | 
                        <strong>Last Updated:</strong> {{ $checklist->updated_at->format('Y-m-d H:i:s') }}
                    </small>
                </div>
            </div>
            
            <!-- Additional Information Card (Optional) -->
            @if($checklist->demandeChecklists->count() > 0)
            <div class="card mt-3">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Warning: Associated Records</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0 text-warning">
                        <i class="fa fa-exclamation-triangle"></i> 
                        This checklist is associated with {{ $checklist->demandeChecklists->count() }} demande(s). 
                        Changes to this checklist may affect existing demandes.
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Optional: Add JavaScript for dynamic behavior
    $(document).ready(function() {
        // You can add AJAX functionality here if needed
        // For example, auto-fill certain fields based on selections
        
        $('#type_demande_id').on('change', function() {
            var typeDemandeId = $(this).val();
            if (typeDemandeId) {
                // Optional: Fetch related data based on type demande
                $.ajax({
                    url: '{{ route("checklists.by-type-demande", "") }}/' + typeDemandeId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Do something with the data if needed
                        console.log('Checklists for this type demande:', data);
                    }
                });
            }
        });
    });
</script>
@endpush