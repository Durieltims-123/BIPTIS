<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchiveNoticeAttachments extends Model
{
  protected $table='archive_notice_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "project_bidder_notice_id",
    "attachment_name"
  ];
}
