<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectTimeline extends Model
{
  protected $table = 'project_timelines';
  protected $primaryKey = 'timeline_id';
  protected $fillable = [
    "plan_id",
    "procact_id",
    "timeline_status",
    "pre_proc_date",
    "advertisement_start",
    "advertisement_end",
    "pre_bid_start",
    "pre_bid_end",
    "bid_submission_start",
    "bid_submission_end",
    "bid_evaluation_start",
    "bid_evaluation_end",
    "post_qualification_start",
    "post_qualification_end",
    "award_notice_start",
    "award_notice_end",
    "contract_signing_start",
    "contract_signing_end",
    "authority_approval_start",
    "authority_approval_end",
    "proceed_notice_start",
    "proceed_notice_end",
    "created_at",
    "updated_at"
  ];
}
