<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Domains\Users\Models\User;

class UserDetail extends Component
{
    public $user;
    public $rejectedHistory = [];
    public $statusHistory = [];

    public function mount($id)
    {
        $this->user = User::findOrFail($id);

        // Fetch rejection history (same email)
        $this->rejectedHistory = \Illuminate\Support\Facades\DB::table('rejected_users')
            ->where('email', $this->user->email)
            ->orderByDesc('rejected_at')
            ->get();

        // Fetch status history (approve, reject, ban, unban)
        $this->statusHistory = \Illuminate\Support\Facades\DB::table('user_status_history')
            ->where('user_id', $this->user->id)
            ->orWhere('email', $this->user->email)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($record) {
                // Get admin who performed the action
                $admin = User::find($record->actioned_by);
                $record->admin_name = $admin?->name ?? 'Unknown Admin';
                $record->admin_email = $admin?->email ?? 'N/A';
                return $record;
            });
    }

    public function render()
    {
        return view('livewire.user.user-detail')->layout('layouts.app');
    }
}
