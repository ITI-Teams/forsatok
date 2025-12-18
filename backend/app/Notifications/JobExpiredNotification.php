<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Domains\Shared\Services\FrontendDetection\FrontendUrlService;

class JobExpiredNotification extends Notification
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
        // For expiration, actor is usually System, but we support passing it.
        $actorName = $this->actor ? $this->actor->name : 'System';

        // Generate dynamic link based on source
        $urlService = app(FrontendUrlService::class);
        $urlService->setSource($this->source);
        $jobUrl = $urlService->makeUrl("jobs/{$this->job->id}");

        return (new MailMessage)
            ->subject('Your Job Has Expired')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("Your job posting '{$this->job->title}' has expired.")
            ->line('The deadline for this job has passed.')
            ->line('It is no longer active on our platform.')
            ->action('View Job', $jobUrl)
            ->line('Thank you for using our application!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'id' => $this->job->id,
            'title' => 'Job Expired',
            'message' => "Your job '{$this->job->title}' has expired.",
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'notification' => $this->toDatabase($notifiable)
        ]);
    }
}
