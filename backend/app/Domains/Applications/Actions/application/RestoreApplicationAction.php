<?php
namespace App\Domains\Applications\Actions\application;

use App\Domains\Applications\Models\JobApplication;
class RestoreApplicationAction{
    public function execute(int $jobpostId){
            $job=JobApplication::onlyTrashed()->findOrFail($jobpostId);
            $job->restore();
    }
}
