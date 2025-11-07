<?php
namespace App\Domains\Applications\Actions\application;

use App\Domains\Applications\Models\JobApplication;
class UpdateApplicationAction{
    public function execute(JobApplication $job_app, array $data){
        $job_app->update($data);
            return $job_app;
    }
}
