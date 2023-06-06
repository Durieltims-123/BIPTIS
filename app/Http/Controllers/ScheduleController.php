<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\APP;
use Validator;


class ScheduleController extends Controller
{

  public function getSVPSchedules()
  {
    $year = date('Y');
    $project_type = '';
    $status = 'all_schedule';
    $mode = 2;
    $municipality = null;
    $source = null;
    $type = null;
    $date_added = null;
    $month = null;
    $sort = 'ASC';
    $pow = true;
    $APP = new APP;
    $fund_category = null;
    $account_classification = null;
    $month = null;
    $sort = null;
    $filter = null;

    $title = "SVP/Negotiated Project Schedules";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view('admin.schedule', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'message' => "one", 'project_plans' => $project_plans, 'year' => $year, 'mode' => $mode]);
  }

  public function getBiddingSchedules()
  {
    $year = date('Y');
    $project_type = '';
    $status = 'all_schedule';
    $mode = 1;
    $municipality = null;
    $source = null;
    $type = null;
    $date_added = null;
    $month = null;
    $sort = 'ASC';
    $pow = true;
    $APP = new APP;
    $fund_category = null;
    $account_classification = null;
    $month = null;
    $sort = null;
    $filter = null;

    $title = "Bidding Project Schedules";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view('admin.schedule', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'message' => "one", 'project_plans' => $project_plans, 'year' => $year, 'mode' => $mode]);
  }


  // manipulation
  public function submitSchedule(Request $request)
  {
    $message = "";
    $specific_message = "";
    $prebid_status = "pending";
    $app_by_ha_status = "pending";
    $plan_ids_array = explode(",", $request->input('plan_ids'));
    $latest_procacts = [];
    $mode_id = DB::table('project_plans')->select('mode_id')->whereIn('plan_id', $plan_ids_array)->distinct()->first();
    if ($request->input('pre_procurement') != null) {
      $pre_procurement = date("Y-m-d", strtotime($request->input('pre_procurement')));
    } else {
      $pre_procurement = null;
    }

    $timeline_status = DB::table('project_timelines')->select("project_timelines.timeline_status")->whereIn('procacts.plan_id', $plan_ids_array)
      ->where('project_activity_status.main_status', 'pending')
      ->join('procacts', 'procacts.procact_id', 'project_timelines.procact_id')
      ->join('project_activity_status', 'procacts.procact_id', 'project_activity_status.procact_id')
      ->distinct()->get();

    if (count($timeline_status) > 1) {
      $message = "multiple status";
    } else {

      if ($timeline_status[0]->timeline_status == "set" && count($plan_ids_array) > 1) {
        $message = "set status";
      } else {

        if ($request->input('ads-post-start') != null) {
          $advertisement_start = date("Y-m-d", strtotime($request->input('ads-post-start')));
        } else {
          $advertisement_start = null;
        }
        if ($request->input('ads-post-end') != null) {
          $advertisement_end = date("Y-m-d", strtotime($request->input('ads-post-end')));
        } else {
          $advertisement_end = null;
        }
        if ($request->input('pre-bid-start') != null) {
          $pre_bid_start = date("Y-m-d", strtotime($request->input('pre-bid-start')));
        } else {
          $pre_bid_start = null;
        }
        if ($request->input('pre-bid-end') != null) {
          $pre_bid_end = date("Y-m-d", strtotime($request->input('pre-bid-end')));
        } else {
          $pre_bid_end = null;
          $prebid_status = "not_needed";
        }
        if ($request->input('sub-of-bid-start') != null) {
          $bid_submission_start = date("Y-m-d", strtotime($request->input('sub-of-bid-start')));
        } else {
          $bid_submission_start = null;
        }
        if ($request->input('sub-of-bid-end') != null) {
          $bid_submission_end = date("Y-m-d", strtotime($request->input('sub-of-bid-end')));
        } else {
          $bid_submission_end = null;
        }
        if ($request->input('bid-eval-start') != null) {
          $bid_evaluation_start = date("Y-m-d", strtotime($request->input('bid-eval-start')));
        } else {
          $bid_evaluation_start = null;
        }

        if ($request->input('bid-eval-end') != null) {
          $bid_evaluation_end = date("Y-m-d", strtotime($request->input('bid-eval-end')));
        } else {
          $bid_evaluation_end = null;
        }
        if ($request->input('post-qual-start') != null) {
          $post_qualification_start = date("Y-m-d", strtotime($request->input('post-qual-start')));
        } else {
          $post_qualification_start = null;
        }
        if ($request->input('post-qual-end') != null) {
          $post_qualification_end = date("Y-m-d", strtotime($request->input('post-qual-end')));
        } else {
          $post_qualification_end = null;
        }
        if ($request->input('iss-of-noa-start') != null) {
          $award_notice_start = date("Y-m-d", strtotime($request->input('iss-of-noa-start')));
        } else {
          $award_notice_start = null;
        }
        if ($request->input('iss-of-noa-end') != null) {
          $award_notice_end = date("Y-m-d", strtotime($request->input('iss-of-noa-end')));
        } else {
          $award_notice_end = null;
        }
        if ($request->input('contract-prep-start') != null) {
          $contract_signing_start = date("Y-m-d", strtotime($request->input('contract-prep-start')));
        } else {
          $contract_signing_start = null;
        }
        if ($request->input('contract-prep-end') != null) {
          $contract_signing_end = date("Y-m-d", strtotime($request->input('contract-prep-end')));
        } else {
          $contract_signing_end = null;
        }
        if ($request->input('app-by-ha-start') != null) {
          $authority_approval_start = date("Y-m-d", strtotime($request->input('app-by-ha-start')));
        } else {
          $app_by_ha_status = "not_needed";
          $authority_approval_start = null;
        }
        if ($request->input('app-by-ha-end') != null) {
          $authority_approval_end = date("Y-m-d", strtotime($request->input('app-by-ha-end')));
        } else {
          $authority_approval_end = null;
        }
        if ($request->input('ntp-start') != null) {
          $proceed_notice_start = date("Y-m-d", strtotime($request->input('ntp-start')));
        } else {
          $proceed_notice_start = null;
        }
        if ($request->input('ntp-end') != null) {
          $proceed_notice_end = date("Y-m-d", strtotime($request->input('ntp-end')));
        } else {
          $proceed_notice_end = null;
        }

        // EDIT
        if ($timeline_status[0]->timeline_status == "set" && count($plan_ids_array) == 1) {

          $message = "success";
          $specific_message = "";
          $project_plan = DB::table('project_plans')->select('current_cluster', 'latest_procact_id')->whereIn('plan_id', $plan_ids_array)->first();
          $latest_procact = DB::table('procacts')->where('procact_id', $project_plan->latest_procact_id)->orderBy('created_at', 'desc')->first();
          $latest_timeline = DB::table('project_timelines')->where('procact_id', $latest_procact->procact_id)->first();
          $latest_activity_status = DB::table('project_activity_status')->where('procact_id', $latest_procact->procact_id)->first();


          if ($pre_procurement != null) {
            if (strtotime($pre_procurement) > strtotime($advertisement_start)) {
              $specific_message = "Sorry! Pre-Procurement should be before Advertisement";
              $message = "edit_error";
            }
          }
          //
          // if($latest_activity_status->open_bid==="finished"){
          //   if($latest_timeline->bid_submission_start!=$bid_submission_start&&$latest_timeline->bid_submission_end!=$bid_submission_end){
          //     $specific_message="Sorry! You cannot edit Bid Submission/Opening Date Range";
          //     $message="edit_error";
          //   }
          // }
          //
          // else
          if ($latest_activity_status->bid_evaluation === "finished") {
            if ($latest_timeline->bid_evaluation_start != $bid_evaluation_start && $latest_timeline->bid_evaluation_end != $bid_evaluation_end) {
              $specific_message = "Sorry! You cannot edit Bid Evaluation Date Range";
              $message = "edit_error";
            }
          } else if ($latest_activity_status->post_qual === "finished") {
            if ($latest_timeline->post_qualification_start != $post_qualification_start && $latest_timeline->post_qualification_end != $post_qualification_end) {
              $specific_message = "Sorry! You cannot edit Post Qualification Date Range";
              $message = "edit_error";
            }
          } else if ($latest_activity_status->award_notice === "finished") {
            if ($latest_timeline->award_notice_start != $award_notice_start && $latest_timeline->award_notice_end != $award_notice_end) {
              $specific_message = "Sorry! You cannot edit Notice of Award Date Range";
              $message = "edit_error";
            }
          } else if ($latest_activity_status->contract_signing === "finished") {
            if ($latest_timeline->contract_signing_start != $contract_signing_start && $latest_timeline->contract_signing_end != $contract_signing_end) {
              $specific_message = "Sorry! You cannot edit Contract Signing Date Range";
              $message = "edit_error";
            }
          } else if ($latest_activity_status->authority_approval === "finished") {
            if ($latest_timeline->authority_approval_start != $authority_approval_start && $latest_timeline->authority_approval_end != $authority_approval_end) {
              $specific_message = "Sorry! You cannot Edit Approval of Higher Authority Date Range";
              $message = "edit_error";
            }
          } else if ($latest_activity_status->proceed_notice === "finished") {
            if ($latest_timeline->proceed_notice_start != $proceed_notice_start && $latest_timeline->proceed_notice_end != $proceed_notice_end) {
              $specific_message = "Sorry! You cannot edit Notice to Proceed Date Range";
              $message = "edit_error";
            }
          } else {
          }


          if ($message == "success") {
            foreach ($plan_ids_array as $plan_id) {
              $project_plan = DB::table('project_plans')->select('current_cluster')->where('plan_id', $plan_id)->first();
              if ($project_plan->current_cluster != null) {
                $clustered_projects = DB::table('project_plans')->select('plan_id')->where([['plan_id', '<>', $plan_id], ['current_cluster', $project_plan->current_cluster]])->get();
                foreach ($clustered_projects as $clustered_project) {
                  array_push($plan_ids_array, $clustered_project->plan_id);
                  $latest = DB::table('procacts')->where('plan_id', $clustered_project->plan_id)->orderBy('created_at', 'desc')->first();
                  array_push($latest_procacts, $latest->procact_id);
                }
              }
              $latest = DB::table('procacts')->where('plan_id', $plan_id)->orderBy('created_at', 'desc')->first();
              array_push($latest_procacts, $latest->procact_id);
            }

            // update
            $update = DB::table('project_timelines')->whereIn('procact_id', $latest_procacts)->update([
              "timeline_status" => "set",
              "advertisement_start" => $advertisement_start,
              "advertisement_end" => $advertisement_end,
              "pre_bid_start" => $pre_bid_start,
              "pre_bid_end" => $pre_bid_end,
              "bid_submission_start" => $bid_submission_start,
              "bid_submission_end" => $bid_submission_end,
              "bid_evaluation_start" => $bid_evaluation_start,
              "bid_evaluation_end" => $bid_evaluation_end,
              "post_qualification_start" => $post_qualification_start,
              "post_qualification_end" => $post_qualification_end,
              "award_notice_start" => $award_notice_start,
              "award_notice_end" => $award_notice_end,
              "contract_signing_start" => $contract_signing_start,
              "contract_signing_end" => $contract_signing_end,
              "authority_approval_start" => $authority_approval_start,
              "authority_approval_end" => $authority_approval_end,
              "proceed_notice_start" => $proceed_notice_start,
              "proceed_notice_end" => $proceed_notice_end,
              "updated_at" => now()
            ]);



            // Also Update Actual Procurement Activity and Project Acitvity Status

            // For Pre procurement checking
            if ($pre_procurement != null) {
              $cmp_plan_ids = [];
              $project_plans = DB::table('project_plans')->select('current_cluster', 'latest_procact_id', 'plan_id', 'project_cost')->whereIn('plan_id', $plan_ids_array)->get();
              foreach ($project_plans as $project_plan) {
                $group_procact_ids = [];
                if (in_array($project_plan->plan_id, $cmp_plan_ids) === false) {
                  if ($project_plan->current_cluster != null) {
                    $clustered_plans = DB::table('project_plans')->select('current_cluster', 'latest_procact_id', 'plan_id', 'project_cost')->where('current_cluster', $project_plan->current_cluster)->get();
                    $total_project_cost = 0;
                    foreach ($clustered_plans as $clustered_plan) {
                      array_push($cmp_plan_ids, $clustered_plan->plan_id);
                      array_push($group_procact_ids, $clustered_plan->latest_procact_id);
                      $total_project_cost = $total_project_cost + $clustered_plan->project_cost;
                    }
                  } else {
                    $total_project_cost = $project_plan->project_cost;
                    array_push($cmp_plan_ids, $project_plan->plan_id);
                    array_push($group_procact_ids, $project_plan->latest_procact_id);
                  }

                  if ($total_project_cost > 5000000) {
                    $update = DB::table('procacts')->whereIn('procact_id', $group_procact_ids)->update([
                      "pre_proc" => $pre_procurement
                    ]);
                    $update = DB::table('project_activity_status')->whereIn('procact_id', $group_procact_ids)->update([
                      "pre_proc" => "finished"
                    ]);
                    $update = DB::table('project_timelines')->whereIn('procact_id', $group_procact_ids)->update([
                      "pre_proc_date" => $pre_procurement
                    ]);
                  }
                }
              }
            }


            // for Advertisement and Pre bid
            $update = DB::table('procacts')->whereIn('procact_id', $latest_procacts)->update([
              "advertisement" => $advertisement_start,
              "pre_bid" => $pre_bid_start,
              "open_bid" => $bid_submission_start,
              "open_time" => "8:30"
            ]);

            if ($prebid_status == "pending") {
              $prebid_status = "finished";
            }

            DB::table("project_activity_status")->whereIn('procact_id', $latest_procacts)->update([
              "advertisement" => "finished",
              "pre_bid" => $prebid_status,
              "authority_approval" => $app_by_ha_status,
              "open_bid" => "finished",
            ]);

            $message = "success";
          }
        }

        // ADD
        else {
          foreach ($plan_ids_array as $plan_id) {
            $project_plan = DB::table('project_plans')->select('current_cluster')->where('plan_id', $plan_id)->first();
            if ($project_plan->current_cluster != null) {
              $clustered_projects = DB::table('project_plans')->select('plan_id')->where([['plan_id', '<>', $plan_id], ['current_cluster', $project_plan->current_cluster]])->get();
              foreach ($clustered_projects as $clustered_project) {
                array_push($plan_ids_array, $clustered_project->plan_id);
                $latest = DB::table('procacts')->where('plan_id', $clustered_project->plan_id)->orderBy('created_at', 'desc')->first();
                array_push($latest_procacts, $latest->procact_id);
              }
            }
            $latest = DB::table('procacts')->where('plan_id', $plan_id)->orderBy('created_at', 'desc')->first();
            array_push($latest_procacts, $latest->procact_id);
          }

          // update
          $update = DB::table('project_timelines')->whereIn('procact_id', $latest_procacts)->update([
            "timeline_status" => "set",
            "advertisement_start" => $advertisement_start,
            "advertisement_end" => $advertisement_end,
            "pre_bid_start" => $pre_bid_start,
            "pre_bid_end" => $pre_bid_end,
            "bid_submission_start" => $bid_submission_start,
            "bid_submission_end" => $bid_submission_end,
            "bid_evaluation_start" => $bid_evaluation_start,
            "bid_evaluation_end" => $bid_evaluation_end,
            "post_qualification_start" => $post_qualification_start,
            "post_qualification_end" => $post_qualification_end,
            "award_notice_start" => $award_notice_start,
            "award_notice_end" => $award_notice_end,
            "contract_signing_start" => $contract_signing_start,
            "contract_signing_end" => $contract_signing_end,
            "authority_approval_start" => $authority_approval_start,
            "authority_approval_end" => $authority_approval_end,
            "proceed_notice_start" => $proceed_notice_start,
            "proceed_notice_end" => $proceed_notice_end,
            "updated_at" => now()
          ]);
          DB::table("project_plans")->whereIn('latest_procact_id', $latest_procacts)->update([
            "status" => "onprocess"
          ]);

          // Also Update Actual Procurement Activity and Project Acitvity Status
          // For Pre procurement checking
          if ($pre_procurement != null) {
            $cmp_plan_ids = [];
            $project_plans = DB::table('project_plans')->select('current_cluster', 'latest_procact_id', 'plan_id', 'project_cost')->whereIn('plan_id', $plan_ids_array)->get();
            foreach ($project_plans as $project_plan) {
              $group_procact_ids = [];
              if (in_array($project_plan->plan_id, $cmp_plan_ids) === false) {
                if ($project_plan->current_cluster != null) {
                  $clustered_plans = DB::table('project_plans')->select('current_cluster', 'latest_procact_id', 'plan_id', 'project_cost')->where('current_cluster', $project_plan->current_cluster)->get();
                  $total_project_cost = 0;
                  foreach ($clustered_plans as $clustered_plan) {
                    array_push($cmp_plan_ids, $clustered_plan->plan_id);
                    array_push($group_procact_ids, $clustered_plan->latest_procact_id);
                    $total_project_cost = $total_project_cost + $clustered_plan->project_cost;
                  }
                } else {
                  $total_project_cost = $project_plan->project_cost;
                  array_push($cmp_plan_ids, $project_plan->plan_id);
                  array_push($group_procact_ids, $project_plan->latest_procact_id);
                }

                if ($total_project_cost > 5000000) {
                  $update = DB::table('procacts')->whereIn('procact_id', $group_procact_ids)->update([
                    "pre_proc" => $pre_procurement
                  ]);
                  $update = DB::table('project_activity_status')->whereIn('procact_id', $group_procact_ids)->update([
                    "pre_proc" => "finished"
                  ]);
                  $update = DB::table('project_timelines')->whereIn('procact_id', $group_procact_ids)->update([
                    "pre_proc_date" => $pre_procurement
                  ]);
                }
              }
            }
          }
          // for Advertisement and Pre bid
          $update = DB::table('procacts')->whereIn('procact_id', $latest_procacts)->update([
            "advertisement" => $advertisement_start,
            "pre_bid" => $pre_bid_start
          ]);

          if ($prebid_status == "pending") {
            $prebid_status = "finished";
          }


          DB::table("project_activity_status")->whereIn('procact_id', $latest_procacts)->update([
            "advertisement" => "finished",
            "pre_bid" => $prebid_status,
            "authority_approval" => $app_by_ha_status
          ]);

          // for Advertisement and Pre bid
          $update = DB::table('procacts')->whereIn('procact_id', $latest_procacts)->update([
            "advertisement" => $advertisement_start,
            "pre_bid" => $pre_bid_start,
            "open_bid" => $bid_submission_start,
            "open_time" => "8:30"
          ]);

          if ($prebid_status == "pending") {
            $prebid_status = "finished";
          }

          DB::table("project_activity_status")->whereIn('procact_id', $latest_procacts)->update([
            "advertisement" => "finished",
            "pre_bid" => $prebid_status,
            "authority_approval" => $app_by_ha_status,
            "open_bid" => "finished",
          ]);


          $message = "success";
        }

        // Arrange project_type
        $projectswithnullarrangement = DB::table("project_timelines")
          ->join("procacts", "procacts.procact_id", "project_timelines.procact_id")
          ->join("project_plans", "project_plans.plan_id", "procacts.plan_id")
          ->join("funds", "project_plans.fund_id", "funds.fund_id")
          ->join("municipalities", "municipalities.municipality_id", "project_plans.municipality_id")
          ->where([["project_timelines.bid_submission_end", $bid_submission_end], ["procacts.itb_arrangement", null]])
          ->count();

        if ($projectswithnullarrangement > 0) {
          $APP = new APP;
          $procact_ids_array = [];
          $project_plans = [];
          $project_plan = ["procact_id" => null, "municipality_id" => null, "project_cost" => null, "total_project_cost" => null, "project_title" => null, "plan_id" => null];
          $arrangement_number = 1;
          $projects = DB::table("project_timelines")
            ->select("procacts.*", "project_plans.project_cost", "municipalities.municipality_id", "project_plans.project_title")
            ->join("procacts", "procacts.procact_id", "project_timelines.procact_id")
            ->join("project_plans", "project_plans.plan_id", "procacts.plan_id")
            ->join("funds", "project_plans.fund_id", "funds.fund_id")
            ->join("municipalities", "municipalities.municipality_id", "project_plans.municipality_id")
            ->where([["project_timelines.bid_submission_end", $bid_submission_end], ['mode_id', $mode_id->mode_id]])
            ->orderBy("procacts.procact_mode_id", "asc")
            ->orderBy("procacts.itb_arrangement", "asc")
            ->orderBy("municipalities.municipality_name", "asc")
            ->orderBy("project_plans.project_cost", "asc")
            ->orderBy("procacts.plan_cluster_id", "asc")
            ->orderBy("project_plans.project_title", "asc")
            ->get();

          foreach ($projects as $project) {
            if (in_array($project->procact_id, $procact_ids_array) === false) {
              if ($project->plan_cluster_id != null) {
                $total_project_cost = 0;
                $clusters = DB::table("project_timelines")
                  ->where("procacts.plan_cluster_id", $project->plan_cluster_id)
                  ->select("procacts.*", "project_plans.project_cost", "municipalities.municipality_id", "project_plans.project_title")
                  ->join("procacts", "procacts.procact_id", "project_timelines.procact_id")
                  ->join("project_plans", "project_plans.plan_id", "procacts.plan_id")
                  ->join("funds", "project_plans.fund_id", "funds.fund_id")
                  ->join("municipalities", "municipalities.municipality_id", "project_plans.municipality_id")
                  ->where("project_timelines.bid_submission_end", $bid_submission_end)
                  ->orderBy("procacts.procact_mode_id", "asc")
                  ->orderBy("municipalities.municipality_name", "asc")
                  ->orderBy("project_plans.plan_id", "asc")
                  ->orderBy("project_plans.project_cost", "asc")
                  ->orderBy("procacts.plan_cluster_id", "asc")
                  ->orderBy("project_plans.project_title", "asc")
                  ->get();
                foreach ($clusters as $cluster) {
                  $total_project_cost = $total_project_cost + $cluster->project_cost;
                }
                foreach ($clusters as $cluster) {
                  $project_plan["procact_id"] = $cluster->procact_id;
                  $project_plan["municipality_id"] = $cluster->municipality_id;
                  $project_plan["project_cost"] = (float)$cluster->project_cost;
                  $project_plan["total_project_cost"] = $total_project_cost;
                  $project_plan["project_title"] = $cluster->project_title;
                  $project_plan["plan_id"] = $cluster->plan_id;
                  array_push($project_plans, (object)$project_plan);
                  array_push($procact_ids_array, $cluster->procact_id);
                }
              } else {
                $project_plan["procact_id"] = $project->procact_id;
                $project_plan["municipality_id"] = $project->municipality_id;
                $project_plan["project_cost"] = (float)$project->project_cost;
                $project_plan["total_project_cost"] = (float)$project->project_cost;
                $project_plan["project_title"] = $project->project_title;
                $project_plan["plan_id"] = $project->plan_id;
                array_push($project_plans, (object)$project_plan);
                array_push($procact_ids_array, $project->procact_id);
              }
            }
          }
          $project_plans = $APP->sortObject($project_plans, array("municipality_id" => 'asc', 'total_project_cost' => 'asc', 'project_title' => 'asc'));
          foreach ($project_plans as $project_plan) {
            $update = DB::table('procacts')->where('procact_id', $project_plan->procact_id)
              ->update(["itb_arrangement" => $arrangement_number]);
            $arrangement_number = $arrangement_number + 1;
          }
        }
      }
    }
    return redirect()->back()->with('message', $message)->with('specific_message', $specific_message);
  }


  public function deleteSchedule($id)
  {
    $data = DB::table('project_timelines')->where('plan_id', $id)->orderBy('procact_id', 'desc')->limit(1)->get();

    if (count($data) < 0) {
      return abort(404);
    } else {

      $bid_doc = DB::table('bid_doc_projects')->where("procact_id", $data[0]->procact_id)->count();
      $rfq = DB::table('rfq_projects')->where("procact_id", $data[0]->procact_id)->count();

      if ($bid_doc > 0 || $rfq > 0) {
        return redirect()->back()->with('message', 'delete_error');
      } else {
        $procact_id = [];
        $plan = DB::table('project_plans')->where('plan_id', $id)->first();
        if ($plan->current_cluster == null) {
          $projects = DB::table('project_plans')->where('plan_id', $id)->get();
          array_push($procact_id, $projects[0]->latest_procact_id);
        } else {
          $projects = DB::table('project_timelines')->where('project_plans.current_cluster', $plan->current_cluster)->join('procacts', 'procacts.procact_id', 'project_timelines.procact_id')
            ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')
            ->get();
          foreach ($projects as $project) {
            array_push($procact_id, $project->latest_procact_id);
          }
        }

        $update = DB::table('project_plans')->where('plan_id', $id)->update(["status" => "pending"]);

        $update = DB::table('project_timelines')->whereIn('procact_id', $procact_id)->update([
          "timeline_status" => "pending",
          "advertisement_start" => null,
          "advertisement_end" => null,
          "pre_bid_start" => null,
          "pre_bid_end" => null,
          "bid_submission_start" => null,
          "bid_submission_end" => null,
          "bid_evaluation_start" => null,
          "bid_evaluation_end" => null,
          "post_qualification_start" => null,
          "post_qualification_end" => null,
          "award_notice_start" => null,
          "award_notice_end" => null,
          "contract_signing_start" => null,
          "contract_signing_end" => null,
          "authority_approval_start" => null,
          "authority_approval_end" => null,
          "proceed_notice_start" => null,
          "proceed_notice_end" => null,
          "updated_at" => now()
        ]);

        $update = DB::table('procacts')->whereIn('procact_id', $procact_id)->update([
          "pre_proc" => null,
          "advertisement" => null,
          "pre_bid" => null,
          "open_bid" => null
        ]);

        DB::table("project_activity_status")->whereIn('procact_id', $procact_id)->update([
          "pre_proc" => "not_needed",
          "advertisement" => "pending",
          "pre_bid" => "pending",
          "open_bid" => "pending",
          "authority_approval" => "pending"
        ]);


        return redirect()->back()->with('message', 'delete_success');
      }
    }
  }


  public function filterSchedule(Request $request)
  {
    $data = $request->validate([
      "date_added" => "nullable|before:tomorrow",
      "project_year" => 'nullable|digits:4|integer|min:2020|max:' . (date('Y')),
      "month_added" => "nullable|date_format:m-Y||max:" . (date('m-Y'))
    ]);

    $year = $request->input('project_year');
    $schedule_status = $request->input('schedule_status');
    $project_type = $request->input('app_type');
    $status = null;
    $mode = $request->input('mode');
    $municipality = null;
    $source = null;
    $type = null;
    $date_added = $request->input('date_added');
    $month = $request->input('month_added');
    $sort = 'ASC';
    $pow = null;
    $fund_category = null;
    $account_classification = null;
    $month = null;
    $sort = null;
    $filter = null;

    if ($schedule_status == 1) {
      $filter = [['project_timelines.timeline_status', 'set']];
    }
    if ($schedule_status == 2) {
      $filter = [['project_timelines.timeline_status', 'pending']];
    }


    $APP = new APP;

    $newData = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    return back()->withInput()->with(["newData" => $newData]);
  }


  public function submitCluster(Request $request)
  {
    $project_plans = $request->input('cluster_ids');
    $project_plans = explode(',', $project_plans);
    $mode = $request->input('cluster_mode');
    $cluster_process = $request->input('cluster_process');
    $special_case_1 = null;
    if ($request->one_title === "1") {
      $special_case_1 = true;
    }

    if ($cluster_process == "add_cluster") {

      $plan_ids_array = explode(",", $request->input('cluster_ids'));
      $timeline_status = DB::table('project_plans')->select("project_timelines.timeline_status")->whereIn('project_plans.plan_id', $plan_ids_array)
        ->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->distinct()->get();
      if (count($timeline_status) > 1) {
        $message = "multiple status";
      } else {

        if ($timeline_status[0]->timeline_status == "set") {
          $message = "set status";
        } else {

          $clustered_count = DB::table('project_plans')->whereIn("plan_id", $project_plans)->whereNotNull('current_cluster')->count();
          if ($clustered_count > 0) {
            $message = "already_clustered";
          } else {
            DB::table('clusters')->insert([
              "cluster_mode" => $mode,
              "created_at" => now(),
              "updated_at" => now()
            ]);

            $new_cluster = DB::table('clusters')->orderBy('cluster_id', 'desc')->first();
            $cluster_id = $new_cluster->cluster_id;

            foreach ($project_plans as $project_plan) {
              DB::table('clustered_project_plans')->insert([
                "cluster_id" => $cluster_id,
                "plan_id" => $project_plan,
                "created_at" => now(),
                "updated_at" => now()
              ]);
            }

            DB::table('project_plans')->whereIn("plan_id", $project_plans)->update([
              "current_cluster" => $cluster_id,
              "special_case_1" => $special_case_1
            ]);

            DB::table('project_plans')->whereIn("project_plans.plan_id", $project_plans)->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')->update([
              "procacts.plan_cluster_id" => $cluster_id
            ]);

            $clustered_amount = DB::table('project_plans')->whereIn("plan_id", $project_plans)->sum('project_cost');
            $clustered_amount = (float)$clustered_amount;

            if ($clustered_amount >= 5000000) {

              DB::table('project_plans')->whereIn("project_plans.plan_id", $project_plans)
                ->join('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
                ->update([
                  "project_activity_status.pre_bid" => 'pending',
                  "project_activity_status.pre_proc" => 'pending'
                ]);
            }
            $message = "cluster_success";
          }
        }
      }
      return redirect()->back()->with('message', $message);
    } else {

      DB::table('project_plans')->whereIn("plan_id", $project_plans)->update([
        "current_cluster" => null,
        "special_case_1" => $special_case_1
      ]);

      DB::table('project_plans')->whereIn("project_plans.plan_id", $project_plans)->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')->update([
        "procacts.plan_cluster_id" => null
      ]);

      foreach ($project_plans as $project_plan) {
        $count = DB::table('project_plans')->where([['project_cost', '>=', 5000000], ["project_plans.plan_id", $project_plan]])->count();
        if ($count > 0) {
          $status = "pending";
        } else {
          $status = "not_needed";
        }

        DB::table('project_plans')->where("project_plans.plan_id", $project_plans)
          ->join('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
          ->update([
            "project_activity_status.pre_bid" => $status,
            "project_activity_status.pre_proc" => $status
          ]);
      }


      return redirect()->back()->with('message', 'uncluster_success');
    }
  }

  public function cancelSchedule(Request $request)
  {
    $plan_ids = $request->plan_ids;
    $with_bidder = DB::table('project_plans')->whereIn('plan_id', $plan_ids)->where('project_bid_id', '<>', null)->count();
    $pending = DB::table("project_timelines")->where('timeline_status', 'pending')->whereIn("plan_id", $plan_ids)->count();
    if ($with_bidder > 0) {
      return ["message" => "with_bidder"];
    }
    if ($pending > 0) {
      return ["message" => "pending_schedule"];
    }
    foreach ($plan_ids as $plan_id) {
      $remarks = $request->remarks;
      // get latest linked files
      $project_plan = DB::table('project_plans')->where('plan_id', $plan_id)->first();
      $latest_activity_status = DB::table("project_activity_status")->where("plan_id", $plan_id)->orderBy('pro_act_stat_id', 'desc')->first();
      $latest_procact = DB::table("project_plans")->select("procacts.*", "project_plans.*")->where("project_plans.plan_id", $plan_id)->join("procacts", "project_plans.latest_procact_id", "procacts.procact_id")->first();
      $latest_timeline = DB::table("project_timelines")->where("plan_id", $plan_id)->orderBy('timeline_id', 'desc')->first();
      $pre_procurement = "not_needed";

      if ($project_plan->abc > 5000000) {
        $pre_procurement = "pending";
      }

      DB::table('project_activity_status')->where('procact_id', $latest_procact->procact_id)->update([
        "main_status" => "cancelled"
      ]);


      // insert to project logs
      DB::table('project_logs')->insert([
        'plan_id' =>  $project_plan->plan_id,
        'user_id' => Auth::user()->id,
        'project_log_type' => "Cancelled Project",
        'project_log_remarks' => $remarks,
        'log_date' => date("Y-m-d"),
        'created_at' => now(),
        'updated_at' => now()
      ]);


      // create new timeline, status and procurement_activity
      DB::table('procacts')->insert([
        "procact_mode_id" => $project_plan->mode_id,
        "plan_id" => $project_plan->plan_id,
        'created_at' => now(),
        'updated_at' => now()
      ]);

      $latest_procact = DB::table('procacts')->where('plan_id', $project_plan->plan_id)->orderBy('created_at', 'desc')->first();

      DB::table('project_plans')->where('plan_id', $plan_id)->update([
        "re_bid_count" => $project_plan->re_bid_count,
        "status" => "pending",
        "current_cluster" => null,
        "latest_procact_id" => $latest_procact->procact_id,
      ]);

      DB::table('project_timelines')->insert([
        "plan_id" => $project_plan->plan_id,
        "procact_id" => $latest_procact->procact_id,
        "timeline_status" => "pending",
        'created_at' => now(),
        'updated_at' => now()
      ]);

      DB::table('project_activity_status')->insert([
        "procact_id" => $latest_procact->procact_id,
        "plan_id" => $project_plan->plan_id,
        "pre_proc" => $pre_procurement,
        'created_at' => now(),
        'updated_at' => now()
      ]);
    }
    return ["message" => "success"];
  }
  public function deferSchedule(Request $request)
  {
    $plan_ids = $request->plan_ids;

    foreach ($plan_ids as $plan_id) {
      $remarks = $request->remarks;
      // get latest linked files
      $project_plan = DB::table('project_plans')->where('plan_id', $plan_id)->first();
      $latest_activity_status = DB::table("project_activity_status")->where("plan_id", $plan_id)->orderBy('pro_act_stat_id', 'desc')->first();
      $latest_procact = DB::table("project_plans")->select("procacts.*", "project_plans.*")->where("project_plans.plan_id", $plan_id)->join("procacts", "project_plans.latest_procact_id", "procacts.procact_id")->first();
      $latest_timeline = DB::table("project_timelines")->where("plan_id", $plan_id)->orderBy('timeline_id', 'desc')->first();
      $pre_procurement = "not_needed";

      if ($project_plan->abc > 5000000) {
        $pre_procurement = "pending";
      }

      DB::table('project_activity_status')->where('procact_id', $latest_procact->procact_id)->update([
        "main_status" => "deferred"
      ]);

      // insert to project logs
      DB::table('project_logs')->insert([
        'plan_id' =>  $project_plan->plan_id,
        'user_id' => Auth::user()->id,
        'project_log_type' => "Deferred Project Opening",
        'project_log_remarks' => $remarks,
        'log_date' => date("Y-m-d"),
        'created_at' => now(),
        'updated_at' => now()
      ]);


      // create new timeline, status and procurement_activity
      DB::table('procacts')->insert([
        "procact_mode_id" => $project_plan->mode_id,
        "plan_id" => $project_plan->plan_id,
        'created_at' => now(),
        'updated_at' => now()
      ]);

      $latest_procact = DB::table('procacts')->where('plan_id', $project_plan->plan_id)->orderBy('created_at', 'desc')->first();

      DB::table('project_plans')->where('plan_id', $plan_id)->update([
        "re_bid_count" => $project_plan->re_bid_count,
        "status" => "pending",
        "current_cluster" => null,
        "latest_procact_id" => $latest_procact->procact_id,
      ]);

      DB::table('project_timelines')->insert([
        "plan_id" => $project_plan->plan_id,
        "procact_id" => $latest_procact->procact_id,
        "timeline_status" => "pending",
        'created_at' => now(),
        'updated_at' => now()
      ]);

      DB::table('project_activity_status')->insert([
        "procact_id" => $latest_procact->procact_id,
        "plan_id" => $project_plan->plan_id,
        "pre_proc" => $pre_procurement,
        'created_at' => now(),
        'updated_at' => now()
      ]);
    }
    return ["message" => "success"];
  }
}
