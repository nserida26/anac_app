<?php

namespace App\Http\Controllers;

use App\Mail\SendFlightAuthorization;

use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\Vol;
use App\Models\EquipeVol;
use App\Models\FretVol;
use App\Models\ReceivingParty;
use App\Models\AssistanceEscalePea;
use App\Models\Mdn;
use App\Models\DocumentAutorisation;
use App\Models\Itineraire;
use App\Models\TypeDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Aeroport;
use App\Models\Autorisation;
use App\Models\Compagnie;
use App\Models\DemandeAutorisation;
use App\Models\EtatDemandeAutorisation;
use App\Models\PersonneDeces;

use App\Models\PaiementAutorisation;
use App\Models\Pays;
use App\Models\Proprietaire;

use App\Models\TypeAvion;
use App\Models\TypeDemandeAutorisation;
use App\Models\TypeDocumentAutorisation;
use App\Models\TypeVol;
use App\Models\User;
use App\Services\DtaApplicationNotificationService;
use App\Services\DtaAutorisationNotificationService;
use App\Services\DirectionNotificationService;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class DemandeAutorisationController extends Controller
{
    protected $dta;


    public function __construct(DtaAutorisationNotificationService $dta)
    {
        $this->dta = $dta;
    }
    public function autorisationPay($id)
    {
        $paiement = PaiementAutorisation::find($id);
        return view('user.autorisations.autorisationPay', compact('paiement'));
    }
    public function invoice($id)
    {

        //
        $paiement = PaiementAutorisation::find($id);

        return view('user.autorisations.invoice', compact('paiement'));
    }
/**
 * Enregistre une nouvelle demande d'autorisation
 *
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
 */
public function store(Request $request)
{
    try {
        // Vérifier le type de demande
        $typeId = (int) $request->type_demande_autorisation_id;
        
        // Pour le type 4, forcer type_vol_id à 1 (VOL CARGO)
        if ($typeId === 4) {
            $request->merge(['type_vol_id' => '1']);
        }
        
        // Définir les règles de validation de base
        $validationRules = [
            'type_demande_autorisation_id' => 'required|exists:type_demande_autorisations,id',
            'date_debut' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    $maxDate = now()->addYear();
                    if (strtotime($value) > strtotime($maxDate)) {
                        $fail('La date de début ne peut pas dépasser un an dans le futur.');
                    }
                },
            ],
            'date_fin' => [
                'required',
                'date',
                'after_or_equal:date_debut',
                function ($attribute, $value, $fail) use ($request) {
                    // Types exclus de la limite de 30 jours
                    $excludedTypes = [3, 5, 6, 7];
                    $type = (int) $request->type_demande_autorisation_id;
                    
                    if (!in_array($type, $excludedTypes)) {
                        $dateDebut = strtotime($request->date_debut);
                        $dateFin = strtotime($value);
                        $maxDuration = 30 * 24 * 60 * 60; // 30 jours en secondes
                        
                        if (($dateFin - $dateDebut) > $maxDuration) {
                            $fail('La durée de la demande ne peut pas dépasser 30 jours.');
                        }
                    }
                },
            ],
            'sous_validite' => 'nullable|integer|min:12|max:72',
            'objet' => 'nullable|string|max:500',
        ];
        
        // Validation du type_vol selon le type de demande
        if ($typeId === 3) {
            // Type 3 : multi-select (array)
            $validationRules['type_vol_id'] = 'required|array|min:1';
            $validationRules['type_vol_id.*'] = 'in:1,2'; // Seulement VOL CARGO et VOL CHARTER
        } elseif ($typeId === 4) {
            // Type 4 : automatiquement VOL CARGO (id=1)
            $validationRules['type_vol_id'] = 'required|in:1';
        } else {
            // Autres types : single select normal
            $validationRules['type_vol_id'] = 'required|exists:type_vols,id';
        }
        
        // Messages d'erreur personnalisés en français
        $messages = [
            'type_demande_autorisation_id.required' => 'Veuillez sélectionner un type d\'autorisation.',
            'type_demande_autorisation_id.exists' => 'Le type d\'autorisation sélectionné n\'existe pas.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_debut.date' => 'La date de début doit être une date valide.',
            'date_debut.after_or_equal' => 'La date de début doit être aujourd\'hui ou une date future.',
            'date_fin.required' => 'La date de fin est obligatoire.',
            'date_fin.date' => 'La date de fin doit être une date valide.',
            'date_fin.after_or_equal' => 'La date de fin doit être égale ou postérieure à la date de début.',
            'type_vol_id.required' => 'Veuillez sélectionner un type de vol.',
            'type_vol_id.array' => 'Format de type de vol invalide.',
            'type_vol_id.min' => 'Veuillez sélectionner au moins un type de vol.',
            'type_vol_id.*.in' => 'Seuls les types de vol "VOL CARGO" et "VOL CHARTER" sont autorisés pour ce type de demande.',
            'type_vol_id.in' => 'Le type de vol doit être "VOL CARGO" pour le transport de dépouille mortelle.',
            'type_vol_id.exists' => 'Le type de vol sélectionné est invalide.',
            'sous_validite.integer' => 'La sous-validité doit être un nombre entier.',
            'sous_validite.min' => 'La sous-validité minimale est de 12 heures.',
            'sous_validite.max' => 'La sous-validité maximale est de 72 heures.',
            'objet.string' => 'L\'objet doit être une chaîne de caractères.',
            'objet.max' => 'L\'objet ne doit pas dépasser 500 caractères.',
        ];
        
        // Valider les données
        $validated = $request->validate($validationRules, $messages);
        
        // Vérifier si c'est une modification
        $isEditMode = $request->edit_mode == '1' && $request->demande_id;
        
        if ($isEditMode) {
            // ========== MISE À JOUR ==========
            $demande = DemandeAutorisation::findOrFail($request->demande_id);
            
            $updateData = [
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'type_demande_autorisation_id' => $typeId,
                'objet' => $request->objet,
                'sous_validite' => $request->sous_validite,
            ];
            
            // Gérer type_vol selon le type
            if ($typeId === 3) {
                // Multi-select : convertir en JSON
                $updateData['type_vol_ids'] = json_encode($request->type_vol_id);
                $updateData['type_vol_id'] = null;
            } else {
                // Single select
                $updateData['type_vol_id'] = $request->type_vol_id;
                $updateData['type_vol_ids'] = null;
            }
            
            $demande->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Demande modifiée avec succès.'
            ]);
            
        } else {
            // ========== CRÉATION ==========
            $currentYear = now()->year;
            
            // Générer le code unique
            $countThisYear = DemandeAutorisation::whereYear('created_at', $currentYear)->count();
            $sequenceNumber = $countThisYear + 1;
            
            // Limiter à 9999 demandes par an
            if ($sequenceNumber > 9999) {
                return response()->json([
                    'message' => 'Le nombre maximum de demandes pour cette année a été atteint.',
                    'errors' => ['system' => ['Limite annuelle atteinte. Veuillez contacter l\'administrateur.']]
                ], 422);
            }
            
            $formattedSequence = str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);
            $code = "{$formattedSequence}/DTA/{$currentYear}";
            
            // Vérifier l'unicité du code
            while (DemandeAutorisation::where('code', $code)->exists()) {
                $sequenceNumber++;
                $formattedSequence = str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);
                $code = "{$formattedSequence}/DTA/{$currentYear}";
            }
            
            // Préparer les données de création
            $creationData = [
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'type_demande_autorisation_id' => $typeId,
                'objet' => $request->objet,
                'sous_validite' => $request->sous_validite,
                'statut' => 'on_hold',
                'user_id' => auth()->id(),
                'code' => $code,
            ];
            
            // Gérer type_vol selon le type
            if ($typeId === 3) {
                // Multi-select : convertir en JSON
                $creationData['type_vol_ids'] = json_encode($request->type_vol_id);
                $creationData['type_vol_id'] = null;
            } else {
                // Single select
                $creationData['type_vol_id'] = $request->type_vol_id;
                $creationData['type_vol_ids'] = null;
            }
            
            // Créer la demande
            $demandeAutorisation = DemandeAutorisation::create($creationData);
            
            // Créer l'état initial de la demande
            EtatDemandeAutorisation::create([
                'demande_id' => $demandeAutorisation->id,
                'user_id' => auth()->id(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Demande créée avec succès.'
            ]);
        }
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Renvoyer les erreurs de validation au format JSON
        return response()->json([
            'message' => 'Erreur de validation',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Demande introuvable.',
            'errors' => ['demande_id' => ['La demande spécifiée n\'existe pas.']]
        ], 404);
        
    } catch (\Exception $e) {
        // Logguer l'erreur pour le débogage
        \Log::error('Erreur lors de la création/modification d\'une demande d\'autorisation', [
            'error' => $e->getMessage(),
            'user_id' => auth()->id(),
            'data' => $request->all()
        ]);
        
        return response()->json([
            'message' => 'Une erreur est survenue lors de l\'enregistrement.',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}



    public function edit($id)
    {
        $demandeAutorisation = DemandeAutorisation::find($id);
if ($demandeAutorisation->type_demande_autorisation_id == 3) {
        // Cette ligne va déclencher l'accesseur
        $demandeAutorisation->type_vols_list;
    }
        $user = Auth::user();
        $vols = $demandeAutorisation->vols;
        $type_avions = TypeAvion::all();
        $avions = $demandeAutorisation->avions;
        $type_vols  = TypeVol::all();
        $proprietaires = Proprietaire::all();
        $mdns =  $demandeAutorisation->mdns;
        
        $equipe_vols = $demandeAutorisation->equipe;

        $fretVols  = $demandeAutorisation->fret;
        $receivingParties = $demandeAutorisation->receivingParties;
        $personnesDeces = PersonneDeces::where('demande_autorisation_id', $demandeAutorisation->id)->get();
        $requiredDocs = [];
        if (isset($vols) && $vols->isNotEmpty()) {

            $requiredDocs = TypeDocumentAutorisation::where('type_vol_id', $demandeAutorisation->typeVol->id)
                ->where('type_demande_autorisation_id', $demandeAutorisation->type->id)
                ->get();
        }
        $aeroports = Aeroport::all();
        $pays = Pays::all();
        $compagnies = Compagnie::all();
        return view('user.autorisations.edit', compact('mdns','personnesDeces','pays', 'vols', 'type_vols', 'compagnies', 'avions', 'proprietaires', 'type_avions', 'receivingParties', 'requiredDocs', 'demandeAutorisation', 'equipe_vols', 'fretVols', 'aeroports'));
    }

    /**
     * Store a newly created MDN.
     */
    public function storeMdn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_autorisation' => 'required|date',
            'numero_mdn' => 'required|string|unique:mdns,numero_mdn',
            'pays_id' => 'required',
            'demande_autorisation_id' => 'required|exists:demande_autorisations,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mdn = Mdn::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'MDN created successfully',
                'data' => $mdn
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating MDN: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified MDN.
     */
    public function updateMdn(Request $request, $id)
    {
        $mdn = Mdn::find($id);

        if (!$mdn) {
            return response()->json([
                'success' => false,
                'message' => 'MDN not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'date_autorisation' => 'required|date',
            'numero_mdn' => 'required|string|unique:mdns,numero_mdn,' . $id,
            'pays_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mdn->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'MDN updated successfully',
                'data' => $mdn
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating MDN: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified MDN.
     */
    public function destroyMdn($id)
    {
        $mdn = Mdn::find($id);

        if (!$mdn) {
            return response()->json([
                'success' => false,
                'message' => 'MDN not found'
            ], 404);
        }

        try {
            $mdn->delete();

            return response()->json([
                'success' => true,
                'message' => 'MDN deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting MDN: ' . $e->getMessage()
            ], 500);
        }
    }

public function storeAeroports(Request $request)
{
    try {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'codeIATA' => 'nullable|string|max:3|unique:aeroports,codeIATA',
            'codeICAO' => 'nullable|string|max:4|unique:aeroports,codeICAO',
            'pays_id' => 'required|exists:pays,id',
            'ville' => 'required|string|max:255',
            //'latitude' => 'required|numeric',
            //'longitude' => 'required|numeric',
        ], [
            'codeIATA.unique' => 'Ce code IATA est déjà utilisé par un autre aéroport',
            'codeICAO.unique' => 'Ce code ICAO est déjà utilisé par un autre aéroport',
            'nom.unique' => 'Un aéroport avec ce nom existe déjà dans cette ville',
        ]);

        // Normaliser les codes en majuscules
        if (!empty($validated['codeIATA'])) {
            $validated['codeIATA'] = strtoupper($validated['codeIATA']);
        }

        if (!empty($validated['codeICAO'])) {
            $validated['codeICAO'] = strtoupper($validated['codeICAO']);
        }

        // Vérification d'unicité nom+ville (validation composite)
        $existingAirport = Aeroport::where('nom', $validated['nom'])
            ->where('ville', $validated['ville'])
            ->where('pays_id', $validated['pays_id'])
            ->first();

        if ($existingAirport) {
            return response()->json([
                'success' => false,
                'message' => 'Un aéroport nommé "' . $validated['nom'] . 
                           '" existe déjà à ' . $validated['ville']
            ], 422);
        }

        // Créer l'aéroport avec validation atomique
        DB::beginTransaction();
        try {
            $aeroport = Aeroport::create($validated);
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Aéroport créé avec succès !',
                'aeroport' => $aeroport->load('pays')
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur de validation',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        //\Log::error('Erreur création aéroport: ' . $e->getMessage());
        
        $message = 'Une erreur est survenue lors de la création de l\'aéroport.';
        
        if (str_contains($e->getMessage(), 'Duplicate entry')) {
            $message = 'Cet aéroport existe déjà (doublon détecté).';
        }

        return response()->json([
            'success' => false,
            'message' => $message
        ], 500);
    }
}
public function store_type_avions(Request $request)
{
    try {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:255',
                'unique:type_avions,code',
                'regex:/^[A-Za-z0-9\-]+$/' // Uniquement lettres, chiffres et tirets
            ],
            'capacite' => 'nullable|integer|min:0|max:1000',
            'charge_max' => 'nullable|integer|min:0|max:1000000',
        ], [
            'code.unique' => 'Ce code de type d\'avion existe déjà',
            'code.regex' => 'Le code ne doit contenir que des lettres, chiffres et tirets',
            'capacite.max' => 'La capacité ne peut pas dépasser 1000 passagers',
            'charge_max.max' => 'La charge maximale ne peut pas dépasser 1,000,000 kg',
        ]);

        // Normaliser le code en majuscules
        $validated['code'] = strtoupper($validated['code']);
        
        // Définir des valeurs par défaut si null
        $validated['capacite'] = $validated['capacite'] ?? 0;
        $validated['charge_max'] = $validated['charge_max'] ?? 0;

        DB::beginTransaction();
        try {
            $typeAvion = TypeAvion::create($validated);
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Type d\'avion créé avec succès',
                'id' => $typeAvion->id,
                'code' => $typeAvion->code,
                'data' => $typeAvion
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur de validation',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        //\Log::error('Erreur création type avion: ' . $e->getMessage());
        
        $message = 'Une erreur est survenue lors de la création du type d\'avion.';
        
        if (str_contains($e->getMessage(), 'Duplicate entry')) {
            $message = 'Ce type d\'avion existe déjà (doublon détecté).';
        }

        return response()->json([
            'success' => false,
            'message' => $message
        ], 500);
    }
}
public function storeCompagnies(Request $request)
{
    try {
        $validated = $request->validate([
            'nom_entreprise' => [
                'required',
                'string',
                'max:100',
                // Vérification d'unicité composite: nom + user_id
                Rule::unique('compagnies')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            ],
            'code' => [
                'required',
                'string',
                'max:100',
                'unique:compagnies,code', // Le code doit être unique globalement
                'regex:/^[A-Za-z0-9\-]+$/'
            ],
            'email' => 'nullable|email|max:100',
            'telephone' => [
                'nullable',
                'string',
                'max:20',
                //'regex:/^[+]?[0-9\s\-\(\)]+$/' 
                // Format téléphone basique
            ],
            'adresse' => 'nullable|string|max:200',
        ], [
            'nom_entreprise.unique' => 'Vous avez déjà une compagnie avec ce nom',
            'code.unique' => 'Ce code de compagnie est déjà utilisé',
            'code.regex' => 'Le code ne doit contenir que des lettres, chiffres et tirets',
            'telephone.regex' => 'Format de téléphone invalide',
        ]);

        // Normaliser le code en majuscules
        $validated['code'] = strtoupper($validated['code']);
        
        // Vérification supplémentaire pour éviter les doublons par nom (insensible à la casse)
        $existingCompagnie = Compagnie::where('user_id', auth()->id())
            ->whereRaw('LOWER(nom_entreprise) = ?', [strtolower($validated['nom_entreprise'])])
            ->first();

        if ($existingCompagnie) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà une compagnie avec un nom similaire : "' . 
                           $existingCompagnie->nom_entreprise . '"'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $compagnie = Compagnie::create(array_merge($validated, [
                'user_id' => auth()->id(),
                'statut' => 'actif' // Statut par défaut
            ]));
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Compagnie créée avec succès',
                'data' => $compagnie
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur de validation',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        //\Log::error('Erreur création compagnie: ' . $e->getMessage());
        
        $message = 'Une erreur est survenue lors de la création de la compagnie.';
        
        if (str_contains($e->getMessage(), 'Duplicate entry')) {
            $message = 'Cette compagnie existe déjà (doublon détecté).';
        }

        return response()->json([
            'success' => false,
            'message' => $message
        ], 500);
    }
}
    /**
     * Met à jour l'état d'une demande
     */
public function updateDemandeState(Request $request, $demandeId)
{
    
    try {
        DB::beginTransaction();
        
        // Validation de base
        $validated = $request->validate([
            'action' => 'required|string|in:compagnie_cree_demande,compagnie_rectifie_demande,dg_annoter,dg_annoter_admin,dg_rejeter,dta_dg_annoter,dta_annoter,dta_annoter_admin,dta_rejeter,dta_notifier,service_annoter,service_raturer,dsv_valider,dsna_valider,dsad_valider,dsf_valider,service_valider,service_tout_valider,dta_valider,dg_valider,dta_dg_valider,compagnie_payer,daf_confirme_pay,dg_signer,service_envoyer',
            'is_approved' => 'sometimes|boolean',
            'is_rejected' => 'sometimes|boolean',
            'motif' => 'required_if:action,dg_rejeter,dta_rejeter|nullable|string',
            'directions' => 'required_if:action,service_annoter|nullable|array',
            'directions.*' => 'in:dsv,dsad,dsna,dsf',
            'points' => 'nullable|string',
        ]);
        

        // Récupération des données
        $demande = DemandeAutorisation::with(['user.demandeur', 'type', 'etatDemande', 'avions.type'])->findOrFail($demandeId);
        
        $notificationService = app(DtaAutorisationNotificationService::class);
        
        // Récupération des acteurs (une seule fois)
        $dg = User::role('dg')->whereHas('signature', fn($q) => $q->whereNotNull('signature'))->latest()->first();
        $dta = User::role('dta')->whereHas('signature', fn($q) => $q->whereNotNull('signature'))->latest()->first();
        $srta = User::role('admin')->whereHas('permissions', fn($q) => $q->where('name', 'menage-vi'))->whereHas('signature', fn($q) => $q->whereNotNull('signature'))->latest()->first();
        $daf = User::role('daf')->latest()->first();
        
        // Récupération des directeurs
        $dsv = User::role('dsv')->latest()->first();
        $dsna = User::role('dsna')->latest()->first();
        $dsad = User::role('dsad')->latest()->first();
        $dsf = User::role('dsf')->latest()->first();

        $action = $request->input('action');

        // Traitement des actions
        switch ($action) {
            case 'compagnie_cree_demande':
                $demande->update([
                    'date_soumission' => now(),
                    'statut' => 'in_progress',
                    'dsv_motif' => null,
                    'dsna_motif' => null,
                    'dsad_motif' => null,
                    'dg_motif' => null,
                    'dta_motif' => null
                ]);

                // Notification au demandeur (accusé)
                $notificationService->sendAcknowledgmentNotification($demande, auth()->user());
                
                // Notifications au DG et DTA
                foreach ([$dg, $dta] as $user) {
                    if ($user && !empty($user->whatsapp)) {
                        if(!$demande->mise_a_jour){
                            $notificationService->sendNewDemandeNotification($demande, $user);
                        }else{
                            $notificationService->sendRectifiedDemandeNotification($demande, $user);
                        }
                        
                    }
                }
                $demande->update(['mise_a_jour' => false]);
                break;

            case 'dg_annoter':
                // DG annote à la DTA
                if ($dta && !empty($dta->whatsapp)) {
                    $notificationService->sendDGAnnotateToDTANotification($demande, $dta);
                }
                break;

            case 'dg_annoter_admin':
                // DG annote à l'admin (SRTA)
                if ($dta && !empty($dta->whatsapp)) {
                    $notificationService->sendDGAnnotateToDTANotification($demande, $dta);
                }
                if ($srta && !empty($srta->whatsapp) && $dta && !empty($dta->whatsapp)) {
                    $notificationService->sendDGAnnotateToAdminNotification($demande, $srta, $dta);
                }
                break;

            case 'dg_rejeter':
                // DG rejette la demande
                if ($dta && !empty($dta->whatsapp) && $demande->user && !empty($demande->user->whatsapp)) {
                    $notificationService->sendDGRejectionNotification(
                        $demande,
                        $dta,
                        $demande->user,
                        $request->motif
                    );
                }
                if ($demande->etatDemande) {
                    $demande->etatDemande->resetAllApprovalStates();
                    $demande->etatDemande->update(['compagnie_cree_demande' => false,'dg_rejeter' => true]);
                }
                $demande->update(['dg_motif' => $request->motif]);
                break;

            case 'dta_dg_annoter':
                // DTA annote à la place du DG
                if ($dg && !empty($dg->whatsapp)) {
                    $notificationService->sendDTAAnnotateForDGNotification($demande, $dg);
                }
                break;


            case 'dta_rejeter':
                // DTA rejette la demande
                if ($demande->user && !empty($demande->user->whatsapp)) {
                    $notificationService->sendDTARejectionNotification(
                        $demande,
                        $demande->user,
                        $request->motif
                    );
                }
                if ($demande->etatDemande) {
                    
                    $demande->etatDemande->resetAllApprovalStates();
                    $demande->etatDemande->update(['compagnie_cree_demande' => false,'dta_rejeter' => true]);
                }
                $demande->update(['dta_motif' => $request->motif]);
                
                break;

            case 'dta_annoter':
                if ($srta && !empty($srta->whatsapp)) {
                    $notificationService->sendApplicationActionRequired(
                        demandeNumber: $demande->code,
                        demandeType: $demande->type->libelle,
                        recipientRole: 'CHEF SERVICE',
                        recipientPhone: $srta->whatsapp,
                        actionType: 'technical_review',
                        applicantName: $demande->user->demandeur->np,
                    );
                }
                break;

            case 'service_annoter':
                $request->validate([
                    'directions' => 'required|array',
                    'directions.*' => 'in:dsv,dsad,dsna,dsf',
                    'points' => 'nullable|string',
                ]);
                
                $demande->directions_annotees = json_encode($request->directions);
                $demande->points = $request->points;
                $demande->save();
                
                // Notification aux directions concernées
                $directionsUsers = [];
                if (in_array('dsv', $request->directions) && $dsv) $directionsUsers['dsv'] = $dsv;
                if (in_array('dsna', $request->directions) && $dsna) $directionsUsers['dsna'] = $dsna;
                if (in_array('dsad', $request->directions) && $dsad) $directionsUsers['dsad'] = $dsad;
                if (in_array('dsf', $request->directions) && $dsf) $directionsUsers['dsf'] = $dsf;
                
                if (!empty($directionsUsers)) {
                    $notificationService->sendDTATransmitToDirectionsNotification($demande, $directionsUsers);
                }
                break;

            case 'service_raturer':
                $request->validate([
                    'directions_to_remove' => 'required|array',
                    'directions_to_remove.*' => 'in:dsv,dsna,dsad,dsf',
                    'motif_retrait' => 'nullable|string'
                ]);
                
                $annotatedDirections = json_decode($demande->directions_annotees) ?? [];
                $directionsToRemove = $request->directions_to_remove;
                $motifRetrait = $request->motif_retrait;
                
                // Filtrer pour ne garder que les directions qui ne sont pas retirées
                $remainingDirections = array_diff($annotatedDirections, $directionsToRemove);
                
                // Mettre à jour les directions annotées
                if (empty($remainingDirections)) {
                    // Plus aucune direction annotée
                    $demande->directions_annotees = null;
                    $demande->points = null;
                    
                    // Réinitialiser l'état service_annoter
                    if ($demande->etatDemande) {
                        $demande->etatDemande->service_annoter = false;
                        $demande->etatDemande->save();
                    }
                } else {
                    // Il reste des directions
                    $demande->directions_annotees = json_encode(array_values($remainingDirections));
                    
                    // Ajouter le motif de retrait aux commentaires existants
                    $existingPoints = $demande->points;
                    $newPoints = $motifRetrait 
                        ? ($existingPoints ? $existingPoints . "\n\n[RETRAIT " . now()->format('d/m/Y H:i') . "] " . $motifRetrait : "[RETRAIT] " . $motifRetrait)
                        : $existingPoints;
                    $demande->points = $newPoints;
                }
                
                // Réinitialiser les motifs des directions retirées
                foreach ($directionsToRemove as $direction) {
                    $motifField = $direction . '_motif';
                    if (in_array($direction, ['dsv', 'dsna', 'dsad'])) {
                        $demande->$motifField = null;
                    }
                }
                
                $demande->save();
                
                // Notifications aux directions retirées
                $directionsUsers = [];
                $users = [
                    'dsv' => $dsv ?? null,
                    'dsna' => $dsna ?? null,
                    'dsad' => $dsad ?? null,
                    'dsf' => $dsf ?? null
                ];
                
                foreach ($directionsToRemove as $direction) {
                    if (isset($users[$direction]) && $users[$direction] && !empty($users[$direction]->whatsapp)) {
                        $directionsUsers[$direction] = $users[$direction];
                    }
                }
                
                if (!empty($directionsUsers)) {
                    $notificationService->sendDTARemoveFromDirectionsNotification($demande, $directionsUsers, $motifRetrait);
                }
                
                // Notification à la DTA que certaines directions ont été retirées
                if ($dta && !empty($dta->whatsapp)) {
                    $removedDirectionsList = implode(', ', array_map('strtoupper', $directionsToRemove));
                    $message = "Les directions suivantes ont été retirées de la demande {$demande->code} : {$removedDirectionsList}";
                    if ($motifRetrait) {
                        $message .= "\nMotif : {$motifRetrait}";
                    }
                    
                    // Vous pouvez créer une méthode spécifique dans le service de notification
                    $notificationService->sendDirectionsRemovedNotification($demande, $dta, $directionsToRemove, $motifRetrait);
                }
                break;

            case 'dta_notifier':
                $notificationService->sendRejectionNotification(
                    $demande,
                    $demande->user,
                    'DTA',
                    $demande->rejection_reasons_list
                );
                if ($demande->etatDemande) {
                    $demande->etatDemande->resetAllApprovalStates();
                    $demande->etatDemande->update(['compagnie_cree_demande' => false,'dta_rejeter' => true]);
                    
                    
                }
                break;

            case 'dsv_valider':
            case 'dsad_valider':
            case 'dsna_valider':
            case 'dsf_valider' :
                // Notification à la DTA qu'une direction a validé
                if ($dta && !empty($dta->whatsapp)) {
                    $direction = str_replace('_valider', '', $action);
                    $notificationService->sendDirectionValidationNotification($demande, $dta, $direction);
                }
                break;

            case 'service_valider':
                if ($dta && !empty($dta->whatsapp)) {
                    $notificationService->sendSRTAValidationNotification($demande, $dta);
                }
                
                $demande->update([
                    'date_validation' => now(),
                    'statut' => 'validated',
                    'dsv_motif' => null,
                    'dsna_motif' => null,
                    'dsad_motif' => null,
                    'dg_motif' => null,
                    'dta_motif' => null
                ]);
                break;

            case 'service_tout_valider':
                if (auth()->user()?->hasRole('dta')) {
                    $demande->update([
                        'dsv_motif' => null,
                        'dsna_motif' => null,
                        'dsad_motif' => null,
                        'dg_motif' => null,
                        'dta_motif' => null
                    ]);
                    EtatDemandeAutorisation::updateState($demandeId, 'dta_valider', auth()->id(), true);
                }
                break;

            case 'compagnie_payer':
                $justificatif = $request->hasFile('justificatif') ? $request->file('justificatif')->store('paiements') : null;
                PaiementAutorisation::where('id', $request->paiement_id)->update([
                    'statut' => 'invoice_paid',
                    'methode' => $request->methode,
                    'date_paiement' => $request->date_paiement,
                    'justificatif' => $justificatif
                ]);
                
                foreach ([$dta, $daf] as $user) {
                    if ($user && !empty($user->whatsapp)) {
                        $notificationService->sendApplicationActionRequired(
                            demandeNumber: $demande->code,
                            demandeType: $demande->type->libelle,
                            recipientRole: $user->hasRole('dta') ? 'DTA' : 'DAF',
                            recipientPhone: $user->whatsapp,
                            actionType: 'payed',
                            applicantName: $demande->user->demandeur->np,
                        );
                    }
                }
                break;

            case 'daf_confirme_pay':
                PaiementAutorisation::where('id', $request->paiement_id)->update(['statut' => 'confirmed']);
                
                // Génération du code autorisation
                $prefix = strtoupper($request->type_autorisation) === 'SURVOL' ? 'SUR' : 'SAT';
                $currentYear = now()->format('y');
                $lastCode = Autorisation::where('code_autorisation', 'like', "{$prefix}-%{$currentYear}")->latest()->first();
                $sequenceNumber = $lastCode && preg_match('/-(\d{4})-/', $lastCode->code_autorisation, $matches) ? (int)$matches[1] + 1 : 1;
                
                Autorisation::create([
                    'demande_id' => $demandeId,
                    'date_delivrance' => $demande->date_debut,
                    'date_expiration' => $demande->date_fin,
                    'code_autorisation' => "{$prefix}-" . str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT) . "-{$currentYear}",
                    'statut' => 'generated',
                    'cachet' => $dg?->cachet->cachet ?? '',
                    'nom_signataire' => $dg?->signature->nom ?? '',
                    'signature_dg' => $dg?->signature->signature ?? '',
                    'signature_dta' => $dta?->signature->signature ?? '',
                    'signature_srta' => $srta?->signature->signature ?? '',
                ]);

                // Notifications
                foreach ([$demande->user, $dta] as $user) {
                    if ($user && !empty($user->whatsapp)) {
                        $notificationService->sendApplicationActionRequired(
                            demandeNumber: $demande->code,
                            demandeType: $demande->type->libelle,
                            recipientRole: $user->hasRole('dta') ? 'DTA' : 'DEMANDEUR',
                            recipientPhone: $user->whatsapp,
                            actionType: 'validated',
                            applicantName: $demande->user->demandeur->np,
                        );
                    }
                }
                break;

            case 'dta_valider':
                // DTA valide la demande pour signature DG
                if ($dg && !empty($dg->whatsapp)) {
                    $notificationService->sendDTAValidationForDGSignatureNotification($demande, $dg);
                }
                break;

            case 'dg_valider':
            case 'dta_dg_valider':
                $typeVolId = intval($request->input('type_vol_id'));
                
                // Cas avec paiement
                if ($demande->type->id === 2 && in_array($typeVolId, [1, 2, 5, 8, 14])) {
                    $montant = 0;
                    foreach ($demande->avions as $avion) {
                        if ($typeVolId === 1) {
                            $montant += 50000;
                        } elseif (in_array($typeVolId, [2, 5, 8, 14])) {
                            $montant += $avion->type->capacite <= 19 ? 30000 : 50000;
                        }
                    }
                    
                    PaiementAutorisation::create([
                        'reference' => substr('PA-' . strtoupper(uniqid('', true)), 0, 10),
                        'demande_autorisation_id' => $demandeId,
                        'user_id' => $demande->user_id,
                        'montant_total' => $montant,
                        'statut' => 'on_hold',
                        'cachet_dg' => $dg?->cachet->cachet ?? '',
                        'signature_dg' => $dg?->signature->signature ?? '',
                        'dg_signataire' => $dg?->signature->nom ?? '',
                        'signature_daf' => 'sc/MLR1MEI0ZUQfbY5Tjqs346vnP6mTXDr7Yxil6rIH.png',
                        'daf_signataire' => 'Ahmed Ould Abdallah',
                    ]);
                    
                    $actionType = 'payment';
                } 
                // Cas sans paiement (autorisation directe)
                else if(in_array($demande->type->id, [1, 3, 4])) {
                    //$prefix = strtoupper($request->type_autorisation) === 'SURVOL' ? 'SUR' : 'SAT';
                    $prefix = match (strtoupper($request->type_autorisation)) {
                        'SURVOL' => 'SUR',
                        'BLOCK PERMIT' => 'BLP',
                        'TRANSPORT DÉPOUILLE MORTELLE' => 'ADM',
                        default => 'SUR',
                    };
                    $currentYear = now()->format('y');
                    $lastCode = Autorisation::where('code_autorisation', 'like', "{$prefix}-%{$currentYear}")->latest()->first();
                    $sequenceNumber = $lastCode && preg_match('/-(\d{4})-/', $lastCode->code_autorisation, $matches) ? (int)$matches[1] + 1 : 1;
                    
                    Autorisation::create([
                        'demande_id' => $demandeId,
                        'date_delivrance' => $demande->date_debut,
                        'date_expiration' => $demande->date_fin,
                        'code_autorisation' => "{$prefix}-" . str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT) . "-{$currentYear}",
                        'statut' => 'generated',
                        'cachet' => $dg?->cachet->cachet ?? '',
                        'nom_signataire' => $dg?->signature->nom ?? '',
                        'signature_dg' => $dg?->signature->signature ?? '',
                        'signature_dta' => $dta?->signature->signature ?? '',
                        'signature_srta' => $srta?->signature->signature ?? '',
                    ]);
                    
                    $actionType = 'validated';
                }
                else if(in_array($demande->type->id, [5, 6, 7])) {
                    
                                $currentYear = now()->year;

            $countThisYear = Autorisation::whereYear('created_at', $currentYear)->count();
            $sequenceNumber = $countThisYear + 1;
            $formattedSequence = str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);
            $code = "{$formattedSequence}/{$currentYear}";
            
            $codeExists = Autorisation::where('code_autorisation', $code)->exists();
            
            if ($codeExists) {
                // En cas de conflit (très improbable), trouver le prochain numéro disponible
                $existingCodes = Autorisation::where('code', 'like', "%/{$currentYear}")
                    ->pluck('code')
                    ->toArray();

                // Extraire les numéros de séquence existants
                $existingSequences = [];
                foreach ($existingCodes as $existingCode) {
                    preg_match('/^(\d{4})\/\d{4}$/', $existingCode, $matches);
                    if (isset($matches[1])) {
                        $existingSequences[] = (int)$matches[1];
                    }
                }

                // Trouver le premier numéro disponible
                $nextAvailable = 1;
                while (in_array($nextAvailable, $existingSequences)) {
                    $nextAvailable++;
                }

                // Vérifier que nous ne dépassons pas 9999
                if ($nextAvailable > 9999) {
                    // Si nous dépassons 9999, nous pourrions utiliser une autre stratégie
                    // Par exemple, réinitialiser à 0001 ou utiliser plus de chiffres
                    // Pour l'instant, on lève une exception
                    //throw new \Exception("Nombre maximum de demandes atteint pour l'année {$currentYear}");
                }

                $formattedSequence = str_pad($nextAvailable, 4, '0', STR_PAD_LEFT);
                $code = "{$formattedSequence}/{$currentYear}";
            }
            
                    Autorisation::create([
                        'demande_id' => $demandeId,
                        'date_delivrance' => $demande->date_debut,
                        'date_expiration' => $demande->date_fin,
                        'code_autorisation' => $code,
                        'statut' => 'generated',
                        'cachet' => $dg?->cachet->cachet ?? '',
                        'nom_signataire' => $dg?->signature->nom ?? '',
                        'signature_dg' => $dg?->signature->signature ?? '',
                        'signature_dta' => $dta?->signature->signature ?? '',
                        'signature_srta' => $srta?->signature->signature ?? '',
                    ]);
                }

                // Notifications
                foreach ([$demande->user, $dta] as $user) {
                    if ($user && !empty($user->whatsapp)) {
                        $notificationService->sendApplicationActionRequired(
                            demandeNumber: $demande->code,
                            demandeType: $demande->type->libelle,
                            recipientRole: $user->hasRole('dta') ? 'DTA' : 'DEMANDEUR',
                            recipientPhone: $user->whatsapp,
                            actionType: $actionType,
                            applicantName: $demande->user->demandeur->np,
                        );
                    }
                }
                break;

            /*case 'dg_signer':
                // DG signe l'autorisation
                if ($dta && !empty($dta->whatsapp) && $demande->user && !empty($demande->user->whatsapp)) {
                    $notificationService->sendDGSignatureNotification($demande, $dta, $demande->user);
                }
                break;*/
        }

        // Mise à jour de l'état
        EtatDemandeAutorisation::updateState(
            $demandeId,
            $validated['action'],
            auth()->id(),
            $validated['is_approved'] ?? false,
            $validated['is_rejected'] ?? false
        );

        Activity::log($validated['action'],$demandeId);
        DB::commit();

        return redirect()->back()->with('success', 'État mis à jour avec succès');

    } catch (\Exception $e) {
        DB::rollBack();
        //\Log::error('Erreur updateDemandeState: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
    }
}


/**
 * Store a newly created deceased person.
 */
public function storeDeceasedPerson(Request $request)
{
    $request->validate([
        'demande_autorisation_id' => 'required|exists:demande_autorisations,id',
        'nom_prenom' => 'required|string|max:255',
        'numero_passport' => 'nullable|string|max:50',
        'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
    ]);

    $data = $request->all();
    
    if ($request->hasFile('justificatif')) {
        $file = $request->file('justificatif');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $filename);
        $data['justificatif'] = $filename;
    }

    $personne = PersonneDeces::create($data);

    return response()->json(['success' => true, 'data' => $personne]);
}

/**
 * Update the specified deceased person.
 */
public function updateDeceasedPerson(Request $request, $id)
{
    $personne = PersonneDeces::findOrFail($id);
    
    $request->validate([
        'nom_prenom' => 'required|string|max:255',
        'numero_passport' => 'nullable|string|max:50',
        'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
    ]);

    $data = $request->all();
    
    if ($request->hasFile('justificatif')) {
        // Delete old file if exists
        if ($personne->justificatif && file_exists(public_path('uploads/' . $personne->justificatif))) {
            unlink(public_path('uploads/' . $personne->justificatif));
        }
        
        $file = $request->file('justificatif');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $filename);
        $data['justificatif'] = $filename;
    }

    $personne->update($data);

    return response()->json(['success' => true, 'data' => $personne]);
}

/**
 * Remove the specified deceased person.
 */
public function destroyDeceasedPerson($id)
{
    $personne = PersonneDeces::findOrFail($id);
    
    // Delete file if exists
    if ($personne->justificatif && file_exists(public_path('uploads/' . $personne->justificatif))) {
        unlink(public_path('uploads/' . $personne->justificatif));
    }
    
    $personne->delete();

    return response()->json(['success' => true]);
}
    //
    // Section Équipage

    /**
     * Ajouter un membre d'équipage
     */
    public function storeEquipe(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'demande_autorisation_id' => 'required|exists:demande_autorisations,id',
            //'nom' => 'required|string|max:255',
            //'prenom' => 'required|string|max:255',
            //'age' => 'required|integer|min:18|max:70',
            'fonction' => 'required|in:pilot,copilot,mechanic,steward,hostess',
            //'email' => 'required|email',
            'licence_numero' => 'nullable|string',
            'licence_expiration' => 'nullable|date',
            'justificatif' => 'nullable|file|mimes:pdf',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = $request->except('justificatif');

        if ($request->hasFile('justificatif')) {
            $path = $request->file('justificatif')->store('equipes');
            $data['justificatif'] = $path;
        }

        $membre = EquipeVol::create($data);


        return response()->json($membre, 201);
    }

    /**
     * Mettre à jour un membre d'équipage
     */
    public function updateEquipe(Request $request, $id)
    {

        $membre = EquipeVol::findOrFail($id);

        $validator = Validator::make($request->all(), [
            //'nom' => 'required|string|max:255',
            //'prenom' => 'required|string|max:255',
            //'age' => 'required|integer|min:18|max:70',
            'fonction' => 'required|in:pilot,copilot,mechanic,steward,hostess',
            //'email' => 'required|email',
            'licence_numero' => 'nullable|string',
            'licence_expiration' => 'nullable|date',
            'justificatif' => 'nullable|file|mimes:pdf',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = $request->except('justificatif');

        if ($request->hasFile('justificatif')) {
            $path = $request->file('justificatif')->store('equipes');
            $data['justificatif'] = $path;
        }
        $data['valider'] = 1;
        $data['motif'] = null;
        $membre->update($data);

        return response()->json($membre);
    }

    /**
     * Supprimer un membre d'équipage
     */
    public function destroyEquipe($id)
    {
        $membre = EquipeVol::findOrFail($id);
        $membre->delete();

        return response()->json(null, 204);
    }

    // Section Fret

    /**
     * Ajouter un fret
     */
    public function storeFret(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'demande_autorisation_id' => 'required|exists:demande_autorisations,id',
            'nature' => 'required|in:normal,dangerous,perishable,living',
            'poids' => 'required|numeric|min:0',
            'numero_waybill' => 'nullable|string',
            'expediteur' => 'nullable|string',
            'destinataire' => 'nullable|string',
            'instructions_speciales' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $fret = FretVol::create($request->all());

        return response()->json($fret, 201);
    }

    /**
     * Mettre à jour un fret
     */
    public function updateFret(Request $request, $id)
    {
        $fret = FretVol::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nature' => 'required|in:normal,dangerous,perishable,living',
            'poids' => 'required|numeric|min:0',
            'numero_waybill' => 'nullable|string',
            'expediteur' => 'nullable|string',
            'destinataire' => 'nullable|string',
            'instructions_speciales' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }



        $fret->update(array_merge($request->all(), ['valider' => 1, 'motif' => null]));

        return response()->json($fret);
    }

    /**
     * Supprimer un fret
     */
    public function destroyFret($id)
    {
        $fret = FretVol::findOrFail($id);
        $fret->delete();

        return response()->json(null, 204);
    }



    // Section Receiving Parties

    /**
     * Ajouter un contact receiving party
     */
    public function storeReceivingParty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'demande_autorisation_id' => 'required|exists:demande_autorisations,id',
            'nom_contact' => 'required|string|max:255',
            'telephone_contact' => 'required|string|max:20',
            'email_contact' => 'nullable|email',
            'fonction_contact' => 'nullable|string',
            'autres_renseignements' => 'nullable|string',
            'piece_identite' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('piece_identite');

        if ($request->hasFile('piece_identite')) {
            $path = $request->file('piece_identite')->store('receiving_parties');
            $data['piece_identite_path'] = $path;
        }

        $party = ReceivingParty::create($data);

        return response()->json($party, 201);
    }

    /**
     * Mettre à jour un contact receiving party
     */
    public function updateReceivingParty(Request $request, $id)
    {
        $party = ReceivingParty::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom_contact' => 'required|string|max:255',
            'telephone_contact' => 'required|string|max:20',
            'email_contact' => 'nullable|email',
            'fonction_contact' => 'nullable|string',
            'autres_renseignements' => 'nullable|string',
            'piece_identite' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('piece_identite');

        if ($request->hasFile('piece_identite')) {
            // Supprimer l'ancien fichier s'il existe
            if ($party->piece_identite_path) {
                Storage::delete($party->piece_identite_path);
            }

            $path = $request->file('piece_identite')->store('receiving_parties');
            $data['piece_identite_path'] = $path;
        }
        $data['valider'] = 1;
        $data['motif'] = null;

        $party->update($data);

        return response()->json($party);
    }

    /**
     * Supprimer un contact receiving party
     */
    public function destroyReceivingParty($id)
    {
        $party = ReceivingParty::findOrFail($id);

        // Supprimer le fichier associé s'il existe
        if ($party->piece_identite_path) {
            Storage::delete($party->piece_identite_path);
        }

        $party->delete();

        return response()->json(null, 204);
    }

    // Section Assistance Escale

    /**
     * Enregistrer ou mettre à jour les informations d'assistance
     */
    public function storeAssistance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'demande_autorisation_id' => 'required|exists:demande_autorisations,id',
            'structure_assistance' => 'nullable|string',
            'etat_pea' => 'nullable|string',
            'renseignements_divers' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Mise à jour ou création
        $assistance = AssistanceEscalePea::updateOrCreate(
            ['demande_autorisation_id' => $request->demande_autorisation_id],
            $request->all()
        );

        return response()->json($assistance, 200);
    }

    // Section Documents

/**
 * Ajouter ou mettre à jour des documents
 */
public function storeDocuments(Request $request)
{
    $validator = Validator::make($request->all(), [
        'demande_autorisation_id' => 'required|exists:demande_autorisations,id',
        'type_document_id' => 'required|array',
        'type_document_id.*' => 'required|exists:type_document_autorisations,id',
        'pieces' => 'required|array',
        'pieces.*' => 'required|file|mimes:pdf|max:10240',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    $demandeAutorisation = DemandeAutorisation::findOrFail($request->demande_autorisation_id);
    
    foreach ($request->type_document_id as $index => $typeDocumentId) {
        // Vérifier si un document de ce type existe déjà
        $existingDocument = $demandeAutorisation->documents()
            ->where('type_document_id', $typeDocumentId)
            ->first();

        if ($request->hasFile('pieces.' . $index)) {
            $file = $request->file('pieces.' . $index);
            
            if ($existingDocument) {
                // Supprimer l'ancien fichier
                if (Storage::exists('uploads/' . $existingDocument->url)) {
                    Storage::delete('uploads/' . $existingDocument->url);
                }
                
                // Mettre à jour le document existant
                $path = $file->store('documents');
                $existingDocument->update([
                    'url' => basename($path),
                    'updated_at' => now()
                ]);
            } else {
                // Créer un nouveau document
                $path = $file->store('documents');
                $demandeAutorisation->documents()->create([
                    'type_document_id' => $typeDocumentId,
                    'url' => basename($path)
                ]);
            }
        }
    }

    return response()->json([
        'message' => 'Documents uploadés avec succès',
        'documents' => $demandeAutorisation->fresh()->documents
    ], 201);
}

/**
 * Supprimer un document spécifique
 */
public function destroyDocument($id)
{
    $document = DocumentAutorisation::findOrFail($id);
    
    // Supprimer le fichier physique
    if (Storage::exists('uploads/' . $document->url)) {
        Storage::delete('uploads/' . $document->url);
    }
    
    $document->delete();

    return response()->json([
        'message' => 'Document supprimé avec succès'
    ], 200);
}

/**
 * Remplacer un document spécifique
 */
public function updateDocument(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'piece' => 'required|file|mimes:pdf|max:10240',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    $document = DocumentAutorisation::findOrFail($id);
    
    // Supprimer l'ancien fichier
    if (Storage::exists('uploads/' . $document->url)) {
        Storage::delete('uploads/' . $document->url);
    }
    
    // Uploader le nouveau fichier
    $path = $request->file('piece')->store('documents');
    $document->update([
        'url' => basename($path),
        'updated_at' => now()
    ]);

    return response()->json([
        'message' => 'Document mis à jour avec succès',
        'document' => $document->fresh()
    ], 200);
}

    /**
     * Récupérer les documents requis pour un type de demande
     */
    public function getRequiredDocuments($typeDemandeId)
    {
        $documents = TypeDocument::where('type_demande_id', $typeDemandeId)->get();
        return response()->json($documents);
    }
    public function destroy($id)
    {
        $demandeAutorisation = DemandeAutorisation::findOrFail($id);

        $demandeAutorisation->delete();
        return redirect()->back()->with('success', 'Demande supprimée avec succès.');
    }

    public function sendAuthorizationEmail(Autorisation $autorisation, $recipients)
    {
        foreach ($recipients as $recipient) {
            Mail::to($recipient)->queue(new SendFlightAuthorization($autorisation));
        }
    }
    
        /**
     * Envoyer des notifications par email et/ou WhatsApp
     */
    public function sendNotifications(Request $request,$id)
    {
        $demande = DemandeAutorisation::findOrFail($id);
        $autorisation = $demande->autorisation($id);
        $notificationService = app(DtaAutorisationNotificationService::class);
        try {
            // Validation des données
            $validator = Validator::make($request->all(), [
                'email_recipients' => 'nullable|array',
                'email_recipients.*' => 'email',
                'whatsapp_recipients' => 'nullable|array',
                'whatsapp_recipients.*' => 'regex:/^\+[0-9]{8,15}$/'
            ], [
                'email_recipients.*.email' => __('trans.invalid_email_format'),
                'whatsapp_recipients.*.regex' => __('trans.invalid_phone_format')
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $emailRecipients = $request->input('email_recipients', []);
            $whatsappRecipients = $request->input('whatsapp_recipients', []);
            
            
            // Vérifier qu'il y a au moins un destinataire
            if ((empty($emailRecipients)) && (empty($whatsappRecipients))) {
                return response()->json([
                    'success' => false,
                    'message' => __('trans.select_at_least_one_recipient')
                ], 422);
            }
            
            $results = [
                'email_sent' => [],
                'email_failed' => [],
                'whatsapp_sent' => [],
                'whatsapp_failed' => []
            ];
            
            // Envoi des emails
            $this->sendAuthorizationEmail($autorisation, $emailRecipients);
            /*if (!empty($emailRecipients)) {
                foreach ($emailRecipients as $email) {
                    try {
                        Mail::to($email)->send(new SendFlightAuthorization(
                            $autorisation
                        ));
                        
                        $results['email_sent'][] = $email;
                    } catch (\Exception $e) {
                        
                        $results['email_failed'][] = $email;
                    }
                }
            }*/
            
            // Envoi des messages WhatsApp
            if (!empty($whatsappRecipients)) {
                foreach ($whatsappRecipients as $phone) {
                    try {
                        $sent = $notificationService->sendAutorisationNotification($autorisation, $phone);
                        
                        
                        if ($sent) {
                            $results['whatsapp_sent'][] = $phone;
                        } else {
                            $results['whatsapp_failed'][] = $phone;
                        }
                    } catch (\Exception $e) {
                       
                        $results['whatsapp_failed'][] = $phone;
                    }
                }
            }
            
            // Sauvegarder comme modèle si demandé
            //$this->saveNotificationTemplate($request->subject, );
            
            // Journaliser l'envoi
            //$this->logNotification($request, $results);
            

            
            
            $successMessage = __('trans.notifications_sent_successfully');
            $successCount = count($results['email_sent']) + count($results['whatsapp_sent']);
            $failedCount = count($results['email_failed']) + count($results['whatsapp_failed']);
            
            if ($failedCount > 0) {
                $successMessage .= " ($successCount envoyés, $failedCount échoués)";
            }
            $demande->etatDemande->update(
                ['service_envoyer' => true]
                );
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            
            
            return response()->json([
                'success' => false,
                'message' => __('trans.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Sauvegarder un modèle de notification
     */
    private function saveNotificationTemplate($subject, $message)
    {
        // Implémentez la sauvegarde dans la base de données
        // Exemple:
        // NotificationTemplate::create([
        //     'subject' => $subject,
        //     'message' => $message,
        //     'user_id' => auth()->id(),
        // ]);
    }
    
    /**
     * Journaliser l'envoi de notification
     */
    private function logNotification($request, $results)
    {
        // Implémentez la journalisation
        // Exemple:
        // NotificationLog::create([
        //     'user_id' => auth()->id(),
        //     'email_recipients' => json_encode($request->email_recipients),
        //     'whatsapp_recipients' => json_encode($request->whatsapp_recipients),
        //     'subject' => $request->subject,
        //     'results' => json_encode($results),
        //     'sent_at' => now(),
        // ]);
    }
}
