<?php

namespace App\Http\Controllers\Admin;

use App\Models\Autorite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class AutoriteController
 * @package App\Http\Controllers
 */
class AutoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $autorites = Autorite::paginate();

        return view('admin.autorites.index', compact('autorites'))
            ->with('i', (request()->input('page', 1) - 1) * $autorites->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $autorite = new Autorite();
        return view('admin.autorites.create', compact('autorite'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Autorite::$rules);

        $autorite = Autorite::create($request->all());

        return redirect()->route('autorites.index')
            ->with('success', 'Autorite created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $autorite = Autorite::find($id);

        return view('admin.autorites.show', compact('autorite'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $autorite = Autorite::find($id);

        return view('admin.autorites.edit', compact('autorite'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Autorite $autorite
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Autorite $autorite)
    {
        request()->validate(Autorite::$rules);

        $autorite->update($request->all());

        return redirect()->route('autorites.index')
            ->with('success', 'Autorite updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $autorite = Autorite::find($id)->delete();

        return redirect()->route('autorites.index')
            ->with('success', 'Autorite deleted successfully');
    }
}
