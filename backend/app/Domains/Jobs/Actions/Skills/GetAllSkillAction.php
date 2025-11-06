<?php

namespace App\Domains\Jobs\Actions\Skills;

use App\Domains\Jobs\Models\Skill;
use Illuminate\Database\Eloquent\Collection;

class GetAllSkillsAction
{
    public function execute(): Collection
    {
        return Skill::with('category')
                    ->orderBy('name')
                    ->get();
    }
}
