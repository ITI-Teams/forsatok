<?php

namespace App\Events;

use App\Domains\Jobs\Models\JobPost;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $job;
    public $employerId;

    public function __construct(JobPost $job)
    {
        $this->job = $job->only(['id','title','type','location','salary_min','salary_max']);
        $this->employerId = $job->employer_id;
    }
    public function broadcastOn()
    {
        return [
            new PrivateChannel('admin'),
            new PrivateChannel('employer.'.$this->employerId),
        ];
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->job['id'],
            'title' => $this->job['title'],
            'location' => $this->job['location'],
            'type' => $this->job['type'],
        ];
    }

    public function broadcastAs()
    {
        return 'JobCreated';
    }
}
