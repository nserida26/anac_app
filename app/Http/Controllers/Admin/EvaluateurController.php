<?php

namespace App\Http\Controllers\Admin;

use App\Models\Evaluateur;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

/**
 * Class EvaluateurController
 * @package App\Http\Controllers
 */
class EvaluateurController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $evaluateurs = Evaluateur::paginate();

        return view('admin.evaluateurs.index', compact('evaluateurs'))
            ->with('i', (request()->input('page', 1) - 1) * $evaluateurs->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::role('evaluateur')->pluck('email', 'id');
        $evaluateur = new Evaluateur();
        return view('admin.evaluateurs.create', compact('evaluateur', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Evaluateur::$rules);

        $evaluateur = Evaluateur::create($request->all());

        return redirect()->route('evaluateurs.index')
            ->with('success', 'Evaluateur created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $evaluateur = Evaluateur::find($id);

        return view('admin.evaluateurs.show', compact('evaluateur'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $evaluateur = Evaluateur::find($id);
        $users = User::role('evaluateur')->pluck('email', 'id');
        return view('admin.evaluateurs.edit', compact('evaluateur', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Evaluateur $evaluateur
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Evaluateur $evaluateur)
    {
        request()->validate(Evaluateur::$rules);

        $evaluateur->update($request->all());

        return redirect()->route('evaluateurs.index')
            ->with('success', 'Evaluateur updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $evaluateur = Evaluateur::find($id)->delete();

        return redirect()->route('evaluateurs.index')
            ->with('success', 'Evaluateur deleted successfully');
    }
}
