<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\ArchiveAbstractAttachments;

class ArchiveAbstract extends Model
{
  protected $table='archive_abstracts';
  protected $primaryKey = 'id';
  protected $fillable=[
    "date_opened",
    "updated_by",
    "deleted_by",
    "deleted",
    "deleted_at",
  ];

  public function abstract_updater(){
      return $this->belongsTo(User::class);
  }

  public function abstract_deleter(){
      return $this->belongsTo(User::class);
  }

  public function abstract_attachments(){
      return $this->hasMany(ArchiveAbstractAttachments::class);
  }
}
