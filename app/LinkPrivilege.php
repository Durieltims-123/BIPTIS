<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinkPrivilege extends Model
{
	protected $table='link_privileges';
	protected $primaryKey = 'id';
	protected $fillable=[
		"link_id",
		"privilege"
	];
}
