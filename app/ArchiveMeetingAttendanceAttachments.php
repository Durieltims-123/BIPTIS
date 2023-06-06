<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ArchiveMeetingAttendance;
class ArchiveMeetingAttendanceAttachments extends Model
{
	protected $table='archive_attendance_attachments';
	protected $primaryKey = 'id';
	protected $fillable=[
		"archive_attendance_id",
		"attachment_name"
	];
	public function attendance_attachments(){
		return $this->belongsTo(ArchiveMeetingAttendance::class);
	}
}
