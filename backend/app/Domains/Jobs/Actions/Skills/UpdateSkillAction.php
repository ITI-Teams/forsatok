<?php

namespace App\Domains\Jobs\Actions\Skills;

use App\Domains\Jobs\Models\Skill;

class UpdateSkillAction
{
    public function execute(Skill $skill, array $data): Skill
    {
        $skill->update($data);
        return $skill;
    }
}
