<?php

namespace App\Notifications\Auth;

use App\Domains\Shared\Services\FrontendDetection\FrontendUrlService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomResetPasswordNotification extends ResetPassword implements ShouldQueue
{
    use Queueable;

    protected string $source;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     * @param string $source
     */
    public function __construct(string $token, string $source = 'web')
    {
        parent::__construct($token);
        $this->source = $source;
    }

    /**
     * Get the reset URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function resetUrl($notifiable)
    {
        // Restore the source from the time the notification was created
        app(FrontendUrlService::class)->setSource($this->source);

        return parent::resetUrl($notifiable);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = $this->resetUrl($notifiable);

        $view = in_array($this->source, ['hive', 'react_dashboard']) ? 'emails.generic_hive' : 'emails.generic_jobhub';
        $projectName = in_array($this->source, ['hive', 'react_dashboard']) ? 'Hive' : 'JobHub';

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Reset Password Notification')
            ->view($view, [
                'title' => 'Reset Password',
                'greeting' => 'Hello ' . ($notifiable->name ?? '') . ',',
                'lines' => [
                    "You are receiving this email because we received a password reset request for your {$projectName} account.",
                    'This password reset link will expire in 60 minutes.',
                    'If you did not request a password reset, no further action is required.'
                ],
                'actionText' => 'Reset Password',
                'actionUrl' => $url
            ]);
    }
}
