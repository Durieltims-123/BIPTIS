<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RescheduleProjects extends Model
{
	protected $table='reschedule_projects';
	protected $primaryKey = 'id';
	protected $fillable=[
		"reschedule_id",
		"procact_id"
	];
}
