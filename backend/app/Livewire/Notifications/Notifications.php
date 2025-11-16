<?php

namespace App\Livewire\Notifications;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Notifications extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'notificationReceived' => 'refreshNotifications',
    ];

    public function refreshNotifications()
    {
        $this->resetPage();
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->update(['read_at' => now()]);
        }

        $this->resetPage();
    }
    public function render()
    {
        return view('livewire.notifications.notifications', [
            'notifications' => Auth::user()
                ->notifications()
                ->latest()
                ->paginate(10)
        ])->layout('layouts.app');
    }
}
