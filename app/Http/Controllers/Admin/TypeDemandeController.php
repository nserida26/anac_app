<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeDemande;
use Illuminate\Http\Request;


/**
 * Class TypeDemandeController
 * @package App\Http\Controllers
 */
class TypeDemandeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $typeDemandes = TypeDemande::paginate();

        return view('admin.type-demandes.index', compact('typeDemandes'))
            ->with('i', (request()->input('page', 1) - 1) * $typeDemandes->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $typeDemande = new TypeDemande();
        return view('admin.type-demandes.create', compact('typeDemande'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(TypeDemande::$rules);

        $typeDemande = TypeDemande::create($request->all());

        return redirect()->route('type-demandes.index')
            ->with('success', 'TypeDemande created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $typeDemande = TypeDemande::find($id);

        return view('admin.type-demandes.show', compact('typeDemande'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $typeDemande = TypeDemande::find($id);

        return view('admin.type-demandes.edit', compact('typeDemande'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  TypeDemande $typeDemande
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeDemande $typeDemande)
    {
        request()->validate(TypeDemande::$rules);

        $typeDemande->update($request->all());

        return redirect()->route('type-demandes.index')
            ->with('success', 'TypeDemande updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $typeDemande = TypeDemande::find($id)->delete();

        return redirect()->route('type-demandes.index')
            ->with('success', 'TypeDemande deleted successfully');
    }
}
