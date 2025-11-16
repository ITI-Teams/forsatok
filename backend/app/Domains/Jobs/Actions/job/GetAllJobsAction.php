<?php
namespace App\Domains\Jobs\Actions\job;

use App\Domains\Jobs\Models\JobPost;
use Illuminate\Database\Eloquent\Collection;

class GetAllJobsAction{
    public function execute():Collection{
        return JobPost::with(['employer', 'category','location.country', 'location.city'])
        ->latest()
        ->get();
    }
}

