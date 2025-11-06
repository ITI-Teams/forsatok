<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Domains\Users\Models\User;
use App\Domains\Users\Actions\SoftDeleteUserAction;

class UserList extends Component
{
    public $users;

    public function mount()
    {
        $this->loadUsers();
    }

    public function loadUsers()
    {
        $this->users = User::latest()->get();
    }

    public function delete($id, SoftDeleteUserAction $delete)
    {
        $user = User::findOrFail($id);
        $delete->execute($user);

        session()->flash('message', 'User moved to trash!');
        $this->loadUsers();
    }

    public function render()
    {
        return view('livewire.users.user-list')->layout('layouts.app');
    }
}
