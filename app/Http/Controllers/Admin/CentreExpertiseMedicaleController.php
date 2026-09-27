<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CentreMedical;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Rattachement par l'ANAC d'un compte (rôle centre_medical) à un centre de la
 * liste centre_medicals : c'est ce qui ouvre le compte « centre d'expertise
 * médicale ». Le compte lui-même se crée dans Utilisateurs, avec ce rôle.
 */
class CentreExpertiseMedicaleController extends Controller
{
    public function index()
    {
        $centres = CentreMedical::with('user')->whereNotNull('user_id')
            ->withCount([
                'medecins',
                'examinateurs as examinateurs_declares_count' => fn ($q) => $q->declares(),
            ])
            ->orderBy('libelle')->get();

        $centresDisponibles = CentreMedical::whereNull('user_id')->orderBy('libelle')->get();
        $comptesDisponibles = User::role('centre_medical')->whereDoesntHave('centreMedical')->orderBy('email')->get();

        return view('admin.centres-expertise-medicale.index', compact('centres', 'centresDisponibles', 'comptesDisponibles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'centre_medical_id' => ['required', Rule::exists('centre_medicals', 'id')->whereNull('user_id')],
            'user_id' => ['required', 'exists:users,id', Rule::unique('centre_medicals', 'user_id')],
        ]);

        $compte = User::findOrFail($request->user_id);
        if (!$compte->hasRole('centre_medical')) {
            return back()->with('error', __('trans.compte_sans_role_centre_medical'));
        }

        CentreMedical::whereKey($request->centre_medical_id)->update(['user_id' => $compte->id]);

        return back()->with('success', __('trans.centre_medical_rattache'));
    }

    /** Retire le compte du centre (le centre et ses déclarations sont conservés). */
    public function destroy(CentreMedical $centreMedical)
    {
        $centreMedical->update(['user_id' => null]);

        return back()->with('success', __('trans.centre_medical_detache'));
    }
}
