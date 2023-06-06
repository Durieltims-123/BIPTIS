<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\ArchiveTransmittalAttachments;

class ArchiveTransmittal extends Model
{
	protected $table='archive_transmittal';
	protected $primaryKey = 'id';
	protected $fillable=[
		"plan_id",
		"date_received_by_coa",
		"transmittal_remarks",
		"updated_by",
		"deleted_by",
		"deleted",
		"deleted_at"
	];

	public function transmittal_updater(){
		return $this->belongsTo(User::class);
	}

	public function transmittal_deleter(){
		return $this->belongsTo(User::class);
	}

	public function transmittal_attachments(){
		return $this->hasMany(ArchiveTransmittalAttachments::class);
	}
}
