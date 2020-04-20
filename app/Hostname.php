<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hostname extends Model
{

	protected $fillable = ['block_list_id', 'hostname', 'domain'];

	/**
	* Relationships
	*/
	public function blockLists()
	{
		return $this->belongsToMany('App\BlockList');
	}

}
