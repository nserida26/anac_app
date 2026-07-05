@extends('layouts.admin')
@section('title')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheaderlink')
    <a href="{{ route('dashboard') }}">
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
        <div class="row justify-content-center">
            <div class="col-md-12">
                @can('manage-dsv')
                    <div class="card">
                        <!--<div class="card-header bg-primary text-white">
                                                                                                        Statistiques des Demandes
                                                                                                    </div>-->

                        <div class="card-body">

                            <!-- Affichage du nombre de demandeurs -->
                            <div class="alert alert-info">
                                @lang('trans.total_applicants') : <strong id="nombreDemandeurs">0</strong>
                            </div>

                            <!-- Graphique des demandes par jour -->
                            <canvas id="chartJour" width="400" height="200"></canvas>
                            <br>

                            <!-- Graphique des demandes par mois -->
                            <canvas id="chartMois" width="400" height="200"></canvas>
                            <br>

                            <!-- Graphique des demandes par année -->
                            <canvas id="chartAnnee" width="400" height="200"></canvas>

                        </div>
                    </div>
                @endcan
                         @can('manage-vi')
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">
                            <i class="fas fa-plane"></i> Tableau de Bord - Direction des Transports Aériens (DTA)
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Statistiques des autorisations -->
                        <div class="row mb-4">
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $autorisations_stats['total'] ?? 0 }}</h3>
                                        <p>Demandes</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-file-contract"></i>
                                    </div>
                                    <a href="{{ route('demandeAutorisations') }}" class="small-box-footer">
                                        Voir toutes <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $autorisations_stats['valides'] ?? 0 }}</h3>
                                        <p>Demandes validées</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <a href="{{ route('demandeAutorisations') }}" class="small-box-footer">
                                        Voir détails <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $autorisations_stats['en_cours'] ?? 0 }}</h3>
                                        <p>En cours de traitement</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <a href="{{ route('demandeAutorisations') }}" class="small-box-footer">
                                        Traiter <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>{{ $autorisations_stats['attente_signature'] ?? 0 }}</h3>
                                        <p>Autorisations</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-signature"></i>
                                    </div>
                                    <a href="#table-autorisations" class="small-box-footer">
                                        Voir <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Demandes en attente -->
                        @if(isset($demandes_en_attente) && $demandes_en_attente > 0)
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> {{ $demandes_en_attente }} demande(s) en attente de traitement</h5>
                            <p>Veuillez traiter ces demandes dans les plus brefs délais.</p>
                            <a href="{{ route('demandeAutorisations') }}" class="btn btn-warning">
                                <i class="fas fa-tasks"></i> Voir les demandes en attente
                            </a>
                        </div>
                        @endif

                        <!-- Tableau des autorisations récentes -->
                        <div class="card" id="table-autorisations">
                            <div class="card-header bg-secondary text-white">
                                <h4 class="card-title">
                                    <i class="fas fa-history"></i> Autorisations
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Code</th>
                                                <th>Demande</th>
                                                <th>Date délivrance</th>
                                                <th>Date expiration</th>
                                                <th>Statut</th>
                                                <th>Signature</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recent_autorisations ?? [] as $autorisation)
                                            <tr>
                                                <td> @if($autorisation->demande)
                                                        <a href="{{ route('autorisations.print', $autorisation->id) }}" target="_blank">
                                                            {{ $autorisation->code_autorisation }}
                                                        </a>
                                                        @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                    
                                                </td>
                                                <td>
                                                    @if($autorisation->demande)
                                                        <strong>{{ $autorisation->demande->code }}</strong>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>{{ $autorisation->date_delivrance ? \Carbon\Carbon::parse($autorisation->date_delivrance)->format('d/m/Y') : 'N/A' }}</td>
                                                <td>
                                                    @if($autorisation->date_expiration)
                                                        @if(\Carbon\Carbon::parse($autorisation->date_expiration)->isPast())
                                                            <span class="badge badge-danger">
                                                                {{ \Carbon\Carbon::parse($autorisation->date_expiration)->format('d/m/Y') }}
                                                            </span>
                                                        @elseif(\Carbon\Carbon::parse($autorisation->date_expiration)->diffInDays(now()) <= 30)
                                                            <span class="badge badge-warning">
                                                                {{ \Carbon\Carbon::parse($autorisation->date_expiration)->format('d/m/Y') }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-success">
                                                                {{ \Carbon\Carbon::parse($autorisation->date_expiration)->format('d/m/Y') }}
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-warning">{{ strtoupper($autorisation->statut) }}</span>
                                                </td>
                                                <td>
                                                    @if($autorisation->signature_dta)
                                                        <span class="badge badge-success"><i class="fas fa-check"></i> Signée</span>
                                                    @else
                                                        <span class="badge badge-warning"><i class="fas fa-times"></i> En attente</span>
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
            
        @endcan
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>
@endpush
@push('custom')
    <script>
    $(document).ready(function() {
        // Initialiser DataTables si nécessaire
        if ($.fn.DataTable) {
            $('.table').DataTable();
        }
    });
        document.addEventListener("DOMContentLoaded", function() {
            fetch("{{ route('dashboard.data') }}")
                .then(response => response.json())
                .then(data => {
                    document.getElementById('nombreDemandeurs').innerText = data.nombreDemandeurs;

                    const labelsJour = data.demandesParJour.map(d => d.date);
                    const traiteeJour = data.demandesParJour.map(d => d.traitees);
                    const nonTraiteeJour = data.demandesParJour.map(d => d.non_traitees);

                    const labelsMois = data.demandesParMois.map(d => d.mois);
                    const traiteeMois = data.demandesParMois.map(d => d.traitees);
                    const nonTraiteeMois = data.demandesParMois.map(d => d.non_traitees);

                    const labelsAnnee = data.demandesParAnnee.map(d => d.annee);
                    const traiteeAnnee = data.demandesParAnnee.map(d => d.traitees);
                    const nonTraiteeAnnee = data.demandesParAnnee.map(d => d.non_traitees);

                    function renderChart(canvasId, labels, dataTraitees, dataNonTraitees, labelX) {
                        new Chart(document.getElementById(canvasId), {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                        label: 'Demandes Traitées',
                                        backgroundColor: 'green',
                                        data: dataTraitees
                                    },
                                    {
                                        label: 'Demandes Non Traitées',
                                        backgroundColor: 'red',
                                        data: dataNonTraitees
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: labelX
                                        }
                                    },
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    }

                    renderChart('chartJour', labelsJour, traiteeJour, nonTraiteeJour, 'Date');
                    renderChart('chartMois', labelsMois, traiteeMois, nonTraiteeMois, 'Mois');
                    renderChart('chartAnnee', labelsAnnee, traiteeAnnee, nonTraiteeAnnee, 'Année');
                });
        });
    </script>
    
@endpush
