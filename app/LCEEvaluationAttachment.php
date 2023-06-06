<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LCEEvaluationAttachment extends Model
{
	protected $table='lce_evaluation_attachments';
	protected $primaryKey = 'id';
	protected $fillable=[
		"lce_evaluation_id",
		"attachment_name"
	];

}
