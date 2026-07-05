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
    public function index()
    {
        $dashboardData = $this->getDTADashboardData();
        $data = $this->getCommonChartData();
        return view('admin.dashboard', array_merge($data, $dashboardData));
    }
    private function getCommonChartData()
    {
        // Données pour les graphiques existants
        $nombreDemandeurs = Demandeur::count();
            
        $demandesParJour = DemandeAutorisation::selectRaw('
                DATE(created_at) as date,
                SUM(CASE WHEN statut = "validated" THEN 1 ELSE 0 END) as traitees,
                SUM(CASE WHEN statut != "validated" THEN 1 ELSE 0 END) as non_traitees
            ')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
            
        $demandesParMois = DemandeAutorisation::selectRaw('
                MONTH(created_at) as mois,
                YEAR(created_at) as annee,
                CONCAT(MONTHNAME(created_at), " ", YEAR(created_at)) as mois_annee,
                SUM(CASE WHEN statut = "validated" THEN 1 ELSE 0 END) as traitees,
                SUM(CASE WHEN statut != "validated" THEN 1 ELSE 0 END) as non_traitees
            ')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get();
            
        $demandesParAnnee = DemandeAutorisation::selectRaw('
                YEAR(created_at) as annee,
                SUM(CASE WHEN statut = "validated" THEN 1 ELSE 0 END) as traitees,
                SUM(CASE WHEN statut != "validated" THEN 1 ELSE 0 END) as non_traitees
            ')
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
    private function getDTADashboardData()
    {
        // Statistiques des autorisations
        $autorisationsStats = [
            'total' => DemandeAutorisation::count(),
            'valides' => DemandeAutorisation::join('etat_demande_autorisations','demande_autorisations.id','etat_demande_autorisations.demande_id')
                                                ->where('etat_demande_autorisations.dg_valider', true)
                                                ->orWhere('etat_demande_autorisations.dta_dg_valider', true)
                                                ->count(),
            'expirees' => Autorisation::where('date_expiration', '<', Carbon::now())->count(),

            'en_cours' => DemandeAutorisation::join('etat_demande_autorisations','demande_autorisations.id','etat_demande_autorisations.demande_id')
                                                ->where('etat_demande_autorisations.dg_annoter', true)
                                                ->orWhere('etat_demande_autorisations.dta_dg_annoter', true)
                                                ->count(),
            'attente_signature' => Autorisation::whereNotNull('signature_dg')->count(),
            'attente_validation' => Autorisation::whereHas('demande', function ($query) {
                $query->whereHas('etatDemande', function ($q) {
                    $q->where('dg_valider', false)->orWhere('dta_dg_valider', false);
                });
            })->count(),

        ];
        
        // Autorisations récentes
        $recentAutorisations = Autorisation::with('demande')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Demandes en attente de traitement DTA
        $demandesEnAttente = DemandeAutorisation::whereHas('etatDemande', function($query) {
                $query->where('dg_annoter', true)->orWhere('dta_dg_annoter', true)
                      ->where('dta_valider', false);
            })->count();
            
        // Statistiques par type d'autorisation
        $statsParType = DB::table('demande_autorisations as d')
            
            ->join('type_demande_autorisations as t', 'd.type_demande_autorisation_id', '=', 't.id')
            ->join('etat_demande_autorisations as e', 'e.demande_id', '=', 'd.id')
            ->selectRaw('
                t.libelle as type,
                COUNT(d.id) as total,
                SUM(CASE WHEN e.dg_valider = true THEN 1 ELSE 0 END) as valides,
                SUM(CASE WHEN d.date_fin > NOW()  THEN 1 ELSE 0 END) as expirees,
                SUM(CASE WHEN e.dg_valider = false THEN 1 ELSE 0 END) as en_cours
            ')
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
    public function getData()
    {
        // Nombre total de demandeurs
        $nombreDemandeurs = Demandeur::count();

        // Récupérer les demandes traitées et non traitées par jour
        $demandesParJour = Demande::join('etat_demandes', 'demandes.id', 'etat_demandes.demande_id')->select(
            DB::raw('DATE(demandes.created_at) as date'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN etat_demandes.demandeur_cree_demande = 1 THEN 1 ELSE 0 END) as traitees'),
            DB::raw('SUM(CASE WHEN etat_demandes.demandeur_cree_demande = 0 THEN 1 ELSE 0 END) as non_traitees')
        )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Récupérer les demandes traitées et non traitées par mois
        $demandesParMois = Demande::join('etat_demandes', 'demandes.id', 'etat_demandes.demande_id')->select(
            DB::raw('DATE_FORMAT(demandes.created_at, "%Y-%m") as mois'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN etat_demandes.demandeur_cree_demande = 1 THEN 1 ELSE 0 END) as traitees'),
            DB::raw('SUM(CASE WHEN etat_demandes.demandeur_cree_demande = 0 THEN 1 ELSE 0 END) as non_traitees')
        )
            ->groupBy('mois')
            ->orderBy('mois', 'ASC')
            ->get();

        // Récupérer les demandes traitées et non traitées par année
        $demandesParAnnee = Demande::join('etat_demandes', 'demandes.id', 'etat_demandes.demande_id')->select(
            DB::raw('YEAR(demandes.created_at) as annee'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN etat_demandes.demandeur_cree_demande = 1 THEN 1 ELSE 0 END) as traitees'),
            DB::raw('SUM(CASE WHEN etat_demandes.demandeur_cree_demande = 0 THEN 1 ELSE 0 END) as non_traitees')
        )
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
