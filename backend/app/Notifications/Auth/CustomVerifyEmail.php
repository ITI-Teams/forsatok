<?php

namespace App\Notifications\Auth;

use App\Domains\Shared\Services\FrontendDetection\FrontendUrlService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomVerifyEmail extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    protected string $source;

    public function __construct(string $source = 'web')
    {
        $this->source = $source;
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        app(FrontendUrlService::class)->setSource($this->source);

        return parent::verificationUrl($notifiable);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = $this->verificationUrl($notifiable);

        $view = in_array($this->source, ['hive', 'react_dashboard']) ? 'emails.generic_hive' : 'emails.generic_jobhub';
        $projectName = in_array($this->source, ['hive', 'react_dashboard']) ? 'Hive' : 'JobHub';

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Verify Email Address')
            ->view($view, [
                'title' => 'Verify Email Address',
                'greeting' => 'Hello ' . ($notifiable->name ?? '') . ',',
                'lines' => [
                    "Please click the button below to verify your email address for your {$projectName} account.",
                    'If you did not create an account, no further action is required.'
                ],
                'actionText' => 'Verify Email Address',
                'actionUrl' => $url
            ]);
    }
}
