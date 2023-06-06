<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reschedule extends Model
{
	protected $table='reschedule';
	protected $primaryKey = 'id';
	protected $fillable=[
		"opening_date",
		"new_opening_date",
		"reschedule_remarks",
	];
}
