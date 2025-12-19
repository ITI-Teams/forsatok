<?php

namespace App\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Domains\Shared\Services\FrontendDetection\FrontendUrlService;

class JobApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $job, public $actor = null, public $source = 'web')
    {
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toMail($notifiable)
    {
        $actorName = $this->actor ? $this->actor->name : 'System';

        $urlService = app(FrontendUrlService::class);
        $urlService->setSource($this->source);
        $jobUrl = $urlService->makeUrl("jobs/{$this->job->id}");

        $view = in_array($this->source, ['hive', 'react_dashboard']) ? 'emails.generic_hive' : 'emails.generic_jobhub';

        return (new MailMessage)
            ->subject('Your Job Has Been Approved')
            ->view($view, [
                'title' => 'Your Job Has Been Approved',
                'greeting' => 'Hello ' . $notifiable->name . ',',
                'lines' => [
                    "Your job posting '{$this->job->title}' has been approved by {$actorName}.",
                    "It is now live on our platform."
                ],
                'actionText' => 'View Job',
                'actionUrl' => $jobUrl
            ]);
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Job Approved',
            'message' => "Your job '{$this->job->title}' has been approved by " . ($this->actor ? $this->actor->name : 'System'),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'notification' => $this->toDatabase($notifiable)
        ]);
    }
}
