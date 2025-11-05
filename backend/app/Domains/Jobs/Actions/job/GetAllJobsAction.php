<?php
namespace App\Domains\Jobs\Actions\Job;

use App\Domains\Jobs\Models\JobPost;
use Illuminate\Database\Eloquent\Collection;

class GetAllJobsAction{
    public function execute():Collection{
        return JobPost::with(['employer', 'category'])->latest()->get();
    }
}

