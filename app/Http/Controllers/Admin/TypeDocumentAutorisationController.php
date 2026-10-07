<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeDocumentAutorisation;
use App\Models\TypeDemandeAutorisation;
use App\Models\TypeVol;
use Illuminate\Http\Request;

/**
 * Class TypeDocumentAutorisationController
 * Catalogue des types de documents exigés sur une demande d'autorisation,
 * selon le type de vol et le type de demande (voir TypeDocumentAutorisation).
 *
 * @package App\Http\Controllers\Admin
 */
class TypeDocumentAutorisationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Catalogue de référence, pagination gérée côté client par DataTables.
        $typeDocumentAutorisations = TypeDocumentAutorisation::with('typeVol', 'typeDemande')->get();

        return view('admin.type-document-autorisations.index', compact('typeDocumentAutorisations'))
            ->with('i', 0);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $typeDocumentAutorisation = new TypeDocumentAutorisation();
        $typeVols = TypeVol::all();
        $typeDemandes = TypeDemandeAutorisation::all();

        return view('admin.type-document-autorisations.create', compact('typeDocumentAutorisation', 'typeVols', 'typeDemandes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(TypeDocumentAutorisation::$rules);

        TypeDocumentAutorisation::create($request->all());

        return redirect()->route('type-document-autorisations.index')
            ->with('success', 'Type de document créé avec succès.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $typeDocumentAutorisation = TypeDocumentAutorisation::with('typeVol', 'typeDemande')->findOrFail($id);

        return view('admin.type-document-autorisations.show', compact('typeDocumentAutorisation'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $typeDocumentAutorisation = TypeDocumentAutorisation::findOrFail($id);
        $typeVols = TypeVol::all();
        $typeDemandes = TypeDemandeAutorisation::all();

        return view('admin.type-document-autorisations.edit', compact('typeDocumentAutorisation', 'typeVols', 'typeDemandes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  TypeDocumentAutorisation $typeDocumentAutorisation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeDocumentAutorisation $typeDocumentAutorisation)
    {
        $request->validate(TypeDocumentAutorisation::$rules);

        $typeDocumentAutorisation->update($request->all());

        return redirect()->route('type-document-autorisations.index')
            ->with('success', 'Type de document mis à jour avec succès.');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        TypeDocumentAutorisation::findOrFail($id)->delete();

        return redirect()->route('type-document-autorisations.index')
            ->with('success', 'Type de document supprimé avec succès.');
    }
}
