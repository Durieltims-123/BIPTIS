<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ArchiveNoticeOfCancellation;

class ArchiveNoticeOfCancellationAttachments extends Model
{
  protected $table='archive_notice_of_cancellation_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "archive_notice_of_cancellation_id",
    "attachment_name",
    "created_at",
    "updated_at"
  ];
  public function noc_attachments(){
    return $this->belongsTo(ArchiveNoticeOfCancellation::class);
  }
}
