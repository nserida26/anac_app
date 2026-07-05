{{-- resources/views/centre/instructeurs/index.blade.php --}}
@extends('centre.layouts.app')

@section('title')
    @lang('trans.instructors_management')
@endsection

@section('contentheader')
    @lang('trans.instructors_management')
@endsection

@section('contentheaderactive')
    @lang('trans.instructors_list')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chalkboard-teacher mr-2"></i>
                        @lang('trans.instructors_list')
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addInstructeurModal">
                            <i class="fas fa-plus"></i> @lang('trans.add_instructor')
                        </button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('trans.name')</th>
                                <th>@lang('trans.email')</th>
                                <th>@lang('trans.phone')</th>
                                <th>@lang('trans.licence_number')</th>
                                <th>@lang('trans.qualifications')</th>
                                <th>@lang('trans.status')</th>
                                <th>@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($instructeurs as $instructeur)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $instructeur->nom_complet }}</td>
                                <td>{{ $instructeur->email }}</td>
                                <td>{{ $instructeur->telephone }}</td>
                                <td>{{ $instructeur->numero_licence }}</td>
                                <td>
                                    @if($instructeur->qualifications)
                                        @foreach($instructeur->qualifications as $qualification)
                                            <span class="badge badge-info">{{ $qualification }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($instructeur->statut == 'actif')
                                        <span class="badge badge-success">@lang('trans.active')</span>
                                    @else
                                        <span class="badge badge-danger">@lang('trans.inactive')</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm view-instructeur" data-id="{{ $instructeur->id }}" title="@lang('trans.view')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{  asset('/uploads/' . $instructeur->document_justificatif) }}" class="btn btn-success btn-sm" target="_blank" title="@lang('trans.download_document')">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button class="btn btn-warning btn-sm edit-instructeur" data-id="{{ $instructeur->id }}" title="@lang('trans.edit')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="alert alert-info m-3">
                                        <i class="fas fa-info-circle"></i> @lang('trans.no_instructors_found')
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($instructeurs->hasPages())
                <div class="card-footer">
                    {{ $instructeurs->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal pour ajouter un instructeur --}}
<div class="modal fade" id="addInstructeurModal" tabindex="-1" role="dialog" aria-labelledby="addInstructeurModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('centre.instructeurs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addInstructeurModalLabel">
                        <i class="fas fa-plus-circle"></i> @lang('trans.add_instructor')
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nom">@lang('trans.last_name') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="prenom">@lang('trans.first_name') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">@lang('trans.email') <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telephone">@lang('trans.phone') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="telephone" name="telephone" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_licence">@lang('trans.licence_number') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="numero_licence" name="numero_licence" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_naissance">@lang('trans.birth_date') <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nationalite">@lang('trans.nationality') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nationalite" name="nationalite" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="statut">@lang('trans.status')</label>
                                <select class="form-control" id="statut" name="statut">
                                    <option value="actif">@lang('trans.active')</option>
                                    <option value="inactif">@lang('trans.inactive')</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="adresse">@lang('trans.address') <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="adresse" name="adresse" rows="2" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="qualifications">@lang('trans.qualifications')</label>
                        <select class="form-control select2" id="qualifications" name="qualifications[]" multiple>
                            <option value="PPL">PPL - Private Pilot License</option>
                            <option value="CPL">CPL - Commercial Pilot License</option>
                            <option value="ATPL">ATPL - Airline Transport Pilot License</option>
                            <option value="IR">IR - Instrument Rating</option>
                            <option value="MEP">MEP - Multi Engine Piston</option>
                            <option value="FI">FI - Flight Instructor</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="document_justificatif">@lang('trans.supporting_document') <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="document_justificatif" name="document_justificatif" accept=".pdf" required>
                            <label class="custom-file-label" for="document_justificatif">@lang('trans.choose_file')</label>
                        </div>
                        <small class="form-text text-muted">@lang('trans.accepted_format'): PDF (Max 10MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> @lang('trans.cancel')
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> @lang('trans.save')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function() {
    // Initialiser Select2
    $('#qualifications').select2({
        placeholder: '@lang("trans.select_qualifications")',
        allowClear: true
    });
    
    // Afficher le nom du fichier sélectionné
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
});
</script>
@endpush