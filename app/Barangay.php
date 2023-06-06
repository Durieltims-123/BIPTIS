<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    protected $guarded = [];
    // Table Name
    protected $table = 'barangays';
    // Primary Key
    public $primaryKey = 'barangay_id';
    // TimeStamps
    public $timestamps = true;

    public function ProjectPlans(){
        return $this->hasMany('App\ProjectPlans');
    }
}
