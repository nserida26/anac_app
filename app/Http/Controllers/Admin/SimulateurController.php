<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Simulateur;
use App\Models\TypeAvion;
use Illuminate\Http\Request;
use App\Models\CentreFormation;

class SimulateurController extends Controller
{
    public function index()
    {
        $simulateurs = Simulateur::with('typeAvion')->orderBy('libelle')->get();
        return view('admin.simulateurs.index', compact('simulateurs'));
    }

    public function create()
    {
        $simulateur = new Simulateur();
        $centres = CentreFormation::pluck('libelle', 'id');
        $typeAvions = TypeAvion::orderBy('code')->get();
        $compagnies = ['Global', 'MAI', 'CLASS AVIATION'];
        return view('admin.simulateurs.create', compact('simulateur', 'centres', 'typeAvions', 'compagnies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'type_avion_id' => 'required|exists:type_avions,id',
            'date_delivrance_initiale' => 'nullable|date',
            'date_renouvellement' => 'nullable|date',
            'date_expiration' => 'nullable|date',
            'compagnie' => 'required|in:Global,MAI,CLASS AVIATION',
        ]);

        $simulateur = Simulateur::create($validated);
        $simulateur->centres()->sync($request->centre_formation_id);

        return redirect()->route('simulateurs.index')->with('success', 'Simulateur créé avec succès.');
    }

    public function show(Simulateur $simulateur)
    {
        return view('admin.simulateurs.show', compact('simulateur'));
    }

    public function edit(Simulateur $simulateur)
    {
        $typeAvions = TypeAvion::orderBy('code')->get();
        $compagnies = ['Global', 'MAI', 'CLASS AVIATION'];
        $centres = CentreFormation::pluck('libelle', 'id');
        return view('admin.simulateurs.edit', compact('centres', 'simulateur', 'typeAvions', 'compagnies'));
    }

    public function update(Request $request, Simulateur $simulateur)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'type_avion_id' => 'required|exists:type_avions,id',
            'date_delivrance_initiale' => 'nullable|date',
            'date_renouvellement' => 'nullable|date',
            'date_expiration' => 'nullable|date',
            'compagnie' => 'required|in:Global,MAI,CLASS AVIATION',
        ]);

        $simulateur->update($validated);
        $simulateur->centres()->sync($request->centre_formation_id);

        return redirect()->route('simulateurs.index')->with('success', 'Simulateur mis à jour avec succès.');
    }

    public function destroy(Simulateur $simulateur)
    {
        $simulateur->delete();
        return redirect()->route('simulateurs.index')->with('success', 'Simulateur supprimé avec succès.');
    }
}
