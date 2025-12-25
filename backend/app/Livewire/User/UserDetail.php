<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Domains\Users\Models\User;

class UserDetail extends Component
{
    public $user;
    public $rejectedHistory = [];

    public function mount($id)
    {
        $this->user = User::findOrFail($id);

        // Fetch rejection history (same email)
        $this->rejectedHistory = \Illuminate\Support\Facades\DB::table('rejected_users')
            ->where('email', $this->user->email)
            ->orderByDesc('rejected_at')
            ->get();
    }

    public function render()
    {
        return view('livewire.user.user-detail')->layout('layouts.app');
    }
}
