<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Aeroport;
use App\Models\Avion;
use App\Models\Cachet;
use App\Models\Demande;
use App\Models\EtatDemande;
use App\Models\OrdreRecette;
use Illuminate\Http\Request;

use App\Models\Compagnie;
use App\Models\CompetenceDemandeur;
use App\Models\DemandeApprobation;
use App\Models\DemandeAutorisation;
use App\Models\Demandeur;
use App\Models\Document;
use App\Models\EmployeurDemandeur;

use App\Models\ExperienceDemandeur;
use App\Models\ExperienceMaintenanceDemandeur;
use App\Models\ExprienceMaintenanceDemandeur;
use App\Models\FormationDemandeur;
use App\Models\InterruptionDemandeur;
use App\Models\Licence;
use App\Models\Autorisation;
use Carbon\Carbon;
use App\Models\MedicalExamination;
use App\Models\PaiementAutorisation;
use App\Models\QualificationDemandeur;
use App\Models\Setting;
use App\Models\Signature;
use App\Models\TrainingDemandeur;
use App\Models\TypeAvion;
use App\Models\TypeDocumentApprobation;
use App\Models\TypeDocumentAutorisation;
use App\Models\User;
use App\Services\DtaApplicationNotificationService;
use App\Services\DtaAutorisationNotificationService;
use App\Services\LicenseApplicationNotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\WhatsAppService;

class DgDsvController extends Controller
{
    protected $dsvNotificationService;
    protected $dtaApprobationNotificationService;
    protected $dtaAutorisationNotificationService;
    protected $whatsAppService;


    public function __construct(
        LicenseApplicationNotificationService $dsvNotificationService,
        DtaApplicationNotificationService $dtaApprobationNotificationService,
        DtaAutorisationNotificationService $dtaAutorisationNotificationService,
        WhatsAppService $whatsAppService
    ) {
        $this->dsvNotificationService = $dsvNotificationService;
        $this->dtaApprobationNotificationService = $dtaApprobationNotificationService;
        $this->dtaAutorisationNotificationService = $dtaAutorisationNotificationService;
        $this->whatsAppService = $whatsAppService;
    }

    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        // Données communes pour les graphiques
        
        $dtaChartData = $this->getDtaChartData();
        $dsvChartData = $this->getDsvChartData();
        // Données spécifiques selon le rôle
        if ($role === 'dg') {
            // DG voit tout : autorisations et licences
            
            $dashboardData = $this->getDGDashboardData();
            $compagnies = $this->getCompagniesStats(); // Les stats opérateurs

            return view('dir.index', array_merge($dtaChartData,$dsvChartData, $dashboardData, [
                'compagnies' => $compagnies,
                'role' => 'dg'
            ]));
        } elseif ($role === 'dta') {
            // DTA voit les autorisations et un aperçu des licences
            $dashboardData = $this->getDTADashboardData();

            return view('dir.index', array_merge($dtaChartData, $dashboardData, [
                'role' => 'dta'
            ]));
        } elseif ($role === 'dsv') {
            // DSV voit les licences
            $dashboardData = $this->getDSVDashboardData();
            $compagnies = $this->getCompagniesStats();

            return view('dir.index', array_merge($dsvChartData, $dashboardData, [
                'compagnies' => $compagnies,
                'role' => 'dsv'
            ]));
        }

        return view('dir.index', $data);
    }

    /**
     * Données dsv pour les graphiques
     */
    private function getDsvChartData()
    {
        // Nombre de demandeurs licence
        $nombreDemandeurs = Demandeur::whereHas('user', function ($query) {
            $query->where('user_type', 'licence');
        })->count();


        // Demandes par jour
        $demandesParJour = Demande::join('etat_demandes', 'demandes.id', '=', 'etat_demandes.demande_id')
            ->selectRaw('
        DATE(demandes.created_at) as date,
        SUM(CASE WHEN etat_demandes.dg_valider = 1 THEN 1 ELSE 0 END) as traitees,
        SUM(CASE WHEN etat_demandes.dg_valider = 0 OR etat_demandes.dg_valider IS NULL THEN 1 ELSE 0 END) as non_traitees
    ')
            ->whereDate('demandes.created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy(DB::raw('DATE(demandes.created_at)'))
            ->orderBy('date')
            ->get();


        // Demandes par mois
        $demandesParMois = Demande::join('etat_demandes', 'demandes.id', '=', 'etat_demandes.demande_id')
            ->selectRaw('
        MONTH(demandes.created_at) as mois,
        YEAR(demandes.created_at) as annee,
        CONCAT(MONTHNAME(demandes.created_at)," ",YEAR(demandes.created_at)) as mois_annee,
        SUM(CASE WHEN etat_demandes.dg_valider = 1 THEN 1 ELSE 0 END) as traitees,
        SUM(CASE WHEN etat_demandes.dg_valider = 0 OR etat_demandes.dg_valider IS NULL THEN 1 ELSE 0 END) as non_traitees
    ')
            ->whereYear('demandes.created_at', Carbon::now()->year)
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get();


        // Demandes par année
        $demandesParAnnee = Demande::join('etat_demandes', 'demandes.id', '=', 'etat_demandes.demande_id')
            ->selectRaw('
        YEAR(demandes.created_at) as annee,
        SUM(CASE WHEN etat_demandes.dg_valider = 1 THEN 1 ELSE 0 END) as traitees,
        SUM(CASE WHEN etat_demandes.dg_valider = 0 OR etat_demandes.dg_valider IS NULL THEN 1 ELSE 0 END) as non_traitees
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
     * Données communes pour les graphiques
     */
    private function getDtaChartData()
    {
        // Données pour les graphiques existants
        $nombreDemandeurs = $demandeurs = Demandeur::with('user')
            ->whereHas('user', function ($query) {
                $query->where('user_type', 'autorisation');
            })
            ->count();


        $demandesParJour = DemandeAutorisation::join(
            'etat_demande_autorisations',
            'demande_autorisations.id',
            '=',
            'etat_demande_autorisations.demande_id'
        )
            ->selectRaw('
                DATE(demande_autorisations.created_at) as date,
                SUM(CASE WHEN etat_demande_autorisations.dg_valider = 1 THEN 1 ELSE 0 END) as traitees,
                SUM(CASE WHEN etat_demande_autorisations.dg_valider = 0 THEN 1 ELSE 0 END) as non_traitees
            ')
            ->whereDate('demande_autorisations.created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy(DB::raw('DATE(demande_autorisations.created_at)'))
            ->orderBy('date')
            ->get();

        $demandesParMois = DemandeAutorisation::join(
            'etat_demande_autorisations',
            'demande_autorisations.id',
            '=',
            'etat_demande_autorisations.demande_id'
        )
            ->selectRaw('
                MONTH(demande_autorisations.created_at) as mois,
                YEAR(demande_autorisations.created_at) as annee,
                CONCAT(MONTHNAME(demande_autorisations.created_at)," ",YEAR(demande_autorisations.created_at)) as mois_annee,
                SUM(CASE WHEN etat_demande_autorisations.dg_valider = 1 THEN 1 ELSE 0 END) as traitees,
                SUM(CASE WHEN etat_demande_autorisations.dg_valider = 0 THEN 1 ELSE 0 END) as non_traitees
            ')
            ->whereYear('demande_autorisations.created_at', Carbon::now()->year)
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get();


        $demandesParAnnee = DemandeAutorisation::join(
            'etat_demande_autorisations',
            'demande_autorisations.id',
            '=',
            'etat_demande_autorisations.demande_id'
        )
            ->selectRaw('
                YEAR(demande_autorisations.created_at) as annee,
                SUM(CASE WHEN etat_demande_autorisations.dg_valider = 1 THEN 1 ELSE 0 END) as traitees,
                SUM(CASE WHEN etat_demande_autorisations.dg_valider = 0 THEN 1 ELSE 0 END) as non_traitees
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
     * Dashboard DG (Direction Générale)
     */
    private function getDGDashboardData()
    {
        // Statistiques des autorisations
        $autorisationsStats = [
            'total' => DemandeAutorisation::join('etat_demande_autorisations', 'demande_autorisations.id', 'etat_demande_autorisations.demande_id')
                ->where('etat_demande_autorisations.compagnie_cree_demande', true)->count(),
            'valides' => DemandeAutorisation::join('etat_demande_autorisations', 'demande_autorisations.id', 'etat_demande_autorisations.demande_id')
                ->where('etat_demande_autorisations.dg_valider', true)
                ->orWhere('etat_demande_autorisations.dta_dg_valider', true)
                ->count(),
            'expirees' => Autorisation::where('date_expiration', '<', Carbon::now())->count(),

            'en_cours' => DemandeAutorisation::join('etat_demande_autorisations', 'demande_autorisations.id', 'etat_demande_autorisations.demande_id')
                ->where('etat_demande_autorisations.compagnie_cree_demande', true)

                ->where('dta_valider', false)
                ->count(),
            'attente_signature' => Autorisation::whereNotNull('signature_dg')->count(),
        ];

        // Autorisations récentes
        $recentAutorisations = Autorisation::with('demande')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Statistiques des licences
        $licencesStats = [
            'total_demandes' => Demande::count(),
            'total' => Licence::count(),
            'valides' => Licence::where('licence_valide', true)
                ->where('licence_bloque', false)
                ->where('date_expiration', '>', Carbon::now())
                ->count(),
            'expirees' => Licence::where(function ($query) {
                $query->where('licence_valide', false)
                    ->orWhere('licence_bloque', true)
                    ->orWhere('date_expiration', '<=', Carbon::now());
            })->count(),
            'expirant_bientot' => Licence::where('licence_valide', true)
                ->where('licence_bloque', false)
                ->whereBetween('date_expiration', [Carbon::now(), Carbon::now()->addDays(30)])
                ->count(),
            'attente_signature' => Licence::whereNull('signature_dg')->count(),
        ];

        // Licences récentes
        $recentLicences = Licence::with('demandeur')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();



        return [
            'autorisations_stats' => $autorisationsStats,
            'recent_autorisations' => $recentAutorisations,
            'licences_stats' => $licencesStats,
            'recent_licences' => $recentLicences
        ];
    }

    /**
     * Dashboard DTA (Direction des Transports Aériens)
     */
    private function getDTADashboardData()
    {
        // Statistiques des autorisations
        $autorisationsStats = [
            'total' => DemandeAutorisation::join('etat_demande_autorisations', 'demande_autorisations.id', 'etat_demande_autorisations.demande_id')
                ->where('etat_demande_autorisations.compagnie_cree_demande', true)->count(),
            'valides' => DemandeAutorisation::join('etat_demande_autorisations', 'demande_autorisations.id', 'etat_demande_autorisations.demande_id')
                ->where('etat_demande_autorisations.dg_valider', true)
                ->orWhere('etat_demande_autorisations.dta_dg_valider', true)
                ->count(),
            'expirees' => Autorisation::where('date_expiration', '<', Carbon::now())->count(),

            'en_cours' => DemandeAutorisation::join('etat_demande_autorisations', 'demande_autorisations.id', 'etat_demande_autorisations.demande_id')
                ->where('etat_demande_autorisations.compagnie_cree_demande', true)

                ->where('dta_valider', false)
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
        $demandesEnAttente = DemandeAutorisation::whereHas('etatDemande', function ($query) {
            $query->where('compagnie_cree_demande', true)
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

    /**
     * Dashboard DSV (Direction de la Sécurité Vol)
     */
    private function getDSVDashboardData()
    {
        // Statistiques des licences
        $licencesStats = [
            'total' => Licence::count(),
            'valides' => Licence::where('licence_valide', true)
                ->where('licence_bloque', false)
                ->where('date_expiration', '>', Carbon::now())
                ->count(),
            'expirees' => Licence::where(function ($query) {
                $query->where('licence_valide', false)
                    ->orWhere('licence_bloque', true)
                    ->orWhere('date_expiration', '<=', Carbon::now());
            })->count(),
            'expirant_bientot' => Licence::where('licence_valide', true)
                ->where('licence_bloque', false)
                ->whereBetween('date_expiration', [Carbon::now(), Carbon::now()->addDays(30)])
                ->count(),
            'attente_signature' => Licence::whereNull('signature_dsv')->count(),
            'attente_validation' => Licence::whereHas('demande.etatDemande', function ($query) {
                $query->where('dsv_annoter', true)
                    ->where('dsv_valider', false);
            })->count(),
        ];

        // Licences récentes
        $recentLicences = Licence::with('demandeur')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Statistiques par catégorie de licence
        $statsParCategorie = Licence::selectRaw('
                categorie_licence as categorie,
                COUNT(*) as total,
                SUM(CASE WHEN licence_valide = 1 AND licence_bloque = 0 AND date_expiration > NOW() THEN 1 ELSE 0 END) as valides
            ')
            ->groupBy('categorie_licence')
            ->orderBy('total', 'desc')
            ->get();

        // Demandes de licence en attente
        $demandesEnAttente = Demande::whereHas('etatDemande', function ($query) {
            $query->where('demandeur_cree_demande', true)
                ->where('pel_valider', false);
        })->count();

        return [
            'licences_stats' => $licencesStats,
            'recent_licences' => $recentLicences,
            'stats_par_categorie' => $statsParCategorie,
            'demandes_en_attente' => $demandesEnAttente,
        ];
    }

    /**
     * Statistiques des opérateurs (compagnies)
     */
    private function getCompagniesStats()
    {
        return DB::table('compagnies as c')
            ->select([
                'c.nom_entreprise',
                'c.id as compagnie_id',
                'c.panier as panier',
                'c.plafond as plafond',
                DB::raw('COALESCE(SUM(ord.montant), 0) as total_recettes'),
                DB::raw('CASE 
                    WHEN c.plafond > 0 THEN COALESCE(SUM(ord.montant), 0) / c.plafond * 100 
                    ELSE 0 
                END as pourcentage_plafond'),
                DB::raw('CASE 
                    WHEN COALESCE(SUM(ord.montant), 0) > c.plafond AND c.plafond > 0 
                    THEN 1 ELSE 0 
                END as depasse_plafond')
            ])
            ->leftJoin('demandeurs as d', function ($join) {
                $join->on('d.compagnie_id', '=', 'c.id')
                    ->where('d.valider_compagnie', true);
            })
            ->leftJoin('demandes as de', 'de.demandeur_id', '=', 'd.id')
            ->leftJoin('ordres_recette as ord', function ($join) {
                $join->on('ord.demande_id', '=', 'de.id')
                    ->where('ord.statut', 'Validé');
            })
            ->groupBy('c.id', 'c.nom_entreprise', 'c.panier', 'c.plafond')
            ->having('total_recettes', '>', 0)
            ->orderByDesc('total_recettes')
            ->get();
    }

    /**
     * API pour les données du dashboard
     */
    public function getDashboardData(Request $request)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        $data = [];

        if ($role === 'dg' || $role === 'dta') {
            // Statistiques des autorisations
            $data['autorisations'] = [
                'total' => Autorisation::count(),
                'valides' => Autorisation::where('statut', 'valide')->count(),
                'expirees' => Autorisation::where('statut', 'expiree')->count(),
                'attente_signature' => Autorisation::whereNull('signature_' . $role)->count(),
            ];
        }

        if ($role === 'dg' || $role === 'dsv') {
            // Statistiques des licences
            $data['licences'] = [
                'total' => Licence::count(),
                'valides' => Licence::where('licence_valide', true)
                    ->where('licence_bloque', false)
                    ->where('date_expiration', '>', Carbon::now())
                    ->count(),
                'expirant' => Licence::where('licence_valide', true)
                    ->where('licence_bloque', false)
                    ->whereBetween('date_expiration', [Carbon::now(), Carbon::now()->addDays(30)])
                    ->count(),
                'attente_signature' => Licence::whereNull('signature_' . $role)->count(),
            ];
        }

        return response()->json($data);
    }
    /**
     * Notifier les compagnies qui dépassent leur plafond
     */
    private function notifyPlafondDepasse($compagnies)
    {
        //$compagniesDepassees = $compagnies->where('depasse_plafond', 1);

        foreach ($compagnies as $compagnie) {
            // Récupérer les utilisateurs de la compagnie avec rôle 'compagnie'

            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'compagnie');
            })
                ->get();

            foreach ($users as $user) {
                if (!empty($user->whatsapp)) {
                    $this->sendPlafondNotification($compagnie, $user);
                }
            }
        }
    }

    /**
     * Envoyer la notification WhatsApp
     */
    private function sendPlafondNotification($compagnie, $user)
    {
        try {
            $message = $this->buildPlafondMessage($compagnie);

            $response = $this->whatsAppService->sendRichMessage(
                $user->whatsapp,
                $message
            );

            // Log de la notification


            return $response;
        } catch (\Exception $e) {

            return false;
        }
    }


    // Dans DirController.php
    public function getCompagnyDetails(Request $request)
    {
        $compagnieId = $request->compagnie_id;

        $details = DB::table('compagnies as c')
            ->select([
                'c.*',
                DB::raw('COUNT(DISTINCT d.id) as nombre_demandeurs'),
                DB::raw('COUNT(DISTINCT de.id) as nombre_demandes'),
                DB::raw('COALESCE(SUM(ord.montant), 0) as total_recettes'),
                DB::raw('COUNT(ord.id) as nombre_ordres'),
                DB::raw('AVG(ord.montant) as moyenne_ordre'),
                DB::raw('MAX(ord.date_ordre) as dernier_ordre')
            ])
            ->leftJoin('demandeurs as d', 'd.compagnie_id', '=', 'c.id')
            ->leftJoin('demandes as de', 'de.demandeur_id', '=', 'd.id')
            ->leftJoin('ordres_recette as ord', function ($join) {
                $join->on('ord.demande_id', '=', 'de.id')
                    ->where('ord.statut', 'Validé');
            })
            ->where('c.id', $compagnieId)
            ->groupBy('c.id')
            ->first();

        $ordres = DB::table('ordres_recette as ord')
            ->select([
                'ord.*',
                'de.code as demande_reference',
                'd.np as demandeur_nom'
            ])
            ->join('demandes as de', 'de.id', '=', 'ord.demande_id')
            ->join('demandeurs as d', 'd.id', '=', 'de.demandeur_id')
            ->where('d.compagnie_id', $compagnieId)
            ->where('ord.statut', 'Validé')
            ->where('ord.montant', '>', 0)
            ->orderBy('ord.date_ordre', 'desc')
            ->get();

        return view('dir.partials.compagny-details', compact('details', 'ordres'));
    }

    public function sendReminder(Request $request)
    {
        $compagnieId = $request->compagnie_id;

        $compagnie = DB::table('compagnies')
            ->select('*')
            ->where('id', $compagnieId)
            ->first();

        if (!$compagnie) {
            return response()->json(['error' => 'Compagnie non trouvée'], 404);
        }

        // Calculer le total des recettes
        $totalRecettes = DB::table('ordres_recette as ord')
            ->join('demandes as de', 'de.id', '=', 'ord.demande_id')
            ->join('demandeurs as d', 'd.id', '=', 'de.demandeur_id')
            ->where('d.compagnie_id', $compagnieId)
            ->where('ord.statut', 'Validé')
            ->sum('ord.montant');

        // Vérifier si le plafond est dépassé
        if ($totalRecettes > $compagnie->plafond) {
            $compagnie->total_recettes = $totalRecettes;

            $this->notifyPlafondDepasse(collect([$compagnie]));

            return response()->json(['success' => 'Rappel envoyé avec succès']);
        }

        return response()->json(['warning' => 'Le plafond n\'est pas dépassé']);
    }
    public function getData()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        $data = [];
        $dtaChartData = $this->getDtaChartData();
        $dsvChartData = $this->getDsvChartData();
        $data = $dtaChartData;
        $data = $dsvChartData;

        // Données spécifiques par rôle
        if ($role === 'dta' || $role === 'dg') {
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

            $data['stats_par_type'] = $statsParType;
        }

        if ($role === 'dsv' || $role === 'dg') {
            // Statistiques des licences par catégorie
            $licencesParCategorie = Licence::selectRaw('
                categorie_licence as categorie,
                COUNT(*) as total,
                SUM(CASE WHEN licence_valide = 1 AND licence_bloque = 0 AND date_expiration > NOW() THEN 1 ELSE 0 END) as valides,
                SUM(CASE WHEN licence_valide = 1 AND licence_bloque = 0 AND date_expiration BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as expirant_soon,
                SUM(CASE WHEN licence_valide = 0 OR licence_bloque = 1 OR date_expiration <= NOW() THEN 1 ELSE 0 END) as expirees
            ')
                ->groupBy('categorie_licence')
                ->orderBy('total', 'desc')
                ->get();

            $data['licences_par_categorie'] = $licencesParCategorie;

            // Statistiques d'évolution des licences
            $licencesEvolution = Licence::selectRaw('
                YEAR(date_deliverance) as annee,
                MONTH(date_deliverance) as mois,
                COUNT(*) as total
            ')
                ->whereYear('date_deliverance', '>=', Carbon::now()->subYear()->year)
                ->groupBy('annee', 'mois')
                ->orderBy('annee')
                ->orderBy('mois')
                ->get();

            $data['licences_evolution'] = $licencesEvolution;
        }

        return response()->json($data);
    }
    public function indexLicence()
    {
        //
        $licences = Licence::with('demandeur')->with('demande')->get();
        return view('dir.licences.index', compact('licences'));
    }
    public function indexDemandeLicence()
    {

        //
        $demandes = Demande::with('demandeur')->with('paiement')->where('status', '<>', 'En attente')->get();
        $ordres = OrdreRecette::with('demande')->get();

        return view('dir.demandeLicences.index', compact('demandes', 'ordres'));
    }

    public function indexApprobation()
    {
        $demandeApprobations = DemandeApprobation::with('compagnie')->with('user')
            ->get();
        return view('dir.demandeApprobations.index', compact('demandeApprobations'));
    }

    public function indexAutorisation()
    {
$demandeAutorisations = DemandeAutorisation::with(['type', 'user', 'etatDemande'])
    ->whereHas('etatDemande', function ($q) {
        $q->where('compagnie_cree_demande', true)
        ->orWhere('dg_rejeter', true)
                     ->orWhere('dta_rejeter', true);
    })
    ->orderBy('created_at', 'desc')
    ->get();
        $demandeAutorisations->map(function ($demande) {
            $demande->created_at_formatted = $demande->created_at
                ? date('d-m-Y', strtotime($demande->created_at))
                : 'N/A';
            $demande->created_at_sort = $demande->created_at
                ? date('Y-m-d', strtotime($demande->created_at))
                : '';

            $demande->date_soumission_formatted = $demande->date_soumission
                ? date('d-m-Y', strtotime($demande->date_soumission))
                : 'N/A';
            $demande->date_soumission_sort = $demande->date_soumission
                ? date('Y-m-d', strtotime($demande->date_soumission))
                : '';

            return $demande;
        });

        $paiements = PaiementAutorisation::with('user')->get();
        return view('dir.demandeAutorisations.index', compact('demandeAutorisations', 'paiements'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {

        $demande = Demande::find($id);

        return view('dir.demandeLicences.create', compact('demande'));
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // Stocker un nouvel ordre de recette
    public function store(Demande $demande)
    {

        $key = $demande->typeDemande->nom_fr . '-' . $demande->typeLicence->nom;

        $setting = Setting::where('key', $key)->orderBy('id', 'desc')->first();




        DB::transaction(function () use ($demande, $setting) {

            // Récupérer le dernier numéro
            $lastReference = OrdreRecette::lockForUpdate()
                ->orderByDesc('id')
                ->value('reference');

            if ($lastReference) {
                $lastNumber = intval(substr($lastReference, -4));
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            // Générer la référence OR-0001
            $reference = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            OrdreRecette::create([
                'demande_id' => $demande->id,
                'montant' => !empty($setting) ? (float) $setting->value : 0,
                'reference' => $reference,
                'date_ordre' => now()->toDateString(),
                'statut' => 'Généré'
            ]);
        });

        $etat_demande = $demande->etatDemande->update(
            [
                'dsv_recette' =>  true
            ]
        );
        $activity = Activity::log('dsv_recette',$demande->id);

        return redirect()->route('dir.demandeLicences')->with('success', 'Ordre de recette créé avec succès.');
    }

    public function showDemandeApprobation($id)
    {
        $demandeApprobation = DemandeApprobation::find($id);

        $user = Auth::user();
        $vols = !empty($demandeApprobation->vols) ? $demandeApprobation->vols : [];

        $compagnie = $demandeApprobation->compagnie;
        $avions = $demandeApprobation->avions;
        $documents = $demandeApprobation->documents;
        $itineraires = $demandeApprobation->itineraires;
        $isFullyValidated = $demandeApprobation->isFullyValidated();

        return view('dir.demandeApprobations.show', compact(
            'vols',
            'avions',
            'isFullyValidated',
            'compagnie',
            'demandeApprobation',
            'itineraires'
        ));
    }
    public function showDemandeAutorisation($id)
    {
        $demandeAutorisation = DemandeAutorisation::find($id);
        $vols = !empty($demandeAutorisation->vols) ? $demandeAutorisation->vols : [];
        $mdns = $demandeAutorisation->mdns;
        $itineraires = $demandeAutorisation->itineraires;
        $equipe_vols = $demandeAutorisation->equipe;
        $fretVols  = $demandeAutorisation->fret;
        $personnesDeces  = $demandeAutorisation->personnes;

        $receivingParties = $demandeAutorisation->receivingParties;
        $requiredDocs = [];
        if (isset($vols) && $vols->isNotEmpty()) {
            # code...
            $requiredDocs = TypeDocumentAutorisation::where('type_vol_id', $demandeAutorisation->typeVol->id)
                ->where('type_demande_autorisation_id', $demandeAutorisation->type->id)
                ->get();
        }


        $aeroports = Aeroport::all();
        $avions = $demandeAutorisation->avions;
        return view('dir.demandeAutorisations.show', compact('mdns', 'personnesDeces', 'avions', 'receivingParties', 'requiredDocs', 'demandeAutorisation', 'vols', 'itineraires', 'equipe_vols', 'fretVols', 'aeroports'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $demande = Demande::find($id);
        $demandeur = $demande->demandeur;

        //
        $formation_demandeurs = FormationDemandeur::join('demandes', 'demandes.id', 'formation_demandeurs.demande_id')
            ->join('centre_formations', 'centre_formations.id', 'formation_demandeurs.centre_formation_id')
            ->where('formation_demandeurs.demande_id', $id)
            ->select('centre_formations.libelle as centre_formation', 'formation_demandeurs.*')
            ->get();
        $qualification_demandeurs = QualificationDemandeur::join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
            ->join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
            ->join('centre_formations', 'centre_formations.id', 'qualification_demandeurs.centre_formation_id')
            ->where('qualification_demandeurs.demande_id', $id)
            ->select('centre_formations.libelle as centre_formation', 'qualifications.libelle as qualification', 'qualification_demandeurs.*')
            ->get();
        $medical_examinations = MedicalExamination::join('demandes', 'demandes.id', 'medical_examinations.demande_id')
            ->join('centre_medicals', 'centre_medicals.id', 'medical_examinations.centre_medical_id')
            ->where('medical_examinations.demande_id', $id)
            ->select('centre_medicals.libelle as centre_medical', 'medical_examinations.*')
            ->get();
        $experience_demandeurs = ExperienceDemandeur::join('demandes', 'demandes.id', 'experience_demandeurs.demande_id')
            ->where('experience_demandeurs.demande_id', $id)
            ->select('experience_demandeurs.*')
            ->get();


        $competence_demandeurs = CompetenceDemandeur::join('demandes', 'demandes.id', 'competence_demandeurs.demande_id')
            ->join('centre_formations', 'centre_formations.id', 'competence_demandeurs.centre_formation_id')
            ->where('competence_demandeurs.demande_id', $id)
            ->select('centre_formations.libelle as centre_formation', 'competence_demandeurs.*')
            ->get();


        $entrainement_demandeurs = TrainingDemandeur::join('demandes', 'demandes.id', 'training_demandeurs.demande_id')
            ->join('centre_formations', 'centre_formations.id', 'training_demandeurs.centre_formation_id')
            ->where('training_demandeurs.demande_id', $id)
            ->select('centre_formations.libelle as centre_formation', 'training_demandeurs.*')
            ->get();
        $interruption_demandeurs = InterruptionDemandeur::join('demandes', 'demandes.id', 'interruption_demandeurs.demande_id')
            ->where('interruption_demandeurs.demande_id', $id)
            ->select('interruption_demandeurs.*')
            ->get();
        $experience_maintenance_demandeurs = ExperienceMaintenanceDemandeur::join('demandes', 'demandes.id', 'experience_maintenance_demandeurs.demande_id')
            ->where('experience_maintenance_demandeurs.demande_id', $id)
            ->select('experience_maintenance_demandeurs.*')
            ->get();
        $employeur_demandeurs = EmployeurDemandeur::join('demandes', 'demandes.id', 'employeur_demandeurs.demande_id')
            ->where('employeur_demandeurs.demande_id', $id)
            ->select('employeur_demandeurs.*')
            ->get();
        $documents = Document::join('demandes', 'demandes.id', 'documents.demande_id')
            ->join('type_documents', 'type_documents.id', 'documents.type_document_id')
            ->where('documents.demande_id', $id)
            ->select('type_documents.*', 'documents.*')
            ->get();




        return view('dir.demandeLicences.show', compact('demande', 'demandeur', 'employeur_demandeurs', 'experience_maintenance_demandeurs', 'interruption_demandeurs', 'formation_demandeurs', 'documents', 'entrainement_demandeurs', 'competence_demandeurs', 'experience_demandeurs', 'medical_examinations', 'qualification_demandeurs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print(OrdreRecette $ordre)
    {

        //
        $dsv = User::role('dsv')
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->first();
        $pel = User::permission('manage-dsv')
            ->role('admin')

            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->first();

        return view('dir.demandeLicences.ordre', compact('ordre', 'dsv', 'pel'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OrdreRecette $ordre)
    {
        //
        $request->validate([
            'montant' => 'required|numeric|min:0',
            'date_ordre' => 'required|date',
            'ordre'  =>  'required|file'
        ]);

        if ($request->hasFile('ordre')) {
            $ordrePath = $request->file('ordre')->store('paiements', 'public');
        } else {
            $ordrePath = null;
        }
        $or = $ordre->update([
            'montant' => $request->montant,
            'date_ordre' => $request->date_ordre,
            'ordre' => $ordrePath
        ]);

        return redirect()->route('dir.demandeLicences')->with('success', 'Ordre de recette mis à jour avec succès.');
    }

    public function delete(Request $request)
    {
        $type = $request->type;
        $id = $request->id;

        try {
            if ($type === 'signature') {
                $record = Signature::find($id);
                if ($record) {
                    // Delete the file first
                    if (Storage::exists('uploads/' . $record->signature)) {
                        Storage::delete('uploads/' . $record->signature);
                    }
                    $record->delete();
                }
            } elseif ($type === 'cachet') {
                $record = Cachet::find($id);
                if ($record) {
                    // Delete the file first
                    if (Storage::exists('uploads/' . $record->cachet)) {
                        Storage::delete('uploads/' . $record->cachet);
                    }
                    $record->delete();
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false]);
        }
    }
    function sc()
    {
        $signature  =  Auth::user()->signature;
        $cachet  =  Auth::user()->cachet;
        return view('dir.signature', compact('signature', 'cachet'));
    }
    public function store_sc(Request $request)
    {
        //
        $request->validate([
            'cachet' => 'required|file',
            'signature'  =>  'required|file'
        ]);
        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('sc', 'public');
        } else {
            $signaturePath = null;
        }
        if ($request->hasFile('cachet')) {
            $cachetPath = $request->file('cachet')->store('sc', 'public');
        } else {
            $cachetPath = null;
        }
        $c = Cachet::create([
            'user_id' => auth()->user()->id,
            'cachet' => $cachetPath
        ]);
        $s = Signature::create([
            'user_id' => auth()->user()->id,
            'nom' => $request->signatory_name,
            'signature' => $signaturePath
        ]);

        return redirect()->route('dsv')->with('success', 'Signature et Cache cree avec succès.');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrdreRecette $ordre)
    {
    if ($ordre->demande) {
        $ordre->demande->etatDemande->update([
            'dsv_recette' => false
        ]);
    }

        $ordre->delete();
        return redirect()->route('dir.demandeLicences')->with('success', 'Ordre de  recette supprimé.');
        //
    }

    function valider(OrdreRecette $ordre)
    {
        $demande = $ordre->demande;

        $ordre = $ordre->update(
            [
                'statut' => 'Validé'
            ]
        );
        $dsv = User::role('dsv')
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->latest()->first();
        $daf = User::role('daf')
            ->latest()->first();
        if (!empty($daf->whatsapp)) {
            $this->dsvNotificationService->sendValidationConfirmation(
                applicationNumber: $demande->code,
                applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                recipientPhone: $daf->whatsapp,
                recipientRole: 'DAF',
                validatorRole: 'DSV',
                applicantName: $demande->demandeur->np,
                nextSteps: [
                    'Facturation requise de votre part',
                    'Validation requise pour la facture émise',
                ]
            );
        }

        return back()->with('success', 'Ordre de  recette validée avec succès.');
    }


    function validerDsv($id)
    {

        $demande = Demande::find($id);
        $etat_demande = $demande->etatDemande->update(
            [
                'dsv_valider' => true
            ]
        );
        $activity = Activity::log('dsv_valider',$demande->id);
        $dg = User::role('dg')
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->latest()->first();
        if (!empty($dg->whatsapp)) {
            $this->dsvNotificationService->sendValidationConfirmation(
                applicationNumber: $demande->code,
                applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                recipientPhone: $dg->whatsapp,
                recipientRole: 'DG',
                validatorRole: 'DSV',
                applicantName: $demande->demandeur->np,
                nextSteps: [
                    'Validation requise de votre part',
                ]
            );
        }
        return back()->with('success', 'Demande validée avec succès.');
    }

    function annoterDemandeDSVtoPEL($id)
    {

        $demande = Demande::find($id);
        $etat_demande = $demande->etatDemande->update(
            [
                'dsv_annoter' => true
            ]
        );
        $activity = Activity::log('dsv_annoter',$demande->id);
        $pel = User::role('admin')
            ->whereHas('permissions', function ($q) {
                $q->where('name', 'menage-dsv');
            })
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->latest()->first();
        if (!empty($pel->whatsapp)) {

            $this->dsvNotificationService->sendApplicationActionRequired(
                demandeNumber: $demande->code,
                demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                recipientRole: 'Chef Service PEL',
                recipientPhone: $pel->whatsapp,
                actionType: 'annotation',
                applicantName: $demande->demandeur->np,
            );
        }
        return back()->with('success', 'Demande annotée avec succès.');
    }





    function validerDg($id)
    {

        $demande = Demande::find($id);
        $etat_demande = $demande->etatDemande->update(
            [
                'dg_valider' => true,
                'dsv_dg_valider' => Auth::user()->hasRole('dsv')

            ]
        );
        $activity = Activity::log('dg_valider',$demande->id);
        $dg = User::role('dg')
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->latest()->first();
        if (!empty($dg->whatsapp)) {
            $this->dsvNotificationService->sendValidationConfirmation(
                applicationNumber: $demande->code,
                applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                recipientPhone: $dg->whatsapp,
                recipientRole: 'DG',
                validatorRole: 'DSV',
                applicantName: $demande->demandeur->np,
                nextSteps: [
                    'Validation requise de votre part',
                ]
            );
        }

        return back()->with('success', 'Demande validée avec succès.');
    }
    function annoterDemandeDGtoDSV($id)
    {
        $demande = Demande::find($id);
        $etat_demande = $demande->etatDemande->update(
            [
                'dg_annoter' => true,
                'dsv_dg_annoter' => Auth::user()->hasRole('dsv')

            ]
        );
        $activity = Activity::log('dg_annoter',$demande->id);
        $dsv = User::role('dsv')
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->latest()->first();
        if (!empty($dsv->whatsapp)) {
            $this->dsvNotificationService->sendApplicationActionRequired(
                demandeNumber: $demande->code,
                demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                recipientRole: 'DSV',
                recipientPhone: $dsv->whatsapp,
                actionType: 'annotation',
                applicantName: $demande->demandeur->np,
            );
        }

        return back()->with('success', 'Demande annotée avec succès.');
    }
    function rejeter(Request $request, $id)
    {


        $motif = $request->input('motif');
        $table = $request->input('table');
        switch ($table) {
            case 'demandes': {
                    $demande = Demande::find($id);
                    if (Auth::user()->hasRole('dsv')) {
                        # code...
                        $demande->update(
                            [
                                'motif_dsv' => $motif
                            ]
                        );
                        $etat_demande = $demande->etatDemande->update(
                            [
                                'dsv_rejeter' => true
                            ]
                        );
                        $activity = Activity::log('dsv_rejeter',$demande->id);
                        return back()->with('success', 'Demande rejetée avec succès.');
                    } else if (Auth::user()->hasRole('dg')) {

                        $demande->update(
                            [
                                'motif_dg' => $motif
                            ]
                        );
                        $etat_demande =  $demande->etatDemande->update(
                            [
                                'dg_rejeter' => true,
                                'dsv_dg_rejeter' => Auth::user()->hasRole('dsv')
                            ]
                        );
                        $activity = Activity::log('dg_rejeter',$demande->id);
                        $state = $demande->etatDemande;
                        $demande->update(['mise_a_jour' => true]);
                        if ($state) {
                            $state->resetAllApprovalStates();
                            $state->update(
                                [
                                    'demandeur_cree_demande' => false,
                                ]

                            );
                        }
                    }
                    if ($demande->demandeur->user && !empty($demande->demandeur->user->whatsapp)) {
                        $this->dsvNotificationService->sendApplicationActionRequired(
                            demandeNumber: $demande->code,
                            demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientRole: 'DEMANDEUR',
                            recipientPhone: $demande->demandeur->user->whatsapp,
                            actionType: 'rejection',
                            applicantName: $demande->demandeur->np,
                        );
                    }
                    return back()->with('success', 'Demande rejetée avec succès.');
                }
                # code...
                break;
            case 'demande_autorisations': {
                    $demande = DemandeAutorisation::find($id);
                    if (Auth::user()->hasRole('dta')) {
                        # code...
                        $demande->update(
                            [
                                'dta_motif' => $motif,
                                'date_soumission' => null,
                                'directions_annotees' => null
                            ]
                        );
                        $etat_demande = $demande->etatDemande->update(
                            [
                                'dta_rejeter' => true
                            ]
                        );
                        $activity = Activity::log('dta_rejeter',$demande->id);
                    } else if (Auth::user()->hasRole('dg')) {

                        $demande->update(
                            [
                                'dg_motif' => $motif,
                                'date_soumission' => null,
                                'directions_annotees' => null
                            ]
                        );
                        $etat_demande =  $demande->etatDemande->update(
                            [
                                'dg_rejeter' => true
                            ]
                        );
                        $activity = Activity::log('dg_rejeter',$demande->id);
                    }
                    $recipientUser = $demande->user;

                    $this->dtaAutorisationNotificationService->sendRejectionNotification(
                        $demande,
                        $recipientUser,
                        'DTA',
                        [$motif]
                    );
                    $state = $demande->etatDemande;
                    $demande->update(['mise_a_jour' => true]);
                    if ($state) {
                        $state->resetAllApprovalStates();
                        $state->update([
                            'compagnie_cree_demande' => false
                        ]);
                    }
                    return back()->with('success', 'Demande rejetée avec succès.');
                }
                # code...
                break;
            case 'demande_approbations': {
                    $demande = DemandeApprobation::find($id);
                    if (Auth::user()->hasRole('dta')) {
                        # code...
                        $demande->update(
                            [
                                'dta_motif' => $motif
                            ]
                        );
                        $etat_demande = $demande->etatDemande->update(
                            [
                                'dta_rejeter' => true
                            ]
                        );
                        $activity = Activity::log('dta_rejeter',$demande->id);
                    } else if (Auth::user()->hasRole('dg')) {

                        $demande->update(
                            [
                                'dg_motif' => $motif
                            ]
                        );
                        $etat_demande =  $demande->etatDemande->update(
                            [
                                'dg_rejeter' => true
                            ]
                        );
                        $activity = Activity::log('dg_rejeter',$demande->id);
                    }
                    $recipientUser = $demande->user;

                    $this->dtaApprobationNotificationService->sendRejectionNotification(
                        $demande,
                        $recipientUser,
                        'DTA'
                    );
                    $state = $demande->etatDemande;
                    if ($state) {
                        $state->resetAllApprovalStates();
                    }
                    return back()->with('success', 'Demande rejetée avec succès.');
                }
                # code...
                break;
            default:
                # code...
                return back()->with('error', 'Erreur.');
                break;
        }
    }

    function achiever(Request $request, $id)
    {
        $motif = $request->input('motif');
        $table = $request->input('table');
        switch ($table) {
            case 'demandes': {
                    $demande = Demande::find($id);
                    if (Auth::user()->hasRole('dsv')) {
                        # code...
                        $demande->update(
                            [
                                'motif_dsv' => $motif
                            ]
                        );
                        $etat_demande = $demande->etatDemande->update(
                            [
                                'dsv_rejeter' => true
                            ]
                        );
                        $activity = Activity::log('dsv_rejeter',$demande->id);
                    } else if (Auth::user()->hasRole('dg')) {
                        $demande->update(
                            [
                                'motif_dg' => $motif
                            ]
                        );
                        $etat_demande =  $demande->etatDemande->update(
                            [
                                'dg_rejeter' => true,
                                'dsv_dg_rejeter' => Auth::user()->hasRole('dsv')
                            ]
                        );
                        $activity = Activity::log('dg_rejeter',$demande->id);

                    }
                    $state = $demande->etatDemande;
                        if ($state) {
                            $state->resetAllApprovalStates();
                        }
                    return back()->with('success', 'Demande rejetée avec succès.');
                }
                # code...
                break;
            case 'demande_autorisations': {
                    $demande = DemandeAutorisation::find($id);
                    if (Auth::user()->hasRole('dsv')) {
                        # code...
                        $demande->update(
                            [
                                'dsv_motif' => $motif,
                                //'date_soumission' => null,
                                //'directions_annotees' => null
                            ]
                        );
                        $activity = Activity::log('dsv_achieve',$demande->id);
                    } else if (Auth::user()->hasRole('dsna')) {
                        $demande->update(
                            [
                                'dsna_motif' => $motif,
                                //'date_soumission' => null,
                                //'directions_annotees' => null
                            ]
                        );
                        $activity = Activity::log('dsna_achieve',$demande->id);
                    } else if (Auth::user()->hasRole('dsad')) {
                        # code...
                        $demande->update(
                            [
                                'dsad_motif' => $motif,
                                //'date_soumission' => null,
                                //'directions_annotees' => null
                            ]
                        );
                        $activity = Activity::log('dsad_achieve',$demande->id);
                    }

                    $roleName = Auth::user()->roles->pluck('name')->first();
                    $dta = User::role('dta')
                        ->whereHas('signature', function ($q) {
                            $q->whereNotNull('signature');
                        })
                        ->latest()->first();

                    $this->dtaAutorisationNotificationService->sendRejectionNotification(
                        $demande,
                        $dta,
                        $roleName,
                        [$motif]
                    );
                   
                    return back()->with('success', 'Demande rejetée avec succès.');
                }
                # code...
                break;
            case 'demande_approbations': {
                    $demande = DemandeApprobation::find($id);
                    if (Auth::user()->hasRole('dsv')) {
                        # code...
                        $demande->update(
                            [
                                'dsv_motif' => $motif
                            ]
                        );
                    } else if (Auth::user()->hasRole('dsna')) {
                        $demande->update(
                            [
                                'dsna_motif' => $motif
                            ]
                        );
                    } else if (Auth::user()->hasRole('dsad')) {
                        # code...
                        $demande->update(
                            [
                                'dsad_motif' => $motif
                            ]
                        );
                    }


                    $recipientUser = $demande->user;

                    $this->dtaApprobationNotificationService->sendRejectionNotification(
                        $demande,
                        $recipientUser,
                        'DTA'
                    );
                    $state = $demande->etatDemande;
                    if ($state) {
                        $state->resetAllApprovalStates();
                    }
                    return back()->with('success', 'Demande d\'achievement ajoutee avec succès.');
                }
                # code...
                break;

            default:
                # code...
                return back()->with('error', 'Erreur.');
                break;
        }
    }

    function signerDsv($id)
    {

        $demande = Demande::find($id);
        $etat_demande = $demande->etatDemande->update(
            [
                'dsv_signer' => true
            ]
        );
        $activity = Activity::log('dsv_signer',$demande->id);
        $pel = User::role('admin')
            ->whereHas('permissions', function ($q) {
                $q->where('name', 'menage-dsv');
            })
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->latest()->first();
        if (!empty($pel->whatsapp)) {
            $this->dsvNotificationService->sendApplicationActionRequired(
                demandeNumber: $demande->code,
                demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                recipientRole: 'Service PEL',
                recipientPhone: $pel->whatsapp,
                actionType: 'signed',
                applicantName: $demande->demandeur->np,
            );
        }
        return back()->with('success', 'Demande signée avec succès.');
    }
    function signerDg($id)
    {

        $demande = Demande::find($id);
        $etat_demande = $demande->etatDemande->update(
            [
                'dg_signer' => true,
                'dsv_dg_signer' => Auth::user()->hasRole('dsv')
            ]
        );
        $activity = Activity::log('dg_signer',$demande->id);
        $dsv = User::role('dsv')
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->latest()->first();
        if (!empty($dsv->whatsapp)) {
            $this->dsvNotificationService->sendApplicationActionRequired(
                demandeNumber: $demande->code,
                demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                recipientRole: 'DSV',
                recipientPhone: $dsv->whatsapp,
                actionType: 'signed',
                applicantName: $demande->demandeur->np,
            );
        }
        # code...
        return back()->with('success', 'Demande signée avec succès.');
    }
    function updateComments(Request $request, DemandeAutorisation $demandeAutorisation)
    {
        if (Auth::user()->hasRole('dsv')) {
            # code...
            $demandeAutorisation->update(
                [
                    'dsv_motif' => $request->commentaires
                ]
            );
        } elseif (Auth::user()->hasRole('dsna')) {
            $demandeAutorisation->update(
                [
                    'dsna_motif' => $request->commentaires
                ]
            );
            # code...
        } elseif (Auth::user()->hasRole('dsad')) {
            $demandeAutorisation->update(
                [
                    'dsad_motif' => $request->commentaires
                ]
            );
            # code...
        }

        return back()->with('success', 'Demande mis a jour avec succès.');
    }
    /**
     * Construire le message WhatsApp
     */
    private function buildPlafondMessage($compagnie): string
    {
        $depassement = $compagnie->total_recettes - $compagnie->plafond;
        $pourcentage = round($compagnie->pourcentage_plafond, 2);

        return <<<MSG
        ⚠️ *ALERTE PLAFOND DÉPASSÉ* ⚠️

        *Compagnie:* {$compagnie->nom_entreprise}
        
        📊 *Statistiques:*
        • Plafond autorisé: {$compagnie->plafond} MRU
        • Total recettes: {$compagnie->total_recettes} MRU
        • Dépassement: {$depassement} MRU
        • Pourcentage: {$pourcentage}%
        
        ⚡ *Action requise:*
        Votre opérateur a dépassé le plafond autorisé de {$compagnie->plafond} MRU.
        Veuillez contacter le DAF pour régulariser votre situation.
        
        📞 *Contact:* DAF
        
        Cordialement,
        DAF
        MSG;
    }
}
