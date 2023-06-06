<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ArchiveITBAttachments;

class Procact extends Model
{
	protected $table = 'procacts';
	protected $primaryKey = 'procact_id';
	protected $fillable = [
		"plan_id",
		"procact_mode_id",
		"itb_arrangement",
		"plan_cluster_id",
		"pre_proc",
		"advertisement",
		"pre_bid",
		"eligibility_check",
		"open_bid",
		"open_time",
		"bid_evaluation",
		"post_qual",
		"award_notice",
		"contract_signing",
		"authority_approval",
		"proceed_notice",
		"itbrfq_attachment",
		"created_at",
		"updated_at",
		"posting_status",
		"posting_date"
	];
	public function itbrfq_attachments()
	{
		return $this->hasMany(ArchiveITBAttachments::class);
	}
}
