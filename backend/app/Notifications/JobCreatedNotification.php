<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class JobCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $job, public $source = 'web')
    {
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'job' => $this->job->only(['id', 'title', 'location']),
            'message' => 'New job created: ' . $this->job->title,
            'user_id' => $notifiable->id,
        ]);
    }

    public function toDatabase($notifiable)
    {
        return [
            'job' => $this->job->id,
            'message' => "A new job titled '{$this->job->title}' was posted.",
            'title' => $this->job->title,
        ];
    }
}
