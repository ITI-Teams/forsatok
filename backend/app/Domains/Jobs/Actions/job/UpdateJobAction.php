<?php
namespace App\Domains\Jobs\Actions\Job;

use App\Domains\Jobs\Models\JobPost;
class UpdateJobAction{
    public function execute(JobPost $job_post, array $data){
            $job_post->update($data);
            return $job_post;
    }
}
