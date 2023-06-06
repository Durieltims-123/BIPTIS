<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ArchiveMinute;

class ArchiveMinuteAttachments extends Model
{
  protected $table='archive_minutes_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "archive_minute_id",
    "attachment_name"
  ];
  public function minute_attachments(){
    return $this->belongsTo(ArchiveMinute::class);
  }
}
