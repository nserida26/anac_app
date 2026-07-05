@extends('compagnie.layouts.app')
@section('title')
    @lang('trans.dashboard_compagny')
@endsection
@section('contentheader')
    @lang('trans.dashboard_compagny') - {{ $compagnie->nom_entreprise }}
@endsection
@section('contentheaderlink')
    <a href="">
        @lang('trans.dashboard_compagny') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_compagny')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .modal-header {
            padding: 0.75rem 1.5rem;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        @if ($demandeurs->isNotEmpty())
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">@lang('trans.applicants')
                            <div class="card-tools">
                                <!-- Buttons, labels, and many other things can be placed here! -->
                                <!-- Here is a label for example -->
                                <span class="badge badge-primary"> @lang('trans.situation')
                                    : {{ $compagnie->panier }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>@lang('trans.id')</th>

                                            <th>@lang('trans.photo')</th>
                                            <th>@lang('trans.fl_name')</th>
                                            <th>@lang('trans.dob') </th>
                                            <th>@lang('trans.address')</th>
                                            <th>@lang('trans.actions')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $controller = app(\App\Http\Controllers\CompagnieController::class);
                                        @endphp
                                        @foreach ($demandeurs as $demandeur)
                                            <tr>
                                                <td>{{ $demandeur->id }}</td>
                                                <td><img src="{{ asset('/uploads/' . $demandeur->photo) }}" width="64"
                                                        height="64" class="card-img-top img-cover" alt=""></td>
                                                <td>{{ $demandeur->np }}</td>

                                                <td>{{ $demandeur->date_naissance }}</td>
                                                <td>{{ $demandeur->adresse }}</td>
                                                <td>
                                                    @if ($demandeur->userAccount)
                                                        <form action="{{ route('compagnie.request.login', $demandeur->userAccount) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-primary btn-sm mb-2"
                                                                    onclick="return confirm('Send login request to {{ $demandeur->np }}?')">
                                                                <i class="fas fa-sign-in-alt"></i> Request Login
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if ($demandeur->valider_compagnie)
                                                        <button class="btn btn-info btn-sm toggle-btn"
                                                            data-target="demandeur-{{ $demandeur->id }}">

                                                            @lang('trans.view_applicants')
                                                        </button>
                                                    @endif
                                                    @if (!$demandeur->valider_compagnie)
                                                        <form action="{{ route('compagnie.valider', $demandeur) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-success btn-sm"
                                                                onclick="return confirm('Confirmer la validation ?')">
                                                                @lang('trans.validate')
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('compagnie.rejeter', $demandeur) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Confirmer le rejet ?')">
                                                                @lang('trans.reject')
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                                <!-- Lignes cachées des demandes du demandeur -->
                                            <tr id="demandeur-{{ $demandeur->id }}" class="toggle-row"
                                                style="display: none;">
                                                <td colspan="6">
                                                    <table class="table table-sm">
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
                                                            @foreach ($demandeur->demandes as $demande)
                                                                <tr>
                                                                    <td>{{ $demande->code }}</td>
                                                                    <td>{{ $demande->demandeur->np }}</td>
                                                                    <td>{{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($demande->typeDemande)->nom_fr : optional($demande->typeDemande)->nom_en }}
                                                                    </td>
                                                                    <td>{{ $demande->typeLicence->nom }}</td>
                                                                    <td>

                                                                        @if (!empty($demande->licence))
                                                                            @if (is_array($demande->licence->expiry_status))
                                                                                <span
                                                                                    class="badge badge-success">@lang('trans.license_status.' . $demande->licence->expiry_status['key'], $demande->licence->expiry_status)</span>
                                                                            @else
                                                                                <span
                                                                                    class="badge badge-danger">@lang('trans.license_status.' . $demande->licence->expiry_status)</span>
                                                                            @endif
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if (!empty($demande->paiement))
                                                                            <button
                                                                                class="btn btn-warning btn-sm toggle-btn"
                                                                                data-target="paiements-{{ $demande->id }}">
                                                                                @lang('trans.view_payements')
                                                                            </button>
                                                                        @endif



                                                                    </td>
                                                                </tr>
                                                                <!-- Lignes cachées des paiements liés à la demande -->
                                                                <tr id="paiements-{{ $demande->id }}" class="toggle-row"
                                                                    style="display: none;">
                                                                    <td colspan="5">
                                                                        <table class="table table-sm">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>@lang('trans.ref')</th>
                                                                                    <th>@lang('trans.amount')</th>

                                                                                    <th>@lang('trans.status')</th>
                                                                                    <th>@lang('trans.payement_date')</th>
                                                                                    <th>@lang('trans.actions')</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>

                                                                                <tr>
                                                                                    <td>{{ optional($demande->paiement)->reference }}
                                                                                    </td>
                                                                                    <td>{{ number_format(optional($demande->paiement)->montant, 2) }}
                                                                                    </td>
                                                                                    <td>{{ optional($demande->paiement)->statut }}
                                                                                    </td>

                                                                                    <td>{{ optional($demande->paiement)->date_paiement ? date('d-m-Y', strtotime(optional($demande->paiement)->date_paiement)) : '-' }}
                                                                                    </td>
                                                                                    <td>
                                                                                        @if ($demande->paiement && $demande->paiement->statut === 'En attente')
                                                                                            <a href="{{ route('compagnie.pay', $demande->paiement) }}"
                                                                                                class="btn btn-warning btn-sm">@lang('trans.pay')</a>
                                                                                            <button
                                                                                                class="btn btn-warning btn-sm"
                                                                                                onclick="openPdfModal('{{ asset('/uploads/' . $demande->facture->facture) }}')">
                                                                                                Facture</i></button>
                                                                                        @endif
                                                                                    </td>
                                                                                </tr>

                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
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

{{-- 
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">@lang('trans.applications')
                        <div class="card-tools">

                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#applicationModal">
                                @lang('trans.add_application')
                            </button>
                        </div>
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



                                                    @if (!optional($demande->etatDemande)->compagnie_cree_demande)
                                                        <a href="{{ url('/compagnie/edit/' . $demande->id) }}"
                                                            class="btn btn-warning btn-sm">@lang('trans.edit')</a>
                                                        <form
                                                            action="{{ route('update-state-approbation', $demande->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="action"
                                                                value="compagnie_cree_demande">
                                                            <input type="hidden" name="is_approved" value="1">
                                                            <button type="submit" class="btn btn-success btn-sm"
                                                                onclick="return confirm('Confirmer la soumettre ?')">
                                                                @lang('trans.send')

                                                            </button>
                                                        </form>
                                                        <form action="{{ route('compagnie.destroy', $demande) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette demande d\'approbation ?')">
                                                                @lang('trans.destroy')
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if ($demande->etatDemande->dg_valider || $demande->etatDemande->dta_dg_valider)
                                                        <a target="_blank"
                                                            href="{{ route('compagnie.print', $demande->approbation) }}"
                                                            class="btn btn-primary btn-sm">@lang('trans.print')</a>
                                                        <a href="{{ url('/compagnie/edit/' . $demande->id) }}"
                                                            class="btn btn-warning btn-sm">@lang('trans.edit')</a>
                                                        <form
                                                            action="{{ route('update-state-approbation', $demande->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="action"
                                                                value="compagnie_cree_demande">
                                                            <input type="hidden" name="is_approved" value="1">
                                                            <button type="submit" class="btn btn-success btn-sm"
                                                                onclick="return confirm('Confirmer la soumettre ?')">
                                                                @lang('trans.send')

                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if ($demande->has_issues)
                                                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                                            data-target="#issuesModal-{{ $demande->id }}"
                                                            title="@lang('View Issues')">
                                                            <i class="fas fa-info-circle"></i>
                                                        </button>

                                                        <!-- Issues Modal -->
                                                        <div class="modal fade" id="issuesModal-{{ $demande->id }}"
                                                            tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-dark text-white">
                                                                        <h5 class="modal-title">
                                                                            @lang('Issues for')
                                                                            {{ $demande->reference }}
                                                                        </h5>
                                                                        <button type="button" class="close text-white"
                                                                            data-dismiss="modal">
                                                                            <span>&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        @if (count($demande->invalid_reasons) > 0)
                                                                            <div class="alert alert-warning">
                                                                                <h6><i
                                                                                        class="fas fa-exclamation-triangle"></i>
                                                                                    @lang('Invalid Components')</h6>
                                                                                <ul>
                                                                                    @foreach ($demande->invalid_reasons as $component => $reason)
                                                                                        <li>
                                                                                            @if (is_array($reason) && !empty($reason['motif']))
                                                                                                {{ $reason['motif'] }}
                                                                                            @endif
                                                                                        </li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            </div>
                                                                        @endif
                                                                        @if (count($demande->rejection_reasons) > 0)
                                                                            <div class="alert alert-danger">
                                                                                <h6><i class="fas fa-ban"></i>
                                                                                    @lang('Rejection Reasons')</h6>
                                                                                <ul>
                                                                                    @foreach ($demande->rejection_reasons as $dept => $reason)
                                                                                        @foreach ($reason as $item)
                                                                                            @if (!empty($item))
                                                                                                <li>
                                                                                                    {{ $item }}
                                                                                                </li>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    @endforeach
                                                                                </ul>
                                                                        @endif
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">
                                                                            @lang('Close')
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
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

--}}

    </div>
    <!-- Modal -->
    <div class="modal fade" id="applicationModal" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="applicationModalLabel">@lang('trans.add_application')</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="applicationForm" action="{{ route('compagnie.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="type_demande_autorisation_id">@lang('trans.select_season')</label>
                                    <select class="form-control select2" id="saison" name="saison" required>
                                        <option value="ETE">@lang('trans.select_summer')</option>
                                        <option value="HIVER">@lang('trans.select_automne')</option>


                                    </select>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> @lang('trans.send')
                        </button>
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
    <!-- Select2 -->
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@endpush
@push('custom')
    <!-- Initialize Select2 for better select dropdown -->
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "@lang('trans.select_flight')",
                allowClear: true,
                width: '100%'
            });

            // Handle form submission
            $('#applicationForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#applicationModal').modal('hide');
                        //toastr.success(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {

                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                //toastr.error(value[0]);
                            });
                        } else {
                            //toastr.error("@lang('trans.error_occurred')");
                        }
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.toggle-btn').forEach(button => {
                button.addEventListener('click', function() {
                    let targetId = this.getAttribute('data-target');
                    let row = document.getElementById(targetId);
                    if (row.style.display === "none") {
                        row.style.display = "table-row";
                    } else {
                        row.style.display = "none";
                    }
                });
            });
        });
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
                ],
            });

        });
    </script>
@endpush
