<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ArchiveAbstract;

class ArchiveNoticeOfCancellationProjects extends Model
{
  protected $table='archive_abstract_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "procact_id",
    "notice_of_cancellation_id"
  ];
  public function noc_projects(){
    return $this->belongsTo(ArchiveNoticeOfCancellation::class);
  }
}
