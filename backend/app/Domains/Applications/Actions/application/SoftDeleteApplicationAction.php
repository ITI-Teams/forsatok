<?php

namespace App\Domains\Applications\Actions\application;

use App\Domains\Applications\Models\JobApplication;

class SoftDeleteApplicationAction{
    public function execute(JobApplication $jobPost){
        $jobPost->delete();
    }
}
