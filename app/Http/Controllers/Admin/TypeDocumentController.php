<?php

namespace App\Http\Controllers\Admin;

use App\Models\TypeDocument;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TypeDemande;
use App\Models\TypeLicence;

/**
 * Class TypeDocumentController
 * @package App\Http\Controllers
 */
class TypeDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $typeDocuments = TypeDocument::with('typeDemande', 'typeLicence')->paginate();


        return view('admin.type-documents.index', compact('typeDocuments'))
            ->with('i', (request()->input('page', 1) - 1) * $typeDocuments->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $typeDocument = new TypeDocument();
        $typeDemandes = TypeDemande::all();
        $typeLicences = TypeLicence::all();
        return view('admin.type-documents.create', compact('typeDocument', 'typeDemandes', 'typeLicences'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(TypeDocument::$rules);

        $typeDocument = TypeDocument::create($request->all());

        return redirect()->route('type-documents.index')
            ->with('success', 'TypeDocument created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $typeDocument = TypeDocument::find($id);

        return view('admin.type-documents.show', compact('typeDocument'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $typeDocument = TypeDocument::find($id);
        $typeDemandes = TypeDemande::all();
        $typeLicences = TypeLicence::all();

        return view('admin.type-documents.edit', compact('typeDocument', 'typeDemandes', 'typeLicences'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  TypeDocument $typeDocument
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeDocument $typeDocument)
    {
        request()->validate(TypeDocument::$rules);

        $typeDocument->update($request->all());

        return redirect()->route('type-documents.index')
            ->with('success', 'TypeDocument updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $typeDocument = TypeDocument::find($id)->delete();

        return redirect()->route('type-documents.index')
            ->with('success', 'TypeDocument deleted successfully');
    }
}
