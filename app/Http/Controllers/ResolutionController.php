<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\APP;
use Validator;
use PhpOffice\PhpWord\TemplateProcessor;

class ResolutionController extends Controller
{
  public function getPublicPath()
  {
    $publicPath = 'C:\xampp\htdocs\biptis\infrabidv2\public\\';
    return $publicPath;
  }

  public function addResolutionRecommendingAward()
  {
    $year = null;
    $APP = new APP;
    $project_plans = $APP->getSpecificProcurementActivity('projects_without_resolution', $year);
    $links = getUserLinks();
    $user_privilege = getUserPrivilegeByLink('resolution_recommending_awards');
    $access = checkUserAccess('add', $user_privilege);

    return view('admin.resolution_form', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => 'Add Resolution Recommending Award', 'resolution_type' => 'RRA', 'project_plans' => $project_plans, 'resolution_projects' => null, "resolution" => null]);
  }

  public function editResolutionRecommendingAward($id)
  {
    $year = null;
    $APP = new APP;
    $project_plans = $APP->getSpecificProcurementActivity('projects_without_resolution', $year);
    $resolution_projects = DB::table('resolution_projects')
      ->where([['resolution_id', $id]])
      ->select('project_plans.*', 'procacts.*', 'barangays.barangay_name', 'municipalities.municipality_name', 'procurement_modes.mode', 'funds.source', 'project_timelines.*', DB::raw("DATEDIFF(post_qualification_end, post_qualification_start) AS post_qual_interval , DATEDIFF(CURDATE(), post_qualification_start) AS post_qual_now_interval"))
      ->join('procacts', 'procacts.procact_id', 'resolution_projects.procact_id')
      ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
      ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id')
      ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
      ->join('funds', 'funds.fund_id', 'project_plans.fund_id')
      ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
      ->get();

    $resolution_project_ids = DB::table('resolution_projects')
      ->where('resolution_id', $id)
      ->select(DB::raw('group_concat(procacts.procact_id) as procact_ids'))
      ->join('procacts', 'procacts.procact_id', 'resolution_projects.procact_id')
      ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
      ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id')
      ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
      ->join('funds', 'funds.fund_id', 'project_plans.fund_id')
      ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
      ->get();

    $resolution = DB::table('resolutions')->where('resolution_id', $id)->first();

    if ($resolution === null) {
      return abort("403", "Unknown Resolution");
    }

    
    foreach ($resolution_projects as $resolution_project) {
      $project_plans->push($resolution_project);
    }

    $links = getUserLinks();
    $user_privilege = getUserPrivilegeByLink('resolution_recommending_awards');
    $access = checkUserAccess('update', $user_privilege);


    return view('admin.resolution_form', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => 'Edit Resolution Recommending Award', 'resolution_type' => 'RRA', 'project_plans' => $project_plans, "resolution_projects" => $resolution_project_ids[0]->procact_ids, "resolution" => $resolution]);
  }

  public function deleteResolution(Request $request)
  {


    $resolution = DB::table("resolutions")->where('resolution_id', $request->id)->first();

    if ($resolution->type === "OTHERS") {
      DB::table('archive_resolution_attachments')->where("resolution_id", $request->id)->delete();
    }
    $archive_resolution_attachments = DB::table('archive_resolution_attachments')->where("resolution_id", $request->id)->count();
    if ($archive_resolution_attachments === 0) {

      if ($resolution->type === "RRRC") {
        $old_bids = DB::table("resolution_project_bids")->where("resolution_id", $request->id)->get()->toArray();
        $old_procact_ids = array_map(function ($e) {
          return is_object($e) ? $e->procact_id : $e['procact_id'];
        }, $old_bids);
        DB::table("resolutions")
          ->where([["type", "RRA"]])
          ->whereIn('resolution_projects.procact_id', $old_procact_ids)
          ->join('resolution_projects', 'resolutions.resolution_id', 'resolution_projects.resolution_id')
          ->update(['resolution_projects.cancelled' => null]);
        $resolution_projects = DB::table('resolution_project_bids')->where("resolution_id", $request->id)->delete();
      } else {
        $resolution_projects = DB::table('resolution_projects')->where("resolution_id", $request->id)->delete();
      }


      DB::table('resolutions')->where('resolution_id', $request->id)->delete();
      return "success";
    } else {
      return "delete_error";
    }
  }


  public function getResolutionProjects(Request $request)
  {
    if ($request->year != null) {
      $year = $request->year;
      $resolution_projects1 = DB::table('resolution_projects')
        ->where([["resolution_date", 'like', $year . "%"]])
        ->select('project_plans.*', 'procacts.*', 'barangays.barangay_name', 'municipalities.municipality_name', 'procurement_modes.mode', 'funds.source', 'project_timelines.*', DB::raw("DATEDIFF(post_qualification_end, post_qualification_start) AS post_qual_interval , DATEDIFF(CURDATE(), post_qualification_start) AS post_qual_now_interval"), 'resolutions.*')
        ->join('procacts', 'procacts.procact_id', 'resolution_projects.procact_id')
        ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
        ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
        ->join('resolutions', 'resolutions.resolution_id', 'resolution_projects.resolution_id')
        ->join('funds', 'funds.fund_id', 'project_plans.fund_id')
        ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
        ->orderBy('resolutions.resolution_id', 'desc')
        ->get();

      $resolution_projects2 = DB::table('resolution_project_bids')
        ->where([["resolution_date", 'like', $year . "%"]])
        ->select('project_plans.*', 'procacts.*', 'barangays.barangay_name', 'municipalities.municipality_name', 'procurement_modes.mode', 'funds.source', 'project_timelines.*', DB::raw("DATEDIFF(post_qualification_end, post_qualification_start) AS post_qual_interval , DATEDIFF(CURDATE(), post_qualification_start) AS post_qual_now_interval"), 'resolutions.*')
        ->join('procacts', 'procacts.procact_id', 'resolution_project_bids.procact_id', 'resolution_project_bids.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
        ->join('funds', 'funds.fund_id', 'project_plans.fund_id')
        ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
        ->join('resolutions', 'resolutions.resolution_id', 'resolution_project_bids.resolution_id')
        ->get();

      $resolution_projects = array_merge((array)json_decode($resolution_projects1), (array)json_decode($resolution_projects2));


      return back()->withInput()->with("resolution_projects", $resolution_projects);
    } else {
      $year = date('Y');
      $resolution_projects1 = DB::table('resolution_projects')
        ->where([["resolution_date", 'like', $year . "%"]])
        ->select('project_plans.*', 'procacts.*', 'barangays.barangay_name', 'municipalities.municipality_name', 'procurement_modes.mode', 'funds.source', 'project_timelines.*', DB::raw("DATEDIFF(post_qualification_end, post_qualification_start) AS post_qual_interval , DATEDIFF(CURDATE(), post_qualification_start) AS post_qual_now_interval"), 'resolutions.*')
        ->join('procacts', 'procacts.procact_id', 'resolution_projects.procact_id')
        ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
        ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
        ->join('resolutions', 'resolutions.resolution_id', 'resolution_projects.resolution_id')
        ->join('funds', 'funds.fund_id', 'project_plans.fund_id')
        ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
        ->orderBy('resolutions.resolution_id', 'desc')
        ->get();

      $resolution_projects2 = DB::table('resolution_project_bids')
        ->where([["resolution_date", 'like', $year . "%"]])
        ->select('project_plans.*', 'procacts.*', 'barangays.barangay_name', 'municipalities.municipality_name', 'procurement_modes.mode', 'funds.source', 'project_timelines.*', DB::raw("DATEDIFF(post_qualification_end, post_qualification_start) AS post_qual_interval , DATEDIFF(CURDATE(), post_qualification_start) AS post_qual_now_interval"), 'resolutions.*')
        ->join('procacts', 'procacts.procact_id', 'resolution_project_bids.procact_id', 'resolution_project_bids.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
        ->join('funds', 'funds.fund_id', 'project_plans.fund_id')
        ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
        ->join('resolutions', 'resolutions.resolution_id', 'resolution_project_bids.resolution_id')
        ->get();

      $resolution_projects = array_merge((array)json_decode($resolution_projects1), (array)json_decode($resolution_projects2));

      $title = "Resolution Projects";
      $links = getUserLinks();
      $user_privilege = getUserPrivilege();
      return view("admin.resolution_projects", ['links' => $links, 'user_privilege' => $user_privilege, "resolution_projects" => $resolution_projects, "title" => $title, "year" => $year]);
    }
  }

  public function filterResolution(Request $request)
  {
    $data = $request->validate([
      "year" => 'required|digits:4|integer|min:2020|max:' . (date('Y')),
    ]);
    $year = $request->year;
    $type = $request->resolution_type;
    $resolutions = DB::table("resolutions")->where([["type", $type], ["resolution_date", 'like', $year . "%"]])->orderBy('resolution_id', 'desc')->get();
    if ($request->resolution_type === "RGMR" || $request->resolution_type === "RDMR") {
      $resolutions = getResolutionBidders($resolutions);
    }
    return back()->withInput()->with("resolutions", $resolutions);
  }

  public function getResolutionRecommendingRecallCancellation()
  {
    $year = date('Y');
    $resolutions = DB::table("resolutions")->where([["type", "RRRC"], ["resolution_date", 'like', $year . "%"]])->orderBy('resolution_id', 'desc')->get();
    $title = "Resolutions Recommending Recall/Cancellation";
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view("admin.resolutions", ['links' => $links, 'user_privilege' => $user_privilege, "resolution_type" => "RRRC", "resolutions" => $resolutions, "title" => $title, "year" => $year]);
  }

  public function addResolutionRecommendingRecallCancellation()
  {
    $year = null;
    $APP = new APP;
    $project_plans = $APP->getSpecificProcurementActivity('projects_with_resolution_pending_noa', $year);
    $links = getUserLinks();
    $user_privilege = getUserPrivilegeByLink('resolution_recommending_recall_cancellation');
    $access = checkUserAccess('add', $user_privilege);
    return view('admin.resolution_form2', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => 'Add Resolution Recommending Recall/Cancellation', 'resolution_type' => 'RRRC', 'project_plans' => $project_plans, 'resolution_projects' => null, "resolution" => null]);
  }

  public function editResolutionRecommendingRecallCancellation($id)
  {
    $year = null;
    $APP = new APP;
    $project_plans = $APP->getSpecificProcurementActivity('projects_with_resolution_pending_noa', $year);
    $links = getUserLinks();
    $user_privilege = getUserPrivilegeByLink('resolution_recommending_recall_cancellation');
    $access = checkUserAccess('add', $user_privilege);
    $resolution_projects = DB::table('resolution_project_bids')
      ->where('resolution_id', $id)
      ->select('project_plans.*', 'procacts.*', 'barangays.barangay_name', 'municipalities.municipality_name', 'procurement_modes.mode', 'funds.source', 'project_timelines.*', DB::raw("DATEDIFF(post_qualification_end, post_qualification_start) AS post_qual_interval , DATEDIFF(CURDATE(), post_qualification_start) AS post_qual_now_interval"), 'resolution_project_bids.project_bid as responsive_bid')
      ->join('procacts', 'procacts.procact_id', 'resolution_project_bids.procact_id', 'resolution_project_bids.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id')
      ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
      ->join('funds', 'funds.fund_id', 'project_plans.fund_id')
      ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
      ->get();

    $resolution_project_ids = DB::table('resolution_project_bids')
      ->where('resolution_id', $id)
      ->select(DB::raw('group_concat(procacts.procact_id) as procact_ids'))
      ->join('procacts', 'procacts.procact_id', 'resolution_project_bids.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id')
      ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
      ->join('funds', 'funds.fund_id', 'project_plans.fund_id')
      ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
      ->get();


    $resolution_project_ids = DB::table('resolution_project_bids')
      ->where('resolution_id', $id)
      ->select(DB::raw('group_concat(procacts.procact_id) as procact_ids'), DB::raw('group_concat(resolution_project_bids.project_bid) as project_bids'))
      ->join('procacts', 'procacts.procact_id', 'resolution_project_bids.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id')
      ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
      ->join('funds', 'funds.fund_id', 'project_plans.fund_id')
      ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
      ->get();


    $resolution = DB::table('resolutions')->where('resolution_id', $id)->first();

    if ($resolution === null) {
      return abort("403", "Unknown Resolution");
    }


    $project_plans = (array) $project_plans;
    foreach ($resolution_projects as $resolution_project) {
      $bidder = $APP->getBid($resolution_project->responsive_bid);
      $resolution_project->responsive_bidder = $bidder->business_name;
      array_push($project_plans, $resolution_project);
    }
    $project_plans = (object) $project_plans;

    $links = getUserLinks();
    $user_privilege = getUserPrivilegeByLink('resolution_declaring_failure');
    $access = checkUserAccess('update', $user_privilege);
    $user_privilege = ['add', 'update', 'delete'];


    return view('admin.resolution_form2', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => 'Edit Resolution Recommending Recall/Cancellation', 'resolution_type' => 'RRRC', 'project_plans' => $project_plans, "resolution_projects" => $resolution_project_ids[0]->procact_ids, "resolution" => $resolution]);
  }


  public function getResolutionRecommendingAwards()
  {
    $year = date('Y');
    $resolutions = DB::table("resolutions")->where([["type", "RRA"], ["resolution_date", 'like', $year . "%"]])->orderBy('resolution_id', 'desc')->get();
    $title = "Resolutions Recommending Award";
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view("admin.resolutions", ['links' => $links, 'user_privilege' => $user_privilege, "resolution_type" => "RRA", "resolutions" => $resolutions, "title" => $title, "year" => $year]);
  }

  public function addResolutionDeclaringFailure()
  {
    $year = null;
    $APP = new APP;
    $project_plans = $APP->getSpecificProcurementActivity('projects_without_bidders', $year);
    $links = getUserLinks();
    $user_privilege = getUserPrivilegeByLink('resolution_declaring_failure');
    $access = checkUserAccess('add', $user_privilege);


    return view('admin.resolution_form', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => 'Add Resolution Declaring Failure', 'resolution_type' => 'RDF', 'project_plans' => $project_plans, 'resolution_projects' => null, "resolution" => null]);
  }


  public function editResolutionDeclaringFailure($id)
  {
    $year = null;
    $APP = new APP;
    $project_plans = $APP->getSpecificProcurementActivity('projects_without_bidders', $year);
    $resolution_projects = DB::table('resolution_projects')
      ->where('resolution_id', $id)
      ->select('project_plans.*', 'procacts.*', 'barangays.barangay_name', 'municipalities.municipality_name', 'procurement_modes.mode', 'funds.source', 'project_timelines.*', DB::raw("DATEDIFF(post_qualification_end, post_qualification_start) AS post_qual_interval , DATEDIFF(CURDATE(), post_qualification_start) AS post_qual_now_interval"))
      ->join('procacts', 'procacts.procact_id', 'resolution_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id')
      ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
      ->join('funds', 'funds.fund_id', 'project_plans.fund_id')
      ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
      ->get();

    $resolution_project_ids = DB::table('resolution_projects')
      ->where('resolution_id', $id)
      ->select(DB::raw('group_concat(procacts.procact_id) as procact_ids'))
      ->join('procacts', 'procacts.procact_id', 'resolution_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id')
      ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
      ->join('funds', 'funds.fund_id', 'project_plans.fund_id')
      ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
      ->get();

    $resolution = DB::table('resolutions')->where('resolution_id', $id)->first();

    if ($resolution === null) {
      return abort("403", "Unknown Resolution");
    }

    $project_plans = (array) $project_plans;
    foreach ($resolution_projects as $resolution_project) {
      $non_responsive = $APP->getActiveBidders($resolution_project->procact_id, 'non-responsive');
      $disqualified = $APP->getActiveBidders($resolution_project->procact_id, 'disqualified');
      $resolution_project = (array) $resolution_project;
      if ($non_responsive > 0) {
        $resolution_project = array_merge($resolution_project, array('failure_status' => "Failure After Post Qual"));
      } else if ($disqualified > 0) {
        $resolution_project = array_merge($resolution_project, array('failure_status' => "Failure Upon Opening"));
      } else {
        $resolution_project = array_merge($resolution_project, array('failure_status' => "No Bidders"));
      }
      array_push($project_plans, (object)$resolution_project);
    }
    $project_plans = (object) $project_plans;
    $links = getUserLinks();
    $user_privilege = getUserPrivilegeByLink('resolution_declaring_failure');
    $access = checkUserAccess('update', $user_privilege);


    return view('admin.resolution_form', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => 'Edit Resolution Declaring Failure', 'resolution_type' => 'RDF', 'project_plans' => $project_plans, "resolution_projects" => $resolution_project_ids[0]->procact_ids, "resolution" => $resolution]);
  }
  public function editResolutionDenyingTheMotionFOrReconsideration($id)
  {
    $year = null;
    $APP = new APP;
    $resolution = DB::table('resolutions')->where('resolution_id', $id)->first();

    $rfqs_motion_for_reconsiderations = DB::table('motion_for_reconsideration')
      ->select('motion_for_reconsideration.*', 'motion_for_reconsideration_project_bid.*', 'project_bidders.*', 'rfq_projects.*', 'rfqs.*', 'procacts.*', 'project_plans.*', 'procurement_modes.*', 'contractors.*', 'twg_evaluations.*')
      ->where([['resolution_mr_project_bids.mr_project_bid_id', null]])
      ->join("motion_for_reconsideration_project_bid", 'motion_for_reconsideration.mr_id', 'motion_for_reconsideration_project_bid.mr_id')
      ->join('project_bidders', 'project_bidders.project_bid', 'motion_for_reconsideration_project_bid.project_bid_id')
      ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->leftJoin('resolution_mr_project_bids', 'motion_for_reconsideration_project_bid.mr_project_bid_id', 'resolution_mr_project_bids.mr_project_bid_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
      ->get();

    $bid_docs_motion_for_reconsiderations = DB::table('motion_for_reconsideration')
      ->select('motion_for_reconsideration.*', 'motion_for_reconsideration_project_bid.*', 'project_bidders.*', 'bid_doc_projects.*', 'bid_docs.*', 'procacts.*', 'project_plans.*', 'procurement_modes.*', 'contractors.*', 'twg_evaluations.*')
      ->where([['resolution_mr_project_bids.mr_project_bid_id', null]])
      ->join("motion_for_reconsideration_project_bid", 'motion_for_reconsideration.mr_id', 'motion_for_reconsideration_project_bid.mr_id')
      ->join('project_bidders', 'project_bidders.project_bid', 'motion_for_reconsideration_project_bid.project_bid_id')
      ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->leftJoin('resolution_mr_project_bids', 'motion_for_reconsideration_project_bid.mr_project_bid_id', 'resolution_mr_project_bids.mr_project_bid_id')
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
      ->get();

    $selected_rfqs_motion_for_reconsiderations = DB::table('motion_for_reconsideration')
      ->where([['resolution_mr_project_bids.resolution_id', $id]])
      ->join("motion_for_reconsideration_project_bid", 'motion_for_reconsideration.mr_id', 'motion_for_reconsideration_project_bid.mr_id')
      ->join('project_bidders', 'project_bidders.project_bid', 'motion_for_reconsideration_project_bid.project_bid_id')
      ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->leftJoin('resolution_mr_project_bids', 'motion_for_reconsideration_project_bid.mr_project_bid_id', 'resolution_mr_project_bids.mr_project_bid_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
      ->get();

    $selected_bid_docs_motion_for_reconsiderations = DB::table('motion_for_reconsideration')
      ->where([['resolution_mr_project_bids.resolution_id', $id]])
      ->join("motion_for_reconsideration_project_bid", 'motion_for_reconsideration.mr_id', 'motion_for_reconsideration_project_bid.mr_id')
      ->join('project_bidders', 'project_bidders.project_bid', 'motion_for_reconsideration_project_bid.project_bid_id')
      ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->leftJoin('resolution_mr_project_bids', 'motion_for_reconsideration_project_bid.mr_project_bid_id', 'resolution_mr_project_bids.mr_project_bid_id')
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
      ->get();

    $mr_project_bid_ids = DB::table('resolution_mr_project_bids')
      ->where('resolution_id', $id)
      ->select(DB::raw('group_concat(resolution_mr_project_bids.mr_project_bid_id) as mr_project_bid_ids'))
      ->get();

    if (count($rfqs_motion_for_reconsiderations) > 0 && count($bid_docs_motion_for_reconsiderations) > 0) {
      $mrs = array_merge((array)json_decode($rfqs_motion_for_reconsiderations), (array)json_decode($bid_docs_motion_for_reconsiderations));
    } else if (count($rfqs_motion_for_reconsiderations) > 0) {
      $mrs = (array)json_decode($rfqs_motion_for_reconsiderations);
    } else if (count($bid_docs_motion_for_reconsiderations) > 0) {
      $mrs = (array)json_decode($bid_docs_motion_for_reconsiderations);
    } else {
      $mrs = [];
    }

    if (count($selected_rfqs_motion_for_reconsiderations) > 0 && count($selected_bid_docs_motion_for_reconsiderations) > 0) {
      $selected_mrs = array_merge((array)json_decode($selected_rfqs_motion_for_reconsiderations), (array)json_decode($selected_bid_docs_motion_for_reconsiderations));
    } else if (count($selected_rfqs_motion_for_reconsiderations) > 0) {
      $selected_mrs = (array)json_decode($selected_rfqs_motion_for_reconsiderations);
    } else if (count($selected_bid_docs_motion_for_reconsiderations) > 0) {
      $selected_mrs = (array)json_decode($selected_bid_docs_motion_for_reconsiderations);
    } else {
      $selected_mrs = [];
    }

    if (count($mrs) > 0 && count($selected_mrs) > 0) {
      $mrs = array_merge((array)$mrs, (array)$selected_mrs);
    } else if (count($mrs) > 0) {
      $mrs = (array)$mrs;
    } else if (count($selected_mrs) > 0) {
      $mrs = (array)$selected_mrs;
    } else {
      $mrs = [];
    }

    $links = getUserLinks();
    $user_privilege = getUserPrivilegeByLink('resolution_denying_the_motion_for_reconsideration');
    $access = checkUserAccess('update', $user_privilege);


    return view('admin.bidders_resolution_form', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => 'Edit Resolution Denying The Motion For Reconsideration', 'resolution_type' => 'RDMR', 'mrs' => $mrs, 'mr_project_bid_ids' => $mr_project_bid_ids, "resolution" => $resolution]);
  }
  public function editResolutionGrantingTheMotionFOrReconsideration($id)
  {
    $year = null;
    $APP = new APP;
    $resolution = DB::table('resolutions')->where('resolution_id', $id)->first();

    $rfqs_motion_for_reconsiderations = DB::table('motion_for_reconsideration')
      ->select('motion_for_reconsideration.*', 'motion_for_reconsideration_project_bid.*', 'project_bidders.*', 'rfq_projects.*', 'rfqs.*', 'procacts.*', 'project_plans.*', 'procurement_modes.*', 'contractors.*', 'twg_evaluations.*')
      ->where([['resolution_mr_project_bids.mr_project_bid_id', null]])
      ->join("motion_for_reconsideration_project_bid", 'motion_for_reconsideration.mr_id', 'motion_for_reconsideration_project_bid.mr_id')
      ->join('project_bidders', 'project_bidders.project_bid', 'motion_for_reconsideration_project_bid.project_bid_id')
      ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->leftJoin('resolution_mr_project_bids', 'motion_for_reconsideration_project_bid.mr_project_bid_id', 'resolution_mr_project_bids.mr_project_bid_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
      ->get();

    $bid_docs_motion_for_reconsiderations = DB::table('motion_for_reconsideration')
      ->select('motion_for_reconsideration.*', 'motion_for_reconsideration_project_bid.*', 'project_bidders.*', 'bid_doc_projects.*', 'bid_docs.*', 'procacts.*', 'project_plans.*', 'procurement_modes.*', 'contractors.*', 'twg_evaluations.*')
      ->where([['resolution_mr_project_bids.mr_project_bid_id', null]])
      ->join("motion_for_reconsideration_project_bid", 'motion_for_reconsideration.mr_id', 'motion_for_reconsideration_project_bid.mr_id')
      ->join('project_bidders', 'project_bidders.project_bid', 'motion_for_reconsideration_project_bid.project_bid_id')
      ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->leftJoin('resolution_mr_project_bids', 'motion_for_reconsideration_project_bid.mr_project_bid_id', 'resolution_mr_project_bids.mr_project_bid_id')
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
      ->get();

    $selected_rfqs_motion_for_reconsiderations = DB::table('motion_for_reconsideration')
      ->where([['resolution_mr_project_bids.resolution_id', $id]])
      ->join("motion_for_reconsideration_project_bid", 'motion_for_reconsideration.mr_id', 'motion_for_reconsideration_project_bid.mr_id')
      ->join('project_bidders', 'project_bidders.project_bid', 'motion_for_reconsideration_project_bid.project_bid_id')
      ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->leftJoin('resolution_mr_project_bids', 'motion_for_reconsideration_project_bid.mr_project_bid_id', 'resolution_mr_project_bids.mr_project_bid_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
      ->get();

    $selected_bid_docs_motion_for_reconsiderations = DB::table('motion_for_reconsideration')
      ->where([['resolution_mr_project_bids.resolution_id', $id]])
      ->join("motion_for_reconsideration_project_bid", 'motion_for_reconsideration.mr_id', 'motion_for_reconsideration_project_bid.mr_id')
      ->join('project_bidders', 'project_bidders.project_bid', 'motion_for_reconsideration_project_bid.project_bid_id')
      ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->leftJoin('resolution_mr_project_bids', 'motion_for_reconsideration_project_bid.mr_project_bid_id', 'resolution_mr_project_bids.mr_project_bid_id')
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
      ->get();

    $mr_project_bid_ids = DB::table('resolution_mr_project_bids')
      ->where('resolution_id', $id)
      ->select(DB::raw('group_concat(resolution_mr_project_bids.mr_project_bid_id) as mr_project_bid_ids'))
      ->get();

    if (count($rfqs_motion_for_reconsiderations) > 0 && count($bid_docs_motion_for_reconsiderations) > 0) {
      $mrs = array_merge((array)json_decode($rfqs_motion_for_reconsiderations), (array)json_decode($bid_docs_motion_for_reconsiderations));
    } else if (count($rfqs_motion_for_reconsiderations) > 0) {
      $mrs = (array)json_decode($rfqs_motion_for_reconsiderations);
    } else if (count($bid_docs_motion_for_reconsiderations) > 0) {
      $mrs = (array)json_decode($bid_docs_motion_for_reconsiderations);
    } else {
      $mrs = [];
    }

    if (count($selected_rfqs_motion_for_reconsiderations) > 0 && count($selected_bid_docs_motion_for_reconsiderations) > 0) {
      $selected_mrs = array_merge((array)json_decode($selected_rfqs_motion_for_reconsiderations), (array)json_decode($selected_bid_docs_motion_for_reconsiderations));
    } else if (count($selected_rfqs_motion_for_reconsiderations) > 0) {
      $selected_mrs = (array)json_decode($selected_rfqs_motion_for_reconsiderations);
    } else if (count($selected_bid_docs_motion_for_reconsiderations) > 0) {
      $selected_mrs = (array)json_decode($selected_bid_docs_motion_for_reconsiderations);
    } else {
      $selected_mrs = [];
    }

    if (count($mrs) > 0 && count($selected_mrs) > 0) {
      $mrs = array_merge((array)$mrs, (array)$selected_mrs);
    } else if (count($mrs) > 0) {
      $mrs = (array)$mrs;
    } else if (count($selected_mrs) > 0) {
      $mrs = (array)$selected_mrs;
    } else {
      $mrs = [];
    }
    $links = getUserLinks();
    $user_privilege = getUserPrivilegeByLink('resolution_granting_the_motion_for_reconsideration');
    $access = checkUserAccess('update', $user_privilege);

    return view('admin.bidders_resolution_form', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => 'Edit Resolution Granting The Motion For Reconsideration', 'resolution_type' => 'RGMR', 'mrs' => $mrs, 'mr_project_bid_ids' => $mr_project_bid_ids, "resolution" => $resolution]);
  }

  public function getResolutionDeclaringFailure()
  {
    $year = date('Y');
    $resolutions = DB::table("resolutions")->where([["type", "RDF"], ["resolution_date", 'like', $year . "%"]])->orderBy('resolution_id', 'desc')->get();
    $title = "Resolutions Declaring Failure";
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();


    return view("admin.resolutions", ['links' => $links, 'user_privilege' => $user_privilege, "resolution_type" => "RDF", "resolutions" => $resolutions, "title" => $title, "year" => $year]);
  }

  public function submitResolution(Request $request)
  {
    $data = $request->validate([
      "resolution_number" => "required",
      "resolution_date" => "required",
      "procact_ids" => "required",
    ]);
    // dd($request);
    $procact_ids = explode(",", $request->input("procact_ids"));
    $project_bids = explode(",", $request->input("project_bids"));
    $APP = new APP;

    if ($request->input("resolution_type") === "RRA") {
      $modes = DB::table('procacts')->select('procact_mode_id')->whereIn('procact_id', $procact_ids)->distinct()->get();
      if (count($modes) > 1) {
        return back()->with("message", "multiple_modes");
      }
    }

    if ($request->input("resolution_type") === "RDF") {
      $multiple_failure_status = false;
      $initial_failure = "";
      // foreach ($procact_ids as $key=>$procact_id) {
      //   $non_responsive=$APP->getActiveBidders($procact_id,'non-responsive');
      //   $disqualified=$APP->getActiveBidders($procact_id,'disqualified');
      //   if($key===0){
      //     if($non_responsive>0){
      //       $initial_failure="Failure After Post Qual";
      //       $failure=$initial_failure;
      //     }
      //     else if($disqualified>0){
      //       $initial_failure="Failure After Post Qual";
      //       $failure=$initial_failure;
      //     }
      //     else{
      //       $initial_failure="No Bidders";
      //       $failure=$initial_failure;
      //     }
      //   }
      //   else{
      //     if($non_responsive>0){
      //       $failure="Failure After Post Qual";
      //     }
      //     else if($disqualified>0){
      //       $failure="Failure After Post Qual";
      //     }
      //     else{
      //       $failure="No Bidders";
      //     }
      //   }
      //   if($failure!=$initial_failure){
      //     $multiple_failure_status=true;
      //     break;
      //   }
      // }

      if ($multiple_failure_status) {
        return back()->with("message", "multiple_failure_status");
      }
    } else {
      $opening = DB::table('procacts')->select('open_bid')->whereIn('procact_id', $procact_ids)->distinct()->get();
      if (count($opening) > 1) {
        return back()->with("message", "multiple_opening");
      }
    }

    $governor = DB::table("governors")->orderBy("governor_id", "desc")->first();
    $message = "success";

    if ($request->input("resolution_id") === null) {
      $duplicate = DB::table("resolutions")->where("resolution_number", $request->input("resolution_number"))->count();

      if ($duplicate > 0) {
        $message = "duplicate";
      } else {
        $add = DB::table("resolutions")->insert([
          "resolution_number" => $request->input("resolution_number"),
          "resolution_date" => date("Y-m-d", strtotime($request->input('resolution_date'))),
          "type" => $request->input("resolution_type"),
          "governor_id" => $governor->governor_id,
          "created_at" => now(),
          "updated_at" => now()
        ]);

        if ($add) {
          $resolution = DB::table("resolutions")->where([["resolution_number", $request->input("resolution_number")], ["type", $request->input("resolution_type")]])->first();
          if ($request->input("resolution_type") != "RRRC") {
            foreach ($procact_ids as $procact_id) {
              $add = DB::table("resolution_projects")->insert([
                "resolution_id" => $resolution->resolution_id,
                "procact_id" => $procact_id,
                "created_at" => now(),
                "updated_at" => now()
              ]);
            }
          } else {
            foreach ($project_bids as
              $key => $project_bid) {
              $add = DB::table("resolution_project_bids")->insert([
                "resolution_id" => $resolution->resolution_id,
                "project_bid" => $project_bid,
                "procact_id" => $procact_ids[$key],
                "created_at" => now(),
                "updated_at" => now()
              ]);
            }

            DB::table("resolutions")->where("type", "RRA")
              ->whereIn('resolution_projects.procact_id', $procact_ids)
              ->join('resolution_projects', 'resolutions.resolution_id', 'resolution_projects.resolution_id')
              ->update(['resolution_projects.cancelled' => 1]);
          }
        }
      }
    } else {

      $duplicate = DB::table("resolutions")->where([["resolution_number", $request->input("resolution_number")], ["resolution_id", '<>', $request->input("resolution_id")]])->count();
      if ($duplicate > 0) {
        $message = "duplicate";
      } else {
        $update = DB::table("resolutions")
          ->where("resolution_id", $request->input("resolution_id"))
          ->update([
            "resolution_number" => $request->input("resolution_number"),
            "resolution_date" => date("Y-m-d", strtotime($request->input('resolution_date'))),
            "type" => $request->input("resolution_type"),
            "governor_id" => $governor->governor_id
          ]);


        $old_bids = DB::table("resolution_projects")->where("resolution_id", $request->input("resolution_id"))->get()->toArray();
        $old_procact_ids = array_map(function ($e) {
          return is_object($e) ? $e->procact_id : $e['procact_id'];
        }, $old_bids);
        if ($request->input("resolution_type") != "RRRC") {
          $delete = DB::table("resolution_projects")->where("resolution_id", $request->input("resolution_id"))->delete();
          foreach ($procact_ids as $procact_id) {
            $add = DB::table("resolution_projects")->insert([
              "resolution_id" => $request->input("resolution_id"),
              "procact_id" => $procact_id
            ]);
          }
        } else {
          $delete = DB::table("resolution_project_bids")->where("resolution_id", $request->input("resolution_id"))->delete();

          DB::table("resolutions")
            ->where([["type", "RRA"]])
            ->whereIn('resolution_projects.procact_id', $old_procact_ids)
            ->join('resolution_projects', 'resolutions.resolution_id', 'resolution_projects.resolution_id')
            ->update(['resolution_projects.cancelled' => null]);

          DB::table("resolutions")->where("type", "RRA")
            ->whereIn('resolution_projects.procact_id', $procact_ids)
            ->join('resolution_projects', 'resolutions.resolution_id', 'resolution_projects.resolution_id')
            ->update(['resolution_projects.cancelled' => 1]);

          foreach ($project_bids as
            $key => $project_bid) {
            $add = DB::table("resolution_project_bids")->insert([
              "resolution_id" => $request->input("resolution_id"),
              "project_bid" => $project_bid,
              "procact_id" => $procact_ids[$key],
              "created_at" => now(),
              "updated_at" => now()
            ]);
          }

          DB::table("resolutions")->where("type", "RRA")
            ->whereIn('resolution_projects.procact_id', $procact_ids)
            ->join('resolution_projects', 'resolutions.resolution_id', 'resolution_projects.resolution_id')
            ->update(['resolution_projects.cancelled' => 1]);
        }
      }
    }
    if ($message === "duplicate" ||  $message === "success") {
      return back()->with("message", $message);
    } else {
      return back()->withInput()->with("message", $message);
    }
  }

  public function submitMRResolution(Request $request)
  {


    $data = $request->validate([
      "resolution_number" => "required",
      "resolution_date" => "required",
      "mr_project_bid_ids" => "required",
      "succeeding_process" => "required_if:resolution_type,RGMR",
      "next_opening_date" => "required_if:succeeding_process,New Schedule"
    ]);

    $mr_project_bid_ids = explode(",", $request->input("mr_project_bid_ids"));
    $APP = new APP;
    $succeeding_process = null;
    $next_opening_date = null;
    $remarks = "Resolution Granting The Motion for Reconsideration";

    $governor = DB::table("governors")->orderBy("governor_id", "desc")->first();
    $message = "success";


    if ($request->input("resolution_type") === 'RGMR') {
      $mr_status = "granted";
      $succeeding_process = $request->input("succeeding_process");
      if ($request->input("next_opening_date") != null) {
        $next_opening_date = date("Y-m-d", strtotime($request->input("next_opening_date")));
      }
    } else {
      $mr_status = "denied";
    }


    if ($request->input("resolution_id") === null) {
      $duplicate = DB::table("resolutions")->where("resolution_number", $request->input("resolution_number"))->count();
      if ($duplicate > 0) {
        $message = "duplicate";
      } else {

        $duplicate_mr_project_bid = DB::table("resolution_mr_project_bids")->whereIn('mr_project_bid_id', $mr_project_bid_ids)->count();
        if ($duplicate_mr_project_bid > 0) {
          return back()->with('message', "reload_error");
        }

        $add = DB::table("resolutions")->insert([
          "resolution_number" => $request->input("resolution_number"),
          "resolution_date" => date("Y-m-d", strtotime($request->input('resolution_date'))),
          "type" => $request->input("resolution_type"),
          "governor_id" => $governor->governor_id,
          "succeeding_process" => $succeeding_process,
          "next_opening_date" => $next_opening_date,
          "created_at" => now(),
          "updated_at" => now()
        ]);

        if ($add) {
          $resolution = DB::table("resolutions")->where([["resolution_number", $request->input("resolution_number")], ["type", $request->input("resolution_type")]])->first();
          foreach ($mr_project_bid_ids as $mr_project_bid_id) {
            $add = DB::table("resolution_mr_project_bids")->insert([
              "resolution_id" => $resolution->resolution_id,
              "mr_project_bid_id" => $mr_project_bid_id,
              "created_at" => now(),
              "updated_at" => now()
            ]);

            $mr = DB::table('resolution_mr_project_bids')->where("resolution_mr_project_bids.mr_project_bid_id", $mr_project_bid_id)
              ->join('motion_for_reconsideration_project_bid', 'motion_for_reconsideration_project_bid.mr_project_bid_id', 'resolution_mr_project_bids.mr_project_bid_id')
              ->join('motion_for_reconsideration', 'motion_for_reconsideration.mr_id', 'motion_for_reconsideration_project_bid.mr_id')
              ->update([
                "mr_status" => $mr_status
              ]);


            if ($succeeding_process != null && $request->input("resolution_type") == "RGMR") {
              $mr_bid = DB::table('motion_for_reconsideration_project_bid')->where('mr_project_bid_id', $mr_project_bid_id)->first();
              $bidder = $APP->getBid($mr_bid->project_bid_id);

              if ($succeeding_process == "Opening") {
                if ($bidder->bid_status == "disqualified" && $bidder->procact_id == $bidder->latest_procact_id) {
                  DB::table('project_bidders')->where('project_bid', $bidder->project_bid)->update(["bid_status" => "active"]);
                  $remarks = "Bidder's status set to active after Resolution Granting Motion for Reconsideration " . $request->input("resolution_number");
                }
              } else if ($succeeding_process == "New Schedule") {

                $project_plan = DB::table('project_plans')->where('plan_id', $bidder->plan_id)->first();
                if ($bidder->bid_status == "disqualified" && $bidder->procact_id == $bidder->latest_procact_id) {
                  $old_timeline = DB::table('project_timelines')->where('procact_id', $bidder->procact_id)->first();
                  $old_activity_status = DB::table('project_activity_status')->where('procact_id', $bidder->procact_id)->first();

                  DB::table('project_activity_status')->where('procact_id', $bidder->latest_procact_id)->update([
                    "main_status" => 're-opened'
                  ]);


                  DB::table('procacts')->insert([
                    "procact_mode_id" => $bidder->mode_id,
                    "plan_id" => $bidder->plan_id,
                    "pre_proc" => $bidder->pre_proc,
                    "advertisement" => $bidder->advertisement,
                    "pre_bid" => $bidder->pre_bid,
                    "eligibility_check" => $bidder->eligibility_check,
                    "open_bid" => $next_opening_date,
                    "open_time" => "08:30",
                    'created_at' => now(),
                    'updated_at' => now()
                  ]);


                  $latest_procact = DB::table('procacts')->where('plan_id', $project_plan->plan_id)->orderBy('created_at', 'desc')->first();

                  if (isset($bidder->rfq_project_id)) {
                    $rfq_or_bid_doc = DB::table('rfq_projects')->where('rfq_project_id', $bidder->rfq_project_id)->first();
                    DB::table('rfq_projects')->insert([
                      "rfq_id" => $bidder->rfq_id,
                      "procact_id" => $latest_procact->procact_id,
                      "detailed_bid_as_read" => null,
                      "detailed_bid_as_evaluated" => null,
                      "detailed_bid_in_words" => null,
                      "detailed_initial_bid_as_evaluated" => null,
                      "detailed_discount" => null,
                      "detailed_amount_of_discount" => null,
                      "detailed_discount_type" => null,
                      "detailed_discount_source" => null,
                      "created_at" => now(),
                      "updated_at" => now(),
                    ]);
                    $latest_rfq = DB::table('rfq_projects')->where("rfq_id", $bidder->rfq_id)->latest()->first();
                    DB::table('project_bidders')->insert([
                      "rfq_project_id" =>  $latest_rfq->rfq_project_id,
                      "rfq_project_id" => null,
                      "bid_status" => "active",
                      "created_at" => now(),
                      "updated_at" => now()
                    ]);
                  } else {
                    $rfq_or_bid_doc = DB::table('bid_doc_projects')->where('bid_doc_project_id', $bidder->bid_doc_project_id)->first();
                    DB::table('bid_doc_projects')->insert([
                      "bid_doc_id" => $bidder->bid_doc_id,
                      "procact_id" => $latest_procact->procact_id,
                      "detailed_bid_as_read" => null,
                      "detailed_bid_as_evaluated" => null,
                      "detailed_bid_in_words" => null,
                      "detailed_initial_bid_as_evaluated" => null,
                      "detailed_discount" => null,
                      "detailed_amount_of_discount" => null,
                      "detailed_discount_type" => null,
                      "detailed_discount_source" => null,
                      "created_at" => now(),
                      "updated_at" => now(),
                    ]);
                    $latest_bid_doc = DB::table('bid_doc_projects')->where("bid_doc_id", $bidder->bid_doc_id)->latest()->first();
                    DB::table('project_bidders')->insert([
                      "rfq_project_id" => null,
                      "bid_doc_project_id" => $latest_bid_doc->bid_doc_project_id,
                      "bid_status" => "active",
                      "created_at" => now(),
                      "updated_at" => now()
                    ]);
                  }



                  DB::table('project_plans')->where('plan_id', $project_plan->plan_id)->update([
                    "re_bid_count" => $project_plan->re_bid_count,
                    "status" => "onprocess",
                    "current_cluster" => $project_plan->current_cluster,
                    "latest_procact_id" => $latest_procact->procact_id,
                  ]);

                  DB::table('project_timelines')->insert([
                    "plan_id" => $project_plan->plan_id,
                    "procact_id" => $latest_procact->procact_id,
                    "timeline_status" => "set",
                    "pre_proc_date" => $old_timeline->pre_proc_date,
                    "advertisement_start" => $old_timeline->advertisement_start,
                    "advertisement_end" => $old_timeline->advertisement_end,
                    "pre_bid_start" => $old_timeline->pre_bid_start,
                    "pre_bid_end" => $old_timeline->pre_bid_end,
                    "bid_submission_start" => $old_timeline->bid_submission_start,
                    "bid_submission_end" => $old_timeline->bid_submission_end,
                    "bid_evaluation_start" => $old_timeline->bid_evaluation_start,
                    "bid_evaluation_end" => $old_timeline->bid_evaluation_end,
                    "post_qualification_start" => $old_timeline->post_qualification_start,
                    "post_qualification_end" => $old_timeline->post_qualification_end,
                    "award_notice_start" => $old_timeline->award_notice_start,
                    "award_notice_end" => $old_timeline->award_notice_end,
                    "contract_signing_start" => $old_timeline->contract_signing_start,
                    "contract_signing_end" => $old_timeline->contract_signing_end,
                    "authority_approval_start" => $old_timeline->authority_approval_start,
                    "authority_approval_end" => $old_timeline->authority_approval_end,
                    "proceed_notice_start" => $old_timeline->proceed_notice_start,
                    "proceed_notice_end" => $old_timeline->proceed_notice_end,
                    'created_at' => now(),
                    'updated_at' => now()
                  ]);


                  $message = $APP->extendSpecificProcess($project_plan->plan_id, "submission_opening", $request->input("next_opening_date"), "Automatic New Opening From Resolution Granting Motion for Reconsideration");

                  DB::table('project_activity_status')->insert([
                    "procact_id" => $latest_procact->procact_id,
                    "plan_id" => $project_plan->plan_id,
                    "main_status" => "pending",
                    "pre_proc" => $old_activity_status->pre_proc,
                    "advertisement" => $old_activity_status->advertisement,
                    "pre_bid" => $old_activity_status->pre_bid,
                    "eligibility_check" => $old_activity_status->eligibility_check,
                    "open_bid" => $old_activity_status->open_bid,
                    "bid_evaluation" => $old_activity_status->bid_evaluation,
                    "post_qual" => $old_activity_status->post_qual,
                    "award_notice" => $old_activity_status->award_notice,
                    "contract_signing" => $old_activity_status->contract_signing,
                    "authority_approval" => $old_activity_status->authority_approval,
                    "proceed_notice" => $old_activity_status->proceed_notice,
                    'created_at' => now(),
                    'updated_at' => now()
                  ]);

                  $remarks = "Automatic New Opening Schedule after Resolution Granting Motion for Reconsideration " . $request->input("resolution_number");
                }
              } else if ($succeeding_process == "Post Qualification") {
                if ($bidder->bid_status == "non-responsive" || $bidder->procact_id == $bidder->latest_procact_id) {
                  DB::table('project_bidders')->where('project_bid', $bidder->project_bid)->update(["bid_status" => "active"]);
                  $remarks = "Bidder's status set to active after Resolution Granting Motion for Reconsideration " . $request->input("resolution_number");
                }
              } else {
                if ($bidder->bid_status == "non-responsive" || $bidder->procact_id == $bidder->latest_procact_id) {
                  // edit here
                  DB::table('project_plans')->where('latest_procact_id', $bidder->procact_id)->update(["project_bid_id" => $bidder->project_bid]);
                  DB::table('procacts')->where('procact_id', $bidder->procact_id)->update(["post_qual" => $bidder->post_qual_end]);
                  DB::table('project_activity_satatus')->where('procact_id', $bidder->procact_id)->update(["post_qual" => "finished"]);
                  DB::table('project_bidders')->where('project_bid', $bidder->project_bid)->update(["bid_status" => "responsive"]);

                  $log = "Bidder's Status Set to Responsive";
                  $remarks = "Bidder's status set to Responsive after Resolution Granting Motion for Reconsideration " . $request->input("resolution_number");
                }
              }



              DB::table('disqualification_records')->insert([
                'project_bid'  => $bidder->project_bid,
                'remarks'  => 'RGMR: ' . $remarks,
                'user_id'  => Auth::user()->id,
                'created_at'  => now(),
                'updated_at' => now()
              ]);
            }
          }
        }
      }
    } else {
      $duplicate = DB::table("resolutions")->where([["resolution_number", $request->input("resolution_number")], ["resolution_id", '<>', $request->input("resolution_id")]])->count();
      if ($duplicate > 0) {
        $message = "duplicate";
      } else {

        $update = DB::table("resolutions")
          ->where([["resolution_id", $request->input("resolution_id")]])
          ->update([
            "resolution_number" => $request->input("resolution_number"),
            "resolution_date" => date("Y-m-d", strtotime($request->input('resolution_date'))),
            "type" => $request->input("resolution_type"),
            "governor_id" => $governor->governor_id,
            "succeeding_process" => $succeeding_process,
            "next_opening_date" => $next_opening_date,
            "updated_at" => now()
          ]);

        $delete = DB::table("resolution_mr_project_bids")->where("resolution_id", $request->input("resolution_id"))->delete();

        foreach ($mr_project_bid_ids as $mr_project_bid_id) {
          $add = DB::table("resolution_mr_project_bids")->insert([
            "resolution_id" => $request->input("resolution_id"),
            "mr_project_bid_id" => $mr_project_bid_id,
            "updated_at" => now()
          ]);

          $mr = DB::table('resolution_mr_project_bids')->where("resolution_mr_project_bids.mr_project_bid_id", $mr_project_bid_id)
            ->join('motion_for_reconsideration_project_bid', 'motion_for_reconsideration_project_bid.mr_project_bid_id', 'resolution_mr_project_bids.mr_project_bid_id')
            ->join('motion_for_reconsideration', 'motion_for_reconsideration.mr_id', 'motion_for_reconsideration_project_bid.mr_id')
            ->update([
              "mr_status" => $mr_status
            ]);

          if ($succeeding_process != null && $request->input("resolution_type") == "RGMR") {
            $mr_bid = DB::table('motion_for_reconsideration_project_bid')->where('mr_project_bid_id', $mr_project_bid_id)->first();
            $bidder = $APP->getBid($mr_bid->project_bid_id);

            if ($succeeding_process == "Opening") {
              if ($bidder->bid_status == "disqualified" && $bidder->procact_id == $bidder->latest_procact_id) {
                DB::table('project_bidders')->where('project_bid', $bidder->project_bid)->update(["bid_status" => "active"]);
                $remarks = "Bidder's status set to active after Resolution Granting Motion for Reconsideration " . $request->input("resolution_number");
              }
            } else if ($succeeding_process == "New Schedule") {

              $project_plan = DB::table('project_plans')->where('plan_id', $bidder->plan_id)->first();
              if ($bidder->bid_status == "disqualified" && $bidder->procact_id == $bidder->latest_procact_id) {
                $old_timeline = DB::table('project_timelines')->where('procact_id', $bidder->procact_id)->first();
                $old_activity_status = DB::table('project_activity_status')->where('procact_id', $bidder->procact_id)->first();

                DB::table('project_activity_status')->where('procact_id', $bidder->latest_procact_id)->update([
                  "main_status" => 're-opened'
                ]);


                DB::table('procacts')->insert([
                  "procact_mode_id" => $bidder->mode_id,
                  "plan_id" => $bidder->plan_id,
                  "pre_proc" => $bidder->pre_proc,
                  "advertisement" => $bidder->advertisement,
                  "pre_bid" => $bidder->pre_bid,
                  "eligibility_check" => $bidder->eligibility_check,
                  "open_bid" => $next_opening_date,
                  "open_time" => "08:30",
                  'created_at' => now(),
                  'updated_at' => now()
                ]);


                $latest_procact = DB::table('procacts')->where('plan_id', $project_plan->plan_id)->orderBy('created_at', 'desc')->first();

                if (isset($bidder->rfq_project_id)) {
                  $rfq_or_bid_doc = DB::table('rfq_projects')->where('rfq_project_id', $bidder->rfq_project_id)->first();
                  DB::table('rfq_projects')->insert([
                    "rfq_id" => $bidder->rfq_id,
                    "procact_id" => $latest_procact->procact_id,
                    "detailed_bid_as_read" => null,
                    "detailed_bid_as_evaluated" => null,
                    "detailed_bid_in_words" => null,
                    "detailed_initial_bid_as_evaluated" => null,
                    "detailed_discount" => null,
                    "detailed_amount_of_discount" => null,
                    "detailed_discount_type" => null,
                    "detailed_discount_source" => null,
                    "created_at" => now(),
                    "updated_at" => now(),
                  ]);
                  $latest_rfq = DB::table('rfq_projects')->where("rfq_id", $bidder->rfq_id)->latest()->first();
                  DB::table('project_bidders')->insert([
                    "rfq_project_id" =>  $latest_rfq->rfq_project_id,
                    "rfq_project_id" => null,
                    "bid_status" => "active",
                    "created_at" => now(),
                    "updated_at" => now()
                  ]);
                } else {
                  $rfq_or_bid_doc = DB::table('bid_doc_projects')->where('bid_doc_project_id', $bidder->bid_doc_project_id)->first();
                  DB::table('bid_doc_projects')->insert([
                    "bid_doc_id" => $bidder->bid_doc_id,
                    "procact_id" => $latest_procact->procact_id,
                    "detailed_bid_as_read" => null,
                    "detailed_bid_as_evaluated" => null,
                    "detailed_bid_in_words" => null,
                    "detailed_initial_bid_as_evaluated" => null,
                    "detailed_discount" => null,
                    "detailed_amount_of_discount" => null,
                    "detailed_discount_type" => null,
                    "detailed_discount_source" => null,
                    "created_at" => now(),
                    "updated_at" => now(),
                  ]);
                  $latest_bid_doc = DB::table('bid_doc_projects')->where("bid_doc_id", $bidder->bid_doc_id)->latest()->first();
                  DB::table('project_bidders')->insert([
                    "rfq_project_id" => null,
                    "bid_doc_project_id" => $latest_bid_doc->bid_doc_project_id,
                    "bid_status" => "active",
                    "created_at" => now(),
                    "updated_at" => now()
                  ]);
                }



                DB::table('project_plans')->where('plan_id', $project_plan->plan_id)->update([
                  "re_bid_count" => $project_plan->re_bid_count,
                  "status" => "onprocess",
                  "current_cluster" => $project_plan->current_cluster,
                  "latest_procact_id" => $latest_procact->procact_id,
                ]);

                DB::table('project_timelines')->insert([
                  "plan_id" => $project_plan->plan_id,
                  "procact_id" => $latest_procact->procact_id,
                  "timeline_status" => "set",
                  "pre_proc_date" => $old_timeline->pre_proc_date,
                  "advertisement_start" => $old_timeline->advertisement_start,
                  "advertisement_end" => $old_timeline->advertisement_end,
                  "pre_bid_start" => $old_timeline->pre_bid_start,
                  "pre_bid_end" => $old_timeline->pre_bid_end,
                  "bid_submission_start" => $old_timeline->bid_submission_start,
                  "bid_submission_end" => $old_timeline->bid_submission_end,
                  "bid_evaluation_start" => $old_timeline->bid_evaluation_start,
                  "bid_evaluation_end" => $old_timeline->bid_evaluation_end,
                  "post_qualification_start" => $old_timeline->post_qualification_start,
                  "post_qualification_end" => $old_timeline->post_qualification_end,
                  "award_notice_start" => $old_timeline->award_notice_start,
                  "award_notice_end" => $old_timeline->award_notice_end,
                  "contract_signing_start" => $old_timeline->contract_signing_start,
                  "contract_signing_end" => $old_timeline->contract_signing_end,
                  "authority_approval_start" => $old_timeline->authority_approval_start,
                  "authority_approval_end" => $old_timeline->authority_approval_end,
                  "proceed_notice_start" => $old_timeline->proceed_notice_start,
                  "proceed_notice_end" => $old_timeline->proceed_notice_end,
                  'created_at' => now(),
                  'updated_at' => now()
                ]);


                $message = $APP->extendSpecificProcess($project_plan->plan_id, "submission_opening", $request->input("next_opening_date"), "Automatic New Opening From Resolution Granting Motion for Reconsideration");

                DB::table('project_activity_status')->insert([
                  "procact_id" => $latest_procact->procact_id,
                  "plan_id" => $project_plan->plan_id,
                  "main_status" => "pending",
                  "pre_proc" => $old_activity_status->pre_proc,
                  "advertisement" => $old_activity_status->advertisement,
                  "pre_bid" => $old_activity_status->pre_bid,
                  "eligibility_check" => $old_activity_status->eligibility_check,
                  "open_bid" => $old_activity_status->open_bid,
                  "bid_evaluation" => $old_activity_status->bid_evaluation,
                  "post_qual" => $old_activity_status->post_qual,
                  "award_notice" => $old_activity_status->award_notice,
                  "contract_signing" => $old_activity_status->contract_signing,
                  "authority_approval" => $old_activity_status->authority_approval,
                  "proceed_notice" => $old_activity_status->proceed_notice,
                  'created_at' => now(),
                  'updated_at' => now()
                ]);

                $remarks = "Automatic New Opening Schedule after Resolution Granting Motion for Reconsideration " . $request->input("resolution_number");
              }
            } else if ($succeeding_process == "Post Qualification") {
              if ($bidder->bid_status == "non-responsive" || $bidder->procact_id == $bidder->latest_procact_id) {
                DB::table('project_bidders')->where('project_bid', $bidder->project_bid)->update(["bid_status" => "active"]);
                $remarks = "Bidder's status set to active after Resolution Granting Motion for Reconsideration " . $request->input("resolution_number");
              }
            } else {
              if ($bidder->bid_status == "non-responsive" || $bidder->procact_id == $bidder->latest_procact_id) {
                DB::table('project_bidders')->where('project_bid', $bidder->project_bid)->update(["bid_status" => "responsive"]);

                $log = "Bidder's Status Set to Responsive";
                $remarks = "Bidder's status set to Responsive after Resolution Granting Motion for Reconsideration " . $request->input("resolution_number");
              }
            }



            DB::table('disqualification_records')->insert([
              'project_bid'  => $bidder->project_bid,
              'remarks'  => 'RGMR: ' . $remarks,
              'user_id'  => Auth::user()->id,
              'created_at'  => now(),
              'updated_at' => now()
            ]);
          }
        }
      }
    }

    return back()->with("message", $message);
  }

  public function generateCCA($id)
  {
    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $resolution = DB::table("resolutions")->where("resolution_id", $id)->join("governors", "governors.governor_id", "resolutions.governor_id")->first();

    if ($resolution == null) {
      abort(403, 'Invalid Resolution.');
    } else {
      $governor = $resolution->name;
      $resolution_date = date("F d,Y", strtotime($resolution->resolution_date));
      $date_format = date("jS", strtotime($resolution->resolution_date)) . " day of " . date("F, Y", strtotime($resolution->resolution_date));
      $APP = new APP;
      $bac = DB::table('bids_and_awards_committee')
        ->select(
          'bids_and_awards_committee.*',
          DB::raw("CONCAT(bac_ch.member_prefix,' ',bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name_prefix"),
          DB::raw("CONCAT(bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name"),
          DB::raw("CONCAT(bac_vice_ch.member_prefix,' ',bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name_prefix"),
          DB::raw("CONCAT(bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name"),
          DB::raw("CONCAT(bac_alternate_vice_ch.member_fname,' ',if(bac_alternate_vice_ch.member_minitial is null ,'',bac_alternate_vice_ch.member_minitial),' ',bac_alternate_vice_ch.member_lname) AS bac_alternate_vice_chairman_name"),
          DB::raw("CONCAT(bac_sec_ch.member_fname,' ',if(bac_sec_ch.member_minitial is null ,'',bac_sec_ch.member_minitial),' ',bac_sec_ch.member_lname) AS bac_sec_chairman_name"),
          DB::raw("CONCAT(bac_sec_vice_ch.member_fname,' ',if(bac_sec_vice_ch.member_minitial is null ,'',bac_sec_vice_ch.member_minitial),' ',bac_sec_vice_ch.member_lname) AS bac_sec_vice_chairman_name"),
          DB::raw("CONCAT(bac_twg_ch.member_fname,' ',if(bac_twg_ch.member_minitial is null ,'',bac_twg_ch.member_minitial),' ',bac_twg_ch.member_lname) AS bac_twg_chairman_name"),
          DB::raw("CONCAT(bac_twg_vice_ch.member_fname,' ',if(bac_twg_vice_ch.member_minitial is null ,'',bac_twg_vice_ch.member_minitial),' ',bac_twg_vice_ch.member_lname) AS bac_twg_vice_chairman_name")
        )
        ->join('member as bac_ch', 'bac_ch.member_id', '=', 'bids_and_awards_committee.bac_chairman')
        ->join('member as bac_vice_ch', 'bac_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_vice_chairman')
        ->leftJoin('member as bac_alternate_vice_ch', 'bac_alternate_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_alternate_vice_chairman')
        ->join('member as bac_sec_ch', 'bac_sec_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_chairman')
        ->join('member as bac_sec_vice_ch', 'bac_sec_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_vice_chairman')
        ->join('member as bac_twg_ch', 'bac_twg_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_chairman')
        ->join('member as bac_twg_vice_ch', 'bac_twg_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_vice_chairman')
        ->orderBy('bac_id', 'desc')
        ->first();

      $bac_infra_members = DB::table('bac_member')->where('bac_id', $bac->bac_id)
        ->select(DB::raw("CONCAT(member.member_fname,' ',if(member.member_minitial is null ,'',member.member_minitial),' ',member.member_lname) AS member_name"), 'member_prefix')
        ->where('bac_member.bac_member_type', 'BAC Infrastructure Member')
        ->join('member', 'member.member_id', '=', 'bac_member.member_id')->orderBy('bac_member.bac_member_arrangement', 'asc')->get();



      $bac_members = [];
      array_push($bac_members, ["name" => $bac->bac_chairman_name, "position" => "Chairperson &amp; Presiding Officer"]);
      array_push($bac_members, ["name" => $bac->bac_vice_chairman_name, "position" => "Vice-Chairperson"]);
      $bac_names = strtoupper(strtolower($bac->bac_chairman_name)) . ", Chairperson &amp; Presiding Officer <w:br/>" . strtoupper(strtolower($bac->bac_vice_chairman_name)) . ", Vice-Chairperson  <w:br/>";
      foreach ($bac_infra_members as $member) {
        array_push($bac_members, ["name" => $member->member_name, "position" => "Member"]);
        $bac_names = $bac_names . strtoupper(strtolower($member->member_prefix . ' ' . $member->member_name)) . ", Member <w:br/>";
      }
      $member_rows = ceil(count($bac_members) / 2);



      $project_plans = DB::table("resolution_projects")->where("resolution_id", $id)
        ->join('procacts', 'procacts.procact_id', 'resolution_projects.procact_id')
        ->join("project_plans", "procacts.plan_id", "project_plans.plan_id")
        ->leftJoin("project_bidders", "project_plans.project_bid_id", "project_bidders.project_bid")
        ->join("municipalities", "project_plans.municipality_id", "municipalities.municipality_id")
        ->join("project_timelines", "project_timelines.procact_id", "procacts.procact_id")
        ->join("funds", "project_plans.fund_id", "funds.fund_id")
        ->leftJoin("barangays", "project_plans.barangay_id", "barangays.barangay_id")
        ->orderBy("municipality_name", "asc")
        ->orderBy("procacts.itb_arrangement", "asc")
        ->get();

      if (count($project_plans) === 0) {
        return abort(404);
      } else {
        $templateProcessor = new TemplateProcessor(public_path("word_templates/rrc.docx"));
        $templateProcessor->setValue('bac', $bac_names);
        $templateProcessor->setValue('bac_sec', strtoupper(strtolower($bac->bac_sec_chairman_name)));
        $templateProcessor->cloneRow('member1', $member_rows);
        $member_counter = 1;
        for ($i = 1; $i <= $member_rows; $i++) {
          $templateProcessor->setValue('member1#' . $i, strtoupper(strtolower($bac_members[$member_counter - 1]['name'])));
          $templateProcessor->setValue('position1#' . $i, $bac_members[$member_counter - 1]['position']);
          $member_counter = $member_counter + 1;
          if ($member_counter <= count($bac_members)) {
            $templateProcessor->setValue('member2#' . $i, strtoupper(strtolower($bac_members[$member_counter - 1]['name'])));
            $templateProcessor->setValue('position2#' . $i, $bac_members[$member_counter - 1]['position']);
            $member_counter = $member_counter + 1;
          } else {
            $templateProcessor->setValue('member2#' . $i, '');
            $templateProcessor->setValue('position2#' . $i, '');
          }
        }
        $ids = [];
        $titles = [];
        $locations = [];
        $project_numbers = [];
        $sources = [];
        $project_costs = [];
        $bidders_array = [];
        $business_names = [];
        $minimum_costs = [];
        $ranks = [];
        $modes = [];
        $duration = [];

        foreach ($project_plans as $project_plan) {
          if (in_array($project_plan->plan_id, $ids) == false) {
            $bidders = $APP->getBiddersData($project_plan->latest_procact_id, 'responsive,active,non-responsive,disapproved,withdrawn');
            $concat_project_cost = "";
            $title = null;

            if ($project_plan->plan_cluster_id != null) {

              if (in_array($project_plan->plan_id, $ids) === false) {
                $cluster_apps = DB::table("project_plans")->where([["procacts.plan_cluster_id", $project_plan->plan_cluster_id]])
                  ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
                  ->join("funds", "project_plans.fund_id", "funds.fund_id")
                  ->orderBy("procacts.itb_arrangement", "asc")
                  ->get();


                $responsive_bidder = "";
                $cd = 0.00;
                $cluster_letter = "";
                $source = "";
                $project_number = "";
                $project_cost = "";
                foreach ($cluster_apps as $key => $cluster_app) {
                  if ($key === 0) {
                    $responsive_bidder = $APP->getBiddersData($cluster_app->procact_id, 'responsive');
                    $responsive_bidder = (array) json_decode($responsive_bidder);
                  }
                  $cluster_responsive_bidder = $APP->getBiddersData($cluster_app->procact_id, 'responsive');

                  $cluster_responsive_bidder = (array) json_decode($cluster_responsive_bidder);
                  if (count($cluster_responsive_bidder) != 0) {
                    if ($cluster_responsive_bidder[0]->business_name == $responsive_bidder[0]->business_name) {
                      if ($cluster_app->plan_id === $cluster_apps[0]->plan_id) {
                        $cluster_letter = "a";
                        $title = "" . $cluster_letter . "." . $cluster_app->project_title;
                        $project_number = "" . $cluster_letter . "." . $cluster_app->project_no;
                        $concat_project_cost = "" . $cluster_letter . ". Php" . number_format($cluster_app->project_cost, 2, '.', ',');
                        $project_cost = $cluster_app->project_cost;
                        $source = "" . $cluster_letter . "." . $cluster_app->source;
                      } else {
                        $title = $title . "  " . $cluster_letter . "." . $cluster_app->project_title;
                        $project_number = $project_number . " " . $cluster_letter . "." . $cluster_app->project_no;
                        $concat_project_cost = $concat_project_cost . " " . $cluster_letter . ". Php" . number_format($cluster_app->project_cost, 2, '.', ',');
                        $project_cost = $project_cost + $cluster_app->project_cost;
                        $source = $source . "  " . $cluster_letter . "." . $cluster_app->source;
                      }
                      $cd = $cd + (float)$cluster_app->duration;
                      $cluster_letter = ++$cluster_letter;
                      array_push($ids, $cluster_app->plan_id);
                    }
                  }
                }
              }
            } else {
              array_push($ids, $project_plan->plan_id);
              $project_number = $project_plan->project_no;
              $title = $project_plan->project_title;
              $project_cost = $project_plan->project_cost;
              $source = $project_plan->source;
              $cd = $project_plan->duration;
            }

            if ($title != null) {

              if ($project_plan->barangay_name != null) {
                $location = $project_plan->barangay_name . "," . $project_plan->municipality_name . ", Benguet";
              } else {
                $location = $project_plan->municipality_name . ",Benguet";
              }

              if ($concat_project_cost != "") {
                $project_cost = $concat_project_cost . " = Php" . number_format($project_cost, 2, '.', ',');
              } else {
                $project_cost = "Php " . number_format($project_cost, 2, '.', ',');
              }
              array_push($modes, $project_plan->mode_id);
              array_push($bidders_array, $bidders);
              array_push($titles, $title);
              array_push($project_numbers, $project_number);
              array_push($locations, $location);
              array_push($project_costs, $project_cost);
              array_push($sources, $source);
              array_push($duration, $cd);
            }
          }
        }


        //Process other template values
        $mode_id = $project_plans[0]->mode_id;
        $date_opened = $project_plans[0]->bid_submission_start;
        $advertisement_start = $project_plans[0]->advertisement_start;
        $advertisement_end = $project_plans[0]->advertisement_end;

        // Projects_opened
        $projects_opened = DB::table('project_timelines')
          ->join('procacts', 'procacts.procact_id', 'project_timelines.procact_id')
          ->where([['procacts.procact_mode_id', $mode_id], ['project_timelines.bid_submission_start', $date_opened]])
          ->get();

        $clusters_array = [];
        $projects_opened_count = 0;
        foreach ($projects_opened as $project_opened) {
          if ($project_opened->plan_cluster_id === null) {
            $projects_opened_count = $projects_opened_count + 1;
          } else {
            if (in_array($project_opened->plan_cluster_id, $clusters_array)) {
            } else {
              array_push($clusters_array, $project_opened->plan_cluster_id);
              $projects_opened_count = $projects_opened_count + 1;
            }
          }
        }

        if ($mode_id === 1) {
          $rfq_or_itb = "Invitation to Bid";
          $bidders_or_contractors = "bidders";
          $action = " bought and filed their bid documents";
          $bidding_or_svp = "Bidding";
          $winner_with_abv = 'Lowest Calculated Responsive Bid (LCRB)';
          $lcrb_or_lcrpq = 'Lowest Calculated Responsive Bid';
          $bidder_or_invited = 'Bidder';
          $bid_or_quotation = "Bid ";
        } else if ($mode_id === 2) {
          $rfq_or_itb = "Request For Quotation";
          $bidders_or_contractors = "contractors";
          $action = "secured and filed their quotations";
          $bidding_or_svp = "Small Value Procurement";
          $winner_with_abv = 'Lowest Calculated Responsive Price Quotation (LCRPQ)';
          $lcrb_or_lcrpq = 'Lowest Calculated Responsive Price Quotation';
          $bidder_or_invited = 'Name of Interested/Invited Contractor';
          $bid_or_quotation = "Quotation ";
        } else {
          $rfq_or_itb = "Request For Quotation";
          $bidders_or_contractors = "contractors";
          $action = "secured and filed their quotations";
          $bidding_or_svp = "Negotiated Procurement";
          $winner_with_abv = 'Lowest Calculated Responsive Price Quotation (LCRPQ)';
          $lcrb_or_lcrpq = 'Lowest Calculated Responsive Price Quotation';
          $bidder_or_invited = 'Name of Interested/Invited Contractor';
          $bid_or_quotation = "Quotation ";
        }

        // Template modification
        $templateProcessor->cloneBlock('item', count($titles), true, true);
        for ($i = 0; $i < count($titles); $i++) {
          $title = str_replace('&', '&amp;', $titles[$i]);
          $count = $i + 1;
          $templateProcessor->setValue('project_title#' . $count, htmlspecialchars($title));
          $templateProcessor->setValue('bidder_or_invited#' . $count, $bidder_or_invited);
          $templateProcessor->setValue('bid_or_quotation#' . $count, $bid_or_quotation);
          $templateProcessor->setValue('count#' . $count, $count);
          $templateProcessor->setValue('item_number#' . $count, $count);
          $templateProcessor->setValue('project_number#' . $count, $project_numbers[$i]);
          $templateProcessor->setValue('project_cost#' . $count, $project_costs[$i]);
          $templateProcessor->setValue('location#' . $count, $locations[$i]);
          $templateProcessor->setValue('source#' . $count, $sources[$i]);
          $templateProcessor->setValue('duration#' . $count, $duration[$i] . " CD");
          $bidders_data = $bidders_array[$i];
          $templateProcessor->cloneRow('business_name#' . $count, count($bidders_data));
          $bidder_counter = 1;
          foreach ($bidders_data as $bidder) {

            $cluster_bids = $APP->getClusterBids($bidder->project_bid);
            if (count($cluster_bids) > 1) {
              $with_detailed_bids = 0;
              $detailed_proposed_bid = "";
              $detailed_bid_as_evaluated = "";
              $detailed_bid_as_calculated = "";
              foreach ($cluster_bids as $key => $project_bid) {
                if ($project_bid->detailed_bid_as_calculated > 0) {
                  if ($detailed_proposed_bid == "") {
                    $detailed_proposed_bid = "PHP" . number_format($project_bid->detailed_bid_as_read, 2, '.', ',');
                    $detailed_bid_as_evaluated = "PHP" . number_format($project_bid->detailed_bid_as_evaluated, 2, '.', ',');
                    $detailed_bid_as_calculated = "PHP" . number_format($project_bid->minimum_detailed_cost, 2, '.', ',');
                  } else {
                    $detailed_proposed_bid = $detailed_proposed_bid . " + PHP" . number_format($project_bid->detailed_bid_as_read, 2, '.', ',');
                    $detailed_bid_as_evaluated = $detailed_bid_as_evaluated . " + PHP" . number_format($project_bid->detailed_bid_as_evaluated, 2, '.', ',');
                    $detailed_bid_as_calculated = $detailed_bid_as_calculated . " + PHP" . number_format($project_bid->minimum_detailed_cost, 2, '.', ',');
                  }
                  ++$with_detailed_bids;
                }
              }

              if (count($cluster_bids) == $with_detailed_bids) {
                $bid_as_read = $detailed_proposed_bid . " = PHP" . number_format($bidder->proposed_bid, 2, '.', ',');
                $bid_as_evaluated = $detailed_bid_as_evaluated . " = PHP" . number_format($bidder->bid_as_evaluated, 2, '.', ',');
                $bid_as_calculated = $detailed_bid_as_calculated . " = PHP" . number_format($bidder->bid_as_evaluated, 2, '.', ',');
                // if($bidder->twg_final_bid_evaluation!=null || $bidder->twg_final_bid_evaluation>=1){
                //   $bid_as_evaluated=$bid_as_evaluated;
                // }
              } else {
                $bid_as_read = 'Php ' . number_format((float)$bidder->proposed_bid, 2, '.', ',');
                if ($bidder->twg_final_bid_evaluation == null || $bidder->twg_final_bid_evaluation < 1) {
                  $bid_as_evaluated = 'Php ' . number_format((float)$bidder->bid_as_evaluated, 2, '.', ',');
                } else {
                  $bid_as_evaluated = 'Php ' . number_format((float)$bidder->bid_as_evaluated, 2, '.', ',');
                }
              }
            } else {
              $bid_as_read = 'Php ' . number_format((float)$bidder->proposed_bid, 2, '.', ',');
              if ($bidder->twg_final_bid_evaluation == null || $bidder->twg_final_bid_evaluation < 1) {
                $bid_as_evaluated = 'Php ' . number_format((float)$bidder->bid_as_evaluated, 2, '.', ',');
              } else {
                $bid_as_evaluated = 'Php ' . number_format((float)$bidder->bid_as_evaluated, 2, '.', ',');
              }
            }

            $templateProcessor->setValue('business_name#' . $count . "#" . $bidder_counter, htmlspecialchars($bidder->business_name));
            $templateProcessor->setValue('owner#' . $count . "#" . $bidder_counter, htmlspecialchars($bidder->owner));
            if (count($bidders_data) === 1) {
              if ($modes[$i] === 1) {
                $rank = "Lone Bidder";
              } else {
                $rank = "Lone Quotation";
              }
            } else {
              if ($modes[$i] === 1) {
                $label = "LCB";
              } else {
                $label = "LCPQ";
              }
              $rank = $bidder_counter . date("S", mktime(0, 0, 0, 0, $bidder_counter, 0)) . " " . $label;
            }
            if ($bidder->bid_status === "responsive") {
              $remarks = "Responsive";
            } else if ($bidder->bid_status === "non-responsive") {
              $remarks = "Non-responsive";
            } else if ($bidder->bid_status === "active") {
              $remarks = "Fund has expired: Please Verify Status of Project";
            } else {
              // $remarks = $bidder_counter . date("S", mktime(0, 0, 0, 0, $bidder_counter, 0)) . " " . $label;
            }
            $templateProcessor->setValue('remarks#' . $count . "#" . $bidder_counter, $remarks);
            $templateProcessor->setValue('rank#' . $count . "#" . $bidder_counter, $rank);
            $templateProcessor->setValue('bid_as_read#' . $count . "#" . $bidder_counter, $bid_as_read);

            $templateProcessor->setValue('bid_as_evaluated#' . $count . "#" . $bidder_counter, $bid_as_evaluated);

            $bidder_counter = $bidder_counter + 1;
          }
        }


        $templateProcessor->setValue('resolution_number', $resolution->resolution_number);
        $templateProcessor->setValue('rfq_or_itb', $rfq_or_itb);
        $templateProcessor->setValue('period', date("F j, Y", strtotime($advertisement_start)) . " - " . date("F j, Y", strtotime($advertisement_end)));
        $templateProcessor->setValue('project_num', $projects_opened_count);
        $templateProcessor->setValue('project_num_words', $formatter->format($projects_opened_count));
        $templateProcessor->setValue('bidders_or_contractors', $bidders_or_contractors);
        $templateProcessor->setValue('action', $action);
        $templateProcessor->setValue('date_opened', date("F j, Y", strtotime($date_opened)));
        $templateProcessor->setValue('bidding_or_svp', $bidding_or_svp);
        $templateProcessor->setValue('winner_with_abv', $winner_with_abv);
        $templateProcessor->setValue('winner_with_abv1', $winner_with_abv);
        $templateProcessor->setValue('lcrb_or_lcrpq', $lcrb_or_lcrpq);
        $templateProcessor->setValue('lcrb_or_lcrpq_bold', strtoupper(strtolower($lcrb_or_lcrpq)));
        $templateProcessor->setValue('num_award_in_words', strtoupper($formatter->format(count($titles))));
        if (count($titles) > 1) {
          $templateProcessor->setValue('project_or_projects', "projects");
        } else {
          $templateProcessor->setValue('project_or_projects', "project");
        }
        $templateProcessor->setValue('num_award', count($titles));
        $templateProcessor->setValue('resolution_date', strtoupper($resolution_date));
        $templateProcessor->setValue('resolution_date', "");
        $templateProcessor->setValue('date_format', $date_format);
        $templateProcessor->setValue('governor', strtoupper($governor));
        $templateProcessor->saveAs($this->getPublicPath() . 'word_results/rrc.docx');
        return  response()->download($this->getPublicPath() . 'word_results/rrc.docx')->deleteFileAfterSend(true);
      }
    }
  }

  public function generateRDF($id)
  {

    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $resolution = DB::table("resolutions")->where("resolution_id", $id)->join("governors", "governors.governor_id", "resolutions.governor_id")->first();

    if ($resolution == null) {
      abort(403, 'Invalid Resolution.');
    } else {

      $governor = $resolution->name;
      $resolution_date = date("F d,Y", strtotime($resolution->resolution_date));
      $date_format = date("jS", strtotime($resolution->resolution_date)) . " day of " . date("F, Y", strtotime($resolution->resolution_date));
      $APP = new APP;
      $project_plans = DB::table("resolution_projects")->where("resolution_id", $id)
        ->join('procacts', 'procacts.procact_id', 'resolution_projects.procact_id')
        ->join("project_plans", "procacts.plan_id", "project_plans.plan_id")
        ->leftJoin("project_bidders", "project_plans.project_bid_id", "project_bidders.project_bid")
        ->join("municipalities", "project_plans.municipality_id", "municipalities.municipality_id")
        ->join("project_timelines", "project_timelines.procact_id", "procacts.procact_id")
        ->join("funds", "project_plans.fund_id", "funds.fund_id")
        ->leftJoin("barangays", "project_plans.barangay_id", "barangays.barangay_id")
        ->orderBy("municipality_name", "asc")
        ->orderBy("procacts.itb_arrangement", "asc")
        ->get();

      $bac = DB::table('bids_and_awards_committee')
        ->select(
          'bids_and_awards_committee.*',
          DB::raw("CONCAT(bac_ch.member_prefix,' ',bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name_prefix"),
          DB::raw("CONCAT(bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name"),
          DB::raw("CONCAT(bac_vice_ch.member_prefix,' ',bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name_prefix"),
          DB::raw("CONCAT(bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name"),
          DB::raw("CONCAT(bac_alternate_vice_ch.member_fname,' ',if(bac_alternate_vice_ch.member_minitial is null ,'',bac_alternate_vice_ch.member_minitial),' ',bac_alternate_vice_ch.member_lname) AS bac_alternate_vice_chairman_name"),
          DB::raw("CONCAT(bac_sec_ch.member_fname,' ',if(bac_sec_ch.member_minitial is null ,'',bac_sec_ch.member_minitial),' ',bac_sec_ch.member_lname) AS bac_sec_chairman_name"),
          DB::raw("CONCAT(bac_sec_vice_ch.member_fname,' ',if(bac_sec_vice_ch.member_minitial is null ,'',bac_sec_vice_ch.member_minitial),' ',bac_sec_vice_ch.member_lname) AS bac_sec_vice_chairman_name"),
          DB::raw("CONCAT(bac_twg_ch.member_fname,' ',if(bac_twg_ch.member_minitial is null ,'',bac_twg_ch.member_minitial),' ',bac_twg_ch.member_lname) AS bac_twg_chairman_name"),
          DB::raw("CONCAT(bac_twg_vice_ch.member_fname,' ',if(bac_twg_vice_ch.member_minitial is null ,'',bac_twg_vice_ch.member_minitial),' ',bac_twg_vice_ch.member_lname) AS bac_twg_vice_chairman_name")
        )
        ->join('member as bac_ch', 'bac_ch.member_id', '=', 'bids_and_awards_committee.bac_chairman')
        ->join('member as bac_vice_ch', 'bac_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_vice_chairman')
        ->leftJoin('member as bac_alternate_vice_ch', 'bac_alternate_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_alternate_vice_chairman')
        ->join('member as bac_sec_ch', 'bac_sec_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_chairman')
        ->join('member as bac_sec_vice_ch', 'bac_sec_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_vice_chairman')
        ->join('member as bac_twg_ch', 'bac_twg_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_chairman')
        ->join('member as bac_twg_vice_ch', 'bac_twg_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_vice_chairman')
        ->orderBy('bac_id', 'desc')
        ->first();

      $bac_infra_members = DB::table('bac_member')->where('bac_id', $bac->bac_id)
        ->select(DB::raw("CONCAT(member.member_fname,' ',if(member.member_minitial is null ,'',member.member_minitial),' ',member.member_lname) AS member_name"), 'member_prefix')
        ->where('bac_member.bac_member_type', 'BAC Infrastructure Member')
        ->join('member', 'member.member_id', '=', 'bac_member.member_id')->orderBy('bac_member.bac_member_arrangement', 'asc')->get();



      $bac_members = [];
      array_push($bac_members, ["name" => $bac->bac_chairman_name, "position" => "Chairperson &amp; Presiding Officer"]);
      array_push($bac_members, ["name" => $bac->bac_vice_chairman_name, "position" => "Vice-Chairperson"]);
      $bac_names = strtoupper(strtolower($bac->bac_chairman_name_prefix)) . ", Chairperson &amp; Presiding Officer <w:br/>" . strtoupper(strtolower($bac->bac_vice_chairman_name_prefix)) . ", Vice-Chairperson  <w:br/>";
      foreach ($bac_infra_members as $member) {
        array_push($bac_members, ["name" => $member->member_name, "position" => "Member"]);
        $bac_names = $bac_names . strtoupper(strtolower($member->member_prefix . ' ' . $member->member_name)) . ", Member <w:br/>";
      }
      $member_rows = ceil(count($bac_members) / 2);



      $project_plans = DB::table("resolution_projects")->where("resolution_id", $id)
        ->join('procacts', 'procacts.procact_id', 'resolution_projects.procact_id')
        ->join("project_plans", "procacts.plan_id", "project_plans.plan_id")
        ->leftJoin("project_bidders", "project_plans.project_bid_id", "project_bidders.project_bid")
        ->join("municipalities", "project_plans.municipality_id", "municipalities.municipality_id")
        ->join("project_timelines", "project_timelines.procact_id", "procacts.procact_id")
        ->join("funds", "project_plans.fund_id", "funds.fund_id")
        ->leftJoin("barangays", "project_plans.barangay_id", "barangays.barangay_id")
        ->orderBy("municipality_name", "asc")
        ->orderBy("procacts.itb_arrangement", "asc")
        ->get();

      if (count($project_plans) === 0) {
        abort(403, 'No Projects for this resolution');
      } else {
        $procact_id = $project_plans[0]->latest_procact_id;
        $non_responsive = $APP->getActiveBidders($procact_id, 'non-responsive');
        $disqualified = $APP->getActiveBidders($procact_id, 'disqualified');
        $governor = $resolution->name;
        $advertisement_start = $project_plans[0]->advertisement_start;
        $advertisement_end = $project_plans[0]->advertisement_end;
        $period = date("F j, Y", strtotime($advertisement_start)) . " - " . date("F j, Y", strtotime($advertisement_end));
        $resolution_number = $resolution->resolution_number;
        $resolution_date = date("F d,Y", strtotime($resolution->resolution_date));
        $date_format = date("jS", strtotime($resolution->resolution_date)) . " day of " . date("F, Y", strtotime($resolution->resolution_date));
        $mode_id = $project_plans[0]->mode_id;
        $date_opened = date("F d,Y", strtotime($project_plans[0]->open_bid));
        $ymd_date_opened = $project_plans[0]->open_bid;
        $ids = [];
        $titles = [];
        $locations = [];
        $project_numbers = [];
        $sources = [];
        $project_costs = [];
        $modes = [];
        $item_numbers = [];
        $bidders_array = [];
        $initial_item = 0;

        if ($mode_id === 1) {
          $bidding_or_svp = "BIDDING";
          $itb_or_rfq = "an Invitation To Bid";
          $bidding_or_quotation = "bidding";
          $action = " bought and submitted their bid proposal";
          $label = "LCB";
        } else if ($mode_id === 12) {
          $bidding_or_svp = "SMALL VALUE PROCUREMENT";
          $itb_or_rfq = "a Request For Quotation";
          $bidding_or_quotation = "quotation";
          $action = " filed and submitted their quotations";
          $label = "LCPQ";
        } else {
          $bidding_or_svp = "NEGOTIATED PROCUREMENT";
          $itb_or_rfq = "a Request For Quotation";
          $bidding_or_quotation = "quotation";
          $action = " filed and submitted their quotations";
          $label = "LCPQ";
        }


        foreach ($project_plans as $project_plan) {
          if (in_array($project_plan->plan_id, $ids) == false) {
            $non_responsive_bidders = $APP->getBiddersData($project_plan->latest_procact_id, 'non-responsive,disqualified');
            $bidders = $APP->getBiddersData($project_plan->latest_procact_id, 'responsive,active,non-responsive,disapproved,withdrawn,disqualified');
            $concat_project_cost = "";
            $title = null;

            if ($project_plan->plan_cluster_id != null) {
              if (in_array($project_plan->plan_id, $ids) === false) {
                $cluster_apps = DB::table("project_plans")->where([["procacts.plan_cluster_id", $project_plan->plan_cluster_id]])
                  ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
                  ->join("funds", "project_plans.fund_id", "funds.fund_id")
                  ->orderBy("procacts.itb_arrangement", "asc")
                  ->get();
                $cluster_letter = "";
                $project_number = "";
                $source = "";
                $project_cost = "";
                foreach ($cluster_apps as $key => $cluster_app) {
                  if ($cluster_app->plan_id === $cluster_apps[0]->plan_id) {
                    $cluster_letter = "a";
                    $title = "" . $cluster_letter . "." . $cluster_app->project_title;
                    $project_number = "" . $cluster_letter . "." . $cluster_app->project_no;
                    $concat_project_cost = "" . $cluster_letter . ". Php" . number_format($cluster_app->project_cost, 2, '.', ',');
                    $project_cost = $cluster_app->project_cost;
                    $source = "" . $cluster_letter . "." . $cluster_app->source;
                  } else {
                    $title = $title . " " . $cluster_letter . "." . $cluster_app->project_title;
                    $project_number = $project_number . " " . $cluster_letter . "." . $cluster_app->project_no;
                    $concat_project_cost = $concat_project_cost . " " . $cluster_letter . ". Php" . number_format($cluster_app->project_cost, 2, '.', ',');
                    $project_cost = $project_cost + $cluster_app->project_cost;
                    $source = $source . " " . $cluster_letter . "." . $cluster_app->source;
                  }
                  $cluster_letter = ++$cluster_letter;
                  array_push($ids, $cluster_app->plan_id);
                }
              }
              $item_number = $APP->getItemNumber($project_plan->procact_id, $ymd_date_opened);
            } else {
              array_push($ids, $project_plan->plan_id);
              $project_number = $project_plan->project_no;
              $title = $project_plan->project_title;
              $project_cost = $project_plan->project_cost;
              $source = $project_plan->source;
              $item_number = $APP->getItemNumber($project_plan->procact_id, $ymd_date_opened);
            }


            if ($title != null) {
              if ($project_plan->barangay_name != null) {
                $location = $project_plan->barangay_name . "," . $project_plan->municipality_name . ", Benguet";
              } else {
                $location = $project_plan->municipality_name . ", Benguet";
              }

              if ($concat_project_cost != "") {
                $project_cost = $concat_project_cost . " = Php" . number_format($project_cost, 2, '.', ',');
              } else {
                $project_cost = "Php " . number_format($project_cost, 2, '.', ',');
              }
              array_push($item_numbers, $item_number);
              array_push($modes, $project_plan->mode_id);
              array_push($titles, $title);
              array_push($project_numbers, $project_number);
              array_push($locations, $location);
              array_push($project_costs, $project_cost);
              array_push($sources, $source);
              array_push($bidders_array, $non_responsive_bidders);
            }
          }
        }

        if (count($titles) > 1) {
          $aforementioned_various = "various  projects";
          $aforementioned_above = "above projects";
          $project_or_projects = "projects";
        } else {
          $aforementioned_various = " aforementioned project";
          $aforementioned_above = " aforementioned project";
          $project_or_projects = "project";
        }

        if ($non_responsive == 0 && $disqualified == 0) {
          $templateProcessor = new TemplateProcessor(public_path("word_templates/RDF-Opening.docx"));
          $templateProcessor->setValue('bac', $bac_names);
          $templateProcessor->setValue('bac_sec', strtoupper(strtolower($bac->bac_sec_chairman_name)));
          $templateProcessor->cloneRow('member1', $member_rows);
          $member_counter = 1;
          for ($i = 1; $i <= $member_rows; $i++) {
            $templateProcessor->setValue('member1#' . $i, strtoupper(strtolower($bac_members[$member_counter - 1]['name'])));
            $templateProcessor->setValue('position1#' . $i, $bac_members[$member_counter - 1]['position']);
            $member_counter = $member_counter + 1;
            if ($member_counter <= count($bac_members)) {
              $templateProcessor->setValue('member2#' . $i, strtoupper(strtolower($bac_members[$member_counter - 1]['name'])));
              $templateProcessor->setValue('position2#' . $i, $bac_members[$member_counter - 1]['position']);
              $member_counter = $member_counter + 1;
            } else {
              $templateProcessor->setValue('member2#' . $i, '');
              $templateProcessor->setValue('position2#' . $i, '');
            }
          }
          $templateProcessor->setValue('date_opened', $date_opened);
          $templateProcessor->setValue('resolution_date', strtoupper($resolution_date));
          $templateProcessor->setValue('resolution_number', strtoupper($resolution_number));
          $templateProcessor->setValue('governor', strtoupper(strtolower($governor)));
          $templateProcessor->setValue('bidding_or_svp', strtoupper(strtolower($bidding_or_svp)));
          $templateProcessor->setValue('itb_or_rfq', $itb_or_rfq);
          $templateProcessor->setValue('aforementioned_various', $aforementioned_various);
          $templateProcessor->setValue('aforementioned_above', $aforementioned_above);
          $templateProcessor->setValue('project_or_projects', $project_or_projects);
          $templateProcessor->setValue('governor', strtoupper(strtolower($governor)));
          $templateProcessor->cloneRow('item_number', count($item_numbers));
          foreach ($item_numbers as $key => $item_number) {
            $i = $key + 1;
            $templateProcessor->setValue('item_number#' . $i, $item_number);
            $templateProcessor->setValue('project_number#' . $i, $project_numbers[$key]);
            $templateProcessor->setValue('project_title#' . $i, $titles[$key]);
            $templateProcessor->setValue('location#' . $i, $locations[$key]);
            $templateProcessor->setValue('project_cost#' . $i, $project_costs[$key]);
            $templateProcessor->setValue('source#' . $i, $sources[$key]);
            $templateProcessor->setValue('period#' . $i, $period);
          }

          $templateProcessor->saveAs($this->getPublicPath() . 'word_results/' . $resolution_number . '.docx');
          return  response()->download($this->getPublicPath() . 'word_results/' . $resolution_number . '.docx')->deleteFileAfterSend(true);
        } else {
          $templateProcessor = new TemplateProcessor(public_path("word_templates/RDF-PostQual.docx"));
          $templateProcessor->setValue('bac', $bac_names);
          $templateProcessor->setValue('bac_sec', strtoupper(strtolower($bac->bac_sec_chairman_name)));
          $templateProcessor->cloneRow('member1', $member_rows);
          $member_counter = 1;
          for ($i = 1; $i <= $member_rows; $i++) {
            $templateProcessor->setValue('member1#' . $i, strtoupper(strtolower($bac_members[$member_counter - 1]['name'])));
            $templateProcessor->setValue('position1#' . $i, $bac_members[$member_counter - 1]['position']);
            $member_counter = $member_counter + 1;
            if ($member_counter <= count($bac_members)) {
              $templateProcessor->setValue('member2#' . $i, strtoupper(strtolower($bac_members[$member_counter - 1]['name'])));
              $templateProcessor->setValue('position2#' . $i, $bac_members[$member_counter - 1]['position']);
              $member_counter = $member_counter + 1;
            } else {
              $templateProcessor->setValue('member2#' . $i, '');
              $templateProcessor->setValue('position2#' . $i, '');
            }
          }
          $templateProcessor->cloneBlock('item', count($item_numbers), true, true);
          $templateProcessor->setValue('resolution_date', strtoupper($resolution_date));
          $templateProcessor->setValue('resolution_number', strtoupper($resolution_number));
          $templateProcessor->setValue('governor', strtoupper(strtolower($governor)));
          $templateProcessor->setValue('bidding_or_svp', strtoupper(strtolower($bidding_or_svp)));
          $templateProcessor->setValue('itb_or_rfq', $itb_or_rfq);
          $templateProcessor->setValue('aforementioned_various', $aforementioned_various);
          $templateProcessor->setValue('aforementioned_above', $aforementioned_above);
          $templateProcessor->setValue('project_or_projects', $project_or_projects);
          $templateProcessor->setValue('governor', strtoupper(strtolower($governor)));
          foreach ($item_numbers as $key => $item_number) {
            $i = $key + 1;
            $templateProcessor->setValue('action#' . $i, $action);
            $templateProcessor->setValue('date_opened#' . $i, $date_opened);
            $templateProcessor->setValue('item_number#' . $i, $item_number);
            $templateProcessor->setValue('project_number#' . $i, $project_numbers[$key]);
            $templateProcessor->setValue('project_title#' . $i, $titles[$key]);
            $templateProcessor->setValue('location#' . $i, $locations[$key]);
            $templateProcessor->setValue('project_cost#' . $i, $project_costs[$key]);
            $templateProcessor->setValue('source#' . $i, $sources[$key]);
            $templateProcessor->setValue('period#' . $i, $period);
            $templateProcessor->cloneRow('business_name#' . $i, count($non_responsive_bidders));
            $templateProcessor->setValue('bidders_count#' . $i, count($non_responsive_bidders));
            $templateProcessor->setValue('bidders_count_in_words#' . $i, $formatter->format(count($non_responsive_bidders)));

            if (count($non_responsive_bidders) == 1) {
              $bidders_or_bidder = "bidder";
              $action = str_replace("their", "his", $action);
            } else {
              $bidders_or_bidder = "bidders";
            }

            $action = $bidders_or_bidder . " " . $action;
            $x = 1;
            foreach ($bidders as $key => $bidder) {



              $templateProcessor->setValue('business_name#' . $i . "#" . $x, htmlspecialchars($bidder->business_name));
              $templateProcessor->setValue('bid_amount_as_eval#' . $i . "#" . $x, "Php " . number_format($bidder->bid_as_evaluated, 2, '.', ','));
              if (count($bidders) === 1 && $bidder->bid_status != "disqualified") {
                if ($label === "LCB") {
                  $templateProcessor->setValue('rank#' . $i . "#" . $x, "Lone Bidder");
                } else {
                  $templateProcessor->setValue('rank#' . $i . "#" . $x, "Lone Quotation");
                }
              } else if (count($bidders) > 1 && $bidder->bid_status != "disqualified") {
                $templateProcessor->setValue('rank#' . $i . "#" . $x, $x . date("S", mktime(0, 0, 0, 0, $x, 0)) . " " . $label);
              } else {
                $templateProcessor->setValue('rank#' . $i . "#" . $x, "Disqualified");
              }
            }
          }



          $templateProcessor->saveAs($this->getPublicPath() . 'word_results/' . $resolution_number . '.docx');
          return  response()->download($this->getPublicPath() . 'word_results/' . $resolution_number . '.docx')->deleteFileAfterSend(true);
        }
      }
    }
  }

  public function getResolutionDenyingTheMotionForReconsideration()
  {
    $year = date('Y');
    $resolutions = DB::table("resolutions")->where([["type", "RDMR"], ["resolution_date", 'like', $year . "%"]])->orderBy('resolution_id', 'desc')->get();
    $title = "Resolution Denying The Motion For Reconsideration";
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    $resolutions = getResolutionBidders($resolutions);
    return view("admin.resolutions", ['links' => $links, 'user_privilege' => $user_privilege, "resolution_type" => "RDMR", "resolutions" => $resolutions, "title" => $title, "year" => $year]);
  }

  public function getResolutionGrantingTheMotionForReconsideration()
  {
    $year = date('Y');
    $resolutions = DB::table("resolutions")->where([["type", "RGMR"], ["resolution_date", 'like', $year . "%"]])->orderBy('resolution_id', 'desc')->get();
    $title = "Resolution Granting The Motion For Reconsideration";
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    $resolutions = getResolutionBidders($resolutions);


    return view("admin.resolutions", ['links' => $links, 'user_privilege' => $user_privilege, "resolution_type" => "RGMR", "resolutions" => $resolutions, "title" => $title, "year" => $year]);
  }

  public function addResolutionDenyingTheMotionForReconsideration()
  {
    $year = null;
    $APP = new APP;
    $rfqs_motion_for_reconsiderations = DB::table('motion_for_reconsideration')
      ->select('motion_for_reconsideration.*', 'motion_for_reconsideration_project_bid.*', 'project_bidders.*', 'rfq_projects.*', 'rfqs.*', 'procacts.*', 'project_plans.*', 'procurement_modes.*', 'contractors.*', 'twg_evaluations.*')
      ->where([['resolution_mr_project_bids.mr_project_bid_id', null]])
      ->join("motion_for_reconsideration_project_bid", 'motion_for_reconsideration.mr_id', 'motion_for_reconsideration_project_bid.mr_id')
      ->join('project_bidders', 'project_bidders.project_bid', 'motion_for_reconsideration_project_bid.project_bid_id')
      ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->leftJoin('resolution_mr_project_bids', 'motion_for_reconsideration_project_bid.mr_project_bid_id', 'resolution_mr_project_bids.mr_project_bid_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
      ->get();

    $bid_docs_motion_for_reconsiderations = DB::table('motion_for_reconsideration')
      ->select('motion_for_reconsideration.*', 'motion_for_reconsideration_project_bid.*', 'project_bidders.*', 'bid_doc_projects.*', 'bid_docs.*', 'procacts.*', 'project_plans.*', 'procurement_modes.*', 'contractors.*', 'twg_evaluations.*')
      ->where([['resolution_mr_project_bids.mr_project_bid_id', null]])
      ->join("motion_for_reconsideration_project_bid", 'motion_for_reconsideration.mr_id', 'motion_for_reconsideration_project_bid.mr_id')
      ->join('project_bidders', 'project_bidders.project_bid', 'motion_for_reconsideration_project_bid.project_bid_id')
      ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->leftJoin('resolution_mr_project_bids', 'motion_for_reconsideration_project_bid.mr_project_bid_id', 'resolution_mr_project_bids.mr_project_bid_id')
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
      ->get();

    if (count($rfqs_motion_for_reconsiderations) > 0 && count($bid_docs_motion_for_reconsiderations) > 0) {
      $mrs = array_merge((array)json_decode($rfqs_motion_for_reconsiderations), (array)json_decode($bid_docs_motion_for_reconsiderations));
    } else if (count($rfqs_motion_for_reconsiderations) > 0) {
      $mrs = (array)json_decode($rfqs_motion_for_reconsiderations);
    } else if (count($bid_docs_motion_for_reconsiderations) > 0) {
      $mrs = (array)json_decode($bid_docs_motion_for_reconsiderations);
    } else {
      $mrs = [];
    }
    $links = getUserLinks();
    $user_privilege = getUserPrivilegeByLink('resolution_denying_the_motion_for_reconsideration');
    $access = checkUserAccess('add', $user_privilege);


    return view('admin.bidders_resolution_form', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => 'Add Resolution Denying The Motion For Reconsideration', 'resolution_type' => 'RDMR', 'mrs' => $mrs, 'mr_project_bid_ids' => null, "resolution" => null]);
  }

  public function addResolutionGrantingTheMotionForReconsideration()
  {
    $year = null;
    $APP = new APP;
    $rfqs_motion_for_reconsiderations = DB::table('motion_for_reconsideration')
      ->select('motion_for_reconsideration.*', 'motion_for_reconsideration_project_bid.*', 'project_bidders.*', 'rfq_projects.*', 'rfqs.*', 'procacts.*', 'project_plans.*', 'procurement_modes.*', 'contractors.*', 'twg_evaluations.*')
      ->where([['resolution_mr_project_bids.mr_project_bid_id', null]])
      ->join("motion_for_reconsideration_project_bid", 'motion_for_reconsideration.mr_id', 'motion_for_reconsideration_project_bid.mr_id')
      ->join('project_bidders', 'project_bidders.project_bid', 'motion_for_reconsideration_project_bid.project_bid_id')
      ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->leftJoin('resolution_mr_project_bids', 'motion_for_reconsideration_project_bid.mr_project_bid_id', 'resolution_mr_project_bids.mr_project_bid_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
      ->get();

    $bid_docs_motion_for_reconsiderations = DB::table('motion_for_reconsideration')
      ->select('motion_for_reconsideration.*', 'motion_for_reconsideration_project_bid.*', 'project_bidders.*', 'bid_doc_projects.*', 'bid_docs.*', 'procacts.*', 'project_plans.*', 'procurement_modes.*', 'contractors.*', 'twg_evaluations.*')
      ->where([['resolution_mr_project_bids.mr_project_bid_id', null]])
      ->join("motion_for_reconsideration_project_bid", 'motion_for_reconsideration.mr_id', 'motion_for_reconsideration_project_bid.mr_id')
      ->join('project_bidders', 'project_bidders.project_bid', 'motion_for_reconsideration_project_bid.project_bid_id')
      ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->leftJoin('resolution_mr_project_bids', 'motion_for_reconsideration_project_bid.mr_project_bid_id', 'resolution_mr_project_bids.mr_project_bid_id')
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
      ->get();

    if (count($rfqs_motion_for_reconsiderations) > 0 && count($bid_docs_motion_for_reconsiderations) > 0) {
      $mrs = array_merge((array)json_decode($rfqs_motion_for_reconsiderations), (array)json_decode($bid_docs_motion_for_reconsiderations));
    } else if (count($rfqs_motion_for_reconsiderations) > 0) {
      $mrs = (array)json_decode($rfqs_motion_for_reconsiderations);
    } else if (count($bid_docs_motion_for_reconsiderations) > 0) {
      $mrs = (array)json_decode($bid_docs_motion_for_reconsiderations);
    } else {
      $mrs = [];
    }
    $links = getUserLinks();
    $user_privilege = getUserPrivilegeByLink('resolution_granting_the_motion_for_reconsideration');
    $access = checkUserAccess('add', $user_privilege);

    return view('admin.bidders_resolution_form', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => 'Add Resolution Granting The Motion For Reconsideration', 'resolution_type' => 'RGMR', 'mrs' => $mrs, 'mr_project_bid_ids' => null, "resolution" => null]);
  }

  public function generateRRRC($id)
  {

    $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $resolution = DB::table("resolutions")->where("resolution_id", $id)->join("governors", "governors.governor_id", "resolutions.governor_id")->first();

    if ($resolution == null) {
      abort(403, 'Invalid Resolution.');
    } else {

      $governor = $resolution->name;
      $resolution_date = date("F d,Y", strtotime($resolution->resolution_date));
      $APP = new APP;

      $bac = DB::table('bids_and_awards_committee')
        ->select(
          'bids_and_awards_committee.*',
          DB::raw("CONCAT(bac_ch.member_prefix,' ',bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name_prefix"),
          DB::raw("CONCAT(bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name"),
          DB::raw("CONCAT(bac_vice_ch.member_prefix,' ',bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name_prefix"),
          DB::raw("CONCAT(bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name"),
          DB::raw("CONCAT(bac_alternate_vice_ch.member_fname,' ',if(bac_alternate_vice_ch.member_minitial is null ,'',bac_alternate_vice_ch.member_minitial),' ',bac_alternate_vice_ch.member_lname) AS bac_alternate_vice_chairman_name"),
          DB::raw("CONCAT(bac_sec_ch.member_fname,' ',if(bac_sec_ch.member_minitial is null ,'',bac_sec_ch.member_minitial),' ',bac_sec_ch.member_lname) AS bac_sec_chairman_name"),
          DB::raw("CONCAT(bac_sec_vice_ch.member_fname,' ',if(bac_sec_vice_ch.member_minitial is null ,'',bac_sec_vice_ch.member_minitial),' ',bac_sec_vice_ch.member_lname) AS bac_sec_vice_chairman_name"),
          DB::raw("CONCAT(bac_twg_ch.member_fname,' ',if(bac_twg_ch.member_minitial is null ,'',bac_twg_ch.member_minitial),' ',bac_twg_ch.member_lname) AS bac_twg_chairman_name"),
          DB::raw("CONCAT(bac_twg_vice_ch.member_fname,' ',if(bac_twg_vice_ch.member_minitial is null ,'',bac_twg_vice_ch.member_minitial),' ',bac_twg_vice_ch.member_lname) AS bac_twg_vice_chairman_name")
        )
        ->join('member as bac_ch', 'bac_ch.member_id', '=', 'bids_and_awards_committee.bac_chairman')
        ->join('member as bac_vice_ch', 'bac_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_vice_chairman')
        ->leftJoin('member as bac_alternate_vice_ch', 'bac_alternate_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_alternate_vice_chairman')
        ->join('member as bac_sec_ch', 'bac_sec_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_chairman')
        ->join('member as bac_sec_vice_ch', 'bac_sec_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_vice_chairman')
        ->join('member as bac_twg_ch', 'bac_twg_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_chairman')
        ->join('member as bac_twg_vice_ch', 'bac_twg_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_vice_chairman')
        ->orderBy('bac_id', 'desc')
        ->first();

      $bac_infra_members = DB::table('bac_member')->where('bac_id', $bac->bac_id)
        ->select(DB::raw("CONCAT(member.member_fname,' ',if(member.member_minitial is null ,'',member.member_minitial),' ',member.member_lname) AS member_name"), 'member_prefix')
        ->where('bac_member.bac_member_type', 'BAC Infrastructure Member')
        ->join('member', 'member.member_id', '=', 'bac_member.member_id')->orderBy('bac_member.bac_member_arrangement', 'asc')->get();


      $bac_members = [];
      array_push($bac_members, ["name" => $bac->bac_chairman_name, "position" => "Chairperson &amp; Presiding Officer"]);
      array_push($bac_members, ["name" => $bac->bac_vice_chairman_name, "position" => "Vice-Chairperson"]);
      $bac_names = strtoupper(strtolower($bac->bac_chairman_name_prefix)) . ", Chairperson &amp; Presiding Officer <w:br/>" . strtoupper(strtolower($bac->bac_vice_chairman_name_prefix)) . ", Vice-Chairperson  <w:br/>";
      foreach ($bac_infra_members as $member) {
        array_push($bac_members, ["name" => $member->member_name, "position" => "Member"]);
        $bac_names = $bac_names . strtoupper(strtolower($member->member_prefix . ' ' . $member->member_name)) . ", Member <w:br/>";
      }
      $member_rows = ceil(count($bac_members) / 2);

      $project_plans = DB::table("resolution_project_bids")->where("resolution_id", $id)
        ->select('*', 'resolution_project_bids.project_bid as bidder')
        ->join('procacts', 'procacts.procact_id', 'resolution_project_bids.procact_id')
        ->join("project_plans", "procacts.plan_id", "project_plans.plan_id")
        ->leftJoin("project_bidders", "project_plans.project_bid_id", "project_bidders.project_bid")
        ->join("municipalities", "project_plans.municipality_id", "municipalities.municipality_id")
        ->join("project_timelines", "project_timelines.procact_id", "procacts.procact_id")
        ->join("funds", "project_plans.fund_id", "funds.fund_id")
        ->leftJoin("barangays", "project_plans.barangay_id", "barangays.barangay_id")
        ->orderBy("municipality_name", "asc")
        ->orderBy("procacts.itb_arrangement", "asc")
        ->get();

      if (count($project_plans) === 0) {
        abort(403, 'No Projects for this resolution');
      } else {
        $procact_id = $project_plans[0]->latest_procact_id;
        $governor = $resolution->name;
        $resolution_number = $resolution->resolution_number;
        $resolution_date = date("F d,Y", strtotime($resolution->resolution_date));
        $date_format = date("jS", strtotime($resolution->resolution_date)) . " day of " . date("F, Y", strtotime($resolution->resolution_date));
        $mode_id = $project_plans[0]->mode_id;
        $date_opened = date("F d,Y", strtotime($project_plans[0]->open_bid));
        $ymd_date_opened = $project_plans[0]->open_bid;
        $ids = [];
        $titles = [];
        $locations = [];
        $project_numbers = [];
        $sources = [];
        $project_costs = [];
        $modes = [];
        $item_numbers = [];
        $bidders_array = [];

        if ($mode_id === 1) {
          $bidding_or_svp = "Public Bidding";
        } else if ($mode_id === 12) {
          $bidding_or_svp = "Small Value Procurement";
        } else {
          $bidding_or_svp = "Small Value Procurement - Negotiated Procurement";
        }

        // if (count($project_plans) > 1) {
        //   dd($project_plans);
        //   return abort("403", "Please choose one project or clustered project");
        // }

        // get Bidders Data
        $bidder = $APP->getBid($project_plans[0]->bidder);
        $rank = getRank($project_plans[0]->procact_id, $project_plans[0]->bidder);
        $templateProcessor = new TemplateProcessor(public_path("word_templates/RRRC.docx"));
        $templateProcessor->setValue('bac', $bac_names);
        $templateProcessor->setValue('bac_sec', strtoupper(strtolower($bac->bac_sec_chairman_name)));
        $templateProcessor->cloneRow('member1', $member_rows);
        $member_counter = 1;
        for ($i = 1; $i <= $member_rows; $i++) {
          $templateProcessor->setValue('member1#' . $i, strtoupper(strtolower($bac_members[$member_counter - 1]['name'])));
          $templateProcessor->setValue('position1#' . $i, $bac_members[$member_counter - 1]['position']);
          $member_counter = $member_counter + 1;
          if ($member_counter <= count($bac_members)) {
            $templateProcessor->setValue('member2#' . $i, strtoupper(strtolower($bac_members[$member_counter - 1]['name'])));
            $templateProcessor->setValue('position2#' . $i, $bac_members[$member_counter - 1]['position']);
            $member_counter = $member_counter + 1;
          } else {
            $templateProcessor->setValue('member2#' . $i, '');
            $templateProcessor->setValue('position2#' . $i, '');
          }
        }
        // Notice of Post Qualification
        $npq = DB::table('project_bidder_notices')->where([['project_bid', $project_plans[0]->bidder], ['notice_type', 'NOPQ']])->first();
        $npq_received = date("F d,Y", strtotime($npq->date_received_by_contractor));

        $templateProcessor->setValue('date_opened', $date_opened);
        $templateProcessor->setValue('npq_received', $npq_received);
        $templateProcessor->setValue('bidder', strtoupper(strtolower($bidder->business_name)));
        $templateProcessor->setValue('owner', strtoupper(strtolower($bidder->owner)));
        $templateProcessor->setValue('project_title', strtoupper(strtolower($project_plans[0]->project_title)));
        $templateProcessor->setValue('resolution_date', strtoupper($resolution_date));
        $templateProcessor->setValue('resolution_number', strtoupper($resolution_number));
        $templateProcessor->setValue('governor', strtoupper(strtolower($governor)));
        $templateProcessor->setValue('bidding_svp', $bidding_or_svp);
        $templateProcessor->setValue('rank', $rank);

        $templateProcessor->saveAs($this->getPublicPath() . 'word_results/' . $resolution_number . '.docx');
        return  response()->download($this->getPublicPath() . 'word_results/' . $resolution_number . '.docx')->deleteFileAfterSend(true);
      }
    }
  }

  public function generateMRResolution($id)
  {
    $APP = new APP;
    $resolution_bids = DB::table('resolutions')->where('resolutions.resolution_id', $id)->whereIn('type', ['RDMR', 'RGMR'])
      ->join('resolution_mr_project_bids', 'resolution_mr_project_bids.resolution_id', 'resolutions.resolution_id')
      ->join('motion_for_reconsideration_project_bid', 'resolution_mr_project_bids.mr_project_bid_id', 'motion_for_reconsideration_project_bid.mr_project_bid_id')
      ->join('motion_for_reconsideration', 'motion_for_reconsideration_project_bid.mr_id', 'motion_for_reconsideration.mr_id')
      ->get();

    $bac = DB::table('bids_and_awards_committee')
      ->select(
        'bids_and_awards_committee.*',
        DB::raw("CONCAT(bac_ch.member_prefix,' ',bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name_prefix"),
        DB::raw("CONCAT(bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name"),
        DB::raw("CONCAT(bac_vice_ch.member_prefix,' ',bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name_prefix"),
        DB::raw("CONCAT(bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name"),
        DB::raw("CONCAT(bac_alternate_vice_ch.member_fname,' ',if(bac_alternate_vice_ch.member_minitial is null ,'',bac_alternate_vice_ch.member_minitial),' ',bac_alternate_vice_ch.member_lname) AS bac_alternate_vice_chairman_name"),
        DB::raw("CONCAT(bac_sec_ch.member_fname,' ',if(bac_sec_ch.member_minitial is null ,'',bac_sec_ch.member_minitial),' ',bac_sec_ch.member_lname) AS bac_sec_chairman_name"),
        DB::raw("CONCAT(bac_sec_vice_ch.member_fname,' ',if(bac_sec_vice_ch.member_minitial is null ,'',bac_sec_vice_ch.member_minitial),' ',bac_sec_vice_ch.member_lname) AS bac_sec_vice_chairman_name"),
        DB::raw("CONCAT(bac_twg_ch.member_fname,' ',if(bac_twg_ch.member_minitial is null ,'',bac_twg_ch.member_minitial),' ',bac_twg_ch.member_lname) AS bac_twg_chairman_name"),
        DB::raw("CONCAT(bac_twg_vice_ch.member_fname,' ',if(bac_twg_vice_ch.member_minitial is null ,'',bac_twg_vice_ch.member_minitial),' ',bac_twg_vice_ch.member_lname) AS bac_twg_vice_chairman_name")
      )
      ->join('member as bac_ch', 'bac_ch.member_id', '=', 'bids_and_awards_committee.bac_chairman')
      ->join('member as bac_vice_ch', 'bac_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_vice_chairman')
      ->leftJoin('member as bac_alternate_vice_ch', 'bac_alternate_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_alternate_vice_chairman')
      ->join('member as bac_sec_ch', 'bac_sec_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_chairman')
      ->join('member as bac_sec_vice_ch', 'bac_sec_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_vice_chairman')
      ->join('member as bac_twg_ch', 'bac_twg_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_chairman')
      ->join('member as bac_twg_vice_ch', 'bac_twg_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_vice_chairman')
      ->orderBy('bac_id', 'desc')
      ->first();

    $bac_infra_members = DB::table('bac_member')->where('bac_id', $bac->bac_id)
      ->select(DB::raw("CONCAT(member.member_fname,' ',if(member.member_minitial is null ,'',member.member_minitial),' ',member.member_lname) AS member_name"), 'member_prefix')
      ->where('bac_member.bac_member_type', 'BAC Infrastructure Member')
      ->join('member', 'member.member_id', '=', 'bac_member.member_id')->orderBy('bac_member.bac_member_arrangement', 'asc')->get();



    $bac_members = [];
    array_push($bac_members, ["name" => $bac->bac_chairman_name, "position" => "Chairperson &amp; Presiding Officer"]);
    array_push($bac_members, ["name" => $bac->bac_vice_chairman_name, "position" => "Vice-Chairperson"]);
    $bac_names = strtoupper(strtolower($bac->bac_chairman_name_prefix)) . ", Chairperson &amp; Presiding Officer <w:br/>" . strtoupper(strtolower($bac->bac_vice_chairman_name_prefix)) . ", Vice-Chairperson  <w:br/>";
    foreach ($bac_infra_members as $member) {
      array_push($bac_members, ["name" => $member->member_name, "position" => "Member"]);
      $bac_names = $bac_names . strtoupper(strtolower($member->member_prefix . ' ' . $member->member_name)) . ", Member <w:br/>";
    }
    $member_rows = ceil(count($bac_members) / 2);


    if (count($resolution_bids) > 0) {
      $formatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
      $meeting_room = "BEN PALISPIS CONFERENCE HALL, 3RD FLOOR, PROVINCIAL CAPITOL, LA TRINIDAD, BENGUET";
      $bid_details = $APP->getBid($resolution_bids[0]->project_bid_id);
      $governor = DB::table('governors')->where('governor_id', $bid_details->governor_id)->first();
      if ($governor == null) {
        $governor = DB::table('governors')->orderBy('governor_id', 'desc')->first();
      }
      $business_name = $bid_details->business_name;
      $date_of_opening = $bid_details->open_bid;
      $project_bidders = $APP->getBiddersData($bid_details->procact_id, 'active,responsive,non-responsive,disqualified');
      $takers = $APP->getAllTakers($bid_details->procact_id);

      $owner = $bid_details->owner;
      $bid_or_quotation = "quotation";
      $bidding_or_svp = "Small Value Procurement";
      $Bids_or_Quotations = "quotations";
      $Bid_or_Quotation = "quotation";
      $bidders_or_contractors = "Name of Interested/Invited Contractor";
      $invited_or_bought = "";
      $lcb_or_lcq = "LCQ";
      $resolution_type = $resolution_bids[0]->type;

      $title = "";
      if (count($resolution_bids) > 1) {
        $letter = "A";
        foreach ($resolution_bids as $key => $value) {
          $bid_details = $APP->getBid($value->project_bid_id);
          $title = $title . $letter . ". " . $bid_details->project_title . "; ";
        }
      } else {
        $title = $bid_details->project_title;
      }
      $meeting = DB::table('meeting')->where('meeting_date', $resolution_bids[0]->resolution_date)
        ->join('meeting_room', 'meeting.meeting_room_id', 'meeting_room.meeting_room_id')->first();
      if ($meeting != null) {
        $meeting_room = $meeting->address;
      }
      if ($bid_details->mode_id === 1) {
        $bidders_or_contractors = "Bidders";
        $bid_or_quotation = "bidder";
        $bidding_or_svp = "Public Bidding";
        $Bids_or_Quotations = "bids";
        $Bid_or_Quotation = "bid";
        $lcb_or_lcq = "lCB";
        if (count($takers) == count($project_bidders)) {
          $invited_or_bought = "contractors bought and submitted their bid proposal";
        } else {
          $invited_or_bought = "contractors bought bid proposals but " . $formatter->format(count($project_bidders)) . " (" . count($project_bidders) . ") submitted their bid proposals";
        }
      } else {
        if (count($takers) == count($project_bidders)) {
          $invited_or_bought = "invited contractors submitted their quotations";
        } else {
          $invited_or_bought = "contractors were invited but " . $formatter->format(count($project_bidders)) . " (" . count($project_bidders) . ") submitted their quotations";
        }
      }
      if ($resolution_bids[0]->mr_type === "Ineligible") {
        $noi = DB::table('project_bidder_notices')->where([['project_bid', $resolution_bids[0]->project_bid_id], ['notice_type', 'NOI']])->first();
        if ($resolution_type == "RDMR") {
          $templateProcessor = new TemplateProcessor(public_path("word_templates/RDMR-Ineligibility.docx"));
        } else {
          $templateProcessor = new TemplateProcessor(public_path("word_templates/RGMR-Ineligibility.docx"));
        }
        $templateProcessor->setValue('bac', $bac_names);
        $templateProcessor->setValue('bac_sec', strtoupper(strtolower($bac->bac_sec_chairman_name)));
        $templateProcessor->cloneRow('member1', $member_rows);
        $member_counter = 1;
        for ($i = 1; $i <= $member_rows; $i++) {
          $templateProcessor->setValue('member1#' . $i, strtoupper(strtolower($bac_members[$member_counter - 1]['name'])));
          $templateProcessor->setValue('position1#' . $i, $bac_members[$member_counter - 1]['position']);
          $member_counter = $member_counter + 1;
          if ($member_counter <= count($bac_members)) {
            $templateProcessor->setValue('member2#' . $i, strtoupper(strtolower($bac_members[$member_counter - 1]['name'])));
            $templateProcessor->setValue('position2#' . $i, $bac_members[$member_counter - 1]['position']);
            $member_counter = $member_counter + 1;
          } else {
            $templateProcessor->setValue('member2#' . $i, '');
            $templateProcessor->setValue('position2#' . $i, '');
          }
        }
        $templateProcessor->setValue("resolution_date", date("F d,Y", strtotime($resolution_bids[0]->resolution_date)));
        $templateProcessor->setValue("meeting_room", $meeting_room);
        $templateProcessor->setValue("resolution_number", $resolution_bids[0]->resolution_number);
        $templateProcessor->setValue("contractor1", str_replace('&', '&amp;', strtoupper(strtolower($business_name))));
        $templateProcessor->setValue("contractor2", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor3", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor4", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor5", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor6", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor7", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor8", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor9", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor10", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor11", str_replace('&', '&amp;', strtoupper(strtolower($business_name))));
        $templateProcessor->setValue("bid_or_quotation1", " " . strtoupper(strtolower($Bid_or_Quotation)));
        $templateProcessor->setValue("bid_or_quotation2", strtolower($bid_or_quotation));
        $templateProcessor->setValue("bid_or_quotation3", ucwords(strtolower($bid_or_quotation)));
        $templateProcessor->setValue("bid_or_quotation4", ucwords(strtolower($bid_or_quotation)));
        $templateProcessor->setValue("bid_or_quotation5", ucwords(strtolower($bid_or_quotation)));
        $templateProcessor->setValue("project_title1", str_replace('&', '&amp;', strtoupper(strtolower($title))));
        $templateProcessor->setValue("project_title2", str_replace('&', '&amp;', strtoupper(strtolower($title))));
        $templateProcessor->setValue("bidding_or_svp", $bidding_or_svp);
        $templateProcessor->setValue("Bids_or_Quotations1", $Bids_or_Quotations);
        $templateProcessor->setValue("Bids_or_Quotations2", $Bids_or_Quotations);
        $templateProcessor->setValue("date_of_opening1", date("F d,Y", strtotime($date_of_opening)));
        $templateProcessor->setValue("date_of_opening2", date("F d,Y", strtotime($date_of_opening)));
        if ($noi == null) {
          $templateProcessor->setValue("noi_date_received1", '[date notice of ineligible received]');
          $templateProcessor->setValue("noi_date_received2", '[date notice of ineligible received]');
        } else {
          $templateProcessor->setValue("noi_date_received1", date("F d,Y", strtotime($noi->date_received)));
          $templateProcessor->setValue("noi_date_received2", date("F d,Y", strtotime($noi->date_received)));
        }

        $templateProcessor->setValue("invited_or_bought", $invited_or_bought);
        $templateProcessor->setValue("bidders_or_contractors", $bidders_or_contractors);
        $templateProcessor->setValue("mr_date_received", date("F d,Y", strtotime($resolution_bids[0]->mr_date_received)));
        $templateProcessor->setValue("governor1", strtoupper(strtolower($governor->name)));
        $templateProcessor->setValue("governor2", strtoupper(strtolower($governor->name)));
        $templateProcessor->setValue("owner", strtoupper(strtolower($owner)));
        if (count($project_bidders) == 1) {
          $templateProcessor->cloneBlock("lone", 1);
          $templateProcessor->cloneBlock("multiple", 0);
        }
        if (count($project_bidders) > 1) {
          $bidder_cnt = 1;
          $templateProcessor->cloneBlock("lone", 0);
          $templateProcessor->cloneBlock("multiple", 1);
          $templateProcessor->cloneRow("business_name", count($project_bidders));
          $templateProcessor->setValue("contractors_number_in_words", $formatter->format(count($takers)));
          $templateProcessor->setValue("contractors_number", count($takers));
          $rank = 1;
          foreach ($project_bidders as $key => $value) {
            $templateProcessor->setValue("business_name#" . $bidder_cnt, htmlspecialchars($value->business_name));
            $templateProcessor->setValue("bid_as_read#" . $bidder_cnt, "Php " . number_format($value->proposed_bid, 2, '.', ','));
            $templateProcessor->setValue("bid_as_evaluated#" . $bidder_cnt, "Php " . number_format($value->bid_as_evaluated, 2, '.', ','));
            if ($value->business_name == $bid_details->business_name) {
              $disqualification = DB::table('disqualification_records')->where([['project_bid', $value->project_bid], ['remarks', 'like', '%Disqualified%'], ['remarks', 'not like', '%Post Disqualified%']])->first();
              if ($disqualification == null) {
                $templateProcessor->setValue("remarks#" . $bidder_cnt, 'Ineligible/Disqualified:Please Research');
              } else {
                $templateProcessor->setValue("remarks#" . $bidder_cnt, $disqualification->remarks);
              }
            } else {
              $disqualification = DB::table('disqualification_records')->where([['project_bid', $value->project_bid], ['remarks', 'like', '%Disqualified%'], ['remarks', 'not like', '%Post Disqualified%']])->first();
              if ($disqualification == null) {
                $ranking = $rank . date("S", mktime(0, 0, 0, 0, $rank, 0)) . " " . $lcb_or_lcq;
                $templateProcessor->setValue("remarks#" . $bidder_cnt, $ranking);
                ++$rank;
              } else {
                $templateProcessor->setValue("remarks#" . $bidder_cnt, $disqualification->remarks);
              }
            }
            ++$bidder_cnt;
          }
        }

        $templateProcessor->saveAs(public_path('word_results/' . $resolution_type . '.docx'));
        return  response()->download(public_path('word_results/' . $resolution_type . '.docx'))->deleteFileAfterSend(true);
      } else {
        $noi = DB::table('project_bidder_notices')->where([['project_bid', $resolution_bids[0]->project_bid_id], ['notice_type', 'NOI']])->first();
        if ($resolution_type == "RDMR") {
          $templateProcessor = new TemplateProcessor(public_path("word_templates/RDMR-disqualification.docx"));
        } else {
          $templateProcessor = new TemplateProcessor(public_path("word_templates/RGMR-disqualification.docx"));
        }
        $templateProcessor->setValue('bac', $bac_names);
        $templateProcessor->setValue('bac_sec', strtoupper(strtolower($bac->bac_sec_chairman_name)));
        $templateProcessor->cloneRow('member1', $member_rows);
        $member_counter = 1;
        for ($i = 1; $i <= $member_rows; $i++) {
          $templateProcessor->setValue('member1#' . $i, strtoupper(strtolower($bac_members[$member_counter - 1]['name'])));
          $templateProcessor->setValue('position1#' . $i, $bac_members[$member_counter - 1]['position']);
          $member_counter = $member_counter + 1;
          if ($member_counter <= count($bac_members)) {
            $templateProcessor->setValue('member2#' . $i, strtoupper(strtolower($bac_members[$member_counter - 1]['name'])));
            $templateProcessor->setValue('position2#' . $i, $bac_members[$member_counter - 1]['position']);
            $member_counter = $member_counter + 1;
          } else {
            $templateProcessor->setValue('member2#' . $i, '');
            $templateProcessor->setValue('position2#' . $i, '');
          }
        }
        $templateProcessor->setValue("resolution_date", date("F d,Y", strtotime($resolution_bids[0]->resolution_date)));
        $templateProcessor->setValue("meeting_room", $meeting_room);
        $templateProcessor->setValue("resolution_number", $resolution_bids[0]->resolution_number);
        $templateProcessor->setValue("contractor1", str_replace('&', '&amp;', strtoupper(strtolower($business_name))));
        $templateProcessor->setValue("contractor2", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor3", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor4", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor5", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor6", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor7", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor8", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor9", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor10", str_replace('&', '&amp;', $business_name));
        $templateProcessor->setValue("contractor11", str_replace('&', '&amp;', strtoupper(strtolower($business_name))));
        $templateProcessor->setValue("bid_or_quotation1", " " . strtoupper(strtolower($Bid_or_Quotation)));
        $templateProcessor->setValue("bid_or_quotation2", strtolower($bid_or_quotation));
        $templateProcessor->setValue("bid_or_quotation3", ucwords(strtolower($bid_or_quotation)));
        $templateProcessor->setValue("bid_or_quotation4", ucwords(strtolower($bid_or_quotation)));
        $templateProcessor->setValue("bid_or_quotation5", strtolower($bid_or_quotation));
        $templateProcessor->setValue("bid_or_quotation6", ucwords(strtolower($bid_or_quotation)));
        $templateProcessor->setValue("project_title1", str_replace('&', '&amp;', strtoupper(strtolower($title))));
        $templateProcessor->setValue("project_title2", str_replace('&', '&amp;', strtoupper(strtolower($title))));
        $templateProcessor->setValue("bidding_or_svp", $bidding_or_svp);
        $templateProcessor->setValue("Bids_or_Quotations1", $Bids_or_Quotations);
        $templateProcessor->setValue("Bids_or_Quotations2", $Bids_or_Quotations);
        $templateProcessor->setValue("date_of_opening1", date("F d,Y", strtotime($date_of_opening)));
        $templateProcessor->setValue("date_of_opening2", date("F d,Y", strtotime($date_of_opening)));
        if ($noi == null) {
          $templateProcessor->setValue("noi_date_received1", '[date notice of ineligible received]');
          $templateProcessor->setValue("noi_date_received2", '[date notice of ineligible received]');
        } else {
          $templateProcessor->setValue("noi_date_received1", date("F d,Y", strtotime($noi->date_received)));
          $templateProcessor->setValue("noi_date_received2", date("F d,Y", strtotime($noi->date_received)));
        }

        $templateProcessor->setValue("invited_or_bought", $invited_or_bought);
        $templateProcessor->setValue("bidders_or_contractors", $bidders_or_contractors);
        $templateProcessor->setValue("mr_date_received", date("F d,Y", strtotime($resolution_bids[0]->mr_date_received)));
        $templateProcessor->setValue("governor1", strtoupper(strtolower($governor->name)));
        $templateProcessor->setValue("governor2", strtoupper(strtolower($governor->name)));
        $templateProcessor->setValue("owner", strtoupper(strtolower($owner)));
        if (count($project_bidders) == 1) {
          $templateProcessor->cloneBlock("lone", 1);
          $templateProcessor->cloneBlock("multiple", 0);
        }
        if (count($project_bidders) > 1) {
          $bidder_cnt = 1;
          $templateProcessor->cloneBlock("lone", 0);
          $templateProcessor->cloneBlock("multiple", 1);
          $templateProcessor->cloneRow("business_name", count($project_bidders));
          $templateProcessor->setValue("contractors_number_in_words", $formatter->format(count($takers)));
          $templateProcessor->setValue("contractors_number", count($takers));
          $rank = 1;
          foreach ($project_bidders as $key => $value) {
            $templateProcessor->setValue("business_name#" . $bidder_cnt, htmlspecialchars($value->business_name));
            $templateProcessor->setValue("bid_as_read#" . $bidder_cnt, "Php " . number_format($value->proposed_bid, 2, '.', ','));
            $templateProcessor->setValue("bid_as_evaluated#" . $bidder_cnt, "Php " . number_format($value->bid_as_evaluated, 2, '.', ','));
            // if($value->business_name==$bid_details->business_name){
            //   $disqualification=DB::table('disqualification_records')->where([['project_bid',$value->project_bid],['remarks','like','%Disqualified%'],['remarks','not like','%Post Disqualified%']])->first();
            //   if($disqualification==null){
            //     $templateProcessor->setValue("remarks#".$bidder_cnt,'Ineligible/Disqualified:Please Research');
            //   }
            //   else{
            //     $templateProcessor->setValue("remarks#".$bidder_cnt,$disqualification->remarks);
            //   }
            // }
            // else{
            $disqualification = DB::table('disqualification_records')->where([['project_bid', $value->project_bid], ['remarks', 'like', '%Disqualified%'], ['remarks', 'not like', '%Post Disqualified%']])->first();
            if ($disqualification == null) {
              $ranking = $rank . date("S", mktime(0, 0, 0, 0, $rank, 0)) . " " . $lcb_or_lcq;
              $templateProcessor->setValue("remarks#" . $bidder_cnt, $ranking);
              ++$rank;
            } else {
              $templateProcessor->setValue("remarks#" . $bidder_cnt, $disqualification->remarks);
            }
            // }
            ++$bidder_cnt;
          }
        }

        $templateProcessor->saveAs(public_path('word_results/' . $resolution_type . '.docx'));
        return  response()->download(public_path('word_results/' . $resolution_type . '.docx'))->deleteFileAfterSend(true);
      }
    } else {
      return back()->with('message', 'unknown_resolution');
    }
  }

  public function pendingRDF()
  {
    $APP = new APP;
    $year = 2022;
    $project_plans = $APP->getSpecificProcurementActivity('pending_rdf', $year);
    $title = "Pending Projects For Resolution Declaring Failure";
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view("admin.pending_rdf", ["links" => $links, 'user_privilege' => $user_privilege, "title" => $title, "project_plans" => $project_plans, "year" => $year]);
  }
}
