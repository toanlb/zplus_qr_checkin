<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\CheckIn;

class ProfileController extends Controller
{
    /**
     * Display the user's profile information.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        
        // Obtener la membresía activa del usuario
        $activeMembership = $user->getActiveMembership();
        
        // Obtener historial de check-ins del usuario (limitar a los últimos 20)
        $checkIns = $user->checkIns()
            ->orderBy('date', 'desc')
            ->orderBy('check_in_time', 'desc')
            ->paginate(10);
        
        return view('profile.show', compact('user', 'activeMembership', 'checkIns'));
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
