<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    protected $guarded = [];
    // Table Name
    protected $table = 'funds';
    // Primary Key
    public $primaryKey = 'fund_id';
    // TimeStamps
    public $timestamps = true;

    protected $fillable=[
        "fund_id",
        "source",
        "fund_category_id",
        "status"
    ];

    public function ProjectPlans(){
        return $this->hasMany('App\ProjectPlans');
    }
}
