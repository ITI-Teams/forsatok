<?php

namespace App\Livewire\Admin\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;
class RoleIndex extends Component
{
    use WithPagination;

    public $name, $role_id;
    public $deleteId;
    protected $listeners = ['deleteConfirmed' => 'deleteRole'];
    public $updateMode = false;

    protected $rules = [
        'name' => 'required|string|max:255|unique:roles,name',
    ];

    public function render()
    {
        return view('livewire.admin.roles.role-index', [
            'roles' => Role::orderBy('id', 'desc')->paginate(5),
        ])->layout('layouts.app');
    }

    public function store()
    {
        $this->validate();
        Role::create(['name' => $this->name]);
        $this->reset(['name']);
        session()->flash('message', 'Role created successfully.');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->role_id = $role->id;
        $this->name = $role->name;
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $this->role_id,
        ]);

        $role = Role::findOrFail($this->role_id);
        $role->update(['name' => $this->name]);

        $this->reset(['name', 'updateMode']);
        session()->flash('message', 'Role updated successfully.');
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('show-delete-modal');
    }

    // Actual delete action
    public function deleteRole()
    {
        if ($this->deleteId) {
            Role::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            session()->flash('message', 'Role deleted successfully.');
        }
    }

    public function resetDelete()
    {
        $this->deleteId = null;
    }
}
