<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Procact;

class ArchiveITBAttachments extends Model
{
  protected $table='archive_itb_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "procact_id",
    "attachment_name"
  ];
  public function itbrfq_attachments(){
    return $this->belongsTo(Procact::class);
  }
}
