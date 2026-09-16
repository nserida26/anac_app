<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * After registration, redirect to login with a success message.
     */
    public function redirectPath(): string
    {
        return route('login');
    }

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'whatsapp'    => ['required', 'string', 'regex:/^[0-9]{8}$/'],
            'country_code' => ['required', 'string', 'max:5'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
            'user_type'   => ['required', 'in:autorisation,licence'],
        ], [
            'whatsapp.regex' => __('register.whatsapp_digits'),
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data): User
    {
        $user = User::create([
            'email'     => $data['email'],
            'whatsapp'  => '+' . $data['country_code'] . $data['whatsapp'],
            'photo'     => 'default.png',
            'password'  => Hash::make($data['password']),
            'user_type' => $data['user_type'],
        ]);

        $user->assignRole('user');

        return $user;
    }

    /**
     * After registration: send WhatsApp verification link, logout, redirect to login.
     */
    protected function registered(Request $request, $user)
    {
        $this->sendWhatsappVerificationLink($user);

        Auth::logout();

        return redirect()->route('login')
            ->with('success', __('register.verify_email_message'));
    }

    /**
     * Send the signed verification URL via WhatsApp.
     * Failure is logged but does not interrupt registration.
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
            Log::error("WhatsApp verification link failed (user {$user->id}): " . $e->getMessage());
        }
    }
}
