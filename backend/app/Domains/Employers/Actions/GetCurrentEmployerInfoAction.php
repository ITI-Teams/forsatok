<?php

namespace App\Domains\Employers\Actions;

use App\Domains\Employers\Models\EmployerInfo;

class GetCurrentEmployerInfoAction
{
    public function execute(int $userId): ?EmployerInfo
    {
        return EmployerInfo::with('reviews')->where('user_id', $userId)->first();
    }
}
