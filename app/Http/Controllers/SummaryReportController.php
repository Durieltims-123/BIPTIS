<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SummaryReportController extends Controller
{
    function generateSummaryReport()
    {
        $links = getUserLinks();
        $user_privilege = getUserPrivilege();

        return view("admin.generate_summary_report", ['links' => $links, 'user_privilege' => $user_privilege]);
    }

    function getReportbyMonthYear(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $keyword = $year . "-" . $month . "%";

        $project_complete = DB::table('project_plans')
            ->join('procacts', 'latest_procact_id', '=', 'procacts.procact_id')
            ->join('project_activity_status', 'procacts.procact_id', 'project_activity_status.procact_id')
            ->where('status', 'completed')
            ->where('procacts.proceed_notice', 'like', $keyword)->count();

        $project_for_rebid = DB::table('project_logs')
            ->where('log_date', 'like', $keyword)
            ->where('project_log_type', 'like', 'Another SVP%')
            ->orWhere('project_log_type', 'like', 'Program for Rebid%')
            ->count();

        $project_for_review = DB::table('project_logs')
            ->where('log_date', 'like', $keyword)
            ->where('project_log_type', 'like', 'Project for Review%')
            ->count();

        $project_onprocess = DB::table('project_plans')
            ->join('procacts', 'latest_procact_id', '=', 'procacts.procact_id')
            ->join('project_activity_status', 'procacts.procact_id', 'project_activity_status.procact_id')
            ->select('latest_procact_id')
            ->where([['procacts.bid_evaluation', '<=', $year . '-' . $month . '-31'], ['procacts.proceed_notice', null]])
            ->orWhere([['status', 'completed'], ['procacts.bid_evaluation', '<=', $year . '-' . $month . '-31'], ['procacts.proceed_notice', '>=', $year . '-' . $month . '-01']])
            ->count();

        $project_reverted = DB::table('project_logs')
            ->where('log_date', 'like', $keyword)
            ->where('project_log_type', 'like', '%reverted')
            ->count();

        $columns = ["Complete", "Rebid", "Review", "On Process", "Reverted"];
        $data = [$project_complete, $project_for_rebid, $project_for_review, $project_onprocess, $project_reverted];

        return compact('columns', 'data');
    }

    function getUnprocuredProject(Request $request)
    {
        $start_year = $request->start_year;
        $end_year = $request->end_year;
        $project_count = array();
        $project_year = array();

        for ($start_year; $start_year <= $end_year; $start_year++) {
            $dataTable = DB::table('project_timelines')
                ->join('bid_doc_projects', 'project_timelines.procact_id', 'bid_doc_projects.procact_id')
                ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
                ->join('project_plans', 'bid_doc_projects.procact_id', 'project_plans.latest_procact_id')
                ->join('project_bidders', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
                ->where('project_year', $start_year)
                ->where(function (Builder $query) {
                    $query->where('timeline_status', 'pending')
                        ->orWhere('bid_status', '!=', 'active')
                        ->orWhere('bid_status', '!=', 'responsive')
                        ->orWhere('bid_docs.date_received', 'NULL');
                })
                ->get()->count();

            array_push($project_year, $start_year);
            array_push($project_count, $dataTable);
        }

        return compact('project_year', 'project_count');

    }

    function getRegSuppProject(Request $request)
    {
        $start_year = $request->start_year;
        $end_year = $request->end_year;
        $project_reg = array();
        $project_year = array();
        $project_supp = array();

        for ($start_year; $start_year <= $end_year; $start_year++) {
            $dataTable = DB::table('project_plans')
                ->where([['project_year', $start_year], ['project_type', 'regular']])
                ->get()->count();
            array_push($project_reg, $dataTable);
            $dataTable = DB::table('project_plans')
                ->where([['project_year', $start_year], ['project_type', 'supplemental']])
                ->get()->count();
            array_push($project_supp, $dataTable);

            array_push($project_year, $start_year);
        }

        return compact('project_year', 'project_reg', 'project_supp');

    }

    function getModeProject(Request $request)
    {
        $start_year = $request->start_year;
        $end_year = $request->end_year;
        $project_SVP = array();
        $project_year = array();
        $project_bidding = array();
        $project_procurement = array();

        for ($start_year; $start_year <= $end_year; $start_year++) {
            $dataTable = DB::table('project_plans')
                ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
                ->where([['project_year', $start_year], ['mode', 'SVP']])
                ->get()->count();
            array_push($project_SVP, $dataTable);
            $dataTable = DB::table('project_plans')
                ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
                ->where([['project_year', $start_year], ['mode', 'Bidding']])
                ->get()->count();
            array_push($project_bidding, $dataTable);
            $dataTable = DB::table('project_plans')
                ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
                ->where([['project_year', $start_year], ['mode', 'Negotiated']])
                ->get()->count();
            array_push($project_procurement, $dataTable);

            array_push($project_year, $start_year);
        }
        return compact('project_year', 'project_SVP', 'project_bidding', 'project_procurement');
    }

    function getStatusProjMun(Request $request)
    {
        $year = $request->year;
        $status = $request->status;
        $municipal = $request->municipal;
        $type_count = array();

        $projtype = DB::table('projtypes')
            ->select('projtype_id', 'type')
            ->get();

        if ($status == 'complete') {
            foreach ($projtype as $type) {
                $count_complete = DB::table('project_plans')
                    ->join('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')
                    ->join('project_bidders', 'project_plans.project_bid_id', 'project_bidders.project_bid')
                    ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
                    ->join('projtypes', 'project_plans.projtype_id', 'projtypes.projtype_id')
                    ->join('project_timelines', 'project_plans.plan_id', 'project_timelines.plan_id')
                    ->where([['project_year', $year], ['municipality_display', $municipal]])
                    ->where([['project_plans.projtype_id', $type->projtype_id], ['project_plans.status', 'completed']])
                    ->count();
                if($count_complete != 0){
                    $type_count[$type->type] = $count_complete;
                }
                

            }
        } else if ($status == 'ongoing') {
            foreach ($projtype as $type) {
                $count_ongoing = DB::table('project_plans')
                    ->join('procacts', 'project_plans.latest_procact_id', '=', 'procacts.procact_id')
                    ->join('project_bidders', 'project_plans.project_bid_id', '=', 'project_bidders.project_bid')
                    ->join('municipalities', 'project_plans.municipality_id', '=', 'municipalities.municipality_id')
                    ->join('projtypes', 'project_plans.projtype_id', '=', 'projtypes.projtype_id')
                    ->join('project_timelines', 'project_plans.plan_id', '=', 'project_timelines.plan_id')
                    ->where(function ($query) use ($year, $municipal, $type) {
                        $query->where([['project_year', '=', $year], ['municipality_display', '=', $municipal], ['project_plans.projtype_id', '=', $type->projtype_id]]);
                    })
                    ->where('timeline_status', '=', 'set')
                    ->whereRaw('DATE(procacts.open_bid) > 2022-12-31')
                    ->orWhere(function ($query) use ($year, $municipal, $type) {
                        $query->where([['project_year', '=', $year], ['municipality_display', '=', $municipal], ['project_plans.projtype_id', '=', $type->projtype_id]]);
                    })
                    ->where(function ($query) {
                        $query->where('proceed_notice', '=', 'NULL')
                            ->where('bid_status', '=', 'active')
                            ->orWhere('bid_status', '=', 'responsive');
                    })
                    ->count();

                    if($count_ongoing != 0){
                        $type_count[$type->type] = $count_ongoing;
                    }

            }
        } else if ($status == 'unprocured') {
            foreach ($projtype as $type) {
                $count_unprocured = DB::table('project_plans')
                    ->join('procacts', 'project_plans.latest_procact_id', '=', 'procacts.procact_id')
                    ->join('project_bidders', 'project_plans.project_bid_id', '=', 'project_bidders.project_bid')
                    ->join('municipalities', 'project_plans.municipality_id', '=', 'municipalities.municipality_id')
                    ->join('projtypes', 'project_plans.projtype_id', '=', 'projtypes.projtype_id')
                    ->join('project_timelines', 'project_plans.plan_id', '=', 'project_timelines.plan_id')
                    ->where(function ($query) use ($year, $municipal, $type) {
                        $query->where([['project_year', '=', $year], ['municipality_display', '=', $municipal], ['project_plans.projtype_id', '=', $type->projtype_id]]);
                    })
                    ->where('timeline_status', '=', 'pending')
                    ->whereRaw('DATE(procacts.open_bid) < 2022-12-31')
                    ->orWhere(function ($query) use ($year, $municipal, $type) {
                        $query->where([['project_year', '=', $year], ['municipality_display', '=', $municipal], ['project_plans.projtype_id', '=', $type->projtype_id]]);
                    })
                    ->where(function ($query) {
                        $query
                            ->where('bid_status', '!=', 'active')
                            ->orWhere('bid_status', '!=', 'responsive');
                    })
                    ->count();

                if ($count_unprocured != 0) {
                    $type_count[$type->type] = $count_unprocured;
                }

            }
        }

        return compact('type_count');
    }

}