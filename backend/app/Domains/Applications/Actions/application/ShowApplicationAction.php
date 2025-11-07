<?php
namespace App\Domains\Applications\Actions\application;

use App\Domains\Applications\Models\JobApplication;

class ShowApplicationAction{
    public function execute(int $jobpostId){
        return JobApplication::with(['employer', 'category'])->findOrFail($jobpostId);
    }
}
