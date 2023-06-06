<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LCEEvaluation extends Model
{
    protected $table='lce_evaluation';
    protected $primaryKey = 'id';
    protected $fillable=[
        "project_bid",
        "contractor_id",
        "procact_id",
        "lce_evaluation_date",
        "governor_id",
        "lce_evaluation_status",
        "lce_evaluation_reason",
        "lce_contractor_date_received",
        "resolution_id",
        "lce_evaluation_remarks",
    ];

}
