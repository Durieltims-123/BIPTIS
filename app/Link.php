<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\LinkPrivilege;
class Link extends Model
{
	protected $table='links';
	protected $primaryKey = 'id';
	protected $fillable=[
		"link_order",
		"link_route",
		"link_name",
		"link_type",
		"parent_name",
		"link_icon"
	];

	public function getLinkPrivileges(){
		return $this->hasMany(LinkPrivilege::class);
	}
}
