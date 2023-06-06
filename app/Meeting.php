<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ArchiveNoticeOfMeetingAttachments;

class Meeting extends Model
{
  protected $table='meeting';
  protected $primaryKey = 'meeting_id';
  protected $fillable=[
    "meeting_date_created",
    "meeting_date",
    "meeting_time",
    "meeting_room_id",
    "bac_id",
    "with_attachment",
    "created_at",
    "updated_at"
  ];

  public function notice_of_meeting_attachments(){
    return $this->hasMany(ArchiveNoticeOfMeetingAttachments::class);
  }
}
