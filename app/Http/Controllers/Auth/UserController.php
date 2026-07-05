<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    //


    public function updatePassword(Request $request, User $user)
    {
        // Verify the authenticated user can update this password
        if (auth()->user()->id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        return response()->json(['success' => 'Password updated successfully.']);
    }
}
