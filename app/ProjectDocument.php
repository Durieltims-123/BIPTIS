<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectDocument extends Model
{
    protected $guarded = [];
    // Table Name
    protected $table = 'project_documents';
    // Primary Key
    public $primaryKey = 'id';
    // TimeStamps
    public $timestamps = true;

    public function DocumentType(){
        return $this->belongsTo('App\DocumentType');
    }

    public function Contractors(){
        return $this->belongsTo('App\Contractors');
    }

    public function ProjectPlans(){
        return $this->belongsTo('App\ProjectPlans');
    }

    public function Sender(){
        return $this->belongsTo('App\User');
    }

    public function Receiver(){
        return $this->belongsTo('App\User');
    }

    public function ProcurementProcesses(){
        return $this->belongsTo('ProcurementProcesses');
    }
}
