<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestForExtensionBids extends Model
{
    protected $table='request_for_extension_bids';
    protected $primaryKey = 'request_bid_id';
    protected $fillable=[
		"request_id",
		"project_bid"
    ];
}
