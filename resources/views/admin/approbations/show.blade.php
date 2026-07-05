@extends('layouts.admin')
@section('title')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheaderlink')
    <a href="{{ route('approbations') }}">
        @lang('trans.dashboard_admin') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_admin')
@endsection

@push('css')
@endpush
@section('content')

    <div class="container-fluid">
        <h1></h1>
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card">

                    <div class="card-body">
                        @isset($approbation)
                            <div class="row justify-content-center">

                                <!-- Definition Section -->
                                <div>
                                    <h5 class="section-title"></h5>
                                    <p><strong><u>Objet :</u> Approbation Programme {{ $approbation->saison }}
                                            {{ date('Y', strtotime($approbation->date_approbation)) }}</strong></p>
                                    <p class="reference"><strong><u>Réf. :</u> {{ $approbation->reference }} du
                                            {{ $approbation->date_approbation }}</strong></p>
                                </div>

                                <!-- Revision Section -->
                                <div>
                                    <h5 class="section-title"></h5>
                                    @php
                                        $dateDebut = \Carbon\Carbon::parse($approbation->date_debut)->locale('fr');
                                        $dayDebut = $dateDebut->day;
                                        $monthDebut = strtoupper($dateDebut->monthName);
                                        $dateFin = \Carbon\Carbon::parse($approbation->date_fin)->locale('fr');
                                        $dayFin = $dateFin->day;
                                        $monthFin = strtoupper($dateFin->monthName);
                                        $yearFin = $dateFin->year;
                                    @endphp
                                    <p><strong>Suite à votre lettre citée en référence, je vous notifie par la présente notre
                                            accord pour
                                            l'exécution de votre programme {{ $approbation->saison }}
                                            {{ date('Y', strtotime($approbation->date_approbation)) }}, du
                                            {{ "$dayDebut $monthDebut" }}
                                            au
                                            {{ "$dayFin $monthFin" }}
                                            {{ $yearFin }}, selon
                                            les jours,
                                            horaires et parcours suivants :</strong></p>

                                    <p> <strong><u>Type avion :</u>
                                            {{ $approbation->demande->avions->pluck('type.code')->implode(', ') }}
                                        </strong></p>
                                    <p> <strong><u>Immatriculation :</u>
                                            {{ $approbation->demande->avions->pluck('immatriculation')->implode(', ') }}
                                        </strong></p>

                                    <!-- Flight Schedule Table -->
                                    <div class="table-responsive flight-table">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Vol</th>
                                                    <th>Jours d'opération</th>
                                                    <th>Routing</th>
                                                    <th>Période</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($approbation->demande->vols as $vol)
                                                    <tr>
                                                        <td>{{ $vol->numero_vol }}</td>
                                                        <td>{{ $vol->jours_operation_display }}</td>
                                                        @php
                                                            $routeString = null;
                                                            // Regrouper les itinéraires par vol
                                                            $groupedItineraires = $approbation->demande->itineraires->groupBy(
                                                                'vol_id',
                                                            );
                                                        @endphp

                                                        @foreach ($groupedItineraires as $volId => $volItineraires)
                                                            @php
                                                                $vol = $volItineraires->first()->volApprobation;
                                                                $output = [];
                                                                foreach ($volItineraires as $itineraire) {
                                                                    $aeroportCode = $itineraire->aeroport->codeICAO;
                                                                    $heureArrivee = date(
                                                                        'Hi',
                                                                        strtotime($itineraire->heure_arrivee),
                                                                    );
                                                                    $heureDepart = date(
                                                                        'Hi',
                                                                        strtotime($itineraire->heure_depart),
                                                                    );
                                                                    if (empty($output)) {
                                                                        $output[] = "{$heureArrivee} {$aeroportCode} {$heureDepart}";
                                                                    } else {
                                                                        $output[] = " - {$heureArrivee} {$aeroportCode} {$heureDepart}";
                                                                    }
                                                                }
                                                                $routeString = implode(' ', $output);
                                                            @endphp
                                                        @endforeach
                                                        <td>{{ $vol->aeroportDepart->codeICAO }}
                                                            {{ date('Hi', strtotime($vol->heure_depart)) }}
                                                            @if (!empty($routeString))
                                                                -
                                                                {{ $routeString }}
                                                            @endif

                                                            -
                                                            {{ date('Hi', strtotime($vol->heure_depart)) }}
                                                            {{ $vol->aeroportArrivee->codeICAO }}
                                                        </td>
                                                        @if ($vol->date_debut !== $approbation->demande->date_debut || $vol->date_fin !== $approbation->demande->date_fin)
                                                            <td>{{ date('d-m-Y', strtotime($vol->date_debut)) }} au
                                                                {{ date('d-m-Y', strtotime($vol->date_fin)) }}</td>
                                                        @else
                                                            <td></td>
                                                        @endif

                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>

                                    @if ($approbation->demande->amender)
                                        @php
                                            $previousApproval = $approbation->getPreviousApproval();
                                        @endphp
                                        <p>Cette approbation annule et remplace l'approbation n°
                                            {{ $previousApproval->reference }} du
                                            {{ date('d/m/Y', strtotime($previousApproval->date_approbation)) }} .</p>
                                    @endif

                                    <p>Veuillez agréer, Monsieur le Délégué, l'expression de mes salutations les meilleures.</p>
                                </div>

                            </div>
                        @endisset


                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('script')
@endpush
@push('custom')
@endpush
