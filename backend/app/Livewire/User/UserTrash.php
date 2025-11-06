<?php

namespace App\Livewire\User;

use App\Domains\Users\Actions\DeleteUserAction;
use App\Domains\Users\Actions\RestoreUserAction;
use App\Domains\Users\Models\User;
use Illuminate\Validation\UnauthorizedException;
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
        session()->flash('message', 'User restored successfully!');
        $this->loadTrashed();
    }

    public function forceDelete($id, DeleteUserAction $forceDelet)
    {
        try {
            $forceDelet->execute($id);
            session()->flash( 'User deleted permanently.');
        } catch (UnauthorizedException $e) {
            session()->flash( $e->getMessage());
        } catch (\LogicException $e) {
            session()->flash($e->getMessage());
        } catch (\Exception $e) {
            session()->flash('Failed to delete user.');
        }

        $this->loadTrashed();
    }

    public function render()
    {
        return view('livewire.users.user-trash')->layout('layouts.app');
    }
}
