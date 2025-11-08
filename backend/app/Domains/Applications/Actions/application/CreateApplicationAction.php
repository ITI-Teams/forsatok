<?php
namespace App\Domains\Applications\Actions\application;

use App\Domains\Applications\Models\JobApplication;

use Illuminate\Support\Facades\Auth;

class CreateApplicationAction{
    public function execute(array $data):JobApplication{
        return JobApplication::create($data);
    }

}
