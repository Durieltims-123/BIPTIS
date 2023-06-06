<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Order;

class ArchiveOrderAttachments extends Model
{
  protected $table='archive_order_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "order_id",
    "attachment_name"
  ];
  public function order_attachments(){
    return $this->belongsTo(Order::class);
  }
}
