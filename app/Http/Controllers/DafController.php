<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\EtatDemande;
use Illuminate\Http\Request;

use App\Models\OrdreRecette;
use App\Models\Demandeur;
use App\Models\Facture;
use App\Models\Paiement;
use App\Models\PaiementAutorisation;
use App\Models\User;
use App\Services\LicenseApplicationNotificationService;
use Illuminate\Support\Facades\DB;

class DafController extends Controller
{
    protected $notificationService;

    public function __construct(LicenseApplicationNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $ordres = OrdreRecette::with('demande')->get();
        $factures = Facture::with('demande')->get();
        $demandeurs = Demandeur::whereHas('user', function ($query) {
            $query->where('user_type', 'licence');
        })->get();

        $paiements = Paiement::with('demande')->get();

        $paiementAutorisations = PaiementAutorisation::with('demande')->get();
        return view('daf.index', compact('demandeurs','ordres', 'factures', 'paiements', 'paiementAutorisations'));
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Paiement $paiement)
    {

        //
        return view('daf.show', compact('paiement'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Facture $facture)
    {
        //
        return view('daf.edit', compact('facture'));
    }

    /**
     * Show the form for invoicing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function invoiceAutorisation($id)
    {

        //
        $paiement = PaiementAutorisation::find($id);

        return view('daf.invoiceAutorisation', compact('paiement'));
    }
    public function invoice($id)
    {

        //
        $facture = Facture::find($id);
        $dg = User::role('dg')->first();

        return view('daf.invoice', compact('facture', 'dg'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Facture $facture)
    {
        //
        $request->validate([
            'montant' => 'required|numeric|min:0',
            'date_facture' => 'required|date',
            'date_limite' => 'required|date'
        ]);

        if ($request->hasFile('facture')) {
            $facturePath = $request->file('facture')->store('paiements', 'public');
        } else {
            $facturePath = null;
        }
        $f = $facture->update([
            'montant' => $request->montant,
            'date_facture' => $request->date_facture,
            'date_limite' => $request->date_limite,
            'facture' => $facturePath
        ]);
        $paiement = Paiement::where('demande_id', $facture->demande_id)->update(
            [
                'montant' => $request->montant,
            ]
        );

        return redirect()->route('daf')->with('success', 'Facture mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Facture $facture)
    {
        $facture->delete();
        return redirect()->route('daf')->with('success', 'Facture supprimée.');
        //
    }

    public function valider(Facture $facture)
    {
        $demande = $facture->demande;
        $f = $facture->update(
            [
                'statut' => 'Confirmée'
            ]
        );

        $paiement = Paiement::create(
            [
                'demande_id' => $facture->demande_id,
                'montant' => $facture->montant,
                'reference' => 'PA-' . strtoupper(uniqid()),
                'statut' => 'En attente'
            ]
        );
        $etat_demande = EtatDemande::where('demande_id', $facture->demande_id)->update(
            [
                'daf_demande_pay' =>  true
            ]
        );
        $activity = Activity::log('daf_demande_pay',$facture->demande_id);
        $invoicePath = asset('/uploads/' . $facture->facture);
        if (!empty($demande->demandeur->user->whatsapp)) {
            $notification = $this->notificationService->sendPaymentRequest(
                applicationNumber: $demande->code,
                applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                recipientPhone: $demande->demandeur->user->whatsapp,
                recipientRole: 'Demandeur',
                amount: $facture->montant,
                dueDate: $facture->date_limite,
                invoiceUrl: $invoicePath,
                applicantName: $demande->demandeur->np,
            );
        }

        return redirect()->route('daf')->with('success', 'Facture Confirmée.');
        //
    }
    public function validerPaiement(Paiement $paiement)
    {
        $demande = $paiement->demande;
        $p = $paiement->update(
            [
                'statut' => 'Payé'
            ]
        );
        $etat_demande = $demande->etatDemande->update(
            [
                'daf_confirme_pay' =>  true
            ]
        );
        $activity = Activity::log('daf_confirme_pay',$facture->demande_id);
        $dg = User::role('dg')
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->latest()->first();
        $dsv = User::role('dsv')
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('signature');
            })
            ->latest()->first();
        if (!empty($demande->demandeur->user->whatsapp)) {
            $response = $this->notificationService->confirmToPayer(
                invoiceNumber: $paiement->reference,
                applicationNumber: $demande->code,
                applicationType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                recipientPhone: $demande->demandeur->user->whatsapp,
                recipientName: 'Demandeur',
                amount: $paiement->montant,
                paymentDate: $paiement->date_paiement
            );
        }
        if (!empty($dg->whatsapp) && !empty($dsv->whatsapp)) {
                $this->notificationService->sendApplicationActionRequired(
                    demandeNumber: $demande->code,
                    demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                    recipientRole: 'DG',
                    recipientPhone: $dg->whatsapp,
                    actionType: 'signature',
                    applicantName: $demande->demandeur->np,
                );
                $this->notificationService->sendApplicationActionRequired(
                    demandeNumber: $demande->code,
                    demandeType: $demande->typeDemande->nom_en . ' ' . $demande->typeDemande->nom_fr,
                    recipientRole: 'DSV',
                    recipientPhone: $dsv->whatsapp,
                    actionType: 'signature',
                    applicantName: $demande->demandeur->np,
                );
        }

        return redirect()->route('daf')->with('success', 'Paiement Confirmée.');
        //
    }
    /**
     * Affiche le formulaire de création de facture(s)
     */
    public function create(Request $request, OrdreRecette $ordre = null)
    {
        $selectedOrdres = collect();
        $isBulkMode = false;
        
        // Mode création multiple (bulk)
        if ($request->has('bulk_mode') || $request->has('bulk')) {
            $ordreIds = $request->input('ordre_ids', []);
            
            if (!empty($ordreIds)) {
                $selectedOrdres = OrdreRecette::whereIn('id', $ordreIds)
                    ->where('statut', 'Validé')
                    ->whereDoesntHave('demande.facture')
                    ->with(['demande.demandeur.compagnie'])
                    ->get();
                    
                $isBulkMode = $selectedOrdres->count() > 1;
            }
        } 
        // Mode création simple
        elseif ($ordre && $ordre->exists) {
            if ($ordre->statut === 'Validé' && empty($ordre->demande->facture)) {
                $selectedOrdres = collect([$ordre->load('demande.demandeur.compagnie')]);
            }
        }
        
        // Si aucun ordre valide n'est trouvé
        if ($selectedOrdres->isEmpty()) {
            return redirect()->route('daf')
                ->with('error', trans('trans.no_valid_orders_selected'));
        }
        
        return view('daf.create', compact('selectedOrdres', 'isBulkMode'));
    }
    
    /**
     * Stocke une ou plusieurs factures
     */
    public function store(Request $request)
    {
        $isBulkMode = $request->has('ordre_ids');
        
        $rules = [
            'date_facture' => 'required|date',
            'date_limite' => 'required|date|after_or_equal:date_facture',
            'mode_paiement' => 'nullable|string',
            'conditions_paiement' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
        
        if ($isBulkMode) {
            $rules['factures'] = 'required|array';
            $rules['factures.*'] = 'required|file|mimes:pdf,doc,docx|max:10240';
            $rules['ordre_ids'] = 'required|array';
            $rules['ordre_ids.*'] = 'exists:ordres_recette,id';
        } else {
            $rules['facture'] = 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240';
            $rules['ordre_id'] = 'required|exists:ordres_recette,id';
        }
        
        $validated = $request->validate($rules);
        
        DB::beginTransaction();
        
        try {
            $facturesCrees = [];
            
            if ($isBulkMode) {
                // Création multiple
                foreach ($request->ordre_ids as $ordreId) {
                    if ($request->hasFile("factures.{$ordreId}")) {
                        $facture = $this->creerFacture(
                            $ordreId,
                            $validated,
                            $request->file("factures.{$ordreId}")
                        );
                        $facturesCrees[] = $facture;
                    }
                }
                
                $message = trans('trans.multiple_invoices_created', ['count' => count($facturesCrees)]);
            } else {
                // Création simple
                $facture = $this->creerFacture(
                    $request->ordre_id,
                    $validated,
                    $request->file('facture')
                );
                $facturesCrees[] = $facture;
                $message = trans('trans.invoice_created_successfully');
            }
            
            DB::commit();
            
            return redirect()->route('daf')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', trans('trans.invoice_creation_error') . ': ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Crée une facture individuelle
     */
    private function creerFacture($ordreId, array $data, $file)
    {
        $ordre = OrdreRecette::findOrFail($ordreId);
        
        // Vérifier que l'ordre est toujours valide
        if ($ordre->statut !== 'Validé' || !empty($ordre->demande->facture)) {
            throw new \Exception(trans('trans.order_not_valid_for_invoice', ['ref' => $ordre->reference]));
        }
        
        // Générer la référence
        $reference = 'FAC-' . date('Y') . '-' . str_pad(Facture::count() + 1, 6, '0', STR_PAD_LEFT);
        
        // Sauvegarder le fichier
        $path = $file->store('factures/' . date('Y/m'), 'public');
        
        // Créer la facture
        $facture = Facture::create([
            'reference' => $reference,
            'demande_id' => $ordre->demande->id,
            'date_facture' => $data['date_facture'],
            'date_limite' => $data['date_limite'],
            'montant' => $ordre->montant,
            'statut' => 'En attente',
            'mode_paiement' => $data['mode_paiement'] ?? null,
            'conditions_paiement' => $data['conditions_paiement'] ?? null,
            'notes' => $data['notes'] ?? null,
            'fichier_facture' => $path,
            'created_by' => auth()->id(),
        ]);
        
        return $facture;
    }
    
    /**
     * Méthode pour la création en masse (alternative)
     */
    public function bulkCreate(Request $request)
    {
        $ordreIds = explode(',', $request->input('orders', ''));
        
        if (empty($ordreIds)) {
            return redirect()->route('daf')
                ->with('error', trans('trans.no_orders_selected'));
        }
        
        $selectedOrdres = OrdreRecette::whereIn('id', $ordreIds)
            ->where('statut', 'Validé')
            ->whereDoesntHave('demande.facture')
            ->with(['demande.demandeur.compagnie'])
            ->get();
            
        if ($selectedOrdres->isEmpty()) {
            return redirect()->route('daf')
                ->with('error', trans('trans.no_valid_orders_selected'));
        }
        
        $isBulkMode = true;
        
        return view('daf.create', compact('selectedOrdres', 'isBulkMode'));
    }
}
