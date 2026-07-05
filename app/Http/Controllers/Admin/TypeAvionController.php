<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeAvion;
use Illuminate\Http\Request;


/**
 * Class TypeAvionController
 * @package App\Http\Controllers
 */
class TypeAvionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $typeAvions = TypeAvion::paginate();

        return view('admin.type-avions.index', compact('typeAvions'))
            ->with('i', (request()->input('page', 1) - 1) * $typeAvions->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $typeAvion = new TypeAvion();
        return view('admin.type-avions.create', compact('typeAvion'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(TypeAvion::$rules);

        $typeAvion = TypeAvion::create($request->all());

        return redirect()->route('type-avions.index')
            ->with('success', 'TypeAvion created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $typeAvion = TypeAvion::find($id);

        return view('admin.type-avions.show', compact('typeAvion'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $typeAvion = TypeAvion::find($id);

        return view('admin.type-avions.edit', compact('typeAvion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  TypeAvion $typeAvion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeAvion $typeAvion)
    {
        request()->validate(TypeAvion::$rules);

        $typeAvion->update($request->all());

        return redirect()->route('type-avions.index')
            ->with('success', 'TypeAvion updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $typeAvion = TypeAvion::find($id)->delete();

        return redirect()->route('type-avions.index')
            ->with('success', 'TypeAvion deleted successfully');
    }
}
