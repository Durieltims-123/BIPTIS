<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchiveResolutionAttachments extends Model
{
  protected $table='archive_resolution_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "resolution_id",
    "attachment_name"
  ];
}
