<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchiveTerminationAttachments extends Model
{
	protected $table='archive_termination_attachments';
	protected $primaryKey = 'termination_attachment_id';
	protected $fillable=[
		'termination_id',
		'attachment_name'
	];
}
