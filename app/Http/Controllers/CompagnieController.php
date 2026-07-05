<?php

namespace App\Http\Controllers;

use App\Enums\SaisonEnum;
use App\Helpers\ReferenceGenerator;
use App\Models\Activity;
use App\Models\Aeroport;
use App\Models\Approbation;
use App\Models\Autorisation;
use App\Models\Avion;
use App\Models\Compagnie;
use App\Models\CompagnieLoginRequest;
use App\Models\DemandeApprobation;
use App\Models\DemandeAutorisation;
use Illuminate\Http\Request;
use App\Models\Demandeur;
use App\Models\DocumentApprobation;
use App\Models\DocumentAutorisation;
use App\Models\EtatDemande;
use App\Models\EtatDemandeApprobation;
use App\Models\EtatDemandeAutorisation;
use App\Models\ItineraireVol;
use App\Models\Paiement;
use App\Models\PaiementAutorisation;
use App\Models\Proprietaire;

use App\Models\TypeAvion;
use App\Models\TypeDemandeAutorisation;
use App\Models\TypeDocument;
use App\Models\TypeDocumentApprobation;
use App\Models\TypeDocumentAutorisation;
use App\Models\TypeVol;
use App\Models\User;
use App\Models\Vol;
use App\Models\VolApprobation;
use App\Notifications\CompagnieLoginRequestNotification;
use App\Services\DtaApplicationNotificationService;
use App\Services\DTAImportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;


class CompagnieController extends Controller
{
    //
    protected $dtaNotificationService;

    public function __construct(DtaApplicationNotificationService $dtaNotificationService)
    {
        $this->dtaNotificationService = $dtaNotificationService;
    }

    // This should ONLY generate a URL (no actions)
    public function generateLoginRequestUrl(User $user)
    {
        // Verify relationship exists
        if (!$user->demandeur) {
            return '#'; // Or handle error differently
        }
    
        return URL::signedRoute('compagnie.request.login', ['user' => $user->id]);
    }
    
    // This handles the actual request (called via route)
    public function processLoginRequest(User $user)
    {
        // Verify relationship exists
        if (!$user->demandeur) {
            abort(403, 'User not associated with your compagnie');
        }
    
        // Create or update request
        $request = CompagnieLoginRequest::updateOrCreate(
            [
                'compagnie_user_id' => Auth::id(),
                'target_user_id' => $user->id
            ],
            [
                'token' => Str::random(60),
                'expires_at' => now()->addHours(24),
                'accepted' => false
            ]
        );
    
        // Send notification to user
        $user->notify(new CompagnieLoginRequestNotification($request));
    
        return back()->with('success', 'Login request sent to user');
    }


    public function loginAsUser(Request $request, User $user)
    {
        // 1. Verify signed URL
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid or expired link');
        }

        // 2. Verify the current user is a compagnie
        if (!Auth::user()->hasRole('compagnie')) {
            abort(403, 'Only compagnie users can access this feature');
        }

        // 3. Verify the target user belongs to this compagnie
        $demandeur = Demandeur::where('user_id', $user->id)
            ->where('compagnie_id', Auth::user()->compagnie->id)
            ->first();

        if (!$demandeur) {
            abort(403, 'This user does not belong to your compagnie');
        }

        // 4. Store original user and login
        session([
            'original_compagnie_user' => Auth::id(),
            'original_compagnie_name' => Auth::user()->name
        ]);

        Auth::login($user);

        // 5. Add security logging
        Log::info('Compagnie user switching', [
            'compagnie_user_id' => session('original_compagnie_user'),
            'target_user_id' => $user->id,
            'ip' => $request->ip()
        ]);

        return redirect()->route('user');
    }

    public function returnToCompagnie()
    {
        if (!session()->has('original_compagnie_user')) {
            return redirect('/');
        }

        $compagnieUser = User::find(session('original_compagnie_user'));

        if ($compagnieUser) {
            Auth::login($compagnieUser);
            session()->forget(['original_compagnie_user', 'original_compagnie_name']);
            return redirect()->route('compagnie');
        }

        return redirect('/');
    }

    public function generateUserLoginLink(User $user)
    {
        return URL::temporarySignedRoute(
            'compagnie.login.as.user',
            now()->addHour(),
            ['user' => $user->id]
        );
    }
    public function index()
    {
        $user = Auth::user();
        $demandeurs = Auth::user()->compagnie->demandeurs()->with('userAccount')->get();

        foreach ($demandeurs as $demandeur) {
            if ($demandeur->userAccount) {
                $demandeur->userAccount->loginUrl = URL::signedRoute(
                    'compagnie.login.as.user',
                    ['user' => $demandeur->userAccount->id],
                    now()->addHours(4)
                );
            }
        }
        $compagnie = $user->compagnie;

        $demandeApprobations = $user->demandeApprobations;
        $demandeApprobations->map(function ($demande) {
            $demande->invalid_reasons = $demande->getInvalidComponents();
            $demande->rejection_reasons = $demande->getRejectionReasons();
            $demande->has_issues = count($demande->invalid_reasons) > 0 || count($demande->rejection_reasons) > 0;
            return $demande;
        });

        return view('compagnie.index', compact('compagnie', 'demandeurs', 'demandeApprobations'));
    }
    public function pay($id)
    {
        $paiement = Paiement::find($id);
        return view('compagnie.pay', compact('paiement'));
    }

    public function updatePaiement(Request $request, Paiement $paiement)
    {
        //
        $request->validate([
            'quittance' => 'required|file',
            'date_paiement' => 'required|date'
        ]);

        if ($request->hasFile('quittance')) {
            $quittancePath = $request->file('quittance')->store('paiements', 'public');
        } else {
            $quittancePath = null;
        }

        $user = Auth::user();
        $compagnie = $user->compagnie;
        $compagnie->update(
            [
                'panier' => $compagnie->panier + doubleval($paiement->montant)
            ]
        );

        $p = $paiement->update(
            [
                'quittance' => $quittancePath,
                'date_paiement' => $request->date_paiement,
                'statut' => 'Réglée'
            ]
        );

        $etat_demande = EtatDemande::where('demande_id', $paiement->demande_id)->update(
            [
                'compagnie_payer' => true
            ]
        );
        $activity = Activity::log('compagnie_payer');
        return redirect()->route('compagnie')->with('success', 'Paiement mis à jour avec succès.');
    }

    function valider(Demandeur $demandeur)
    {

        $valider_compagnie = $demandeur->update(
            [

                'valider_compagnie' => true,
            ]
        );

        return back()->with('success', 'Demandeur validee avec succès.');
    }
    function rejeter(Demandeur $demandeur)
    {

        $rejeter_compagnie = $demandeur->update(
            [

                'valider_compagnie' => false,
                'compagnie_id' => null,
            ]
        );

        return back()->with('success', 'Demandeur rejetee avec succès.');
    }


    public function autorisationPay($id)
    {
        $paiement = PaiementAutorisation::find($id);
        return view('compagnie.autorisationPay', compact('paiement'));
    }
    public function store(Request $request, DTAImportService $importService)
    {
        $updateted = $request->validate([
            'saison' => 'required|in:ETE,HIVER',
        ]);
        $currentYear = date('Y');
        $seasonDates = $request->saison == 'ETE'
            ? VolApprobation::calculateSummerSeasonDates($currentYear)
            : VolApprobation::calculateWinterSeasonDates($currentYear);
        $compagnie = Auth::user()->compagnie;

        $reference = ReferenceGenerator::generateApprovalReference(
            $compagnie->code,
            $currentYear
        );
        $demandeApprobation = DemandeApprobation::create(
            [
                'reference' => $reference,
                'date_debut' => $seasonDates['date_debut'],
                'date_fin' => $seasonDates['date_fin'],
                'saison' => $request->saison,
                'statut' => 'EN_ATTENTE',
                'user_id' => auth()->id(),
                'compagnie_id' => $compagnie->id,
            ]
        );
        $etat_demande = EtatDemandeApprobation::create([
            'demande_id' => $demandeApprobation->id,
            'user_id' => auth()->id(),
        ]);
        try {
            $demande = $importService->importFromFile(
                $compagnie,
                base_path('DTA.xlsx'),
                $demandeApprobation
            );
            return redirect()->route('compagnie')->with('success', __('Application submitted successfully'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        $demandeApprobation = DemandeApprobation::find($id);
        $user = Auth::user();
        $vols = $demandeApprobation->vols->sortBy('numero_vol');
        $type_avions = TypeAvion::all();
        $compagnie = $user->compagnie;
        $avions = $user->compagnie->avions;
        $documents = $demandeApprobation->documents;

        $itineraires = $demandeApprobation->itineraires;
        $requiredDocs = TypeDocumentApprobation::all();
        //if ($compagnie->id === 13) {
        # code...
        //$aeroportDeparts = Aeroport::all();
        //$aeroportArrivees = Aeroport::all();
        //}
        $aeroports = Aeroport::all();

        return view('compagnie.edit', compact(
            'vols',
            'avions',
            'compagnie',
            'type_avions',
            'requiredDocs',
            'demandeApprobation',
            'itineraires',
            'aeroports'
        ));
    }

    public function updateProgramStatus(Request $request)
    {
        $avion = Avion::findOrFail($request->avion_id);
        $avion->demande_approbation_id = $request->demande_approbation_id;
        $avion->save();


        return response()->json(['success' => true]);
    }
    // Section Documents

    /**
     * Ajouter des documents
     */
    public function storeDocuments(Request $request)
    {


        $updatetor = Validator::make($request->all(), [
            'demande_approbation_id' => 'required|exists:demande_approbations,id',
            'type_document_id' => 'required|array',
            'type_document_id.*' => 'exists:type_document_approbations,id',
            'pieces' => 'required|array',
            'pieces.*' => 'file|mimes:pdf|max:5120',
        ]);

        if ($updatetor->fails()) {
            return response()->json(['errors' => $updatetor->errors()], 422);
        }

        $documents = [];

        foreach ($request->type_document_id as $index => $typeId) {
            if (isset($request->pieces[$index])) {
                $file = $request->pieces[$index];
                $path = $file->store('documents');

                $documents[] = DocumentApprobation::create([
                    'demande_approbation_id' => $request->demande_approbation_id,
                    'type_document_id' => $typeId,
                    'url' => $path,

                ]);
            }
        }

        return response()->json($documents, 201);
    }

    /**
     * Supprimer un document
     */
    public function destroyDocument($id)
    {
        $document = DocumentApprobation::findOrFail($id);

        // Supprimer le fichier
        Storage::delete($document->url);

        $document->delete();

        return response()->json(null, 204);
    }

    /**
     * Récupérer les documents requis pour un type de demande
     */
    public function getRequiredDocuments($typeDemandeId)
    {
        // $documents = TypeDocumentApprobation::where('type_demande_id', $typeDemandeId)->get();
        $documents = [];
        return response()->json($documents);
    }
    // Section Itinéraires

    /**
     * Ajouter un itinéraire
     */
    public function storeItineraire(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'demande_approbation_id' => 'required|exists:demande_approbations,id',
            'vol_id' => 'required|exists:vol_approbations,id',
            'aeroport_id' => 'required|exists:aeroports,id',
            'heure_depart' => 'required|date_format:H:i|after:heure_arrivee',
            'heure_arrivee' => 'required|date_format:H:i'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $itineraire = ItineraireVol::create($request->all());

        return response()->json($itineraire, 201);
    }

    /**
     * Mettre à jour un itinéraire
     */
    public function updateItineraire(Request $request, $id)
    {
        $itineraire = ItineraireVol::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'demande_approbation_id' => 'required|exists:demande_approbations,id',
            'vol_id' => 'required|exists:vol_approbations,id',
            'aeroport_id' => 'required|exists:aeroports,id',
            'heure_depart' => 'required|date_format:H:i|after:heure_arrivee',
            'heure_arrivee' => 'required|date_format:H:i'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $itineraire->update($request->all());

        return response()->json($itineraire);
    }

    /**
     * Supprimer un itinéraire
     */
    public function destroyItineraire($id)
    {
        $itineraire = ItineraireVol::findOrFail($id);
        $itineraire->delete();

        return response()->json(null, 204);
    }

    public function destroy(DemandeApprobation $demande)
    {
        $demande->delete();
        return redirect()->back()->with('success', 'Demande supprimée avec succès');
    }
    public function print(Approbation $approbation)
    {
        $dg = User::role('dg')->first();
        $demande = $approbation->demande;

        return view('compagnie.print', compact('approbation', 'dg', 'demande'));
    }
    public function updateDemandeState(Request $request, $demandeId)
    {

        $validated = $request->validate([
            'action' => 'required|string|in:compagnie_cree_demande,dg_annoter,dta_dg_annoter,dta_annoter,service_annoter,dsv_valider,dsna_valider,dsad_valider,dta_notifier,service_tout_valider,service_valider,dta_valider,dta_dg_valider,dg_valider',
            'is_approved' => 'sometimes|boolean',
            'is_rejected' => 'sometimes|boolean'
        ]);

        if ($request->input('action') === 'dta_notifier') {
            # code...

            $demande = DemandeApprobation::find($demandeId);
            $recipientUser = $demande->user;

            $this->dtaNotificationService->sendRejectionNotification(
                $demande,
                $recipientUser,
                'DTA'
            );
        }
        if ($request->input('action') === 'service_valider' || $request->input('action') === 'service_tout_valider') {
            # code...
            $updateDemande = DemandeApprobation::where('id', $demandeId)->update(
                [
                    'date_approbation' => date('Y-m-d'),
                    'statut' => 'APPROUVEE'

                ]
            );
        }
        if ($request->input('action') === 'dg_valider' || $request->input('action') === 'dta_dg_valider') {

            $demande = DemandeApprobation::find($demandeId);

            $approbation = Approbation::create([
                'saison' => $demande->saison,
                'date_approbation' => $demande->date_approbation ?? date('Y-m-d'),
                'reference' => $demande->reference,
                'date_debut' => $demande->date_debut,
                'date_fin' => $demande->date_fin,
                'compagnie_id' => $demande->compagnie_id,
                'demande_id' => $demande->id,
            ]);
        }

        if ($request->input('action') === 'compagnie_cree_demande') {
            # code...
            //
            $demande = DemandeApprobation::find($demandeId);

            $updateDemande = $demande->update(
                [
                    'date_soumission' => date('Y-m-d'),
                    'statut' => 'EN_ATTENTE',
                    'dg_motif' => null,
                    'dta_motif' => null,
                    'dsna_motif' => null,
                    'dsad_motif' => null,
                    'dsv_motif' => null,

                ]
            );



            $state = $demande->etatDemande;

            if ($state->dg_valider || $state->dta_dg_valider) {
                # code...
                $demande->update(
                    [
                        'amender' => true
                    ]
                );
            }
            if ($state) {
                $state->resetAllApprovalStates();
            }
        }
        try {
            DB::beginTransaction();

            $etat = EtatDemandeApprobation::updateState(
                $demandeId,
                $validated['action'],
                auth()->id(),
                $validated['is_approved'] ?? false,
                $validated['is_rejected'] ?? false
            );

            $activity = Activity::log($validated['action']);

            DB::commit();

            return redirect()->back()
                ->with('success', 'État mis à jour avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    public function finalizeLogin($token)
    {
        $loginData = Cache::get('compagnie_login_token_' . $token);

        if (!$loginData || Auth::id() != $loginData['compagnie_user_id']) {
            abort(403, 'Invalid or expired login token');
        }

        $targetUser = User::find($loginData['target_user_id']);

        // Store original user
        session([
            'original_compagnie_user' => Auth::id(),
            'original_compagnie_name' => Auth::user()->name
        ]);

        Auth::login($targetUser);
        Cache::forget('compagnie_login_token_' . $token);

        return redirect()->route('user');
    }
}
