<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ArchiveApp;

class ArchivePPMPAttachments extends Model
{
  protected $table='archive_ppmp_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "archive_ppmp_id",
    "attachment_name"
  ];
  public function ppmp_attachments(){
    return $this->belongsTo(ArchivePPMP::class);
  }
}
