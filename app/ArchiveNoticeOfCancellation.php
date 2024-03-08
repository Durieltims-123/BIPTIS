<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\ArchiveAbstractAttachments;

class ArchiveNoticeOfCancellation extends Model
{
  protected $table = 'archive_notice_of_cancellation';
  protected $primaryKey = 'id';
  protected $fillable = [
    "date",
    "reason",
    "created_at",
    "updated_at",
  ];
}
