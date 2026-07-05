<?php

namespace App\Http\Controllers;

use App\Models\Qualification;
use Illuminate\Http\Request;

/**
 * Class QualificationController
 * @package App\Http\Controllers
 */
class QualificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $qualifications = Qualification::paginate();

        return view('qualification.index', compact('qualifications'))
            ->with('i', (request()->input('page', 1) - 1) * $qualifications->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $qualification = new Qualification();
        return view('qualification.create', compact('qualification'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Qualification::$rules);

        $qualification = Qualification::create($request->all());

        return redirect()->route('qualifications.index')
            ->with('success', 'Qualification created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $qualification = Qualification::find($id);

        return view('qualification.show', compact('qualification'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $qualification = Qualification::find($id);

        return view('qualification.edit', compact('qualification'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Qualification $qualification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Qualification $qualification)
    {
        request()->validate(Qualification::$rules);

        $qualification->update($request->all());

        return redirect()->route('qualifications.index')
            ->with('success', 'Qualification updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $qualification = Qualification::find($id)->delete();

        return redirect()->route('qualifications.index')
            ->with('success', 'Qualification deleted successfully');
    }
}
