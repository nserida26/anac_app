{{-- Désignations d'examinateur par l'ANAC (types de licence + période). Remplace l'ancienne case « Examinateur ». --}}
<label class="form-label">@lang('trans.designations_examinateur')</label>

@forelse ($demandeur->designationsExaminateur->sortByDesc('date_debut') as $designation)
    <div class="border rounded p-2 mb-2 d-flex justify-content-between align-items-center">
        <div>
            <strong>{{ $designation->typesLicence->pluck('nom')->implode(', ') }}</strong><br>
            <small>{{ $designation->date_debut->format('d/m/Y') }} – {{ $designation->date_fin->format('d/m/Y') }}</small>
            @if ($designation->retiree_le)
                <span class="badge bg-secondary">@lang('trans.designation_retiree') {{ $designation->retiree_le->format('d/m/Y') }}</span>
            @elseif ($designation->estEnVigueur())
                <span class="badge bg-success">@lang('trans.designation_en_vigueur')</span>
            @else
                <span class="badge bg-warning">@lang('trans.designation_hors_periode')</span>
            @endif
        </div>
        @unless ($designation->retiree_le)
            <form action="{{ route('demandeurs.designations.retirer', $designation) }}" method="POST"
                  onsubmit="return confirm(@json(__('trans.confirmer_retrait_designation')))">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">@lang('trans.retirer')</button>
            </form>
        @endunless
    </div>
@empty
    <p class="text-muted mb-2">@lang('trans.aucune_designation')</p>
@endforelse

@if ($demandeur->is_instructeur && $demandeur->detientLicenceValide())
    <form action="{{ route('demandeurs.designations.store', $demandeur) }}" method="POST" class="border rounded p-2">
        @csrf
        <div class="form-group mb-2">
            <label for="types_licence">@lang('trans.types_licence_autorises') <span class="text-danger">*</span></label>
            <select name="types_licence[]" id="types_licence" class="form-control select2" multiple required style="width: 100%">
                @foreach ($typesLicence as $typeLicence)
                    <option value="{{ $typeLicence->id }}">{{ $typeLicence->nom }} – {{ $typeLicence->fr }}</option>
                @endforeach
            </select>
        </div>
        <div class="row">
            <div class="col-6">
                <label for="date_debut">@lang('trans.date_debut') <span class="text-danger">*</span></label>
                <input type="date" name="date_debut" id="date_debut" class="form-control" required value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-6">
                <label for="date_fin">@lang('trans.date_fin') <span class="text-danger">*</span></label>
                <input type="date" name="date_fin" id="date_fin" class="form-control" required>
            </div>
        </div>
        <button type="submit" class="btn btn-sm btn-success mt-2">
            <i class="fas fa-user-check"></i> @lang('trans.designer_examinateur')
        </button>
    </form>
@else
    <div class="alert alert-info py-2 mb-0">@lang('trans.designation_conditions')</div>
@endif
