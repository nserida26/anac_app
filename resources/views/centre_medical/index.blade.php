{{-- resources/views/centre_medical/index.blade.php --}}
@extends('centre.layouts.app')

@section('title')
    @lang('trans.dashboard')
@endsection

@section('contentheader')
    {{ $centre->libelle }}
@endsection

@section('contentheaderactive')
    @lang('trans.dashboard')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4 col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['medecins'] }}</h3>
                    <p>@lang('trans.medecins_actifs')</p>
                </div>
                <div class="icon"><i class="fas fa-user-md"></i></div>
                <a href="{{ route('centre_medical.medecins') }}" class="small-box-footer">
                    @lang('trans.view') <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['examinateurs_valides'] }}</h3>
                    <p>@lang('trans.examinateurs_medicaux_valides')</p>
                </div>
                <div class="icon"><i class="fas fa-user-check"></i></div>
                <a href="{{ route('centre_medical.examinateurs') }}" class="small-box-footer">
                    @lang('trans.view') <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['examinateurs_en_attente'] }}</h3>
                    <p>@lang('trans.pending_validation')</p>
                </div>
                <div class="icon"><i class="fas fa-clock"></i></div>
                <a href="{{ route('centre_medical.examinateurs') }}" class="small-box-footer">
                    @lang('trans.view') <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-check mr-2"></i>
                        @lang('trans.derniers_examinateurs_declares')
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>@lang('trans.name')</th>
                                <th>@lang('trans.approval_number')</th>
                                <th>@lang('trans.validity_period')</th>
                                <th>@lang('trans.validation_status')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($examinateurs as $examinateur)
                            <tr>
                                <td>{{ $examinateur->nom_complet }}</td>
                                <td>{{ $examinateur->numero_licence_examinateur }}</td>
                                <td>
                                    {{ optional($examinateur->date_debut_validite)->format('d/m/Y') }} -
                                    {{ optional($examinateur->date_fin_validite)->format('d/m/Y') }}
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
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    <div class="alert alert-info m-3">
                                        <i class="fas fa-info-circle"></i> @lang('trans.no_examiners_found')
                                    </div>
                                </td>
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
