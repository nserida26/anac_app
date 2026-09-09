<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */

    protected function redirectTo()
    {
        if (Auth::user()->hasRole('user')) {
            return '/user';
        }
        return '/';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'whatsapp' =>  [
                'required',
                'string',
                'regex:/^[0-9]{7,14}$/',
            ],
            'country_code' => ['required', 'string', 'max:5'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_type' => ['required', 'in:autorisation,licence'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {

        $user = User::create([
            'email' => $data['email'],
            'whatsapp' => '+' . $data['country_code'] . $data['whatsapp'],
            'photo' => 'default.png',
            'password' => Hash::make($data['password']),
            'user_type' => $data['user_type'],
        ]);

        $user->assignRole('user');
        return $user;
    }

    /**
     * Après l'inscription : l'e-mail de vérification est déjà envoyé par
     * l'événement Registered ; on envoie en plus le lien d'activation par WhatsApp.
     */
    protected function registered(Request $request, $user)
    {
        $this->sendWhatsappVerificationLink($user);
    }

    /**
     * Envoie le lien signé de vérification d'e-mail (le même que celui du mail)
     * au numéro WhatsApp fourni à l'inscription. Un échec n'interrompt pas
     * l'inscription : il est seulement journalisé.
     */
    protected function sendWhatsappVerificationLink(User $user): void
    {
        if (empty($user->whatsapp)) {
            return;
        }

        try {
            $expireMinutes = (int) config('auth.verification.expire', 60);

            $verifyUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes($expireMinutes),
                [
                    'id'   => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );

            $message = __('register.activation_whatsapp_message', [
                'url'     => $verifyUrl,
                'minutes' => $expireMinutes,
            ]);

            app(WhatsAppService::class)->sendMessage($user->whatsapp, $message);
        } catch (\Throwable $e) {
            Log::error("Lien d'activation WhatsApp non envoyé (user {$user->id}) : " . $e->getMessage());
        }
    }
}
