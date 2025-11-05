<?php
namespace App\Domains\Jobs\Actions\Job;

use App\Domains\Jobs\Models\JobPost;

class ShowJobAction{
    public function execute(int $jobpostId){
        return JobPost::with(['employer', 'category'])->findOrFail($jobpostId);
    }
}
