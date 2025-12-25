<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;

class RejectedUserList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public function render()
    {
        $query = \Illuminate\Support\Facades\DB::table('rejected_users')->orderByDesc('rejected_at');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%');
        }

        $rejectedUsers = $query->paginate(10);

        return view('livewire.users.rejected-user-list', [
            'rejectedUsers' => $rejectedUsers
        ])->layout('layouts.app');
    }
}
