@extends('sec.layouts.app')
@section('title')
    @lang('trans.dashboard_sec')
@endsection
@section('contentheader')
    @lang('trans.dashboard_sec')
@endsection
@section('contentheaderlink')
    <a href="">
        @lang('trans.dashboard_sec') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_sec')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">@lang('trans.applicants')</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="demandes">
                                <thead>
                                    <tr>
                                        <th>@lang('trans.id')</th>
                                        <th>@lang('trans.applicant')</th>
                                        <th>@lang('trans.type_application')</th>
                                        <th>@lang('trans.type_license')</th>
                                        <th>@lang('trans.status')</th>
                                        @if (auth()->user()->hasRole('sla'))
                                            <th>@lang('trans.training')</th>
                                        @endif
                                        @if (auth()->user()->hasRole('sma'))
                                            <th>@lang('trans.exams')</th>
                                        @endif
                                        <th>@lang('trans.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($demandes as $demande)
                                        <tr>
                                            <td>{{ $demande->code }}</td>
                                            <td>{{ $demande->demandeur->np }}</td>
                                            <td>{{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($demande->typeDemande)->nom_fr : optional($demande->typeDemande)->nom_en }}
                                            </td>
                                            <td>{{ $demande->typeLicence->nom }}</td>
                                            <td>{{ $demande->status }}</td>
                                            @if (auth()->user()->hasRole('sla'))
                                                <td>
                                                    @if ($demande->demandeur->formations->isNotEmpty())
                                                        <span class="badge badge-primary">
                                                            @lang('trans.yes')
                                                        </span>
                                                    @else
                                                        <span class="badge badge-danger">
                                                            @lang('trans.no')
                                                        </span>
                                                    @endif
                                                </td>
                                            @endif
                                            @if (auth()->user()->hasRole('sma'))
                                                <td>
                                                    @if ($demande->demandeur->examens->isNotEmpty())
                                                        <span class="badge badge-primary">
                                                            @lang('trans.yes')
                                                        </span>
                                                    @else
                                                        <span class="badge badge-danger">
                                                            @lang('trans.no')
                                                        </span>
                                                    @endif
                                                </td>
                                            @endif
                                            <td>
                                                @php
                                                    $isSMA = in_array($demande->typeDemande->id, [1, 3, 10 , 5, 7, 9]);
                                                    $isSLA = !in_array($demande->typeDemande->id, [5]);
                                                @endphp
                                                @if (auth()->user()->hasRole('sla'))
                                                    @if (optional($demande->etatDemande)->demandeur_cree_demande)
                                                        <a href="{{ route('sla.show', $demande->id) }}"
                                                            class="btn btn-info btn-sm">@lang('trans.view')</a>
                                                    @endif
                                                    @if ($isSLA && optional($demande->etatDemande)->pel_annoter && !optional($demande->etatDemande)->sl_valider)
                                                        {{-- Service Licences Aéronautiques Valider --}}
                                                        <form action="{{ route('update-state-licence', $demande->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="action" value="sl_valider">
                                                            <input type="hidden" name="is_approved" value="1">
                                                            <button type="submit" class="btn btn-success btn-sm mb-1"
                                                                onclick="return confirm('Confirmer la validation par le service des licences aéronautiques ?')">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.validate_sla')
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                                @if (auth()->user()->hasRole('sma'))
                                                    @if (optional($demande->etatDemande)->demandeur_cree_demande)
                                                        <a href="{{ route('sma.show', $demande->id) }}"
                                                            class="btn btn-info btn-sm">@lang('trans.view')</a>
                                                    @endif
                                                    @if ($isSMA && optional($demande->etatDemande)->pel_annoter && !optional($demande->etatDemande)->sm_valider)
                                                        <form action="{{ route('update-state-licence', $demande->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="action" value="sm_valider">
                                                            <input type="hidden" name="is_approved" value="1">
                                                            <button type="submit" class="btn btn-success btn-sm mb-1"
                                                                onclick="return confirm('Confirmer la validation par la section de médecine aéronautique ?')">
                                                                <i class="fas fa-check-circle"></i> @lang('trans.validate_sma')
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if (
                                                        $isSMA &&
                                                            optional($demande->etatDemande)->pel_annoter &&
                                                            !optional($demande->etatDemande)->sm_valider &&
                                                            !optional($demande->etatDemande)->evaluateur_annoter &&
                                                            $demande->demandeur->examens->isEmpty())
                                                        <button type="button" class="btn btn-warning btn-sm"
                                                            onclick="openModal('{{ $demande->id }}')">
                                                            @lang('trans.annotate')
                                                        </button>
                                                    @endif
                                                @endif
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
        @if ($examens->isNotEmpty() && auth()->user()->hasRole('sma'))
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">@lang('trans.exams')</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="exams">
                                    <thead>
                                        <tr>

                                            <th>@lang('trans.applicant')</th>
                                            <th>@lang('trans.examiner')</th>
                                            <th>@lang('trans.evaluator')</th>
                                            <th>@lang('trans.exam_date')</th>
                                            <th>@lang('trans.validity')</th>
                                            <th>@lang('trans.validity_evaluator')</th>
                                            <th>@lang('trans.medical_fitness')</th>


                                            <th>@lang('trans.restriction')</th>
                                            <th>@lang('trans.validated')</th>
                                            <th>@lang('trans.period_for_execution')</th>
                                            <th>@lang('trans.actions')</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        @inject('carbon', 'Carbon\Carbon')

                                        @foreach ($examens as $examen)
                                            @php
                                                $today = $carbon::today();
                                                $dateExamen = $carbon::parse($examen->date_examen);
                                                $expiryDate = $dateExamen->copy()->addDays(15);
                                                $daysRemaining = $today->diffInDays($expiryDate, false);
                                            @endphp
                                            <tr>

                                                <td>{{ $examen->demandeur->np }}</td>
                                                <td>{{ $examen->examinateur->np }}</td>
                                                <td>{{ $examen->evaluateur->np }}</td>
                                                <td>{{ $examen->date_examen }}</td>
                                                <td>{{ $examen->validite }}</td>
                                                <td>{{ $examen->validite_evaluateur }}</td>
                                                <td>{{ $examen->aptitude }}</td>
                                                <td>
                                                    @if ($examen->validite_evaluateur === $examen->validite)
                                                        <span class="badge badge-danger">
                                                            @lang('trans.no')
                                                        </span>
                                                    @else
                                                        <span class="badge badge-primary">
                                                            @lang('trans.yes')
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($examen->valider_examinateur && $examen->valider_evaluateur)
                                                        <span class="badge bg-success">@lang('trans.yes')</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($examen->valider_examinateur && !$examen->valider_evaluateur)
                                                        @if ($daysRemaining > 0)
                                                            <span class="badge bg-info">
                                                                @lang('trans.expires_in') {{ $daysRemaining }} @lang('trans.days')
                                                                ({{ $expiryDate->format('d-M-Y') }})
                                                            </span>
                                                        @elseif ($daysRemaining == 0)
                                                            <span class="badge bg-warning">@lang('trans.expires_today')</span>
                                                        @else
                                                            <span class="badge bg-danger">
                                                                @lang('trans.expired') {{ abs($daysRemaining) }}
                                                                @lang('trans.days_ago')
                                                                ({{ $expiryDate->format('d-M-Y') }})
                                                            </span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($examen->valider_examinateur && !$examen->valider_evaluateur)
                                                        <form action="{{ route('sma.relaunch', $examen) }}" method="POST"
                                                            class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-success btn-sm"
                                                                onclick="return confirm('@lang('trans.confirm_relaunch')')">
                                                                @lang('trans.relaunch')
                                                            </button>
                                                        </form>
                                                    @endif
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
        @endif
    </div>
    <div class="modal fade" id="annotationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('sma.annoter') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="modal-header">
                        <h5 class="modal-title">@lang('trans.annotate_to_evaluator')</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="demande_id" id="demande_id">
                        <div class="form-group mb-3">
                            <label for="evaluateur_id" class="form-label">@lang('trans.select_evaluator')</label>
                            <select class="form-control" id="evaluateur_id" name="evaluateur_id" required>
                                <option value="">@lang('trans.choose')</option>
                                @foreach ($evaluateurs as $evaluateur)
                                    <option value="{{ $evaluateur->user_id }}">{{ $evaluateur->np }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('trans.cancel')</button>
                        <button type="submit" class="btn btn-primary">@lang('trans.annotate')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
@endpush
@push('custom')
    <script>
        function openModal(demande_id) {
            document.getElementById('demande_id').value = demande_id;
            new bootstrap.Modal(document.getElementById('annotationModal')).show();
        }
        $(function() {

            $('#exams').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "columnDefs": [{
                        "targets": 5,
                        "orderable": false
                    },
                    {
                        "targets": 3,
                        "searchable": true
                    }
                ]
            });
            $('#demandes').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "columnDefs": [{
                        "targets": 5,
                        "orderable": false
                    },
                    {
                        "targets": 3,
                        "searchable": true
                    }
                ]
            });
        });
    </script>
@endpush
