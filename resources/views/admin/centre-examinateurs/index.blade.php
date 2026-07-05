{{-- resources/views/admin/examinateurs/index.blade.php --}}
@extends('layouts.admin')

@section('title')
    @lang('trans.all_examiners')
@endsection

@section('contentheader')
    @lang('trans.examiners_management')
@endsection

@section('content')
<div class="container-fluid">
    {{-- Filtres --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="form-inline">
                        <div class="form-group mr-2">
                            <label class="mr-2">@lang('trans.status'):</label>
                            <select class="form-control" name="statut">
                                <option value="">@lang('trans.all')</option>
                                <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>
                                    @lang('trans.pending')
                                </option>
                                <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>
                                    @lang('trans.validated')
                                </option>
                                <option value="refuse" {{ request('statut') == 'refuse' ? 'selected' : '' }}>
                                    @lang('trans.rejected')
                                </option>
                            </select>
                        </div>
                        
                        
                        
                        <div class="form-group mr-2">
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   placeholder="@lang('trans.search')"
                                   value="{{ request('search') }}">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> @lang('trans.filter')
                        </button>
                        <a href="{{ route('admin.examinateurs.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-times"></i> @lang('trans.reset')
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Liste --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('trans.examiners_list')</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('trans.examiner')</th>
                                <th>@lang('trans.training_center')</th>
                                <th>@lang('trans.licence_number')</th>
                                <th>@lang('trans.validity_period')</th>
                                <th>@lang('trans.status')</th>
                                <th>@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($examinateurs as $examinateur)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $examinateur->nom }} {{ $examinateur->prenom }}<br>
                                    <small>{{ $examinateur->email }}</small>
                                </td>
                                <td>{{ $examinateur->centreFormation->libelle ?? 'N/A' }}</td>
                                <td>{{ $examinateur->numero_licence_examinateur }}</td>
                                <td>
                                    {{ $examinateur->date_debut_validite->format('d/m/Y') }} - 
                                    {{ $examinateur->date_fin_validite->format('d/m/Y') }}
                                </td>
                                <td>
                                    @if($examinateur->statut_validation == 'en_attente')
                                        <span class="badge badge-warning">@lang('trans.pending')</span>
                                    @elseif($examinateur->statut_validation == 'valide')
                                        <span class="badge badge-success">@lang('trans.validated')</span>
                                    @else
                                        <span class="badge badge-danger">@lang('trans.rejected')</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.examinateurs.show', $examinateur->id) }}" 
                                       class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection