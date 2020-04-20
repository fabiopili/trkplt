<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackerRadarDomains extends Model
{

	protected $dates = ['updated_at'];
    protected $fillable = ['data','domain'];

	// Cast JSON options field as an array
    protected $casts = [
        'data' => 'array'
    ];

}
