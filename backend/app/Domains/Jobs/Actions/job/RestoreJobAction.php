<?php
namespace App\Domains\Jobs\Actions\Job;

use App\Domains\Jobs\Models\JobPost;

class RestoreJobAction{
    public function execute(int $jobpostId){
            $job=JobPost::onlyTrashed()->findOrFail($jobpostId);
            $job->restore();
    }
}
