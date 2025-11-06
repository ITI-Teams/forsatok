<?php

namespace App\Domains\Jobs\Actions\Skills;

use App\Domains\Jobs\Models\Skill;

class RestoreSkillAction
{
    public function execute(int $skillId): void
    {
        $skill = Skill::onlyTrashed()->findOrFail($skillId);
        $skill->restore();
    }
}
