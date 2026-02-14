<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\GuestScan;
use App\Models\Scan;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to Google's OAuth page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable) {
            return redirect()->route('login')->with('status', 'Unable to authenticate with Google. Please try again.');
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update(['google_id' => $googleUser->getId()]);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                ]);

                $user->markEmailAsVerified();

                event(new Registered($user));
            }
        }

        Auth::login($user, remember: true);

        $this->attachGuestScans($request, $user);

        return redirect()->route('dashboard');
    }

    /**
     * Attach any guest scans to the user.
     */
    private function attachGuestScans(Request $request, User $user): void
    {
        $scanIds = collect();

        if ($guestScanId = $request->session()->pull('guest_scan_id')) {
            $scanIds->push($guestScanId);
        }

        $emailScanIds = GuestScan::where('email', $user->email)
            ->pluck('scan_id');
        $scanIds = $scanIds->merge($emailScanIds)->unique();

        if ($scanIds->isNotEmpty()) {
            Scan::whereIn('id', $scanIds)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);
        }
    }
}
