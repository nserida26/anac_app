<?php

namespace App\Http\Controllers\Compagnie;

use App\Models\VolApprobation;
use App\Models\Aeroport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class VolApprobationController extends Controller
{
    /**
     * Display a listing of flight approvals
     */
    public function index()
    {
        $volApprobations = VolApprobation::with(['aeroportDepart', 'aeroportArrivee', 'demandeApprobation'])
            ->get();

        $aeroports = Aeroport::all();

        return view('vol_approbations.index', compact('volApprobations', 'aeroports'));
    }

    /**
     * Store a newly created flight approval
     */
    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'numero_vol' => 'required|string|max:20',
            'jours_operation' => 'required|array',
            'jours_operation.*' => 'in:J1,J2,J3,J4,J5,J6,J7',
            'aeroport_depart_id' => 'required|exists:aeroports,id',
            'aeroport_arrivee_id' => 'required|exists:aeroports,id',
            'heure_depart' => 'required|date_format:H:i',
            'heure_arrivee' => 'required|date_format:H:i',
            'periode' => 'required|string',
            'demande_approbation_id' => 'required|exists:demande_approbations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        $period = explode(' - ', $request->periode);
        $date_debut = $period[0];
        $date_fin = $period[1];


        try {
            $volApprobation = VolApprobation::create([
                'numero_vol' => $request->numero_vol,
                'jours_operation' => json_encode($request->jours_operation),
                'aeroport_depart_id' => $request->aeroport_depart_id,
                'aeroport_arrivee_id' => $request->aeroport_arrivee_id,
                'heure_depart' => $request->heure_depart,
                'heure_arrivee' => $request->heure_arrivee,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
                'demande_approbation_id' => $request->demande_approbation_id,
            ]);


            return response()->json([
                'status' => 'success',
                'message' => 'Vol approbation créée avec succès',
                'data' => $volApprobation
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la création de la vol approbation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified flight approval
     */
    public function update(Request $request, $id)
    {
        $volApprobation = VolApprobation::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'numero_vol' => 'required|string|max:20',
            'jours_operation' => 'required|array',
            'jours_operation.*' => 'in:J1,J2,J3,J4,J5,J6,J7',
            'aeroport_depart_id' => 'required|exists:aeroports,id',
            'aeroport_arrivee_id' => 'required|exists:aeroports,id',
            'heure_depart' => 'required|date_format:H:i',
            'heure_arrivee' => 'required|date_format:H:i',
            'periode' => 'required|string',
            'demande_approbation_id' => 'required|exists:demande_approbations,id',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        $period = explode(' - ', $request->periode);
        $date_debut = $period[0];
        $date_fin = $period[1];

        try {
            $volApprobation->update([
                'numero_vol' => $request->numero_vol,
                'jours_operation' => json_encode($request->jours_operation),
                'aeroport_depart_id' => $request->aeroport_depart_id,
                'aeroport_arrivee_id' => $request->aeroport_arrivee_id,
                'heure_depart' => $request->heure_depart,
                'heure_arrivee' => $request->heure_arrivee,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
                'demande_approbation_id' => $request->demande_approbation_id,
                'valider' => 1,
                'motif' => null
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Vol approbation mise à jour avec succès',
                'data' => $volApprobation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour de la vol approbation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified flight approval
     */
    public function destroy($id)
    {
        $volApprobation = VolApprobation::findOrFail($id);

        try {
            $volApprobation->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Vol approbation supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la suppression de la vol approbation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the specified flight approval for editing
     */
    public function edit($id)
    {
        $volApprobation = VolApprobation::with(['aeroportDepart', 'aeroportArrivee', 'demandeApprobation'])
            ->findOrFail($id);

        // Decode the JSON days for editing
        $volApprobation->jours_operation = json_decode($volApprobation->jours_operation);

        return response()->json($volApprobation);
    }
}
