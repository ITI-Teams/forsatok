<?php
namespace App\Domains\Jobs\Actions\Job;

use App\Domains\Jobs\Models\JobPost;

class CreateJobAction{
    public function execute(array $data):JobPost{
        return JobPost::create($data);
    }

}
