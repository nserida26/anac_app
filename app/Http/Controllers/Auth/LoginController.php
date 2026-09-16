<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Role-to-dashboard redirect map.
     * Adding a new role = adding one line here (OCP).
     */
    protected array $roleRedirects = [
        'admin'       => '/admin',
        'user'        => '/user',
        'dg'          => '/dir/dg',
        'dsv'         => '/dir/dsv',
        'dta'         => '/dir/dta',
        'dsad'        => '/dir/dsad',
        'dsna'        => '/dir/dsna',
        'sma'         => '/sec/sma',
        'sla'         => '/sec/sla',
        'examinateur' => '/examinateur',
        'evaluateur'  => '/evaluateur',
        'daf'         => '/daf',
        'agent'       => '/agent',
        'centre'      => '/centre',
        'compagnie'   => '/compagnie',
    ];

    /**
     * Where to redirect users after login.
     */
    protected function redirectTo(): string
    {
        $user = Auth::user();

        foreach ($this->roleRedirects as $role => $path) {
            if ($user->hasRole($role)) {
                return $path;
            }
        }

        return '/user';
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
