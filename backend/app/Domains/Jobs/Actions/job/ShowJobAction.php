<?php
namespace App\Domains\Jobs\Actions\job;

use App\Domains\Jobs\Models\JobPost;

class ShowJobAction{
    public function execute(int $jobpostId){
        return JobPost::with(['employer', 'category', 'location.country', 'location.city'])
        ->findOrFail($jobpostId);
    }
}
