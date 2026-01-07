<?php

namespace App\Http\Controllers;

use App\Mail\MemberInvitationMail;
use App\Models\MemberInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminMemberInvitationController extends Controller
{
    public function store(User $member): RedirectResponse
    {
        $token = Str::random(64);

        $inviteUrl = url("/invite/{$token}");

        MemberInvitation::create([
            'user_id' => $member->id,
            'created_by_user_id' => request()->user()?->id,
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addHours(24),
        ]);

        // Send email (requires mail config in .env)
        $mailSent = false;
        if ($member->email) {
            try {
                Mail::to($member->email)->send(new MemberInvitationMail($member, $inviteUrl));
                $mailSent = true;
            } catch (\Throwable $e) {
                Log::warning('Member invitation email failed', [
                    'member_id' => $member->id,
                    'email' => $member->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()
            ->back()
            ->with('success', $mailSent ? 'Link invito generato e inviato via email (valido 24 ore).' : 'Link invito generato (valido 24 ore). Email non inviata.')
            ->with('invite_url', $inviteUrl);
    }

    public function destroy(User $member): RedirectResponse
    {
        // Delete all pending/unused invitations for this user
        $member->invitations()
            ->whereNull('used_at')
            ->delete();

        return redirect()
            ->back()
            ->with('success', 'Inviti annullati con successo.');
    }
}


