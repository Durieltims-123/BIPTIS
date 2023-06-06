<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectBidderNotice extends Model
{
  protected $table='project_bidder_notices';
  protected $primaryKey = 'project_bidder_notice_id';
  protected $fillable=[
    'project_bid',
    'notice_type',
    'bac_id',
    'date_generated',
    'date_released',
    'date_received_by_contractor',
    'date_received',
    'mr_due_date',
    'with_attachment',
    'notice_remarks'
  ];
}
