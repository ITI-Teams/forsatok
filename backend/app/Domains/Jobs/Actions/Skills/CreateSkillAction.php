<?php

namespace App\Domains\Jobs\Actions\Skills;

use App\Domains\Jobs\Models\Skill;
use Illuminate\Support\Str;

class CreateSkillAction
{
    public function execute(array $data): Skill
    {
        $data['slug'] = Str::slug($data['name']);
        return Skill::create($data);
    }
}
