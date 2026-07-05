{{-- resources/views/centre/examinateurs/index.blade.php --}}
@extends('centre.layouts.app')

@section('title')
    @lang('trans.examiners_management')
@endsection

@section('contentheader')
    @lang('trans.examiners_management')
@endsection

@section('contentheaderactive')
    @lang('trans.examiners_list')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-check mr-2"></i>
                        @lang('trans.examiners_list')
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addExaminateurModal">
                            <i class="fas fa-plus"></i> @lang('trans.add_examiner')
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
                                <th>@lang('trans.licence_number')</th>
                                <th>@lang('trans.validity_period')</th>
                                <th>@lang('trans.validation_status')</th>
                                <th>@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($examinateurs as $examinateur)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $examinateur->nom_complet }}</td>
                                <td>{{ $examinateur->email }}</td>
                                <td>{{ $examinateur->numero_licence_examinateur }}</td>
                                <td>
                                    {{ $examinateur->date_debut_validite->format('d/m/Y') }} - 
                                    {{ $examinateur->date_fin_validite->format('d/m/Y') }}
                                    @if($examinateur->date_fin_validite < now())
                                        <span class="badge badge-danger">@lang('trans.expired')</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($examinateur->statut_validation)
                                        @case('en_attente')
                                            <span class="badge badge-warning">@lang('trans.pending')</span>
                                            @break
                                        @case('valide')
                                            <span class="badge badge-success">@lang('trans.validated')</span>
                                            @break
                                        @case('refuse')
                                            <span class="badge badge-danger">@lang('trans.rejected')</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm view-examinateur" data-id="{{ $examinateur->id }}" title="@lang('trans.view')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ asset('/uploads/' . $examinateur->document_justificatif) }}" class="btn btn-success btn-sm" target="_blank" title="@lang('trans.download_document')">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="alert alert-info m-3">
                                        <i class="fas fa-info-circle"></i> @lang('trans.no_examiners_found')
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($examinateurs->hasPages())
                <div class="card-footer">
                    {{ $examinateurs->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Note d'information --}}
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                @lang('trans.examiner_validation_note')
            </div>
        </div>
    </div>
</div>

{{-- Modal pour ajouter un examinateur --}}
<div class="modal fade" id="addExaminateurModal" tabindex="-1" role="dialog" aria-labelledby="addExaminateurModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('centre.examinateurs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addExaminateurModalLabel">
                        <i class="fas fa-plus-circle"></i> @lang('trans.add_examiner')
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        @lang('trans.examiner_requires_validation')
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nom_exam">@lang('trans.last_name') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom_exam" name="nom" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="prenom_exam">@lang('trans.first_name') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="prenom_exam" name="prenom" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email_exam">@lang('trans.email') <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email_exam" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telephone_exam">@lang('trans.phone') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="telephone_exam" name="telephone" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_licence_examinateur">@lang('trans.examiner_licence_number') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="numero_licence_examinateur" name="numero_licence_examinateur" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_naissance_exam">@lang('trans.birth_date') <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_naissance_exam" name="date_naissance" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nationalite_exam">@lang('trans.nationality') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nationalite_exam" name="nationalite" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="adresse_exam">@lang('trans.address') <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="adresse_exam" name="adresse" rows="2" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_debut_validite">@lang('trans.validity_start_date') <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_debut_validite" name="date_debut_validite" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_fin_validite">@lang('trans.validity_end_date') <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_fin_validite" name="date_fin_validite" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="document_justificatif_exam">@lang('trans.supporting_document') <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="document_justificatif_exam" name="document_justificatif" accept=".pdf" required>
                            <label class="custom-file-label" for="document_justificatif_exam">@lang('trans.choose_file')</label>
                        </div>
                        <small class="form-text text-muted">@lang('trans.accepted_format'): PDF (Max 10MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> @lang('trans.cancel')
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> @lang('trans.submit_for_validation')
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
    // Afficher le nom du fichier sélectionné
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
    
    // Validation des dates
    $('#date_fin_validite').on('change', function() {
        let dateDebut = $('#date_debut_validite').val();
        let dateFin = $(this).val();
        
        if (dateDebut && dateFin && dateFin <= dateDebut) {
            toastr.error('@lang("trans.end_date_must_be_after_start_date")');
            $(this).val('');
        }
    });
});
</script>
@endpush