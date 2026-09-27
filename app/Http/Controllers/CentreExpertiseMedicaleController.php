<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedecinCentreRequest;
use App\Models\CentreMedical;
use App\Models\Demandeur;
use App\Models\ExamenMedical;
use App\Models\Examinateur;
use App\Models\MedecinCentre;
use App\Services\RapportMedicalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Compte d'un centre d'expertise médicale (ex. CEMPA) : sur le modèle du compte
 * centre de formation, avec des médecins à la place des instructeurs et des
 * examinateurs médicaux (validés par l'ANAC) à la place des examinateurs.
 */
class CentreExpertiseMedicaleController extends Controller
{
    public function __construct()
    {
        // La mise en page commune des comptes centre affiche le menu « médical ».
        view()->share('espace', 'medical');
    }

    public function index()
    {
        $centre = $this->centreConnecte();

        $stats = [
            'medecins' => $centre->medecins()->where('statut', 'actif')->count(),
            'examinateurs_valides' => $centre->examinateursDeclares()->valide()->count(),
            'examinateurs_en_attente' => $centre->examinateursDeclares()->enAttente()->count(),
        ];
        $examinateurs = $centre->examinateursDeclares()->latest()->limit(5)->get();

        return view('centre_medical.index', compact('centre', 'stats', 'examinateurs'));
    }

    public function medecins()
    {
        $centre = $this->centreConnecte();
        $medecins = $centre->medecins()->latest()->paginate(10);

        return view('centre_medical.medecins.index', compact('centre', 'medecins'));
    }

    public function storeMedecin(StoreMedecinCentreRequest $request)
    {
        $centre = $this->centreConnecte();

        $medecin = new MedecinCentre(collect($request->validated())->except('document_justificatif')->all());
        $medecin->centre_medical_id = $centre->id;
        $medecin->statut = $request->input('statut', 'actif');
        $medecin->document_justificatif = $request->file('document_justificatif')->store('medecins/documents', 'public');
        $medecin->save();

        return redirect()->route('centre_medical.medecins')->with('success', __('trans.medecin_added_successfully'));
    }

    /** Même page que pour un centre de formation (vue partagée). */
    public function examinateurs()
    {
        $centre = $this->centreConnecte();
        $examinateurs = $centre->examinateursDeclares()->latest()->paginate(10);

        return view('centre.examinateurs.index', [
            'centre' => $centre,
            'examinateurs' => $examinateurs,
            'routeDeclaration' => route('centre_medical.examinateurs.store'),
            'libelleNumero' => 'trans.approval_number',
        ]);
    }

    public function storeExaminateur(Request $request)
    {
        $request->validate(Examinateur::reglesDeclaration());

        Examinateur::declarer($this->centreConnecte()->id, $request->all(), $request->file('document_justificatif'));

        return redirect()->route('centre_medical.examinateurs')->with('success', __('trans.examinateur_added_successfully'));
    }

    // ----- Rapports médicaux (rapport + attestation d'une visite) -----

    public function examens()
    {
        $centre = $this->centreConnecte();
        $examens = ExamenMedical::duCentre($centre)->with(['demandeur', 'examinateur'])->latest()->paginate(15);

        return view('centre_medical.examens.index', compact('centre', 'examens'));
    }

    public function createExamen()
    {
        $centre = $this->centreConnecte();

        return view('centre_medical.examens.create', [
            'centre' => $centre,
            'demandeurs' => Demandeur::orderBy('np')->get(['id', 'np', 'date_naissance']),
            'examinateurs' => $centre->examinateursDeclares()->valide()->orderBy('nom')->get(),
        ]);
    }

    public function storeExamen(Request $request, RapportMedicalService $rapports)
    {
        $centre = $this->centreConnecte();
        $donnees = $request->validate(RapportMedicalService::regles(true) + [
            'demandeur_id' => 'required|exists:demandeurs,id',
            'examinateur_id' => ['required', $this->regleExaminateurDuCentre($centre)],
        ]);

        $rapports->creer($donnees, $request->file('rapport'), $request->file('attestation'), Examinateur::findOrFail($donnees['examinateur_id']), $centre);

        return redirect()->route('centre_medical.examens')->with('success', __('trans.rapport_medical_enregistre'));
    }

    public function showExamen(ExamenMedical $examen)
    {
        $this->authorize('view', $examen);

        return view('centre_medical.examens.show', ['centre' => $this->centreConnecte(), 'examen' => $examen]);
    }

    public function editExamen(ExamenMedical $examen)
    {
        $this->authorize('update', $examen);
        $centre = $this->centreConnecte();

        return view('centre_medical.examens.edit', [
            'centre' => $centre,
            'examen' => $examen,
            'examinateurs' => $centre->examinateursDeclares()->valide()->orderBy('nom')->get(),
        ]);
    }

    public function updateExamen(Request $request, ExamenMedical $examen, RapportMedicalService $rapports)
    {
        $this->authorize('update', $examen);
        $centre = $this->centreConnecte();
        $donnees = $request->validate(RapportMedicalService::regles(false) + [
            'examinateur_id' => ['required', $this->regleExaminateurDuCentre($centre)],
        ]);

        $examen->examinateur_id = $donnees['examinateur_id'];
        $rapports->modifier($examen, $donnees, $request->file('rapport'), $request->file('attestation'));

        return redirect()->route('centre_medical.examens')->with('success', __('trans.rapport_medical_enregistre'));
    }

    public function destroyExamen(ExamenMedical $examen, RapportMedicalService $rapports)
    {
        $this->authorize('delete', $examen);
        $rapports->supprimer($examen);

        return redirect()->route('centre_medical.examens')->with('success', __('trans.rapport_medical_supprime'));
    }

    /** Transmet le rapport à l'ANAC (évaluateur) : il n'est plus modifiable ensuite. */
    public function transmettreExamen(ExamenMedical $examen)
    {
        $this->authorize('transmettre', $examen);
        $examen->update(['valider_examinateur' => true]);

        return redirect()->route('centre_medical.examens')->with('success', __('trans.rapport_medical_transmis'));
    }

    /** L'examinateur doit être l'un des examinateurs validés (validité en cours) de ce centre. */
    private function regleExaminateurDuCentre(CentreMedical $centre)
    {
        return Rule::exists('examinateurs', 'id')
            ->where('centre_medical_id', $centre->id)
            ->whereNull('user_id')
            ->where('statut_validation', 'valide')
            ->where(fn ($q) => $q->where('date_fin_validite', '>=', now()->toDateString()));
    }

    /** Centre d'expertise médicale du compte connecté ; refuse l'accès si le compte n'en a pas. */
    private function centreConnecte(): CentreMedical
    {
        $centre = CentreMedical::where('user_id', Auth::id())->first();
        abort_unless($centre, 403, __('trans.centre_medical_non_rattache'));

        return $centre;
    }
}
