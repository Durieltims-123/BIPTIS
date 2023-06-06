<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contractors extends Model
{
  protected $guarded = [];
  // Table Name
  protected $table = 'contractors';
  // Primary Key
  public $primaryKey = 'contractor_id';
  // TimeStamps
  public $timestamps = true;

  protected $fillable=[
    'business_name',
    'owner',
    'position',
    'address',
    'contact_number',
    'email',
    'status',
  ];

  public function ProjectDocument(){
    return $this->hasMany('App\ProjectDocument');
  }
}
