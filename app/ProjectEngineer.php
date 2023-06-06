<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectEngineer extends Model
{
  protected $guarded = [];
  // Table Name
  protected $table = 'project_engineers';
  // Primary Key
  public $primaryKey = 'id';
  // TimeStamps
  public $timestamps = true;

  protected $fillable = [
    'name',
  ];
}
