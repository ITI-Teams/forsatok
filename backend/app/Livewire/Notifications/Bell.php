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
        'notificationReceived' => 'onNotificationReceived'
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

    public function onNotificationReceived($notification = null)
    {
        if (is_array($notification) && isset($notification['notification'])) {
            $notification = $notification['notification'];
        }

        if (!$notification) {
            $notification = [
                'id' => uniqid(),
                'type' => 'TestNotification',
                'data' => ['message' => 'Test notification message'],
            ];
        }

        $notificationData = [
            'id' => $notification['id'] ?? uniqid(),
            'type' => class_basename($notification['type'] ?? 'JobCreatedNotification'),
            'data' => $notification['data'] ?? $notification,
            'read_at' => null,
            'created_at' => now()->diffForHumans(),
        ];

        array_unshift($this->notifications, $notificationData);

        if (count($this->notifications) > $this->limit) {
            $this->notifications = array_slice($this->notifications, 0, $this->limit);
        }

        $this->unreadCount++;
    }

    public function markAsRead($id)
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }
        try {
            $updated = $user->notifications()
                ->where('id', $id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            if ($updated) {
                $this->unreadCount = $user->unreadNotifications()->count();

                foreach ($this->notifications as &$item) {
                    if ($item['id'] === $id) {
                        $item['read_at'] = now()->toDateTimeString();
                        break;
                    }
                }

                $this->dispatch('notification-marked-read');
            }
        } catch (\Exception $e) {
            $this->loadNotifications(); // Fallback
        }
    }

    public function markAllRead()
    {
        $user = auth()->user();
        if (! $user) return;

        $user->unreadNotifications->markAsRead();
        $this->unreadCount = 0;

        foreach ($this->notifications as &$notif) {
            $notif['read_at'] = now();
        }
    }

    public function testNotification()
    {
        $this->onNotificationReceived([
            'id' => 'test-' . uniqid(),
            'type' => 'TestNotification',
            'data' => [
                'message' => 'This is a test notification from Livewire',
                'title' => 'Test Title'
            ]
        ]);
    }

    public function render()
    {
        return view('livewire.notifications.bell');
    }
}
