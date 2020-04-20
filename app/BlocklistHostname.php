<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlocklistHostname extends Model
{

	protected $table = 'block_list_hostname';
	protected $fillable = ['block_list_id', 'hostname_id'];

}
