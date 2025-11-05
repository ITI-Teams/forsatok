<?php

namespace App\Domains\Employers\Actions;

use App\Domains\Employers\Models\EmployerInfo;

class UpdateEmployerInfoAction
{
    public function execute(EmployerInfo $employerInfo, array $data): EmployerInfo
    {
        // Remove email and phone from data as they're not stored in employer_infos table
        unset($data['email'], $data['phone']);
        $employerInfo->fill($data);
        $employerInfo->save();
        return $employerInfo;
    }
}



