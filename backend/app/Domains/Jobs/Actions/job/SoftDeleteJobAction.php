<?php

namespace App\Domains\Jobs\Actions\job;

use App\Domains\Jobs\Models\JobPost;

class SoftDeleteJobAction{
    public function execute(JobPost $jobPost){
        $jobPost->delete();
    }
}
