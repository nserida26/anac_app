@extends('layouts.admin')
@section('title')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheaderlink')
    <a href="{{ route('demandeApprobations') }}">
        @lang('trans.dashboard_admin') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_admin')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush
@section('content')
    <div class="container-fluid">
        @if ($demandeApprobations->isNotEmpty())
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">@lang('trans.applications')
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="applications">
                                    <thead>
                                        <tr>
                                            <th>@lang('trans.ref')</th>
                                            <th>@lang('trans.season')</th>
                                            <th>@lang('trans.start_date')</th>
                                            <th>@lang('trans.end_date')</th>
                                            <th>@lang('trans.actions')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($demandeApprobations->isNotEmpty())
                                            @foreach ($demandeApprobations as $demande)
                                                <tr>
                                                    <td>{{ $demande->reference }}</td>
                                                    <td>{{ $demande->saison }}</td>
                                                    <td>{{ date('d/m/Y', strtotime($demande->date_debut)) }}</td>
                                                    <td>{{ date('d/m/Y', strtotime($demande->date_fin)) }}</td>
                                                    <td>


                                                        @if (optional($demande->etatDemande)->compagnie_cree_demande)
                                                            <a href="{{ route('demandeApprobations.show', $demande->id) }}"
                                                                class="btn btn-info btn-sm">@lang('trans.view')</a>
                                                        @endif
                                                        @if (optional($demande->etatDemande)->compagnie_cree_demande &&
                                                                (optional($demande->etatDemande)->dg_annoter || optional($demande->etatDemande)->dta_dg_annoter) &&
                                                                optional($demande->etatDemande)->dta_annoter &&
                                                                !optional($demande->etatDemande)->service_annoter &&
                                                                !optional($demande->etatDemande)->service_tout_valider)
                                                            <form
                                                                action="{{ route('update-state-approbation', $demande->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="hidden" name="action"
                                                                    value="service_annoter">
                                                                <input type="hidden" name="is_approved" value="1">
                                                                <button type="submit" class="btn btn-success"
                                                                    onclick="return confirm('Confirmer l\'annotation ?')">
                                                                    @lang('trans.forward')

                                                                </button>
                                                            </form>
                                                            @if ($demande?->isFullyValidated() ?? false)
                                                                <form
                                                                    action="{{ route('update-state-approbation', $demande->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="action"
                                                                        value="service_tout_valider">
                                                                    <input type="hidden" name="is_approved" value="1">
                                                                    <button type="submit" class="btn btn-success"
                                                                        onclick="return confirm('Confirmer la validation a la place de directions ?')">
                                                                        @lang('trans.validate')
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endif
                                                        @if (optional($demande->etatDemande)->compagnie_cree_demande &&
                                                                (optional($demande->etatDemande)->dg_annoter || optional($demande->etatDemande)->dta_dg_annoter) &&
                                                                optional($demande->etatDemande)->dta_annoter &&
                                                                optional($demande->etatDemande)->service_annoter &&
                                                                optional($demande->etatDemande)->dsad_valider &&
                                                                optional($demande->etatDemande)->dsna_valider &&
                                                                optional($demande->etatDemande)->dsv_valider &&
                                                                !optional($demande->etatDemande)->service_valider &&
                                                                $demande?->isFullyValidated() ?? false)
                                                            <form
                                                                action="{{ route('update-state-approbation', $demande->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="hidden" name="action"
                                                                    value="service_valider">
                                                                <input type="hidden" name="is_approved" value="1">
                                                                <button type="submit" class="btn btn-success"
                                                                    onclick="return confirm('Confirmer la validation ?')">
                                                                    @lang('trans.validate')
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>

                                </table>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @endif
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
            $('#applications').DataTable({
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
