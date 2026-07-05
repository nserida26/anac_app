@extends('dir.layouts.app')
@section('title')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection
@section('contentheader')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection
@section('contentheaderlink')
    @if (auth()->user()->hasRole('dsv'))
        <a href="{{ route('dsv') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')]) </a>
    @endif
    @if (auth()->user()->hasRole('dg'))
        <a href="{{ route('dg') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')]) </a>
    @endif
    @if (auth()->user()->hasRole('dta'))
        <a href="{{ route('dta') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')]) </a>
    @endif
    @if (auth()->user()->hasRole('dsna'))
        <a href="{{ route('dsna') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])</a>
    @endif
    @if (auth()->user()->hasRole('dsad'))
        <a href="{{ route('dsad') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')]) </a>
    @endif
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
        .select2-container--open {
            z-index: 9999 !important;
        }
        .btn-group-vertical > .btn {
            margin-bottom: 5px;
        }
        .badge-submitted { background-color: #17a2b8; color: white; }
        .badge-under_review { background-color: #ffc107; color: black; }
        .badge-service_approved { background-color: #28a745; color: white; }
        .badge-paid { background-color: #007bff; color: white; }
        .badge-payment_confirmed { background-color: #20c997; color: white; }
        
        .badge-printed{background-color: navy; color: white;}
        .badge-rejected { background-color: #dc3545; color: white; }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        @if ($demandes->isNotEmpty())
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">@lang('trans.applications')</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="demandes">
                                    <thead>
                                        <tr>
                                        <th>@lang('trans.id')</th>
                                        <th>@lang('trans.date')</th>
                                        <th>@lang('trans.applicant')</th>
                                        <th>@lang('trans.compagny')</th>
                                        <th>@lang('trans.type_application')</th>
                                        <th>@lang('trans.type_license')</th>
                                        <th>@lang('trans.status')</th>
                                        <th>@lang('trans.paiements')</th>
                                        <th>@lang('trans.signed')</th>
                                        
                                            @if (auth()->user()->hasRole('dg'))
                                                <th>#</th>
                                            @endif
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
                                            <td>{{ $demande->date }}</td>
                                            <td>{{ $demande->demandeur->np }}  </td>
                                             <td> 
                                              @if (!empty(optional($demande->demandeur)->compagnie))
                                              <span class="badge badge-success">{{ $demande->demandeur->compagnie->nom_entreprise }}</span>
                                              @else
                                              <span class="badge badge-danger">{{ 'Privé' }}</span>
                                              @endif
                                             </td>
                                            <td>{{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($demande->typeDemande)->nom_fr : optional($demande->typeDemande)->nom_en }}
                                            </td>
                                            <td>{{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($demande->typeLicence)->fr : optional($demande->typeLicence)->en }}</td>
                                            <td>
                                            @php
                                                $badgeClass = match($etatDemande) {
                                                    'submitted' => 'badge-submitted',
                                                    'under_review' => 'badge-under_review',
                                                    'service_approved' => 'badge-service_approved',
                                                    'paid' => 'badge-paid',
                                                    'payment_confirmed' => 'badge-payment_confirmed',
                                                     'printed' => 'badge-printed',
                                                    'rejected' => 'badge-rejected',
                                                    default => 'badge-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ $etatDemande }}
                                            </span>
                                            </td>
                                    
                                            <td>{{ optional($demande->ordre)->statut }}</td>
                                            <td>
                                                @if (empty(optional($demande->demandeur)->signature))
                                                    <span class="badge badge-danger">@lang('trans.no')</span>
                                                @else
                                                    <span class="badge badge-success">@lang('trans.yes')</span>
                                                @endif
                                            </td>
                                                @if (auth()->user()->hasRole('dg'))
                                                    <td>

                                                        @if (optional($demande->etatDemande)->dsv_dg_annoter)
                                                            <span class="badge badge-primary">@lang('trans.annotated_dsv')</span>
                                                        @endif
                                                        @if (optional($demande->etatDemande)->dsv_dg_valider)
                                                            <span class="badge badge-primary">@lang('trans.validated_dsv')</span>
                                                        @endif
                                                        


                                                    </td>
                                                @endif

                                                <td>


                                                    @if (auth()->user()->hasRole('dg'))
                                                        @if (optional($demande->etatDemande)->demandeur_cree_demande)
                                                            <a href="{{ route('dg.show', $demande->id) }}"
                                                                class="btn btn-info btn-sm">@lang('trans.view')</a>
                                                        @endif

                                                        @if (optional($demande->etatDemande)->demandeur_cree_demande && 
                                                                    !optional($demande->etatDemande)->dg_annoter && 
                                                                    !optional($demande->etatDemande)->dg_rejeter)
                                                            <form action="{{ route('update-state-licence', $demande->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="action" value="dg_annoter">
                                                                <input type="hidden" name="is_approved" value="1">
                                                                <button type="submit" class="btn btn-info btn-sm mb-1"
                                                                    onclick="return confirm('Confirmer l\'annotation par le DG ?')">
                                                                    <i class="fas fa-edit"></i> @lang('trans.annotate')
                                                                </button>
                                                            </form>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="openRejectionModal('demandes','{{ $demande->id }}')">
                                                                @lang('trans.reject')
                                                            </button>
                                                        @endif


                                                        @if (optional($demande->etatDemande)->pel_valider &&
                                                                optional($demande->etatDemande)->dsv_valider &&
                                                                !optional($demande->etatDemande)->dg_valider)
                                                                {{-- DG Valider --}}
                                                                <form action="{{ route('update-state-licence', $demande->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <input type="hidden" name="action" value="dg_valider">
                                                                    <input type="hidden" name="is_approved" value="1">
                                                                    <button type="submit" class="btn btn-success btn-sm mb-1"
                                                                        onclick="return confirm('Confirmer la validation par le DG ?')">
                                                                        <i class="fas fa-check-circle"></i> @lang('trans.validate')
                                                                    </button>
                                                                </form>
                                                        @endif
                                                    @endif


                                                    @if (auth()->user()->hasRole('dsv'))
                                                        @if (optional($demande->etatDemande)->demandeur_cree_demande)
                                                            <a href="{{ route('dsv.show', $demande->id) }}"
                                                                class="btn btn-info btn-sm">@lang('trans.view')</a>
                                                        @endif

                                                        {{--@if (optional($demande->etatDemande)->demandeur_cree_demande &&
                                                                !optional($demande->etatDemande)->dg_annoter &&
                                                                !optional($demande->etatDemande)->dg_rejeter &&
                                                                !optional($demande->etatDemande)->dsv_annoter)
                                                            <form action="{{ route('dg.annoter', $demande->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-success btn-sm"
                                                                    onclick="return confirm('Confirmer l\' annotation vers DSV ?')">
                                                                    @lang('trans.annotate_dg')
                                                                </button>
                                                            </form>
                                                        @endif
                                                        @if (optional($demande->etatDemande)->pel_valider &&
                                                                optional($demande->etatDemande)->dsv_valider &&
                                                                !optional($demande->etatDemande)->dg_valider)
                                                            <form action="{{ route('dg.valider', $demande->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-success btn-sm"
                                                                    onclick="return confirm('Confirmer la validation ?')">
                                                                    @lang('trans.validate_dg')
                                                                </button>
                                                            </form>
                                                        @endif
                                                        --}}

                                                        @if (optional($demande->etatDemande)->dg_annoter &&
                                                                !optional($demande->etatDemande)->dg_rejeter &&
                                                                !optional($demande->etatDemande)->dsv_annoter &&
                                                                !optional($demande->etatDemande)->dsv_rejeter)
                                                            <form action="{{ route('update-state-licence', $demande->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="action" value="dsv_annoter">
                                                                <input type="hidden" name="is_approved" value="1">
                                                                <button type="submit" class="btn btn-info btn-sm mb-1"
                                                                    onclick="return confirm('Confirmer l\'annotation par le DSV ?')">
                                                                    <i class="fas fa-edit"></i> @lang('trans.annotate')
                                                                </button>
                                                            </form>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="openRejectionModal('demandes','{{ $demande->id }}')">
                                                                @lang('trans.reject')
                                                            </button>
                                                        @endif
                                                        

                                                        @if (optional($demande->etatDemande)->pel_valider && !optional($demande->etatDemande)->dsv_valider)
                                                            {{-- DSV Valider --}}
                                                            <form action="{{ route('update-state-licence', $demande->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="action" value="dsv_valider">
                                                                <input type="hidden" name="is_approved" value="1">
                                                                <button type="submit" class="btn btn-success btn-sm mb-1"
                                                                    onclick="return confirm('Confirmer la validation par le DSV ?')">
                                                                    <i class="fas fa-check-circle"></i> @lang('trans.validate')
                                                                </button>
                                                            </form>
                                                        @endif


                                                    @if (
                                                                optional($demande->etatDemande)->dg_valider === true &&
                                                                optional($demande->etatDemande)->dsv_recette !== true &&
                                                                is_null($demande->ordre)
                                                            )

                                                            
                                                            <form action="{{ route('dsv.store', $demande) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-success btn-sm"
                                                                    onclick="return confirm('Confirmer la generation ?')">
                                                                    @lang('trans.generate_order')
                                                                </button>
                                                            </form>
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
        @endif
        @if (auth()->user()->hasRole('dsv') && $ordres->isNotEmpty())
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">@lang('trans.orders')</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="ordres">
                                    <thead>
                                        <tr>
                                            <th>@lang('trans.code')</th>
                                            <th>@lang('trans.applicant')</th>
                                            <th>@lang('trans.operator')</th>
                                            <th>@lang('trans.date')</th>
                                            <th>@lang('trans.amount')</th>
                                            <th>@lang('trans.status')</th>

                                            <th>@lang('trans.actions')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ordres as $ordre)
                                            <tr>
                                                <td>{{  $ordre->demande->code }}</td>
                                                <td>{{ $ordre->demande->demandeur->np ?? 'N/A' }}</td>
                                                <td>{{ $ordre->demande->demandeur->compagnie->nom_entreprise ?? 'N/A' }}</td>
                                                <td>{{ $ordre->date_ordre }}</td>
                                                <td>{{ $ordre->montant }}</td>

                                                <td>{{ $ordre->statut }}</td>

                                                <td>

                                                    @if ($ordre->statut !== 'Validé')
                                                        <form action="{{ route('dsv.ordre.valider', $ordre) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-success btn-sm btn-sm"
                                                                onclick="return confirm('Confirmer la validation ?')">
                                                                @lang('trans.validate')
                                                            </button>
                                                        </form>

                                                        
                                                    @endif
                                                    <form action="{{ route('dsv.ordre.destroy', $ordre) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Confirmer la suppression ?')">
                                                                @lang('trans.destroy')
                                                            </button>
                                                    </form>
                                                    <a href="{{ route('dsv.ordre', $ordre->id) }}"
                                                        class="btn btn-warning btn-sm"
                                                        target="_blank">@lang('trans.print')</a>


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
    <!-- Modale pour le motif de rejet -->
    <div class="modal fade" id="rejectionModal" tabindex="-1" aria-labelledby="rejectionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectionModalLabel">Motif de rejet</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rejectionForm" method="POST" class="d-inline">
                        @csrf

                        <div class="form-group">
                            <label for="motif">Veuillez preciser le motif de rejet :</label>
                            <textarea name="motif" id="motif" class="form-control" rows="3" required></textarea>
                        </div>
                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="table" id="table">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" onclick="submitRejectionForm()">Rejeter</button>
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
        // Fonction pour ouvrir la modale et définir les valeurs du formulaire
        function openRejectionModal(table, id) {
            // Définir les valeurs des champs cachés
            document.getElementById('id').value = id;
            document.getElementById('table').value = table;
            // Ouvrir la modale
            new bootstrap.Modal(document.getElementById('rejectionModal')).show();
        }

        function submitRejectionForm() {
            const motif = document.getElementById('motif').value;
            if (!motif) {
                alert('Veuillez saisir un motif de rejet.');
                return;
            }

            // Confirmer avant de soumettre
            if (confirm('Confirmer le rejet de cette information ?')) {
                const form = $('#rejectionForm');
                const data = form.serialize();

                let demandeId = $('#id').val();


                $.ajax({
                    url: "{{ route('dir.rejeter', ':id') }}".replace(':id', demandeId),
                    type: 'POST',
                    data: data,
                    success: function(response) {

                        alert('Rejet effectué avec succès !');
                        window.location.reload();
                    },
                    error: function(xhr) {
                        alert('Une erreur s\'est produite : ' + xhr.responseText);
                    }
                });


            }
        }
    </script>
    <script>
        $(function() {
            
            $('#ordres').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "columnDefs": [{
                        "targets": [5, 6],
                        "orderable": false,
                        "searchable": false
                    }
                ],
                "initComplete": function() {
                    // Add simple search inputs
                    this.api().columns().every(function(index) {
                        if (index !== 5 && index !== 6) {
                            var column = this;
                            var $input = $('<input type="text" placeholder="Search" class="form-control form-control-sm">')
                                .appendTo($(column.header()))
                                .on('keyup change', function() {
                                    column.search(this.value).draw();
                                });
                        }
                    });
                }
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
                        "targets": [7],
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "targets": 3,
                        "searchable": true
                    }
                ],
                "initComplete": function() {
                    // Add simple search inputs
                    this.api().columns().every(function(index) {
                        if (index !== 7) {
                            var column = this;
                            var $input = $('<input type="text" placeholder="Search" class="form-control form-control-sm">')
                                .appendTo($(column.header()))
                                .on('keyup change', function() {
                                    column.search(this.value).draw();
                                });
                        }
                    });
                }
            });
        });
    </script>
@endpush
