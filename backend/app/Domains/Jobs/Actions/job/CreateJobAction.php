<?php
namespace App\Domains\Jobs\Actions\Job;

use App\Domains\Jobs\Models\JobPost;
use Illuminate\Support\Facades\Auth;

class CreateJobAction{
    public function execute(array $data):JobPost{
        $data['employer_id'] = Auth::id();
        return JobPost::create($data);
    }

}
