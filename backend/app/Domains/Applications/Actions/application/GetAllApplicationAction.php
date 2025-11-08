<?php
namespace App\Domains\Applications\Actions\application;

use App\Domains\Applications\Models\JobApplication;
use Illuminate\Database\Eloquent\Collection;

class GetAllApplicationAction{
    public function execute():Collection{
        return JobApplication::with(['employer', 'category'])->latest()->get();
    }
}

