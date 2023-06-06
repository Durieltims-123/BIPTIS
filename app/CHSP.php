<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CHSP extends Model
{
	protected $table='CHSP';
	protected $primaryKey = 'chsp_id';
	protected $fillable=[
		"chsp_project_bid",
		"contractor_id",
		"chsp_date_issuance",
		"chsp_received_date",
		"chsp_remarks"
	];
}
