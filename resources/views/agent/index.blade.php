@extends('agent.layouts.app')
@section('title')
    @lang('trans.dashboard_agent')
@endsection
@section('contentheader')
    @lang('trans.dashboard_agent')
@endsection
@section('contentheaderlink')
    <a href="{{ route('agent') }}">
        @lang('trans.dashboard_agent') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_agent')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <style>
            .badge-submitted { background-color: #17a2b8; color: white; }
        .badge-under_review { background-color: #ffc107; color: black; }
        .badge-service_approved { background-color: #28a745; color: white; }
        .badge-paid { background-color: #007bff; color: white; }
        .badge-payment_confirmed { background-color: #20c997; color: white; }
        .badge-rejected { background-color: #dc3545; color: white; }
        .badge-printed{background-color: navy; color: white;}
        </style>
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
                                        <th>@lang('trans.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($demandes as $demande)
                                    @php
                                        $etatDemande = $demande->etat_workflow;
                                    @endphp
                                        <tr>
                                            <td>{{ $demande->code }}</td>
                                            <td>{{ $demande->demandeur->np }}</td>
                                            <td>{{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($demande->typeDemande)->nom_fr : optional($demande->typeDemande)->nom_en }}
                                            </td>
                                            <td>{{ $demande->typeLicence->nom }}</td>
                                            <td>
                                            @php
                                                $badgeClass = match($etatDemande) {
                                                    'submitted' => 'badge-submitted',
                                                    'under_review' => 'badge-under_review',
                                                    'service_approved' => 'badge-service_approved',
                                                    'paid' => 'badge-paid',
                                                    'payment_confirmed' => 'badge-payment_confirmed',
                                                    'rejected' => 'badge-rejected',
                                                    'rejected' => 'badge-rejected',
                                                    'printed' => 'badge-printed',
                                                    default => 'badge-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ $etatDemande }}
                                            </span>
                                         </td>
                                            <td>

                                                @if (optional($demande->etatDemande)->pel_valider && !in_array($demande->typeDemande->id,[7]))
                                                <a href="{{ route('agent.sign', $demande->demandeur->id) }}"
                                                    class="btn btn-warning btn-sm">@lang('trans.sign')</a>
                                               @endif
                                                @if (empty($demande->demandeur->dossier))
                                                    <a href="{{ route('agent.upload', $demande->demandeur->id) }}"
                                                        class="btn btn-success btn-sm">@lang('trans.upload')</a>
                                                @endif

                                                @if (!empty($demande->licence) && optional($demande->licence)->licence_valide)
                                                    <a href="{{ route('agent.imprimer', $demande->id) }}"
                                                        class="btn btn-primary btn-sm"
                                                        target="_blank">@lang('trans.print')</a>
                                                    <form action="{{ route('agent.valider', $demande->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success btn-sm"
                                                            onclick="return confirm('Confirmer la validation ?')">
                                                            @lang('trans.validate')
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
        $(function() {
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
