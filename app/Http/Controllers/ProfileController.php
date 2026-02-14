<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
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

    /**
     * Update the user's email preferences.
     */
    public function updateEmailPreferences(Request $request): RedirectResponse
    {
        $request->user()->update([
            'marketing_emails_enabled' => $request->boolean('marketing_emails_enabled'),
            'system_emails_enabled' => $request->boolean('system_emails_enabled'),
        ]);

        return Redirect::route('profile.edit')->with('status', 'email-preferences-updated');
    }

    /**
     * Create a new API token for the user.
     */
    public function createApiKey(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isPaid()) {
            return Redirect::route('profile.edit')
                ->with('error', 'API access requires a paid plan.');
        }

        $token = $user->createToken('API Access');

        return Redirect::route('profile.edit')
            ->with('status', 'api-key-created')
            ->with('api_token', $token->plainTextToken);
    }

    /**
     * Revoke all API tokens for the user.
     */
    public function revokeApiKey(Request $request): RedirectResponse
    {
        $request->user()->tokens()->delete();

        return Redirect::route('profile.edit')
            ->with('status', 'api-key-revoked');
    }
}
