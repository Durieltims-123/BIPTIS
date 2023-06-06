<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PerformanceBond extends Model
{
	protected $table='additional_performance_bonds';
	protected $primaryKey = 'additional_pb_id';
	protected $fillable=[
		"contract_id",
		"contractor_id",
		"additional_pb_date_issuance",
		"additional_pb_expiration",
		"additional_pb_received_date",
		"additional_pb_remarks",
		"additional_pb_cluster",
	];


}
