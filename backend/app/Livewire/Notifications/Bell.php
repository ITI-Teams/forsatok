<?php

namespace App\Livewire\Notifications;

use Illuminate\Notifications\DatabaseNotification;
use Livewire\Component;

class Bell extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public $limit = 10;

    protected $listeners = [
        'notificationReceived' => 'onNotificationReceived',
        'refreshNotifications' => '$refresh'
    ];

    public function mount(int $limit = 10)
    {
        $this->limit = $limit;
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = auth()->user();
        if (! $user) {
            $this->notifications = [];
            $this->unreadCount = 0;
            return;
        }


        $all = $user->notifications()->latest()->take($this->limit)->get();
        $this->notifications = $all->map(function (DatabaseNotification $n) {
            return [
                'id' => $n->id,
                'type' => class_basename($n->type),
                'data' => $n->data,
                'read_at' => $n->read_at,
                'created_at' => $n->created_at->diffForHumans(),
            ];
        })->toArray();

        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function onNotificationReceived($payload)
    {
        // payload from Echo listener (notification object)
        array_unshift($this->notifications, [
            'id' => $payload['id'] ?? ($payload['notification']['id'] ?? null),
            'type' => $payload['type'] ?? ($payload['notification']['type'] ?? null),
            'data' => $payload['data'] ?? ($payload['notification']['data'] ?? []),
            'read_at' => null,
            'created_at' => now()->diffForHumans(),
        ]);
        $this->unreadCount++;
        $this->notifications = array_slice($this->notifications, 0, $this->limit);
        $this->emitSelf('refreshNotifications');
    }

    public function markAsRead($id)
    {
        $user = auth()->user();
        if (! $user) return;

        $notif = $user->unreadNotifications()->find($id);
        if ($notif) {
            $notif->markAsRead();
            $this->unreadCount = $user->unreadNotifications()->count();
            $this->loadNotifications();
        }
    }

    public function markAllRead()
    {
        $user = auth()->user();
        if (! $user) return;

        $user->unreadNotifications->markAsRead();
        $this->unreadCount = 0;
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notifications.bell');
    }
}
