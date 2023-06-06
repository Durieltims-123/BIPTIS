<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NoticeOfAward extends Model
{
  protected $table='notice_of_awards';
  protected $primaryKey = 'notice_award_id';
  protected $fillable=[
    "project_bid_id",
    "date_generated",
    "date_released",
    "date_received_by_contractor",
    "date_received",
    "noa_remarks",
    "with_attachment",
    "posting_status",
    "posting_date"
  ];
}
