<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\ArchiveMinuteAttachments;

class ArchiveMinute extends Model
{
  protected $table='archive_minutes';
  protected $primaryKey = 'id';
  protected $fillable=[
    "date_opened",
    "updated_by",
    "deleted_by",
    "deleted",
    "deleted_at",
  ];

  public function minute_updater(){
      return $this->belongsTo(User::class);
  }

  public function minute_deleter(){
      return $this->belongsTo(User::class);
  }

  public function minute_attachments(){
      return $this->hasMany(ArchiveMinuteAttachments::class);
  }
}
