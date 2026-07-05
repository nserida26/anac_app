{{-- resources/views/admin/examinateurs/pending.blade.php --}}
@extends('layouts.admin')

@section('title')
    @lang('trans.pending_examiners')
@endsection

@section('contentheader')
    @lang('trans.examiners_validation')
@endsection

@section('content')
<div class="container-fluid">
    {{-- Statistiques --}}
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['total_pending'] }}</h3>
                    <p>@lang('trans.pending_validation')</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['total_validated'] }}</h3>
                    <p>@lang('trans.validated')</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['total_rejected'] }}</h3>
                    <p>@lang('trans.rejected')</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_expired'] }}</h3>
                    <p>@lang('trans.expired')</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Liste des examinateurs en attente --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-clock mr-2"></i>
                        @lang('trans.pending_examiners_list')
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.examinateurs.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-list"></i> @lang('trans.all_examiners')
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    @if($examinateurs->count() > 0)
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('trans.examiner')</th>
                                <th>@lang('trans.training_center')</th>
                                <th>@lang('trans.licence_number')</th>
                                <th>@lang('trans.request_date')</th>
                                <th>@lang('trans.validity_period')</th>
                                <th>@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($examinateurs as $examinateur)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $examinateur->nom }} {{ $examinateur->prenom }}</strong><br>
                                    <small class="text-muted">
                                        <i class="fas fa-envelope"></i> {{ $examinateur->email }}<br>
                                        <i class="fas fa-phone"></i> {{ $examinateur->telephone }}
                                    </small>
                                </td>
                                <td>
                                    {{ $examinateur->centreFormation->libelle ?? 'N/A' }}<br>
                                    <small class="text-muted">
                                        <i class="fas fa-user"></i> {{ $examinateur->centreFormation->user->email ?? 'N/A' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge badge-primary">
                                        {{ $examinateur->numero_licence_examinateur }}
                                    </span>
                                </td>
                                <td>{{ $examinateur->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <strong>@lang('trans.from'):</strong> {{ $examinateur->date_debut_validite->format('d/m/Y') }}<br>
                                    <strong>@lang('trans.to'):</strong> {{ $examinateur->date_fin_validite->format('d/m/Y') }}
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm view-examiner" 
                                            data-id="{{ $examinateur->id }}"
                                            title="@lang('trans.view_details')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-success btn-sm validate-examiner" 
                                            data-id="{{ $examinateur->id }}"
                                            data-name="{{ $examinateur->nom }} {{ $examinateur->prenom }}"
                                            title="@lang('trans.validate')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm reject-examiner" 
                                            data-id="{{ $examinateur->id }}"
                                            data-name="{{ $examinateur->nom }} {{ $examinateur->prenom }}"
                                            title="@lang('trans.reject')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <a href="{{ asset('/uploads/' . $examinateur->document_justificatif) }}" 
                                       class="btn btn-secondary btn-sm" 
                                       target="_blank"
                                       title="@lang('trans.download_document')">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-info m-3">
                        <i class="fas fa-info-circle"></i> @lang('trans.no_pending_examiners')
                    </div>
                    @endif
                </div>
                @if($examinateurs->hasPages())
                <div class="card-footer">
                    {{ $examinateurs->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal de validation --}}
<div class="modal fade" id="validateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="validateForm">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle"></i> @lang('trans.validate_examiner')
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>@lang('trans.validate_examiner_confirm'): <strong id="validateExaminerName"></strong></p>
                    
                    <div class="form-group">
                        <label>@lang('trans.validity_end_date')</label>
                        <input type="date" class="form-control" name="date_fin_validite" id="validate_date_fin">
                        <small class="form-text text-muted">
                            @lang('trans.leave_empty_to_keep_current_date')
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label>@lang('trans.comment') (@lang('trans.optional'))</label>
                        <textarea class="form-control" name="commentaire" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> @lang('trans.cancel')
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> @lang('trans.confirm_validation')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal de rejet --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle"></i> @lang('trans.reject_examiner')
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>@lang('trans.reject_examiner_confirm'): <strong id="rejectExaminerName"></strong></p>
                    
                    <div class="form-group">
                        <label>@lang('trans.rejection_reason') <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="motif_refus" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> @lang('trans.cancel')
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> @lang('trans.confirm_rejection')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal de détails --}}
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user"></i> @lang('trans.examiner_details')
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="examinerDetailsContent">
                <!-- Chargé dynamiquement -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>

<script>
$(document).ready(function() {
    let currentExaminerId = null;
    
    // Validation
    $('.validate-examiner').on('click', function() {
        currentExaminerId = $(this).data('id');
        var name = $(this).data('name');
        
        $('#validateExaminerName').text(name);
        $('#validateModal').modal('show');
    });
    
    $('#validateForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: '{{ route("admin.examinateurs.validate", ":id") }}'.replace(':id', currentExaminerId),
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#validateForm button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> @lang("trans.processing")').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('@lang("trans.error_occurred")');
            },
            complete: function() {
                $('#validateForm button[type="submit"]').html('<i class="fas fa-check"></i> @lang("trans.confirm_validation")').prop('disabled', false);
                $('#validateModal').modal('hide');
            }
        });
    });
    
    // Rejet
    $('.reject-examiner').on('click', function() {
        currentExaminerId = $(this).data('id');
        var name = $(this).data('name');
        
        $('#rejectExaminerName').text(name);
        $('#rejectModal').modal('show');
    });
    
    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: '{{ route("admin.examinateurs.reject", "") }}/' + currentExaminerId,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#rejectForm button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> @lang("trans.processing")').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('@lang("trans.error_occurred")');
            },
            complete: function() {
                $('#rejectForm button[type="submit"]').html('<i class="fas fa-times"></i> @lang("trans.confirm_rejection")').prop('disabled', false);
                $('#rejectModal').modal('hide');
            }
        });
    });
    
    // Voir les détails
    $('.view-examiner').on('click', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: '{{ route("admin.examinateurs.show", "") }}/' + id,
            type: 'GET',
            success: function(response) {
                $('#examinerDetailsContent').html(response);
                $('#detailsModal').modal('show');
            },
            error: function() {
                toastr.error('@lang("trans.error_loading_details")');
            }
        });
    });
});
</script>
@endpush