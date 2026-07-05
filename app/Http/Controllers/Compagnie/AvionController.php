<?php

namespace App\Http\Controllers\Compagnie;

use App\Http\Controllers\Controller;
use App\Models\Avion;
use App\Models\TypeAvion;
use App\Models\Proprietaire;
use App\Models\Compagnie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class AvionController extends Controller
{
    /**
     * Affiche la liste des avions
     */
    public function index()
    {
        $avions = Avion::with(['type', 'proprietaire', 'compagnie'])->get();
        $type_avions = TypeAvion::all();
        $proprietaires = Proprietaire::all();
        $compagnies = Compagnie::all();

        return view('avions.index', compact('avions', 'type_avions', 'proprietaires', 'compagnies'));
    }

    /**
     * Enregistre un nouvel avion
     */
    public function store(Request $request)
    {
            // Vérifier si c'est un ajout multiple
            if ($request->has('immatriculations') && is_array($request->immatriculations)) {
                return $this->storeMultiple($request);
            }
        $validator = Validator::make($request->all(), [
            'immatriculation' => 'required|string|max:50',
            'type_avion_id' => 'required|exists:type_avions,id',
            //'proprietaire_id' => 'required|exists:proprietaires,id',
            'compagnie_aerienne_id' => 'required|exists:compagnies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $avion = Avion::create($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Avion créé avec succès',
                'data' => $avion
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la création de l\'avion',
                'error' => $e->getMessage()
            ], 500);
        }
    }


/**
 * Store multiple aircraft at once
 */
public function storeMultiple(Request $request)
{
    $validator = Validator::make($request->all(), [
        'demande_autorisation_id' => 'required|exists:demande_autorisations,id',
        'immatriculations' => 'required|array|min:1',
        'immatriculations.*' => [
            'required',
            'string',
            'max:50'
        ],
        'type_avion_id' => 'required|exists:type_avions,id',
        'compagnie_aerienne_id' => 'required|exists:compagnies,id',
    ]);

    if ($validator->fails()) {
        if ($request->ajax()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }
        
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    try {
        DB::beginTransaction();
        
        $created = [];
        
        $failed = [];

        foreach ($request->immatriculations as $immatriculation) {
            try {

                $avion = Avion::create([
                    'immatriculation' => strtoupper($immatriculation),
                    'type_avion_id' => $request->type_avion_id,
                    'compagnie_aerienne_id' => $request->compagnie_aerienne_id,
                    'demande_autorisation_id' => $request->demande_autorisation_id,
                ]);
                
                $created[] = $avion->immatriculation;
                
            } catch (\Exception $e) {
                $failed[] = $immatriculation;
            }
        }

        DB::commit();

        $message = count($created) . " avion(s) créé(s) avec succès.";
    
        if (!empty($failed)) {
            $message .= " Échecs : " . implode(', ', $failed);
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => [
                    'created' => $created,
                    
                    'failed' => $failed
                ]
            ]);
        }

        return redirect()->back()
            ->with('success', $message)
            ->with('created_count', count($created));

    } catch (\Exception $e) {
        DB::rollBack();
        
        
        if ($request->ajax()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la création des avions: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->back()
            ->with('error', 'Erreur lors de la création des avions: ' . $e->getMessage())
            ->withInput();
    }
}
    /**
     * Met à jour un avion existant
     */
    public function update(Request $request, $id)
    {
        $avion = Avion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'immatriculation' => 'required|string|max:50',
            'type_avion_id' => 'required|exists:type_avions,id',
            //'proprietaire_id' => 'required|exists:proprietaires,id',
            'compagnie_aerienne_id' => 'required|exists:compagnies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $avion->update(array_merge($request->all(), ['valider' => 1, 'motif' => null]));

            return response()->json([
                'status' => 'success',
                'message' => 'Avion mis à jour avec succès',
                'data' => $avion
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour de l\'avion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprime un avion
     */
    public function destroy($id)
    {
        $avion = Avion::findOrFail($id);

        try {
            $avion->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Avion supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la suppression de l\'avion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les données d'un avion pour l'édition
     */
    public function edit($id)
    {
        $avion = Avion::with(['typeAvion', 'proprietaire', 'compagnie'])->findOrFail($id);
        return response()->json($avion);
    }
}
