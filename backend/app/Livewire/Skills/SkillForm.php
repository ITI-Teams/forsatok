<?php

namespace App\Livewire\Skills;

use App\Domains\Jobs\Actions\Skills\CreateSkillAction;
use App\Domains\Jobs\Actions\Skills\UpdateSkillAction;
use App\Domains\Jobs\Requests\Skill\StoreSkillRequest;
use App\Domains\Jobs\Requests\Skill\UpdateSkillRequest;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use App\Domains\Jobs\Models\Skill;
use App\Domains\Jobs\Models\Category;

class SkillForm extends Component
{
    public $skillId, $name, $category_id;
    public $categories;

    protected $rules = [
        'name'        => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
    ];

    public function mount($skill = null)
    {
        $this->categories = Category::all();

        if ($skill) {
            $model = Skill::findOrFail($skill);
            $this->skillId     = $model->id;
            $this->name        = $model->name;
            $this->category_id = $model->category_id;
        }
    }

    public function save(CreateSkillAction $create, UpdateSkillAction $update)
    {
        if ($this->skillId) {
            $request = new UpdateSkillRequest();
            $request->merge([
                'name'        => $this->name,
                'skill_id'    => $this->skillId,
                'category_id' => $this->category_id,
            ]);
            $validated = Validator::make($request->all(), $request->rules())->validate();
        } else {
            $request = new StoreSkillRequest();
            $request->merge([
                'name'        => $this->name,
                'category_id' => $this->category_id,
            ]);
            $validated = Validator::make($request->all(), $request->rules())->validate();
        }

        if ($this->skillId) {
            $skill = Skill::findOrFail($this->skillId);
            $update->execute($skill, $validated);
            session()->flash('message', 'Skill updated successfully!');
        } else {
            $create->execute($validated);
            session()->flash('message', 'Skill created successfully!');
        }

        return $this->redirectRoute('skills.index', navigate: true);
    }

    public function cancel()
    {
        return $this->redirectRoute('skills.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.skill.skill-form')->layout('layouts.app');

    }
}
