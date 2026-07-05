<?php

namespace App\Http\Controllers;

use App\Models\Demandeur;
use App\Models\Licence;
use App\Models\Formation;
use App\Models\TypeFormation;
use App\Models\TypeLicence;
use App\Models\DispositifFormation;
use App\Models\CentreFormation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        
        // Statistiques
        $stats = [
            'total_formations' => Formation::where('instructeur_id', $demandeur->id)
                ->orWhere('examinateur_id', $demandeur->id)
                ->count(),
            'formations_a_venir' => Formation::where(function($q) use ($demandeur) {
                    $q->where('instructeur_id', $demandeur->id)
                      ->orWhere('examinateur_id', $demandeur->id);
                })
                ->where('date_formation', '>=', now())
                ->count(),
            'formations_passees' => Formation::where(function($q) use ($demandeur) {
                    $q->where('instructeur_id', $demandeur->id)
                      ->orWhere('examinateur_id', $demandeur->id);
                })
                ->where('date_formation', '<', now())
                ->count(),
            'total_stagiaires' => Formation::where(function($q) use ($demandeur) {
                    $q->where('instructeur_id', $demandeur->id)
                      ->orWhere('examinateur_id', $demandeur->id);
                })
                ->distinct('demandeur_id')
                ->count('demandeur_id'),
        ];
        
        $recentFormations = Formation::where(function($q) use ($demandeur) {
                $q->where('instructeur_id', $demandeur->id)
                  ->orWhere('examinateur_id', $demandeur->id);
            })
            ->with(['demandeur', 'typeFormation'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('user.demandeur.dashboard', compact('demandeur', 'stats', 'recentFormations'));
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
        
        // Vérifier si le demandeur peut attribuer des formations
        if (!$demandeur->is_instructeur && !$demandeur->is_examinateur) {
            return redirect()->route('demandeur.dashboard')
                ->with('error', trans('trans.not_authorized_to_assign_training'));
        }
        
        if($demandeur->is_instructeur){
            $typeFormations = TypeFormation::where('is_instructor', true)->get();
        }else{
            $typeFormations = TypeFormation::get();
        }
        
        $typeLicences = TypeLicence::get();
        
        // Centres de formation (si nécessaire)
        $centres = CentreFormation::get();
        
        return view('user.demandeur.assign-training', compact('demandeur', 'typeFormations', 'typeLicences', 'centres'));
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
                'is_examinateur' => $demandeur->is_examinateur,
            ]
        ]);
    }

    /**
     * Enregistrer la formation attribuée
     */
    public function storeFormation(Request $request)
    {
        $request->validate([
            'demandeur_id' => 'required|exists:demandeurs,id',
            'type_formation_id' => 'required|exists:type_formations,id',
            'type_licence_id' => 'nullable|exists:type_licences,id',
            'intitule_formation' => 'nullable|string|max:255',
            'date_formation' => 'required|date',
            'lieu' => 'nullable|string|max:255',
            'dispositif_formation_id' => 'nullable|exists:dispositif_formations,id',
            'attestation' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
        
        $user = Auth::user();
        $formateurDemandeur = $user->demandeur;
        
        if (!$formateurDemandeur || (!$formateurDemandeur->is_instructeur && !$formateurDemandeur->is_examinateur)) {
            return redirect()->back()->with('error', trans('trans.not_authorized_to_assign_training'));
        }
        
        DB::beginTransaction();
        
        try {
            // Upload de l'attestation
            $attestationPath = $request->file('attestation')->store('attestations_formation', 'public');
            
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
                'instructeur_id' => $formateurDemandeur->is_instructeur ? $formateurDemandeur->id : null,
                'examinateur_id' => $formateurDemandeur->is_examinateur ? $formateurDemandeur->id : null,
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
            \Log::error('Erreur lors de la création de la formation: ' . $e->getMessage());
            
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
        
        $query = Formation::where(function($q) use ($demandeur) {
            $q->where('instructeur_id', $demandeur->id)
              ->orWhere('examinateur_id', $demandeur->id);
        });
        
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
        
        $formations = $query->with(['demandeur', 'typeFormation', 'instructeur', 'examinateur'])
            ->orderBy('date_formation', 'desc')
            ->paginate(20);
        
        $stats = [
            'total' => Formation::where(function($q) use ($demandeur) {
                $q->where('instructeur_id', $demandeur->id)
                  ->orWhere('examinateur_id', $demandeur->id);
            })->count(),
            'planifiees' => Formation::where('status', 'planifiee')
                ->where(function($q) use ($demandeur) {
                    $q->where('instructeur_id', $demandeur->id)
                      ->orWhere('examinateur_id', $demandeur->id);
                })->count(),
            'terminees' => Formation::where('status', 'terminee')
                ->where(function($q) use ($demandeur) {
                    $q->where('instructeur_id', $demandeur->id)
                      ->orWhere('examinateur_id', $demandeur->id);
                })->count(),
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
        
        $formation = Formation::with(['demandeur', 'typeFormation', 'typeLicence', 'instructeur', 'examinateur', 'dispositifFormation'])
            ->findOrFail($id);
        
        // Vérifier l'autorisation
        if ($formation->instructeur_id != $demandeur->id && 
            $formation->examinateur_id != $demandeur->id) {
            return redirect()->back()->with('error', trans('trans.unauthorized'));
        }
        
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
        
        $formation = Formation::findOrFail($id);
        
        // Vérifier l'autorisation
        if ($formation->instructeur_id != $demandeur->id && $formation->examinateur_id != $demandeur->id) {
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
                \Log::info('Notification WhatsApp à envoyer à: ' . $formation->demandeur->user->whatsapp);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur envoi notification: ' . $e->getMessage());
        }
    }
}