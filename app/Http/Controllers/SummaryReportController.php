<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SummaryReportController extends Controller
{
    function generateSummaryReport(){
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view("admin.generate_summary_report", ['links' => $links, 'user_privilege' => $user_privilege]);
    }

    function getReportbyMonthYear(Request $request){
        $month = $request->month;
        $year = $request->year;
        $keyword = $year."-".$month."%";

        $project_complete = DB::table('project_plans')
        ->join('procacts','latest_procact_id','=','procacts.procact_id')
        ->join('project_activity_status','procacts.procact_id','project_activity_status.procact_id')
        ->where('status','completed')
        ->where('procacts.proceed_notice','like',$keyword)-> count();

        $project_for_rebid =DB::table('project_logs')
        ->where('log_date','like',$keyword)
        ->where('project_log_type','like','Another SVP%')
        ->orWhere('project_log_type','like','Program for Rebid%')
        ->count();

        $project_for_review = DB::table('project_logs')
        ->where('log_date','like',$keyword)
        ->where('project_log_type','like','Project for Review%')
        ->count();

        $project_onprocess = DB::table('project_plans')
        ->join('procacts','latest_procact_id','=','procacts.procact_id')
        ->join('project_activity_status','procacts.procact_id','project_activity_status.procact_id')
        ->select('latest_procact_id')
        ->where([['procacts.bid_evaluation','<=',$year.'-'.$month.'-31'],['procacts.proceed_notice',null]])
        ->orWhere([['status','completed'],['procacts.bid_evaluation','<=',$year.'-'.$month.'-31'],['procacts.proceed_notice','>=',$year.'-'.$month.'-01']])
        ->count();

        $project_reverted = DB::table('project_logs')
        ->where('log_date','like',$keyword)
        ->where('project_log_type','like','%reverted')
        ->count();

        $columns = ["Complete","Rebid","Review","On Process","Reverted"];
        $data = [$project_complete,$project_for_rebid,$project_for_review,$project_onprocess,$project_reverted];

        return compact('columns','data');
    }
}
