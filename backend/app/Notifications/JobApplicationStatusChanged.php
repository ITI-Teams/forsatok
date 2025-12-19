<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Domains\Shared\Services\FrontendDetection\FrontendUrlService;

class JobApplicationStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $application, public $source = 'web') {}

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toMail($notifiable)
    {
        $companyName = $this->application->jobPost->employer->employerInfo->company_name ?? 'the company';
        $status = ucfirst($this->application->status);

        // Generate dynamic link based on source
        $urlService = app(FrontendUrlService::class);
        $urlService->setSource($this->source);
        $applicationUrl = $urlService->makeUrl('dashboard/candidate/applications');

        $view = in_array($this->source, ['hive', 'react_dashboard']) ? 'emails.generic_hive' : 'emails.generic_jobhub';

        return (new MailMessage)
                    ->subject('Application Status Updated')
                    ->view($view, [
                        'title' => 'Application Status Updated',
                        'greeting' => 'Hello ' . $notifiable->name . ',',
                        'lines' => [
                            "Your application for the position '{$this->application->jobPost->title}' has been updated.",
                            "New Status: {$status}",
                            "Action taken by: {$companyName}"
                        ],
                        'actionText' => 'View Application',
                        'actionUrl' => $applicationUrl
                    ]);
    }

    public function toDatabase($notifiable)
    {
        $companyName = $this->application->jobPost->employer->employerInfo->company_name ?? 'the company';
        return [
            'title' => 'Application Status Updated',
            'message' => "Your application for '{$this->application->jobPost->title}' is now '{$this->application->status}' by '{$companyName}'.",
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'notification' => $this->toDatabase($notifiable)
        ]);
    }
}
