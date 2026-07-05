{{-- resources/views/admin/examinateurs/partials/details.blade.php --}}
<div class="row">
    <div class="col-md-6">
        <h6 class="mb-3"><i class="fas fa-user"></i> @lang('trans.personal_information')</h6>
        
        <table class="table table-sm">
            <tr>
                <th width="40%">@lang('trans.full_name'):</th>
                <td>{{ $examinateur->nom }} {{ $examinateur->prenom }}</td>
            </tr>
            <tr>
                <th>@lang('trans.email'):</th>
                <td>{{ $examinateur->email }}</td>
            </tr>
            <tr>
                <th>@lang('trans.phone'):</th>
                <td>{{ $examinateur->telephone }}</td>
            </tr>
            <tr>
                <th>@lang('trans.birth_date'):</th>
                <td>{{ $examinateur->date_naissance->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>@lang('trans.nationality'):</th>
                <td>{{ $examinateur->nationalite }}</td>
            </tr>
            <tr>
                <th>@lang('trans.address'):</th>
                <td>{{ $examinateur->adresse }}</td>
            </tr>
        </table>
    </div>
    
    <div class="col-md-6">
        <h6 class="mb-3"><i class="fas fa-id-card"></i> @lang('trans.licence_information')</h6>
        
        <table class="table table-sm">
            <tr>
                <th width="40%">@lang('trans.licence_number'):</th>
                <td>
                    <span class="badge badge-primary">
                        {{ $examinateur->numero_licence_examinateur }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>@lang('trans.validity_period'):</th>
                <td>
                    {{ $examinateur->date_debut_validite->format('d/m/Y') }} - 
                    {{ $examinateur->date_fin_validite->format('d/m/Y') }}
                </td>
            </tr>
            <tr>
                <th>@lang('trans.training_center'):</th>
                <td>{{ $examinateur->centreFormation->libelle ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>@lang('trans.status'):</th>
                <td>
                    @if($examinateur->statut_validation == 'en_attente')
                        <span class="badge badge-warning">@lang('trans.pending')</span>
                    @elseif($examinateur->statut_validation == 'valide')
                        <span class="badge badge-success">@lang('trans.validated')</span>
                    @else
                        <span class="badge badge-danger">@lang('trans.rejected')</span>
                    @endif
                </td>
            </tr>
            
            @if($examinateur->statut_validation == 'valide')
            <tr>
                <th>@lang('trans.validated_by'):</th>
                <td>{{ $examinateur->validePar->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>@lang('trans.validation_date'):</th>
                <td>{{ $examinateur->date_validation ? $examinateur->date_validation->format('d/m/Y H:i') : 'N/A' }}</td>
            </tr>
            @endif
            
            @if($examinateur->statut_validation == 'refuse' && $examinateur->motif_refus)
            <tr>
                <th>@lang('trans.rejection_reason'):</th>
                <td class="text-danger">{{ $examinateur->motif_refus }}</td>
            </tr>
            @endif
        </table>
    </div>
</div>

@if($examinateur->formations->count() > 0)
<div class="row mt-3">
    <div class="col-md-12">
        <h6 class="mb-3"><i class="fas fa-graduation-cap"></i> @lang('trans.trainings_assigned')</h6>
        
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>@lang('trans.date')</th>
                    <th>@lang('trans.training_type')</th>
                    <th>@lang('trans.demandeur')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($examinateur->formations as $formation)
                <tr>
                    <td>{{ $formation->date_formation->format('d/m/Y') }}</td>
                    <td>{{ $formation->typeFormation->nom ?? 'N/A' }}</td>
                    <td>{{ $formation->demandeur->np ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="row mt-3">
    <div class="col-md-12 text-right">
        <a href="{{ asset('/uploads/' . $examinateur->document_justificatif) }}" 
           class="btn btn-success" 
           target="_blank">
            <i class="fas fa-download"></i> @lang('trans.download_document')
        </a>
    </div>
</div>