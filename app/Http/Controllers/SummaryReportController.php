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
        $switcher = $request->switcher;

        $keyword = $year . "-" . $month . "%";

        //completed projects
        $project_complete = DB::table('project_plans')
            ->join('procacts', 'latest_procact_id', 'procacts.procact_id')
            ->where([['status', 'completed'], ['project_plans.is_old', '<>', true], ['procacts.proceed_notice', 'like', $keyword]])
            ->get();

        // projects for rebid
        $project_for_rebid = DB::table('project_logs')
            ->where([['log_date', 'like', $keyword], ['project_log_type', 'like', 'Project for Rebid%']])
            ->get();

        // $project_for_rebid = DB::table('project_plans')
        //     ->where([['bid_submission_start', 'like', $keyword], ['project_activity_status.main_status', 'rebid']])
        //     ->join('project_activity_status', 'project_activity_status.plan_id', 'project_plans.plan_id')
        //     ->join('procacts', 'procacts.procact_id', 'project_activity_status.procact_id')
        //     ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        //     ->get();

        // projects for review
        $project_for_review = DB::table('project_logs')
            ->where([['log_date', 'like', $keyword], ['project_log_type', 'like', 'Project for Review%']])
            ->get();

        // $project_for_review = DB::table('project_plans')
        //     ->where([['bid_submission_start', 'like', $keyword], ['project_activity_status.main_status', 'review']])
        //     ->join('project_activity_status', 'project_activity_status.plan_id', 'project_plans.plan_id')
        //     ->join('procacts', 'procacts.procact_id', 'project_activity_status.procact_id')
        //     ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        //     ->get();

        //on process at that time
        $project_onprocess = DB::table('project_plans')
            ->join('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')
            ->orWhere([['procacts.advertisement', 'like', $keyword], ['sub_open_date', '<=', $year . "-" . $month . "-31"]])
            ->orWhere([['procacts.pre_bid', 'like', $keyword], ['sub_open_date', '<=', $year . "-" . $month . "-31"]])
            ->orWhere([['procacts.open_bid', 'like', $keyword], ['sub_open_date', '<=', $year . "-" . $month . "-31"]])
            ->orWhere([['procacts.bid_evaluation', 'like', $keyword], ['sub_open_date', '<=', $year . "-" . $month . "-31"]])
            ->orWhere([['procacts.post_qual', 'like', $keyword], ['sub_open_date', '<=', $year . "-" . $month . "-31"]])
            ->orWhere([['procacts.award_notice', 'like', $keyword], ['sub_open_date', '<=', $year . "-" . $month . "-31"]])
            ->orWhere([['procacts.contract_signing', 'like', $keyword], ['sub_open_date', '<=', $year . "-" . $month . "-31"]])
            ->orWhere([['procacts.proceed_notice', 'like', $keyword], ['sub_open_date', '<=', $year . "-" . $month . "-31"]])
            ->distinct()
            ->get();

        // reverted
        $project_reverted = DB::table('project_logs')
            ->where([['log_date', 'like', $keyword], ['project_log_type', 'like', '%reverted']])
            ->get();

        if ($switcher == 0) {
            $columns = ["Complete", "Rebid", "Review", "On Process", "Reverted"];
            $data = [$project_complete->count(), $project_for_rebid->count(), $project_for_review->count(), $project_onprocess->count(), $project_reverted->count()];

            return compact('columns', 'data');
        } else {
            $project_complete = $project_complete->toArray();
            $project_for_rebid = $project_for_rebid->toArray();
            $project_for_review = $project_for_review->toArray();
            $project_onprocess = $project_onprocess->toArray();
            $project_reverted = $project_reverted->toArray();

            array_map(function ($item) {
                $item->current_status = 'Complete';
            }, $project_complete);

            array_map(function ($item) {
                $item->current_status = 'Rebid';
            }, $project_for_rebid);

            array_map(function ($item) {
                $item->current_status = 'Review';
            }, $project_for_review);

            array_map(function ($item) {
                $item->current_status = 'On Process';
            }, $project_onprocess);

            array_map(function ($item) {
                $item->current_status = 'Reverted';
            }, $project_reverted);

            $datatable = array_merge($project_complete, $project_for_rebid, $project_for_review, $project_onprocess, $project_reverted);
            // dd($datatable);

            return $datatable;
        }

        // $columns = ["Complete", "Rebid", "Review", "On Process", "Reverted"];
        // $data = [$project_complete, $project_for_rebid, $project_for_review, $project_onprocess, $project_reverted];

        // return compact('columns', 'data');
    }

    function getUnprocuredProject(Request $request)
    {
        $start_year = (int) $request->start_year;
        $end_year = (int) $request->end_year;
        $current_year = date("Y");
        $project_year = array();
        $project_count = array();
        $current_month = date("m");
        $table_format = $request->table_format;

        // $dataTable = DB::table('project_plans')
        //     ->leftJoin('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')
        //     ->leftJoin('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        //     ->leftJoin('bid_doc_projects', 'project_timelines.procact_id', 'bid_doc_projects.procact_id')
        //     ->leftJoin('project_bidders', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
        //     ->leftJoin('municipalities', 'project_plans.municipality_id', '=', 'municipalities.municipality_id')
        //     ->leftJoin('projtypes', 'project_plans.projtype_id', '=', 'projtypes.projtype_id')
        //     ->where([
        //         ['project_plans.project_year', '>=', $start_year],
        //         ['project_plans.project_year', '<=', 2020],
        //         ['procacts.advertisement', null],
        //         ['project_plans.project_bid_id', null],
        //     ])->get();



        $dataTable = DB::table('project_plans')
            ->leftJoin('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')
            ->leftJoin('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
            ->leftJoin('bid_doc_projects', 'project_timelines.procact_id', 'bid_doc_projects.procact_id')
            ->leftJoin('project_bidders', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
            ->leftJoin('municipalities', 'project_plans.municipality_id', '=', 'municipalities.municipality_id')
            ->leftJoin('projtypes', 'project_plans.projtype_id', '=', 'projtypes.projtype_id')
            ->where([
                ['project_timelines.timeline_status', 'pending'],
                ['project_plans.status', 'pending'],
                ['project_plans.project_year', '>=', $start_year],
                ['project_plans.project_year', '<=', ($end_year)],
                ['project_plans.is_old', '<>', true],
                ['procacts.advertisement', null],
                ['project_plans.project_bid_id', null],
                ['project_plans.mode_id', 1],
            ])
            ->orWhere([
                ['project_timelines.timeline_status', 'set'],
                ['project_plans.status', 'onprocess'],
                ['project_plans.project_year', '>=', $start_year],
                ['project_plans.project_year', '<=', ($end_year)],
                ['project_plans.is_old', '<>', true],
                ['procacts.advertisement', null],
                ['bid_status', '!=', 'active'],
                ['procacts.proceed_notice', '!=', null],
                ['project_plans.mode_id', 1],
            ])
            ->orWhere([
                ['project_timelines.timeline_status', 'set'],
                ['project_plans.status', 'onprocess'],
                ['project_plans.project_year', '>=', $start_year],
                ['project_plans.project_year', '<=', ($end_year)],
                ['project_plans.is_old', '<>', true],
                ['procacts.advertisement', null],
                ['bid_status', '!=', 'responsive'],
                ['procacts.proceed_notice', '!=', null],
                ['project_plans.mode_id', 1],
            ])
            ->orWhere([
                // ['project_timelines.timeline_status', 'set'],
                // ['project_plans.status', 'onprocess'],
                ['project_plans.project_year', '>=', $start_year],
                ['project_plans.project_year', '<=', ($end_year)],
                ['project_plans.is_old', '<>', true],
                ['project_plans.sub_open_date', '!=', null],
                ['procacts.proceed_notice', null],
                ['project_bidders.rfq_project_id', '=', null],
                ['project_bidders.bid_doc_project_id', null],
            ]);



        if ($table_format == "true") {

            $dataTable =  $dataTable->get();

            return compact('dataTable');
        } else {
            $dataTable = $dataTable->select('project_plans.project_year', DB::raw('count(*) as total'))
                ->groupBy('project_plans.project_year')
                ->get()
                ->toarray();
        }

        if ($current_month <= 3) {
            $current_year_query = 0;
        } else {
            $quarter = '';
            if ($current_month <= 6 && $current_month >= 4) {
                $quarter = $current_year . '-04-01';
            } elseif ($current_month <= 9 && $current_month >= 7) {
                $quarter = $current_year . '-07-01';
            } elseif ($current_month <= 12 && $current_month >= 10) {
                $quarter = $current_year . '-10-01';
            }

            $current_year_query = DB::table('project_plans')
                ->leftJoin('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')
                ->leftJoin('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
                ->leftJoin('bid_doc_projects', 'project_timelines.procact_id', 'bid_doc_projects.procact_id')
                ->leftJoin('project_bidders', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
                ->leftJoin('municipalities', 'project_plans.municipality_id', '=', 'municipalities.municipality_id')
                ->leftJoin('projtypes', 'project_plans.projtype_id', '=', 'projtypes.projtype_id')
                ->where([
                    ['project_timelines.timeline_status', 'pending'],
                    ['project_plans.status', 'pending'],
                    ['project_plans.project_year', $current_year],
                    ['project_plans.is_old', '<>', true],
                    ['procacts.advertisement', null],
                    ['project_plans.project_bid_id', null],
                    ['project_plans.abc_post_date', '<', $quarter],
                    ['project_plans.mode_id', 1]
                ])
                ->orWhere([
                    ['project_timelines.timeline_status', 'set'],
                    ['project_plans.status', 'onprocess'],
                    ['project_plans.project_year', $current_year],
                    ['project_plans.is_old', '<>', true],
                    ['procacts.advertisement', null],
                    ['bid_status', '!=', 'active'],
                    ['procacts.proceed_notice', '!=', null],
                    ['project_plans.mode_id', 1]
                ])
                ->orWhere([
                    ['project_timelines.timeline_status', 'set'],
                    ['project_plans.status', 'onprocess'],
                    ['project_plans.project_year', $current_year],
                    ['project_plans.is_old', '<>', true],
                    ['procacts.advertisement', null],
                    ['bid_status', '!=', 'responsive'],
                    ['procacts.proceed_notice', '!=', null],
                    ['project_plans.mode_id', 1]
                ])
                ->orWhere([
                    // ['project_timelines.timeline_status', 'set'],
                    // ['project_plans.status', 'onprocess'],
                    ['project_plans.project_year', $current_year],
                    ['project_plans.is_old', '<>', true],
                    ['project_plans.sub_open_date', '<=', date("Y-m-d")],
                    ['procacts.proceed_notice', null],
                    ['project_bidders.rfq_project_id', '=', null],
                    ['project_bidders.bid_doc_project_id', null]
                ])
                ->count();
        }

        foreach ($dataTable as $item) {
            $project_year[] = $item->project_year;
            if ($item->project_year == $current_year) {
                $project_count[] = $current_year_query;
            } else {
                $project_count[] = $item->total;
            }
        }

        while ($start_year <= $end_year) {
            if (in_array($start_year, $project_year)) {
                $start_year++;
            } else {
                for ($i = 0; $i <= sizeof($project_year) - 1; $i++) {
                    $stopper = $start_year;
                    if ($start_year < $project_year[$i]) {
                        $first_array_year = array_slice($project_year, 0, $i);
                        $second_array_year = array_slice($project_year, $i);
                        $first_array_count = array_slice($project_count, 0, $i);
                        $second_array_count = array_slice($project_count, $i);
                        array_push($first_array_year, $start_year);
                        array_push($first_array_count, 0);
                        $project_year = array_merge($first_array_year, $second_array_year);
                        $project_count = array_merge($first_array_count, $second_array_count);
                        $start_year++;
                    }
                    if ($stopper != $start_year) {
                        break;
                    }
                }
                if ($start_year > end($project_year)) {
                    array_push($project_year, $start_year);
                    array_push($project_count, 0);
                }
                continue;
            }
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
        $project_total = array();
        $project_actual = array();

        for ($start_year; $start_year <= $end_year; $start_year++) {
            $dataTable = DB::table('project_plans')
                ->where([['project_year', $start_year], ['project_plans.is_old', '<>', true]])
                ->get()->count();
            array_push($project_total, $dataTable);
            $dataTable = DB::table('project_plans')
                ->where([['project_year', $start_year], ['status', 'completed'], ['project_plans.is_old', '<>', true]])
                ->get()->count();
            array_push($project_actual, $dataTable);
            $dataTable = DB::table('project_plans')
                ->where([['project_year', $start_year], ['project_type', 'regular'], ['status', 'completed'], ['project_plans.is_old', '<>', true]])
                ->get()->count();
            array_push($project_reg, $dataTable);
            $dataTable = DB::table('project_plans')
                ->where([['project_year', $start_year], ['project_type', 'supplemental'], ['status', 'completed'], ['project_plans.is_old', '<>', true],])
                ->get()->count();
            array_push($project_supp, $dataTable);
            array_push($project_year, $start_year);
        }

        return compact('project_year', 'project_reg', 'project_supp', 'project_total', 'project_actual');
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
                ->where([['project_year', $start_year], ['mode', 'SVP'], ['project_plans.is_old', '<>', true]])
                ->get()->count();
            array_push($project_SVP, $dataTable);
            $dataTable = DB::table('project_plans')
                ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
                ->where([['project_year', $start_year], ['mode', 'Bidding'], ['project_plans.is_old', '<>', true]])
                ->get()->count();
            array_push($project_bidding, $dataTable);
            $dataTable = DB::table('project_plans')
                ->join('procurement_modes', 'project_plans.mode_id', 'procurement_modes.mode_id')
                ->where([['project_year', $start_year], ['mode', 'Negotiated'], ['project_plans.is_old', '<>', true],])
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
        $switcher = $request->switcher;
        $labels = array();
        $type_count = array();

        $projtype = DB::table('projtypes')
            ->select('projtype_id', 'type')
            ->get();

        if ($status == 'complete') {
            if ($municipal == 'all') {
                $dataTable = DB::table('project_plans')
                    ->join('projtypes', 'project_plans.projtype_id', 'projtypes.projtype_id')
                    ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
                    ->where([['project_year', $year], ['project_plans.status', 'completed'], ['project_plans.is_old', '<>', true]]);

                if ($switcher == 0) {
                    $dataTable = $dataTable->select('type', DB::raw('count(*) as total'))->groupBy('type')->get()->toArray();
                    $labels = array_map(function ($item) {
                        return $item->type;
                    }, $dataTable);
                    $type_count = array_map(function ($item) {
                        return $item->total;
                    }, $dataTable);
                } else {
                    $dataTable = $dataTable->get()->toArray();
                    array_map(function ($item) {
                        $item->current_status = 'Complete';
                    }, $dataTable);
                    return $dataTable;
                }
            } else {
                $dataTable = DB::table('project_plans')
                    ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
                    ->join('projtypes', 'project_plans.projtype_id', 'projtypes.projtype_id')
                    ->where([['project_year', $year], ['municipality_display', $municipal], ['project_plans.status', 'completed']]);

                if ($switcher == 0) {
                    $dataTable = $dataTable->select('type', DB::raw('count(*) as total'))->groupBy('type')->get()->toArray();
                    $labels = array_map(function ($item) {
                        return $item->type;
                    }, $dataTable);
                    $type_count = array_map(function ($item) {
                        return $item->total;
                    }, $dataTable);
                } else {
                    $dataTable = $dataTable->get()->toArray();
                    array_map(function ($item) {
                        $item->current_status = 'Complete';
                    }, $dataTable);
                    return $dataTable;
                }
            }
        } else if ($status == 'ongoing') {
            if ($municipal == 'all') {
                $subQuery = DB::table('project_plans')
                    ->leftJoin('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
                    ->leftJoin('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')
                    ->leftJoin('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
                    ->leftJoin('bid_doc_projects', 'procacts.procact_id', 'bid_doc_projects.procact_id')
                    ->leftJoin('project_bidders', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
                    ->leftJoin('projtypes', 'project_plans.projtype_id', '=', 'projtypes.projtype_id')
                    ->where([
                        ['project_timelines.timeline_status', 'pending'],
                        ['project_plans.status', 'pending'],
                        ['project_plans.project_year', $year],
                        ['project_plans.is_old', '<>', true],
                        ['procacts.advertisement', null],
                        ['project_plans.project_bid_id', '!=', null],
                        ['project_plans.sub_open_date', '>=', date("Y-m") . '-01'],
                        ['project_plans.mode_id', 1],

                    ])
                    ->orWhere([
                        ['project_timelines.timeline_status', 'set'],
                        ['project_plans.status', 'onprocess'],
                        ['project_plans.project_year', $year],
                        ['project_plans.is_old', '<>', true],
                        ['project_plans.sub_open_date', '<=', date("Y-m-d")],
                        ['bid_status', 'active'],
                        ['procacts.proceed_notice', null],
                        ['project_plans.mode_id', 1],
                    ])
                    ->orWhere([
                        ['project_timelines.timeline_status', 'set'],
                        ['project_plans.status', 'onprocess'],
                        ['project_plans.project_year', $year],
                        ['project_plans.is_old', '<>', true],
                        ['project_plans.sub_open_date', '<=', date("Y-m-d")],
                        ['bid_status', 'responsive'],
                        ['procacts.proceed_notice', null],
                        ['project_plans.mode_id', 1]
                    ])
                    ->orWhere([
                        ['project_timelines.timeline_status', 'set'],
                        ['project_plans.status', 'onprocess'],
                        ['project_plans.project_year', $year],
                        ['project_plans.is_old', '<>', true],
                        ['project_plans.sub_open_date', '<=', date("Y-m-d")],
                        ['procacts.proceed_notice', null],
                        ['rfq_project_id', '!=', null],
                        ['project_plans.mode_id', '!=', 1]
                    ])
                    ->select('project_plans.plan_id', 'type', 'project_no', 'project_title', 'municipality_display')
                    ->distinct('project_plans.plan_id');

                $dataTable = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))->mergeBindings($subQuery);

                if ($switcher == 0) {
                    $dataTable = $dataTable->select('plan_id', 'type', DB::raw('count(*) as total'))->groupBy('type')->get()->toArray();
                    $labels = array_map(function ($item) {
                        return $item->type;
                    }, $dataTable);
                    $type_count = array_map(function ($item) {
                        return $item->total;
                    }, $dataTable);
                } else {
                    $dataTable = $dataTable->get()->toArray();
                    array_map(function ($item) {
                        $item->current_status = 'Ongoing';
                    }, $dataTable);
                    return $dataTable;
                }
            } else {
                $subQuery = DB::table('project_plans')
                    ->leftJoin('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')
                    ->leftJoin('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
                    ->leftJoin('bid_doc_projects', 'procacts.procact_id', 'bid_doc_projects.procact_id')
                    ->leftJoin('project_bidders', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
                    ->leftJoin('projtypes', 'project_plans.projtype_id', 'projtypes.projtype_id')
                    ->leftJoin('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
                    ->where([
                        ['project_timelines.timeline_status', 'pending'],
                        ['project_plans.status', 'pending'],
                        ['project_plans.project_year', $year],
                        ['project_plans.is_old', '<>', true],
                        ['procacts.advertisement', null],
                        ['project_plans.project_bid_id', '!=', null],
                        ['project_plans.sub_open_date', '>=', date("Y-m") . '-01'],
                        ['project_plans.mode_id', 1],
                        ['municipality_display', $municipal]
                    ])
                    ->orWhere([
                        ['project_timelines.timeline_status', 'set'],
                        ['project_plans.status', 'onprocess'],
                        ['project_plans.project_year', $year],
                        ['project_plans.is_old', '<>', true],
                        ['project_plans.sub_open_date', '<=', date("Y-m-d")],
                        ['bid_status', 'active'],
                        ['procacts.proceed_notice', null],
                        ['project_plans.mode_id', 1],
                        ['municipality_display', $municipal]
                    ])
                    ->orWhere([
                        ['project_timelines.timeline_status', 'set'],
                        ['project_plans.status', 'onprocess'],
                        ['project_plans.project_year', $year],
                        ['project_plans.is_old', '<>', true],
                        ['project_plans.sub_open_date', '<=', date("Y-m-d")],
                        ['bid_status', 'responsive'],
                        ['procacts.proceed_notice', null],
                        ['project_plans.mode_id', 1],
                        ['municipality_display', $municipal]
                    ])
                    ->orWhere([
                        ['project_timelines.timeline_status', 'set'],
                        ['project_plans.status', 'onprocess'],
                        ['project_plans.project_year', $year],
                        ['project_plans.is_old', '<>', true],
                        ['project_plans.sub_open_date', '<=', date("Y-m-d")],
                        ['procacts.proceed_notice', null],
                        ['rfq_project_id', '!=', null],
                        ['project_plans.mode_id', '!=', 1],
                        ['municipality_display', $municipal]
                    ])
                    ->select('project_plans.plan_id', 'type', 'project_no', 'project_title', 'municipality_display')
                    ->distinct('project_plans.plan_id');

                $dataTable = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))->mergeBindings($subQuery);

                if ($switcher == 0) {
                    $dataTable = $dataTable->select('plan_id', 'type', DB::raw('count(*) as total'))->groupBy('type')->get()->toArray();
                    $labels = array_map(function ($item) {
                        return $item->type;
                    }, $dataTable);
                    $type_count = array_map(function ($item) {
                        return $item->total;
                    }, $dataTable);
                } else {
                    $dataTable = $dataTable->get()->toArray();
                    array_map(function ($item) {
                        $item->current_status = 'Ongoing';
                    }, $dataTable);
                    return $dataTable;
                }
            }
        } else if ($status == 'unprocured') {
            $current_month = date("m");
            $current_year = date("Y");
            $dataTable = array();
            if ($municipal == 'all') {
                if ($year != $current_year) {
                    $subQuery = DB::table('project_plans')
                        ->leftJoin('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')
                        ->leftJoin('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
                        ->leftJoin('bid_doc_projects', 'project_timelines.procact_id', 'bid_doc_projects.procact_id')
                        ->leftJoin('project_bidders', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
                        ->leftJoin('municipalities', 'project_plans.municipality_id', '=', 'municipalities.municipality_id')
                        ->leftJoin('projtypes', 'project_plans.projtype_id', '=', 'projtypes.projtype_id')
                        ->where([
                            ['project_timelines.timeline_status', 'pending'],
                            ['project_plans.status', 'pending'],
                            ['project_plans.project_year', $year],
                            ['project_plans.is_old', '<>', true],
                            ['procacts.advertisement', null],
                            ['project_plans.project_bid_id', null],
                            ['project_plans.mode_id', 1]
                        ])
                        ->orWhere([
                            ['project_timelines.timeline_status', 'set'],
                            ['project_plans.status', 'onprocess'],
                            ['project_plans.project_year', $year],
                            ['project_plans.is_old', '<>', true],
                            ['procacts.advertisement', null],
                            ['bid_status', '!=', 'active'],
                            ['procacts.proceed_notice', '!=', null],
                            ['project_plans.mode_id', 1]
                        ])
                        ->orWhere([
                            ['project_timelines.timeline_status', 'set'],
                            ['project_plans.status', 'onprocess'],
                            ['project_plans.project_year', $year],
                            ['project_plans.is_old', '<>', true],
                            ['procacts.advertisement', null],
                            ['bid_status', '!=', 'responsive'],
                            ['procacts.proceed_notice', '!=', null],
                            ['project_plans.mode_id', 1]
                        ])
                        ->orWhere([
                            ['project_plans.project_year', $year],
                            ['project_plans.is_old', '<>', true],
                            ['project_plans.sub_open_date', '!=', null],
                            ['procacts.proceed_notice', null],
                            ['project_bidders.rfq_project_id', '=', null],
                            ['project_bidders.bid_doc_project_id', null]
                        ])
                        ->select('project_plans.plan_id', 'type', 'project_no', 'project_title', 'municipality_display')
                        ->distinct('project_plans.plan_id');

                    $dataTable = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))->mergeBindings($subQuery);

                    if ($switcher == 0) {
                        $dataTable = $dataTable->select('plan_id', 'type', DB::raw('count(*) as total'))->groupBy('type')->get()->toArray();
                        $labels = array_map(function ($item) {
                            return $item->type;
                        }, $dataTable);
                        $type_count = array_map(function ($item) {
                            return $item->total;
                        }, $dataTable);
                    } else {
                        $dataTable = $dataTable->get()->toArray();
                        array_map(function ($item) {
                            $item->current_status = 'Unprocured';
                        }, $dataTable);
                        return $dataTable;
                    }
                } else {
                    if ($current_month <= 3) {
                        $dataTable = 0;
                    } else {
                        $quarter = '';
                        if ($current_month <= 6 && $current_month >= 4) {
                            $quarter = $current_year . '-04-01';
                        } elseif ($current_month <= 9 && $current_month >= 7) {
                            $quarter = $current_year . '-07-01';
                        } elseif ($current_month <= 12 && $current_month >= 10) {
                            $quarter = $current_year . '-10-01';
                        }

                        $subQuery = DB::table('project_plans')
                            ->leftJoin('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')
                            ->leftJoin('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
                            ->leftJoin('bid_doc_projects', 'project_timelines.procact_id', 'bid_doc_projects.procact_id')
                            ->leftJoin('project_bidders', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
                            ->leftJoin('municipalities', 'project_plans.municipality_id', '=', 'municipalities.municipality_id')
                            ->leftJoin('projtypes', 'project_plans.projtype_id', '=', 'projtypes.projtype_id')
                            ->where([
                                ['project_timelines.timeline_status', 'pending'],
                                ['project_plans.status', 'pending'],
                                ['project_plans.project_year', $year],
                                ['project_plans.is_old', '<>', true],
                                ['procacts.advertisement', null],
                                ['project_plans.project_bid_id', null],
                                ['project_plans.abc_post_date', '<', $quarter],
                                ['project_plans.mode_id', 1]
                            ])
                            ->orWhere([
                                ['project_timelines.timeline_status', 'set'],
                                ['project_plans.status', 'onprocess'],
                                ['project_plans.project_year', $year],
                                ['project_plans.is_old', '<>', true],
                                ['procacts.advertisement', null],
                                ['bid_status', '!=', 'active'],
                                ['procacts.proceed_notice', '!=', null],
                                ['project_plans.mode_id', 1]
                            ])
                            ->orWhere([
                                ['project_timelines.timeline_status', 'set'],
                                ['project_plans.status', 'onprocess'],
                                ['project_plans.project_year', $year],
                                ['project_plans.is_old', '<>', true],
                                ['procacts.advertisement', null],
                                ['bid_status', '!=', 'responsive'],
                                ['procacts.proceed_notice', '!=', null],
                                ['project_plans.mode_id', 1]
                            ])
                            ->orWhere([
                                // ['project_timelines.timeline_status', 'set'],
                                // ['project_plans.status', 'onprocess'],
                                ['project_plans.project_year', $year],
                                ['project_plans.is_old', '<>', true],
                                ['project_plans.sub_open_date', '<=', date("Y-m-d")],
                                ['procacts.proceed_notice', null],
                                ['project_bidders.rfq_project_id', '=', null],
                                ['project_bidders.bid_doc_project_id', null]
                            ])
                            ->select('project_plans.plan_id', 'type', 'project_no', 'project_title', 'municipality_display')
                            ->distinct('project_plans.plan_id');

                        $dataTable = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))->mergeBindings($subQuery);
                    }
                    if ($switcher == 0) {
                        $dataTable = $dataTable->select('plan_id', 'type', DB::raw('count(*) as total'))->groupBy('type')->get()->toArray();
                        $labels = array_map(function ($item) {
                            return $item->type;
                        }, $dataTable);
                        $type_count = array_map(function ($item) {
                            return $item->total;
                        }, $dataTable);
                    } else {
                        $dataTable = $dataTable->get()->toArray();
                        array_map(function ($item) {
                            $item->current_status = 'Unprocured';
                        }, $dataTable);
                        return $dataTable;
                    }
                }
            } else {
                if ($year != $current_year) {
                    $subQuery = DB::table('project_plans')
                        ->leftJoin('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')
                        ->leftJoin('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
                        ->leftJoin('bid_doc_projects', 'project_timelines.procact_id', 'bid_doc_projects.procact_id')
                        ->leftJoin('project_bidders', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
                        ->leftJoin('municipalities', 'project_plans.municipality_id', '=', 'municipalities.municipality_id')
                        ->leftJoin('projtypes', 'project_plans.projtype_id', '=', 'projtypes.projtype_id')
                        ->where([
                            ['project_timelines.timeline_status', 'pending'],
                            ['project_plans.status', 'pending'],
                            ['project_plans.project_year', $year],
                            ['project_plans.is_old', '<>', true],
                            ['procacts.advertisement', null],
                            ['project_plans.project_bid_id', null],
                            ['project_plans.mode_id', 1],
                            ['municipality_display', $municipal]
                        ])
                        ->orWhere([
                            ['project_timelines.timeline_status', 'set'],
                            ['project_plans.status', 'onprocess'],
                            ['project_plans.project_year', $year],
                            ['project_plans.is_old', '<>', true],
                            ['procacts.advertisement', null],
                            ['bid_status', '!=', 'active'],
                            ['procacts.proceed_notice', '!=', null],
                            ['project_plans.mode_id', 1],
                            ['municipality_display', $municipal]
                        ])
                        ->orWhere([
                            ['project_timelines.timeline_status', 'set'],
                            ['project_plans.status', 'onprocess'],
                            ['project_plans.project_year', $year],
                            ['project_plans.is_old', '<>', true],
                            ['procacts.advertisement', null],
                            ['bid_status', '!=', 'responsive'],
                            ['procacts.proceed_notice', '!=', null],
                            ['project_plans.mode_id', 1],
                            ['municipality_display', $municipal]
                        ])
                        ->orWhere([
                            // ['project_timelines.timeline_status', 'set'],
                            // ['project_plans.status', 'onprocess'],
                            ['project_plans.project_year', $year],
                            ['project_plans.is_old', '<>', true],
                            ['project_plans.sub_open_date', '!=', null],
                            ['procacts.proceed_notice', null],
                            ['project_bidders.rfq_project_id', '=', null],
                            ['project_bidders.bid_doc_project_id', null],
                            ['municipality_display', $municipal]
                        ])
                        ->select('project_plans.plan_id', 'type', 'project_no', 'project_title', 'municipality_display')
                        ->distinct('project_plans.plan_id');

                    $dataTable = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))->mergeBindings($subQuery);

                    if ($switcher == 0) {
                        $dataTable = $dataTable->select('plan_id', 'type', DB::raw('count(*) as total'))->groupBy('type')->get()->toArray();
                        $labels = array_map(function ($item) {
                            return $item->type;
                        }, $dataTable);
                        $type_count = array_map(function ($item) {
                            return $item->total;
                        }, $dataTable);
                    } else {
                        $dataTable = $dataTable->get()->toArray();
                        array_map(function ($item) {
                            $item->current_status = 'Unprocured';
                        }, $dataTable);
                        return $dataTable;
                    }
                } else {
                    if ($current_month <= 3) {
                    } else {
                        $quarter = '';
                        if ($current_month <= 6 && $current_month >= 4) {
                            $quarter = $current_year . '-04-01';
                        } elseif ($current_month <= 9 && $current_month >= 7) {
                            $quarter = $current_year . '-07-01';
                        } elseif ($current_month <= 12 && $current_month >= 10) {
                            $quarter = $current_year . '-10-1';
                        }

                        $subQuery = DB::table('project_plans')
                            ->leftJoin('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')
                            ->leftJoin('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
                            ->leftJoin('bid_doc_projects', 'project_timelines.procact_id', 'bid_doc_projects.procact_id')
                            ->leftJoin('project_bidders', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
                            ->leftJoin('municipalities', 'project_plans.municipality_id', '=', 'municipalities.municipality_id')
                            ->leftJoin('projtypes', 'project_plans.projtype_id', '=', 'projtypes.projtype_id')
                            ->where([
                                ['project_timelines.timeline_status', 'pending'],
                                ['project_plans.status', 'pending'],
                                ['project_plans.project_year', $year],
                                ['project_plans.is_old', '<>', true],
                                ['procacts.advertisement', null],
                                ['project_plans.project_bid_id', null],
                                ['project_plans.abc_post_date', '<', $quarter],
                                ['project_plans.mode_id', 1],
                                ['municipality_display', $municipal]
                            ])
                            ->orWhere([
                                ['project_timelines.timeline_status', 'set'],
                                ['project_plans.status', 'onprocess'],
                                ['project_plans.project_year', $year],
                                ['project_plans.is_old', '<>', true],
                                ['procacts.advertisement', null],
                                ['bid_status', '!=', 'active'],
                                ['procacts.proceed_notice', '!=', null],
                                ['project_plans.mode_id', 1],
                                ['municipality_display', $municipal]
                            ])
                            ->orWhere([
                                ['project_timelines.timeline_status', 'set'],
                                ['project_plans.status', 'onprocess'],
                                ['project_plans.project_year', $year],
                                ['project_plans.is_old', '<>', true],
                                ['procacts.advertisement', null],
                                ['bid_status', '!=', 'responsive'],
                                ['procacts.proceed_notice', '!=', null],
                                ['project_plans.mode_id', 1],
                                ['municipality_display', $municipal]
                            ])
                            ->orWhere([
                                // ['project_timelines.timeline_status', 'set'],
                                // ['project_plans.status', 'onprocess'],
                                ['project_plans.project_year', $year],
                                ['project_plans.is_old', '<>', true],
                                ['project_plans.sub_open_date', '<=', date("Y-m-d")],
                                ['procacts.proceed_notice', null],
                                ['project_bidders.rfq_project_id', '=', null],
                                ['project_bidders.bid_doc_project_id', null],
                                ['municipality_display', $municipal]
                            ])
                            ->select('project_plans.plan_id', 'type', 'project_no', 'project_title', 'municipality_display')
                            ->distinct('project_plans.plan_id');

                        $dataTable = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))->mergeBindings($subQuery);
                    }
                    if ($switcher == 0) {
                        $dataTable = $dataTable->select('plan_id', 'type', DB::raw('count(*) as total'))->groupBy('type')->get()->toArray();
                        $labels = array_map(function ($item) {
                            return $item->type;
                        }, $dataTable);
                        $type_count = array_map(function ($item) {
                            return $item->total;
                        }, $dataTable);
                    } else {
                        $dataTable = $dataTable->get()->toArray();
                        array_map(function ($item) {
                            $item->current_status = 'Unprocured';
                        }, $dataTable);
                        return $dataTable;
                    }
                }
            }
        } else if ($status == 'all') {
            if ($municipal == 'all') {
                $dataTable = DB::table('project_plans')
                    ->leftJoin('projtypes', 'project_plans.projtype_id', 'projtypes.projtype_id')
                    ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
                    ->select('plan_id', 'type', 'project_no', 'project_title', 'municipality_display', 'project_plans.status as projstatus')
                    ->where([['project_plans.is_old', '<>', true], ['project_year', $year]], ['abc_post_date', '<', '2023-07-01']);

                if ($switcher == 0) {
                    $dataTable = $dataTable->select('projtypes.type', DB::raw('count(*) as total'))->groupBy('type')->get()->toArray();
                    $labels = array_map(function ($item) {
                        return $item->type;
                    }, $dataTable);
                    $type_count = array_map(function ($item) {
                        return $item->total;
                    }, $dataTable);
                } else {
                    $dataTable = $dataTable->get()->toArray();
                    array_map(function ($item) {
                        $item->current_status = $item->projstatus;
                    }, $dataTable);
                    return $dataTable;
                }
            } else {
                $dataTable = DB::table('project_plans')
                    ->join('projtypes', 'project_plans.projtype_id', 'projtypes.projtype_id')
                    ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
                    ->select('plan_id', 'type', 'project_no', 'project_title', 'municipality_display', 'project_plans.status as projstatus')
                    ->where([['project_year', $year], ['municipality_display', $municipal], ['project_plans.is_old', '<>', true]]);

                if ($switcher == 0) {
                    $dataTable = $dataTable->select('projtypes.type', DB::raw('count(*) as total'))->groupBy('projtypes.type')->get()->toArray();
                    $labels = array_map(function ($item) {
                        return $item->type;
                    }, $dataTable);
                    $type_count = array_map(function ($item) {
                        return $item->total;
                    }, $dataTable);
                } else {
                    $dataTable = $dataTable->get()->toArray();
                    array_map(function ($item) {
                        $item->current_status = $item->projstatus;
                    }, $dataTable);
                    return $dataTable;
                }
            }
        }

        return compact('labels', 'type_count');
    }
}
