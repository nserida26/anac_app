<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Demande;
use Illuminate\Http\Request;
use App\Models\ExamenMedical;
use App\Models\MedicalExamination;
use App\Models\EtatDemande;
use App\Models\User;
use App\Services\LicenseApplicationNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EvaluateurController extends Controller
{
    /** Tables dont l'évaluateur peut valider une ligne via valider(). */
    private const TABLES_VALIDABLES = ['medical_examinations', 'examens_medicaux'];

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
        // Sans nouveau fichier, on garde le rapport déjà déposé.
        $rapportPath = $examen->rapport_evaluateur;
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
        // Seules ces tables peuvent être validées par un évaluateur (le nom vient de l'URL).
        abort_unless(in_array($table, self::TABLES_VALIDABLES, true), 404);
        abort_unless(DB::table($table)->where('id', $id)->exists(), 404);

        // Une visite médicale ne peut être validée que par l'évaluateur affecté à la demande.
        if ($table === 'medical_examinations') {
            $evaluateurId = DB::table('medical_examinations')
                ->join('demandes', 'demandes.id', 'medical_examinations.demande_id')
                ->where('medical_examinations.id', $id)
                ->value('demandes.evaluateur_id');
            abort_unless((int) $evaluateurId === (int) Auth::id(), 403);
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
