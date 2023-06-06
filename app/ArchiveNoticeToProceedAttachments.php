<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchiveNoticeToProceedAttachments extends Model
{
  protected $table='archive_notice_to_proceed_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "ntp_id",
    "attachment_name"
  ];
}
