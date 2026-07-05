<?php

namespace App\Http\Controllers\Compagnie;

use App\Models\Vol;
use App\Models\Avion;
use App\Models\TypeVol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\DemandeAutorisation;
use App\Models\Escale;


class VolController extends Controller
{
    /**
     * Affiche la liste des vols
     */
    public function index()
    {
        $vols = Vol::with(['avion', 'type'])->get();
        $avions = Avion::all();
        $type_vols = TypeVol::all();

        return view('vols.index', compact('vols', 'avions', 'type_vols'));
    }

// In your VolController store method:
public function store(Request $request)
{
    $validated = $request->validate([
        'numero_vol' => 'nullable|string|max:20',
        'aeroport_depart_id' => 'required|exists:aeroports,id',
        'aeroport_arrivee_id' => [
            'required',
            'exists:aeroports,id',
            // Validation personnalisée : différent de l'aéroport de départ si pas d'escales
            function ($attribute, $value, $fail) use ($request) {
                $hasEscales = $request->has('escales') && 
                              is_array($request->escales) && 
                              count(array_filter($request->escales, function($escale) {
                                  return !empty($escale['aeroport_id']);
                              })) > 0;
                
                // Si pas d'escales, l'aéroport d'arrivée doit être différent de l'aéroport de départ
                if (!$hasEscales && $value == $request->aeroport_depart_id) {
                    $fail('L\'aéroport d\'arrivée doit être différent de l\'aéroport de départ lorsqu\'il n\'y a pas d\'escales.');
                }
            },
        ],
        'date_depart' => 'required|date_format:H:i',
        'date_arrivee' => 'required|date_format:H:i',
        'nbr_passagers' => 'nullable|integer|min:0',
        'demande_autorisation_id' => 'required|exists:demande_autorisations,id',
        'escales' => 'nullable|array',
        'escales.*.aeroport_id' => [
            'required',
            'exists:aeroports,id',
            // Validation pour chaque escale : différente de l'aéroport précédent
            function ($attribute, $value, $fail) use ($request) {
                // Extraire l'index de l'escale depuis l'attribut (ex: escales.0.aeroport_id)
                preg_match('/escales\.(\d+)\.aeroport_id/', $attribute, $matches);
                $currentIndex = isset($matches[1]) ? (int)$matches[1] : null;
                
                if ($currentIndex === null) return;
                
                // Pour la première escale, comparer avec l'aéroport de départ
                if ($currentIndex === 0) {
                    if ($value == $request->aeroport_depart_id) {
                        $fail('La première escale doit être différente de l\'aéroport de départ.');
                    }
                } else {
                    // Pour les escales suivantes, comparer avec l'escale précédente
                    $previousIndex = $currentIndex - 1;
                    if (isset($request->escales[$previousIndex]['aeroport_id']) && 
                        $value == $request->escales[$previousIndex]['aeroport_id']) {
                        $fail('Deux escales consécutives ne peuvent pas avoir le même aéroport.');
                    }
                }
            },
        ],
        'escales.*.date_arrivee' => 'required|date_format:H:i',
        'escales.*.date_depart' => [
            'required',
            'date_format:H:i'
        ],
    ], [
        'aeroport_depart_id.required' => 'L\'aéroport de départ est obligatoire.',
        'aeroport_arrivee_id.required' => 'L\'aéroport d\'arrivée est obligatoire.',
        'date_depart.required' => 'La date de départ est obligatoire.',
        'date_arrivee.required' => 'La date d\'arrivée est obligatoire.',
        
    ]);
    
    // Boucle supplémentaire pour vérifier toutes les escales
    $hasValidEscales = $request->has('escales') && 
                       is_array($request->escales) && 
                       count(array_filter($request->escales, function($escale) {
                           return !empty($escale['aeroport_id']);
                       })) > 0;
    
    if (!$hasValidEscales) {
        // Vérification supplémentaire : aéroport d'arrivée != aéroport de départ
        if ($request->aeroport_depart_id == $request->aeroport_arrivee_id) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => [
                    'aeroport_arrivee_id' => ['L\'aéroport d\'arrivée doit être différent de l\'aéroport de départ lorsqu\'il n\'y a pas d\'escales.'],
                ]
            ], 422);
        }
        
    } else {
        // Avec escales : vérifier que la dernière escale n'est pas l'aéroport d'arrivée
        $escales = array_values(array_filter($request->escales, function($escale) {
            return !empty($escale['aeroport_id']);
        }));
        
        if (count($escales) > 0) {
            $lastEscale = end($escales);
            
            // Vérifier que la dernière escale n'est pas identique à l'aéroport d'arrivée
            if ($lastEscale['aeroport_id'] == $request->aeroport_arrivee_id) {
                return response()->json([
                    'message' => 'Erreur de validation',
                    'errors' => [
                        'aeroport_arrivee_id' => ['L\'aéroport d\'arrivée doit être différent de la dernière escale.'],
                    ]
                ], 422);
            }
        
        }
    }
    
    // Create the vol
    $vol = Vol::create([
        'numero_vol' => $validated['numero_vol'] ?? null,
        'aeroport_depart_id' => $validated['aeroport_depart_id'],
        'aeroport_arrivee_id' => $validated['aeroport_arrivee_id'],
        'date_depart' => $validated['date_depart'],
        'date_arrivee' => $validated['date_arrivee'],
        'nbr_passagers' => $validated['nbr_passagers'] ?? null,
        'demande_autorisation_id' => $validated['demande_autorisation_id'],
    ]);
    
    // Create escales if provided
    if ($request->has('escales')) {
        $escaleOrder = 1;
        foreach ($request->escales as $escale) {
            // Ne créer que les escales avec un aéroport défini
            if (!empty($escale['aeroport_id'])) {
                Escale::create([
                    'vol_id' => $vol->id,
                    'aeroport_id' => $escale['aeroport_id'],
                    'date_arrivee' => $escale['date_arrivee'],
                    'date_depart' => $escale['date_depart'],
                    'ordre' => $escaleOrder++,
                ]);
            }
        }
    }
    
    // Charger les relations pour la réponse
    $vol->load(['aeroportDepart', 'aeroportArrivee', 'escales.aeroport']);
    
    return response()->json([
        'message' => 'Vol créé avec succès',
        'vol' => $vol
    ]);
}

public function update(Request $request, $id)
{
    $vol = Vol::findOrFail($id);
    
    $validated = $request->validate([
        'numero_vol' => 'nullable|string|max:20',
        'aeroport_depart_id' => 'required|exists:aeroports,id',
        'aeroport_arrivee_id' => [
            'required',
            'exists:aeroports,id',
            // Validation personnalisée : différent de l'aéroport de départ si pas d'escales
            function ($attribute, $value, $fail) use ($request) {
                $hasEscales = $request->has('escales') && 
                              is_array($request->escales) && 
                              count(array_filter($request->escales, function($escale) {
                                  return !empty($escale['aeroport_id']);
                              })) > 0;
                
                // Si pas d'escales, l'aéroport d'arrivée doit être différent de l'aéroport de départ
                if (!$hasEscales && $value == $request->aeroport_depart_id) {
                    $fail('L\'aéroport d\'arrivée doit être différent de l\'aéroport de départ lorsqu\'il n\'y a pas d\'escales.');
                }
            },
        ],
        'date_depart' => 'required|date_format:H:i',
        'date_arrivee' => 'required|date_format:H:i',
        'nbr_passagers' => 'nullable|integer|min:0',
        'demande_autorisation_id' => 'required|exists:demande_autorisations,id',
        'escales' => 'nullable|array',
        'escales.*.id' => 'nullable|exists:escales,id',
        'escales.*.aeroport_id' => [
            'required',
            'exists:aeroports,id',
            // Validation pour chaque escale : différente de l'aéroport précédent
            function ($attribute, $value, $fail) use ($request) {
                // Extraire l'index de l'escale depuis l'attribut (ex: escales.0.aeroport_id)
                preg_match('/escales\.(\d+)\.aeroport_id/', $attribute, $matches);
                $currentIndex = isset($matches[1]) ? (int)$matches[1] : null;
                
                if ($currentIndex === null) return;
                
                // Pour la première escale, comparer avec l'aéroport de départ
                if ($currentIndex === 0) {
                    if ($value == $request->aeroport_depart_id) {
                        $fail('La première escale doit être différente de l\'aéroport de départ.');
                    }
                } else {
                    // Pour les escales suivantes, comparer avec l'escale précédente
                    $previousIndex = $currentIndex - 1;
                    if (isset($request->escales[$previousIndex]['aeroport_id']) && 
                        $value == $request->escales[$previousIndex]['aeroport_id']) {
                        $fail('Deux escales consécutives ne peuvent pas avoir le même aéroport.');
                    }
                }
            },
        ],
        'escales.*.date_arrivee' => 'required|date_format:H:i',
        'escales.*.date_depart' => [
            'required',
            'date_format:H:i'
        ],
    ], [
        'aeroport_depart_id.required' => 'L\'aéroport de départ est obligatoire.',
        'aeroport_arrivee_id.required' => 'L\'aéroport d\'arrivée est obligatoire.',
        'date_depart.required' => 'La date de départ est obligatoire.',
        'date_arrivee.required' => 'La date d\'arrivée est obligatoire.',
        
    ]);
    
    // Boucle supplémentaire pour vérifier toutes les escales
    $hasValidEscales = $request->has('escales') && 
                       is_array($request->escales) && 
                       count(array_filter($request->escales, function($escale) {
                           return !empty($escale['aeroport_id']);
                       })) > 0;
    
    if (!$hasValidEscales) {
        // Vérification supplémentaire : aéroport d'arrivée != aéroport de départ
        if ($request->aeroport_depart_id == $request->aeroport_arrivee_id) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => [
                    'aeroport_arrivee_id' => ['L\'aéroport d\'arrivée doit être différent de l\'aéroport de départ lorsqu\'il n\'y a pas d\'escales.'],
                ]
            ], 422);
        }
        
    } else {
        // Avec escales : vérifier que la dernière escale n'est pas l'aéroport d'arrivée
        $escales = array_values(array_filter($request->escales, function($escale) {
            return !empty($escale['aeroport_id']);
        }));
        
        if (count($escales) > 0) {
            $lastEscale = end($escales);
            
            // Vérifier que la dernière escale n'est pas identique à l'aéroport d'arrivée
            if ($lastEscale['aeroport_id'] == $request->aeroport_arrivee_id) {
                return response()->json([
                    'message' => 'Erreur de validation',
                    'errors' => [
                        'aeroport_arrivee_id' => ['L\'aéroport d\'arrivée doit être différent de la dernière escale.'],
                    ]
                ], 422);
            }
            
            // Vérifier la cohérence temporelle des escales
            $previousTime = $request->date_depart;
            foreach ($escales as $index => $escale) {
                if (strtotime($escale['date_arrivee']) <= strtotime($previousTime)) {
                    return response()->json([
                        'message' => 'Erreur de validation',
                        'errors' => [
                            "escales.{$index}.date_arrivee" => ['L\'heure d\'arrivée à l\'escale doit être après l\'heure de départ précédente.'],
                        ]
                    ], 422);
                }
                if (strtotime($escale['date_depart']) <= strtotime($escale['date_arrivee'])) {
                    return response()->json([
                        'message' => 'Erreur de validation',
                        'errors' => [
                            "escales.{$index}.date_depart" => ['L\'heure de départ de l\'escale doit être après son heure d\'arrivée.'],
                        ]
                    ], 422);
                }
                $previousTime = $escale['date_depart'];
            }
            
            // Vérifier que l'arrivée finale est après la dernière escale
            if (strtotime($request->date_arrivee) <= strtotime($previousTime)) {
                return response()->json([
                    'message' => 'Erreur de validation',
                    'errors' => [
                        'date_arrivee' => ['L\'heure d\'arrivée finale doit être après la dernière escale.'],
                    ]
                ], 422);
            }
        }
    }
    
    // Update the vol
    $vol->update([
        'numero_vol' => $validated['numero_vol'],
        'aeroport_depart_id' => $validated['aeroport_depart_id'],
        'aeroport_arrivee_id' => $validated['aeroport_arrivee_id'],
        'date_depart' => $validated['date_depart'],
        'date_arrivee' => $validated['date_arrivee'],
        'nbr_passagers' => $validated['nbr_passagers'] ?? null,
        'demande_autorisation_id' => $validated['demande_autorisation_id'],
    ]);
    
    // Update escales
    $existingEscaleIds = $vol->escales->pluck('id')->toArray();
    $updatedEscaleIds = [];
    
    if ($request->has('escales')) {
        $escaleOrder = 1;
        foreach ($request->escales as $escaleData) {
            // Ne traiter que les escales avec un aéroport défini
            if (empty($escaleData['aeroport_id'])) {
                continue;
            }
            
            if (isset($escaleData['id'])) {
                // Update existing escale
                $escale = Escale::find($escaleData['id']);
                if ($escale && $escale->vol_id == $vol->id) {
                    $escale->update([
                        'aeroport_id' => $escaleData['aeroport_id'],
                        'date_arrivee' => $escaleData['date_arrivee'],
                        'date_depart' => $escaleData['date_depart'],
                        'ordre' => $escaleOrder,
                    ]);
                    $updatedEscaleIds[] = $escaleData['id'];
                }
            } else {
                // Create new escale
                $escale = Escale::create([
                    'vol_id' => $vol->id,
                    'aeroport_id' => $escaleData['aeroport_id'],
                    'date_arrivee' => $escaleData['date_arrivee'],
                    'date_depart' => $escaleData['date_depart'],
                    'ordre' => $escaleOrder,
                ]);
                $updatedEscaleIds[] = $escale->id;
            }
            $escaleOrder++;
        }
    }
    
    // Delete escales that were removed
    $escalesToDelete = array_diff($existingEscaleIds, $updatedEscaleIds);
    Escale::whereIn('id', $escalesToDelete)->delete();
    
    // Charger les relations pour la réponse
    $vol->load(['aeroportDepart', 'aeroportArrivee', 'escales.aeroport']);
    
    return response()->json([
        'message' => 'Vol mis à jour avec succès',
        'vol' => $vol
    ]);
}

    /**
     * Supprime un vol
     */
    public function destroy($id)
    {
        $vol = Vol::findOrFail($id);

        try {
            $vol->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Vol supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la suppression du vol',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Récupère les données d'un vol pour l'édition
     */
    public function edit($id)
    {
        $vol = Vol::with(['avion', 'type'])->findOrFail($id);
        return response()->json($vol);
    }
}
