<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Demandeur;
use App\Models\Compagnie;
use App\Models\CompagnieLoginRequest;
use App\Notifications\LoginRequestApprovedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemandeurController extends Controller
{
    //
    public function index()
    {
        $demandeur = Auth::user()->demandeur;
        $compagnies = Compagnie::all();

        return view('user.profile', compact('compagnies', 'demandeur'));
    }
    public function store(Request $request)
    {

        $request->validate([
            'np' => 'required|string|max:255',
            //'date_naissance' => 'required|date',
            //'lieu_naissance' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'nationalite' => 'required|string|max:255',
            'compagnie_id' => 'nullable|string|max:255',
            //'signature' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            //'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);



        // Handle file upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        } else {
            $photoPath = null;
        }
        // Create Demandeur

        Demandeur::create(array_merge($request->all(), ['photo' => $photoPath], ['user_id' => auth()->id()]));

        return redirect()->route('user.profile')->with('success', 'Demandeur created successfully.');
    }
    public function update(Request $request)
    {
        $request->validate([
            'np' => 'required|string|max:255',
            //'date_naissance' => 'required|date',
            //'lieu_naissance' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'nationalite' => 'required|string|max:255',
            'compagnie_id' => 'nullable|exists:compagnies,id',
            //'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // Trouver le demandeur associé à l'utilisateur connecté
        $demandeur = Demandeur::where('user_id', auth()->id())->firstOrFail();

        // Gérer la photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($demandeur->photo && Storage::disk('public')->exists($demandeur->photo)) {
                Storage::disk('public')->delete($demandeur->photo);
            }

            // Stocker la nouvelle photo
            $photoPath = $request->file('photo')->store('photos', 'public');
            if ($demandeur->licence && !empty($photoPath)) {
                $demandeur->licence->update([
                    'photo' => $photoPath,
                ]);
            }

        } else {
            // Garder l'ancienne photo si aucune nouvelle n'est fournie
            $photoPath = $demandeur->photo;
        }

        // Mettre à jour les données
        $demandeur->update([
            'np' => $request->np,
            'date_naissance' => $request->date_naissance,
            'lieu_naissance' => $request->lieu_naissance,
            'adresse' => $request->adresse,
            'nationalite' => $request->nationalite,
            'compagnie_id' => $request->compagnie_id,
            'photo' => $photoPath,
        ]);

        return redirect()->route('user.profile')->with('success', 'Profil mis à jour avec succès.');
    }
    public function loginRequests()
    {
        $requests = CompagnieLoginRequest::with('compagnieUser.compagnie')
            ->where('target_user_id', Auth::id())
            ->where('expires_at', '>', now())
            ->where('accepted', false)
            ->get();

        return view('user.licences.login-requests', compact('requests'));
    }

    public function approveLogin(CompagnieLoginRequest $request)
    {
        if ($request->target_user_id !== Auth::id()) {
            abort(403);
        }

        if ($request->expires_at < now()) {
            return back()->with('error', 'This request has expired');
        }

        $request->update(['accepted' => true]);

        // Generate a one-time login link
        $loginToken = Str::random(60);
        Cache::put('compagnie_login_token_' . $loginToken, [
            'compagnie_user_id' => $request->compagnie_user_id,
            'target_user_id' => $request->target_user_id
        ], now()->addMinutes(15));

        // Notify compagnie user
        $request->compagnieUser->notify(new LoginRequestApprovedNotification(
            route('compagnie.finalize.login', $loginToken)
        ));

        return back()->with('success', 'Login request approved');
    }
}
