{{-- resources/views/centre_medical/examens/index.blade.php --}}
@extends('centre.layouts.app')

@section('title')
    @lang('trans.rapports_medicaux')
@endsection

@section('contentheader')
    @lang('trans.rapports_medicaux')
@endsection

@section('contentheaderactive')
    @lang('trans.rapports_medicaux')
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-file-medical mr-2"></i> @lang('trans.rapports_medicaux')</h3>
            <div class="card-tools">
                <a href="{{ route('centre_medical.examens.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> @lang('trans.nouveau_rapport_medical')
                </a>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>@lang('trans.applicant')</th>
                        <th>@lang('trans.medical_examiner')</th>
                        <th>@lang('trans.exam_date')</th>
                        <th>@lang('trans.medical_fitness')</th>
                        <th>@lang('trans.statut_circuit')</th>
                        <th>@lang('trans.actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($examens as $examen)
                        <tr>
                            <td>{{ $examen->demandeur->np ?? '-' }}</td>
                            <td>{{ optional($examen->examinateur)->nom_complet ?? '-' }}</td>
                            <td>{{ $examen->date_examen }}</td>
                            <td>{{ $examen->aptitude }}</td>
                            <td>
                                @if ($examen->valider_sma)
                                    <span class="badge badge-success">@lang('trans.valide_sma')</span>
                                @elseif ($examen->valider_evaluateur)
                                    <span class="badge badge-info">@lang('trans.chez_sma')</span>
                                @elseif ($examen->estTransmis())
                                    <span class="badge badge-primary">@lang('trans.chez_evaluateur')</span>
                                @else
                                    <span class="badge badge-secondary">@lang('trans.brouillon')</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('centre_medical.examens.show', $examen) }}" class="btn btn-info btn-sm" title="@lang('trans.view')">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('update', $examen)
                                    <a href="{{ route('centre_medical.examens.edit', $examen) }}" class="btn btn-primary btn-sm" title="@lang('trans.edit')">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('centre_medical.examens.transmettre', $examen) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm(@json(__('trans.confirmer_transmission_rapport')))">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning btn-sm" title="@lang('trans.transmettre_anac')">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('centre_medical.examens.destroy', $examen) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm(@json(__('trans.confirm_delete')))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="@lang('trans.destroy')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted p-3">@lang('trans.aucun_rapport_medical')</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($examens->hasPages())
            <div class="card-footer">{{ $examens->links() }}</div>
        @endif
    </div>
</div>
@endsection
