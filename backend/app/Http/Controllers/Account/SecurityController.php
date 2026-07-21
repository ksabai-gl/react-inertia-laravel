<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class SecurityController extends Controller
{
    /**
     * Display the user's security settings and active sessions.
     */
    public function show(Request $request): Response
    {
        $currentSessionId = $request->session()->getId();

        $sessions = Session::query()
            ->where('user_id', $request->user()->id)
            ->select(['id', 'ip_address', 'user_agent', 'last_activity', 'user_id'])
            ->orderByDesc('last_activity')
            ->get()
            ->map(function (Session $session) use ($currentSessionId) {
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => $session->last_activity,
                    'last_active_ago' => $session->last_active_ago,
                    'is_current_device' => $session->id === $currentSessionId,
                ];
            });

        return Inertia::render('Security/Show', [
            'sessions' => $sessions,
            'isTwoFactorAuthenticationFeatureEnabled' => Features::enabled(Features::twoFactorAuthentication()),
        ]);
    }
}
