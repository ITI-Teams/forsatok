<?php

namespace App\Livewire\Admin\Permissions;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Livewire\WithPagination;

class PermissionIndex extends Component
{
    use WithPagination;

    public $name, $permissionId;
    public $updateMode = false;
    public $deleteId;

    protected $rules = [
        'name' => 'required|string|max:255|unique:permissions,name',
    ];

    public function render()
    {
        return view('livewire.admin.permissions.permission-index', [
            'permissions' => Permission::orderBy('id', 'desc')->paginate(10),
        ])->layout('layouts.app');
    }

    public function resetInput()
    {
        $this->name = '';
        $this->permissionId = null;
        $this->updateMode = false;
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();
        Permission::create(['name' => $this->name]);
        $this->dispatch('show-message', type: 'success', message: 'Permission created successfully.');
        $this->resetInput();
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        $this->name = $permission->name;
        $this->permissionId = $id;
        $this->updateMode = true;
    }

    public function update()
    {
        $permission = Permission::findOrFail($this->permissionId);
        $this->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);
        $permission->update(['name' => $this->name]);
        $this->dispatch('show-message', type: 'success', message: 'Permission updated successfully.');
        $this->resetInput();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function deletePermission()
    {
        if ($this->deleteId) {
            Permission::destroy($this->deleteId);
            $this->deleteId = null;
            $this->dispatch('show-message', type: 'success', message: 'Permission deleted successfully.');
        }
    }

    public function cancelDelete()
    {
        $this->deleteId = null;
    }
}
