<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcurementProcesses extends Model
{
    protected $guarded = [];
    // Table Name
    protected $table = 'procurement_processes';
    // Primary Key
    public $primaryKey = 'id';
    // TimeStamps
    public $timestamps = true;

    public function ProcessDocuments(){
        return $this->hasMany('App\ProcessDocuments');
    }

    public function ProjectDocument(){
        return $this->hasMany('App\ProjectDocument');
    }
}
