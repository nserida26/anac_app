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
    @lang('trans.dashboard_admin')
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Create Multiple Checklists</h3>
                    <div class="mt-2">
                        <button type="button" class="btn btn-success" id="addChecklistBtn">
                            <i class="fas fa-plus"></i> Add Another Checklist
                        </button>
                        <button type="button" class="btn btn-danger" id="removeChecklistBtn">
                            <i class="fas fa-trash"></i> Remove Last Checklist
                        </button>
                        <span class="ml-3 badge badge-info" id="checklistCount">1</span>
                    </div>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('checklists.store.multiple') }}" method="POST" id="multipleChecklistForm">
                        @csrf
                        <div id="checklistsContainer">
                            <!-- Checklist forms will be added here dynamically -->
                            <div class="checklist-form card mb-4" data-index="0">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Checklist #1</h5>
                                    <button type="button" class="btn btn-sm btn-danger remove-form" data-index="0">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="numero_0">Number *</label>
                                                <input type="text" 
                                                       name="checklists[0][numero]" 
                                                       id="numero_0" 
                                                       class="form-control" 
                                                       value="{{ old('checklists.0.numero') }}" 
                                                       required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="index_0">Index</label>
                                                <input type="text" 
                                                       name="checklists[0][index]" 
                                                       id="index_0" 
                                                       class="form-control" 
                                                       value="{{ old('checklists.0.index') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="type_0">Type</label>
                                                <input type="text" 
                                                       name="checklists[0][type]" 
                                                       id="type_0" 
                                                       class="form-control" 
                                                       value="{{ old('checklists.0.type') }}">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ordre_0">Order</label>
                                                <input type="number" 
                                                       name="checklists[0][ordre]" 
                                                       id="ordre_0" 
                                                       class="form-control" 
                                                       value="{{ old('checklists.0.ordre', '0') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="libelle_0">Label *</label>
                                        <textarea name="checklists[0][libelle]" 
                                                  id="libelle_0" 
                                                  rows="3" 
                                                  class="form-control" 
                                                  required>{{ old('checklists.0.libelle') }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="section_0">Section</label>
                                        <input type="text" 
                                               name="checklists[0][section]" 
                                               id="section_0" 
                                               class="form-control" 
                                               value="{{ old('checklists.0.section') }}">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="type_demande_id_0">Type Demande</label>
                                                <select name="checklists[0][type_demande_id]" 
                                                        id="type_demande_id_0" 
                                                        class="form-control">
                                                    <option value="">Select Type Demande</option>
                                                    @foreach($typeDemandes as $typeDemande)
                                                        <option value="{{ $typeDemande->id }}" 
                                                            {{ old('checklists.0.type_demande_id') == $typeDemande->id ? 'selected' : '' }}>
                                                            {{ $typeDemande->nom_fr }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="type_licence_id_0">Type Licence</label>
                                                <select name="checklists[0][type_licence_id]" 
                                                        id="type_licence_id_0" 
                                                        class="form-control">
                                                    <option value="">Select Type Licence</option>
                                                    @foreach($typeLicences as $typeLicence)
                                                        <option value="{{ $typeLicence->id }}" 
                                                            {{ old('checklists.0.type_licence_id') == $typeLicence->id ? 'selected' : '' }}>
                                                            {{ $typeLicence->nom ?? $typeLicence->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Save All Checklists
                            </button>
                            <a href="{{ route('checklists.index') }}" class="btn btn-secondary btn-lg">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let formCount = 1;
    
    // Function to generate a new form HTML
    function getNewFormHtml(index) {
        return `
            <div class="checklist-form card mb-4" data-index="${index}">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Checklist #${index + 1}</h5>
                    <button type="button" class="btn btn-sm btn-danger remove-form" data-index="${index}">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_${index}">Number *</label>
                                <input type="text" 
                                       name="checklists[${index}][numero]" 
                                       id="numero_${index}" 
                                       class="form-control" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="index_${index}">Index</label>
                                <input type="text" 
                                       name="checklists[${index}][index]" 
                                       id="index_${index}" 
                                       class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type_${index}">Type</label>
                                <input type="text" 
                                       name="checklists[${index}][type]" 
                                       id="type_${index}" 
                                       class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ordre_${index}">Order</label>
                                <input type="number" 
                                       name="checklists[${index}][ordre]" 
                                       id="ordre_${index}" 
                                       class="form-control" 
                                       value="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="libelle_${index}">Label *</label>
                        <textarea name="checklists[${index}][libelle]" 
                                  id="libelle_${index}" 
                                  rows="3" 
                                  class="form-control" 
                                  required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="section_${index}">Section</label>
                        <input type="text" 
                               name="checklists[${index}][section]" 
                               id="section_${index}" 
                               class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type_demande_id_${index}">Type Demande</label>
                                <select name="checklists[${index}][type_demande_id]" 
                                        id="type_demande_id_${index}" 
                                        class="form-control">
                                    <option value="">Select Type Demande</option>
                                    @foreach($typeDemandes as $typeDemande)
                                        <option value="{{ $typeDemande->id }}">{{ $typeDemande->nom_fr }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type_licence_id_${index}">Type Licence</label>
                                <select name="checklists[${index}][type_licence_id]" 
                                        id="type_licence_id_${index}" 
                                        class="form-control">
                                    <option value="">Select Type Licence</option>
                                    @foreach($typeLicences as $typeLicence)
                                        <option value="{{ $typeLicence->id }}">{{ $typeLicence->nom ?? $typeLicence->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Add new checklist form
    $('#addChecklistBtn').click(function() {
        const newForm = getNewFormHtml(formCount);
        $('#checklistsContainer').append(newForm);
        formCount++;
        $('#checklistCount').text(formCount);
    });
    
    // Remove individual checklist form
    $(document).on('click', '.remove-form', function() {
        const $form = $(this).closest('.checklist-form');
        $form.remove();
        formCount--;
        $('#checklistCount').text(formCount);
        reindexForms();
    });
    
    // Remove last checklist
    $('#removeChecklistBtn').click(function() {
        if (formCount > 1) {
            $('.checklist-form:last').remove();
            formCount--;
            $('#checklistCount').text(formCount);
            reindexForms();
        } else {
            alert('At least one checklist form is required');
        }
    });
    
    // Reindex all forms after removal
    function reindexForms() {
        $('.checklist-form').each(function(newIndex) {
            $(this).attr('data-index', newIndex);
            $(this).find('.card-header h5').text(`Checklist #${newIndex + 1}`);
            $(this).find('.remove-form').attr('data-index', newIndex);
            
            // Update all input names and IDs
            $(this).find('input, textarea, select').each(function() {
                const name = $(this).attr('name');
                const id = $(this).attr('id');
                
                if (name) {
                    const newName = name.replace(/checklists\[\d+\]/, `checklists[${newIndex}]`);
                    $(this).attr('name', newName);
                }
                
                if (id) {
                    const newId = id.replace(/_\d+$/, `_${newIndex}`);
                    $(this).attr('id', newId);
                }
            });
            
            // Update labels' for attributes
            $(this).find('label').each(function() {
                const forAttr = $(this).attr('for');
                if (forAttr) {
                    const newFor = forAttr.replace(/_\d+$/, `_${newIndex}`);
                    $(this).attr('for', newFor);
                }
            });
        });
    }
    
    // Form validation before submit
    $('#multipleChecklistForm').on('submit', function(e) {
        let isValid = true;
        let errorMessages = [];
        
        $('.checklist-form').each(function(index) {
            const numero = $(this).find(`input[name="checklists[${index}][numero]"]`).val();
            const libelle = $(this).find(`textarea[name="checklists[${index}][libelle]"]`).val();
            
            if (!numero) {
                $(this).find(`input[name="checklists[${index}][numero]"]`).addClass('is-invalid');
                isValid = false;
                errorMessages.push(`Checklist #${index + 1}: Number is required`);
            } else {
                $(this).find(`input[name="checklists[${index}][numero]"]`).removeClass('is-invalid');
            }
            
            if (!libelle) {
                $(this).find(`textarea[name="checklists[${index}][libelle]"]`).addClass('is-invalid');
                isValid = false;
                errorMessages.push(`Checklist #${index + 1}: Label is required`);
            } else {
                $(this).find(`textarea[name="checklists[${index}][libelle]"]`).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fix the following errors:\n' + errorMessages.join('\n'));
            return false;
        }
    });
    
    // Remove error styling on input
    $(document).on('input', 'input, textarea', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
@endpush