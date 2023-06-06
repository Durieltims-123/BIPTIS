<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rfq extends Model
{
	protected $table='rfqs';
	protected $primaryKey = 'rfq_id';
	protected $fillable=[
		'contractor_id',
		'date_released',
		'date_received',
		'time_received',
		'proposed_bid',
		'bid_in_words',
		'initial_bid_as_evaluated',
		'bid_as_evaluated',
		'discount',
		'amount_of_discount',
		'discount_type',
		'discount_source'
	];
}
