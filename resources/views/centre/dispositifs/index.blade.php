{{-- resources/views/centre/dispositifs/index.blade.php --}}
@extends('centre.layouts.app')

@section('title')
    @lang('trans.training_devices_management')
@endsection

@section('contentheader')
    @lang('trans.training_devices_management')
@endsection

@section('contentheaderactive')
    @lang('trans.devices_list')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-microchip mr-2"></i>
                        @lang('trans.training_devices_list')
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addDispositifModal">
                            <i class="fas fa-plus"></i> @lang('trans.add_device')
                        </button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('trans.simulator')</th>
                                <th>@lang('trans.acquisition_date')</th>
                                <th>@lang('trans.last_certification')</th>
                                <th>@lang('trans.certification_expiry')</th>
                                <th>@lang('trans.status')</th>
                                <th>@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dispositifs as $dispositif)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $dispositif->simulateur->libelle }}</strong><br>
                                    <small class="text-muted">{{ $dispositif->simulateur->compagnie ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $dispositif->date_acquisition->format('d/m/Y') }}</td>
                                <td>{{ $dispositif->date_derniere_certification->format('d/m/Y') }}</td>
                                <td>
                                    {{ $dispositif->date_expiration_certification->format('d/m/Y') }}
                                    @if($dispositif->date_expiration_certification < now())
                                        <span class="badge badge-danger">@lang('trans.expired')</span>
                                    @elseif($dispositif->date_expiration_certification < now()->addMonths(1))
                                        <span class="badge badge-warning">@lang('trans.expires_soon')</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($dispositif->statut)
                                        @case('operationnel')
                                            <span class="badge badge-success">@lang('trans.operational')</span>
                                            @break
                                        @case('maintenance')
                                            <span class="badge badge-warning">@lang('trans.maintenance')</span>
                                            @break
                                        @case('hors_service')
                                            <span class="badge badge-danger">@lang('trans.out_of_service')</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm view-dispositif" data-id="{{ $dispositif->id }}" title="@lang('trans.view')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ asset('/uploads/' . $dispositif->certificat_document) }}" class="btn btn-success btn-sm" target="_blank" title="@lang('trans.download_certificate')">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button class="btn btn-warning btn-sm edit-dispositif" data-id="{{ $dispositif->id }}" title="@lang('trans.edit')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="alert alert-info m-3">
                                        <i class="fas fa-info-circle"></i> @lang('trans.no_devices_found')
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($dispositifs->hasPages())
                <div class="card-footer">
                    {{ $dispositifs->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal pour ajouter un dispositif --}}
<div class="modal fade" id="addDispositifModal" tabindex="-1" role="dialog" aria-labelledby="addDispositifModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('centre.dispositifs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addDispositifModalLabel">
                        <i class="fas fa-plus-circle"></i> @lang('trans.add_training_device')
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="simulateur_id">@lang('trans.select_simulator') <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="simulateur_id" name="simulateur_id" required>
                            <option value="">@lang('trans.select_simulator')</option>
                            @foreach($simulateurs as $simulateur)
                                <option value="{{ $simulateur->id }}">
                                    {{ $simulateur->libelle }} 
                                    @if($simulateur->type_avion_id)
                                        ({{ $simulateur->typeAvion->code ?? 'N/A' }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_acquisition">@lang('trans.acquisition_date') <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_acquisition" name="date_acquisition" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_derniere_certification">@lang('trans.last_certification_date') <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_derniere_certification" name="date_derniere_certification" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_expiration_certification">@lang('trans.certification_expiry_date') <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_expiration_certification" name="date_expiration_certification" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="statut">@lang('trans.status')</label>
                        <select class="form-control" id="statut" name="statut">
                            <option value="operationnel">@lang('trans.operational')</option>
                            <option value="maintenance">@lang('trans.maintenance')</option>
                            <option value="hors_service">@lang('trans.out_of_service')</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="certificat_document">@lang('trans.certification_document') <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="certificat_document" name="certificat_document" accept=".pdf" required>
                            <label class="custom-file-label" for="certificat_document">@lang('trans.choose_file')</label>
                        </div>
                        <small class="form-text text-muted">@lang('trans.accepted_format'): PDF (Max 10MB)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">@lang('trans.notes')</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="@lang('trans.additional_notes')"></textarea>
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
    $('#simulateur_id').select2({
        placeholder: '@lang("trans.select_simulator")',
        allowClear: true
    });
    
    // Afficher le nom du fichier sélectionné
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
    
    // Validation des dates
    $('#date_expiration_certification').on('change', function() {
        let dateCertification = $('#date_derniere_certification').val();
        let dateExpiration = $(this).val();
        
        if (dateCertification && dateExpiration && dateExpiration <= dateCertification) {
            toastr.error('@lang("trans.expiry_date_must_be_after_certification_date")');
            $(this).val('');
        }
    });
});
</script>
@endpush