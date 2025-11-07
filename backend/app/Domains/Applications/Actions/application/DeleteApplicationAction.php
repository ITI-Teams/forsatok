<?php
namespace App\Domains\Applications\Actions\application;

use App\Domains\Applications\Models\JobApplication;

class DeleteApplicationAction{
    public function execute(int $jobpostId){
        $jobs =JobApplication::onlyTrashed()->findOrFail($jobpostId);
        $jobs->forceDelete();
    }
}
