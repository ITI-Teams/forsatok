<?php

namespace App\Mail;

use App\Domains\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountBanned extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public string $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $reason)
    {
        $this->user = $user;
        $this->reason = $reason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account Suspended',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.account-banned',
            with: [
                'user' => $this->user,
                'reason' => $this->reason,
                'supportEmail' => config('mail.from.address', 'support@example.com'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
