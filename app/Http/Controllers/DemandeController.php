<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Autorite;
use App\Models\CentreFormation;
use App\Models\CentreMedical;
use App\Models\CompetenceDemandeur;
use App\Models\Demande;
use App\Models\Demandeur;
use App\Models\TypeAvion;
use App\Models\Document;
use App\Models\EmployeurDemandeur;
use App\Models\EtatDemande;
use App\Models\ExperienceDemandeur;
use App\Models\ExperienceMaintenanceDemandeur;
use App\Models\ExprienceMaintenanceDemandeur;
use App\Models\FormationDemandeur;
use App\Models\InterruptionDemandeur;
use App\Models\MedicalExamination;
use App\Models\TypeVol;

use App\Models\Qualification;
use App\Models\User;
use App\Models\QualificationDemandeur;
use App\Models\Simulateur;
use App\Models\TrainingDemandeur;
use App\Models\Facture;
use App\Models\LicenceDemandeur;
use App\Models\Paiement;
use App\Models\PaiementAutorisation;
use App\Models\TypeDemande;
use App\Models\TypeDemandeAutorisation;
use App\Models\TypeDocument;
use App\Models\TypeLicence;
use App\Models\ValidationLicence;
use App\Services\LicenseApplicationNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DemandeController extends Controller
{
    protected $notificationService;
    protected $dsvNotificationService;

    public function __construct(
        LicenseApplicationNotificationService $notificationService,
        LicenseApplicationNotificationService $dsvNotificationService
    ) {
        $this->notificationService = $notificationService;
        $this->dsvNotificationService = $dsvNotificationService;
    }

    public function index()
    {
        $user = Auth::user();
        $demandes = empty($user->demandeur->demandes) ? [] : $user->demandeur->demandes;


        $demandeAutorisations =  $user->demandeAutorisations->sortByDesc('created_at');
        $type_vols  = TypeVol::all();

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



        $type_demande_autorisations = TypeDemandeAutorisation::all();
        $paiementAutorisations = $user->paiements;


        return view('user.index', compact('type_vols','demandes', 'demandeAutorisations', 'type_demande_autorisations', 'paiementAutorisations'));
    }
    public function create()
    {

        $type_demandes = TypeDemande::all();
        $type_licences = TypeLicence::all();
        return view('user.licences.create', compact('type_demandes', 'type_licences'));
    }

    public function pay($id)
    {
        $paiement = Paiement::find($id);
        return view('user.licences.pay', compact('paiement'));
    }
    public function store(Request $request)
    {
        do {
            $code = rand(1000, 9999);
        } while (Demande::where('code', $code)->exists());
        

        $demandeur = Demandeur::where('user_id', auth()->id())->first();
        $request->validate([
            'type_demande_id' => 'required|integer|exists:type_demandes,id',
            'type_licence_id' => 'required|integer|exists:type_licences,id',
        ]);
        

        // Créer l demande
        $demande = Demande::create(array_merge($request->all(), ['status' => 'En attente'], ['date' => date('Y-m-d')], ['code' => $code], ['demandeur_id' => $demandeur->id]));
        $etat_demande = EtatDemande::create([
            'demande_id' => $demande->id,
            'user_id' => auth()->id(),
        ]);
        return redirect()->route('user')->with('success', 'Demande créée avec succès.');
    }

    public function edit($id)
    {
        $demande = Demande::find($id);

        $qualifications = $demande->typeLicence->qualifications;
        $centre_formations = CentreFormation::all();
        $simulateurs = Simulateur::all();
        $centre_medicals = CentreMedical::all();
        $autorites = Autorite::all();

        $type_avions = TypeAvion::all();
        $typeLicenceId = $demande->typeLicence->id;
        $typeDemandeId =  $demande->typeDemande->id;
        $type_documents = TypeDocument::where('type_licence_id', $typeLicenceId)
            ->where('type_demande_id', $typeDemandeId)
            ->select('id', 'nom_fr', 'nom_en')
            ->get();


        $licence_demandeurs = LicenceDemandeur::join('demandes', 'demandes.id', 'licence_demandeurs.demande_id')
            ->where('licence_demandeurs.demande_id', $id)
            ->select('licence_demandeurs.*')
            ->get();

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
            //->join('demandes', 'demandes.demande_id', 'demandeurs.id')
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
        return view('user.licences.edit', compact('type_documents', 'type_avions', 'licence_demandeurs', 'autorites', 'id', 'employeur_demandeurs', 'experience_maintenance_demandeurs', 'interruption_demandeurs', 'formation_demandeurs', 'documents', 'entrainement_demandeurs', 'competence_demandeurs', 'experience_demandeurs', 'medical_examinations', 'qualification_demandeurs', 'demande', 'centre_formations', 'qualifications', 'simulateurs', 'centre_medicals'));
    }

    public function update(Request $request, Paiement $paiement)
    {
        //
        $request->validate([
            'quittance' => 'required|file',
            'date_paiement' => 'required|date'
        ]);

        if ($request->hasFile('quittance')) {
            $quittancePath = $request->file('quittance')->store('paiements', 'public');
        } else {
            $quittancePath = null;
        }

        $p = $paiement->update(
            [
                'quittance' => $quittancePath,
                'date_paiement' => $request->date_paiement,
                'statut' => 'Réglée'
            ]
        );
        $demande = $paiement->demande;
        $etat_demande = $demande->etatDemande->update(
            [
                'demandeur_payer' => true
            ]
        );
        $activity = Activity::log('demandeur_payer',$demande->id);
        $daf = User::role('daf')
            ->latest()->first();
        if (!empty($daf->whatsapp)) {
            $this->notificationService->notifyPaymentSettled(
                invoiceNumber: $paiement->reference,
                applicationNumber: $demande->code,
                applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                recipientPhone: $daf->whatsapp,
                recipientRole: 'DAF',
                paymentDate: $paiement->date_paiement,
            );
        }
        return redirect()->route('user')->with('success', 'Paiement mis à jour avec succès.');
    }

    public function validateDemande($id)
    {
        $demande = Demande::findOrFail($id);

        $etat_demande = $demande->etatDemande->update(
            [
                'demandeur_cree_demande' => true
            ]
        );
        $activity = Activity::log('demandeur_cree_demande',$demande->id);
        $demande->resetAllMotifs();
        $demande->resetAllValidations();
        $demande->etatDemande->resetAllApprovalStates();
        $demande->status = 'En cours de traitement';
        $demande->mise_a_jour = false;
        $demande->date = date('Y-m-d');
        $demande->save();
        $dg = User::role('dg')
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->latest()->first();
        if (!$demande->mise_a_jour) {
            # code...
            if (!empty($dg->whatsapp)) {
                $this->notificationService->sendApplicationActionRequired(
                    demandeNumber: $demande->code,
                    demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                    recipientRole: 'DG',
                    recipientPhone: $dg->whatsapp,
                    actionType: '',
                    applicantName: $demande->demandeur->np,
                );
            }
        }



        return back()->with('success', 'Demande validée avec succès.');
    }

    function getLicenceValidation(ValidationLicence $validation)
    {
        return view('user.licences.validation.print', compact('validation'));
    }
    public function invoice($id)
    {

        //
        $facture = Facture::find($id);

        return view('user.licences.invoice', compact('facture'));
    }

    public function destroy($id)
    {
        $demande = Demande::findOrFail($id);

        $demande->delete();
        return redirect()->back()->with('success', 'Demande supprimée avec succès.');
    }
    public function storeMcentres(Request $request)
    {
        $request->validate([
            'libelle' => 'required'
        ]);

        $centre = CentreMedical::create($request->all());

        return response()->json([
            'success' => 'Centre créée avec succès.',
            'centre' => $centre
        ]);
    }

    public function storeCentres(Request $request)
    {
        $request->validate([
            'libelle' => 'required'
        ]);

        $centre = CentreFormation::create($request->all());

        return response()->json([
            'success' => 'Centre créée avec succès.',
            'centre' => $centre
        ]);
    }


    public function storeLicences(Request $request)
    {
        $request->validate([
            'date_licence' => 'required|date',
            'lieu_delivrance' => 'required',
            'autorite_id' => 'required',
            'num_licence' => 'required'
        ]);
        // Créer l Licence demande
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }

        $licence_demandeur = LicenceDemandeur::create(array_merge($request->all(), ['document' => $documentPath]));

        return response()->json([
            'success' => 'Licence créée avec succès.',
            'licence' => $licence_demandeur
        ]);
    }
    public function updateLicences(Request $request, LicenceDemandeur $licence_demandeur)
    {
        $request->validate([
            'date_licence' => 'required|date',
            'lieu_delivrance' => 'required',
            'autorite_id' => 'required',
            'num_licence' => 'required'
        ]);
        // Créer l Licence demande
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }

        $licence_demandeur = $licence_demandeur->update(array_merge($request->all(), ['document' => $documentPath]));

        return response()->json([
            'success' => 'Licence mis a jour avec succès.',
            'licence' => $licence_demandeur
        ]);
    }

    public function destroyLicences(LicenceDemandeur $licence_demandeur)
    {
        // Supprimer l'enregistrement
        $licence_demandeur->delete();
        // Redirection avec un message de succès
        return response()->json(['success' => 'Licence supprimée avec succès.']);
    }

    public function storeFormations(Request $request)
    {

        $request->validate([
            'date_formation' => 'required|date',
            'lieu' => 'required',
            'centre_formation_id' => 'required',
            'document' => 'required|file|mimes:pdf'
        ]);
        if ($request->hasFile('document')) {

            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }


        $formation_demandeur = FormationDemandeur::create(array_merge($request->all(), ['document' => $documentPath]));

        return response()->json([
            'success' => 'Formation créée avec succès.',
            'formation' => $formation_demandeur
        ]);
    }
    public function updateFormations(Request $request, FormationDemandeur $formation)
    {
        $request->validate([
            'date_formation' => 'required|date',
            'lieu' => 'required',
            'centre_formation_id' => 'required',
        ]);

        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }

        $formation_demandeur = $formation->update(array_merge($request->all(), ['document' => $documentPath]));

        return response()->json([
            'success' => 'Formation mis a jour avec succès.',
            'formation' => $formation_demandeur
        ]);
    }
    public function destroyFormations(FormationDemandeur $formation)
    {
        // Supprimer l'enregistrement
        $formation->delete();

        // Redirection avec un message de succès

        return response()->json(['success' => 'Formation supprimé avec succès.']);
    }

    public function getQualification($id)
    {
        $qualification = QualificationDemandeur::findOrFail($id);
        return response()->json($qualification);
    }

    public function storeQualifications(Request $request)
    {



        $request->validate([
            'qualification_id' => 'required',
            //'type_avion_id' => 'required',
            'date_examen' => 'required|date',
            'lieu' => 'required',
            'centre_formation_id' => 'required',
        ]);
        // Créer l Qualifications demande
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }

        $qualification_demandeur = QualificationDemandeur::create(array_merge($request->all(), ['document' => $documentPath]));


        return response()->json([
            'success' => 'Qualification créée avec succès.',
            'qualification' => $qualification_demandeur
        ]);
    }

    public function updateQualifications(Request $request, QualificationDemandeur $qualification_demandeur)
    {
        $qualification_demandeur->delete();

        $request->validate([
            'qualification_id' => 'required',
            //'type_avion_id' => 'required',
            'date_examen' => 'required|date',
            'lieu' => 'required',
            'centre_formation_id' => 'required',
        ]);
        // Créer l Qualifications demande
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }
        $qd = QualificationDemandeur::create(array_merge($request->all(), ['document' => $documentPath]));

        return response()->json([
            'success' => 'Qualification mis a jour avec succès.',
            'qualification' => $qd
        ]);
    }
    public function destroyQualifications(QualificationDemandeur $qualification_demandeur)
    {
        // Supprimer l'enregistrement
        $qualification_demandeur->delete();

        // Redirection avec un message de succès

        return response()->json(['success' => 'Qualification supprimé avec succès.']);
    }

    public function storeAptitudes(Request $request)
    {


        $request->validate([
            'date_examen' => 'required|date',
            'validite' => 'required',
            'centre_medical_id' => 'required',
        ]);
        // Créer l Aptitude demande


        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }

        $medical_examination = MedicalExamination::create(array_merge($request->all(), ['document' => $documentPath]));


        return response()->json([
            'success' => 'Aptitude créée avec succès.',
            'aptitude' => $medical_examination
        ]);
    }
    public function updateAptitudes(Request $request, MedicalExamination $medical_examination)
    {

        $request->validate([
            'date_examen' => 'required|date',
            'validite' => 'required',
            'centre_medical_id' => 'required',
        ]);
        // Créer l Aptitude demande
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }
        $md = $medical_examination->update(array_merge($request->all(), ['document' => $documentPath]));

        return response()->json([
            'success' => 'Aptitude mis a jour avec succès.',
            'aptitude' => $md
        ]);
    }
    public function destroyAptitudes(MedicalExamination $medical_examination)
    {
        // Supprimer l'enregistrement
        $medical_examination->delete();

        // Redirection avec un message de succès

        return response()->json([
            'success' => 'Aptitude supprimée avec succès.',
        ]);
    }


    public function storeExpriences(Request $request)
    {

        $request->validate([
            'nature' => 'required',
            'total' => 'required',
            'six_mois' => 'required',
            'trois_mois' => 'required',
        ]);
        // Créer l Experience demande
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }
        $experience_demandeur = ExperienceDemandeur::create(array_merge($request->all(), ['document' => $documentPath]));
        return response()->json([
            'success' => 'Experience créée avec succès.',
            'experience' => $experience_demandeur
        ]);
    }
    public function updateExpriences(Request $request, ExperienceDemandeur $experience_demandeur)
    {

        $request->validate([
            'nature' => 'required',
            'total' => 'required',
            'six_mois' => 'required',
            'trois_mois' => 'required',
        ]);

        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }
        $ed = $experience_demandeur->update(array_merge($request->all(), ['document' => $documentPath]));
        return response()->json([
            'success' => 'Experience mis a jour avec succès.',
            'experience' => $ed
        ]);
    }
    public function destroyExpriences(ExperienceDemandeur $experience_demandeur)
    {

        // Supprimer l'enregistrement
        $experience_demandeur->delete();

        // Redirection avec un message de succès
        return response()->json([
            'success' => 'Experience supprimée avec succès.'
        ]);
    }


    public function storeCompetences(Request $request)
    {

        $request->validate([
            'type' => 'required',
            'date' => 'required',
            //'validite' => 'required',
            'centre_formation_id' => 'required',
        ]);
        // Créer l Aptitude demande
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }
        $competence_demandeur = CompetenceDemandeur::create(array_merge($request->all(), ['document' => $documentPath]));
        return response()->json([
            'success' => 'Competence créée avec succès.',
            'competence' => $competence_demandeur
        ]);
    }

    public function updateCompetences(Request $request, CompetenceDemandeur $competence_demandeur)
    {

        $request->validate([
            'type' => 'required',
            'niveau' => 'nullable|required',
            'date' => 'required',
            //'validite' => 'required',
            'centre_formation_id' => 'required',
        ]);
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }
        $cd = $competence_demandeur->update(array_merge($request->all(), ['document' => $documentPath]));

        return response()->json([
            'success' => 'Competence mis a jour avec succès.',
            'competence' => $cd
        ]);
    }

    public function destroyCompetences(CompetenceDemandeur $competence_demandeur)
    {
        // Supprimer l'enregistrement
        $competence_demandeur->delete();

        // Redirection avec un message de succès

        return response()->json([
            'success' => 'Competence supprimée avec succès.'
        ]);
    }

    public function storeEntrainements(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'date' => 'required',
            'validite' => 'required',
            'centre_formation_id' => 'required',
        ]);
        // Créer l Aptitude demande
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }
        $training_demandeur = TrainingDemandeur::create(array_merge($request->all(), ['document' => $documentPath]));

        return response()->json([
            'success' => 'Entrainement créée avec succès.',
            'entrainement' => $training_demandeur
        ]);
    }

    public function updateEntrainements(Request $request, TrainingDemandeur $training_demandeur)
    {
        $request->validate([
            'type' => 'required',
            'date' => 'required',
            'validite' => 'required',
            'centre_formation_id' => 'required',
        ]);
        // Créer l Aptitude demande
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }
        $training_demandeur = $training_demandeur->update(array_merge($request->all(), ['document' => $documentPath]));

        return response()->json([
            'success' => 'Entrainement mis a jour avec succès.',
            'entrainement' => $training_demandeur
        ]);
    }

    public function destroyEntrainements(TrainingDemandeur $entrainement_demandeur)
    {
        // Supprimer l'enregistrement

        $entrainement_demandeur->delete();

        // Redirection avec un message de succès
        return response()->json([
            'success' => 'Entrainement supprimée avec succès.',

        ]);
    }

    public function storeDocuments(Request $request)
    {


        $request->validate([
            'type_document_id' => 'required|array', // Assurez-vous que c'est un tableau
            'type_document_id.*' => 'required|exists:type_documents,id', // Valider chaque élément du tableau
            'pieces' => 'required|array', // Assurez-vous que c'est un tableau
            'pieces.*' => 'required|mimes:pdf', // Valider chaque fichier
        ]);
        $demandeId = $request->input('demande_id');
        $documents = [];

        foreach ($request->file('pieces') as $index => $file) {
            $typeDocumentId = $request->input('type_document_id')[$index];

            $fileName = 'document_' . $demandeId . '_' . $typeDocumentId . '_' . time() . '.' . $file->getClientOriginalExtension();

            $file->storeAs('documents', $fileName, 'public');
            $document = Document::create([
                'demande_id' => $demandeId,
                'type_document_id' => $typeDocumentId,
                'nom_fr' => TypeDocument::find($typeDocumentId)->nom_fr,
                'url' => 'documents/' . $fileName,
            ]);

            $documents[] = $document;
        }
        return response()->json([
            'success' => 'Document ajouté avec succès',
            'documents' => $documents
        ]);
    }

    public function updateDocuments(Request $request, Document $document)
    {
        $request->validate([
            'piece' => 'required|mimes:pdf',
        ]);
        // Créer l Aptitude demande

        if ($request->hasFile('piece')) {
            $documentPath = $request->file('piece')->store('documents', 'public');
        } else {
            $documentPath = null;
        }
        $doc = $document->update(array_merge(['url' => $documentPath]));
        $updatedDocument = Document::find($document->id);
        return response()->json([
            'success' => 'Document mis à jour avec succès',
            'document' => $updatedDocument
        ]);
    }

    public function destroyDocuments(Document $document)
    {

        // Supprimer l'enregistrement
        $document->delete();

        // Redirection avec un message de succès
        return response()->json(['success' => 'Document supprimé avec succès']);
    }


    public function storeInterruptions(Request $request)
    {


        $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date',
            'raison' => 'required',
            'demande_id' => 'required',
        ]);

        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }

        $interruptionDemandeur = InterruptionDemandeur::create(array_merge($request->all(), ['document' => $documentPath]));
        return response()->json([
            'success' => 'Interruption créée avec succès.',
            'interruption' => $interruptionDemandeur
        ]);
    }
    public function updateInterruptions(Request $request, InterruptionDemandeur $interruptionDemandeur)
    {


        $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date',
            'raison' => 'required'
        ]);

        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }

        $interruptionDemandeur = $interruptionDemandeur->update(array_merge($request->all(), ['document' => $documentPath]));
        return response()->json([
            'success' => 'Interruption mis a jour avec succès.',
            'interruption' => $interruptionDemandeur
        ]);
    }
    public function destroyInterruptions(InterruptionDemandeur $interruptionDemandeur)
    {
        // Supprimer l'enregistrement
        $interruptionDemandeur->delete();

        // Redirection avec un message de succès
        return response()->json([
            'success' => 'Interruption supprimée avec succès.',

        ]);
    }


    public function storeMaintenances(Request $request)
    {
        $request->validate([
            'date_debut' => 'required',
            'date_fin' => 'required|date',
            'description_maintenance' => 'required',
        ]);
        // Créer l Maintenance demande

        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }
        $experience_maintenance_demandeur = ExperienceMaintenanceDemandeur::create(array_merge($request->all(), ['document' => $documentPath]));
        return response()->json([
            'success' => 'Experience Maintenance créée avec succès.',
            'maintenance' => $experience_maintenance_demandeur
        ]);
    }
    public function updateMaintenances(Request $request, ExperienceMaintenanceDemandeur $experience_maintenance_demandeur)
    {
        $request->validate([
            'date_debut' => 'required',
            'date_fin' => 'required|date',
            'description_maintenance' => 'required',
        ]);
        // Créer l Maintenance demande

        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }
        $experience_maintenance_demandeur = $experience_maintenance_demandeur->update(array_merge($request->all(), ['document' => $documentPath]));

        return response()->json([
            'success' => 'Experience Maintenance mis a jour avec succès.',
            'maintenance' => $experience_maintenance_demandeur
        ]);
    }
    public function destroyMaintenances(ExperienceMaintenanceDemandeur $experience_maintenance_demandeur)
    {
        // Supprimer l'enregistrement
        $experience_maintenance_demandeur->delete();

        // Redirection avec un message de succès
        return response()->json([
            'success' => 'Experience Maintenance supprimée avec succès.',

        ]);
    }


    public function storeEmployeurs(Request $request)
    {
        $request->validate([
            'periode_du' => 'required|date',
            'periode_au' => 'required|date',
            'fonction' => 'required',
            'employeur' => 'required',
        ]);
        // Créer l Employeur demande
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }
        $employeurDemandeur = EmployeurDemandeur::create(array_merge($request->all(), ['document' => $documentPath]));
        return response()->json([
            'success' => 'Employeur créée avec succès.',
            'employeur' => $employeurDemandeur
        ]);
    }
    public function updateEmployeurs(Request $request, EmployeurDemandeur $employeur_demandeur)
    {
        $request->validate([
            'periode_du' => 'required|date',
            'periode_au' => 'required|date',
            'fonction' => 'required',
            'employeur' => 'required',
        ]);
        // Créer l Employeur demande
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        } else {
            $documentPath = null;
        }
        $employeurDemandeur = $employeur_demandeur->update(array_merge($request->all(), ['document' => $documentPath]));

        return response()->json([
            'success' => 'Employeur mis a jour avec succès.',
            'employeur' => $employeurDemandeur
        ]);
    }
    public function destroyEmployeurs(EmployeurDemandeur $employeurDemandeur)
    {
        // Supprimer l'enregistrement
        $employeurDemandeur->delete();

        // Redirection avec un message de succès

        return response()->json([
            'success' => 'Employeur supprimée avec succès.',

        ]);
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
                ->join('type_avions', 'type_avions.id', 'qualification_demandeurs.type_avion_id')
                ->select('qualification_demandeurs.date_examen', 'qualification_demandeurs.machine', 'type_avions.code', 'qualification_demandeurs.type_privilege')
                ->where('qualifications.libelle', 'Qualification instructeur')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.id')
                ->first();
            $qualification_examinateur = QualificationDemandeur::join('qualifications', 'qualifications.id', 'qualification_demandeurs.qualification_id')
                ->join('demandes', 'demandes.id', 'qualification_demandeurs.demande_id')
                ->join('type_avions', 'type_avions.id', 'qualification_demandeurs.type_avion_id')
                ->select('qualification_demandeurs.date_examen', 'qualification_demandeurs.machine', 'type_avions.code', 'qualification_demandeurs.type_privilege')
                ->where('qualifications.libelle', 'Autorisation examinateur')
                ->where('demandes.id', $id)
                ->orderByDesc('qualification_demandeurs.id')
                ->first();
            $competence_demandeur = CompetenceDemandeur::join('demandes', 'demandes.id', 'competence_demandeurs.demande_id')
                ->select('competence_demandeurs.date', 'competence_demandeurs.validite', 'competence_demandeurs.niveau')
                ->where('competence_demandeurs.type', 'Contrôle de compétence linguistique')
                ->where('demandes.id', $id)
                ->orderByDesc('competence_demandeurs.id')
                ->first();
            



            return view('user.licences.authentification.print', compact('qualification_ulm', 'qualification_atcs', 'qualification_amts', 'competence_demandeur', 'qualification_examinateur', 'qualification_instructeur', 'qualification_classe', 'qualification_ifr', 'qualification_types', 'demande', 'demandeur', 'licence', 'medical_certificat'));
        } else {
            return redirect()->back()->with('error', 'Licence n\' est pas encore valide.');
        }
    }
    
        /**
     * Unified function to update demande state
     */
    public function updateDemandeState(Request $request, $demandeId)
    {
        
        try {
            DB::beginTransaction();

            // Validation de base
            $validated = $request->validate([
                'action' => 'required|string|in:demandeur_cree_demande,dg_valider,dsv_valider,pel_valider,sl_valider,sm_valider,evaluateur_valider,' .
                    'dg_annoter,dsv_annoter,pel_annoter,evaluateur_annoter,dsv_dg_annoter,' .
                    'dg_rejeter,dsv_rejeter,dsv_dg_rejeter,' .
                    'dg_signer,dsv_signer,pel_dsv_signer,dsv_dg_signer,' .
                    'daf_demande_pay,daf_confirme_pay,compagnie_payer,demandeur_payer,' .
                    'agent_enroler,pel_valider_enrol,pel_licence_valider,agent_imprimer,' .
                    'evaluateur_valider_medical',
                'is_approved' => 'sometimes|boolean',
                'is_rejected' => 'sometimes|boolean',
                'motif' => 'required_if:action,dg_rejeter,dsv_rejeter,dsv_dg_rejeter|nullable|string',
                'evaluateur_id' => 'required_if:action,evaluateur_annoter|nullable|exists:users,id',
                'medical_evaluator_id' => 'required_if:action,evaluateur_valider_medical|nullable|exists:users,id',
            ]);
            

            // Récupération des données
            $demande = Demande::with(['demandeur.user', 'typeDemande', 'etatDemande'])->findOrFail($demandeId);
            $action = $validated['action'];

            // Récupération des acteurs (une seule fois)
            $dg = User::role('dg')
                ->whereHas('signature', fn($q) => $q->whereNotNull('signature'))
                ->latest()->first();
            
            $dsv = User::role('dsv')
                ->whereHas('signature', fn($q) => $q->whereNotNull('signature'))
                ->latest()->first();
            
            $pel = User::role('admin')
                ->whereHas('permissions', fn($q) => $q->where('name', 'menage-dsv'))
                ->whereHas('signature', fn($q) => $q->whereNotNull('signature'))
                ->latest()->first();
            
            $daf = User::role('daf')->latest()->first();
            $sma = User::role('sma')->latest()->first();
            $sla = User::role('sla')->latest()->first();

            // Traitement des actions
            switch ($action) {
                // Validation actions
                case 'demandeur_cree_demande':
                    
                        if($demande->mise_a_jour){
                            $demande->resetAllValidations();
                            $demande->resetAllMotifs();
                            if ($demande->etatDemande) {
                                $demande->etatDemande->resetAllApprovalStates();
                                
                            }
                        }
                    $demande->update([
                        'status' => 'En cours de traitement',
                        'mise_a_jour' => false,
                        'date' => now()->format('Y-m-d')
                    ]);

                    $demande->etatDemande->update(['demandeur_cree_demande' => true]);

                    if ($dg && !empty($dg->whatsapp)) {
                        $this->notificationService->sendApplicationActionRequired(
                            demandeNumber: $demande->code,
                            demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientRole: 'DG',
                            recipientPhone: $dg->whatsapp,
                            actionType: '',
                            applicantName: $demande->demandeur->np,
                        );
                    }
                    break;

                case 'dg_valider':
                case 'dsv_dg_valider':
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update([
                            'dg_valider' => true,
                            'dsv_dg_valider' => Auth::user()->hasRole('dsv')
                        ]);
                    }

                    if ($dg && !empty($dg->whatsapp)) {
                        $this->dsvNotificationService->sendValidationConfirmation(
                            applicationNumber: $demande->code,
                            applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientPhone: $dg->whatsapp,
                            recipientRole: 'DG',
                            validatorRole: 'DSV',
                            applicantName: $demande->demandeur->np,
                            nextSteps: ['Validation requise de votre part']
                        );
                    }
                    break;

                case 'dsv_valider':
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update(['dsv_valider' => true]);
                    }

                    if ($dg && !empty($dg->whatsapp)) {
                        $this->dsvNotificationService->sendValidationConfirmation(
                            applicationNumber: $demande->code,
                            applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientPhone: $dg->whatsapp,
                            recipientRole: 'DG',
                            validatorRole: 'DSV',
                            applicantName: $demande->demandeur->np,
                            nextSteps: ['Validation requise de votre part']
                        );
                    }
                    break;

                case 'pel_valider':
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update(['pel_valider' => true]);
                    }

                    if ($dsv && !empty($dsv->whatsapp)) {
                        $this->dsvNotificationService->sendValidationConfirmation(
                            applicationNumber: $demande->code,
                            applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientPhone: $dsv->whatsapp,
                            recipientRole: 'DSV',
                            validatorRole: 'Chef service PEL',
                            applicantName: $demande->demandeur->np,
                            nextSteps: ['Validation requise de votre part']
                        );
                    }
                    break;

                case 'sl_valider':
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update(['sl_valider' => true]);
                    }

                    if ($pel && !empty($pel->whatsapp)) {
                        $this->notificationService->sendValidationConfirmation(
                            applicationNumber: $demande->code,
                            applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientPhone: $pel->whatsapp,
                            recipientRole: 'Chef service PEL',
                            validatorRole: 'Service des licences aéronautiques',
                            applicantName: $demande->demandeur->np,
                            nextSteps: ['Validation requise de votre part']
                        );
                    }
                    break;

                case 'sm_valider':
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update(['sm_valider' => true]);
                    }

                    if ($pel && !empty($pel->whatsapp)) {
                        $this->notificationService->sendValidationConfirmation(
                            applicationNumber: $demande->code,
                            applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientPhone: $pel->whatsapp,
                            recipientRole: 'Chef service PEL',
                            validatorRole: 'Service des licences aéronautiques',
                            applicantName: $demande->demandeur->np,
                            nextSteps: ['Validation requise de votre part']
                        );
                    }
                    break;


                // Annotation actions
                case 'dg_annoter':
                case 'dsv_dg_annoter':
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update([
                            'dg_annoter' => true,
                            'dsv_dg_annoter' => Auth::user()->hasRole('dsv')
                        ]);
                    }

                    if ($dsv && !empty($dsv->whatsapp)) {
                        $this->dsvNotificationService->sendApplicationActionRequired(
                            demandeNumber: $demande->code,
                            demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientRole: 'DSV',
                            recipientPhone: $dsv->whatsapp,
                            actionType: 'annotation',
                            applicantName: $demande->demandeur->np,
                        );
                    }
                    break;

                case 'dsv_annoter':
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update(['dsv_annoter' => true]);
                    }

                    if ($pel && !empty($pel->whatsapp)) {
                        $this->dsvNotificationService->sendApplicationActionRequired(
                            demandeNumber: $demande->code,
                            demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientRole: 'Chef Service PEL',
                            recipientPhone: $pel->whatsapp,
                            actionType: 'annotation',
                            applicantName: $demande->demandeur->np,
                        );
                    }
                    break;

                case 'pel_annoter':
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update(['pel_annoter' => true]);
                    }
                    
                    if ($sma && !empty($sma->whatsapp)) {
                        $this->dsvNotificationService->sendApplicationActionRequired(
                            demandeNumber: $demande->code,
                            demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientRole: 'SMA',
                            recipientPhone: $sma->whatsapp,
                            actionType: 'annotation',
                            applicantName: $demande->demandeur->np,
                        );
                    }
                    if ($sla && !empty($sla->whatsapp)) {
                        $this->dsvNotificationService->sendApplicationActionRequired(
                            demandeNumber: $demande->code,
                            demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientRole: 'SLA',
                            recipientPhone: $sla->whatsapp,
                            actionType: 'annotation',
                            applicantName: $demande->demandeur->np,
                        );
                    }
                    break;



                // Signature actions
                case 'dg_signer':
                case 'dsv_dg_signer':
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update([
                            'dg_signer' => true,
                            'dsv_dg_signer' => Auth::user()->hasRole('dsv')
                        ]);
                    }

                    if ($dsv && !empty($dsv->whatsapp)) {
                        $this->dsvNotificationService->sendApplicationActionRequired(
                            demandeNumber: $demande->code,
                            demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientRole: 'DSV',
                            recipientPhone: $dsv->whatsapp,
                            actionType: 'signed',
                            applicantName: $demande->demandeur->np,
                        );
                    }
                    break;

                case 'dsv_signer':
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update(['dsv_signer' => true]);
                    }

                    if ($pel && !empty($pel->whatsapp)) {
                        $this->dsvNotificationService->sendApplicationActionRequired(
                            demandeNumber: $demande->code,
                            demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientRole: 'Service PEL',
                            recipientPhone: $pel->whatsapp,
                            actionType: 'signed',
                            applicantName: $demande->demandeur->np,
                        );
                    }
                    break;

                case 'pel_dsv_signer':
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update(['pel_dsv_signer' => Auth::user()->hasRole('admin')]);
                    }

                    if ($dsv && !empty($dsv->whatsapp)) {
                        $this->dsvNotificationService->sendApplicationActionRequired(
                            demandeNumber: $demande->code,
                            demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientRole: 'DSV',
                            recipientPhone: $dsv->whatsapp,
                            actionType: 'signed',
                            applicantName: $demande->demandeur->np,
                        );
                    }
                    break;

                case 'daf_confirme_pay':
                    $paiement = $demande->paiement;
                    $p = $paiement->update(
                            [
                                'statut' => 'Payé'
                            ]
                        );
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update(['daf_confirme_pay' => true]);
                    }
                    if (!empty($demande->demandeur->user->whatsapp)) {
                        $response = $this->notificationService->confirmToPayer(
                            invoiceNumber: $paiement->reference,
                            applicationNumber: $demande->code,
                            applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientPhone: $demande->demandeur->user->whatsapp,
                            recipientName: 'Demandeur',
                            amount: $paiement->montant,
                            paymentDate: $paiement->date_paiement
                        );
                    }
                    if (!empty($dg->whatsapp) && !empty($dsv->whatsapp)) {
                        if (in_array($demande->typeDemande->id, array(1, 3, 7))) {
                            # code...
                            $this->notificationService->sendApplicationActionRequired(
                                demandeNumber: $demande->code,
                                demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                                recipientRole: 'DG',
                                recipientPhone: $dg->whatsapp,
                                actionType: 'signature',
                                applicantName: $demande->demandeur->np,
                            );
                        } else {
                            # code...
                            $this->notificationService->sendApplicationActionRequired(
                                demandeNumber: $demande->code,
                                demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                                recipientRole: 'DSV',
                                recipientPhone: $dsv->whatsapp,
                                actionType: 'signature',
                                applicantName: $demande->demandeur->np,
                            );
                        }
                    }
                    break;

                case 'compagnie_payer':
                    if ($daf && !empty($daf->whatsapp)) {
                        $this->dsvNotificationService->sendApplicationActionRequired(
                            demandeNumber: $demande->code,
                            demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientRole: 'DAF',
                            recipientPhone: $daf->whatsapp,
                            actionType: 'payed',
                            applicantName: $demande->demandeur->np,
                        );
                    }
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update(['compagnie_payer' => true]);
                    }
                    break;

                case 'demandeur_payer':
                    if ($daf && !empty($daf->whatsapp)) {
                        $this->dsvNotificationService->sendApplicationActionRequired(
                            demandeNumber: $demande->code,
                            demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientRole: 'DAF',
                            recipientPhone: $daf->whatsapp,
                            actionType: 'payed',
                            applicantName: $demande->demandeur->np,
                        );
                    }
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update(['demandeur_payer' => true]);
                    }
                    break;

                // Enrolment and finalization actions
                case 'agent_enroler':
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update(['agent_enroler' => true]);
                    }
                    break;

                case 'pel_valider_enrol':
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update(['pel_valider_enrol' => true]);
                    }
                    break;

                case 'pel_licence_valider':
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update(['pel_licence_valider' => true]);
                    }
                    break;

                case 'agent_imprimer':
                    if ($demande->etatDemande) {
                        $demande->etatDemande->update(['agent_imprimer' => true]);
                    }
                    break;

                default:
                    throw new \Exception("Action non reconnue: {$action}");
            }
            
            // Mise à jour de l'état via EtatDemande
            /*if ($demande->etatDemande) {
                
                EtatDemande::updateState(
                    $demandeId,
                    $action,
                    auth()->id(),
                    $validated['is_approved'] ?? false,
                    $validated['is_rejected'] ?? false
                );
            }
*/
            // Log activity
            
            Activity::log($action,$demande->id);

            DB::commit();

            return redirect()->back()->with('success', 'État mis à jour avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur updateDemandeState: ' . $e->getMessage(), [
                'action' => $request->input('action'),
                'demande_id' => $demandeId,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
}
