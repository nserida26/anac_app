<?php
// app/Http/Controllers/Admin/ChecklistDemandeController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Models\Checklist;
use App\Models\ChecklistDemande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChecklistDemandeController extends Controller
{
    public function edit(Demande $demande)
    {
        // Get all checklists based on demande type
        $checklists = Checklist::where('type_demande_id', $demande->type_demande_id)
            ->orWhereNull('type_demande_id')
            ->orderBy('section')
            ->orderBy('ordre')
            ->get()
            ->groupBy('section');
        
        // Get existing responses
        $reponses = ChecklistDemande::where('demande_id', $demande->id)
            ->get()
            ->keyBy('checklist_id');
        
        return view('admin.demandes.checklist-modal', compact('demande', 'checklists', 'reponses'));
    }
    
    public function update(Request $request, Demande $demande)
    {
        $request->validate([
            'reponses' => 'required|array',
            'reponses.*.checklist_id' => 'required|exists:checklists,id',
            'reponses.*.etat' => 'nullable|in:OUI,NON',
            'reponses.*.mise_en_oeuvre' => 'nullable|in:S,NS,S/O',
            'reponses.*.observations' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            foreach ($request->reponses as $reponseData) {
                ChecklistDemande::updateOrCreate(
                    [
                        'demande_id' => $demande->id,
                        'checklist_id' => $reponseData['checklist_id']
                    ],
                    [
                        'etat' => $reponseData['etat'] ?? null,
                        'mise_en_oeuvre' => $reponseData['mise_en_oeuvre'] ?? null,
                        'observations' => $reponseData['observations'] ?? null,
                    ]
                );
            }
            
            DB::commit();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Checklist enregistrée avec succès!'
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Checklist enregistrée avec succès!')
                ->with('open_modal', true);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }
}