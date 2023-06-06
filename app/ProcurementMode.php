<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcurementMode extends Model
{
    protected $guarded = [];
    // Table Name
    protected $table = 'procurement_modes';
    // Primary Key
    public $primaryKey = 'mode_id';
    // TimeStamps
    public $timestamps = true;

    public function ProjectPlans(){
        return $this->hasMany('App\ProjectPlans');
    }
}
