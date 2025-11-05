<?php
namespace App\Domains\Jobs\Actions\Job;

use App\Domains\Jobs\Models\JobPost;

class DeleteJobAction{
    public function execute(int $jobpostId){
        $jobs =JobPost::onlyTrashed()->findOrFail($jobpostId);
        $jobs->forceDelete();
    }
}
