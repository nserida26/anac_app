<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Demande;
use App\Models\Demandeur;
use App\Models\Empreinte;
use App\Models\EtatDemande;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $demandeurs = Demandeur::get();
        return response()->json($demandeurs);
    }

    // 📌 Enrôlement de l’empreinte
    public function enroler(Request $request)
    {

        $validated = $request->validate([
            'demandeur_id' => 'required|exists:demandeurs,id',
            'empreinte_data' => 'required|string'
        ]);

        try {
            // Decode and validate the fingerprint data
            $empreinteData = base64_decode($request->empreinte_data);
            if ($empreinteData === false) {
                return response()->json(['error' => 'Invalid base64 fingerprint data'], 400);
            }

            // Additional validation for fingerprint data
            if (strlen($empreinteData) < 100) {
                return response()->json(['error' => 'Invalid fingerprint data length --- ' . strlen($empreinteData)], 400);
            }

            // Hash the fingerprint data
            $empreinteHash = hash('sha256', $empreinteData);

            // Check for duplicate fingerprints
            $existing = Empreinte::where('empreinte_hash', $empreinteHash)->first();
            if ($existing) {
                return response()->json([
                    'message' => 'Fingerprint already exists',
                    'existing_demandeur_id' => $existing->demandeur_id
                ], 409);
            }

            // Store the fingerprint
            $empreinte = Empreinte::create([
                'demandeur_id' => $validated['demandeur_id'],
                'empreinte_hash' => $empreinteHash,
                'empreinte_data' => $request->empreinte_data // Storing the base64 encoded version
            ]);

            // Update application state if needed
            if ($request->has('demande_id')) {
                EtatDemande::where('demande_id', $request->demande_id)
                    ->update(['agent_enroler' => true]);
            }

            return response()->json([
                'message' => 'Fingerprint saved successfully',
                'empreinte_id' => $empreinte->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Fingerprint processing failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // 📌 Vérification de l’empreinte
    public function verifier(Request $request)
    {
        $request->validate([
            'demandeur_id' => 'required|exists:demandeurs,id',
            'empreinte_data' => 'required'
        ]);

        //$empreinteHash = hash('sha256', base64_decode($request->empreinte_data));
        $count = Empreinte::whereRaw('LEFT(empreinte_data, 100) = ?', [substr($request->empreinte_data, 0, 100)])->count();


        if ($count >= 1) {
            return response()->json(['message' => 'Authentification réussie']);
        } else {
            return response()->json(['message' => 'Échec d\'authentification'], 401);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
