<?php

namespace App\Http\Controllers\Admin;

use App\Models\CentreFormation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

/**
 * Class CentreFormationController
 * @package App\Http\Controllers
 */
class CentreFormationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $centreFormations = CentreFormation::paginate();

        return view('admin.centre-formations.index', compact('centreFormations'))
            ->with('i', (request()->input('page', 1) - 1) * $centreFormations->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::role('centre')->pluck('email', 'id');
        $centreFormation = new CentreFormation();
        return view('admin.centre-formations.create', compact('centreFormation', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(CentreFormation::$rules);

        $centreFormation = CentreFormation::create($request->all());

        return redirect()->route('centre-formations.index')
            ->with('success', 'CentreFormation created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $centreFormation = CentreFormation::find($id);

        return view('admin.centre-formations.show', compact('centreFormation'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $users = User::role('centre')->pluck('email', 'id');
        $centreFormation = CentreFormation::find($id);

        return view('admin.centre-formations.edit', compact('centreFormation', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  CentreFormation $centreFormation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CentreFormation $centreFormation)
    {
        request()->validate(CentreFormation::$rules);

        $centreFormation->update($request->all());

        return redirect()->route('centre-formations.index')
            ->with('success', 'CentreFormation updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $centreFormation = CentreFormation::find($id)->delete();

        return redirect()->route('centre-formations.index')
            ->with('success', 'CentreFormation deleted successfully');
    }
}
