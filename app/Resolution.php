<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resolution extends Model
{
  protected $table='resolutions';
  protected $primaryKey = 'resolution_id';
  protected $fillable=[
    'resolution_date',
    'resolution_number',
    'resolution_id',
    'type',
    'succeeding_process',
    'next_opening_date',
    'with_attachment',
    'governor_id',
    'resolution_remarks'
  ];
}
