<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Aeroport;
use App\Models\Pays;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AeroportController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Aeroport::with('pays')->latest();
        
        // Recherche
        if ($request->has('search')) {
            $query->search($request->search);
        }
        
        // Filtrage par pays
        if ($request->has('pays_id')) {
            $query->byCountry($request->pays_id);
        }
        
        // Tri
        $sort = $request->get('sort', 'nom');
        $direction = $request->get('direction', 'asc');
        
        if (in_array($sort, ['nom', 'codeIATA', 'codeICAO', 'ville'])) {
            $query->orderBy($sort, $direction);
        }
        
        // Pagination
        $aeroports = $query->paginate(20)->withQueryString();
        $pays = Pays::orderBy('nom')->get();
        
        return view('admin.aeroports.index', compact('aeroports', 'pays'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pays = Pays::orderBy('nom')->get();
        return view('admin.aeroports.create', compact('pays'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            Aeroport::rules(),
            Aeroport::messages()
        );
        
        try {
            DB::beginTransaction();
            
            $aeroport = Aeroport::create($validated);
            
            DB::commit();
            
            Log::info('Nouvel aéroport créé', [
                'id' => $aeroport->id,
                'nom' => $aeroport->nom,
                'user_id' => auth()->id()
            ]);
            
            return redirect()
                ->route('aeroports.index')
                ->with('success', 'Aéroport créé avec succès!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création aéroport', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Aeroport $aeroport)
    {
        $aeroport->load(['pays', 'volsDepart', 'volsArrivee']);
        
        return view('admin.aeroports.show', compact('aeroport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aeroport $aeroport)
    {
        $pays = Pays::orderBy('nom')->get();
        return view('admin.aeroports.edit', compact('aeroport', 'pays'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aeroport $aeroport)
    {
        $validated = $request->validate(
            Aeroport::rules($aeroport->id),
            Aeroport::messages()
        );
        
        try {
            DB::beginTransaction();
            
            $aeroport->update($validated);
            
            DB::commit();
            
            Log::info('Aéroport mis à jour', [
                'id' => $aeroport->id,
                'nom' => $aeroport->nom,
                'user_id' => auth()->id()
            ]);
            
            return redirect()
                ->route('aeroports.index')
                ->with('success', 'Aéroport mis à jour avec succès!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur mise à jour aéroport', [
                'id' => $aeroport->id,
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aeroport $aeroport)
    {
        // Vérifier si l'aéroport est utilisé
        if ($aeroport->volsDepart()->exists() || $aeroport->volsArrivee()->exists()) {
            return back()
                ->with('error', 'Impossible de supprimer cet aéroport car il est utilisé dans des vols.');
        }
        
        try {
            DB::beginTransaction();
            
            $nom = $aeroport->nom;
            $aeroport->delete();
            
            DB::commit();
            
            Log::info('Aéroport supprimé', [
                'nom' => $nom,
                'user_id' => auth()->id()
            ]);
            
            return redirect()
                ->route('aeroports.index')
                ->with('success', 'Aéroport supprimé avec succès!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur suppression aéroport', [
                'id' => $aeroport->id,
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * API: Liste des aéroports pour autocomplete
     */
    public function apiIndex(Request $request)
    {
        $query = Aeroport::query();
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('codeIATA', 'LIKE', "%{$search}%")
                  ->orWhere('codeICAO', 'LIKE', "%{$search}%")
                  ->orWhere('ville', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->has('pays_id')) {
            $query->where('pays_id', $request->pays_id);
        }
        
        $aeroports = $query->limit(50)->get();
        
        return response()->json([
            'data' => $aeroports->map(function ($aeroport) {
                return [
                    'id' => $aeroport->id,
                    'text' => $aeroport->nom_complet,
                    'codeIATA' => $aeroport->codeIATA,
                    'codeICAO' => $aeroport->codeICAO,
                    'ville' => $aeroport->ville,
                    'pays' => $aeroport->pays->nom ?? ''
                ];
            })
        ]);
    }

    /**
     * Import CSV des aéroports
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048'
        ]);
        
        try {
            $path = $request->file('file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            
            $headers = array_shift($data);
            $imported = 0;
            $failed = 0;
            $errors = [];
            
            foreach ($data as $key => $row) {
                try {
                    // Assurez-vous que la ligne a le bon nombre de colonnes
                    if (count($row) !== count($headers)) {
                        $errors[] = "Ligne " . ($key + 2) . ": Nombre de colonnes incorrect";
                        $failed++;
                        continue;
                    }
                    
                    $rowData = array_combine($headers, $row);
                    
                    // Validation des données
                    $validated = validator($rowData, [
                        'nom' => 'required|string|max:100',
                        'codeIATA' => 'required|string|size:3|unique:aeroports,codeIATA',
                        'codeICAO' => 'required|string|size:4|unique:aeroports,codeICAO',
                        'pays_id' => 'required|exists:pays,id',
                        'ville' => 'required|string|max:100',
                        'latitude' => 'nullable|numeric',
                        'longitude' => 'nullable|numeric'
                    ])->validate();
                    
                    Aeroport::create($validated);
                    $imported++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Ligne " . ($key + 2) . ": " . $e->getMessage();
                    $failed++;
                }
            }
            
            $message = "Import terminé: {$imported} importés, {$failed} échoués.";
            
            if (!empty($errors)) {
                session()->flash('import_errors', $errors);
            }
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Export des aéroports en CSV
     */
    public function export()
    {
        $fileName = 'aeroports_' . date('Y-m-d') . '.csv';
        $aeroports = Aeroport::with('pays')->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];
        
        $callback = function() use ($aeroports) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'ID', 'Nom', 'Code IATA', 'Code ICAO', 
                'Pays', 'ID Pays', 'Ville', 'Latitude', 'Longitude'
            ]);
            
            // Données
            foreach ($aeroports as $aeroport) {
                fputcsv($file, [
                    $aeroport->id,
                    $aeroport->nom,
                    $aeroport->codeIATA,
                    $aeroport->codeICAO,
                    $aeroport->pays->nom ?? '',
                    $aeroport->pays_id,
                    $aeroport->ville,
                    $aeroport->latitude,
                    $aeroport->longitude
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Afficher la carte des aéroports
     */
    public function map()
    {
        $aeroports = Aeroport::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('pays')
            ->get();
        
        return view('aeroports.map', compact('aeroports'));
    }
}