<?php

namespace App\Http\Controllers;

use App\Models\MemberInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class MemberInvitationAcceptController extends Controller
{
    public function show(string $token)
    {
        $invitation = MemberInvitation::query()
            ->with('user')
            ->where('token_hash', hash('sha256', $token))
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $invitation) {
            abort(404);
        }

        return Inertia::render('Auth/Invite', [
            'token' => $token,
            'email' => $invitation->user->email,
            'name' => $invitation->user->name,
        ]);
    }

    public function store(Request $request, string $token)
    {
        $invitation = MemberInvitation::query()
            ->with('user')
            ->where('token_hash', hash('sha256', $token))
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->lockForUpdate()
            ->first();

        if (! $invitation) {
            abort(404);
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $invitation->user;
        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'must_set_password' => false,
        ])->save();

        $invitation->update(['used_at' => now()]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/me/onboarding')->with('success', 'Password impostata. Benvenuto!');
    }
}


