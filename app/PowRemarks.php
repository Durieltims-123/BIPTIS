<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PowRemarks extends Model
{
	protected $table='pow_remarks';
	protected $fillable=[
		"plan_id",
		"pow_reason",
		"pow_remarks",
		"created_by"
	];

}
