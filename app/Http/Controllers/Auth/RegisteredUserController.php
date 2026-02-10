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
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $this->attachGuestScans($request, $user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Attach any guest scans to the newly registered user.
     */
    private function attachGuestScans(Request $request, User $user): void
    {
        $scanIds = collect();

        // Attach scan from current session (most reliable)
        if ($guestScanId = $request->session()->pull('guest_scan_id')) {
            $scanIds->push($guestScanId);
        }

        // Attach any scans linked by email via GuestScan records
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
