<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlockList extends Model
{
	protected $dates = ['updated_at'];
    protected $fillable = ['name', 'url'];
	protected $hidden = ['pivot'];

	/**
	* Relationships
	*/
	public function hostnames()
	{
		return $this->belongsToMany('App\Hostname');
	}

}
