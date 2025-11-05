<?php

namespace App\Livewire\User;

use App\Domains\Users\Actions\DeleteUserAction;
use App\Domains\Users\Actions\RestoreUserAction;
use App\Domains\Users\Models\User;
use Livewire\Component;

class UserTrash extends Component
{
    public $trashedUsers;

    public function mount()
    {
        $this->loadTrashed();
    }

    public function loadTrashed()
    {
        $this->trashedUsers = User::onlyTrashed()->latest()->get();
    }

    public function restore($id, RestoreUserAction $restore)
    {
        $restore->execute($id);
        session()->flash('message', '✅ User restored successfully!');
        $this->loadTrashed();
    }

    public function forceDelete($id, DeleteUserAction $forceDelet)
    {
        $forceDelet->execute($id);
        session()->flash('message', '❌ User permanently deleted!');
        $this->loadTrashed();
    }

    public function render()
    {
        return view('livewire.users.user-trash')->layout('layouts.app');
    }
}
