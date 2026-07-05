<?php

namespace App\Http\Controllers;

use App\Models\ExamenMedical;
use App\Models\Demandeur;
use App\Models\Examinateur;
use App\Models\Licence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExaminateurController extends Controller
{
    // Liste des examens
    public function index()
    {
        $user = Auth::user();
        $examinateur = $user->examinateur;
        if(!empty($examinateur)){
            $demandeurs = Demandeur::distinct()->get();
            $examens = ExamenMedical::with(['demandeur', 'examinateur'])->get();
            return view('examinateur.index', compact('examens', 'demandeurs'));
        } else {
            return redirect()->back()->with('error', 'Aucun examinateur n’est associé à votre compte.');
        }
    }

    // Formulaire de recherche avancée
    public function searchForm()
    {
        $user = Auth::user();
        $examinateur = $user->examinateur;
        
        if(!empty($examinateur)){
            return view('examinateur.search-advanced', compact('examinateur'));
        }
        
        return redirect()->back()->with('error', 'Aucun examinateur n’est associé à votre compte.');
    }

    // API de recherche pour autocomplete
    public function searchAutocomplete(Request $request)
    {
        $search = $request->get('q', '');
        $type = $request->get('type', 'all'); // all, licence, name
        
        if (strlen($search) < 2) {
            return response()->json([]);
        }
        
        $results = [];
        
        // Recherche par numéro de licence
        if ($type == 'all' || $type == 'licence') {
            $licences = Licence::where('numero_licence', 'LIKE', "%{$search}%")
                ->with('demandeur')
                ->limit(10)
                ->get();
            
            foreach ($licences as $licence) {
                if ($licence->demandeur) {
                    $results[] = [
                        'type' => 'licence',
                        'id' => $licence->demandeur->id,
                        'licence_id' => $licence->id,
                        'licence_number' => $licence->numero_licence,
                        'licence_type' => $licence->type_licence,
                        'np' => $licence->demandeur->np,
                        'photo' => $licence->demandeur->photo,
                        'date_naissance' => $licence->demandeur->date_naissance,
                        'label' => "🔑 Licence: {$licence->numero_licence} - {$licence->demandeur->np}",
                        'relevance' => $this->calculateRelevance($licence->numero_licence, $search)
                    ];
                }
            }
        }
        
        // Recherche par nom et prénom
        if ($type == 'all' || $type == 'name') {
            $demandeurs = Demandeur::where('np', 'LIKE', "%{$search}%")
                ->with('licence')
                ->limit(10)
                ->get();
            
            foreach ($demandeurs as $demandeur) {
                $licenceNumber = $demandeur->licence ? $demandeur->licence->numero_licence : 'Aucune licence';
                $results[] = [
                    'type' => 'name',
                    'id' => $demandeur->id,
                    'licence_id' => $demandeur->licence ? $demandeur->licence->id : null,
                    'licence_number' => $licenceNumber,
                    'licence_type' => $demandeur->licence ? $demandeur->licence->type_licence : 'N/A',
                    'np' => $demandeur->np,
                    'photo' => $demandeur->photo,
                    'date_naissance' => $demandeur->date_naissance,
                    'adresse' => $demandeur->adresse,
                    'label' => "👤 {$demandeur->np} - Licence: {$licenceNumber}",
                    'relevance' => $this->calculateRelevance($demandeur->np, $search)
                ];
            }
        }
        
        // Trier par pertinence
        usort($results, function($a, $b) {
            return $b['relevance'] <=> $a['relevance'];
        });
        
        return response()->json($results);
    }
    
    private function calculateRelevance($text, $search)
    {
        $text = strtolower($text);
        $search = strtolower($search);
        
        if ($text === $search) return 100;
        if (str_starts_with($text, $search)) return 90;
        if (str_contains($text, $search)) return 70;
        return 50;
    }
    
    // Recherche avancée avec filtres
    public function advancedSearch(Request $request)
    {
        $query = Demandeur::query();
        
        // Filtre par licence
        if ($request->filled('licence_number')) {
            $query->whereHas('licence', function($q) use ($request) {
                $q->where('numero_licence', 'LIKE', "%{$request->licence_number}%");
            });
        }
        
        // Filtre par nom
        if ($request->filled('name')) {
            $query->where('np', 'LIKE', "%{$request->name}%");
        }
        
        // Filtre par type de licence
        if ($request->filled('licence_type') && $request->licence_type != 'all') {
            $query->whereHas('licence', function($q) use ($request) {
                $q->where('type_licence', $request->licence_type);
            });
        }
        
        $demandeurs = $query->with('licence')->paginate(20);
        $licenceTypes = Licence::select('type_licence')->distinct()->whereNotNull('type_licence')->pluck('type_licence');
        
        if ($request->ajax()) {
            return response()->json([
                'demandeurs' => $demandeurs,
                'licenceTypes' => $licenceTypes
            ]);
        }
        
        return view('examinateur.search-results', compact('demandeurs', 'licenceTypes'));
    }

    // Formulaire de création
    public function create(Request $request, Demandeur $demandeur)
    {
        $user = Auth::user();
        $examinateur = $user->examinateur;
        
        if(!empty($examinateur)){
            $licenceNumber = $request->get('licence', '');
            return view('examinateur.create', compact('demandeur', 'examinateur', 'licenceNumber'));
        }
        
        return redirect()->route('examinateur')->with('error', 'Accès non autorisé');
    }

    // Stocker un nouvel examen
    public function store(Request $request)
    {
        $request->validate([
            'demandeur_id' => 'required|exists:demandeurs,id',
            'examinateur_id' => 'required|exists:examinateurs,id',
            'date_examen' => 'required|date',
            'validite' => 'required|integer',
            'aptitude' => 'required|in:Apte,Inapte',
            'rapport' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'attestation' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $attestationPath = $request->file('attestation')->store('attestations', 'public');
        $rapportPath = $request->file('rapport')->store('rapports', 'public');

        ExamenMedical::create([
            'demandeur_id' => $request->demandeur_id,
            'examinateur_id' => $request->examinateur_id,
            'date_examen' => $request->date_examen,
            'validite' => $request->validite,
            'aptitude' => $request->aptitude,
            'rapport' => $rapportPath,
            'attestation' => $attestationPath,
        ]);

        return redirect()->route('examinateur')->with('success', 'Examen médical ajouté avec succès.');
    }

    // Afficher un examen
    public function show(ExamenMedical $examen)
    {
        return view('examinateur.show', compact('examen'));
    }

    // Formulaire d'édition
    public function edit(ExamenMedical $examen)
    {
        return view('examinateur.edit', compact('examen'));
    }

    // Mettre à jour un examen
    public function update(Request $request, ExamenMedical $examen)
    {
        $request->validate([
            'demandeur_id' => 'required|exists:demandeurs,id',
            'examinateur_id' => 'required|exists:examinateurs,id',
            'date_examen' => 'required|date',
            'validite' => 'required|integer',
            'aptitude' => 'required|in:Apte,Inapte',
            'rapport' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'attestation' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        if ($request->hasFile('attestation')) {
            $attestationPath = $request->file('attestation')->store('attestations', 'public');
            $examen->attestation = $attestationPath;
        }
        if ($request->hasFile('rapport')) {
            $rapportPath = $request->file('rapport')->store('rapports', 'public');
            $examen->rapport = $rapportPath;
        }

        $examen->update([
            'demandeur_id' => $request->demandeur_id,
            'examinateur_id' => $request->examinateur_id,
            'date_examen' => $request->date_examen,
            'validite' => $request->validite,
            'aptitude' => $request->aptitude
        ]);

        return redirect()->route('examinateur')->with('success', 'Examen médical mis à jour.');
    }

    // Supprimer un examen
    public function destroy(ExamenMedical $examen)
    {
        $examen->delete();
        return redirect()->route('examinateur')->with('success', 'Examen médical supprimé.');
    }
    
    public function valider(ExamenMedical $examen)
    {
        $examen->update([
            'valider_examinateur' => true
        ]);
        return redirect()->route('examinateur')->with('success', 'Examen médical validé.');
    }
}