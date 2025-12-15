<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobApplicationStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public $application) {}

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
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
