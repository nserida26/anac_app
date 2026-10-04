<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CentreMedical;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

/**
 * Rattachement par l'ANAC d'un compte (rôle centre_medical) à un centre de la
 * liste centre_medicals : c'est ce qui ouvre le compte « centre d'expertise
 * médicale ». Le compte lui-même se crée dans Utilisateurs, avec ce rôle.
 */
class CentreExpertiseMedicaleController extends Controller
{
    /** Rôle des comptes « centre d'expertise médicale » (créé par la migration 2026_09_27_100300). */
    private const ROLE = 'centre_medical';

    public function index()
    {
        $centres = CentreMedical::with('user')->whereNotNull('user_id')
            ->withCount([
                'medecins',
                'examinateurs as examinateurs_declares_count' => fn ($q) => $q->declares(),
            ])
            ->orderBy('libelle')->get();

        $centresDisponibles = CentreMedical::whereNull('user_id')->orderBy('libelle')->get();

        // User::role() lève une exception si le rôle n'existe pas encore (migration non lancée) :
        // on passe par la relation roles, qui renvoie simplement une liste vide.
        $roleExiste = Role::where('name', self::ROLE)->where('guard_name', 'web')->exists();
        $comptesDisponibles = User::whereHas('roles', fn ($q) => $q->where('name', self::ROLE))
            ->whereDoesntHave('centreMedical')->orderBy('email')->get();

        return view('admin.centres-expertise-medicale.index', compact('centres', 'centresDisponibles', 'comptesDisponibles', 'roleExiste'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'centre_medical_id' => ['required', Rule::exists('centre_medicals', 'id')->whereNull('user_id')],
            'user_id' => ['required', 'exists:users,id', Rule::unique('centre_medicals', 'user_id')],
        ]);

        $compte = User::findOrFail($request->user_id);
        if (!$compte->hasRole(self::ROLE)) {
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
