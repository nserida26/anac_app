@extends('layouts.admin')
@section('title')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheaderlink')
    <a href="{{ route('demandeurs') }}">
        @lang('trans.dashboard_admin') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_admin')
@endsection

@push('css')
    <style>

    </style>
@endpush
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.profile')
                    </div>
                    <div class="card-body">
                        @isset($demandeur)
                            <div class="row justify-content-center">

                                <div class="col-lg-9">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th>@lang('trans.fl_name')</th>
                                            <td>{{ $demandeur->np ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.email')</th>
                                            <td>{{ optional($demandeur->user)->email ?? '-' }}</td>
                                        </tr>
                                        <tr>

                                            <th>@lang('trans.dob')</th>
                                            <td>{{ !empty($demandeur->date_naissance) ? date('Y-m-d', strtotime($demandeur->date_naissance)) : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.lieu_naissance')</th>
                                            <td>{{ $demandeur->lieu_naissance ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.address')</th>
                                            <td>{{ $demandeur->adresse ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.adresse_employeur')</th>
                                            <td>{{ $demandeur->compagnie->adresse ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.signature')</th>
                                            <td class="text-center">
                                                @if (isset($demandeur->signature) && $demandeur->signature != '')
                                                    <img src="{{ asset('/uploads/' . $demandeur->signature) }}"
                                                        alt="User Signature" class="img-thumbnail" width="120">
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Profile Picture -->
                                <div class="col-lg-3 text-center">
                                    <img src="{{ asset('/uploads/' . ($demandeur->photo ?? 'default.png')) }}"
                                        alt="Profile Picture" class="img-fluid rounded-circle"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Examinateur Status</label>
                                    <div class="d-flex align-items-center">
                                        <div class="form-check form-switch me-3">
                                            <input class="form-check-input examinateur-toggle" 
                                                   type="checkbox" 
                                                   id="is_examinateur"
                                                   data-demandeur-id="{{ $demandeur->id }}"
                                                   {{ $demandeur->is_examinateur ? 'checked' : '' }}>
                                        </div>
                                        <span class="badge bg-{{ $demandeur->is_examinateur ? 'success' : 'secondary' }}">
                                            {{ $demandeur->is_examinateur ? 'Examinateur' : 'Non Examinateur' }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Instructeur Status</label>
                                    <div class="d-flex align-items-center">
                                        <div class="form-check form-switch me-3">
                                            <input class="form-check-input instructeur-toggle" 
                                                   type="checkbox" 
                                                   id="is_instructeur"
                                                   data-demandeur-id="{{ $demandeur->id }}"
                                                   {{ $demandeur->is_instructeur ? 'checked' : '' }}>
                                        </div>
                                        <span class="badge bg-{{ $demandeur->is_instructeur ? 'success' : 'secondary' }}">
                                            {{ $demandeur->is_instructeur ? 'Instructeur' : 'Non Instructeur' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('script')

<script>
$(document).ready(function() {
    // CSRF token setup for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Examinateur toggle
    $('.examinateur-toggle').change(function() {
        const demandeurId = $(this).data('demandeur-id');
        const isActive = $(this).is(':checked');
        
        toggleStatus(demandeurId, 'is_examinateur', isActive, $(this));
    });

    // Instructeur toggle
    $('.instructeur-toggle').change(function() {
        const demandeurId = $(this).data('demandeur-id');
        const isActive = $(this).is(':checked');
        
        toggleStatus(demandeurId, 'is_instructeur', isActive, $(this));
    });

    function toggleStatus(demandeurId, field, isActive, toggleElement) {
        // Show loading state
        const originalState = toggleElement.is(':checked');
        toggleElement.prop('disabled', true);
        
        $.ajax({
            url: "{{ route('demandeurs.toggle-status', ':id') }}".replace(':id', demandeurId),
            method: 'PATCH',
            data: {
                field: field,
                value: isActive ? 1 : 0
            },
            success: function(response) {
                // Update label text
                
                
                
                // Update badge
                const badge = toggleElement.closest('.d-flex').find('.badge');
                if (field === 'is_examinateur') {
                    badge.text(isActive ? 'Examinateur' : 'Non Examinateur');
                    badge.removeClass('bg-success bg-secondary').addClass(isActive ? 'bg-success' : 'bg-secondary');
                } else {
                    badge.text(isActive ? 'Instructeur' : 'Non Instructeur');
                    badge.removeClass('bg-success bg-secondary').addClass(isActive ? 'bg-success' : 'bg-secondary');
                }
                
                // Show success message
                showAlert('Statut updated!', 'success');
            },
            error: function(xhr) {
                // Revert toggle state on error
                toggleElement.prop('checked', !isActive);
                
                // Show error message
                showAlert('Erreur lors de la mise � jour du statut', 'error');
                
                console.error('Error:', xhr.responseText);
            },
            complete: function() {
                toggleElement.prop('disabled', false);
            }
        });
    }

    function showAlert(message, type) {
        // Remove existing alerts
        $('.alert-dismissible').remove();
        
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.card-header').after(alertHtml);
        
        // Auto dismiss after 3 seconds
        setTimeout(() => {
            $('.alert-dismissible').alert('close');
        }, 3000);
    }
});
</script>

@endpush
