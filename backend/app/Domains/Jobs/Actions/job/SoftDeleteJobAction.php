<?php

namespace App\Domains\Jobs\Actions\Job;

use App\Domains\Jobs\Models\JobPost;

class SoftDeleteJobAction{
    public function execute(JobPost $jobPost){
        $jobPost->delete();
    }
}
