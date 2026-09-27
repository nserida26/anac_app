<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedecinCentreRequest;
use App\Models\CentreMedical;
use App\Models\Examinateur;
use App\Models\MedecinCentre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /** Centre d'expertise médicale du compte connecté ; refuse l'accès si le compte n'en a pas. */
    private function centreConnecte(): CentreMedical
    {
        $centre = CentreMedical::where('user_id', Auth::id())->first();
        abort_unless($centre, 403, __('trans.centre_medical_non_rattache'));

        return $centre;
    }
}
