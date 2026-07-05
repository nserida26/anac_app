@extends('user.layouts.app')
@section('title', trans('trans.instructor_dashboard'))
@section('contentheader', trans('trans.dashboard'))
@section('contentheaderlink')
    <a href="{{ route('demandeur.dashboard') }}">@lang('trans.dashboard')</a>
@endsection
@section('contentheaderactive', trans('trans.instructor_dashboard'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @if($demandeur->is_instructeur)
                <div class="alert alert-info">
                    <i class="fas fa-chalkboard-teacher"></i>
                    @lang('trans.you_are_instructor')
                </div>
            @endif
            @if($demandeur->is_examinateur)
                <div class="alert alert-success">
                    <i class="fas fa-stethoscope"></i>
                    @lang('trans.you_are_examiner')
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_formations'] }}</h3>
                    <p>@lang('trans.total_trainings')</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <a href="{{ route('demandeur.formations.list') }}" class="small-box-footer">
                    @lang('trans.more_info') <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['formations_a_venir'] }}</h3>
                    <p>@lang('trans.upcoming_trainings')</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <a href="{{ route('demandeur.formations.list', ['status' => 'planifiee']) }}" class="small-box-footer">
                    @lang('trans.more_info') <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['formations_passees'] }}</h3>
                    <p>@lang('trans.completed_trainings')</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('demandeur.formations.list', ['status' => 'terminee']) }}" class="small-box-footer">
                    @lang('trans.more_info') <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['total_stagiaires'] }}</h3>
                    <p>@lang('trans.total_trainees')</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('demandeur.formations.list') }}" class="small-box-footer">
                    @lang('trans.more_info') <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clock"></i> @lang('trans.recent_trainings')
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentFormations->isEmpty())
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i>
                            @lang('trans.no_trainings_found')
                            <br>
                            <a href="{{ route('demandeur.create.formation') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus"></i> @lang('trans.assign_training')
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>@lang('trans.id')</th>
                                        <th>@lang('trans.trainee')</th>
                                        <th>@lang('trans.training_type')</th>
                                        <th>@lang('trans.training_date')</th>
                                        
                                        <th>@lang('trans.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentFormations as $formation)
                                        <tr>
                                            <td>{{ $formation->id }}</td>
                                            <td>
                                                @if($formation->demandeur)
                                                    {{ $formation->demandeur->np }}
                                                    <br>
                                                    <small class="text-muted">
                                                        @if($formation->demandeur->licence)
                                                            {{ $formation->demandeur->licence->numero_licence }}
                                                        @endif
                                                    </small>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $formation->typeFormation->nom ?? 'N/A' }}</td>
                                            <td>{{ $formation->date_formation->format('d/m/Y') }}</td>
                                           
                                            <td>
                                                <a href="{{ route('demandeur.show.formation', $formation->id) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                @if(!$recentFormations->isEmpty())
                    <div class="card-footer text-right">
                        <a href="{{ route('demandeur.create.formation') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> @lang('trans.assign_new_training')
                        </a>
                        <a href="{{ route('demandeur.formations.list') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> @lang('trans.view_all_trainings')
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection