<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchiveNoticeOfAwardAttachments extends Model
{
  protected $table='archive_notice_of_awards_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "notice_award_id",
    "attachment_name"
  ];
}
