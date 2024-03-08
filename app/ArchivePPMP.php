<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ArchiveProjectAttachments;

class ArchivePPMP extends Model
{
	//
	protected $table="archive_ppmp";

	protected $fillable = [
		"ppmp_year",
		"ppmp_no",
		"ppmp_type",
		"fund_category_id",
		"remarks"
	];

	public function ppmp_attachments()
	{
		return $this->hasMany(ArchivePPMPAttachments::class);
	}
}
