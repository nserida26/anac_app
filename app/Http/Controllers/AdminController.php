<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Approbation;
use App\Models\Autorisation;
use App\Models\Avion;
use Illuminate\Http\Request;

use App\Models\TypeDemande;
use App\Models\Demande;
use App\Models\DemandePiece;
use App\Models\CarteStagiare;
use App\Models\ChecklistDemande;
use Illuminate\Support\Facades\Validator;
use App\Models\ExaminateurCentre;
use App\Models\CentreFormation;

use App\Models\Validation;

use App\Models\Checklist;
use Carbon\Carbon;
use App\Models\CompetenceDemandeur;
use App\Models\DemandeApprobation;
use App\Models\DemandeAutorisation;
use App\Models\Document;
use App\Models\DocumentAutorisation;
use App\Models\EmployeurDemandeur;
use App\Models\EquipeVol;
use App\Models\EtatDemande;
use App\Models\Cachet;

use App\Models\ExperienceDemandeur;
use App\Models\ExperienceMaintenanceDemandeur;
use App\Models\ExprienceMaintenanceDemandeur;
use App\Models\FormationDemandeur;
use App\Models\FretVol;
use App\Models\InterruptionDemandeur;
use App\Models\Itineraire;
use App\Models\ItineraireVol;
use App\Models\Licence;
use App\Models\MedicalExamination;
use App\Models\Paiement;
use App\Models\QualificationDemandeur;
use App\Models\ReceivingParty;
use App\Models\Signature;
use App\Models\TrainingDemandeur;
use App\Models\Demandeur;
use App\Models\TypeAvion;
use App\Models\TypeDocumentAutorisation;
use App\Models\User;
use App\Models\ValidationLicence;
use App\Models\Vol;
use App\Models\VolApprobation;
use App\Services\DtaApplicationNotificationService;
use App\Services\DtaAutorisationNotificationService;
use App\Services\LicenseApplicationNotificationService;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{

    protected $dsvNotificationService;
    protected $dtaNotificationService;
    protected $dtaAutorisationNotificationService;

    public function __construct(
        LicenseApplicationNotificationService $dsvNotificationService,
        DtaApplicationNotificationService $dtaNotificationService,
        DtaAutorisationNotificationService $dtaAutorisationNotificationService,
    ) {
        $this->dsvNotificationService = $dsvNotificationService;
        $this->dtaNotificationService = $dtaNotificationService;
        $this->dtaAutorisationNotificationService = $dtaAutorisationNotificationService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $demandes = Demande::with('demandeur')->where('status', '<>', 'En attente')->get();
        $typesDemandes = TypeDemande::all();
        return view('admin.demandeLicences.index', compact('demandes','typesDemandes'));
    }
    
    public function indexDemandeur()
    {
        //
        $demandeurs = Demandeur::with('licence')->get();
        return view('admin.demandeurs.index', compact('demandeurs'));
    }


    public function index_vr()
    {
        //
        $demandeApprobations = DemandeApprobation::with('compagnie')->with('user')
            //->where('statut', '<>', 'EN_ATTENTE')
            ->get();

        return view('admin.demandeApprobations.index', compact('demandeApprobations'));
    }
    public function show_vr($id)
    {
        $demandeApprobation = DemandeApprobation::find($id);
        $user = Auth::user();
        $vols = !empty($demandeApprobation->vols) ? $demandeApprobation->vols : [];
        $compagnie = $demandeApprobation->compagnie;
        $avions = $demandeApprobation->avions;
        $documents = $demandeApprobation->documents;
        $itineraires = $demandeApprobation->itineraires;




        return view('admin.demandeApprobations.show', compact(
            'vols',
            'avions',
            'compagnie',
            'demandeApprobation',
            'itineraires'
        ));
    }
    public function index_vi()
    {
        //
        $demandeAutorisations = DemandeAutorisation::with('type')->with('user')
            ->where('statut', '<>', 'on_hold')
            ->orderBy('date_soumission', 'desc')
            ->get();

        return view('admin.demandeAutorisations.index', compact('demandeAutorisations'));
    }

    function sc()
    {
        $signature  =  Auth::user()->signature;
        $cachet  =  Auth::user()->cachet;

        return view('admin.signature', compact('signature','cachet'));
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
            'nom' => $request->signatory_name,
            'user_id' => auth()->user()->id,
            'signature' => $signaturePath
        ]);

        return redirect()->route('admin.sc')->with('success', 'Signature cree avec succès.');
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
    public function show_vi($id)
    {
        //
        $demandeAutorisation = DemandeAutorisation::find($id);
        $vols = $demandeAutorisation->vols;

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

        $avions = $demandeAutorisation->avions;

        return view('admin.demandeAutorisations.show', compact('personnesDeces','avions', 'receivingParties', 'demandeAutorisation', 'vols', 'itineraires', 'equipe_vols', 'fretVols'));
    }

    public function approbations()
    {
        $approbations = Approbation::all();
        return view('admin.approbations.index', compact('approbations'));

        //

    }
    public function showApprobation(Approbation $approbation)
    {
        //

        return view('admin.approbations.show', compact('approbation'));
    }
    public function printApprobation(Approbation $approbation)
    {
        $dg = User::role('dg')->first();
        $demande = $approbation->demande;
        return view('admin.approbations.print', compact('approbation', 'dg', 'demande'));
    }

    public function print(Autorisation $autorisation)
    {
        
        return view('admin.autorisations.print', compact('autorisation'));
        
    }

    public function autorisations()
    {
        $autorisations = Autorisation::whereHas('demande')
        ->orderBy('created_at', 'desc')
        ->get();
        return view('admin.autorisations.index', compact('autorisations'));

        //

    }
    public function showAutorisation(Autorisation $autorisation)
    {
        //

        return view('admin.autorisations.show', compact('autorisation'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function licences(Request $request)
    {
        $query = Licence::with(['demandeur', 'demande']);
        
        // Filter by license type
        if ($request->has('type') && $request->type && $request->type !== 'all') {
            $query->byType($request->type);
        }
        
        // Filter by expiry status
        if ($request->has('status') && $request->status) {
            switch ($request->status) {
                case 'expired':
                    $query->expired();
                    break;
                case 'expiring_soon':
                    $query->expiringSoon(15);
                    break;
                case 'valid':
                    $query->valid();
                    break;
            }
        }
        
        $licences = $query->orderBy('date_expiration', 'asc')->get();
        
        // Get distinct license types for filter dropdown
        $licenseTypes = Licence::getDistinctTypes();
        
        return view('admin.licences.index', compact('licences', 'licenseTypes'));

        //
    }
    
    // Method to send manual notification for a specific licence
    public function sendExpiryNotification(Licence $licence)
    {
        try {
            $result = $licence->sendExpiryNotification();
            
            
            if ($result) {
                return redirect()->back()->with('success', __('trans.notification_sent_successfully'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    // Method to send notifications for all expiring licences
    public function sendAllExpiryNotifications()
    {
        try {
            $expiringLicences = Licence::valid()
                ->whereBetween('date_expiration', [now(), now()->addDays(15)])
                ->get();
            
            $sentCount = 0;
            $failedCount = 0;
            
            foreach ($expiringLicences as $licence) {
                if ($licence->sendExpiryNotification()) {
                    $sentCount++;
                } else {
                    $failedCount++;
                }
            }
            
            return redirect()->back()->with('success', 
                __('trans.notifications_sent', ['sent' => $sentCount, 'failed' => $failedCount])
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
        public function carteStagiares()
    {
        $cartes  = CarteStagiare::all();

        return view('admin.stagiares.index', compact('cartes'));

        //
    }
     
    
        public function validations()
    {
        $validations = ValidationLicence::all();
        

        return view('admin.validations.index', compact('validations'));

        //
    }

    public function showLicence(Licence $licence)
    {
        //

        return view('admin.licences.show', compact('licence'));
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
        $demande = Demande::find($id);
        $demandeur = $demande->demandeur;
        $examens = $demande->demandeur->examens;
        $formations = $demande->demandeur->formations;
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
    // Get checklists based on demande's type_demande_id and type_licence_id
$checklists = Checklist::where('type_demande_id', $demande->type_demande_id)
    ->where('type_licence_id', $demande->type_licence_id)
    ->orderBy('section')
    ->orderBy('ordre')
    ->get()
    ->groupBy('section');

    
    
    // Get existing responses
    $reponses = ChecklistDemande::where('demande_id', $demande->id)
        ->get()
        ->keyBy('checklist_id');
            
        return view('admin.demandeLicences.show', compact('reponses','checklists','examens', 'formations', 'demande', 'demandeur', 'employeur_demandeurs', 'experience_maintenance_demandeurs', 'interruption_demandeurs', 'formation_demandeurs', 'documents', 'entrainement_demandeurs', 'competence_demandeurs', 'experience_demandeurs', 'medical_examinations', 'qualification_demandeurs'));
    }
    
    public function showDemandeur($id){
        
        $demandeur = Demandeur::find($id);
        
        return view('admin.demandeurs.show', compact('demandeur'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approveAllStates($id)
    {

        try {
            $demande = Demande::find($id);
            $demande->etatDemande->approveAllStates();

            $paiement = Paiement::create([
                'demande_id' => $demande->id,
                'montant' => 0,
                'statut' => 'Payé',
                'date_paiement' => now(),

            ]);
            return response()->json([
                'success' => true,
                'message' => 'All states approved successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function updateLicence(Request $request, Licence $licence)
    {

        $licence->update([
            'date_deliverance' => isset($request->date_deliverance) ? $request->date_deliverance :  $licence->date_deliverance,
            'date_mise_a_jour' => isset($request->date_mise_a_jour) ? $request->date_mise_a_jour :  $licence->date_mise_a_jour,
            'date_expiration' => isset($request->date_expiration) ? $request->date_expiration :  $licence->date_expiration,
            'numero_licence' => isset($request->numero_licence) ? $request->numero_licence :  $licence->numero_licence,
        ]);

        return redirect()->route('licences')->with('success', 'Licence mis à jour.');

        //
    }
    // Dans LicenceController.php

public function updateCalculation(Request $request, $id)
{
    
    try {
        $request->validate([
            'type_calcul' => 'required|in:none,jours,fin_mois',
            'jours_supplementaires' => 'required_if:type_calcul,jours|nullable|integer|min:1|max:365'
        ]);

        $licence = Licence::findOrFail($id);
        
        $licence->type_calcul = $request->type_calcul;
        
        if ($request->type_calcul == 'jours') {
            $licence->jours_supplementaires = $request->jours_supplementaires;
        } else {
            $licence->jours_supplementaires = null;
        }
        
        $licence->save();

        return response()->json([
            'success' => true,
            'message' => __('trans.calculation_settings_updated_success')
        ]);

        
    } catch (\Exception $e) {
return response()->json([
    'success' => false,
    'message' => __('trans.error_updating_calculation_settings')
], 400);
    }
}


    public function imprimerAuth($id)
    {
        $demande  = Demande::find($id);

        $demandeur = $demande->demandeur;
        $licence = $demande->licence;


        if ($licence->licence_valide) {
            # code...
            $medical_certificat = $demande->medicalExaminations()->orderByDesc('date_examen')->first();
            
            $qualification_ulm = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->where('qualifications.libelle', 'Qualification de Class')
                ->where('demandes.id', $id)
                ->whereNotNull('qualification_demandeurs.ulm')
                ->select('qualification_demandeurs.ulm', 'qualification_demandeurs.date_examen')
                ->orderByDesc('qualification_demandeurs.id')
                ->first();
            $qualification_rpas = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->where('qualifications.libelle', 'Qualification Type Machine')
                ->where('demandes.id', $id)
                ->whereNotNull('qualification_demandeurs.rpa')
                ->select('qualification_demandeurs.rpa', 'qualification_demandeurs.date_examen')
                ->orderByDesc('qualification_demandeurs.id')
                ->get();
            $qualification_atcs = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->where('qualifications.libelle', 'Qualifications ATC')
                ->where('demandes.id', $id)
                ->select('qualification_demandeurs.atc', 'qualification_demandeurs.date_examen')
                ->orderByDesc('qualification_demandeurs.id')
                ->get();
            $qualification_amts = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->where('qualifications.libelle', 'Qualifications AMT')
                ->where('demandes.id', $id)
                ->select('qualification_demandeurs.amt', 'qualification_demandeurs.date_examen')
                ->orderByDesc('qualification_demandeurs.id')
                ->get();

            $qualification_types = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->leftJoin('type_avions', 'type_avions.id', 'qualification_demandeurs.type_avion_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->select('type_avions.code', 'qualification_demandeurs.date_examen')
                ->where('qualifications.libelle', 'Qualification Type Machine')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.id')
                ->get();
            $qualification_ifr = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->select('qualification_demandeurs.date_examen')
                ->where('qualifications.libelle', 'Qualification IFR')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.date_examen')
                ->first();

            $qualification_classe = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->select('qualification_demandeurs.date_examen', 'qualification_demandeurs.type_moteur')
                ->where('qualifications.libelle', 'Qualification de Class')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.date_examen')
                ->first();
            $qualification_instructeur = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->leftJoin('type_avions', 'type_avions.id', 'qualification_demandeurs.type_avion_id')
                ->select('qualification_demandeurs.date_examen', 'qualification_demandeurs.machine', 'type_avions.code', 'qualification_demandeurs.type_privilege')
                ->where('qualifications.libelle', 'Qualification instructeur')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.id')
                ->get();
            $qualification_examinateur = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->leftJoin('type_avions', 'type_avions.id', 'qualification_demandeurs.type_avion_id')
                ->select('qualification_demandeurs.date_examen', 'qualification_demandeurs.machine', 'type_avions.code', 'qualification_demandeurs.type_privilege')
                ->where('qualifications.libelle', 'Autorisation examinateur')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.id')
                ->get();
            $competence_demandeur = CompetenceDemandeur::join('demandes', 'demandes.id', 'competence_demandeurs.demande_id')
                ->select('competence_demandeurs.date', 'competence_demandeurs.validite', 'competence_demandeurs.niveau')
                ->where('competence_demandeurs.type', 'Contrôle de compétence linguistique')
                ->where('demandes.id', $id)
                ->orderByDesc('competence_demandeurs.date')
                ->first();
                
            



            return view('admin.licences.auth', compact('qualification_ulm', 'qualification_atcs', 'qualification_amts', 'competence_demandeur', 'qualification_examinateur', 'qualification_instructeur', 'qualification_classe', 'qualification_ifr', 'qualification_types', 'demande', 'demandeur', 'licence', 'medical_certificat'));
        } else {
            return redirect()->back()->with('error', 'Licence n\' est pas encore valide.');
        }
    }
    public function imprimer($id)
    {
        $demande  = Demande::find($id);

        $demandeur = $demande->demandeur;
        $licence = $demande->licence;


        if ($licence->licence_valide) {
            # code...
            $medical_certificat = $demande->medicalExaminations()->orderByDesc('id')->where(
                'valider',
                true
            )->first();
            $qualification_ulm = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->where('qualifications.libelle', 'Qualification de Class')
                ->where('demandes.id', $id)
                ->whereNotNull('qualification_demandeurs.ulm')
                ->select('qualification_demandeurs.ulm', 'qualification_demandeurs.date_examen')
                ->orderByDesc('qualification_demandeurs.id')
                ->first();
            $qualification_rpas = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->where('qualifications.libelle', 'Qualification Type Machine')
                ->where('demandes.id', $id)
                ->whereNotNull('qualification_demandeurs.rpa')
                ->select('qualification_demandeurs.rpa', 'qualification_demandeurs.date_examen')
                ->orderByDesc('qualification_demandeurs.id')
                ->get();
            $qualification_atcs = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->where('qualifications.libelle', 'Qualifications ATC')
                ->where('demandes.id', $id)
                ->select('qualification_demandeurs.atc', 'qualification_demandeurs.date_examen')
                ->orderByDesc('qualification_demandeurs.id')
                ->get();
            $qualification_amts = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->leftJoin('type_avions', 'type_avions.id', 'qualification_demandeurs.type_avion_id')
                ->where('qualifications.libelle', 'Qualifications AMT')
                ->where('demandes.id', $id)
                ->select(
                    'qualification_demandeurs.amt',
                    'qualification_demandeurs.date_examen',
                    'qualification_demandeurs.machine',
                    'type_avions.code'
                )
                ->orderByDesc('qualification_demandeurs.id')
                ->get();

            $qualification_types = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->leftJoin('type_avions', 'type_avions.id', 'qualification_demandeurs.type_avion_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->select('type_avions.code', 'qualification_demandeurs.date_examen')
                ->where('qualifications.libelle', 'Qualification Type Machine')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.id')
                ->get();
                
            $qualification_ifr = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->select('qualification_demandeurs.date_examen')
                ->where('qualifications.libelle', 'Qualification IFR')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.date_examen')
                ->first();

            $qualification_classe = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->select('qualification_demandeurs.date_examen', 'qualification_demandeurs.type_moteur')
                ->where('qualifications.libelle', 'Qualification de Class')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.date_examen')
                ->get();
            $qualification_instructeur = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->leftJoin('type_avions', 'type_avions.id', 'qualification_demandeurs.type_avion_id')
                ->select('qualification_demandeurs.date_examen', 'qualification_demandeurs.machine', 'type_avions.code', 'qualification_demandeurs.type_privilege')
                ->where('qualifications.libelle', 'Qualification instructeur')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.id')
                ->get();
            $qualification_examinateur = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->leftJoin('type_avions', 'type_avions.id', 'qualification_demandeurs.type_avion_id')
                ->select('qualification_demandeurs.date_examen', 'qualification_demandeurs.machine', 'type_avions.code', 'qualification_demandeurs.type_privilege')
                ->where('qualifications.libelle', 'Autorisation examinateur')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.id')
                ->get();
            $competence_demandeur = CompetenceDemandeur::join('demandes', 'demandes.id', 'competence_demandeurs.demande_id')
                ->select('competence_demandeurs.date', 'competence_demandeurs.validite', 'competence_demandeurs.niveau')
                ->where('competence_demandeurs.type', 'Contrôle de compétence linguistique')
                ->where('demandes.id', $id)
                ->orderByDesc('competence_demandeurs.date')
                ->first();




            return view('admin.licences.preview', compact('qualification_ulm', 'qualification_atcs', 'qualification_amts', 'competence_demandeur', 'qualification_examinateur', 'qualification_instructeur', 'qualification_classe', 'qualification_ifr', 'qualification_types', 'demande', 'demandeur', 'licence', 'medical_certificat'));
        } else {
            return redirect()->back()->with('error', 'Licence n\' est pas encore valide.');
        }
    }
    
        public function imprimerCarte($id)
    {
        $demande  = Demande::find($id);

        $demandeur = $demande->demandeur;
        $carte_stagiare = $demande->carteStagiare;
            return view('admin.stagiares.preview', compact('demande', 'demandeur', 'carte_stagiare'));
    }
    public function validerLicence(Licence $licence)
    {
        $etat_demande = $licence->update(
            [
                'licence_valide' => true,
                'licence_bloque' => false,
            ]
        );

        $etat_demande = $licence->demande->etatDemande->update(
            [
                'pel_licence_valider' => true
            ]
        );
        $activity = Activity::log('pel_licence_valider',$licence->demande->id);

        return back()->with('success', 'Licence validée avec succès.');
    }

    public function bloquerLicence(Licence $licence)
    {
        $etat_demande = $licence->update(
            [
                'licence_bloque' => true,
                'licence_valide' => false,
            ]
        );
        $etat_demande = $licence->demande->etatDemande->update(
            [
                'pel_licence_valider' => false
            ]
        );
        $activity = Activity::log('pel_licence_valider',$licence->demande->id);

        return back()->with('success', 'Licence bloquée avec succès.');
    }
    public function supprimerLicence(Licence $licence)
    {
        $l = $licence->delete();
        return back()->with('success', 'Licence supprimée avec succès.');
    }
    
    public function supprimerValidation(ValidationLicence $validation)
{
    // Sécurité optionnelle
    // $this->authorize('delete', $demande);

    $validation->delete();

    return redirect()->back()
        ->with('success', __('trans.deleted_successfully'));
}
    public function supprimerCarte(CarteStagiare $carte)
    {
        $l = $carte->delete();
        return back()->with('success', 'Carte supprimée avec succès.');
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Demande $demande)
    {
        //
        $request->validate([
            'description' => 'required|string',
        ]);


        $demande->update([
            'description' => $request->description,
        ]);

        return redirect()->route('demandes')->with('success', 'Demande mis à jour.');
    }
    
    
    public function updateDemandeur(Request $request, Demandeur $demandeur)
    {
        //
        $request->validate([
            
        ]);


        $demandeur->update([
            
        ]);

        return redirect()->route('demandeurs')->with('success', 'Demandeur mis à jour.');
    }


public function destroy(Demande $demande)
{
    // Sécurité optionnelle
    // $this->authorize('delete', $demande);

    $demande->delete();

    return redirect()->back()
        ->with('success', __('trans.deleted_successfully'));
}

    public function enroller($id)
    {

        $etat_demande = EtatDemande::where('demande_id', $id)->update(
            [
                'pel_valider_enrol' => true
            ]
        );
        $activity = Activity::log('pel_valider_enrol',$id);


        return back()->with('success', 'Demandeur enrolée avec succès.');
    }
    //ETAT DEMANDE
    public function annoterDemande($id)
    {

        $demande = Demande::find($id);
        $etat_demande = $demande->etatDemande->update(
            [
                'pel_annoter' => true
            ]
        );
        $activity = Activity::log('pel_annoter',$demande->id);
        // Aucun envoie a lieu
        /*if (!empty($demande->demandeur->user->whatsapp)) {
            # code...
            $this->dsvNotificationService->sendApplicationActionRequired(
                demandeNumber: $demande->code,
                demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                recipientRole: 'Service des licences aéronautiques - Section de médecine aéronautique',
                recipientPhone: $demande->demandeur->user->whatsapp,
                actionType: 'technical_review',
                applicantName: $demande->demandeur->np,
            );
        }*/


        return back()->with('success', 'Demande annotée avec succès.');
    }
    function signer($id)
    {

        $demande = Demande::find($id);
        $etat_demande = $demande->etatDemande->update(
            [
                'pel_dsv_signer' => Auth::user()->hasRole('admin')
            ]
        );
        $activity = Activity::log('pel_dsv_signer',$demande->id);
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
                recipientRole: 'PEL',
                recipientPhone: $pel->whatsapp,
                actionType: 'signed',
                applicantName: $demande->demandeur->np,
            );
        }
        # code...
        return back()->with('success', 'Demande signée avec succès.');
    }
    public function valider($id)
    {
        $demande = Demande::find($id);
        $etat_demande = $demande->etatDemande->update(
            [
                'pel_valider' => true
            ]
        );
        $activity = Activity::log('pel_valider',$demande->id);
        $dsv = User::role('dsv')
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->latest()->first();
        if (!empty($dsv->whatsapp)) {
            $this->dsvNotificationService->sendValidationConfirmation(
                applicationNumber: $demande->code,
                applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                recipientPhone: $dsv->whatsapp,
                recipientRole: 'DSV',
                validatorRole: 'Chef service PEL',
                applicantName: $demande->demandeur->np,
                nextSteps: [
                    'Validation requise de votre part',
                ]
            );
        }
        return back()->with('success', 'Demande validée avec succès.');
    }
    // END ETAT DEMANDE

    function generateLicenceValidation(ValidationLicence $validation)
    {
        
                $dg = User::role('dg')
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->latest()->first();
        $qualification_amts = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
            ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
            ->join('type_avions', 'type_avions.id', 'qualification_demandeurs.type_avion_id')
            ->where('qualifications.libelle', 'Qualifications AMT')
            ->where('demandes.id', $validation->demande_id)
            ->select(
                'qualification_demandeurs.amt',
                'qualification_demandeurs.date_examen',
                'qualification_demandeurs.machine',
                'type_avions.code'
            )
            ->orderByDesc('qualification_demandeurs.id')
            ->get()
            ->unique('code'); // Filter unique results by code after retrieval
            $qualification_types = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('type_avions', 'type_avions.id', 'qualification_demandeurs.type_avion_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->select('type_avions.code', 'qualification_demandeurs.date_examen')
                ->where('qualifications.libelle', 'Qualification Type Machine')
                ->where('demandes.id', $validation->demande_id)
                ->orderByDesc('qualification_demandeurs.id')
                ->get();
                
        return view('admin.licences.print', compact('validation','qualification_types','qualification_amts','dg'));
    }
    function generateLicence($id)
    {
        $demande = Demande::findOrFail($id);
        $demandeur = $demande->demandeur;
        $dateExpiration = null;
        if (in_array($demande->typeDemande->id, array(2, 4, 5, 6, 8)) && !empty($demandeur->licence)) {
            $oldDemande = $demandeur->licence->demande;
            if ($oldDemande->qualifications->isNotEmpty()) {
                foreach ($oldDemande->qualifications as $qualification) {
                    $newQualification = $qualification->replicate();
                    $newQualification->demande_id = $demande->id;
                    $newQualification->save();
                }
            }
            if ($oldDemande->competences->isNotEmpty()) {
                foreach ($oldDemande->competences as $competence) {
                    $newCompetence = $competence->replicate();
                    $newCompetence->demande_id = $demande->id;
                    $newCompetence->save();
                }
            }
        }
        if(!in_array($demande->typeDemande->id , array(8))){
            $qualification_demandeurs = $demande->qualifications;
            $competence_demandeurs = $demande->competences;
            $maxExpirationDateCompetence = null;
            if ($competence_demandeurs->isNotEmpty()) {
                $maxExpirationDateCompetence = $competence_demandeurs->map(function ($item) {
                    if (intval($item->niveau) === 6) {
                        return INF; // Special value for "never expires"
                    } else {
                        $startDate = \Carbon\Carbon::parse($item->date);
                        return $startDate->addMonths($item->validite);
                    }
                })->max();
                if ($maxExpirationDateCompetence === INF) {
                    $maxExpirationDateCompetence = null;
                } else {
                    $maxExpirationDateCompetence = $maxExpirationDateCompetence->format('Y-m-d');
                }
            }
            $maxExpirationDateQualification = null;
            if ($qualification_demandeurs->isNotEmpty()) {
                # code...
                $maxExpirationDateQualification = $qualification_demandeurs->map(function ($item) use ($demande) {
                    $startDate = \Carbon\Carbon::parse($item->date_examen);
                    if (in_array($demande->typeLicence->id, [35, 36, 37, 38])) {
                        # code...
                        $expirationDate = $startDate->copy()->addMonths(24)->endOfMonth();
                    } else {
                        $expirationDate = $startDate->copy()->addMonths(12)->endOfMonth();
                    }
    
                    return $expirationDate->format('Y-m-d');
                })->max();
                
            }
           
            $dateExpiration = $this->findMinDate(array($maxExpirationDateQualification, $maxExpirationDateCompetence));
            $dateExpiration  =  $dateExpiration->format('Y-m-d');
            
        }

        $licenseId = '';
        $first_part_code = strtoupper(substr($demande->typeLicence->nom, 0, 1)) . '' . strtoupper(substr($demande->typeLicence->machine, 0, 1));
        $countLicense = Licence::where('type_licence', 'LIKE', $demande->typeLicence->nom . '%')
            ->count();
        $nextNumber = str_pad(!empty($countLicense) ? $countLicense + 1 : 1, 3, '0', STR_PAD_LEFT);
        $second_part_code = $nextNumber;
        if (in_array($demande->typeLicence->id, array(27, 28, 29, 30, 31, 32, 33, 34))) {
            # code..
            $licenseId = $first_part_code . '-' . $second_part_code;
        } else if (in_array($demande->typeLicence->id, array(39))) {
            $licenseId = $second_part_code . '/' . date('Y');
        } else if (in_array($demande->typeLicence->id, array(37, 38))) {
            $licenseId = 'TE' . '-' . $second_part_code;
        } else if (in_array($demande->typeLicence->id, array(35))) {
            $licenseId = 'ATC' . '-' . $second_part_code;
        } else if (in_array($demande->typeLicence->id, array(36))) {
            $licenseId = 'ATE' . '-' . $second_part_code . '/' . date('Y');
        }
        // Get first DG user with non-null signature
        $dg = User::role('dg')
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->latest()->first();
        
        // Get first DSV user with non-null signature  
        $dsv = User::role('dsv')
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->latest()->first();
        
        // Get first admin user with 'menage-dsv' permission and non-null signature
        $pel = User::role('admin')
            ->whereHas('permissions', function ($q) {
                $q->where('name', 'menage-dsv');
            })
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->latest()->first();
        if (in_array($demande->typeDemande->id, array(1, 3 ,10))) {
            //D + C + V
            $licence = Licence::create(
                [
                    'demande_id' => $demande->id,
                    'demandeur_id' => $demandeur->id,
                    'categorie_licence' => $demande->typeLicence->categorie,
                    'machine_licence' => !empty($demande->typeLicence->machine) ? strtoupper(substr($demande->typeLicence->machine, 0, 1)) : '',
                    'type_licence' => $demande->typeLicence->nom,
                    'numero_licence' => $licenseId,
                    'np' => $demandeur->np,
                    'date_naissance' => $demandeur->date_naissance,
                    'adresse' => $demandeur->adresse,
                    'nationalite' => $demandeur->nationalite,
                    'photo' => $demandeur->photo,
                    'signature' =>  $demandeur->signature,
                    'cachet' => $dg ? $dg->cachet->cachet : '',
                    'signature_dg' => $dg ? $dg->signature->signature : '',
                    'signature_dsv' => $dsv ? $dsv->signature->signature : '',
                    'signature_pel' => $pel ? $pel->signature->signature : '',
                    'date_deliverance' => date('Y-m-d'),
                    'date_expiration' => $dateExpiration,
                ]
            );
        } else if ($demande->typeDemande->id === 7) {
            $currentDate = Carbon::now();
            $dateExpiration = $currentDate->copy()->addMonths(12)->endOfMonth();
            $demande = Demande::findOrFail($id);
            $validation = ValidationLicence::create([
                'demande_id' => $demande->id,
                'type_licence_id' => $demande->type_licence_id,
                'compagnie_id' => $demande->demandeur->compagnie_id,
                'numero_validation' => 'ANAC-' . now()->format('Y') . '-' . str_pad(ValidationLicence::count() + 1, 4, '0', STR_PAD_LEFT),
                'num_licence' => $demande->licences->first()->num_licence,
                'date_delivrance_licence' => $demande->licences->first()->date_licence,
                'lieu_delivrance_licence' => $demande->licences->first()->lieu_delivrance,
                'type_appareil' => '',
                'immatriculation_appareil' => '',
                'date_debut_validite' => now(),
                'date_fin_validite' => $dateExpiration,
                'date_emission' => now(),
                'restrictions' => null,
                'is_active' => true,
                'signataire_nom' => 'Abba SIDI MHAMED',
                'signataire_titre' => 'Directeur de la Sécurité des Vols',
                'signature_path' => $dsv->signature->signature,
                'cachet_path' => $dsv->cachet->cachet,
            ]);
        } else if ($demande->typeDemande->id === 9 && !empty($demandeur->licence)) {
            // reemission
        } else if (in_array($demande->typeDemande->id, array(2, 4, 5, 6)) && !empty($demandeur->licence)) {

            $licenceMiseAjour = $demandeur->licence;
            $licenceMiseAjour->date_mise_a_jour = date('Y-m-d');
            $oldDemande = $demandeur->licence->demande;
            $licenceMiseAjour->demande_id = $demande->id;
            $licenceMiseAjour->date_expiration = $dateExpiration;
            $licenceMiseAjour->licence_valide = 0;
            $licenceMiseAjour->signature_dg = $dg ? $dg->signature->signature : '';
            $licenceMiseAjour->signature_dsv = $dsv ? $dsv->signature->signature : '';
            $licenceMiseAjour->signature_pel = $pel ? $pel->signature->signature : '';
            $licenceMiseAjour->save();
            if (!empty($demande->demandeur->user->whatsapp)) {
                $response = $this->dsvNotificationService->notifyLicenseGenerated(
                    licenseNumber: $licenceMiseAjour->numero_licence,
                    licenseType: $licenceMiseAjour->type_licence,
                    recipientPhone: $demande->demandeur->user->whatsapp,
                    recipientName: $licenceMiseAjour->np,
                    issueDate: date('d-M-Y', strtotime($licenceMiseAjour->date_deliverance)),
                    expiryDate: date('d-M-Y', strtotime($licenceMiseAjour->date_expiration))
                );
            }
        } else if ($demande->typeDemande->id === 8) {

            // Récupérer la dernière carte
            $lastCarte = CarteStagiare::orderBy('id', 'desc')->first();
            
            if ($lastCarte && !empty($lastCarte->id)) {
                // Extraire le numéro de la dernière carte
                $parts = explode('/', $lastCarte->numero_carte);
                
                // Vérifier que le format est correct et qu'il y a un numéro
                if (count($parts) > 0 && is_numeric($parts[0])) {
                    $lastNumber = (int)$parts[0];
                    $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
                } else {
                    // Format invalide, recommencer à 1
                    $nextNumber = '001';
                }
            } else {
                // Aucune carte existante
                $nextNumber = '001';
            }
            
            $carteId = $nextNumber . '/' . date('Y') . '/PEL/DSV/ANAC';
            $currentDate = Carbon::now();
            $dateExpiration = $currentDate->copy()->addMonths(12)->endOfMonth();
            $carte = CarteStagiare::create(
                [
                    'demande_id' => $demande->id,
                    'demandeur_id' => $demandeur->id,
                    'numero_carte' => $carteId,
                    'np' => $demandeur->np,
                    'date_naissance' => $demandeur->date_naissance,
                    'adresse' => $demandeur->adresse,
                    'nationalite' => $demandeur->nationalite,
                    'photo' => $demandeur->photo,
                    'signature' =>  $demandeur->signature,
                    'cachet' => empty($dg->cachet->cachet) ? '' : $dg->cachet->cachet,
                    'signature_dg' => empty($dg->signature->signature) ? '' : $dg->signature->signature,
                    'signature_dsv' => empty($dsv->signature->signature) ? '' : $dsv->signature->signature,
                    'signature_pel' => empty($pel->signature->signature) ? '' : $pel->signature->signature,
                    'date_deliverance' => date('Y-m-d'),
                    'date_expiration' => $dateExpiration,
                ]
            );
        }
        return redirect()->route('licences')->with('success', 'Licence cree avec succès.');
    }
    /**
     * Finds the minimum non-null DateTime from an array of DateTime objects
     * 
     * @param \DateTime[] $dateTimes
     * @return \DateTime|null
     */
function findMinDate(array $dateTimes): ?DateTime
{
    $validDates = array_filter($dateTimes, function ($date) {
        // Accept both DateTime objects and valid date strings
        if ($date instanceof DateTime || $date instanceof \Carbon\Carbon) {
            return true;
        }
        if (is_string($date) && strtotime($date) !== false) {
            return new \DateTime($date);
        }
        return false;
    });

    if (empty($validDates)) {
        return null;
    }

    // Convert strings to DateTime if needed
    $validDates = array_map(function ($date) {
        if (is_string($date)) {
            return new \DateTime($date);
        }
        return $date;
    }, $validDates);

    $minDate = min($validDates);
    return $minDate;
}


    public function rejeter(Request $request)
    {
        $motif = $request->input('motif');
        if (!DB::getSchemaBuilder()->hasTable($request->table)) {
            return redirect()->back()->with('error', 'Table non trouvée.');
        }

        if (!DB::getSchemaBuilder()->hasColumn($request->table, 'valider') && !DB::getSchemaBuilder()->hasColumn($request->table, 'motif')) {
            return redirect()->back()->with('error', 'Colonne non trouvée dans la table.');
        }

        DB::table($request->table)->where('id', $request->id)->update(['valider' => 0, 'motif' => $motif]);

        $demande = DemandeAutorisation::find($request->demande_id);
        $state = $demande->etatDemande;
        if ($state) {
            $state->resetAllApprovalStates();
        }

        return redirect()->back()->with('success', 'Information rejetée avec succès.');
    }

    /**
     * Validate an aircraft (avion) in the context of a demand
     */
    public function validateAvion(Request $request)
    {

        $request->validate([
            'item_id' => 'required|integer',
            'valider' => 'required|boolean',
            'motif' => 'nullable|string|max:500'
        ]);

        try {
            DB::table('avions')
                ->where('id', $request->item_id)
                ->update([
                    'valider' => $request->valider,
                    'motif' => $request->motif,
                    'updated_at' => now()
                ]);
            if (!$request->valider) {
                # code...

                $demande = DemandeApprobation::find($request->demande_id);
                $state = $demande->etatDemande;
                if ($state) {
                    $state->resetAllApprovalStates();
                }
                $recipientUser = $demande->user;

                $this->dtaNotificationService->sendRejectionNotification(
                    $demande,
                    $recipientUser,
                    'DTA'
                );
            }
            return response()->json([
                'success' => true,
                'message' => 'Validation de l\'avion mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate a flight (vol)
     */
    public function validateVol(Request $request)
    {

        $request->validate([
            'item_id' => 'required|integer',
            'valider' => 'required|boolean',
            'motif' => 'nullable|string|max:500'
        ]);

        try {

            DB::table('vol_approbations')
                ->where('id', $request->item_id)
                ->update([
                    'valider' => $request->valider,
                    'motif' => $request->motif,
                    'updated_at' => now()
                ]);

            if (!$request->valider) {
                # code...
                $demande = DemandeApprobation::find($request->demande_id);

                $state = $demande->etatDemande;

                if ($state) {
                    $state->resetAllApprovalStates();
                }
                $recipientUser = $demande->user;

                $this->dtaNotificationService->sendRejectionNotification(
                    $demande,
                    $recipientUser,
                    'DTA'
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Validation du vol mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate an itinerary (itineraire)
     */
    public function validateItineraire(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'valider' => 'required|boolean',
            'motif' => 'nullable|string|max:500'
        ]);


        try {

            DB::table('itineraire_vols')
                ->where('id', $request->item_id)
                ->update([
                    'valider' => $request->valider,
                    'motif' => $request->motif,
                    'updated_at' => now()
                ]);
            if (!$request->valider) {
                # code...
                $demande = DemandeApprobation::find($request->demande_id);
                $state = $demande->etatDemande;
                if ($state) {
                    $state->resetAllApprovalStates();
                }
                $recipientUser = $demande->user;

                $this->dtaNotificationService->sendRejectionNotification(
                    $demande,
                    $recipientUser,
                    'DTA'
                );
            }
            return response()->json([
                'success' => true,
                'message' => 'Validation de l\'itinéraire mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate a document in the context of a demand
     */
    public function validateDocument(Request $request)
    {


        $request->validate([
            'item_id' => 'required|integer',
            'valider' => 'required|boolean',
            'motif' => 'nullable|string|max:500'
        ]);


        try {

            DB::table('document_approbations')
                ->where('id', $request->item_id)
                ->update([
                    'valider' => $request->valider,
                    'motif' => $request->motif,
                    'updated_at' => now()
                ]);
            if (!$request->valider) {
                # code...
                $demande = DemandeApprobation::find($request->demande_id);
                $state = $demande->etatDemande;
                if ($state) {
                    $state->resetAllApprovalStates();
                }
                $recipientUser = $demande->user;

                $this->dtaNotificationService->sendRejectionNotification(
                    $demande,
                    $recipientUser,
                    'DTA'
                );
            }
            return response()->json([
                'success' => true,
                'message' => 'Validation du document mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate the entire demand (demande)
     */
    public function validateDemande(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'valider' => 'required|boolean',
            'motif' => 'nullable|string|max:500'
        ]);

        try {

            //DB::table('etat_demande_approbations')
            //  ->where('demande_id', $request->item_id)
            //->update([
            //  'service_valider' => $request->valider,
            //'updated_at' => now()
            //]);
            if (!$request->valider) {
                # code...
                $demande = DemandeApprobation::find($request->demande_id);
                $state = $demande->etatDemande;
                if ($state) {
                    $state->resetAllApprovalStates();
                }
                $recipientUser = $demande->user;

                $this->dtaNotificationService->sendRejectionNotification(
                    $demande,
                    $recipientUser,
                    'DTA'
                );
            }
            // If validating, optionally validate all related items
            if ($request->valider) {
                DB::table('avions')
                    ->where('demande_approbation_id', $request->item_id)
                    ->update(['valider' => true]);

                DB::table('vol_approbations')
                    ->where('demande_approbation_id', $request->item_id)
                    ->update(['valider' => true]);

                DB::table('itineraire_vols')
                    ->whereIn('vol_id', function ($query) use ($request) {
                        $query->select('id')
                            ->from('vol_approbations')
                            ->where('demande_approbation_id', $request->item_id);
                    })->update(['valider' => true]);

                DB::table('document_approbations')
                    ->where('demande_approbation_id', $request->item_id)
                    ->update(['valider' => true]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Validation de la demande mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

public function validateAllItems(Request $request)
{
    try {
        $demande = DemandeAutorisation::findOrFail($request->demande_id);
        
        // Valider tous les avions
        if ($demande->avions->isNotEmpty()) {
            foreach ($demande->avions as $avion) {
                $avion->update([
                    'valider' => true,
                    //'motif' => $request->global_comments
                ]);
            }
        }
        
        // Valider tous les vols
        if ($demande->vols->isNotEmpty()) {
            foreach ($demande->vols as $vol) {
                $vol->update([
                    'valider' => true,
                    //'motif' => $request->global_comments
                ]);
            }
        }
        
        // Valider tous les membres d'équipage
        if ($demande->equipe->isNotEmpty()) {
            foreach ($demande->equipe as $membre) {
                $membre->update([
                    'valider' => true,
                    //'motif' => $request->global_comments
                ]);
            }
        }
        
        // Valider tous les fret
        if ($demande->fret->isNotEmpty()) {
            foreach ($demande->fret as $fret) {
                $fret->update([
                    'valider' => true,
                    //'motif' => $request->global_comments
                ]);
            }
        }
        
        // Valider toutes les receiving parties
        if ($demande->receivingParties->isNotEmpty()) {
            foreach ($demande->receivingParties as $party) {
                $party->update([
                    'valider' => true,
                    //'motif' => $request->global_comments
                ]);
            }
        }
        
        // Valider tous les documents
        if ($demande->documents->isNotEmpty()) {
            foreach ($demande->documents as $document) {
                $document->update([
                    'valider' => true,
                    //'motif' => $request->global_comments
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Tous les éléments ont été validés avec succès!'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la validation globale: ' . $e->getMessage()
        ], 500);
    }
}
    public function validateAvionVi(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'valider' => 'required|boolean',
            'motif' => 'nullable|string|max:500'
        ]);

        try {
            DB::table('avions')
                ->where('id', $request->item_id)
                ->update([
                    'valider' => $request->valider,
                    'motif' => $request->motif,
                    'updated_at' => now()
                ]);

            if (!$request->valider) {
                $demande = DemandeAutorisation::findOrFail($request->demande_id);
                $state = $demande->etatDemande;
                $recipientUser = $demande->user;

                $this->dtaAutorisationNotificationService->sendRejectionNotification(
                    $demande,
                    $recipientUser,
                    'DTA'
                );

                if ($state) {
                    $state->resetAllApprovalStates();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Validation de l\'avion mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validateVolVi(Request $request)
    {

        $request->validate([
            'item_id' => 'required|integer',
            'valider' => 'required|boolean',
            'motif' => 'nullable|string|max:500'
        ]);

        try {
            DB::table('vols')
                ->where('id', $request->item_id)
                ->update([
                    'valider' => $request->valider,
                    'motif' => $request->motif,
                    'updated_at' => now()
                ]);

            if (!$request->valider) {
                $demande = DemandeAutorisation::findOrFail($request->demande_id);

                $state = $demande->etatDemande;
                $recipientUser = $demande->user;

                $this->dtaAutorisationNotificationService->sendRejectionNotification(
                    $demande,
                    $recipientUser,
                    'DTA'
                );

                if ($state) {
                    $state->resetAllApprovalStates();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Validation du vol mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validateEquipageVi(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'valider' => 'required|boolean',
            'motif' => 'nullable|string|max:500'
        ]);

        try {
            DB::table('equipe_vols')
                ->where('id', $request->item_id)
                ->update([
                    'valider' => $request->valider,
                    'motif' => $request->motif,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Validation de l\'équipage mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validateFretVi(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'valider' => 'required|boolean',
            'motif' => 'nullable|string|max:500'
        ]);

        try {
            DB::table('fret_vols')
                ->where('id', $request->item_id)
                ->update([
                    'valider' => $request->valider,
                    'motif' => $request->motif,
                    'updated_at' => now()
                ]);

            if (!$request->valider) {
                $demande = DemandeAutorisation::findOrFail($request->demande_id);
                $state = $demande->etatDemande;
                $recipientUser = $demande->user;

                $this->dtaAutorisationNotificationService->sendRejectionNotification(
                    $demande,
                    $recipientUser,
                    'DTA'
                );

                if ($state) {
                    $state->resetAllApprovalStates();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Validation du fret mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validateReceivingVi(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'valider' => 'required|boolean',
            'motif' => 'nullable|string|max:500'
        ]);

        try {
            DB::table('receiving_parties')
                ->where('id', $request->item_id)
                ->update([
                    'valider' => $request->valider,
                    'motif' => $request->motif,
                    'updated_at' => now()
                ]);

            if (!$request->valider) {
                $demande = DemandeAutorisation::findOrFail($request->demande_id);
                $state = $demande->etatDemande;
                $recipientUser = $demande->user;

                $this->dtaAutorisationNotificationService->sendRejectionNotification(
                    $demande,
                    $recipientUser,
                    'DTA'
                );

                if ($state) {
                    $state->resetAllApprovalStates();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Validation du destinataire mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validateItineraireVi(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'valider' => 'required|boolean',
            'motif' => 'nullable|string|max:500'
        ]);

        try {
            DB::table('itineraires')
                ->where('id', $request->item_id)
                ->update([
                    'valider' => $request->valider,
                    'motif' => $request->motif,
                    'updated_at' => now()
                ]);

            if (!$request->valider) {
                $demande = DemandeAutorisation::findOrFail($request->demande_id);
                $state = $demande->etatDemande;
                $recipientUser = $demande->user;

                $this->dtaAutorisationNotificationService->sendRejectionNotification(
                    $demande,
                    $recipientUser,
                    'DTA'
                );

                if ($state) {
                    $state->resetAllApprovalStates();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Validation de l\'itinéraire mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validateDocumentVi(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'valider' => 'required|boolean',
            'motif' => 'nullable|string|max:500'
        ]);

        try {
            DB::table('document_autorisations')
                ->where('id', $request->item_id)
                ->update([
                    'valider' => $request->valider,
                    'motif' => $request->motif,
                    'updated_at' => now()
                ]);

            if (!$request->valider) {
                $demande = DemandeAutorisation::findOrFail($request->demande_id);
                $state = $demande->etatDemande;
                $recipientUser = $demande->user;

                $this->dtaAutorisationNotificationService->sendRejectionNotification(
                    $demande,
                    $recipientUser,
                    'DTA'
                );

                if ($state) {
                    $state->resetAllApprovalStates();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Validation du document mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validateDemandeVi(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'valider' => 'required|boolean',
            'motif' => 'nullable|string|max:500'
        ]);

        try {
            if (!$request->valider) {
                $demande = DemandeAutorisation::findOrFail($request->demande_id);
                $state = $demande->etatDemande;
                $recipientUser = $demande->user;

                $this->dtaAutorisationNotificationService->sendRejectionNotification(
                    $demande,
                    $recipientUser,
                    'DTA'
                );

                if ($state) {
                    $state->resetAllApprovalStates();
                }
            }

            if ($request->valider) {
                DB::table('avions')
                    ->where('demande_autorisation_id', $request->item_id)
                    ->update(['valider' => true]);

                DB::table('vols')
                    ->where('demande_autorisation_id', $request->item_id)
                    ->update(['valider' => true]);

                DB::table('itineraires')
                    ->whereIn('vol_id', function ($query) use ($request) {
                        $query->select('id')
                            ->from('vols')
                            ->where('demande_autorisation_id', $request->item_id);
                    })->update(['valider' => true]);

                DB::table('document_autorisations')
                    ->where('demande_autorisation_id', $request->item_id)
                    ->update(['valider' => true]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Validation de la demande mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }
    
     public function toggleStatus(Request $request, Demandeur $demandeur)
    {
        $request->validate([
            'field' => 'required|in:is_examinateur,is_instructeur',
            'value' => 'required|boolean'
        ]);

        try {
            $field = $request->field;
            $demandeur->update([
                $field => $request->value
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'demandeur' => $demandeur->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status'
            ], 500);
        }
    }
        public function handleApproval(Request $request)
    {
        $action = $request->input('action_type');
        $table = $request->input('table');
        $id = $request->input('id');
        $demandeId = $request->input('demande_id');
        $motif = $request->input('motif');

        if (!DB::getSchemaBuilder()->hasTable($table)) {
            return response()->json(['success' => false, 'message' => 'Table not found'], 400);
        }

        $demande = DemandeAutorisation::findOrFail($demandeId);

        if ($action === 'approve') {
            DB::table($table)->where('id', $id)->update([
                'valider' => 1,
                'motif' => null,
                'updated_at' => now()
            ]);

            Activity::log('approved',$demande->id);

            return response()->json(['success' => true]);
        } else {
            $request->validate(['motif' => 'required|string|max:500']);
            
            DB::table($table)->where('id', $id)->update([
                'valider' => 0,
                'motif' => $motif,
                'updated_at' => now()
            ]);

            $demande->update(['mise_a_jour' => true]);
            if ($state = $demande->etatDemande) {
                $state->resetAllApprovalStates();
                $state->update(['compagnie_cree_demande' => false,'dta_rejeter' => true]);
                $demande->update(['date_soumission' => null]);
                
            }

            Activity::log('rejected',$demande->id);
            $recipientUser = $demande->user;

                $this->dtaAutorisationNotificationService->sendRejectionNotification(
                    $demande,
                    $recipientUser,
                    'DTA',
                    [$motif]
                );
            return response()->json(['success' => true]);
        }
    }
    
        public function storeDemandePiece(Request $request)
    {
        $request->validate([
            'demande_id' => 'required|exists:demandes,id',
            'titre' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
        ]);

        try {
            $piece = new DemandePiece();
            $piece->demande_id = $request->demande_id;
            $piece->titre = $request->titre;

            // Handle file upload
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('pieces', $filename, 'public');
                $piece->url = $filename;
            }

            $piece->save();

            return response()->json([
                'success' => true,
                'message' => 'Pièce ajoutée avec succès',
                'piece' => $piece
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateDemandePiece(Request $request, $id)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048'
        ]);

        try {
            $piece = DemandePiece::findOrFail($id);
            $piece->titre = $request->titre;

            // Handle file upload
            if ($request->hasFile('document')) {
                // Delete old file
                if ($piece->url) {
                    //Storage::disk('public')->delete('uploads/pieces/' . $piece->url);
                }

                $file = $request->file('document');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('pieces', $filename, 'public');
                $piece->url = $filename;
            }

            $piece->save();

            return response()->json([
                'success' => true,
                'message' => 'Pièce mise à jour avec succès',
                'piece' => $piece
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyDemandePiece($id)
    {
        try {
            $piece = DemandePiece::findOrFail($id);
            
            // Delete file
            if ($piece->url) {
                //Storage::disk('public')->delete('uploads/pieces/' . $piece->url);
            }
            
            $piece->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pièce supprimée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function updatePhoto(Request $request, $id)
{
    $request->validate([
        'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    try {
        $licence = Licence::findOrFail($id);
        
        // Delete old photo if exists and not default
        if ($licence->photo && $licence->photo != 'default.png' && file_exists(public_path('uploads/' . $licence->photo))) {
            unlink(public_path('uploads/' . $licence->photo));
        }
        
        // Upload new photo
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $licence->photo = $filename;
            $licence->save();
        }
        
        return redirect()->back()->with('success', __('trans.photo_updated_successfully'));
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', __('trans.error_updating_photo') . ': ' . $e->getMessage());
    }
}
public function updateType(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'type_demande_id' => 'required|exists:type_demandes,id'
        ], [
            'type_demande_id.required' => __('trans.type_required'),
            'type_demande_id.exists' => __('trans.type_invalid')
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('trans.validation_error'),
                'errors' => $validator->errors()
            ], 422);
        }
        
        $demande = Demande::findOrFail($id);
        $demande->type_demande_id = $request->type_demande_id;
        $demande->save();
        
        // Log pour audit si nécessaire
        \Log::info('Type de demande mis à jour', [
            'demande_id' => $id,
            'new_type_id' => $request->type_demande_id,
            'user_id' => auth()->id()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => __('trans.type_updated_successfully')
        ]);
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => __('trans.demande_not_found')
        ], 404);
        
    } catch (\Exception $e) {
        \Log::error('Erreur mise à jour type demande: ' . $e->getMessage(), [
            'demande_id' => $id,
            'user_id' => auth()->id(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => __('trans.error_updating_type')
        ], 500);
    }
}
    

    public function pendingExaminateurs()
    {
        $examinateurs = ExaminateurCentre::with(['centreFormation', 'centreFormation.user'])
            ->where('statut_validation', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $stats = [
            'total_pending' => ExaminateurCentre::where('statut_validation', 'en_attente')->count(),
            'total_validated' => ExaminateurCentre::where('statut_validation', 'valide')->count(),
            'total_rejected' => ExaminateurCentre::where('statut_validation', 'refuse')->count(),
            'total_expired' => ExaminateurCentre::where('statut_validation', 'valide')
                ->where('date_fin_validite', '<', now())
                ->count()
        ];
        
        return view('admin.centre-examinateurs.pending', compact('examinateurs', 'stats'));
    }
    
    /**
     * Valider un examinateur
     */
    public function validateExaminateur(Request $request, $id)
    {
        $request->validate([
            'date_fin_validite' => 'nullable|date|after:today',
            'commentaire' => 'nullable|string|max:500'
        ]);
        
        try {
            DB::beginTransaction();
            
            $examinateur = ExaminateurCentre::findOrFail($id);
            
            $examinateur->statut_validation = 'valide';
            $examinateur->valide_par = Auth::id();
            $examinateur->date_validation = now();
            $examinateur->motif_refus = null;
            
            if ($request->has('date_fin_validite')) {
                $examinateur->date_fin_validite = $request->date_fin_validite;
            }
            
            $examinateur->save();
            
            // Log de l'action
            \Log::info('Examinateur validé', [
                'examinateur_id' => $id,
                'validated_by' => Auth::id(),
                'date' => now()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => __('trans.examiner_validated_successfully')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Erreur validation examinateur: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('trans.error_validating_examiner')
            ], 500);
        }
    }
    
    /**
     * Rejeter un examinateur
     */
    public function rejectExaminateur(Request $request, $id)
    {
        $request->validate([
            'motif_refus' => 'required|string|max:500'
        ]);
        
        try {
            DB::beginTransaction();
            
            $examinateur = ExaminateurCentre::findOrFail($id);
            
            $examinateur->statut_validation = 'refuse';
            $examinateur->motif_refus = $request->motif_refus;
            $examinateur->valide_par = Auth::id();
            $examinateur->date_validation = now();
            
            $examinateur->save();
            
            // Log de l'action
            \Log::info('Examinateur rejeté', [
                'examinateur_id' => $id,
                'rejected_by' => Auth::id(),
                'motif' => $request->motif_refus
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => __('trans.examiner_rejected_successfully')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Erreur rejet examinateur: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('trans.error_rejecting_examiner')
            ], 500);
        }
    }
    
    /**
     * Afficher tous les examinateurs (validés et rejetés)
     */
    public function allExaminateurs(Request $request)
    {
        $query = ExaminateurCentre::with(['centreFormation', 'validePar']);
        
        // Filtres
        if ($request->has('statut') && $request->statut != '') {
            $query->where('statut_validation', $request->statut);
        }
        
        if ($request->has('centre_id') && $request->centre_id != '') {
            $query->where('centre_formation_id', $request->centre_id);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('numero_licence_examinateur', 'LIKE', "%{$search}%");
            });
        }
        
        $examinateurs = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $centres = CentreFormation::all();
        
        $stats = [
            'total' => ExaminateurCentre::count(),
            'pending' => ExaminateurCentre::where('statut_validation', 'en_attente')->count(),
            'validated' => ExaminateurCentre::where('statut_validation', 'valide')->count(),
            'rejected' => ExaminateurCentre::where('statut_validation', 'refuse')->count()
        ];
        
        return view('admin.centre-examinateurs.index', compact('examinateurs', 'centres', 'stats'));
    }
    
    /**
     * Afficher les détails d'un examinateur
     */
    public function showExaminateur($id)
    {
        $examinateur = ExaminateurCentre::with([
            'centreFormation',
            'validePar',
            'formations.typeFormation',
            'formations.demandeur'
        ])->findOrFail($id);
        
        return view('admin.centre-examinateurs.partials.details', compact('examinateur'));
    }
    
}