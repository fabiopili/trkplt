<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{

	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
    	'url',
    	'ip',
    	'results'
    ];

	// Cast JSON options field as an array
    protected $casts = [
        'results' => 'array',
        'raw_results' => 'array',
    ];

}
