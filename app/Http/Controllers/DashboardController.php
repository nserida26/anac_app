<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Demande;
use Illuminate\Support\Facades\DB;
use App\Models\Demandeur;
use App\Models\DemandeAutorisation;
use App\Models\Autorisation;

use Carbon\Carbon;
class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // Page des statistiques
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $dashboardData = $this->getDTADashboardData($dateFrom, $dateTo);
        $data = $this->getCommonChartData($dateFrom, $dateTo);
        return view('admin.dashboard', array_merge($data, $dashboardData, [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]));
    }
    private function getCommonChartData($dateFrom = null, $dateTo = null)
    {
        // Données pour les graphiques existants
        $nombreDemandeurs = Demandeur::count();

        $demandesParJourQuery = DemandeAutorisation::selectRaw('
                DATE(created_at) as date,
                SUM(CASE WHEN statut = "validated" THEN 1 ELSE 0 END) as traitees,
                SUM(CASE WHEN statut != "validated" THEN 1 ELSE 0 END) as non_traitees
            ');
        if ($dateFrom && $dateTo) {
            $demandesParJourQuery->whereBetween('created_at', [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()]);
        } else {
            $demandesParJourQuery->whereDate('created_at', '>=', Carbon::now()->subDays(7));
        }
        $demandesParJour = $demandesParJourQuery
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $demandesParMoisQuery = DemandeAutorisation::selectRaw('
                MONTH(created_at) as mois,
                YEAR(created_at) as annee,
                CONCAT(MONTHNAME(created_at), " ", YEAR(created_at)) as mois_annee,
                SUM(CASE WHEN statut = "validated" THEN 1 ELSE 0 END) as traitees,
                SUM(CASE WHEN statut != "validated" THEN 1 ELSE 0 END) as non_traitees
            ');
        if ($dateFrom && $dateTo) {
            $demandesParMoisQuery->whereBetween('created_at', [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()]);
        } else {
            $demandesParMoisQuery->whereYear('created_at', Carbon::now()->year);
        }
        $demandesParMois = $demandesParMoisQuery
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get();

        $demandesParAnneeQuery = DemandeAutorisation::selectRaw('
                YEAR(created_at) as annee,
                SUM(CASE WHEN statut = "validated" THEN 1 ELSE 0 END) as traitees,
                SUM(CASE WHEN statut != "validated" THEN 1 ELSE 0 END) as non_traitees
            ');
        if ($dateFrom && $dateTo) {
            $demandesParAnneeQuery->whereBetween('created_at', [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()]);
        }
        $demandesParAnnee = $demandesParAnneeQuery
            ->groupBy('annee')
            ->orderBy('annee', 'desc')
            ->limit(5)
            ->get();

        return [
            'nombreDemandeurs' => $nombreDemandeurs,
            'demandesParJour' => $demandesParJour,
            'demandesParMois' => $demandesParMois,
            'demandesParAnnee' => $demandesParAnnee
        ];
    }

    /**
     * Dashboard DTA (Direction des Transports Aériens)
     */
    private function getDTADashboardData($dateFrom = null, $dateTo = null)
    {
        $applyDateRange = function ($query, $column = 'created_at') use ($dateFrom, $dateTo) {
            if ($dateFrom && $dateTo) {
                $query->whereBetween($column, [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()]);
            }
            return $query;
        };

        // Statistiques des autorisations
        $autorisationsStats = [
            'total' => $applyDateRange(DemandeAutorisation::query())->count(),
            'valides' => $applyDateRange(DemandeAutorisation::join('etat_demande_autorisations','demande_autorisations.id','etat_demande_autorisations.demande_id'), 'demande_autorisations.created_at')
                                                ->where('etat_demande_autorisations.dg_valider', true)
                                                ->orWhere('etat_demande_autorisations.dta_dg_valider', true)
                                                ->count(),
            'expirees' => $applyDateRange(Autorisation::where('date_expiration', '<', Carbon::now()))->count(),

            'en_cours' => $applyDateRange(DemandeAutorisation::join('etat_demande_autorisations','demande_autorisations.id','etat_demande_autorisations.demande_id'), 'demande_autorisations.created_at')
                                                ->where('etat_demande_autorisations.dg_annoter', true)
                                                ->orWhere('etat_demande_autorisations.dta_dg_annoter', true)
                                                ->count(),
            'attente_signature' => $applyDateRange(Autorisation::whereNotNull('signature_dg'))->count(),
            'attente_validation' => $applyDateRange(Autorisation::whereHas('demande', function ($query) {
                $query->whereHas('etatDemande', function ($q) {
                    $q->where('dg_valider', false)->orWhere('dta_dg_valider', false);
                });
            }))->count(),

        ];

        // Autorisations récentes
        $recentAutorisationsQuery = Autorisation::with('demande')->orderBy('created_at', 'desc');
        $recentAutorisations = $applyDateRange($recentAutorisationsQuery)->get();

        // Demandes en attente de traitement DTA
        $demandesEnAttenteQuery = DemandeAutorisation::whereHas('etatDemande', function($query) {
                $query->where('dg_annoter', true)->orWhere('dta_dg_annoter', true)
                      ->where('dta_valider', false);
            });
        $demandesEnAttente = $applyDateRange($demandesEnAttenteQuery)->count();

        // Statistiques par type d'autorisation
        $statsParTypeQuery = DB::table('demande_autorisations as d')
            ->join('type_demande_autorisations as t', 'd.type_demande_autorisation_id', '=', 't.id')
            ->join('etat_demande_autorisations as e', 'e.demande_id', '=', 'd.id')
            ->selectRaw('
                t.libelle as type,
                COUNT(d.id) as total,
                SUM(CASE WHEN e.dg_valider = true THEN 1 ELSE 0 END) as valides,
                SUM(CASE WHEN d.date_fin > NOW()  THEN 1 ELSE 0 END) as expirees,
                SUM(CASE WHEN e.dg_valider = false THEN 1 ELSE 0 END) as en_cours
            ');
        if ($dateFrom && $dateTo) {
            $statsParTypeQuery->whereBetween('d.created_at', [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()]);
        }
        $statsParType = $statsParTypeQuery
            ->groupBy('t.libelle')
            ->orderBy('total', 'desc')
            ->get();

        return [
            'autorisations_stats' => $autorisationsStats,
            'recent_autorisations' => $recentAutorisations,
            'demandes_en_attente' => $demandesEnAttente,
            'stats_par_type' => $statsParType,
        ];
    }
    // Retourner les statistiques sous format JSON
    public function getData(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Nombre total de demandeurs
        $nombreDemandeurs = Demandeur::count();

        // Récupérer les demandes traitées et non traitées par jour
        $demandesParJourQuery = Demande::join('etat_demandes', 'demandes.id', 'etat_demandes.demande_id')->select(
            DB::raw('DATE(demandes.created_at) as date'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN etat_demandes.demandeur_cree_demande = 1 THEN 1 ELSE 0 END) as traitees'),
            DB::raw('SUM(CASE WHEN etat_demandes.demandeur_cree_demande = 0 THEN 1 ELSE 0 END) as non_traitees')
        );
        if ($dateFrom && $dateTo) {
            $demandesParJourQuery->whereBetween('demandes.created_at', [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()]);
        }
        $demandesParJour = $demandesParJourQuery
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Récupérer les demandes traitées et non traitées par mois
        $demandesParMoisQuery = Demande::join('etat_demandes', 'demandes.id', 'etat_demandes.demande_id')->select(
            DB::raw('DATE_FORMAT(demandes.created_at, "%Y-%m") as mois'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN etat_demandes.demandeur_cree_demande = 1 THEN 1 ELSE 0 END) as traitees'),
            DB::raw('SUM(CASE WHEN etat_demandes.demandeur_cree_demande = 0 THEN 1 ELSE 0 END) as non_traitees')
        );
        if ($dateFrom && $dateTo) {
            $demandesParMoisQuery->whereBetween('demandes.created_at', [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()]);
        }
        $demandesParMois = $demandesParMoisQuery
            ->groupBy('mois')
            ->orderBy('mois', 'ASC')
            ->get();

        // Récupérer les demandes traitées et non traitées par année
        $demandesParAnneeQuery = Demande::join('etat_demandes', 'demandes.id', 'etat_demandes.demande_id')->select(
            DB::raw('YEAR(demandes.created_at) as annee'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN etat_demandes.demandeur_cree_demande = 1 THEN 1 ELSE 0 END) as traitees'),
            DB::raw('SUM(CASE WHEN etat_demandes.demandeur_cree_demande = 0 THEN 1 ELSE 0 END) as non_traitees')
        );
        if ($dateFrom && $dateTo) {
            $demandesParAnneeQuery->whereBetween('demandes.created_at', [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()]);
        }
        $demandesParAnnee = $demandesParAnneeQuery
            ->groupBy('annee')
            ->orderBy('annee', 'ASC')
            ->get();

        return response()->json([
            'nombreDemandeurs' => $nombreDemandeurs,
            'demandesParJour' => $demandesParJour,
            'demandesParMois' => $demandesParMois,
            'demandesParAnnee' => $demandesParAnnee,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
