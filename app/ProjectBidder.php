<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectBidder extends Model
{
  protected $table='project_bidders';
  protected $primaryKey = 'project_bid';
  protected $fillable=[
    'project_bid',
    'rfq_project_id',
    'bid_doc_project_id',
    'bid_status',
    "withdrawal_letter_date",
    "withdrawal_receive_date"
  ];
}
