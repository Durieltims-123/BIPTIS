<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchiveContractAttachments extends Model
{
  protected $table='archive_contract_attachments';
  protected $primaryKey = 'id';
  protected $fillable=[
    "contract_id",
    "attachment_name"
  ];
}
