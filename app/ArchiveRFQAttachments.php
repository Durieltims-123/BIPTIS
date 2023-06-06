<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Procact;

class ArchiveRFQAttachments extends Model
{
  protected $table='archive_rfq_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "procact_id",
    "attachment_name"
  ];
  public function rfq_attachments(){
    return $this->belongsTo(Procact::class);
  }
}
