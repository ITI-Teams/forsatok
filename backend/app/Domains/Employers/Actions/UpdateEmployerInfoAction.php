<?php

namespace App\Domains\Employers\Actions;

use App\Domains\Employers\Models\EmployerInfo;

class UpdateEmployerInfoAction
{
    public function execute(EmployerInfo $employerInfo, array $data): EmployerInfo
    {
        //
        unset($data['email'], $data['phone']);
        $employerInfo->fill($data);
        $employerInfo->save();
        return $employerInfo;
    }
}



