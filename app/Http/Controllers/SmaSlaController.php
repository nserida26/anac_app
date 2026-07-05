<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Demande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\CompetenceDemandeur;

use App\Models\LicenceDemandeur;

use App\Models\Document;
use App\Models\EmployeurDemandeur;
use App\Models\EtatDemande;
use App\Models\ExperienceDemandeur;
use App\Models\ExperienceMaintenanceDemandeur;
use App\Models\ExprienceMaintenanceDemandeur;
use App\Models\FormationDemandeur;
use App\Models\InterruptionDemandeur;
use App\Models\MedicalExamination;
use App\Models\QualificationDemandeur;
use App\Models\TrainingDemandeur;
use App\Models\ExamenMedical;
use App\Models\Evaluateur;
use App\Services\LicenseApplicationNotificationService;

class SmaSlaController extends Controller
{
    protected $notificationService;

    public function __construct(LicenseApplicationNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $evaluateurs = Evaluateur::all();
        $demandes = Demande::with('demandeur')->where('status', '<>', 'En attente')->get();
        $examens = ExamenMedical::with(['demandeur', 'examinateur', 'evaluateur'])->get();

        return view('sec.index', compact('demandes', 'evaluateurs', 'examens'));
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
        $demande = Demande::find($id);
        $demandeur = $demande->demandeur;
        $examens = $demande->demandeur->examens;
        $formations = $demande->demandeur->formations;
        //
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



        return view('sec.show', compact('formations', 'licence_demandeurs', 'examens', 'demande', 'demandeur', 'employeur_demandeurs', 'experience_maintenance_demandeurs', 'interruption_demandeurs', 'formation_demandeurs', 'documents', 'entrainement_demandeurs', 'competence_demandeurs', 'experience_demandeurs', 'medical_examinations', 'qualification_demandeurs'));
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
    public function relaunch(ExamenMedical $examen)
    {


        if (!empty($examen->evaluateur->user->whatsapp)) {
            $this->notificationService->sendMedicalValidationRequest(
                demandeNumber: $examen->id,
                applicantName: $examen->demandeur->np,
                medicalEvaluator: $examen->evaluateur->user,
                examen: $examen
            );
        }
        //
        return back()->with('success', 'Validation relanceé avec succès.');
    }

    function annoter(Request $request)
    {

        $id = $request->input('demande_id');
        $demande = Demande::find($id);
        $d = $demande->update([
            'evaluateur_id' => $request->evaluateur_id
        ]);
        $etat_demande = $demande->etatDemande->update(
            [

                'evaluateur_annoter' => true,
            ]
        );
        $activity = Activity::log('evaluateur_annoter',$demande->id);
        if (!empty($demande->evaluateur->user->whatsapp)) {
            $this->notificationService->sendApplicationActionRequired(
                demandeNumber: $demande->code,
                demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                recipientRole: 'ÉVALUATEUR',
                recipientPhone: $demande->evaluateur->user->whatsapp,
                actionType: 'annotation',
                applicantName: $demande->demandeur->np,
            );
        }
        return back()->with('success', 'Demande annotée avec succès.');
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
    function validerSla($id)
    {

        $demande = Demande::find($id);
        $etat_demande = $demande->etatDemande->update(
            [

                'sl_valider' => true,
            ]
        );
        $activity = Activity::log('sl_valider',$demande->id);
        if (!empty($demande->demandeur->user->whatsapp)) {
            $this->notificationService->sendValidationConfirmation(
                applicationNumber: $demande->code,
                applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                recipientPhone: $demande->demandeur->user->whatsapp,
                recipientRole: 'Chef service PEL',
                validatorRole: 'Service des licences aéronautiques',
                applicantName: $demande->demandeur->np,
                nextSteps: [
                    'Validation requise de votre part',
                ]
            );
        }
        return back()->with('success', 'Demande validee avec succès.');
    }
    function validerSma($id)
    {


        $demande = Demande::find($id);
        $etat_demande = $demande->etatDemande->update(
            [
                'sm_valider' => true,
            ]
        );
        $activity = Activity::log('sm_valider',$demande->id);
        if (!empty($demande->demandeur->user->whatsapp)) {
            $this->notificationService->sendValidationConfirmation(
                applicationNumber: $demande->code,
                applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                recipientPhone: $demande->demandeur->user->whatsapp,
                recipientRole: 'Chef service PEL',
                validatorRole: 'Chef section de médecine aéronautique',
                applicantName: $demande->demandeur->np,
                nextSteps: [
                    'Validation requise de votre part',
                ]
            );
        }

        return back()->with('success', 'Demande annotée avec succès.');
    }
    public function valider(ExamenMedical $examen)
    {
        $examen->update(
            [
                'valider_sma' => true
            ]
        );
        return redirect()->route('sma')->with('success', 'Examen médical validé.');
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

        $demande = Demande::findOrFail($demandeId);

        if ($action === 'approve') {
            DB::table($table)->where('id', $id)->update([
                'valider' => 1,
                'motif' => null,
                'updated_at' => now()
            ]);

            Activity::log('approved' ,$demande->id);

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
                $state->update(['demandeur_cree_demande' => false]);
            }

            Activity::log('rejected',$demande->id);

            if (!empty($demande->demandeur->user->whatsapp)) {
                $this->notificationService->sendApplicationRejection(
                    applicationNumber: $demande->code,
                    applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                    rejecterRole: 'Service PEL',
                    recipientPhone: $demande->demandeur->user->whatsapp,
                    reasons: [$motif],
                    applicantName: $demande->demandeur->np,
                );
            }

            return response()->json(['success' => true]);
        }
    }
    public function checklist(Request $request, Demande $demande)
    {
        $role = auth()->user()->getRoleNames()->first();

        //
        $request->validate([
            'checklist' => 'required|file|mimes:pdf'
        ]);

        if ($request->hasFile('checklist')) {
            $checklistPath = $request->file('checklist')->store('checklists', 'public');
        } else {
            $checklistPath = null;
        }
        $columnName = "checklist_$role";
        $p = $demande->update(
            [
                $columnName => $checklistPath,
            ]
        );

        return redirect()->back()->with('success', 'CheckList enregistree avec succès.');
    }
}
