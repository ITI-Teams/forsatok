<?php

namespace App\Livewire\Skills;

use App\Domains\Jobs\Actions\Skills\DeleteSkillAction;
use App\Domains\Jobs\Actions\Skills\RestoreSkillAction;
use App\Domains\Jobs\Models\Skill;
use Livewire\Component;

class SkillTrash extends Component
{
    public $trashedSkills;

    public function mount()
    {
        $this->loadTrashed();
    }

    public function loadTrashed()
    {
        // Load only soft-deleted skills + category relationship
        $this->trashedSkills = Skill::onlyTrashed()->with('category')->latest()->get();
    }

    public function restore($id, RestoreSkillAction $restore)
    {
        $restore->execute($id);
        session()->flash('message', 'Skill restored successfully!');
        $this->loadTrashed();
    }

    public function forceDelete($id, DeleteSkillAction $forceDelete)
    {
        $forceDelete->execute($id);
        session()->flash('message', 'Skill permanently deleted!');
        $this->loadTrashed();
    }

    public function render()
    {
        return view('livewire.skill.skill-trash')->layout('layouts.app');
    }
}
