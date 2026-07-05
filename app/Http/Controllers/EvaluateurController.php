<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Demande;
use Illuminate\Http\Request;
use App\Models\ExamenMedical;
use App\Models\MedicalExamination;
use App\Models\EtatDemande;
use App\Services\LicenseApplicationNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EvaluateurController extends Controller
{
    //
    protected $notificationService;

    public function __construct(LicenseApplicationNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // Liste des examens
    public function index()
    {
        $userId = Auth::user()->id;

        $medical_examinations = MedicalExamination::join('demandes', 'demandes.id', 'medical_examinations.demande_id')
            ->join('centre_medicals', 'centre_medicals.id', 'medical_examinations.centre_medical_id')
            ->where('demandes.evaluateur_id', $userId)
            ->select('centre_medicals.libelle as centre_medical', 'medical_examinations.*')
            ->get();
        $examens = ExamenMedical::with(['demandeur', 'examinateur'])->get();
        return view('evaluateur.index', compact('examens', 'medical_examinations'));
    }

    // Afficher un examen
    public function show(ExamenMedical $examen)
    {
        return view('evaluateur.show', compact('examen'));
    }

    // Formulaire d'édition
    public function edit(ExamenMedical $examen)
    {
        return view('evaluateur.edit', compact('examen'));
    }

    // Mettre à jour un examen
    public function update(Request $request, ExamenMedical $examen)
    {
        $evaluateur = Auth::user()->evaluateur;
        $request->validate([
            'rapport_evaluateur' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'validite_evaluateur' => 'integer'
        ]);
        if ($request->hasFile('rapport_evaluateur')) {
            $rapportPath = $request->file('rapport_evaluateur')->store('rapports', 'public');
        }


        $examen->update([
            'validite_evaluateur' => $request->validite_evaluateur,
            'rapport_evaluateur' => $rapportPath,
            'evaluateur_id' =>  $evaluateur->id
        ]);

        return redirect()->route('evaluateur')->with('success', 'Examen médical mis à jour.');
    }



    public function valider($table, $id)
    {
        // Vérifiez si la table existe dans la base de données
        if (!DB::getSchemaBuilder()->hasTable($table)) {
            return redirect()->back()->with('error', 'Table non trouvée.');
        }

        // Vérifiez si la colonne 'valider_evaluateur' existe dans la table
        if (!DB::getSchemaBuilder()->hasColumn($table, 'valider_evaluateur')) {
            return redirect()->back()->with('error', 'Colonne "valider_evaluateur" non trouvée dans la table.');
        }

        // Mettez à jour la valeur du booléen 'valider_evaluateur' à 1
        DB::table($table)->where('id', $id)->update(['valider_evaluateur' => 1]);
        if ($table  !== 'examens_medicaux') {
            # code...
            $demande_id = DB::table($table)
                ->where('id', $id)
                ->value('demande_id');
            if (!$demande_id) {
                throw new \Exception("Could not find demande_id for the record");
            }

            $demande = Demande::find($demande_id);
            $etat  = $demande->etatDemande->update([
                'evaluateur_valider' => 1
            ]);
            $sma = User::role('sma')->latest()->first();
            $pel = User::role('admin')
                ->whereHas('permissions', fn($q) => $q->where('name', 'menage-dsv'))
                ->whereHas('signature', fn($q) => $q->whereNotNull('signature'))
                ->latest()->first();
            $activity = Activity::log('evaluateur_valider',$demande->id);
            if ($sma && !empty($sma->whatsapp)) {
                        $this->notificationService->sendValidationConfirmation(
                            applicationNumber: $demande->code,
                            applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                            recipientPhone: $sma->whatsapp,
                            recipientRole: 'SMA',
                            validatorRole: 'SMA',
                            applicantName: $demande->demandeur->np,
                            nextSteps: ['Validation requise de votre part']
                        );
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
        }


        return redirect()->back()->with('success', 'Information validée avec succès.');
    }
}
