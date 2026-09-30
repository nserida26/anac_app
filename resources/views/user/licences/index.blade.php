<x-app-user-layout title="@lang('trans.license_applications')">

    @push('css')
        <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}">
        <style>
            .badge-draft { background-color: #6c757d; color: white; }
            .badge-submitted { background-color: #17a2b8; color: white; }
            .badge-under_review { background-color: #ffc107; color: black; }
            .badge-service_approved { background-color: #28a745; color: white; }
            .badge-paid { background-color: #007bff; color: white; }
            .badge-payment_confirmed { background-color: #20c997; color: white; }
            .badge-issued { background-color: #6f42c1; color: white; }
            .badge-printed{background-color: navy; color: white;}
            .badge-rejected { background-color: #dc3545; color: white; }

            .table-danger {
                background-color: rgba(220, 53, 69, 0.1) !important;
            }

            @media (max-width: 768px) {
                td {
                    white-space: normal !important;
                }
            }
        </style>
    @endpush

    @if (Auth::user()->user_type === 'licence')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        @lang('trans.license_applications')
                        <div class="card-tools">
                            @isset(Auth::user()->demandeur)
                                <a href="{{ url('user/create') }}" class="btn btn-success btn-sm">
                                    <i class="fa fa-plus" aria-hidden="true"></i> @lang('trans.add')
                                </a>
                            @endisset
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="license_applications">
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
                                            $badgeClass = match($etatDemande) {
                                                'submitted' => 'badge-submitted',
                                                'under_review' => 'badge-under_review',
                                                'service_approved' => 'badge-service_approved',
                                                'paid' => 'badge-paid',
                                                'payment_confirmed' => 'badge-payment_confirmed',
                                                'printed' => 'badge-printed',
                                                'rejected' => 'badge-rejected',
                                                default => 'badge-secondary',
                                            };
                                        @endphp
                                        <tr>
                                            <td>{{ $demande->code }}</td>
                                            <td>{{ $demande->demandeur->np }}</td>
                                            <td>{{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($demande->typeDemande)->nom_fr : optional($demande->typeDemande)->nom_en }}</td>
                                            <td>{{ $demande->typeLicence->nom }}</td>
                                            <td>
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ $etatDemande }}
                                                </span>
                                            </td>
                                            <td>
                                                @if (!$demande->etatDemande->demandeur_cree_demande)
                                                    <a href="{{ route('user.licences.edit', $demande->id) }}"
                                                        class="btn btn-warning btn-sm">@lang('trans.edit')</a>

                                                    <form action="{{ route('update-state-licence', $demande->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="action" value="demandeur_cree_demande">
                                                        <input type="hidden" name="is_approved" value="1">
                                                        <button type="submit" class="btn btn-success btn-sm mb-1"
                                                            onclick="return confirm('Confirmer la validation de la demande ?')">
                                                            <i class="fas fa-check-circle"></i> @lang('trans.validate')
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('user.licences.destroy', $demande->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Confirmer la suppression ?')">@lang('trans.destroy')</button>
                                                    </form>
                                                @endif
                                                @if (!empty($demande->paiement) && !$demande->etatDemande->demandeur_payer && !$demande->etatDemande->compagnie_payer)
                                                    <a href="{{ route('user.licences.pay', $demande->paiement->id) }}"
                                                        class="btn btn-primary btn-sm">@lang('trans.pay')</a>
                                                @endif
                                                @if (!empty($demande->facture) && !$demande->etatDemande->demandeur_payer && !$demande->etatDemande->compagnie_payer)
                                                    <button class="btn btn-warning btn-sm"
                                                        onclick="openPdfModal('{{ asset('/uploads/' . $demande->facture->facture) }}')">
                                                        @lang('trans.invoice')</button>
                                                @endif
                                                @if ($demande->authentificationDisponible())
                                                    <a href="{{ route('user.imprimer', $demande->id) }}"
                                                        class="btn btn-primary btn-sm"
                                                        target="_blank">@lang('trans.print_authentication')</a>
                                                @endif

                                                @if (in_array($demande->typeDemande->id, [7]) && !empty($demande->validation) && isset($demande->validation))
                                                    <a href="{{ route('user.validation', $demande->validation) }}"
                                                        class="btn btn-primary btn-sm" target="_blank">
                                                        @lang('trans.print_validation')</a>
                                                @endif
                                                @if ($demande->has_issues && $demande->etatDemande->demandeur_cree_demande && !$demande->etatDemande->pel_valider)
                                                    <button class="btn btn-info btn-sm" data-toggle="modal"
                                                        data-target="#issuesModal-{{ $demande->id }}"
                                                        title="@lang('trans.view_issues')">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>

                                                    <div class="modal fade" id="issuesModal-{{ $demande->id }}"
                                                        tabindex="-1">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-dark text-white">
                                                                    <h5 class="modal-title">
                                                                        @lang('trans.issues_for')
                                                                        {{ $demande->typeDemande->nom_fr ?? '' }}
                                                                    </h5>
                                                                    <button type="button" class="close text-white"
                                                                        data-dismiss="modal">
                                                                        <span>&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    @if (count($demande->invalid_reasons) > 0)
                                                                        <div class="alert alert-warning">
                                                                            <h6><i class="fas fa-exclamation-triangle"></i>
                                                                                @lang('trans.invalid_components')</h6>
                                                                            <ul>
                                                                                @foreach ($demande->invalid_reasons as $component)
                                                                                    <li>
                                                                                        <strong>{{ ucfirst(str_replace('_', ' ', $component['type'])) }}:</strong>
                                                                                        {{ $component['identifier'] }}
                                                                                        @if (!empty($component['motif']))
                                                                                            -
                                                                                            <em>{{ $component['motif'] }}</em>
                                                                                        @endif
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </div>
                                                                    @endif

                                                                    @if (count($demande->rejection_reasons_list) > 0)
                                                                        <div class="alert alert-danger">
                                                                            <h6><i class="fas fa-ban"></i>
                                                                                @lang('trans.rejection_reasons')</h6>
                                                                            <ul>
                                                                                @foreach ($demande->rejection_reasons_list as $reason)
                                                                                    <li>{{ $reason }}</li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">
                                                                        @lang('trans.close')
                                                                    </button>
                                                                    @if (auth()->user()->can('edit-demandes'))
                                                                        <a href="{{ route('demandes.edit', $demande->id) }}"
                                                                            class="btn btn-primary">
                                                                            <i class="fas fa-edit"></i>
                                                                            @lang('trans.correct_issues')
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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

    @push('script')
        <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>
    @endpush

    @push('custom')
        <script>
            $(document).ready(function() {
                $('#license_applications').DataTable({
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
                    }]
                });
            });
        </script>
    @endpush

</x-app-user-layout>
