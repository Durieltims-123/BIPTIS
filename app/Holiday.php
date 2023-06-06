<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
	protected $table='holidays';
	protected $primaryKey = 'id';
	protected $fillable=[
		"holiday_date",
	  "holiday_name"
	];
}
