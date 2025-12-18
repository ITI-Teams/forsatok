<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Domains\Shared\Services\FrontendDetection\FrontendUrlService;

class JobApplicationStatusChanged extends Notification
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
        // Assuming the candidate application list route is 'candidate/applications' or similar
        // Adjust path based on your actual frontend routing for candidate applications
        $applicationUrl = $urlService->makeUrl('dashboard/candidate/applications');

        return (new MailMessage)
                    ->subject('Application Status Updated')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line("Your application for the position '{$this->application->jobPost->title}' has been updated.")
                    ->line("New Status: {$status}")
                    ->line("Action taken by: {$companyName}")
                    ->action('View Application', $applicationUrl)
                    ->line('Thank you for using our platform!');
    }

    public function toDatabase($notifiable)
    {
        $companyName = $this->application->jobPost->employer->employerInfo->company_name ?? 'the company';
        return [
            'title' => 'Application Status Updated',
            'message' => "Your application for '{$this->application->jobPost->title}' is now '{$this->application->status}' by '{$companyName}'.",
        ];
    }

    // {$this->application->employer_info->name}
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'notification' => $this->toDatabase($notifiable)
        ]);
    }
}
