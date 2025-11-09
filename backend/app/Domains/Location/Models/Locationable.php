<?php

namespace App\Domains\Location\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Locationable extends Model
{
    use HasFactory;

    protected $fillable = [
        'locationable_id',
        'locationable_type',
        'country_id',
        'city_id',
        'address',
    ];

    public function locationable()
    {
        return $this->morphTo();
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}

