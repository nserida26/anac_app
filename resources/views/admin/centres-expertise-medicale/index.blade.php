{{-- resources/views/admin/centres-expertise-medicale/index.blade.php --}}
@extends('layouts.admin')

@section('title')
    @lang('trans.medical_expertise_centres')
@endsection

@section('contentheader')
    @lang('trans.medical_expertise_centres')
@endsection

@section('content')
<div class="container-fluid">
    {{-- Rattacher un compte à un centre --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-link mr-2"></i> @lang('trans.rattacher_compte_centre_medical')</h3>
                </div>
                <form action="{{ route('admin.centres-expertise-medicale.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="centre_medical_id">@lang('trans.medical_expertise_centre') <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="centre_medical_id" name="centre_medical_id" required style="width: 100%">
                                        <option value="">--</option>
                                        @foreach($centresDisponibles as $centreDisponible)
                                            <option value="{{ $centreDisponible->id }}">{{ $centreDisponible->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">@lang('trans.compte_utilisateur') <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="user_id" name="user_id" required style="width: 100%">
                                        <option value="">--</option>
                                        @foreach($comptesDisponibles as $compte)
                                            <option value="{{ $compte->id }}">{{ $compte->email }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">@lang('trans.aide_compte_centre_medical')</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-link"></i> @lang('trans.rattacher')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Centres disposant d'un compte --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-hospital mr-2"></i> @lang('trans.centres_avec_compte')</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>@lang('trans.medical_expertise_centre')</th>
                                <th>@lang('trans.compte_utilisateur')</th>
                                <th>@lang('trans.medecins')</th>
                                <th>@lang('trans.medical_examiners')</th>
                                <th>@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($centres as $centre)
                            <tr>
                                <td>{{ $centre->libelle }}</td>
                                <td>{{ optional($centre->user)->email }}</td>
                                <td>{{ $centre->medecins_count }}</td>
                                <td>
                                    {{ $centre->examinateurs_declares_count }}
                                    <a href="{{ route('admin.examinateurs.index', ['type' => 'medical']) }}" class="ml-1" title="@lang('trans.view')">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </td>
                                <td>
                                    <form action="{{ route('admin.centres-expertise-medicale.destroy', $centre) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm(@json(__('trans.confirmer_detacher_compte')))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="@lang('trans.detacher')">
                                            <i class="fas fa-unlink"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted p-3">@lang('trans.aucun_centre_medical_avec_compte')</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
