<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\{User,Roles};
class UserRoles extends Model
{
	protected $table='user_roles';
	protected $primaryKey = 'id';
	protected $fillable=[
		'user_id',
		'role_id'
	];

	// public function user_role(){
 	//  return $this->belongsTo(User::class);
  // }

	public function role(){
	 return $this->belongsTo(Roles::class);
	}
}
