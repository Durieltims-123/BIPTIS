<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequestForExtension extends Model
{
	protected $table='request_for_extension';
	protected $primaryKey = 'request_id';
	protected $fillable=[
		"request_date_generated",
		"request_date",
		"governor_id",
		"request_reason",
		"request_remarks",
		"with_attachment",
		"opening_dates"
	];

	public function bids(): HasMany
	{
		return $this->hasMany(RequestForExtensionBids::class, 'request_id')->latest();
	}
}
