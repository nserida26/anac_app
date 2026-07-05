<?php
namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\TypeDemande;
use App\Models\TypeLicence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ChecklistController extends Controller
{
public function index(Request $request)
{
    $query = Checklist::query();
    
    // Search filter
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('numero', 'LIKE', "%{$search}%")
              ->orWhere('libelle', 'LIKE', "%{$search}%");
        });
    }
    
    // Type Demande filter
    if ($request->filled('type_demande')) {
        $query->where('type_demande_id', $request->type_demande);
    }
    
    // Type Licence filter
    if ($request->filled('type_licence')) {
        $query->where('type_licence_id', $request->type_licence);
    }
    
    $checklists = $query->with(['typeDemande', 'typeLicence'])
                        ->orderBy('numero', 'asc')
                        ->paginate(15);
    
    // Get all type demandes and licences for filter dropdowns
    $typeDemandes = TypeDemande::orderBy('nom_fr')->get();
    $typeLicences = TypeLicence::orderBy('nom')->get();
    
    return view('admin.checklists.index', compact('checklists', 'typeDemandes', 'typeLicences'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $typeDemandes = TypeDemande::all();
        $typeLicences = TypeLicence::all();
        
        return view('admin.checklists.create', compact('typeDemandes', 'typeLicences'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero' => 'required|string|max:255',
            'index' => 'nullable|string',
            'type' => 'nullable|string|max:100',
            'libelle' => 'required|string',
            'section' => 'nullable|string|max:255',
            'ordre' => 'nullable|string',
            'type_licence_id' => 'nullable|exists:type_licences,id',
            'type_demande_id' => 'nullable|exists:type_demandes,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        Checklist::create($request->all());
        
        return redirect()->route('checklists.index')
            ->with('success', 'Checklist created successfully.');
    }

    /**
     * Store multiple checklists at once
     */
    public function storeMultiple(Request $request)
    {
        $checklists = $request->input('checklists', []);
        
        if (empty($checklists)) {
            return redirect()->back()
                ->with('error', 'No checklists to save.');
        }
        
        $errors = [];
        $successCount = 0;
        
        DB::beginTransaction();
        
        try {
            foreach ($checklists as $index => $checklistData) {
                $validator = Validator::make($checklistData, [
                    'numero' => 'required|string|max:255',
                    'index' => 'nullable|string',
                    'type' => 'nullable|string|max:100',
                    'libelle' => 'required|string',
                    'section' => 'nullable|string|max:255',
                    'ordre' => 'nullable|integer',
                    'type_licence_id' => 'nullable|exists:type_licences,id',
                    'type_demande_id' => 'nullable|exists:type_demandes,id',
                ]);
                
                if ($validator->fails()) {
                    $errors[] = "Checklist #" . ($index + 1) . ": " . implode(', ', $validator->errors()->all());
                    continue;
                }
                
                Checklist::create($checklistData);
                $successCount++;
            }
            
            if ($successCount > 0) {
                DB::commit();
                
                $message = $successCount . " checklist(s) created successfully.";
                if (count($errors) > 0) {
                    $message .= " But " . count($errors) . " checklist(s) had errors: " . implode('; ', $errors);
                }
                
                return redirect()->route('checklists.index')
                    ->with('success', $message);
            } else {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'No checklists were created. Errors: ' . implode('; ', $errors))
                    ->withInput();
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Checklist $checklist)
    {
        $checklist->load(['typeDemande', 'typeLicence', 'demandeChecklists']);
        
        return view('admin.checklists.show', compact('checklist'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Checklist $checklist)
    {
        $typeDemandes = TypeDemande::all();
        $typeLicences = TypeLicence::all();
        
        return view('admin.checklists.edit', compact('checklist', 'typeDemandes', 'typeLicences'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Checklist $checklist)
    {
        $validator = Validator::make($request->all(), [
            'numero' => 'required|string|max:255',
            'index' => 'nullable|string',
            'type' => 'nullable|string|max:100',
            'libelle' => 'required|string',
            'section' => 'nullable|string|max:255',
            'ordre' => 'nullable|integer',
            'type_licence_id' => 'nullable|exists:type_licences,id',
            'type_demande_id' => 'nullable|exists:type_demandes,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $checklist->update($request->all());
        
        return redirect()->route('checklists.index')
            ->with('success', 'Checklist updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Checklist $checklist)
    {
        // Check if there are related demandeChecklists
        if ($checklist->demandeChecklists()->count() > 0) {
            return redirect()->route('checklists.index')
                ->with('error', 'Cannot delete checklist because it has associated demandes.');
        }
        
        $checklist->delete();
        
        return redirect()->route('checklists.index')
            ->with('success', 'Checklist deleted successfully.');
    }
    
    /**
     * Get checklists by type demande
     */
    public function getByTypeDemande($typeDemandeId)
    {
        $checklists = Checklist::where('type_demande_id', $typeDemandeId)
            ->orderBy('section')
            ->orderBy('ordre')
            ->get();
            
        return response()->json($checklists);
    }
    
    /**
     * Get checklists by type licence
     */
    public function getByTypeLicence($typeLicenceId)
    {
        $checklists = Checklist::where('type_licence_id', $typeLicenceId)
            ->orderBy('section')
            ->orderBy('ordre')
            ->get();
            
        return response()->json($checklists);
    }
}