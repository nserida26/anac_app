{{-- resources/views/admin/examinateurs/show.blade.php --}}
@extends('layouts.admin')

@section('title')
    @lang('trans.examiner_details')
@endsection

@section('contentheader')
    @lang('trans.examiner_details')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user mr-2"></i>
                        {{ $examinateur->nom }} {{ $examinateur->prenom }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.examinateurs.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> @lang('trans.back_to_list')
                        </a>
                        
                        @if($examinateur->statut_validation == 'en_attente')
                        <button class="btn btn-success btn-sm validate-examiner" 
                                data-id="{{ $examinateur->id }}"
                                data-name="{{ $examinateur->nom }} {{ $examinateur->prenom }}">
                            <i class="fas fa-check"></i> @lang('trans.validate')
                        </button>
                        <button class="btn btn-danger btn-sm reject-examiner" 
                                data-id="{{ $examinateur->id }}"
                                data-name="{{ $examinateur->nom }} {{ $examinateur->prenom }}">
                            <i class="fas fa-times"></i> @lang('trans.reject')
                        </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @include('admin.centre-examinateurs.partials.details')
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Inclure les modals de validation/rejet --}}
@include('admin.centre-examinateurs.partials.modals')
@endsection

@push('script')
<script>
$(document).ready(function() {
    // Même JavaScript que dans pending.blade.php pour validation/rejet
    let currentExaminerId = null;
    
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
            url: '{{ route("admin.examinateurs.validate", "") }}/' + currentExaminerId,
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
});
</script>
@endpush