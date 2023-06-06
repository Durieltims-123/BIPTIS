<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ArchiveProjectAttachments;

class ArchiveApp extends Model
{
	//
	protected $fillable = [
		"project_year",
		"app_group_no",
		"project_type",
		"fund_category_id",
		"remarks"
	];

	public function project_attachments(){
		return $this->hasMany(ArchiveProjectAttachments::class);
	}
}
