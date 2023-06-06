<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Termination extends Model
{
	protected $table='termination';
	protected $primaryKey = 'termination_id';
	protected $fillable=[
		'date_of_termination',
		'procact_id',
		'project_bid',
		'contractor_id',
		'governor_id',
		'reason',
		'with_attachment'
	];
}
