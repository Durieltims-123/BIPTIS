<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ArchiveCertificateOfPosting;

class ArchiveCertificateOfPostingAttachments extends Model
{
  protected $table='archive_certificate_of_posting_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "archive_certificate_of_posting_id",
    "attachment_name"
  ];
  public function certificate_of_posting_attachments(){
    return $this->belongsTo(ArchiveCertificateOfPosting::class);
  }
}
