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
use App\Services\RapportMedicalService;
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
        // Tableau confidentiel : seuls les rapports transmis par leur auteur arrivent chez l'évaluateur.
        $examens = ExamenMedical::transmis()
            ->with(['demandeur.user', 'demandeur.licences', 'demandeur.compagnie', 'examinateur', 'centreMedical'])
            ->latest()->get();
        [$traites, $aTraiter] = $examens->partition(fn ($examen) => $examen->valider_evaluateur);

        return view('evaluateur.index', compact('examens', 'aTraiter', 'traites', 'medical_examinations'));
    }

    /** Avis et observations saisis directement dans le tableau confidentiel. */
    public function enregistrerAvis(Request $request, ExamenMedical $examen)
    {
        $this->authorize('evaluer', $examen);
        $request->validate($this->reglesAvis());

        $this->appliquerAvis($examen, [
            'avis_evaluateur' => $request->avis_evaluateur,
            'observations_evaluateur' => $request->observations_evaluateur,
            // Validité inchangée tant que l'évaluateur ne la réduit pas (formulaire complet).
            // La colonne vaut 0 par défaut (et non NULL) : 0 signifie « pas encore fixée ».
            'validite_evaluateur' => $examen->validite_evaluateur ?: $examen->validite,
        ]);

        return redirect()->route('evaluateur')->with('success', __('trans.avis_enregistre'));
    }

    // Afficher un examen
    public function show(ExamenMedical $examen)
    {
        $this->authorize('view', $examen);

        return view('evaluateur.show', compact('examen'));
    }

    // Formulaire d'avis
    public function edit(ExamenMedical $examen)
    {
        $this->authorize('evaluer', $examen);

        return view('evaluateur.edit', compact('examen'));
    }

    /**
     * Avis de l'évaluateur : valider, émettre une réserve ou une suggestion,
     * et éventuellement réduire la validité (jamais l'augmenter).
     */
    public function update(Request $request, ExamenMedical $examen, RapportMedicalService $rapports)
    {
        $this->authorize('evaluer', $examen);

        $request->validate($this->reglesAvis() + [
            'validite_evaluateur' => 'required|integer|min:1|max:' . (int) $examen->validite,
            'rapport_evaluateur' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Sans nouveau fichier, on garde le rapport déjà déposé.
        $rapportPath = $examen->rapport_evaluateur;
        if ($request->hasFile('rapport_evaluateur')) {
            $rapportPath = $rapports->stocker($request->file('rapport_evaluateur'), 'evaluateur');
        }

        $this->appliquerAvis($examen, [
            'avis_evaluateur' => $request->avis_evaluateur,
            'observations_evaluateur' => $request->observations_evaluateur,
            'validite_evaluateur' => $request->validite_evaluateur,
            'rapport_evaluateur' => $rapportPath,
        ]);

        return redirect()->route('evaluateur')->with('success', 'Examen médical mis à jour.');
    }

    /** Règles communes de l'avis : observations obligatoires en cas de réserve ou de suggestion. */
    private function reglesAvis(): array
    {
        return [
            'avis_evaluateur' => 'required|in:' . implode(',', ExamenMedical::AVIS),
            'observations_evaluateur' => 'required_unless:avis_evaluateur,valide|nullable|string|max:5000',
        ];
    }

    /** Enregistre l'avis au nom de l'évaluateur connecté (qui doit avoir une fiche évaluateur). */
    private function appliquerAvis(ExamenMedical $examen, array $donnees): void
    {
        $evaluateur = Auth::user()->evaluateur;
        abort_unless($evaluateur, 403, __('trans.fiche_evaluateur_absente'));

        $examen->update($donnees + ['evaluateur_id' => $evaluateur->id]);
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

        // Un rapport médical ne se valide qu'une fois l'avis de l'évaluateur donné.
        if ($table === 'examens_medicaux') {
            $examen = ExamenMedical::findOrFail($id);
            $this->authorize('evaluer', $examen);
            if (!$examen->avis_evaluateur) {
                return redirect()->back()->with('error', __('trans.avis_evaluateur_requis'));
            }
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
