<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImmatriculationBlacklist;
use Illuminate\Http\Request;

class ImmatriculationBlacklistController extends Controller
{
    public function index()
    {
        $entries = ImmatriculationBlacklist::with('creePar')->latest()->get();

        return view('admin.immatriculation-blacklists.index', compact('entries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pattern' => 'required|string|max:20|regex:/^[A-Za-z0-9-]+$/',
            'motif' => 'nullable|string|max:255',
        ]);

        ImmatriculationBlacklist::create([
            'pattern' => strtoupper($validated['pattern']),
            'motif' => $validated['motif'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.immatriculation-blacklists.index')
            ->with('success', "Le motif de blocage a été ajouté.");
    }

    public function destroy(ImmatriculationBlacklist $immatriculationBlacklist)
    {
        $immatriculationBlacklist->delete();

        return redirect()->route('admin.immatriculation-blacklists.index')
            ->with('success', 'Le motif de blocage a été supprimé.');
    }
}
