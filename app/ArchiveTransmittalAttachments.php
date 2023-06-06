<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\ArchiveTransmittal;

class ArchiveTransmittalAttachments extends Model
{
	protected $table='archive_transmittal_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "archive_transmittal_id",
    "attachment_name"
  ];
  public function transmittal_attachments(){
    return $this->belongsTo(ArchiveTransmittal::class);
  }
}
