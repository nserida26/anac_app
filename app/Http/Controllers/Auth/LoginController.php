<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function redirectTo()
    {

        if (Auth::user()->hasRole('admin')) {
            return '/admin';
        } elseif (Auth::user()->hasRole('user')) {
            return '/user';
        } else if (Auth::user()->hasRole('dg')) {
            return '/dir/dg';
        } else if (Auth::user()->hasRole('dsv')) {
            return '/dir/dsv';
        } else if (Auth::user()->hasRole('dta')) {
            return '/dir/dta';
        } else if (Auth::user()->hasRole('dsad')) {
            return '/dir/dsad';
        } else if (Auth::user()->hasRole('dsna')) {
            return '/dir/dsna';
        } else if (Auth::user()->hasRole('sma')) {
            return '/sec/sma';
        } else if (Auth::user()->hasRole('sla')) {
            return '/sec/sla';
        } else if (Auth::user()->hasRole('examinateur')) {
            return '/examinateur';
        } else if (Auth::user()->hasRole('evaluateur')) {
            return '/evaluateur';
        } else if (Auth::user()->hasRole('daf')) {
            return '/daf';
        } else if (Auth::user()->hasRole('agent')) {
            return '/agent';
        } else if (Auth::user()->hasRole('centre')) {
            return '/centre';
        } else if (Auth::user()->hasRole('compagnie')) {
            return '/compagnie';
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
