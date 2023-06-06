<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectPlans extends Model
{
    protected $guarded = [];
    // Table Name
    protected $table = 'project_plans';
    // Primary Key
    public $primaryKey = 'plan_id';
    // TimeStamps
    public $timestamps = true;

    protected $fillable=[

        'app_group_no',
        'project_no',
        'account_code',
        'project_title',
        'project_year',
        'year_funded',
        'project_type',
        'date_added',
        'sector_id',
        'municipality_id',
        'barangay_id',
        'projtype_id',
        'mode_id',
        'fund_id',
        'account_id',
        'abc',
        'project_cost',
        'abc_post_date',
        'sub_open_date',
        'award_notice_date',
        'contract_signing_date',
        'status',
        're_bid_count',
        'pow_ready',
        'date_pow_added',
        'pow_date_edited',
        'governor_id',
        'project_bid_id',
        'current_cluster',
        'latest_procact_id',
        'duration',
        'remarks',
        'is_old',
        'parent_id',
        'special_case_1',
    ];

    public function ProjectDocument(){
        return $this->hasMany('App\ProjectDocument');
    }

    public function Municipality(){
        return $this->belongsTo('App\Municipality');
    }

    public function Barangay(){
        return $this->belongsTo('App\Barangay');
    }

    public function ProcurementMode(){
        return $this->belongsTo('App\ProcurementMode');
    }

    public function Fund(){
        return $this->belongsTo('App\Fund');
    }
}
