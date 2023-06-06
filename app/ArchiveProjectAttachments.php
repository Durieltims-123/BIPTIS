<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ArchiveApp;

class ArchiveProjectAttachments extends Model
{
  protected $table='archive_app_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "archive_app_id",
    "attachment_name"
  ];
  public function project_attachments(){
    return $this->belongsTo(ArchiveApp::class);
  }
}
