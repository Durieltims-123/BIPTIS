<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ArchiveOrderAttachments;
class Order extends Model
{
	protected $table='order_request';
	protected $primaryKey = 'order_id';
	protected $fillable=[
		"order_date_generated",
		"order_number",
		"request_id",
		"order_remarks",
		"order_with_attachment"
	];

	public function order_attachments(){
			return $this->hasMany(ArchiveOrderAttachments::class);
	}
}
