<?php

namespace App\Livewire\Skills;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Jobs\Models\Skill;
use Livewire\Attributes\On;

class SkillList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    protected $queryString = ['search', 'searchFields', 'page'];

    public $search = '';
    public $searchFields = [];

    #[On('skillSearchUpdated')]
    public function handleSearch($payload)
    {
        $this->search = $payload['query'] ?? '';
        $this->searchFields = $payload['fields'] ?? [];
        $this->resetPage();
    }

    public function delete($id)
    {
        $skill = Skill::findOrFail($id);
        $skill->delete();

        session()->flash('message', 'Skill moved to trash!');
    }

    public function render()
    {
        $query = Skill::with('category')->latest();

        // فقط لو في search
        if ($this->search && count($this->searchFields) > 0) {
            $query->where(function ($q) {
                foreach ($this->searchFields as $i => $field) {
                    if (str_contains($field, '.')) {
                        [$relation, $col] = explode('.', $field);
                        if ($i === 0) {
                            $q->whereHas($relation, fn($q2) => $q2->where($col, 'like', "%{$this->search}%"));
                        } else {
                            $q->orWhereHas($relation, fn($q2) => $q2->where($col, 'like', "%{$this->search}%"));
                        }
                    } else {
                        if ($i === 0) {
                            $q->where($field, 'like', "%{$this->search}%");
                        } else {
                            $q->orWhere($field, 'like', "%{$this->search}%");
                        }
                    }
                }
            });
        }

        $skills = $query->paginate(10);

        return view('livewire.skill.skill-list', [
            'skills' => $skills,
            'search' => $this->search,
            'searchFields' => $this->searchFields,
        ])->layout('layouts.app');
    }
}

