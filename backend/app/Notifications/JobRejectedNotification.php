<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Domains\Shared\Services\FrontendDetection\FrontendUrlService;

class JobRejectedNotification extends Notification implements ShouldQueue
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
        $reason = $this->job->latestRejection->reason ?? 'No reason provided';

        $urlService = app(FrontendUrlService::class);
        $urlService->setSource($this->source);
        $jobUrl = $urlService->makeUrl("jobs/{$this->job->id}");

        $view = in_array($this->source, ['hive', 'react_dashboard']) ? 'emails.generic_hive' : 'emails.generic_jobhub';

        return (new MailMessage)
            ->subject('Your Job Has Been Rejected')
            ->view($view, [
                'title' => 'Your Job Has Been Rejected',
                'greeting' => 'Hello ' . $notifiable->name . ',',
                'lines' => [
                    "Unfortunately, your job posting '{$this->job->title}' has been rejected by {$actorName}.",
                    "Reason: {$reason}",
                    "You can review the reason and re-submit your job."
                ],
                'actionText' => 'View Job',
                'actionUrl' => $jobUrl
            ]);
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Job Rejected',
            'message' => "Your job '{$this->job->title}' has been rejected by " . ($this->actor ? $this->actor->name : 'System'),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'notification' => $this->toDatabase($notifiable)
        ]);
    }
}
