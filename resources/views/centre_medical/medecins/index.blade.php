{{-- resources/views/centre_medical/medecins/index.blade.php --}}
@extends('centre.layouts.app')

@section('title')
    @lang('trans.medecins_management')
@endsection

@section('contentheader')
    @lang('trans.medecins_management')
@endsection

@section('contentheaderactive')
    @lang('trans.medecins_list')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-md mr-2"></i>
                        @lang('trans.medecins_list')
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addMedecinModal">
                            <i class="fas fa-plus"></i> @lang('trans.add_medecin')
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
                                <th>@lang('trans.numero_ordre')</th>
                                <th>@lang('trans.status')</th>
                                <th>@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($medecins as $medecin)
                            <tr>
                                <td>{{ $medecins->firstItem() + $loop->index }}</td>
                                <td>{{ $medecin->nom_complet }}</td>
                                <td>{{ $medecin->email }}</td>
                                <td>{{ $medecin->telephone }}</td>
                                <td>{{ $medecin->numero_ordre }}</td>
                                <td>
                                    @if($medecin->statut == 'actif')
                                        <span class="badge badge-success">@lang('trans.active')</span>
                                    @else
                                        <span class="badge badge-danger">@lang('trans.inactive')</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ asset('/uploads/' . $medecin->document_justificatif) }}" class="btn btn-success btn-sm" target="_blank" title="@lang('trans.download_document')">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="alert alert-info m-3">
                                        <i class="fas fa-info-circle"></i> @lang('trans.no_medecins_found')
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($medecins->hasPages())
                <div class="card-footer">
                    {{ $medecins->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal pour ajouter un médecin --}}
<div class="modal fade" id="addMedecinModal" tabindex="-1" role="dialog" aria-labelledby="addMedecinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('centre_medical.medecins.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addMedecinModalLabel">
                        <i class="fas fa-plus-circle"></i> @lang('trans.add_medecin')
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
                                <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="prenom">@lang('trans.first_name') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">@lang('trans.email') <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telephone">@lang('trans.phone') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="telephone" name="telephone" value="{{ old('telephone') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_ordre">@lang('trans.numero_ordre') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="numero_ordre" name="numero_ordre" value="{{ old('numero_ordre') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_naissance">@lang('trans.birth_date') <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="{{ old('date_naissance') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nationalite">@lang('trans.nationality') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nationalite" name="nationalite" value="{{ old('nationalite') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="statut">@lang('trans.status')</label>
                                <select class="form-control" id="statut" name="statut">
                                    <option value="actif">@lang('trans.active')</option>
                                    <option value="inactif" {{ old('statut') === 'inactif' ? 'selected' : '' }}>@lang('trans.inactive')</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="adresse">@lang('trans.address') <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="adresse" name="adresse" rows="2" required>{{ old('adresse') }}</textarea>
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
    // Afficher le nom du fichier sélectionné
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
});
</script>
@endpush
