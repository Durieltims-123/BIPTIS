<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\ArchiveMeetingAttendanceAttachments;
class ArchiveMeetingAttendance extends Model
{
	protected $table='archive_attendance';
	protected $primaryKey = 'id';
	protected $fillable=[
		"meeting_date",
		"updated_by",
		"deleted_by",
		"deleted",
		"deleted_at",
	];

	public function attendance_updater(){
		return $this->belongsTo(User::class);
	}

	public function attendance_deleter(){
		return $this->belongsTo(User::class);
	}

	public function attendance_attachments(){
		return $this->hasMany(ArchiveMeetingAttendanceAttachments::class);
	}
}
