<?php

namespace App\Domains\Jobs\Actions\Skills;

use App\Domains\Jobs\Models\Skill;

class SoftDeleteSkillAction
{
    public function execute(Skill $skill): void
    {
        $skill->delete();
    }
}
