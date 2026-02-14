<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailPreferenceController extends Controller
{
    /**
     * Show the unsubscribe confirmation page.
     */
    public function show(Request $request, User $user): View
    {
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        return view('email.unsubscribe', [
            'user' => $user,
        ]);
    }

    /**
     * Process the unsubscribe request.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        $validated = $request->validate([
            'marketing_emails_enabled' => ['sometimes', 'boolean'],
            'system_emails_enabled' => ['sometimes', 'boolean'],
        ]);

        $user->update([
            'marketing_emails_enabled' => $validated['marketing_emails_enabled'] ?? false,
            'system_emails_enabled' => $validated['system_emails_enabled'] ?? false,
        ]);

        return back()->with('status', 'email-preferences-updated');
    }
}
