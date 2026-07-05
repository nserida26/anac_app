<?php

namespace App\Http\Controllers\Admin;

use App\Models\Compagny;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

/**
 * Class CompagnyController
 * @package App\Http\Controllers
 */
class CompagnyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $compagnies = Compagny::paginate();

        return view('admin.compagnies.index', compact('compagnies'))
            ->with('i', (request()->input('page', 1) - 1) * $compagnies->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $compagny = new Compagny();
        $users = User::role('compagnie')->pluck('email', 'id');
        return view('admin.compagnies.create', compact('compagny', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Compagny::$rules);

        $compagny = Compagny::create($request->all());

        return redirect()->route('compagnies.index')
            ->with('success', 'Compagny created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $compagny = Compagny::find($id);

        return view('admin.compagnies.show', compact('compagny'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $users = User::role('compagnie')->pluck('email', 'id');
        $compagny = Compagny::find($id);

        return view('admin.compagnies.edit', compact('compagny', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Compagny $compagny
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Compagny $compagny)
    {
        request()->validate(Compagny::$rules);

        $compagny->update($request->all());

        return redirect()->route('compagnies.index')
            ->with('success', 'Compagny updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $compagny = Compagny::find($id)->delete();

        return redirect()->route('compagnies.index')
            ->with('success', 'Compagny deleted successfully');
    }
}
