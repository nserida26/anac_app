<div class="row">
    <div class="col-md-6">
        <h5>Informations de l'opérateur</h5>
        <table class="table table-sm">
            <tr>
                <th>Nom Entreprise:</th>
                <td>{{ $details->nom_entreprise }}</td>
            </tr>
            <tr>
                <th>Panier:</th>
                <td>{{ number_format($details->panier, 0, ',', ' ') }} MRU</td>
            </tr>
            <tr>
                <th>Plafond:</th>
                <td>{{ number_format($details->plafond, 0, ',', ' ') }} MRU</td>
            </tr>
            <tr>
                <th>Total Recettes:</th>
                <td class="font-weight-bold">{{ number_format($details->total_recettes, 0, ',', ' ') }} MRU</td>
            </tr>
            <tr>
                <th>Pourcentage:</th>
                <td>
                    @if($details->plafond > 0)
                        @php $pourcentage = ($details->total_recettes / $details->plafond) * 100; @endphp
                        {{ round($pourcentage, 1) }}%
                        @if($pourcentage > 100)
                            <span class="badge badge-danger ml-2">Dépassé</span>
                        @elseif($pourcentage > 80)
                            <span class="badge badge-warning ml-2">Risque</span>
                        @else
                            <span class="badge badge-success ml-2">Normal</span>
                        @endif
                    @else
                        N/A
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h5>Statistiques</h5>
        <table class="table table-sm">
            <tr>
                <th>Nombre de demandeurs:</th>
                <td>{{ $details->nombre_demandeurs }}</td>
            </tr>
            <tr>
                <th>Nombre de demandes:</th>
                <td>{{ $details->nombre_demandes }}</td>
            </tr>
            <tr>
                <th>Nombre d'ordres:</th>
                <td>{{ $details->nombre_ordres }}</td>
            </tr>
            <tr>
                <th>Moyenne par ordre:</th>
                <td>{{ number_format($details->moyenne_ordre, 0, ',', ' ') }} MRU</td>
            </tr>
            <tr>
                <th>Dernier ordre:</th>
                <td>{{ $details->dernier_ordre ? \Carbon\Carbon::parse($details->dernier_ordre)->format('d/m/Y') : 'Aucun' }}</td>
            </tr>
        </table>
    </div>
</div>

@if($ordres->isNotEmpty())
<div class="mt-4">
    <h5>Ordres de recette</h5>
    <div class="table-responsive">
        <table class="table table-sm table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Référence</th>
                    <th>Demandeur</th>
                    <th>Montant</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ordres as $ordre)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($ordre->date_ordre)->format('d/m/Y') }}</td>
                    <td>{{ $ordre->demande_reference }}</td>
                    <td>{{ $ordre->demandeur_nom }}</td>
                    <td>{{ number_format($ordre->montant, 0, ',', ' ') }} MRU</td>
                    <td><span class="badge badge-success">{{ $ordre->statut }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif