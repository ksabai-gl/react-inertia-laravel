<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    /**
     * Destroy all sessions except the current one.
     */
    public function destroyOtherSessions(Request $request): RedirectResponse
    {
        if (config('session.driver') !== 'database') {
            return back(409);
        }

        $request->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', '!=', request()->session()->getId())
            ->delete();

        return back(303)->with('status', 'other-browser-sessions-terminated');
    }

    /**
     * Destroy a specific session.
     */
    public function destroySession(Request $request, string $session): RedirectResponse
    {
        if (config('session.driver') !== 'database') {
            return back(409);
        }

        $request->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        // Don't allow destroying the current session
        if ($session === $request->session()->getId()) {
            throw ValidationException::withMessages([
                'session' => ['You cannot terminate your current session.'],
            ]);
        }

        // Verify the session belongs to the current user
        $existing = DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', $session)
            ->first();

        if (! $existing) {
            throw ValidationException::withMessages([
                'session' => ['Session not found or does not belong to you'],
            ]);
        }

        DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', $session)
            ->delete();

        return back(303)->with('status', 'browser-session-terminated');
    }
}
