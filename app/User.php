<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use App\{UserLink,UserRoles,Roles};

class User extends \TCG\Voyager\Models\User
{
  use Notifiable;

  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'name',
    'email',
    'avatar',
  ];

  /**
  * The attributes that should be hidden for arrays.
  *
  * @var array
  */
  protected $hidden = [
    'password', 'remember_token',
  ];

  /**
  * The attributes that should be cast to native types.
  *
  * @var array
  */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public function Sender(){
    return $this->hasMany('App\ProjectDocument');
  }

  public function Receiver(){
    return $this->hasMany('App\ProjectDocument');
  }

  public function abstract_updater(){
    return $this->hasMany('App\ArchiveAbstract');
  }

  public function abstract_deleter(){
    return $this->hasMany('App\ArchiveAbstract');
  }

  public function user_links(){
    return $this->hasMany(UserLink::class);
  }

  public function user_roles(){
    return $this->hasMany(UserRoles::class);
  }

}
