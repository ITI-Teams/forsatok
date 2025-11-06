<?php

namespace App\Livewire\Skills;

use App\Domains\Jobs\Actions\Skills\SoftDeleteSkillAction;
use App\Domains\Jobs\Actions\Skills\DeleteSkillAction;
use App\Domains\Jobs\Models\Skill;
use Livewire\Component;

class SkillList extends Component
{
    public $skills;

    public function mount()
    {
        $this->loadSkills();
    }

    public function loadSkills()
    {
        // Load all skills with related category
        $this->skills = Skill::with('category')->latest()->get();
    }

    public function delete($id, SoftDeleteSkillAction $delete)
    {
        $skill = Skill::findOrFail($id);
        $delete->execute($skill);

        session()->flash('message', '🗑️ Skill moved to trash!');
        $this->loadSkills();
    }

    public function render()
    {
        return view('livewire.skills.skill-list')->layout('layouts.app');
    }
}
