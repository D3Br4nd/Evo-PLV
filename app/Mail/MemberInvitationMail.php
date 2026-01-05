<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemberInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $member,
        public readonly string $inviteUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invito accesso – Pro Loco Venticanese',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.member_invitation',
            with: [
                'member' => $this->member,
                'inviteUrl' => $this->inviteUrl,
            ],
        );
    }
}


