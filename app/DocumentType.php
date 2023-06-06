<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $guarded = [];
    // Table Name
    protected $table = 'document_types';
    // Primary Key
    public $primaryKey = 'id';
    // TimeStamps
    public $timestamps = true;

    public function ProjectDocument(){
        return $this->hasMany('App\ProjectDocument');
    }

    public function ProcessDocuments(){
        return $this->hasMany('App\ProcessDocuments');
    }
}
