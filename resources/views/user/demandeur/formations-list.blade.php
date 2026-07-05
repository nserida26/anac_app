@extends('user.layouts.app')
@section('title', trans('trans.my_trainings'))
@section('contentheader', trans('trans.trainings_list'))
@section('contentheaderlink')
    <a href="{{ route('demandeur.dashboard') }}">@lang('trans.dashboard')</a>
@endsection
@section('contentheaderactive', trans('trans.trainings_list'))
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

@endpush
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total'] }}</h3>
                    <p>@lang('trans.total_trainings')</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['planifiees'] }}</h3>
                    <p>@lang('trans.planned')</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['terminees'] }}</h3>
                    <p>@lang('trans.completed')</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>
                        <a href="{{ route('demandeur.create.formation') }}" class="text-white">
                            <i class="fas fa-plus"></i> @lang('trans.new')
                        </a>
                    </h3>
                    <p>@lang('trans.assign_training')</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">@lang('trans.trainings_list')</h3>
            <div class="card-tools">
                <form method="GET" class="form-inline">
                    <select name="status" class="form-control mr-2">
                        <option value="">@lang('trans.all_status')</option>
                        <option value="planifiee" {{ request('status') == 'planifiee' ? 'selected' : '' }}>@lang('trans.planifiee')</option>
                        <option value="en_cours" {{ request('status') == 'en_cours' ? 'selected' : '' }}>@lang('trans.en_cours')</option>
                        <option value="terminee" {{ request('status') == 'terminee' ? 'selected' : '' }}>@lang('trans.terminee')</option>
                        <option value="annulee" {{ request('status') == 'annulee' ? 'selected' : '' }}>@lang('trans.annulee')</option>
                    </select>
                    <input type="date" name="date_from" class="form-control mr-2" value="{{ request('date_from') }}" placeholder="@lang('trans.from_date')">
                    <input type="date" name="date_to" class="form-control mr-2" value="{{ request('date_to') }}" placeholder="@lang('trans.to_date')">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> @lang('trans.filter')
                    </button>
                    <a href="{{ route('demandeur.formations.list') }}" class="btn btn-default ml-2">
                        <i class="fas fa-sync-alt"></i> @lang('trans.reset')
                    </a>
                </form>
            </div>
        </div>
        <div class="card-body">
            @if($formations->isEmpty())
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
                                <th>@lang('trans.location')</th>
                                <th>@lang('trans.status')</th>
                                <th>@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formations as $formation)
                                <tr>
                                    <td>{{ $formation->id }}</td>
                                    <td>
                                        <strong>{{ $formation->demandeur->np ?? 'N/A' }}</strong>
                                        @if($formation->demandeur && $formation->demandeur->licence)
                                            <br><small class="text-muted">{{ $formation->demandeur->licence->numero_licence }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $formation->typeFormation->nom ?? 'N/A' }}</td>
                                    <td>{{ $formation->date_formation->format('d/m/Y') }}</td>
                                    <td>{{ $formation->lieu ?? '-' }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'planifiee' => 'warning',
                                                'en_cours' => 'info',
                                                'terminee' => 'success',
                                                'annulee' => 'danger'
                                            ];
                                            $statusColor = $statusColors[$formation->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $statusColor }}">
                                            @lang('trans.' . $formation->status)
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('demandeur.show.formation', $formation->id) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($formation->status != 'terminee' && $formation->status != 'annulee')
                                            <button class="btn btn-sm btn-success update-status" 
                                                    data-id="{{ $formation->id }}"
                                                    data-status="terminee">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $formations->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function() {
    $('.update-status').on('click', function() {
        var formationId = $(this).data('id');
        var newStatus = $(this).data('status');
        
        if (confirm('@lang("trans.confirm_status_change")')) {
            $.ajax({
                url: '/user/demandeur/formation/' + formationId + '/status',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        location.reload();
                    }
                },
                error: function() {
                    toastr.error('@lang("trans.error_updating_status")');
                }
            });
        }
    });
});
</script>
@endpush
@endsection