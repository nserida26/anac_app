<?php 
namespace App\Http\Controllers;
use App\Models\Formation;
use App\Models\TypeFormation;
use App\Models\TypeLicence;
use App\Models\Instructeur;
use App\Models\ExaminateurCentre;
use App\Models\DispositifFormation;
use App\Models\CentreFormation;
use App\Models\Demandeur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


use App\Models\CentreLicence;
use App\Models\Licence;
use App\Models\LicenceDemandeur;

use App\Models\Simulateur;
class CentreController extends Controller
{

public function index(Request $request)
{
    $user = Auth::user();
    $centre = CentreFormation::where('user_id', $user->id)->first();
    
    if (!$centre) {
        return redirect()->route('centre.profile')->with('error', __('trans.create_centre_first'));
    }
    
    // Statistiques
    $totalFormations = Formation::where('centre_formation_id', $centre->id)->count();
    $totalInstructeurs = Instructeur::where('centre_formation_id', $centre->id)
                                    ->where('statut', 'actif')
                                    ->count();
    $totalExaminateurs = ExaminateurCentre::where('centre_formation_id', $centre->id)
                                          ->where('statut_validation', 'valide')
                                          ->where('date_fin_validite', '>=', now())
                                          ->count();
    $totalDispositifs = DispositifFormation::where('centre_formation_id', $centre->id)
                                           ->where('statut', 'operationnel')
                                           ->count();
    
    $formations = Formation::with(['typeFormation', 'typeLicence', 'instructeur', 'examinateur', 'dispositifFormation.simulateur', 'demandeur.user'])
                           ->where('centre_formation_id', $centre->id)
                           ->latest()
                           ->paginate(10);
    
    return view('centre.index', compact(
        'formations',
        'centre',
        'totalFormations',
        'totalInstructeurs',
        'totalExaminateurs',
        'totalDispositifs'
    ));
}

public function create(Request $request)
{
    $user = Auth::user();
    $centre = CentreFormation::where('user_id', $user->id)->first();
    
    if (!$centre) {
        return redirect()->route('centre.profile')->with('error', __('trans.create_centre_first'));
    }
    
    // Récupérer le demandeur présélectionné si présent
    $preselectedDemandeur = null;
    if ($request->has('demandeur_id')) {
        $preselectedDemandeur = Demandeur::with(['user', 'licences', 'licenceDemandeurs'])
            ->find($request->demandeur_id);
    }
    
    $typeFormations = TypeFormation::all();
    
    $typeLicences = TypeLicence::get();
    
    $instructeurs = Instructeur::where('centre_formation_id', $centre->id)
                               ->where('statut', 'actif')
                               ->get();
    
    $examinateurs = ExaminateurCentre::where('centre_formation_id', $centre->id)
                                     ->where('statut_validation', 'valide')
                                     ->where('date_fin_validite', '>=', now())
                                     ->get();
    
    $dispositifs = DispositifFormation::where('centre_formation_id', $centre->id)
                                      ->where('statut', 'operationnel')
                                      ->with('simulateur')
                                      ->get();
    
    return view('centre.formations.create', compact(
        'centre',
        'preselectedDemandeur',
        'typeFormations',
        'typeLicences',
        'instructeurs',
        'examinateurs',
        'dispositifs'
    ));
}



public function store(Request $request)
{
    
    $validated = $request->validate([
        'centre_formation_id' => 'required|exists:centre_formations,id',
        'demandeur_id' => 'required|exists:demandeurs,id',
        'type_formation_id' => 'required|exists:type_formations,id',
        'type_licence_id' => 'nullable|exists:type_licences,id',
        'intitule_formation' => 'nullable|string|max:255',
        'instructeur_id' => 'nullable|exists:instructeurs,id',
        'examinateur_id' => 'nullable|exists:examinateurs_centre,id',
        'dispositif_formation_id' => 'nullable|exists:dispositifs_formation,id',
        'date_formation' => 'required|date',
        'lieu' => 'nullable|string|max:255',
        'attestation' => 'required|file|mimes:pdf|max:10240'
    ]);

    try {
        

        // Vérifier que le demandeur a une licence
        $demandeur = Demandeur::with('licence')->findOrFail($validated['demandeur_id']);

        if (!$demandeur->licence) {
            return back()
                ->withInput()
                ->with('error', __('trans.demandeur_has_no_licence'));
        }

        // Upload fichier
        if ($request->hasFile('attestation')) {
            $validated['attestation'] = $request->file('attestation')
                ->store('formations/attestations', 'public');
        }
        
        // Création directe
        $formation = Formation::create($validated);
 
        

        return redirect()->route('centre.index')
            ->with('success', __('trans.formation_added_successfully'));

    } catch (\Exception $e) {
        
        return back()
            ->withInput()
            ->with('error', __('trans.error_adding_formation'));
    }
}

    
    // Gestion des instructeurs
    public function instructeurs()
    {
        $user = Auth::user();
        $centre = CentreFormation::where('user_id', $user->id)->first();
        
        $instructeurs = Instructeur::where('centre_formation_id', $centre->id)
                                   ->latest()
                                   ->paginate(10);
        
        return view('centre.instructeurs.index', compact('instructeurs', 'centre'));
    }
    
    public function storeInstructeur(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:instructeurs,email',
            'telephone' => 'required|string|max:20',
            'numero_licence' => 'required|string|max:50',
            'date_naissance' => 'required|date',
            'nationalite' => 'required|string|max:100',
            'adresse' => 'required|string',
            'document_justificatif' => 'required|file|mimes:pdf|max:10240',
            'qualifications' => 'nullable|array'
        ]);
        
        try {
            $user = Auth::user();
            $centre = CentreFormation::where('user_id', $user->id)->first();
            
            $instructeur = new Instructeur();
            $instructeur->centre_formation_id = $centre->id;
            $instructeur->nom = $request->nom;
            $instructeur->prenom = $request->prenom;
            $instructeur->email = $request->email;
            $instructeur->telephone = $request->telephone;
            $instructeur->numero_licence = $request->numero_licence;
            $instructeur->date_naissance = $request->date_naissance;
            $instructeur->nationalite = $request->nationalite;
            $instructeur->adresse = $request->adresse;
            $instructeur->qualifications = $request->qualifications;
            
            if ($request->hasFile('document_justificatif')) {
                $path = $request->file('document_justificatif')->store('instructeurs/documents', 'public');
                $instructeur->document_justificatif = $path;
            }
            
            $instructeur->save();
            
            return redirect()->route('centre.instructeurs')
                             ->with('success', __('trans.instructeur_added_successfully'));
                             
        } catch (\Exception $e) {
            return back()->withInput()
                         ->with('error', __('trans.error_adding_instructeur') . ': ' . $e->getMessage());
        }
    }
    
    // Gestion des examinateurs
    public function examinateurs()
    {
        $user = Auth::user();
        $centre = CentreFormation::where('user_id', $user->id)->first();
        
        $examinateurs = ExaminateurCentre::where('centre_formation_id', $centre->id)
                                         ->latest()
                                         ->paginate(10);
        
        return view('centre.examinateurs.index', compact('examinateurs', 'centre'));
    }
    
    public function storeExaminateur(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:examinateurs_centre,email',
            'telephone' => 'required|string|max:20',
            'numero_licence_examinateur' => 'required|string|max:50',
            'date_naissance' => 'required|date',
            'nationalite' => 'required|string|max:100',
            'adresse' => 'required|string',
            'document_justificatif' => 'required|file|mimes:pdf|max:10240',
            'date_debut_validite' => 'required|date',
            'date_fin_validite' => 'required|date|after:date_debut_validite'
        ]);
        
        try {
            $user = Auth::user();
            $centre = CentreFormation::where('user_id', $user->id)->first();
            
            $examinateur = new ExaminateurCentre();
            $examinateur->centre_formation_id = $centre->id;
            $examinateur->nom = $request->nom;
            $examinateur->prenom = $request->prenom;
            $examinateur->email = $request->email;
            $examinateur->telephone = $request->telephone;
            $examinateur->numero_licence_examinateur = $request->numero_licence_examinateur;
            $examinateur->date_naissance = $request->date_naissance;
            $examinateur->nationalite = $request->nationalite;
            $examinateur->adresse = $request->adresse;
            $examinateur->date_debut_validite = $request->date_debut_validite;
            $examinateur->date_fin_validite = $request->date_fin_validite;
            
            if ($request->hasFile('document_justificatif')) {
                $path = $request->file('document_justificatif')->store('examinateurs/documents', 'public');
                $examinateur->document_justificatif = $path;
            }
            
            $examinateur->save();
            
            return redirect()->route('centre.examinateurs')
                             ->with('success', __('trans.examinateur_added_successfully'));
                             
        } catch (\Exception $e) {
            return back()->withInput()
                         ->with('error', __('trans.error_adding_examinateur') . ': ' . $e->getMessage());
        }
    }
    
    // Gestion des dispositifs de formation
    public function dispositifs()
    {
        $user = Auth::user();
        $centre = CentreFormation::where('user_id', $user->id)->first();
        
        $dispositifs = DispositifFormation::where('centre_formation_id', $centre->id)
                                          ->with('simulateur')
                                          ->latest()
                                          ->paginate(10);
        
        $simulateurs = Simulateur::all();
        
        return view('centre.dispositifs.index', compact('dispositifs', 'centre', 'simulateurs'));
    }
    
    public function storeDispositif(Request $request)
    {
        $request->validate([
            'simulateur_id' => 'required|exists:simulateurs,id',
            'date_acquisition' => 'required|date',
            'date_derniere_certification' => 'required|date',
            'date_expiration_certification' => 'required|date|after:date_derniere_certification',
            'certificat_document' => 'required|file|mimes:pdf|max:10240',
            'notes' => 'nullable|string'
        ]);
        
        try {
            $user = Auth::user();
            $centre = CentreFormation::where('user_id', $user->id)->first();
            
            $dispositif = new DispositifFormation();
            $dispositif->centre_formation_id = $centre->id;
            $dispositif->simulateur_id = $request->simulateur_id;
            $dispositif->date_acquisition = $request->date_acquisition;
            $dispositif->date_derniere_certification = $request->date_derniere_certification;
            $dispositif->date_expiration_certification = $request->date_expiration_certification;
            $dispositif->notes = $request->notes;
            
            if ($request->hasFile('certificat_document')) {
                $path = $request->file('certificat_document')->store('dispositifs/certificats', 'public');
                $dispositif->certificat_document = $path;
            }
            
            $dispositif->save();
            
            return redirect()->route('centre.dispositifs')
                             ->with('success', __('trans.dispositif_added_successfully'));
                             
        } catch (\Exception $e) {
            return back()->withInput()
                         ->with('error', __('trans.error_adding_dispositif') . ': ' . $e->getMessage());
        }
    }


public function searchDemandeurs(Request $request)
{
    $search = $request->get('search');
    $page = $request->get('page', 1);
    
    $query = Demandeur::with(['user', 'licences' => function($q) {
        $q->latest();
    }]);
    
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('np', 'LIKE', "%{$search}%")
              ->orWhereHas('user', function($q) use ($search) {
                  $q->where('email', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('licence', function($q) use ($search) {
                  $q->where('numero_licence', 'LIKE', "%{$search}%")
                    ->orWhere('num_licence', 'LIKE', "%{$search}%");
              });
        });
    }
    
    $demandeurs = $query->paginate(10, ['*'], 'page', $page);
    
    $results = $demandeurs->map(function($demandeur) {
        $licence = $demandeur->licences->first();
        $licenceNumber = null;
        
        if ($licence) {
            // Chercher dans les différentes tables de licences
            if ($licence instanceof Licence) {
                $licenceNumber = $licence->numero_licence;
            } elseif ($licence instanceof LicenceDemandeur) {
                $licenceNumber = $licence->num_licence;
            }
        }
        
        return [
            'id' => $demandeur->id,
            'np' => $demandeur->np,
            'email' => $demandeur->user->email ?? null,
            'licence_number' => $licenceNumber
        ];
    });
    
    return response()->json([
        'results' => $results,
        'pagination' => [
            'more' => $demandeurs->hasMorePages()
        ]
    ]);
}

// app/Http/Controllers/CentreController.php

public function searchByLicence(Request $request)
{
    $licenceNumber = $request->get('licence_number');
    
    if (strlen($licenceNumber) < 3) {
        return response()->json([
            'success' => false,
            'message' => __('trans.enter_at_least_3_characters')
        ]);
    }
    
    try {
        // Rechercher directement dans la table licences
        $licences = Licence::where('numero_licence', 'LIKE', "%{$licenceNumber}%")
            ->with(['demandeur.user'])
            ->get();
        
        $results = [];
        
        foreach ($licences as $licence) {
            if ($licence->demandeur) {
                $results[] = [
                    'id' => $licence->demandeur->id,
                    'np' => $licence->demandeur->np,
                    'email' => $licence->demandeur->user->email ?? null,
                    'licence_number' => $licence->numero_licence,
                    'date_naissance' => $licence->demandeur->date_naissance ? $licence->demandeur->date_naissance : null,
                    'nationalite' => $licence->demandeur->nationalite ?? 'N/A'
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'demandeurs' => $results
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Erreur recherche par licence: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => __('trans.error_searching_demandeur'),
            'demandeurs' => []
        ]);
    }
}

public function getDemandeurDetails(Request $request)
{
    $demandeurId = $request->get('demandeur_id');
    
    $demandeur = Demandeur::with(['user', 'licence'])->find($demandeurId);
    
    if (!$demandeur) {
        return response()->json([
            'success' => false,
            'message' => __('trans.demandeur_not_found')
        ]);
    }
    
    // Préparer les informations de la licence
    $licenceInfo = null;
    if ($demandeur->licence) {
        $licenceInfo = [
            'numero_licence' => $demandeur->licence->numero_licence,
            'categorie' => $demandeur->licence->categorie_licence ?? null,
            'type_licence' => $demandeur->licence->type_licence ?? null,
            'date_deliverance' => $demandeur->licence->date_deliverance ? $demandeur->licence->date_deliverance : null,
            'date_expiration' => $demandeur->licence->date_expiration ? $demandeur->licence->date_expiration : null,
            'machine_licence' => $demandeur->licence->machine_licence ?? null
        ];
    }
    
    return response()->json([
        'success' => true,
        'demandeur' => [
            'id' => $demandeur->id,
            'np' => $demandeur->np,
            'date_naissance' => $demandeur->date_naissance ? $demandeur->date_naissance : null,
            'lieu_naissance' => $demandeur->lieu_naissance,
            'nationalite' => $demandeur->nationalite,
            'adresse' => $demandeur->adresse,
            'adresse_employeur' => $demandeur->adresse_employeur,
            'user' => [
                'email' => $demandeur->user->email ?? null
            ],
            'licence' => $licenceInfo
        ]
    ]);
}
// app/Http/Controllers/CentreController.php

public function show($id)
{
    $user = Auth::user();
    $centre = CentreFormation::where('user_id', $user->id)->first();
    
    if (!$centre) {
        return redirect()->route('centre.index')->with('error', __('trans.create_centre_first'));
    }
    
    // Récupérer la formation avec toutes ses relations
    $formation = Formation::with([
        'typeFormation',
        'typeLicence',
        'instructeur',
        'examinateur',
        'dispositifFormation.simulateur',
        'demandeur.user',
        'demandeur.licence',
        'centreFormation'
    ])->where('centre_formation_id', $centre->id)
      ->findOrFail($id);
    
    return view('centre.formations.show', compact('formation', 'centre'));
}
// app/Http/Controllers/CentreController.php

public function licences()
{
    $user = Auth::user();
    $centre = CentreFormation::where('user_id', $user->id)->first();
    
    if (!$centre) {
        return redirect()->route('centre.index')->with('error', __('trans.create_centre_first'));
    }
    
    $licences = CentreLicence::where('centre_formation_id', $centre->id)
        ->with('typeLicence')
        ->orderBy('date_expiration', 'desc')
        ->paginate(10);
    
    $typeLicences = TypeLicence::all();
    
    return view('centre.licences.index', compact('licences', 'centre', 'typeLicences'));
}

public function storeLicence(Request $request)
{
    $request->validate([
        'type_licence_id' => 'required|exists:type_licences,id',
        'date_obtention' => 'required|date',
        'date_expiration' => 'required|date|after:date_obtention',
        'document_justificatif' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
    ]);
    
    try {
        $user = Auth::user();
        $centre = CentreFormation::where('user_id', $user->id)->first();
        
        if (!$centre) {
            return back()->with('error', __('trans.centre_not_found'));
        }
        
        $licence = new CentreLicence();
        $licence->centre_formation_id = $centre->id;
        $licence->type_licence_id = $request->type_licence_id;
        $licence->date_obtention = $request->date_obtention;
        $licence->date_expiration = $request->date_expiration;
        $licence->statut = 'actif';
        
        if ($request->hasFile('document_justificatif')) {
            $path = $request->file('document_justificatif')->store('centre/licences', 'public');
            $licence->document_justificatif = $path;
        }
        
        $licence->save();
        
        return redirect()->route('centre.licences')
            ->with('success', __('trans.licence_added_successfully'));
            
    } catch (\Exception $e) {
        \Log::error('Erreur ajout licence centre: ' . $e->getMessage());
        
        return back()->withInput()
            ->with('error', __('trans.error_adding_licence') . ': ' . $e->getMessage());
    }
}

public function editLicence($id)
{
    $user = Auth::user();
    $centre = CentreFormation::where('user_id', $user->id)->first();
    
    $licence = CentreLicence::where('centre_formation_id', $centre->id)
        ->findOrFail($id);
    
    $typeLicences = TypeLicence::all();
    
    return view('centre.licences.edit', compact('licence', 'typeLicences'));
}

public function updateLicence(Request $request, $id)
{
    $request->validate([
        'type_licence_id' => 'required|exists:type_licences,id',
        'date_obtention' => 'required|date',
        'date_expiration' => 'required|date|after:date_obtention',
        'document_justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
    ]);
    
    try {
        $user = Auth::user();
        $centre = CentreFormation::where('user_id', $user->id)->first();
        
        $licence = CentreLicence::where('centre_formation_id', $centre->id)
            ->findOrFail($id);
        
        $licence->type_licence_id = $request->type_licence_id;
        $licence->date_obtention = $request->date_obtention;
        $licence->date_expiration = $request->date_expiration;
        
        // Mise à jour automatique du statut
        if ($request->date_expiration < now()) {
            $licence->statut = 'expire';
        } else {
            $licence->statut = 'actif';
        }
        
        if ($request->hasFile('document_justificatif')) {
            // Supprimer l'ancien document
            if ($licence->document_justificatif) {
                Storage::disk('public')->delete($licence->document_justificatif);
            }
            
            $path = $request->file('document_justificatif')->store('centre/licences', 'public');
            $licence->document_justificatif = $path;
        }
        
        $licence->save();
        
        return redirect()->route('centre.licences')
            ->with('success', __('trans.licence_updated_successfully'));
            
    } catch (\Exception $e) {
        \Log::error('Erreur modification licence centre: ' . $e->getMessage());
        
        return back()->withInput()
            ->with('error', __('trans.error_updating_licence') . ': ' . $e->getMessage());
    }
}

public function destroyLicence($id)
{
    try {
        $user = Auth::user();
        $centre = CentreFormation::where('user_id', $user->id)->first();
        
        $licence = CentreLicence::where('centre_formation_id', $centre->id)
            ->findOrFail($id);
        
        // Supprimer le document
        if ($licence->document_justificatif) {
            Storage::disk('public')->delete($licence->document_justificatif);
        }
        
        $licence->delete();
        
        return response()->json([
            'success' => true,
            'message' => __('trans.licence_deleted_successfully')
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => __('trans.error_deleting_licence')
        ], 500);
    }
}
}