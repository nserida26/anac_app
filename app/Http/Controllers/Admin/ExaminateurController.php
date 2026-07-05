<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CentreMedical;
use App\Models\Examinateur;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Class ExaminateurController
 * @package App\Http\Controllers
 */
class ExaminateurController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $examinateurs = Examinateur::paginate();
        return view('admin.examinateurs.index', compact('examinateurs'))
            ->with('i', (request()->input('page', 1) - 1) * $examinateurs->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $examinateur = new Examinateur();
        $centres =  CentreMedical::pluck('libelle', 'id');
        $users = User::role('examinateur')->pluck('email', 'id');
        return view('admin.examinateurs.create', compact('examinateur', 'users', 'centres'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Examinateur::$rules);

        $examinateur = Examinateur::create($request->all());

        return redirect()->route('examinateurs.index')
            ->with('success', 'Examinateur created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $examinateur = Examinateur::find($id);

        return view('admin.examinateurs.show', compact('examinateur'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $examinateur = Examinateur::find($id);
        $users = User::role('examinateur')->pluck('email', 'id');
        $centres =  CentreMedical::pluck('libelle', 'id');
        return view('admin.examinateurs.edit', compact('examinateur', 'users', 'centres'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Examinateur $examinateur
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Examinateur $examinateur)
    {
        request()->validate(Examinateur::$rules);

        $examinateur->update($request->all());

        return redirect()->route('examinateurs.index')
            ->with('success', 'Examinateur updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $examinateur = Examinateur::find($id)->delete();

        return redirect()->route('examinateurs.index')
            ->with('success', 'Examinateur deleted successfully');
    }
}
