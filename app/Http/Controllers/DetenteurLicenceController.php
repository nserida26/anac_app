<?php

namespace App\Http\Controllers;

use App\Models\Demandeur;
use App\Models\Licence;
use App\Models\Formation;
use App\Models\TypeFormation;
use App\Models\TypeLicence;
use App\Models\DispositifFormation;
use App\Models\CentreFormation;
use App\Services\DesignationExaminateurService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DetenteurLicenceController extends Controller
{

    /**
     * Dashboard du demandeur (instructeur/examinateur)
     */
    public function dashboard()
    {
        $user = Auth::user();
        $demandeur = $user->demandeur;
        
        if (!$demandeur) {
            return redirect()->back()->with('error', trans('trans.demandeur_not_found'));
        }
        
        // Statistiques (formations enregistrées par ce détenteur, en tant qu'instructeur ou examinateur)
        $stats = [
            'total_formations' => Formation::duFormateur($demandeur)->count(),
            'formations_a_venir' => Formation::duFormateur($demandeur)->where('date_formation', '>=', now())->count(),
            'formations_passees' => Formation::duFormateur($demandeur)->where('date_formation', '<', now())->count(),
            'total_stagiaires' => Formation::duFormateur($demandeur)->distinct('demandeur_id')->count('demandeur_id'),
        ];

        $recentFormations = Formation::duFormateur($demandeur)
            ->with(['demandeur', 'typeFormation'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $designations = $demandeur->designationsExaminateur()->enVigueur()->with('typesLicence')->get();

        return view('user.demandeur.dashboard', compact('demandeur', 'stats', 'recentFormations', 'designations'));
    }

    /**
     * Formulaire d'attribution de formation
     */
    public function createFormation()
    {
        $user = Auth::user();
        $demandeur = $user->demandeur;

        if (!$demandeur) {
            return redirect()->back()->with('error', trans('trans.demandeur_not_found'));
        }

        // Instructeur, ou examinateur désigné par l'ANAC (désignation en vigueur)
        if (!$demandeur->estFormateur()) {
            return redirect()->route('demandeur.dashboard')
                ->with('error', trans('trans.not_authorized_to_assign_training'));
        }

        $designations = $demandeur->designationsExaminateur()->enVigueur()->with('typesLicence')->get();
        $qualites = array_values(array_filter([
            $demandeur->is_instructeur ? 'instructeur' : null,
            $designations->isNotEmpty() ? 'examinateur' : null,
        ]));

        // Types de formation : ceux d'instructeur, plus tous les autres pour un examinateur désigné.
        $typeFormations = $designations->isNotEmpty() ? TypeFormation::get() : TypeFormation::where('is_instructor', true)->get();

        $typeLicences = TypeLicence::get();
        // Types de licence pour lesquels il est désigné examinateur
        $typesLicenceDesignes = $designations->flatMap->typesLicence->unique('id')->values();

        // Centres de formation (si nécessaire)
        $centres = CentreFormation::get();

        return view('user.demandeur.assign-training', compact('demandeur', 'typeFormations', 'typeLicences', 'centres', 'qualites', 'typesLicenceDesignes'));
    }

    /**
     * Recherche de demandeurs par numéro de licence
     */
    public function searchByLicence(Request $request)
    {
        $request->validate([
            'licence_number' => 'required|string|min:2'
        ]);
        
        $searchTerm = $request->licence_number;
        
        // Rechercher les licences qui correspondent
        $licences = Licence::where('numero_licence', 'LIKE', "%{$searchTerm}%")
            ->with('demandeur')
            ->get();
        
        $demandeurs = [];
        
        foreach ($licences as $licence) {
            if ($licence->demandeur) {
                // Exclure le demandeur actuel s'il essaye de s'attribuer une formation à lui-même
                $currentDemandeur = Auth::user()->demandeur;
                if ($currentDemandeur && $licence->demandeur->id == $currentDemandeur->id) {
                    continue;
                }
                
                $demandeurs[] = [
                    'id' => $licence->demandeur->id,
                    'np' => $licence->demandeur->np,
                    'licence_number' => $licence->numero_licence,
                    'licence_type' => $licence->type_licence,
                    'categorie_licence' => $licence->categorie_licence,
                    'date_naissance' => $licence->demandeur->date_naissance,
                    'nationalite' => $licence->demandeur->nationalite,
                    'photo' => $licence->demandeur->photo,
                    'licence_expiration' => $licence->date_expiration,
                ];
            }
        }
        
        // Rechercher aussi par nom si pas de résultat
        if (empty($demandeurs)) {
            $demandeursByName = Demandeur::where('np', 'LIKE', "%{$searchTerm}%")
                ->with('licence')
                ->get();
            
            foreach ($demandeursByName as $demandeur) {
                $currentDemandeur = Auth::user()->demandeur;
                if ($currentDemandeur && $demandeur->id == $currentDemandeur->id) {
                    continue;
                }
                
                $demandeurs[] = [
                    'id' => $demandeur->id,
                    'np' => $demandeur->np,
                    'licence_number' => $demandeur->licence ? $demandeur->licence->numero_licence : trans('trans.no_licence'),
                    'licence_type' => $demandeur->licence ? $demandeur->licence->type_licence : 'N/A',
                    'categorie_licence' => $demandeur->licence ? $demandeur->licence->categorie_licence : 'N/A',
                    'date_naissance' => $demandeur->date_naissance,
                    'nationalite' => $demandeur->nationalite,
                    'photo' => $demandeur->photo,
                    'licence_expiration' => $demandeur->licence ? $demandeur->licence->date_expiration : null,
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'demandeurs' => $demandeurs
        ]);
    }

    /**
     * Récupérer les détails d'un demandeur
     */
    public function getDemandeurDetails(Request $request)
    {
        $request->validate([
            'demandeur_id' => 'required|exists:demandeurs,id'
        ]);
        
        $demandeur = Demandeur::with(['licence', 'user'])->find($request->demandeur_id);
        
        if (!$demandeur) {
            return response()->json([
                'success' => false,
                'message' => trans('trans.demandeur_not_found')
            ]);
        }
        
        $licenceData = null;
        if ($demandeur->licence) {
            $licenceData = [
                'id' => $demandeur->licence->id,
                'numero_licence' => $demandeur->licence->numero_licence,
                'type_licence' => $demandeur->licence->type_licence,
                'categorie_licence' => $demandeur->licence->categorie_licence,
                'machine_licence' => $demandeur->licence->machine_licence,
                'date_deliverance' => $demandeur->licence->date_deliverance ? $demandeur->licence->date_deliverance->format('d/m/Y') : null,
                'date_expiration' => $demandeur->licence->date_expiration ? $demandeur->licence->date_expiration->format('d/m/Y') : null,
            ];
        }
        
        return response()->json([
            'success' => true,
            'demandeur' => [
                'id' => $demandeur->id,
                'np' => $demandeur->np,
                'date_naissance' => $demandeur->date_naissance,
                'lieu_naissance' => $demandeur->lieu_naissance,
                'adresse' => $demandeur->adresse,
                'adresse_employeur' => $demandeur->adresse_employeur,
                'nationalite' => $demandeur->nationalite,
                'photo' => $demandeur->photo ? asset('uploads/' . $demandeur->photo) : null,
                'user' => $demandeur->user ? [
                    'email' => $demandeur->user->email,
                    'whatsapp' => $demandeur->user->whatsapp ?? null,
                ] : null,
                'licence' => $licenceData,
                'is_instructeur' => $demandeur->is_instructeur,
                'is_examinateur' => $demandeur->estExaminateurDesigne(),
            ]
        ]);
    }

    /**
     * Enregistrer la formation attribuée
     */
    public function storeFormation(Request $request, DesignationExaminateurService $designations)
    {
        $request->validate([
            'demandeur_id' => 'required|exists:demandeurs,id',
            'qualite_formateur' => 'required|in:' . implode(',', Formation::QUALITES_FORMATEUR),
            'type_formation_id' => 'required|exists:type_formations,id',
            // Un examen se rattache à un type de licence pour lequel l'examinateur est désigné.
            'type_licence_id' => 'nullable|required_if:qualite_formateur,examinateur|exists:type_licences,id',
            'intitule_formation' => 'nullable|string|max:255',
            'date_formation' => 'required|date',
            'lieu' => 'nullable|string|max:255',
            'dispositif_formation_id' => 'nullable|exists:dispositifs_formation,id',
            'centre_formation_id' => 'nullable|exists:centre_formations,id',
            'attestation' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            // Rapport d'examen obligatoire dès que l'on agit en tant qu'examinateur.
            'rapport' => 'nullable|required_if:qualite_formateur,examinateur|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $user = Auth::user();
        $formateurDemandeur = $user->demandeur;

        if (!$formateurDemandeur || !$formateurDemandeur->estFormateur()) {
            return redirect()->back()->with('error', trans('trans.not_authorized_to_assign_training'));
        }

        $refus = $designations->verifierFormation($formateurDemandeur, $request->qualite_formateur, $request->date_formation, $request->type_licence_id ? (int) $request->type_licence_id : null);
        if ($refus) {
            return redirect()->back()->withInput()->with('error', $refus);
        }

        DB::beginTransaction();

        try {
            // Upload de l'attestation (et du rapport d'examen)
            $attestationPath = $request->file('attestation')->store('attestations_formation', 'public');
            $rapportPath = $request->hasFile('rapport') ? $request->file('rapport')->store('rapports_formation', 'public') : null;

            // Créer la formation
            $formation = Formation::create([
                'demandeur_id' => $request->demandeur_id, // Le stagiaire
                'type_formation_id' => $request->type_formation_id,
                'type_licence_id' => $request->type_licence_id,
                'intitule_formation' => $request->intitule_formation,
                'date_formation' => $request->date_formation,
                'lieu' => $request->lieu,
                'dispositif_formation_id' => $request->dispositif_formation_id,
                'attestation' => $attestationPath,
                'rapport' => $rapportPath,
                'formateur_demandeur_id' => $formateurDemandeur->id,
                'qualite_formateur' => $request->qualite_formateur,
                'centre_formation_id' => $request->centre_formation_id,
                'status' => 'planifiee',
            ]);
            
            DB::commit();
            
            // Optionnel: Envoyer une notification WhatsApp
            $this->sendTrainingNotification($formation);
            
            return redirect()->route('demandeur.formations.list')
                ->with('success', trans('trans.training_assigned_successfully') . ' #' . $formation->id);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la formation: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', trans('trans.error_assigning_training') . ': ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Liste des formations attribuées
     */
    public function listFormations(Request $request)
    {
        $user = Auth::user();
        $demandeur = $user->demandeur;
        
        if (!$demandeur) {
            return redirect()->back()->with('error', trans('trans.demandeur_not_found'));
        }
        
        $query = Formation::duFormateur($demandeur);
        
        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('date_formation', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('date_formation', '<=', $request->date_to);
        }
        
        $formations = $query->with(['demandeur', 'typeFormation', 'typeLicence'])
            ->orderBy('date_formation', 'desc')
            ->paginate(20);

        $stats = [
            'total' => Formation::duFormateur($demandeur)->count(),
            'planifiees' => Formation::duFormateur($demandeur)->where('status', 'planifiee')->count(),
            'terminees' => Formation::duFormateur($demandeur)->where('status', 'terminee')->count(),
        ];
        
        return view('user.demandeur.formations-list', compact('formations', 'demandeur', 'stats'));
    }
    
    /**
     * Détails d'une formation
     */
    public function showFormation($id)
    {
        $user = Auth::user();
        $demandeur = $user->demandeur;
        
        // Uniquement les formations que ce détenteur a lui-même enregistrées
        $formation = Formation::duFormateur($demandeur)
            ->with(['demandeur', 'typeFormation', 'typeLicence', 'dispositifFormation'])
            ->findOrFail($id);
        
        return view('user.demandeur.formation-details', compact('formation', 'demandeur'));
    }
    
    /**
     * Modifier le statut d'une formation
     */
    public function updateFormationStatus(Request $request, $id)
    {
        
        $request->validate([
            'status' => 'required|in:planifiee,en_cours,terminee,annulee'
        ]);
        
        $user = Auth::user();
        $demandeur = $user->demandeur;
        
        // Uniquement les formations que ce détenteur a lui-même enregistrées
        $formation = Formation::duFormateur($demandeur)->find($id);
        if (!$formation) {
            return response()->json(['success' => false, 'message' => trans('trans.unauthorized')], 403);
        }
        
        $formation->update(['status' => $request->status]);
        
        return response()->json([
            'success' => true,
            'message' => trans('trans.status_updated_successfully'),
            'status' => $request->status
        ]);
    }
    
    /**
     * Envoyer une notification WhatsApp
     */
    private function sendTrainingNotification($formation)
    {
        try {
            if ($formation->demandeur && $formation->demandeur->user && $formation->demandeur->user->whatsapp) {
                // Logique d'envoi de notification WhatsApp
                // Vous pouvez implémenter votre service WhatsApp ici
                Log::info('Notification WhatsApp à envoyer à: ' . $formation->demandeur->user->whatsapp);
            }
        } catch (\Exception $e) {
            Log::error('Erreur envoi notification: ' . $e->getMessage());
        }
    }
}