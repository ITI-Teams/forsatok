<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Domains\Shared\Services\FrontendDetection\FrontendUrlService;

class JobRejectedNotification extends Notification
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

        // Generate dynamic link based on source
        $urlService = app(FrontendUrlService::class);
        $urlService->setSource($this->source);
        $jobUrl = $urlService->makeUrl("jobs/{$this->job->id}");

        return (new MailMessage)
            ->subject('Your Job Has Been Rejected')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("Unfortunately, your job posting '{$this->job->title}' has been rejected by {$actorName}.")
            ->line("Reason: {$reason}")
            ->line('You can review the reason and re-submit your job.')
            ->action('View Job', $jobUrl)
            ->line('Thank you for using our application!');
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
