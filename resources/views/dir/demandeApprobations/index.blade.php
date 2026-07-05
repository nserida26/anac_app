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
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])</a>
    @endif
    @if (auth()->user()->hasRole('dta'))
        <a href="{{ route('dta') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')]) </a>
    @endif
    @if (auth()->user()->hasRole('dsna'))
        <a href="{{ route('dsna') }}">
            @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')]) </a>
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
                                                            <a href="{{ route('dg.approbations.show', $demande->id) }}"
                                                                class="btn btn-info btn-sm">@lang('trans.view')</a>
                                                        @endif
                                                        @if (auth()->user()->hasRole('dg'))
                                                            @if (optional($demande->etatDemande)->compagnie_cree_demande &&
                                                                    !optional($demande->etatDemande)->dg_annoter &&
                                                                    !optional($demande->etatDemande)->dta_dg_annoter &&
                                                                    !optional($demande->etatDemande)->dg_rejeter)
                                                                <form
                                                                    action="{{ route('update-state-approbation', $demande->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="action" value="dg_annoter">
                                                                    <input type="hidden" name="is_approved" value="1">
                                                                    <button type="submit" class="btn btn-success btn-sm"
                                                                        onclick="return confirm('Confirmer l\'annotation ?')">
                                                                        @lang('trans.annotate')
                                                                    </button>
                                                                </form>
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                    onclick="openRejectionModal('demande_approbations','{{ $demande->id }}')">
                                                                    @lang('trans.reject')
                                                                </button>
                                                            @endif
                                                            @if (optional($demande->etatDemande)->compagnie_cree_demande &&
                                                                    (optional($demande->etatDemande)->dg_annoter || optional($demande->etatDemande)->dta_dg_annoter) &&
                                                                    (optional($demande->etatDemande)->service_valider || optional($demande->etatDemande)->service_tout_valider) &&
                                                                    optional($demande->etatDemande)->dta_valider &&
                                                                    !optional($demande->etatDemande)->dg_valider)
                                                                <form
                                                                    action="{{ route('update-state-approbation', $demande->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="action" value="dg_valider">
                                                                    <input type="hidden" name="is_approved" value="1">
                                                                    <button type="submit" class="btn btn-success btn-sm"
                                                                        onclick="return confirm('Confirmer la validation ?')">
                                                                        @lang('trans.validate')
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endif
                                                        @if (auth()->user()->hasRole('dta'))
                                                            @if (optional($demande->etatDemande)->compagnie_cree_demande &&
                                                                    !optional($demande->etatDemande)->dg_annoter &&
                                                                    !optional($demande->etatDemande)->dta_dg_annoter &&
                                                                    !optional($demande->etatDemande)->dg_rejeter)
                                                                <form
                                                                    action="{{ route('update-state-approbation', $demande->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="action"
                                                                        value="dta_dg_annoter">
                                                                    <input type="hidden" name="is_approved" value="1">
                                                                    <button type="submit" class="btn btn-success btn-sm"
                                                                        onclick="return confirm('Confirmer l\'annotation ?')">
                                                                        @lang('trans.annotate_dg')
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            @if (optional($demande->etatDemande)->compagnie_cree_demande &&
                                                                    (optional($demande->etatDemande)->dg_annoter || optional($demande->etatDemande)->dta_dg_annoter) &&
                                                                    !optional($demande->etatDemande)->dg_rejeter)
                                                                @if (!optional($demande->etatDemande)->dta_annoter && !optional($demande->etatDemande)->service_tout_valider)
                                                                    <form
                                                                        action="{{ route('update-state-approbation', $demande->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="action"
                                                                            value="dta_annoter">
                                                                        <input type="hidden" name="is_approved"
                                                                            value="1">
                                                                        <button type="submit"
                                                                            class="btn btn-success btn-sm"
                                                                            onclick="return confirm('Confirmer l\'annotation ?')">
                                                                            @lang('trans.annotate')
                                                                        </button>
                                                                    </form>

                                                                    <button type="button" class="btn btn-danger btn-sm"
                                                                        onclick="openRejectionModal('demande_approbations','{{ $demande->id }}')">
                                                                        @lang('trans.reject')
                                                                    </button>
                                                                @endif


                                                                @if (
                                                                    !optional($demande->etatDemande)->dta_annoter &&
                                                                        !optional($demande->etatDemande)->service_tout_valider &&
                                                                        !optional($demande->etatDemande)->dta_valider &&
                                                                        $demande?->hasRejectionReasons() ?? false)
                                                                    <form
                                                                        action="{{ route('update-state-approbation', $demande->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="action"
                                                                            value="dta_notifier">
                                                                        <input type="hidden" name="is_approved"
                                                                            value="1">
                                                                        <button type="submit"
                                                                            class="btn btn-warning btn-sm"
                                                                            onclick="return confirm('Confirmer la notification ?')">
                                                                            @lang('trans.notify')
                                                                        </button>
                                                                    </form>
                                                                @endif

                                                                @if (optional($demande->etatDemande)->compagnie_cree_demande &&
                                                                        (optional($demande->etatDemande)->dg_annoter || optional($demande->etatDemande)->dta_dg_annoter) &&
                                                                        !optional($demande->etatDemande)->dta_annoter &&
                                                                        !optional($demande->etatDemande)->service_annoter &&
                                                                        !optional($demande->etatDemande)->service_tout_valider &&
                                                                        $demande?->isFullyValidated() ?? false)
                                                                    <form
                                                                        action="{{ route('update-state-approbation', $demande->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="action"
                                                                            value="service_tout_valider">
                                                                        <input type="hidden" name="is_approved"
                                                                            value="1">
                                                                        <button type="submit" class="btn btn-success"
                                                                            onclick="return confirm('Confirmer la validation a la place de directions ?')">
                                                                            @lang('trans.validate')
                                                                        </button>
                                                                    </form>
                                                                @endif

                                                                @if (optional($demande->etatDemande)->compagnie_cree_demande &&
                                                                        (optional($demande->etatDemande)->service_valider || optional($demande->etatDemande)->service_tout_valider) &&
                                                                        !optional($demande->etatDemande)->dta_valider &&
                                                                        $demande?->isFullyValidated() ?? false)
                                                                    <form
                                                                        action="{{ route('update-state-approbation', $demande->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="action"
                                                                            value="dta_valider">
                                                                        <input type="hidden" name="is_approved"
                                                                            value="1">
                                                                        <button type="submit"
                                                                            class="btn btn-success btn-sm"
                                                                            onclick="return confirm('Confirmer la validation ?')">
                                                                            @lang('trans.validate')
                                                                        </button>
                                                                    </form>
                                                                @endif

                                                                @if (
                                                                    !optional($demande->etatDemande)->dg_valider &&
                                                                        !optional($demande->etatDemande)->dta_dg_valider &&
                                                                        optional($demande->etatDemande)->dta_valider)
                                                                    <form
                                                                        action="{{ route('update-state-approbation', $demande->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="action"
                                                                            value="dta_dg_valider">
                                                                        <input type="hidden" name="is_approved"
                                                                            value="1">
                                                                        <button type="submit"
                                                                            class="btn btn-success btn-sm"
                                                                            onclick="return confirm('Confirmer la validation ?')">
                                                                            @lang('trans.validate_dg')
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            @endif
                                                        @endif
                                                        @if (auth()->user()->hasRole('dsv'))
                                                            @if (optional($demande->etatDemande)->compagnie_cree_demande &&
                                                                    (optional($demande->etatDemande)->dg_annoter || optional($demande->etatDemande)->dta_dg_annoter) &&
                                                                    optional($demande->etatDemande)->dta_annoter &&
                                                                    optional($demande->etatDemande)->service_annoter &&
                                                                    !optional($demande->etatDemande)->dsv_valider)
                                                                <form
                                                                    action="{{ route('update-state-approbation', $demande->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="action"
                                                                        value="dsv_valider">
                                                                    <input type="hidden" name="is_approved"
                                                                        value="1">
                                                                    <button type="submit" class="btn btn-success btn-sm"
                                                                        onclick="return confirm('Confirmer la validation ?')">
                                                                        @lang('trans.validate')
                                                                    </button>
                                                                </form>
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                    onclick="openAchievementModal('demande_approbations','{{ $demande->id }}')">
                                                                    @lang('trans.achieve')
                                                                </button>
                                                            @endif
                                                        @endif
                                                        @if (auth()->user()->hasRole('dsna'))
                                                            @if (optional($demande->etatDemande)->compagnie_cree_demande &&
                                                                    (optional($demande->etatDemande)->dg_annoter || optional($demande->etatDemande)->dta_dg_annoter) &&
                                                                    optional($demande->etatDemande)->dta_annoter &&
                                                                    optional($demande->etatDemande)->service_annoter &&
                                                                    !optional($demande->etatDemande)->dsna_valider)
                                                                <form
                                                                    action="{{ route('update-state-approbation', $demande->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="action"
                                                                        value="dsna_valider">
                                                                    <input type="hidden" name="is_approved"
                                                                        value="1">
                                                                    <button type="submit" class="btn btn-success btn-sm"
                                                                        onclick="return confirm('Confirmer la validation ?')">
                                                                        @lang('trans.validate')
                                                                    </button>
                                                                </form>
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                    onclick="openAchievementModal('demande_approbations','{{ $demande->id }}')">
                                                                    @lang('trans.achieve')
                                                                </button>
                                                            @endif
                                                        @endif
                                                        @if (auth()->user()->hasRole('dsad'))
                                                            @if (optional($demande->etatDemande)->compagnie_cree_demande &&
                                                                    (optional($demande->etatDemande)->dg_annoter || optional($demande->etatDemande)->dta_dg_annoter) &&
                                                                    optional($demande->etatDemande)->dta_annoter &&
                                                                    optional($demande->etatDemande)->service_annoter &&
                                                                    !optional($demande->etatDemande)->dsad_valider)
                                                                <form
                                                                    action="{{ route('update-state-approbation', $demande->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="action"
                                                                        value="dsad_valider">
                                                                    <input type="hidden" name="is_approved"
                                                                        value="1">
                                                                    <button type="submit" class="btn btn-success btn-sm"
                                                                        onclick="return confirm('Confirmer la validation ?')">
                                                                        @lang('trans.validate')
                                                                    </button>
                                                                </form>
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                    onclick="openAchievementModal('demande_approbations','{{ $demande->id }}')">
                                                                    @lang('trans.achieve')
                                                                </button>
                                                            @endif
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

    <!-- Modale pour le motif de achievement -->
    <div class="modal fade" id="achievementModal" tabindex="-1" aria-labelledby="achievementModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="achievementModalLabel">Motif</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="achievementForm" method="POST" class="d-inline">
                        @csrf

                        <div class="form-group">
                            <label for="motif">Veuillez preciser le motif :</label>
                            <textarea name="motif" id="motifForm" class="form-control" rows="3" required></textarea>
                        </div>
                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="table" id="tableForm">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" onclick="submitAchievementForm()">Demande
                        d'achievement</button>
                </div>
            </div>
        </div>
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
        function openAchievementModal(table, id) {
            // Définir les valeurs des champs cachés
            document.getElementById('id').value = id;
            document.getElementById('tableForm').value = table;
            // Ouvrir la modale
            new bootstrap.Modal(document.getElementById('achievementModal')).show();
        }

        function submitAchievementForm() {
            const motif = document.getElementById('motifForm').value;
            if (!motif) {
                alert('Veuillez saisir un motif.');
                return;
            }

            // Confirmer avant de soumettre
            if (confirm('Confirmer l\'achievement de cette information ?')) {
                const form = $('#achievementForm');
                const data = form.serialize();

                let demandeId = $('#id').val();



                $.ajax({
                    url: "{{ route('dir.achiever', ':id') }}".replace(':id', demandeId),
                    type: 'POST',
                    data: data,
                    success: function(response) {

                        alert('Achievement effectué avec succès !');
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
