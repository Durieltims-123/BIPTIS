<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcessDocuments extends Model
{
    protected $guarded = [];
    // Table Name
    protected $table = 'process_documents';
    // Primary Key
    public $primaryKey = 'id';
    // TimeStamps
    public $timestamps = true;

    public function DocumentType(){
        return $this->belongsTo('App\DocumentType');
    }

    public function ProcurementProcesses(){
        return $this->belongsTo('App\ProcurementProcesses');
    }
}
