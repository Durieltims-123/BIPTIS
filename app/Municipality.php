<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    protected $guarded = [];
    // Table Name
    protected $table = 'municipalities';
    // Primary Key
    public $primaryKey = 'municipality_id';
    // TimeStamps
    public $timestamps = true;

    public function ProjectPlans(){
        return $this->hasMany('App\ProjectPlans');
    }
}
