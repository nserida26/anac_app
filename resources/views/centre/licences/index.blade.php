{{-- resources/views/centre/licences/index.blade.php --}}
@extends('centre.layouts.app')

@section('title')
    @lang('trans.licences_management')
@endsection

@section('contentheader')
    @lang('trans.licences_management')
@endsection

@section('contentheaderactive')
    @lang('trans.licences_list')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-certificate mr-2"></i>
                        @lang('trans.centre_licences')
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addLicenceModal">
                            <i class="fas fa-plus"></i> @lang('trans.add_licence')
                        </button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('trans.licence_type')</th>
                                <th>@lang('trans.obtention_date')</th>
                                <th>@lang('trans.expiry_date')</th>
                                <th>@lang('trans.status')</th>
                                <th>@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($licences as $licence)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $licence->typeLicence->nom }}</td>
                                <td>{{ $licence->date_obtention->format('d/m/Y') }}</td>
                                <td>
                                    {{ $licence->date_expiration->format('d/m/Y') }}
                                    @if($licence->date_expiration < now())
                                        <span class="badge badge-danger">@lang('trans.expired')</span>
                                    @elseif($licence->date_expiration < now()->addMonths(1))
                                        <span class="badge badge-warning">@lang('trans.expires_soon')</span>
                                    @endif
                                </td>
                                <td>
                                    @if($licence->statut == 'actif')
                                        <span class="badge badge-success">@lang('trans.active')</span>
                                    @elseif($licence->statut == 'expire')
                                        <span class="badge badge-danger">@lang('trans.expired')</span>
                                    @else
                                        <span class="badge badge-warning">@lang('trans.pending')</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm view-licence" data-id="{{ $licence->id }}" title="@lang('trans.view')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{  asset('/uploads/' . $licence->document_justificatif) }}" class="btn btn-success btn-sm" target="_blank" title="@lang('trans.download_document')">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="alert alert-info m-3">
                                        <i class="fas fa-info-circle"></i> @lang('trans.no_licences_found')
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($licences->hasPages())
                <div class="card-footer">
                    {{ $licences->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal pour ajouter une licence --}}
<div class="modal fade" id="addLicenceModal" tabindex="-1" role="dialog" aria-labelledby="addLicenceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('centre.licences.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addLicenceModalLabel">
                        <i class="fas fa-plus-circle"></i> @lang('trans.add_licence')
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="type_licence_id">@lang('trans.licence_type') <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="type_licence_id" name="type_licence_id" required>
                            <option value="">@lang('trans.select_licence_type')</option>
                            @foreach($typeLicences as $licence)
                                <option value="{{ $licence->id }}">{{ $licence->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_obtention">@lang('trans.obtention_date') <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_obtention" name="date_obtention" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_expiration">@lang('trans.expiry_date') <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_expiration" name="date_expiration" required>
                            </div>
                        </div>
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
    $('#type_licence_id').select2({
        placeholder: '@lang("trans.select_licence_type")',
        allowClear: true
    });
    
    // Afficher le nom du fichier sélectionné
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
    
    // Validation des dates
    $('#date_expiration').on('change', function() {
        let dateObtention = $('#date_obtention').val();
        let dateExpiration = $(this).val();
        
        if (dateObtention && dateExpiration && dateExpiration <= dateObtention) {
            toastr.error('@lang("trans.expiry_date_must_be_after_obtention_date")');
            $(this).val('');
        }
    });
});
</script>
@endpush