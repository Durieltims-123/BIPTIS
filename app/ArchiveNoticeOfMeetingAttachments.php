<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Meeting;

class ArchiveNoticeOfMeetingAttachments extends Model
{
  protected $table='archive_notice_of_meeting_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "meeting_id",
    "attachment_name"
  ];
  public function arvhive_notice_of_meeting_attachments(){
    return $this->belongsTo(Meeting::class);
  }
}
