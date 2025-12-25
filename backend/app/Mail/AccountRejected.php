<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountRejected extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $userEmail;
    public string $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(string $userName, string $userEmail, string $reason)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->reason = $reason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account Application Status Update',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.account-rejected',
            with: [
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
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
