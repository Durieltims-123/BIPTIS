<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLink extends Model
{
    protected $table='user_links';
    protected $primaryKey = 'id';
    protected $fillable=[
        "user_id",
        "link_privilege_id"
    ];

}
