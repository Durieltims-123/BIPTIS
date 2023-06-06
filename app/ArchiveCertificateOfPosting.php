<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\ArchiveCertificateOfPostingAttachments;

class ArchiveCertificateOfPosting extends Model
{
  protected $table='archive_certificate_of_posting';
  protected $primaryKey = 'id';
  protected $fillable=[
    "date_opened",
    "updated_by",
    "deleted_by",
    "deleted",
    "deleted_at",
  ];

  public function certificate_of_posting_updater(){
      return $this->belongsTo(User::class);
  }

  public function certificate_of_posting_deleter(){
      return $this->belongsTo(User::class);
  }

  public function certificate_of_posting_attachments(){
      return $this->hasMany(ArchiveCertificateOfPostingAttachments::class);
  }
}
