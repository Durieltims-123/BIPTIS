<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ArchiveAbstract;

class ArchiveAbstractAttachments extends Model
{
  protected $table='archive_abstract_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "archive_abstract_id",
    "attachment_name"
  ];
  public function abstract_attachments(){
    return $this->belongsTo(ArchiveAbstract::class);
  }
}
