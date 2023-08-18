<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Session;

class ProgressReportController extends Controller
{
    function generateProgressReport()
    {
        $links = getUserLinks();
        $user_privilege = getUserPrivilege();

        return view("admin.generate_progress_report", ['links' => $links, 'user_privilege' => $user_privilege]);
    }

    function getTableData(Request $request)
    {
        $year = $request->year;
        $tabledata = DB::table('project_plans')
            ->join('project_activity_status', 'project_plans.plan_id', '=', 'project_activity_status.plan_id')
            ->join('procacts', 'project_plans.latest_procact_id', '=', 'procacts.procact_id')
            ->select(
                '*',
                'project_activity_status.post_qual as post_qual_process',
                'project_activity_status.award_notice as award_notice_process',
                'project_activity_status.contract_signing as contract_signing_process',
                'project_activity_status.proceed_notice as proceed_notice_process'
            )
            ->where([['status', '=', 'onprocess'], ['project_year', $year], ['project_activity_status.bid_evaluation', 'finished']])
            ->get()->toarray();


        //to identify the current processing stage of the project
        array_map(function ($item) {
            if ($item->post_qual_process != 'finished') {
                $item->process = 'Post Qualification';
            } elseif ($item->award_notice_process != 'finished') {
                $item->process = 'Notice of Award';
            } elseif ($item->contract_signing_process != 'finished') {
                $item->process = 'Signing of Contract';
            } elseif ($item->proceed_notice_process != 'finished') {
                $item->process = 'Notice to Proceed';
            }
            $current_day = date_create(date("Y-m-d"));
            $opening_day = date_create(date("Y-m-d", strtotime($item->open_bid)));
            $diff = date_diff($current_day, $opening_day)->format('%a');
            $item->progress = $diff;
        }, $tabledata);


        return back()->withInput()->with(compact('tabledata'));
    }
}
