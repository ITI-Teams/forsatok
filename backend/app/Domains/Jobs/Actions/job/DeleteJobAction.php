<?php
namespace App\Domains\Jobs\Actions\job;

use App\Domains\Jobs\Models\JobPost;

class DeleteJobAction{
    public function execute(int $jobpostId){
        $jobs =JobPost::onlyTrashed()->findOrFail($jobpostId);
        $jobs->forceDelete();
    }
}
