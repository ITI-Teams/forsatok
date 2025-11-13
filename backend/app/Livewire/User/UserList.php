<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Users\Models\User;
use App\Domains\Users\Actions\SoftDeleteUserAction;
use Livewire\Attributes\On;

class UserList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $searchFields = [];

    #[On('userSearchUpdated')]
    public function handleSearch($payload)
    {
        $this->search = $payload['query'] ?? '';
        $this->searchFields = $payload['fields'] ?? [];
        $this->resetPage(); // ترجع للصفحة 1 عند البحث
    }

    public function delete($id, SoftDeleteUserAction $delete)
    {
        $user = User::findOrFail($id);
        $delete->execute($user);

        session()->flash('message', 'User moved to trash!');
    }

    public function render()
    {
        $query = User::latest();

        if ($this->search && count($this->searchFields) > 0) {
            $query->where(function ($q) {
                foreach ($this->searchFields as $i => $field) {
                    $method = $i === 0 ? 'where' : 'orWhere';
                    $q->$method($field, 'like', "%{$this->search}%");
                }
            });
        }

        $users = $query->paginate(5);

        return view('livewire.users.user-list', [
            'users' => $users,
        ])->layout('layouts.app');
    }
}
