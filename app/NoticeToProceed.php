<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NoticeToProceed extends Model
{
  protected $table='notice_to_proceeds';
  protected $primaryKey = 'ntp_id';
  protected $fillable=[
    "project_bid_id",
    "ntp_date_generated",
    "ntp_date_released",
    "ntp_date_received_by_contractor",
    "ntp_date_received",
    "ntp_remarks",
    "with_attachment",
    "duration_start_date",
    "duration_end_date",
    "posting_status",
    "posting_date",
  ];
}
