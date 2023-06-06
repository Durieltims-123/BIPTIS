<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Controllers\ProcurementController;
use App\APP;
use App\ProjectEngineer;
use App\PowRemarks;
use Validator;

class APPController extends Controller
{

  // Viewing Controllers

  public function getSupplementalAPP()
  {

    $year = date('Y');
    $project_type = 'supplemental';
    $status = null;
    $mode = null;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = null;

    $APP = new APP;

    $title = "Supplemental Plan Table";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
    $fund_categories = DB::table('fund_category')->orderBy('title', 'asc')->get();
    $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('admin.app', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'classifications' => $classifications, 'fund_categories' => $fund_categories, 'modes' => $modes, 'status' => $status, 'project_type' => $project_type, 'year' => $year]);
  }

  public function getOngoingProjects()
  {
    $year = null;
    $project_type = null;
    $status = 'all_ongoing';
    $mode = null;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = null;

    $APP = new APP;

    $title = "Ongoing Projects Table";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view('admin.specific_app', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'year' => $year]);
  }

  public function getCompletedProjects()
  {
    $year = date('Y');
    $project_type = null;
    $status = 'all_completed';
    $mode = null;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = null;

    $APP = new APP;

    $title = "Completed Projects Table";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view('admin.specific_app', ['links' => $links, 'title' => $title, 'project_plans' => $project_plans, 'year' => $year]);
  }

  public function getForReviewProjects()
  {
    $year = date('Y');
    $project_type = null;
    $status = 'projects_for_review';
    $mode = null;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = null;

    $APP = new APP;

    $title = "For Review Projects Table";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view('admin.specific_app', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'year' => $year]);
  }

  public function getForRebidProjects()
  {
    $year = null;
    $project_type = null;
    $status = 'for_rebid';
    $mode = null;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = null;

    $APP = new APP;

    $title = "For Rebid Projects Table";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view('admin.specific_app', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'year' => $year]);
  }

  public function getUnprocuredProjects()
  {
    $year = null;
    $project_type = null;
    $status = 'unprocured_projects';
    $mode = null;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = null;

    $APP = new APP;

    $title = "Unprocured Projects Table";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view('admin.unprocured_projects', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'year' => $year]);
  }

  public function getRevertedProjects(Request $request)
  {
    if ($request->project_year != null) {
      $year = $request->project_year;
    } else {
      $year = date('Y');
    }

    $project_type = null;
    $status = 'reverted';
    $mode = null;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = null;

    $APP = new APP;

    $title = "Reverted Projects";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    if ($request->project_year != null) {
      return back()->withInput()->with("project_plans", $project_plans);
    } else {
      $links = getUserLinks();
      $user_privilege = getUserPrivilege();
      return view('admin.reverted', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'year' => $year]);
    }
  }

  public function getTerminatedProjects(Request $request)
  {
    if ($request->project_year != null) {
      $year = $request->project_year;
    } else {
      $year = date('Y');
    }

    $project_type = null;
    $status = 'terminated';
    $mode = null;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = null;

    $APP = new APP;

    $title = "Terminated Projects";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);

    if ($request->project_year != null) {
      return back()->withInput()->with("project_plans", $project_plans);
    } else {
      $links = getUserLinks();
      $user_privilege = getUserPrivilege();
      return view('admin.terminated', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'year' => $year]);
    }
  }

  public function getRegularAPP()
  {

    $year = date('Y');
    $project_type = 'regular';
    $status = null;
    $mode = null;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = null;

    $APP = new APP;
    $title = "Regular Plan Table";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
    $fund_categories = DB::table('fund_category')->orderBy('title', 'asc')->get();
    $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view('admin.app', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'fund_categories' => $fund_categories, 'classifications' => $classifications, 'modes' => $modes, 'project_type' => $project_type, 'status' => $status, 'year' => $year]);
  }

  public function getLimitedSupplementalAPP()
  {
    $year = date('Y');
    $project_type = 'supplemental';
    $status = null;
    $mode = null;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = null;

    $APP = new APP;
    $title = "Regular Plan Table";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
    $fund_categories = DB::table('fund_category')->orderBy('title', 'asc')->get();
    $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view('twg.app', ["links" => $links, 'title' => $title, 'project_plans' => $project_plans, 'fund_categories' => $fund_categories, 'classifications' => $classifications, 'modes' => $modes, 'project_type' => $project_type, 'status' => $status, 'year' => $year]);
  }

  public function getLimitedRegularAPP()
  {

    $year = date('Y');
    $project_type = 'regular';
    $status = null;
    $mode = null;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = null;

    $APP = new APP;
    $title = "Regular Plan Table";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
    $fund_categories = DB::table('fund_category')->orderBy('title', 'asc')->get();
    $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view('twg.app', ["links" => $links, 'title' => $title, 'project_plans' => $project_plans, 'fund_categories' => $fund_categories, 'classifications' => $classifications, 'modes' => $modes, 'project_type' => $project_type, 'status' => $status, 'year' => $year]);
  }

  public function getPowApp(Request $request)
  {

    $year = date('Y');
    $project_type = '';
    $status = 'new';
    $mode = null;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = null;
    $APP = new APP;
    $title = "Program of Work";

    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);

    $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
    $fund_categories = DB::table('fund_category')->orderBy('title', 'asc')->get();
    $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view('admin.pow', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'fund_categories' => $fund_categories, 'classifications' => $classifications, 'modes' => $modes, 'status' => $status, 'pow' => $pow, 'project_type' => $project_type, 'year' => $year]);
  }

  public function getPowYearly($year)
  {

    $project_type = '';
    $status = 'new';
    $mode = null;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = "true";
    $APP = new APP;

    $title = "APP With Program of Work";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);

    $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
    $fund_categories = DB::table('fund_category')->orderBy('title', 'asc')->get();
    $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();

    $links = getUserLinks();
    $user_privilege = getUserPrivilegeByLink('pow');
    return view('admin.pow', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'fund_categories' => $fund_categories, 'classifications' => $classifications, 'modes' => $modes, 'status' => $status, 'pow' => $pow, 'project_type' => $project_type, 'year' => $year]);
  }

  public function getWithoutPowYearly(Request $request)
  {
    if ($request->project_year != null) {
      $year = $request->project_year;
    } else {
      $year = date('Y');
    }
    $project_type = '';
    $status = 'new';
    $mode = null;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = "false";
    $APP = new APP;
    $page_filter = "filter_without_pow";

    $title = "Projects Without Program of Work";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    if ($request->project_year != null) {
      return back()->withInput()->with("filtered_data", $project_plans);
    } else {
      $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
      $fund_categories = DB::table('fund_category')->orderBy('title', 'asc')->get();
      $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();

      $links = getUserLinks();
      $user_privilege = getUserPrivilege();
      return view('admin.custom_pow', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'fund_categories' => $fund_categories, 'classifications' => $classifications, 'modes' => $modes, 'status' => $status, 'pow' => $pow, 'project_type' => $project_type, 'year' => $year, "page_filter" => $page_filter]);
    }
  }

  public function getWithPowYearly(Request $request)
  {
    if ($request->project_year != null) {
      $year = $request->project_year;
    } else {
      $year = date('Y');
    }
    $project_type = '';
    $status = 'new';
    $mode = null;
    $municipality = null;
    $fund_category = null;
    $type = null;
    $account_classification = null;
    $month = null;
    $sort = $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = "true";
    $APP = new APP;
    $page_filter = "filter_with_pow";

    $title = "Projects With Program of Work";
    $project_plans = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    if ($request->project_year != null) {
      return back()->withInput()->with("filtered_data", $project_plans);
    } else {
      $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
      $fund_categories = DB::table('fund_category')->orderBy('title', 'asc')->get();
      $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();

      $links = getUserLinks();
      $user_privilege = getUserPrivilege();
      return view('admin.custom_pow', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_plans' => $project_plans, 'fund_categories' => $fund_categories, 'classifications' => $classifications, 'modes' => $modes, 'status' => $status, 'pow' => $pow, 'project_type' => $project_type, 'year' => $year, "page_filter" => $page_filter]);
    }
  }

  // Adding , Updating , Deleting
  public function addSupplementalAPP()
  {
    $user_privilege = getUserPrivilegeByLink('supplemental_app');
    $access = checkUserAccess('add', $user_privilege);
    $year = date('Y');
    $project_type = 'supplemental';
    $title = "Add Supplemental Plan";
    $data = [];
    $additional_sapp = false;
    $data = json_encode($data);
    $municipalities = DB::table('municipalities')->orderBy('municipalities.municipality_name')->get();
    $projtypes = DB::table('projtypes')->orderBy('projtypes.type')->get();
    $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
    $funds = DB::table('funds')->orderBy('funds.source')->get();
    $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view('admin.add_app', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_type' => $project_type, 'year' => $year, 'data' => $data, 'municipalities' => $municipalities, 'projtypes' => $projtypes, 'modes' => $modes, 'funds' => $funds, "additional_sapp" => $additional_sapp, 'classifications' => $classifications]);
  }
  public function additionalSupplementalApp($id)
  {
    $plan = DB::table('project_plans')
      ->where([['project_plans.plan_id', $id]])
      ->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')
      ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
      ->first();

    $bidders = getBiddersDataFirst($plan->latest_procact_id, "active,responsive");

    if ($plan->is_old === 1) {
      return abort(403, 'Sorry! You are prohibited to create another SAPP for Projects with SAPP. Please contact your developer for validation.');
    }

    if (strtotime($plan->bid_submission_start) > strtotime(Date('Y-m-d'))) {
      return abort(403, 'Sorry! You are prohibited to create another SAPP for Projects with Future Scheduled Opening. Please contact your developer for validation.');
    }

    if ($bidders != null) {
      return abort(403, 'Sorry! You are prohibited to create another SAPP for Projects with Active/Responsive Bidder. Please contact your developer for validation.');
    }

    $user_privilege = getUserPrivilegeByLink('supplemental_app');
    $access = checkUserAccess('create sapp', $user_privilege);

    $available_months = [];
    $month_now = (int)date('m');
    $start_month = $month_now - 6 + 1;

    for ($i = $start_month; $i <= $month_now; $i++) {
      if ($i < 0) {
        $temp = $i + 12;
      } else {
        $temp = $i;
      }
      array_push($available_months, date('M-Y', strtotime(date('Y') . '-' . $temp . '-01')));
    }

    $data = DB::table('project_plans')->select('project_plans.*', 'sectors.sector_type')->where('plan_id', $id)
      ->leftJoin('sectors', 'project_plans.sector_id', "=", "sectors.sector_id")
      ->whereNotIn('project_plans.abc_post_date', $available_months)->where('project_plans.is_old', '<>', true)
      ->get();

    if (count($data) == 0) {
      $year = date('Y');
      $project_type = 'supplemental';
      $title = "Add Supplemental Plan";
      $data = [];
      $additional_sapp = false;
      $data = json_encode($data);
      $municipalities = DB::table('municipalities')->orderBy('municipalities.municipality_name')->get();
      $projtypes = DB::table('projtypes')->orderBy('projtypes.type')->get();
      $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
      $funds = DB::table('funds')->orderBy('funds.source')->get();
      $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
      $links = getUserLinks();
      return view('admin.add_app', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_type' => $project_type, 'year' => $year, 'data' => $data, 'municipalities' => $municipalities, 'projtypes' => $projtypes, 'modes' => $modes, 'funds' => $funds, "additional_sapp" => $additional_sapp, 'classifications' => $classifications]);
    } else {
      $additional_sapp = true;
      $year = $data[0]->project_year;
      $project_type = "supplemental";
      $title = "Add Supplemental Plan";
      $municipalities = DB::table('municipalities')->orderBy('municipalities.municipality_name')->get();
      $projtypes = DB::table('projtypes')->orderBy('projtypes.type')->get();
      $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
      $funds = DB::table('funds')->orderBy('funds.source')->get();
      $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
      $data = json_encode($data);
      $links = getUserLinks();
      return view('admin.add_app', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_type' => $project_type, 'year' => $year, 'data' => $data, 'municipalities' => $municipalities, 'projtypes' => $projtypes, 'modes' => $modes, 'funds' => $funds, "additional_sapp" => $additional_sapp, 'classifications' => $classifications]);
    }
  }
  public function adjustSupplementalApp($id)
  {
    $available_months = [];
    $month_now = (int)date('m');
    $start_month = $month_now - 6 + 1;

    for ($i = $start_month; $i <= $month_now; $i++) {
      if ($i < 0) {
        $temp = $i + 12;
      } else {
        $temp = $i;
      }
      array_push($available_months, date('M-Y', strtotime(date('Y') . '-' . $temp . '-01')));
    }

    $data = DB::table('project_plans')->select('project_plans.*', 'sectors.sector_type')->where('plan_id', $id)
      ->leftJoin('sectors', 'project_plans.sector_id', "=", "sectors.sector_id")
      // ->whereNotIn('project_plans.abc_post_date',$available_months)->where('project_plans.is_old','<>',true)
      ->get();

    if (count($data) == 0) {
      $year = date('Y');
      $project_type = 'supplemental';
      $title = "Add Supplemental Plan";
      $data = [];
      $additional_sapp = false;
      $data = json_encode($data);
      $municipalities = DB::table('municipalities')->orderBy('municipalities.municipality_name')->get();
      $projtypes = DB::table('projtypes')->orderBy('projtypes.type')->get();
      $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
      $funds = DB::table('funds')->orderBy('funds.source')->get();
      $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
      $links = getUserLinks();
      $user_privilege = getUserPrivilege();
      return view('admin.adjust_sapp', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_type' => $project_type, 'year' => $year, 'data' => $data, 'municipalities' => $municipalities, 'projtypes' => $projtypes, 'modes' => $modes, 'funds' => $funds, "additional_sapp" => $additional_sapp, 'classifications' => $classifications]);
    } else {
      $additional_sapp = true;
      $year = $data[0]->project_year;
      $project_type = "supplemental";
      $title = "Adjust Supplemental Plan";
      $municipalities = DB::table('municipalities')->orderBy('municipalities.municipality_name')->get();
      $projtypes = DB::table('projtypes')->orderBy('projtypes.type')->get();
      $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
      $funds = DB::table('funds')->orderBy('funds.source')->get();
      $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
      $data = json_encode($data);
      $links = getUserLinks();
      $user_privilege = getUserPrivilege();
      return view('admin.adjust_sapp', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_type' => $project_type, 'year' => $year, 'data' => $data, 'municipalities' => $municipalities, 'projtypes' => $projtypes, 'modes' => $modes, 'funds' => $funds, "additional_sapp" => $additional_sapp, 'classifications' => $classifications]);
    }
  }

  public function addRegularAPP()
  {
    $user_privilege = getUserPrivilegeByLink('regular_app');
    $access = checkUserAccess('add', $user_privilege);
    $year = date('Y');
    $project_type = 'regular';
    $title = "Add Regular Plan";
    $data = [];
    $additional_sapp = false;
    $data = json_encode($data);
    $municipalities = DB::table('municipalities')->orderBy('municipalities.municipality_name')->get();
    $projtypes = DB::table('projtypes')->orderBy('projtypes.type')->get();
    $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
    $funds = DB::table('funds')->orderBy('funds.source')->get();
    $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
    $links = getUserLinks();
    return view('admin.add_app', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_type' => $project_type, 'year' => $year, 'data' => $data, 'municipalities' => $municipalities, 'projtypes' => $projtypes, 'modes' => $modes, 'funds' => $funds, "additional_sapp" => $additional_sapp, 'classifications' => $classifications]);
  }

  public function changeProjectType($id)
  {
    $data = DB::table('project_plans')->select('project_plans.*', 'sectors.sector_type')->where('plan_id', $id)
      ->leftJoin('sectors', 'project_plans.sector_id', "=", "sectors.sector_id")
      ->get();

    $scheduled = DB::table('project_plans')
      ->where([['project_timelines.timeline_status', 'set'], ['project_plans.plan_id', $id]])
      ->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')
      ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
      ->count();

    $supplemental_bid = DB::table('project_plans')
      ->where([['project_plans.plan_id', $id]])
      ->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')
      ->join('supplemental_bid_procacts', 'procacts.procact_id', 'supplemental_bid_procacts.procact_id')
      ->count();

    // if($supplemental_bid>0){
    //
    // }
    // else if($scheduled>0){
    //   return abort(403,'Sorry! You are not allowed to edit this Project. Please contact your developer if you think this is an error.');
    // }
    // else{
    //
    // }

    if (count($data) == 0) {
      return abort(404);
    } else {
      if ($data[0]->project_type == "regular") {
        $data[0]->project_type = "supplemental";
      } else {
        $data[0]->project_type = "regular";
      }

      $year = $data[0]->project_year;
      $project_type = $data[0]->project_type;
      $title = "Edit " . ucwords($data[0]->project_type) . " Plan";
      $municipalities = DB::table('municipalities')->orderBy('municipalities.municipality_name')->get();
      $projtypes = DB::table('projtypes')->orderBy('projtypes.type')->get();
      $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
      $funds = DB::table('funds')->orderBy('funds.source')->get();
      $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
      $data = json_encode($data);
      $additional_sapp = false;
      $links = getUserLinks();
      $user_privilege = getUserPrivilege();
      return view('admin.add_app', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_type' => $project_type, 'year' => $year, 'data' => $data, 'municipalities' => $municipalities, 'projtypes' => $projtypes, 'modes' => $modes, 'funds' => $funds, "additional_sapp" => $additional_sapp, 'classifications' => $classifications]);
    }
  }

  public function editApp($id)
  {

    $data = DB::table('project_plans')->select('project_plans.*', 'sectors.sector_type')->where('plan_id', $id)
      ->leftJoin('sectors', 'project_plans.sector_id', "=", "sectors.sector_id")
      ->get();

    $scheduled = DB::table('project_plans')
      ->where([['project_timelines.timeline_status', 'set'], ['project_plans.plan_id', $id]])
      ->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')
      ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
      ->count();

    $supplemental_bid = DB::table('project_plans')
      ->where([['project_plans.plan_id', $id]])
      ->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')
      ->join('supplemental_bid_procacts', 'procacts.procact_id', 'supplemental_bid_procacts.procact_id')
      ->count();
    // if ($supplemental_bid > 0) {
    // } else if ($scheduled > 0) {
    //   return abort(403, 'Sorry! You are not allowed to edit this Project. Please contact your developer for validation and editing.');
    // }

    if (count($data) == 0) {
      return abort(404);
    } else {

      $year = $data[0]->project_year;
      $project_type = $data[0]->project_type;
      $title = "Edit " . ucwords($data[0]->project_type) . " Plan";
      $municipalities = DB::table('municipalities')->orderBy('municipalities.municipality_name')->get();
      $projtypes = DB::table('projtypes')->orderBy('projtypes.type')->get();
      $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
      $funds = DB::table('funds')->orderBy('funds.source')->get();
      $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
      $data = json_encode($data);
      $additional_sapp = false;
      $links = getUserLinks();
      $user_privilege = getUserPrivilege();
      return view('admin.add_app', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_type' => $project_type, 'year' => $year, 'data' => $data, 'municipalities' => $municipalities, 'projtypes' => $projtypes, 'modes' => $modes, 'funds' => $funds, "additional_sapp" => $additional_sapp, 'classifications' => $classifications]);
    }
  }

  public function editSapp($id)
  {
    $data = DB::table('project_plans')->select('project_plans.*', 'sectors.sector_type')->where('plan_id', $id)
      ->leftJoin('sectors', 'project_plans.sector_id', "=", "sectors.sector_id")
      ->get();

    $scheduled = DB::table('project_plans')
      ->where([['project_timelines.timeline_status', 'set'], ['project_plans.plan_id', $id]])
      ->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')
      ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
      ->count();

    $supplemental_bid = DB::table('project_plans')
      ->where([['project_plans.plan_id', $id]])
      ->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')
      ->join('supplemental_bid_procacts', 'procacts.procact_id', 'supplemental_bid_procacts.procact_id')
      ->count();

    // if($supplemental_bid>0){
    //
    // }
    // else if($scheduled>0){
    //   return abort(403,'Sorry! You are not allowed to edit this Project. Please contact your developer if you think this is an error.');
    // }
    // else{
    //
    // }

    if (count($data) == 0) {
      return abort(404);
    } else {

      $year = $data[0]->project_year;
      $project_type = $data[0]->project_type;
      $title = "Edit " . ucwords($data[0]->project_type) . " Plan";
      $municipalities = DB::table('municipalities')->orderBy('municipalities.municipality_name')->get();
      $projtypes = DB::table('projtypes')->orderBy('projtypes.type')->get();
      $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
      $funds = DB::table('funds')->orderBy('funds.source')->get();
      $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
      $data = json_encode($data);
      $additional_sapp = false;
      $links = getUserLinks();
      $user_privilege = getUserPrivilege();
      return view('admin.edit_sapp', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'project_type' => $project_type, 'year' => $year, 'data' => $data, 'municipalities' => $municipalities, 'projtypes' => $projtypes, 'modes' => $modes, 'funds' => $funds, "additional_sapp" => $additional_sapp, 'classifications' => $classifications]);
    }
  }

  public function deleteApp($id)
  {
    $data = DB::table('project_plans')->select('project_plans.*')->where('plan_id', $id)
      ->first();

    if ($data === null) {
      return abort(404);
    } else {
      $bidders = getBiddersDataFirst($data->latest_procact_id, "active,responsive");
      $procacts = DB::table('procacts')->where('plan_id', $id)
        ->count();
      if ($data->is_old === 1) {
        return abort(403, 'Sorry! You are prohibited to delete this project. Please contact your developer for validation.');
      }

      if ($procacts > 1) {
        return abort(403, 'Sorry! You are prohibited to delete this project. Please contact your developer for validation.');
      }

      if ($bidders != null) {
        return abort(403, 'Sorry! You are prohibited to delete Projects with Active/Responsive Bidder. Please contact your developer for validation.');
      }

      if ($data->status != "pending") {
        return abort(404);
      } else {
        if ($data->is_old === 1) {
          return abort(403, "Sorry You can't delete this Project! If you think this is an error please contact your system developer!");
        }
        if ($data->parent_id != null) {
          DB::table('project_plans')->where('plan_id', $data->parent_id)->update([
            "is_old" => 0
          ]);
        }
        // delete
        DB::table('project_plans')->where('parent_id', $data->plan_id)->update([
          "parent_id" => null
        ]);
        DB::table('project_logs')->where('plan_id', $data->plan_id)->delete();
        DB::table('project_activity_status')->where('procact_id', $data->latest_procact_id)->delete();
        DB::table('project_timelines')->where('procact_id', $data->latest_procact_id)->delete();
        DB::table('project_plans')->where('plan_id', $id)->update(["latest_procact_id" => null]);
        DB::table('resolution_projects')->where('procact_id', $data->latest_procact_id)->delete();
        DB::table('rfq_projects')->where('procact_id', $data->latest_procact_id)->delete();
        DB::table('bid_doc_projects')->where('procact_id', $data->latest_procact_id)->delete();
        DB::table('procacts')->where('procact_id', $data->latest_procact_id)->delete();
        DB::table('project_plans')->where('plan_id', $data->plan_id)->delete();

        if ($data->project_type == "regular") {
          return redirect()->route('regular_app')->with("message", "success");
        }
        if ($data->project_type == "supplemental") {
          return redirect()->route('supplemental_app')->with("message", "success");
        }
      }
    }
  }

  public function submitPlan(Request $request)
  {
    // validation
    $data = $request->validate([
      "date_added" => "required|before:tomorrow",
      "project_year" => 'required|digits:4|integer|min:2020|max:' . (date('Y')),
      "year_funded" => 'required|digits:4|integer|min:' . (date("Y") - 8) . '|max:' . (date("Y")),
      "project_number" => "required",
      "app_number" => "nullable|integer|required_if:app_type,supplemental",
      "project_title" => "required|max:255",
      // "sector_type"=>"required",
      // "sector"=>"required",
      // "account_code"=>"required",
      "municipality" => "required",
      // "barangay"=>"required",
      "type_of_project" => "required",
      "mode_of_procurement" => "required",
      "approved_budget_cost" => "required|numeric",
      "source_of_fund" => "required|",
      "account_classification" => "required",
      // "ABC_post_date"=>"required|date_format:m-Y|after_or_equal:".(date("m-Y")),
      "ABC_post_date" => "required",
      "opening_of_bid" => "required|date_format:m-Y|after_or_equal:ABC_post_date",
      "notice_of_award" => "required|date_format:m-Y|after_or_equal:opening_of_bid",
      "contract_signing" => "required|date_format:m-Y|after_or_equal:notice_of_award",
      "remarks" => "max:255",
    ]);

    $abc_post_date = date("Y-m-d", strtotime('01-' . $request->input('ABC_post_date')));
    $sub_open_date = date("Y-m-d", strtotime('01-' . $request->input('opening_of_bid')));
    $award_notice_date = date("Y-m-d", strtotime('01-' . $request->input('notice_of_award')));
    $contract_signing_date = date("Y-m-d", strtotime('01-' . $request->input('contract_signing')));

    if ($request->input('id') != null) {
      $existing_data = DB::table('project_plans')->where([['plan_id', $request->input('id')]])->orderBy('plan_id', 'desc')->first();
    } else {
      $existing_data = DB::table('project_plans')->where([['project_no', $request->input('project_number')], ['project_title', $request->input('project_title')]])->orderBy('plan_id', 'desc')->first();
    }
    if ($request->input('app_type') === "regular") {
      $duplicate = DB::table('project_plans')->where([['project_no', $request->input('project_number')], ['project_title', $request->input('project_title')], ['project_type', $request->input('app_type')], ["abc_post_date", $abc_post_date], ["sub_open_date", $sub_open_date], ["award_notice_date", $award_notice_date], ["contract_signing_date", $contract_signing_date]])->count();
    } else {
      $duplicate = 0;
    }
    if ($duplicate > 0) {
      return back()->with('message', 'duplicate');
    } else {
      if ($request->input('mode_of_procurement') === "3") {
        $mode = 3;
      } else {
        $mode = $request->input('mode_of_procurement');

        if ($mode === "2") {
          $mode = 2;
        } else if ($mode === "3") {
          $mode = 3;
        } else {
          $mode = 1;
        }

        // if($request->input('approved_budget_cost')>1000000){
        //   $mode=1;
        // }
        // else{
        //   $mode=2;
        // }
      }
      $rebid_count = 0;
      $parent_id = null;
      $project_cost = null;
      $date_pow_added = null;
      $pow_date_edited = null;
      $project_bid_id = null;
      $duration = null;

      if ($existing_data != null) {
        // check the data
        $existing_procact = DB::table('procacts')->where('plan_id', $existing_data->plan_id)->orderBy('procact_id', 'desc')->first();
        $rebid_count = $existing_data->re_bid_count;
        $parent_id = $existing_data->plan_id;
        $project_cost = $existing_data->project_cost;
        $date_pow_added = $existing_data->date_pow_added;
        $pow_date_edited = $existing_data->pow_date_edited;
        $project_bid_id = $existing_data->project_bid_id;
        $duration = $existing_data->duration;
        DB::table('project_plans')->where('plan_id', $existing_data->plan_id)
          ->update([
            "is_old" => true
          ]);

        $parents = DB::table('project_plans')->select('parent.*', 'project_plans.plan_id as child_id', 'project_plans.latest_procact_id as child_procact')
          ->join('project_plans as parent', 'project_plans.parent_id', 'parent.plan_id')
          ->where([['parent.is_old', true], ['parent.project_bid_id', '<>', null], ['parent.plan_id', $existing_data->plan_id]])
          ->get();

        foreach ($parents as $parent) {
          if (
            $parent->latest_procact_id < $parent->child_procact
          ) {
            $child = DB::table('project_plans')->where('latest_procact_id', $parent->child_procact)->first();
            $main_procact = DB::table('procacts')->where('procact_id', $parent->latest_procact_id)->first();
            // fix procacts
            DB::table('procacts')->where('procact_id', $parent->child_procact)
              ->update([
                "plan_id" => $parent->plan_id
              ]);

            DB::table('procacts')->where('procact_id', $parent->latest_procact_id)->update([
              "plan_id" => $parent->child_id
            ]);

            DB::table('project_activity_status')->where('procact_id', $parent->child_procact)->update([
              "plan_id" => $parent->plan_id
            ]);

            DB::table('project_activity_status')->where('procact_id', $parent->latest_procact_id)->update([
              "plan_id" => $parent->child_id
            ]);

            DB::table('project_timelines')->where('procact_id', $parent->child_procact)->update([
              "plan_id" => $parent->plan_id
            ]);

            DB::table('project_timelines')->where('procact_id', $parent->latest_procact_id)->update([
              "plan_id" => $parent->child_id
            ]);

            // fix parent
            $pp = DB::table('project_plans')->where('plan_id', $parent->plan_id)
              ->update([
                "status" => "pending",
                "latest_procact_id" => $parent->child_procact,
                "project_bid_id" => null
              ]);

            // fix child
            DB::table('project_plans')->where('plan_id', $parent->child_id)->update([
              "status" => $parent->status,
              "latest_procact_id" => $parent->latest_procact_id,
              "project_bid_id" => $parent->project_bid_id,
            ]);

            $logs = DB::table('project_logs')->where([['plan_id', $parent->plan_id], ['created_at', '>=', $main_procact->created_at]])
              ->update([
                "plan_id" => $parent->child_id
              ]);
          }
        }
      }

      $add = DB::table('project_plans')->insert([
        "project_type" => $request->input('app_type'),
        "date_added" => date("Y-m-d", strtotime($request->input('date_added'))),
        "project_year" => $request->input('project_year'),
        "year_funded" => $request->input('year_funded'),
        "app_group_no" => $request->input('app_number'),
        "project_no" => $request->input('project_number'),
        "account_code" => $request->input('account_code'),
        "project_title" => str_replace("–", "-", $request->input('project_title')),
        "sector_id" => $request->input('sector'),
        "municipality_id" => $request->input('municipality'),
        // "barangay_id"=>$request->input('barangay'),
        "projtype_id" => $request->input('type_of_project'),
        "mode_id" => $mode,
        "abc" => $request->input('approved_budget_cost'),
        "fund_id" => $request->input('source_of_fund'),
        "account_id" => $request->input('account_classification'),
        "abc_post_date" => $abc_post_date,
        "sub_open_date" => $sub_open_date,
        "award_notice_date" => $award_notice_date,
        "contract_signing_date" => $contract_signing_date,
        "remarks" => $request->input('remarks'),
        "status" => 'pending',
        "re_bid_count" => $rebid_count,
        "parent_id" => $parent_id,
        "project_cost" => $project_cost,
        "date_pow_added" => $date_pow_added,
        "pow_date_edited" => $pow_date_edited,
        "project_bid_id" => $project_bid_id,
        "duration" => $duration,
        'created_at' => now(),
        'updated_at' => now()
      ]);

      if ($add) {
        $plan = DB::table('project_plans')->where([['project_no', $request->input('project_number')], ['project_type', $request->input('app_type')]])->orderBy('created_at', 'desc')->first();
        $pre_procurement = "not_needed";
        $pre_bid = "not_needed";
        if ($request->input('approved_budget_cost') > 5000000 && $mode === 1) {
          $pre_procurement = "pending";
        }

        if ($mode === 1) {
          $pre_bid = "pending";
        }

        DB::table('procacts')->insert([
          "procact_mode_id" => $mode,
          "plan_id" => $plan->plan_id,
          'created_at' => now(),
          'updated_at' => now()
        ]);

        $latest_procact = DB::table('procacts')->where('plan_id', $plan->plan_id)->orderBy('created_at', 'desc')->first();

        DB::table('project_timelines')->insert([
          "plan_id" => $plan->plan_id,
          "procact_id" => $latest_procact->procact_id,
          "timeline_status" => "pending",
          'created_at' => now(),
          'updated_at' => now()
        ]);

        DB::table('project_activity_status')->insert([
          "procact_id" => $latest_procact->procact_id,
          "plan_id" => $plan->plan_id,
          "pre_proc" => $pre_procurement,
          "pre_bid" => $pre_bid,
          'created_at' => now(),
          'updated_at' => now()
        ]);

        $latest_plan = DB::table('project_plans')->where([['project_no', $request->input('project_number')], ['project_type', $request->input('app_type')]])->orderBy("created_at", "desc")->first();
        $old_plan = DB::table('project_plans')->where('plan_id', $request->input('id'))->orderBy('created_at', 'desc')->first();
        DB::table('project_plans')->where("plan_id", $latest_plan->plan_id)
          ->update(["latest_procact_id" => $latest_procact->procact_id]);

        if ($request->input('id') != null) {
          if ($old_plan->pow_ready == 1) {
            $update = DB::table('project_plans')->where("plan_id", $latest_plan->plan_id)->update([
              "pow_ready" => true,
              "date_pow_added" => $old_plan->date_pow_added,
              "project_cost" => $old_plan->project_cost,
              "duration" => $old_plan->duration
            ]);
          }

          DB::table('project_plans')->where('plan_id', $request->input('id'))->update(['is_old' => true]);
          DB::table('project_logs')->insert([
            'plan_id' =>  $request->input('id'),
            'user_id' => Auth::user()->id,
            'project_log_type' => "Created new SAPP",
            'project_log_remarks' => "Created new  SAPP with plan id " . $latest_plan->plan_id . ".",
            'log_date' => date("Y-m-d"),
            'created_at' => now(),
            'updated_at' => now()
          ]);

          return back()->with('message', 'success');
        } else {
          return back()->with('message', 'success');
        }
      } else {
        return back()->with('message', 'error');
      }
    }
  }

  public function submitAdjustSAPP(Request $request)
  {
    // validation
    $data = $request->validate([
      "date_added" => "required|before:tomorrow",
      "project_year" => 'required|digits:4|integer|min:2020|max:' . (date('Y')),
      "year_funded" => 'required|digits:4|integer|min:' . (date("Y") - 8) . '|max:' . (date("Y")),
      "project_number" => "required",
      "app_number" => "nullable|integer|required_if:app_type,supplemental",
      "project_title" => "required|max:255",
      // "sector_type"=>"required",
      // "sector"=>"required",
      "account_code" => "required",
      "municipality" => "required",
      // "barangay"=>"required",
      "type_of_project" => "required",
      "mode_of_procurement" => "required",
      "approved_budget_cost" => "required|numeric",
      "source_of_fund" => "required|",
      "account_classification" => "required",
      // "ABC_post_date"=>"required|date_format:m-Y|after_or_equal:".(date("m-Y")),
      "ABC_post_date" => "required",
      "opening_of_bid" => "required|date_format:m-Y|after_or_equal:ABC_post_date",
      "notice_of_award" => "required|date_format:m-Y|after_or_equal:opening_of_bid",
      "contract_signing" => "required|date_format:m-Y|after_or_equal:notice_of_award",
      "remarks" => "max:255",
    ]);

    $abc_post_date = date("Y-m-d", strtotime('01-' . $request->input('ABC_post_date')));
    $sub_open_date = date("Y-m-d", strtotime('01-' . $request->input('opening_of_bid')));
    $award_notice_date = date("Y-m-d", strtotime('01-' . $request->input('notice_of_award')));
    $contract_signing_date = date("Y-m-d", strtotime('01-' . $request->input('contract_signing')));
    $parent = DB::table('project_plans')->where('plan_id', $request->id)->first();

    // adding
    $duplicate = DB::table('project_plans')->where([['project_no', $request->input('project_number')], ['project_type', $request->input('app_type')], ["abc_post_date", $abc_post_date], ["sub_open_date", $sub_open_date], ["award_notice_date", $award_notice_date], ["contract_signing_date", $contract_signing_date]])->count();
    if (false) {
      return back()->with('message', 'duplicate');
    } else {
      if ($request->input('mode_of_procurement') === "3") {
        $mode = 3;
      } else {
        $mode = $request->input('mode_of_procurement');

        if ($mode === "2") {
          $mode = 2;
        } else if ($mode === "3") {
          $mode = 3;
        } else {
          $mode = 1;
        }
      }

      $add = DB::table('project_plans')->insert([
        "project_type" => $request->input('app_type'),
        "date_added" => date("Y-m-d", strtotime($request->input('date_added'))),
        "project_year" => $request->input('project_year'),
        "year_funded" => $request->input('year_funded'),
        "app_group_no" => $request->input('app_number'),
        "project_no" => $request->input('project_number'),
        "account_code" => $request->input('account_code'),
        "project_title" => str_replace("–", "-", $request->input('project_title')),
        "sector_id" => $request->input('sector'),
        "municipality_id" => $request->input('municipality'),
        // "barangay_id"=>$request->input('barangay'),
        "account_code" => $parent->account_code,
        "projtype_id" => $request->input('type_of_project'),
        "mode_id" => $mode,
        "abc" => $request->input('approved_budget_cost'),
        "fund_id" => $request->input('source_of_fund'),
        "account_id" => $request->input('account_classification'),
        "abc_post_date" => $abc_post_date,
        "sub_open_date" => $sub_open_date,
        "award_notice_date" => $award_notice_date,
        "contract_signing_date" => $contract_signing_date,
        "project_cost" => $parent->project_cost,
        "status" => $parent->status,
        "re_bid_count" => $parent->re_bid_count,
        "date_pow_added" => $parent->date_pow_added,
        "pow_date_edited" => $parent->pow_date_edited,
        "project_bid_id" => $parent->project_bid_id,
        "governor_id" => $parent->governor_id,
        "current_cluster" => $parent->current_cluster,
        "latest_procact_id" => $parent->latest_procact_id,
        "duration" => $parent->duration,
        "remarks" => $request->input('remarks'),
        "parent_id" => $request->input('id'),
        'created_at' => now(),
        'updated_at' => now()
      ]);

      if (true) {
        $new_plan = DB::table('project_plans')->where('project_no', $request->input('project_number'))->latest()->first();

        $parent_id = DB::table('project_plans')->where('plan_id', $request->id)->update([
          "is_old" => true
        ]);

        $procact = DB::table('procacts')->where('plan_id', $request->id)->orderBy('procact_id', 'desc')->first();

        DB::table('procacts')->where('procact_id', $procact->procact_id)->update([
          "plan_id" => $new_plan->plan_id
        ]);

        $project_activity_status = DB::table('project_activity_status')
          ->where('procact_id', $procact->procact_id)->update([
            "plan_id" => $new_plan->plan_id
          ]);

        $project_timelines = DB::table('project_activity_status')->where('procact_id', $procact->procact_id)
          ->update([
            "plan_id" => $new_plan->plan_id
          ]);

        $project_logs = DB::table('project_logs')->where([['plan_id', $procact->plan_id], ['created_at', '>=', $procact->created_at]])->update([
          "plan_id" => $new_plan->plan_id
        ]);

        $clusters = DB::table('clustered_project_plans')->where([['plan_id', $procact->plan_id], ['created_at', '>=', $procact->created_at]])->update([
          "plan_id" => $new_plan->plan_id
        ]);

        return back()->with('message', 'success');
      } else {
        return back()->with('message', 'error');
      }
    }
  }

  public function updatePlan(Request $request)
  {

    // validation
    $data = $request->validate([
      "date_added" => "required|before:tomorrow",
      "project_year" => 'required|digits:4|integer|min:2020|max:' . (date('Y')),
      "year_funded" => 'required|digits:4|integer|min:' . (date("Y") - 8) . '|max:' . (date("Y")),
      "project_number" => "required",
      "app_number" => "nullable|integer|required_if:app_type,supplemental",
      "project_title" => "required|max:255",
      // "sector_type"=>"required",
      // "sector"=>"required",
      // "account_code"=>"required",
      "municipality" => "required",
      // "barangay"=>"required",
      "type_of_project" => "required",
      "mode_of_procurement" => "required",
      "approved_budget_cost" => "required|numeric",
      "source_of_fund" => "required|",
      "account_classification" => "required",
      // "ABC_post_date"=>"required|date_format:m-Y|after_or_equal:".(date("m-Y")),
      "ABC_post_date" => "required",
      "opening_of_bid" => "required|date_format:m-Y|after_or_equal:ABC_post_date",
      "notice_of_award" => "required|date_format:m-Y|after_or_equal:opening_of_bid",
      "contract_signing" => "required|date_format:m-Y|after_or_equal:notice_of_award",
      "remarks" => "max:255",
    ]);

    $abc_post_date = date("Y-m-d", strtotime('01-' . $request->input('ABC_post_date')));
    $sub_open_date = date("Y-m-d", strtotime('01-' . $request->input('opening_of_bid')));
    $award_notice_date = date("Y-m-d", strtotime('01-' . $request->input('notice_of_award')));
    $contract_signing_date = date("Y-m-d", strtotime('01-' . $request->input('contract_signing')));
    $id = $request->input('id');

    // updating
    if ($request->input('app_type') === "regular") {
      $duplicate = DB::table('project_plans')->where([['plan_id', '<>', $id], ['project_no', $request->input('project_number')], ['project_title', $request->input('project_title')], ['project_type', $request->input('app_type')], ["abc_post_date", $abc_post_date], ["sub_open_date", $sub_open_date], ["award_notice_date", $award_notice_date], ["contract_signing_date", $contract_signing_date]])->count();
    } else {
      $duplicate = 0;
    }

    if ($duplicate > 0) {
      return back()->withInput()->with('message', 'duplicate');
    } else {
      if ($request->input('mode_of_procurement') === "3") {
        $mode = 3;
      } else {

        $mode = $request->input('mode_of_procurement');

        if ($mode === "2") {
          $mode = 2;
        } else if ($mode === "3") {
          $mode = 3;
        } else {
          $mode = 1;
        }

        // if($request->input('approved_budget_cost')>1000000){
        //   $mode=1;
        // }
        // else{
        //   $mode=2;
        // }
      }

      $update = DB::table('project_plans')->where('plan_id', $id)
        ->update([
          "date_added" => date("Y-m-d", strtotime($request->input('date_added'))),
          "project_year" => $request->input('project_year'),
          "year_funded" => $request->input('year_funded'),
          "app_group_no" => $request->input('app_number'),
          "project_no" => $request->input('project_number'),
          "account_code" => $request->input('account_code'),
          "project_title" => str_replace("–", "-", $request->input('project_title')),
          "sector_id" => $request->input('sector'),
          "municipality_id" => $request->input('municipality'),
          "project_type" => $request->input('app_type'),
          "barangay_id" => $request->input('barangay'),
          "projtype_id" => $request->input('type_of_project'),
          "mode_id" => $mode,
          "abc" => $request->input('approved_budget_cost'),
          "fund_id" => $request->input('source_of_fund'),
          "account_id" => $request->input('account_classification'),
          "abc_post_date" => date("Y-m-d", strtotime('01-' . $request->input('ABC_post_date'))),
          "sub_open_date" => date("Y-m-d", strtotime('01-' . $request->input('opening_of_bid'))),
          "award_notice_date" => date("Y-m-d", strtotime('01-' . $request->input('notice_of_award'))),
          "contract_signing_date" => date("Y-m-d", strtotime('01-' . $request->input('contract_signing'))),
          "remarks" => $request->input('remarks'),
          'updated_at' => now()
        ]);

      if ($update) {

        $plan = DB::table('project_plans')->where('plan_id', $id)->first();
        $procact = DB::table('project_plans')->select('procacts.*')->where('project_no', $request->input('project_number'))->join('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();
        $pre_procurement = "not_needed";
        $pre_bid = "not_needed";

        if ($request->input('approved_budget_cost') > 5000000 && $mode === 1) {
          if ($procact->pre_proc != null) {
            $pre_procurement = "finished";
          } else {
            $pre_procurement = "pending";
          }
        }

        if ($mode === 1) {
          if ($procact->pre_bid != null) {
            $pre_bid = "finished";
          } else {
            $pre_bid = "pending";
          }
        }

        DB::table('project_activity_status')->where("procact_id", $plan->latest_procact_id)->update([
          "pre_proc" => $pre_procurement,
          "pre_bid" => $pre_bid,
          'updated_at' => now()
        ]);

        DB::table('procacts')->where("procact_id", $plan->latest_procact_id,)->update([
          "procact_mode_id" => $mode,
          'updated_at' => now()
        ]);
        return back()->with('message', 'success');
      } else {
        return back()->with('message', 'success');
      }
    }
  }

  // POW
  public function submitPow(Request $request)
  {
    $data = $request->validate([
      "plan_ids" => "required",
      "project_engineer" => "required",
      "date_pow_added" => "required|before:tomorrow",
      "duration" => "required|lt:1000",
      "project_cost" => "required|numeric",
    ]);

    $message = "";
    $plan_ids_array = explode(",", $request->input('plan_ids'));
    $isPowReady = DB::table('project_plans')->select("pow_ready")->whereIn('plan_id', $plan_ids_array)->distinct()->get();

    if (count($isPowReady) > 1) {
      $message = "multiple pow";
    } else {

      // 
      $project_engineer = ProjectEngineer::where('name', $request->input('project_engineer'))->exists();
      if (!$project_engineer) {
        ProjectEngineer::create([
          "name" => $request->input('project_engineer')
        ]);
      }

      if (count($isPowReady) === 1) {
        if ($isPowReady[0]->pow_ready === 1 && count($plan_ids_array) > 1) {
          $message = "all true";
        } else {
          $update = DB::table('project_plans')->whereIn('plan_id', $plan_ids_array)->update([
            // "status"=>"onprocess",
            "pow_ready" => true,
            "project_engineer" => $request->input('project_engineer'),
            "duration" => $request->input('duration'),
            "project_cost" => $request->input('project_cost'),
            "date_pow_added" => date("Y-m-d", strtotime($request->input('date_pow_added'))),
            'pow_date_edited' => date('Y-m-d'),
            'pow_remarks' => $request->additional_remarks,
          ]);




          $plans = DB::table('project_plans')->whereIn('plan_id', $plan_ids_array)->get();
          foreach ($plans as $plan) {
            if ($plan->project_cost > 5000000 && $plan->mode_id === 1) {
              $update_activity_status = DB::table('project_activity_status')->where('procact_id', $plan->latest_procact_id)->orderBy('created_at', 'desc')->first();
              if ($update_activity_status->pre_proc === "not_needed") {
                $update_activity_status = DB::table('project_activity_status')->where('pro_act_stat_id', $update_activity_status->pro_act_stat_id)->update([
                  "pre_proc" => "pending"
                ]);
              }
            }
            if ($request->additional_remarks != "") {
              $same_remarks = DB::table('project_logs')->where([["plan_id", $plan->plan_id], ['project_log_type', "Pow Remarks"], ['project_log_remarks', "Details: " . $request->additional_remarks]])->count();
              if ($same_remarks === 0) {
                DB::table('project_logs')->insert([
                  'plan_id' =>  $plan->plan_id,
                  'user_id' => Auth::user()->id,
                  'project_log_type' => "Pow Remarks",
                  'project_log_remarks' => "Details:" . $request->additional_remarks,
                  'log_date' => date("Y-m-d"),
                  'created_at' => now(),
                  'updated_at' => now()
                ]);
              }
            }
          }

          $message = "success";
        }
      } else {
        $message = "no_data";
      }
    }

    return back()->with('message', $message);
  }



  public function submitPowRemarks(Request $request)
  {
    $data = $request->validate([
      "pow_remarks_plan_id" => "required",
      "reason" => "required"
    ]);

    PowRemarks::create([
      "plan_id" => $request->pow_remarks_plan_id,
      "pow_reason" => $request->reason,
      "pow_remarks" => $request->remarks,
      "created_by" => Auth::user()->id,
    ]);

    DB::table('project_logs')->insert([
      'plan_id' =>  $request->pow_remarks_plan_id,
      'user_id' => Auth::user()->id,
      'project_log_type' => "No POW Reason",
      'project_log_remarks' => "Reason:" . $request->reason,
      'log_date' => date("Y-m-d"),
      'created_at' => now(),
      'updated_at' => now()
    ]);

    return back()->with("message", "remarks_success");
  }
  public function deletePow($id)
  {
    $data = DB::table('project_plans')->select('project_plans.*')->where('plan_id', $id)
      ->get();

    if (count($data) == 0) {
      return abort(404);
    } else {

      if ($data[0]->pow_ready == 0 || $data[0]->pow_ready == null) {
        return abort(404);
      } else {
        $procurement_activity = DB::table('project_timelines')->where([['plan_id', $id], ['timeline_status', 'set']])->first();

        if ($procurement_activity != null) {
          return back()->with('message', 'delete_error');
        } else {
          DB::table('project_plans')->where('plan_id', $id)->update([
            "status" => "pending",
            "pow_ready" => 0,
            "date_pow_added" => null
          ]);
          return back()->with('message', 'delete_success');
        }
      }
    }
  }

  // trigerred Controllers

  public function getSector(Request $request)
  {
    $sector_type = $request->input('sector_type');
    $sectors = DB::table('sectors')->where('sector_type', $sector_type)->orderBy('sectors.sector_name')->get();
    return $sectors;
  }

  public function getBarangays(Request $request)
  {
    $municipality = $request->input('municipality');
    $barangays = DB::table('barangays')->where('municipality_id', $municipality)->orderBy('barangays.barangay_name')->get();
    return $barangays;
  }

  public function filterApp(Request $request)
  {
    $data = $request->validate([
      "project_year" => 'digits:4|integer|min:2020|max:' . (date('Y'))
    ]);

    $year = $request->input('project_year');
    $project_type = $request->input('app_type');
    $status = $request->input('status');
    $mode = $request->input('mode_of_procurement');
    $municipality = null;
    $fund_category = $request->input('fund_category');
    $type = null;
    $account_classification = $request->input('account_classification');
    $month = null;
    $sort = [["column" => "project_plans.plan_id", "sorting" => "desc"]];
    $filter = null;
    $pow = $request->input('pow');
    $APP = new APP;
    $project_plans = [];
    if ($request->sapp_number != null) {
      $filter = [["project_plans.app_group_no", $request->sapp_number]];
    }
    $filtered_data = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
    return back()->withInput()->with(['filtered_data' => $filtered_data]);
  }

  public function getTitles(Request $request)
  {
    $hint = '%' . $request->input('data') . '%';
    $project_plans = DB::table("project_plans")->select('project_plans.project_title')->where('project_title', 'like', $hint)->take(10)->get();
    return json_encode($project_plans);
  }

  public function autoCompletePlanTitlesForFile(Request $request)
  {
    $term = $request->term;

    $results = array();

    $project_plans = DB::table('project_plans')->select('project_plans.plan_id', 'project_plans.project_title', 'project_plans.project_type', 'project_plans.project_no')
      ->where([['project_plans.is_old', false], ['project_plans.project_title', 'LIKE', '%' . $term . '%'], ['project_timelines.timeline_status', 'set']])
      ->join('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')

      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->distinct()
      ->take(10)
      ->get();

    if (sizeOf($project_plans) != 0) {
      foreach ($project_plans as $project_plan) {
        $results[] = [
          'id' => $project_plan->plan_id,
          'value' => $project_plan->project_title,
          'project_number' => $project_plan->project_no,
          'project_type' => $project_plan->project_type
        ];
      }
    } else {
      $results[] = [
        'id' => '',
        'value' => 'No Match Found'
      ];
    }
    return response()->json($results);
  }

  public function autoCompletePlanTitles(Request $request)
  {
    $mode_id = $request->mode_id;
    $show_all = $request->show_all;

    $term = $request->term;
    if ($mode_id != null) {
      if ($mode_id == 1) {
        $mode_id = [1];
      } else {
        $mode_id = [2, 3];
      }
    }
    $results = array();
    if ($request->opening_date != null) {
      $opening_date = date("Y-m-d", strtotime($request->opening_date));
      $project_plans = DB::table('project_plans')
        ->select(
          'project_plans.plan_id',
          'project_plans.project_title',
          'project_plans.project_title',
          'project_plans.project_type',
          'project_plans.project_no',
          'procacts.*'
        )
        ->where([['project_plans.is_old', false], ['project_plans.status', '<>', 'complete'], ['project_plans.status', '<>', 'for_review'], ['project_plans.project_title', 'LIKE', '%' . $term . '%'], ['project_timelines.timeline_status', 'set'], ['project_timelines.bid_submission_start', $opening_date]]);

      if ($mode_id != null) {
        $project_plans = $project_plans->whereIn("mode_id", $mode_id);
      }

      if ($show_all != null) {
        $project_plans = $project_plans->join('procacts', 'project_plans.plan_id', 'procacts.plan_id');
      } else {
        $project_plans = $project_plans
          // ->join('procacts','project_plans.latest_procact_id','procacts.procact_id');
          ->join('procacts', 'project_plans.plan_id', 'procacts.plan_id');
      }

      $project_plans = $project_plans
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->distinct()
        ->take(10)
        ->get();
    } else {
      $project_plans = DB::table('project_plans')
        ->select('project_plans.plan_id', 'project_plans.project_title', 'project_plans.project_type', 'project_plans.project_no', 'procacts.*')
        ->where([['project_plans.is_old', false], ['project_plans.status', '<>', 'complete'], ['project_plans.status', '<>', 'for_review'], ['project_plans.project_title', 'LIKE', '%' . $term . '%'], ['project_timelines.timeline_status', 'set']]);
      if ($mode_id != null) {
        $project_plans = $project_plans->whereIn("mode_id", $mode_id);
      }

      if ($show_all != null) {
        $project_plans = $project_plans
          ->join('procacts', 'project_plans.plan_id', 'procacts.procact_id');
      } else {
        $project_plans = $project_plans
          ->join('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id');
      }

      $project_plans = $project_plans
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->distinct()
        ->take(10)
        ->get();
    }

    if (sizeOf($project_plans) != 0) {
      foreach ($project_plans as $project_plan) {
        $results[] = [
          'id' => $project_plan->plan_id,
          'procact_id' => $project_plan->procact_id,
          'value' => $project_plan->project_title,
          'project_number' => $project_plan->project_no,
          'project_type' => $project_plan->project_type
        ];
      }
    } else {
      $results[] = [
        'id' => '',
        'value' => 'No Match Found'
      ];
    }
    return response()->json($results);
  }

  public function autoCompleteUnreceivePlanTitles(Request $request)
  {
    $mode_id = $request->mode_id;
    $term = $request->term;
    if ($mode_id != null) {
      if ($mode_id == 1) {
        $mode_id = [1];
      } else if ($mode_id == 2) {
        $mode_id = [2];
      } else if ($mode_id == 3) {
        $mode_id = [3];
      } else {
        $mode_id = [1, 2, 3];
      }
    } else {
      $mode_id = [1, 2, 3];
    }
    if ($request->opening_date != null) {
      $opening_date = date('Y-m-d', strtotime($request->opening_date));
    } else {
      $opening_date = null;
    }

    $project_plans = DB::table('project_plans')->select('project_plans.plan_id', 'project_plans.project_title', 'project_plans.project_type', 'project_plans.project_no')
      ->where([['project_plans.status', '<>', 'complete'], ['project_plans.status', '<>', 'for_review'], ['project_plans.project_title', 'LIKE', '%' . $term . '%'], ['project_timelines.timeline_status', 'set'], ['project_timelines.bid_submission_start', $opening_date]]);
    if ($mode_id != null) {
      $project_plans = $project_plans->whereIn("mode_id", $mode_id);
    }

    $project_plans = $project_plans->join('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->distinct()
      ->take(10)
      ->get();

    if (sizeOf($project_plans) != 0) {
      foreach ($project_plans as $project_plan) {
        $results[] = [
          'id' => $project_plan->plan_id,
          'value' => $project_plan->project_title,
          'project_number' => $project_plan->project_no,
          'project_type' => $project_plan->project_type
        ];
      }
    } else {
      $results[] = [
        'id' => '',
        'value' => 'No Match Found'
      ];
    }
    return response()->json($results);
  }

  public function viewProject($id)
  {
    // Original Plan
    $project_plan = DB::table('project_plans')->where('project_plans.plan_id', $id)->first();

    $count = DB::table('project_plans')->where('plan_id', $id)->count();
    if ($count < 1) {
      return back();
    }

    // Project Details
    $project_details = DB::table('project_plans')->where('project_plans.plan_id', $id)
      ->select('*', 'childproject.plan_id as child_id', 'childproject.project_title as child_title', 'project_plans.*', 'project_plans.status as project_status')
      ->join('procacts', 'procacts.procact_id', 'project_plans.latest_procact_id')
      ->leftJoin('project_bidders', 'project_bidders.project_bid', 'project_plans.project_bid_id')
      ->leftJoin('twg_evaluations', "twg_evaluations.project_bid", "project_bidders.project_bid")
      ->join('municipalities', 'project_plans.municipality_id', '=', 'municipalities.municipality_id')
      ->leftJoin('barangays', 'project_plans.barangay_id', '=', 'barangays.barangay_id')
      ->join('projtypes', 'project_plans.projtype_id', '=', 'projtypes.projtype_id')
      ->join('procurement_modes', 'project_plans.mode_id', '=', 'procurement_modes.mode_id')
      ->join('funds', 'project_plans.fund_id', '=', 'funds.fund_id')
      ->join('account_classifications', 'project_plans.account_id', '=', 'account_classifications.account_id')
      ->leftJoin('resolution_projects', 'procacts.procact_id', '=', 'resolution_projects.procact_id')
      ->leftJoin('resolutions', 'resolution_projects.resolution_id', 'resolutions.resolution_id')
      ->leftJoin('project_plans as childproject', 'project_plans.plan_id', 'childproject.parent_id');

    if ($project_plan->mode_id == 1) {
      $project_details = $project_details->leftJoin('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->leftJoin('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->leftJoin('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')->first();
    } else if ($project_plan->mode_id == 2 || $project_plan->mode_id == 3) {
      $project_details = $project_details->leftJoin('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->leftJoin('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->leftJoin('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')->first();
    } else {
    }

    $project_timelines = DB::table('project_plans')
      ->where('project_plans.plan_id', $id)
      ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('project_activity_status', 'procacts.procact_id', 'project_activity_status.procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->orderBy('procacts.procact_id', 'asc')
      ->get();

    $procacts = DB::table('project_plans')
      ->where('project_plans.plan_id', $id)
      ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
      ->orderBy('procacts.procact_id', 'asc')
      ->get();

    $project_logs = DB::table("project_logs")->select('*', "project_logs.updated_at as date_updated")->where('plan_id', $id)->join('users', 'users.id', 'project_logs.user_id')->get();

    $APP = new APP;
    $project_bidders = $APP->getAllBidders($id);
    $disqualification_records = $APP->getBiddersDisqualificationRecords($id);

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('admin.project_details', ['links' => $links, 'user_privilege' => $user_privilege, "project_details" => $project_details, "project_bidders" => $project_bidders, "project_logs" => $project_logs, "project_timelines" => $project_timelines, "procacts" => $procacts, "disqualification_records" => $disqualification_records]);
  }

  public function createAsSAPP($id)
  {

    $data = DB::table('project_plans')->select('project_plans.*', 'sectors.sector_type')->where('plan_id', $id)
      ->leftJoin('sectors', 'project_plans.sector_id', "=", "sectors.sector_id")
      ->get();

    if (count($data) == 0) {
      return abort(404);
    } else {
      $year = $data[0]->project_year;
      $project_type = $data[0]->project_type;
      $title = "Create SAPP " . ucwords($data[0]->project_type) . " Plan";
      $municipalities = DB::table('municipalities')->orderBy('municipalities.municipality_name')->get();
      $projtypes = DB::table('projtypes')->orderBy('projtypes.type')->get();
      $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
      $funds = DB::table('funds')->orderBy('funds.source')->get();
      $classifications = DB::table('account_classifications')->orderBy('account_classifications.classification')->get();
      $data = json_encode($data);
      $links = getUserLinks();
      $user_privilege = getUserPrivilege();
      return view('admin.add_app', ['links' => $links, 'title' => $title, 'project_type' => $project_type, 'year' => $year, 'data' => $data, 'municipalities' => $municipalities, 'projtypes' => $projtypes, 'modes' => $modes, 'funds' => $funds, 'classifications' => $classifications]);
    }
  }

  public function downloadApp(Request $request)
  {
    $data = $request->validate([
      "project_year" => 'digits:4|integer|min:2020|max:' . (date('Y'))
    ]);

    $governor = DB::table('governors')->orderBy('governor_id', 'desc')->first();

    $bac = DB::table('bids_and_awards_committee')
      ->select(
        'bids_and_awards_committee.bac_id',
        DB::raw("CONCAT(bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname) AS bac_chairman_name"),
        DB::raw("CONCAT(bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name")
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
      ->select(DB::raw("CONCAT(member.member_fname,' ',if(member.member_minitial is null ,'',member.member_minitial),' ',member.member_lname) AS member_name"))
      ->where('bac_member.bac_member_type', 'BAC Infrastructure Member')
      ->join('member', 'member.member_id', '=', 'bac_member.member_id')->orderBy('bac_member.bac_member_arrangement', 'asc')->get();



    $bac_observers = DB::table('bac_observer')->where('bac_id', $bac->bac_id)
      ->select('observer.*', DB::raw("CONCAT(if(observer.observer_prefix is null ,'',CONCAT(observer.observer_prefix,' ')),observer.observer_fname,' ',if(observer.observer_minitial is null ,'',observer.observer_minitial),' ',observer.observer_lname) AS observer_name"))
      ->join('observer', 'observer.observer_id', '=', 'bac_observer.observer_id')
      ->get();

    $year = $request->input('project_year');
    $project_type = $request->input('app_type');
    $status = null;
    $mode = $request->input('mode_of_procurement');
    $municipality = null;
    $fund_category = $request->input('fund_category');
    $type = null;
    $account_classification = $request->input('account_classification');
    $month = null;
    $filter = null;
    $pow = null;
    $APP = new APP;
    $project_plans = [];

    $bold = ['font' => ['bold'  =>  true, 'size'  =>  11, 'name'  =>  'Arial', 'color' => array('rgb' => '000000'),]];
    $title = ['font' => ['bold'  =>  true, 'size'  =>  11, 'name'  =>  'Arial', 'color' => array('rgb' => '000000'),], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '32CD32']]];
    $align_left = ['font' => ['bold'  =>  true, 'size'  =>  11, 'name'  =>  'Arial', 'color' => array('rgb' => '000000'),], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '32CD32']]];
    $project_plans = DB::table('procacts')
      ->where([['procact_mode_id', 2], ['abc', '>', 1000000]])
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->get();
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path() . '\\' . "excel_templates/app_sapp.xlsx");
    $fund_categories = DB::table('fund_category')->orderBy('sort', 'asc')->get();

    // regular
    if ($project_type == "regular") {
      foreach ($fund_categories as $key => $value) {
        if (strpos($value->title, "GF")) {
          $filter = [['title', $value->title]];
          $sort = [["column" => "project_plans.account_id", "sorting" => "asc"], ["column" => "project_plans.abc_post_date", "sorting" => "asc"], ["column" => "project_plans.sub_open_date", "sorting" => "asc"], ["column" => "project_plans.award_notice_date", "sorting" => "asc"], ["column" => "project_plans.contract_signing_date", "sorting" => "asc"], ["column" => "project_plans.project_no", "sorting" => "asc"]];
          $data = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
          if (count($data) > 0) {
            $worksheet = $spreadsheet->setActiveSheetIndexByName('GF');

            //set signatories
            $worksheet->getCell('J20')->setValue(strtoupper(strtolower($governor->name)));
            $member_rows = ceil((count($bac_infra_members) + 2) / 4 * 3);
            $worksheet->insertNewRowBefore(25, $member_rows);
            for ($i = 25; $i < (25 + $member_rows); $i++) {
              $worksheet->mergeCells('A' . $i . ':' . 'D' . $i);
              $worksheet->mergeCells('E' . $i . ':' . 'G' . $i);
              $worksheet->mergeCells('H' . $i . ':' . 'K' . $i);
              $worksheet->mergeCells('L' . $i . ':' . 'O' . $i);
              if (($i % 3) == 1) {
                $worksheet->getStyle('A' . $i . ':' . 'O' . $i)->applyFromArray($bold);
                $worksheet->getStyle('A' . $i . ':' . 'O' . $i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
              } else {
                $worksheet->getStyle('A' . $i . ':' . 'O' . $i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
              }
              $worksheet->getStyle('A' . $i . ':' . 'O' . $i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }

            // insert data
            $member_row = 1;
            $member = 0;

            for ($i = 25; $i < (25 + $member_rows); $i += 3) {
              if ($member_row == 1) {
                $worksheet->getCell('A' . $i)->setValue(strtoupper(strtolower($bac->bac_chairman_name)));
                $worksheet->getCell('E' . $i)->setValue(strtoupper(strtolower($bac->bac_vice_chairman_name)));
                $worksheet->getCell('A' . ($i + 1))->setValue('BAC Chairperson');
                $worksheet->getCell('E' . ($i + 1))->setValue('BAC-Vice Chairperson');

                if (count($bac_infra_members) === 0) {
                } else if (count($bac_infra_members) === 1) {
                  $worksheet->getCell('H' . $i)->setValue(strtoupper(strtolower($bac_infra_members[$member]->member_name)));
                } else {
                  $worksheet->getCell('H' . $i)->setValue(strtoupper(strtolower($bac_infra_members[$member]->member_name)));
                  $worksheet->getCell('L' . $i)->setValue(strtoupper(strtolower($bac_infra_members[$member + 1]->member_name)));
                  $worksheet->getCell('H' . ($i + 1))->setValue('BAC Member');
                  $worksheet->getCell('L' . ($i + 1))->setValue('BAC Member');
                  $member = $member + 3;
                }
              } else {
                $position = 1;
                $member_checker = ($member_row * 4) - 2;
                while ($member <= $member_checker) {
                  if ($member <= count($bac_infra_members)) {
                    if ($position == 1) {
                      $column = "A";
                    } else if ($position == 2) {
                      $column = "E";
                    } else if ($position == 3) {
                      $column = "H";
                    } else {
                      $column = "L";
                    }
                    $worksheet->getCell($column . $i)->setValue(strtoupper(strtolower($bac_infra_members[$member - 1]->member_name)));
                    $worksheet->getCell($column . ($i + 1))->setValue('BAC Member');
                    $member = $member + 1;
                    $position = $position + 1;
                  } else {
                    break;
                  }
                }

                $member = $member + 5;
              }
              $member_row = $member_row + 1;
            }


            $row = 11;
            $item_number = 1;
            $init_classification = $data[0]->account_id;
            $start_row = 12;
            foreach ($data as $key => $value) {
              if ($value->account_id == $init_classification) {
                $row++;
              } else {
                $row = $row + 1;
                $worksheet->insertNewRowBefore($row, 1);
                $worksheet->getStyle('E' . $row . ':' . 'O' . $row)->applyFromArray($bold);
                $worksheet->getCell('E' . $row)->setValue("SUBTOTAL");
                $worksheet->getCell('M' . $row)->setValue("=sum(M" . $start_row . ":M" . ($row - 1) . ")");
                $worksheet->getCell('N' . $row)->setValue("=sum(N" . $start_row . ":N" . ($row - 1) . ")");
                $worksheet->getCell('O' . $row)->setValue("=sum(O" . $start_row . ":O" . ($row - 1) . ")");
                $init_classification = $value->account_id;
                $row += 3;
              }
              // get original mode of procurement
              $procurement_mode = DB::table('procacts')->where('plan_id', $value->plan_id)->orderBy('procact_id', 'asc')
                ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')->first();
              $worksheet->getCell('A' . $row)->setValue($item_number);
              $worksheet->getCell('B' . $row)->setValue($value->project_no);
              $worksheet->getCell('C' . $row)->setValue($value->account_code);
              $worksheet->getCell('D' . $row)->setValue($value->sector_name);
              $worksheet->getCell('E' . $row)->setValue($value->project_title);
              $worksheet->getCell('F' . $row)->setValue(strtoupper(strtolower($value->municipality_display)));
              if ($procurement_mode->mode === "SVP") {
                $procurement_mode->mode = "Small Value Procurement";
              } else if ($procurement_mode->mode === "Bidding") {
                $procurement_mode->mode = "Public Bidding";
              } else {
                $procurement_mode->mode = "Small Value Procurement - Negotiated Procurement";
              }
              $worksheet->getCell('G' . $row)->setValue($procurement_mode->mode);
              $worksheet->getCell('H' . $row)->setValue(date('M-Y', strtotime($value->abc_post_date)));
              $worksheet->getCell('I' . $row)->setValue(date('M-Y', strtotime($value->sub_open_date)));
              $worksheet->getCell('J' . $row)->setValue(date('M-Y', strtotime($value->award_notice_date)));
              $worksheet->getCell('K' . $row)->setValue(date('M-Y', strtotime($value->contract_signing_date)));
              $worksheet->getCell('L' . $row)->setValue($value->source);
              if ($value->account_id === 1) {
                $worksheet->getCell('N' . $row)->setValue($value->abc);
              } else {
                $worksheet->getCell('M' . $row)->setValue($value->abc);
              }
              $worksheet->getCell('O' . $row)->setValue($value->abc);
              $worksheet->getCell('P' . $row)->setValue($value->remarks);
              // if ($value->date_pow_added != null) {
              //   $worksheet->getCell('Q' . $row)->setValue(date('F d,Y', strtotime($value->date_pow_added)));
              // }
              $worksheet->insertNewRowBefore($row + 1, 1);
              $item_number++;
            }
          } else {
            $worksheet = $spreadsheet->setActiveSheetIndexByName('GF');
            $sheet_index = $spreadsheet->getActiveSheetIndex();
            $spreadsheet->removeSheetByIndex($sheet_index);
          }
        } else if (strpos($value->title, "PDF")) {
          $filter = [['title', $value->title]];
          $sort = [["column" => "projtypes.type", "sorting" => "asc"], ["column" => "project_plans.account_id", "sorting" => "asc"], ["column" => "project_plans.abc_post_date", "sorting" => "asc"], ["column" => "project_plans.sub_open_date", "sorting" => "asc"], ["column" => "project_plans.award_notice_date", "sorting" => "asc"], ["column" => "project_plans.contract_signing_date", "sorting" => "asc"]];
          $data = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
          if (count($data) > 0) {
            $types = $data->unique('type');
            $worksheet = $spreadsheet->setActiveSheetIndexByName('20%PDF');
            $row = 11;
            $item_number = 1;
            $init_type = "";
            $letter = "A";
            $total_mooe_formula = "=";
            $total_co_formula = "=";
            $total_formula = "=";
            //set signatories
            $worksheet->getCell('I16')->setValue(strtoupper(strtolower($governor->name)));
            $member_rows = ceil((count($bac_infra_members) + 2) / 4 * 3);
            $worksheet->insertNewRowBefore(22, $member_rows);
            for ($i = 22; $i < (22 + $member_rows); $i++) {
              $worksheet->mergeCells('A' . $i . ':' . 'D' . $i);
              $worksheet->mergeCells('E' . $i . ':' . 'F' . $i);
              $worksheet->mergeCells('H' . $i . ':' . 'K' . $i);
              $worksheet->mergeCells('L' . $i . ':' . 'O' . $i);
              if (($i % 3) == 1) {
                $worksheet->getStyle('A' . $i . ':' . 'O' . $i)->applyFromArray($bold);
                $worksheet->getStyle('A' . $i . ':' . 'O' . $i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
              } else {
                $worksheet->getStyle('A' . $i . ':' . 'O' . $i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
              }
              $worksheet->getStyle('A' . $i . ':' . 'O' . $i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }

            // insert data
            $member_row = 1;
            $member = 0;

            for ($i = 22; $i < (22 + $member_rows); $i += 3) {
              if ($member_row == 1) {
                $worksheet->getCell('A' . $i)->setValue(strtoupper(strtolower($bac->bac_chairman_name)));
                $worksheet->getCell('E' . $i)->setValue(strtoupper(strtolower($bac->bac_vice_chairman_name)));
                $worksheet->getCell('A' . ($i + 1))->setValue('BAC Chairperson');
                $worksheet->getCell('E' . ($i + 1))->setValue('BAC-Vice Chairperson');

                if (count($bac_infra_members) === 0) {
                } else if (count($bac_infra_members) === 1) {
                  $worksheet->getCell('H' . $i)->setValue(strtoupper(strtolower($bac_infra_members[$member]->member_name)));
                } else {
                  $worksheet->getCell('H' . $i)->setValue(strtoupper(strtolower($bac_infra_members[$member]->member_name)));
                  $worksheet->getCell('L' . $i)->setValue(strtoupper(strtolower($bac_infra_members[$member + 1]->member_name)));
                  $worksheet->getCell('H' . ($i + 1))->setValue('BAC Member');
                  $worksheet->getCell('L' . ($i + 1))->setValue('BAC Member');
                  $member = $member + 3;
                }
              } else {
                $position = 1;
                $member_checker = ($member_row * 4) - 2;
                while ($member <= $member_checker) {
                  if ($member <= count($bac_infra_members)) {
                    if ($position == 1) {
                      $column = "A";
                    } else if ($position == 2) {
                      $column = "E";
                    } else if ($position == 3) {
                      $column = "H";
                    } else {
                      $column = "L";
                    }
                    $worksheet->getCell($column . $i)->setValue(strtoupper(strtolower($bac_infra_members[$member - 1]->member_name)));
                    $worksheet->getCell($column . ($i + 1))->setValue('BAC Member');
                    $member = $member + 1;
                    $position = $position + 1;
                  } else {
                    break;
                  }
                }

                $member = $member + 5;
              }
              $member_row = $member_row + 1;
            }

            foreach ($types as $key => $value) {
              if ($init_type != $value->type) {
                $start_row = $row + 1;
                $worksheet->insertNewRowBefore($row + 1, 1);
                $worksheet->mergeCells('A' . $row . ':' . 'O' . $row);
                $worksheet->getStyle('A' . $row)->applyFromArray($title);
                $worksheet->getCell('A' . $row)->setValue($letter . ". " . $value->type);
                $letter = ++$letter;
                $init_type = $value->type;
                $row = $row + 1;
              }
              $filter = [['title', $value->title], ['type', $value->type]];
              $sort = [["column" => "projtypes.type", "sorting" => "asc"], ["column" => "project_plans.project_no", "sorting" => "asc"], ["column" => "project_plans.account_id", "sorting" => "asc"], ["column" => "project_plans.abc_post_date", "sorting" => "asc"], ["column" => "project_plans.sub_open_date", "sorting" => "asc"], ["column" => "project_plans.award_notice_date", "sorting" => "asc"], ["column" => "project_plans.contract_signing_date", "sorting" => "asc"]];
              $data_per_type = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
              foreach ($data_per_type as $key => $row_data) {
                $procurement_mode = DB::table('procacts')->where('plan_id', $row_data->plan_id)->orderBy('procact_id', 'asc')
                  ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')->first();

                $worksheet->insertNewRowBefore($row + 1, 1);
                $worksheet->getCell('A' . $row)->setValue($item_number);
                $worksheet->getCell('B' . $row)->setValue($row_data->project_no);
                $worksheet->getCell('C' . $row)->setValue($row_data->account_code);
                $worksheet->getCell('D' . $row)->setValue($row_data->project_title);
                $worksheet->getCell('E' . $row)->setValue(strtoupper(strtolower($row_data->municipality_name)));
                if ($procurement_mode->mode === "SVP") {
                  $procurement_mode->mode = "Small Value Procurement";
                } else if ($procurement_mode->mode === "Bidding") {
                  $procurement_mode->mode = "Public Bidding";
                } else {
                  $procurement_mode->mode = "Small Value Procurement - Negotiated Procurement";
                }
                $worksheet->getCell('F' . $row)->setValue($procurement_mode->mode);
                $worksheet->getCell('G' . $row)->setValue(date('M-Y', strtotime($row_data->abc_post_date)));
                $worksheet->getCell('H' . $row)->setValue(date('M-Y', strtotime($row_data->sub_open_date)));
                $worksheet->getCell('I' . $row)->setValue(date('M-Y', strtotime($row_data->award_notice_date)));
                $worksheet->getCell('J' . $row)->setValue(date('M-Y', strtotime($row_data->contract_signing_date)));
                $worksheet->getCell('K' . $row)->setValue($row_data->source);
                if ($row_data->account_id === 1) {
                  $worksheet->getCell('M' . $row)->setValue($row_data->abc);
                } else {
                  $worksheet->getCell('L' . $row)->setValue($row_data->abc);
                }
                $worksheet->getCell('N' . $row)->setValue($row_data->abc);
                $worksheet->getCell('O' . $row)->setValue($row_data->remarks);
                // if ($row_data->date_pow_added != null) {
                //   $worksheet->getCell('P' . $row)->setValue(date('F d,Y', strtotime($row_data->date_pow_added)));
                // }
                $row++;
                $item_number++;
              }
              $end_row = $row - 1;
              $worksheet->insertNewRowBefore($row + 1, 2);
              $worksheet->getStyle('D' . $row . ':' . 'M' . $row)->applyFromArray($bold);
              $worksheet->getCell('D' . $row)->setValue("SUBTOTAL");
              $worksheet->getCell('L' . $row)->setValue("=sum(L" . $start_row . ":L" . $end_row . ")");
              $worksheet->getCell('M' . $row)->setValue("=sum(M" . $start_row . ":M" . $end_row . ")");
              $worksheet->getCell('N' . $row)->setValue("=sum(N" . $start_row . ":N" . $end_row . ")");

              if ($total_formula == "=") {
                $total_mooe_formula = $total_mooe_formula . "L" . $row;
                $total_co_formula = $total_co_formula . "M" . $row;
                $total_formula = $total_formula . "N" . $row;
              } else {
                $total_mooe_formula = $total_mooe_formula . "+L" . $row;
                $total_co_formula = $total_co_formula . "+M" . $row;
                $total_formula = $total_formula . "+N" . $row;
              }
              $row += 2;
            }
            $worksheet->getStyle('D' . $row . ':' . 'N' . $row)->applyFromArray($bold);
            $worksheet->getCell('D' . $row)->setValue("TOTAL");
            $worksheet->getCell('L' . $row)->setValue($total_mooe_formula);
            $worksheet->getCell('M' . $row)->setValue($total_co_formula);
            $worksheet->getCell('N' . $row)->setValue($total_formula);
          } else {
            $worksheet = $spreadsheet->setActiveSheetIndexByName('20%PDF');
            $sheet_index = $spreadsheet->getActiveSheetIndex();
            $spreadsheet->removeSheetByIndex($sheet_index);
          }
        } else {
          $sheet_name = $value->title;
          if (strlen($sheet_name) >= 30) {
            $start = false;
            $abv = "";
            for ($i = 0; $i < strlen($sheet_name); $i++) {
              if ($sheet_name[$i] == "(") {
                $start = true;
              } else if ($sheet_name[$i] == ")") {
                $start = false;
              } else {
                if ($start == true) {
                  $abv = $abv . strtoupper($sheet_name[$i]);
                }
              }
            }
            $sheet_name = $abv;
          }
          $filter = [['title', $value->title]];
          $sort = [["column" => "project_plans.project_no", "sorting" => "asc"], ["column" => "project_plans.abc_post_date", "sorting" => "asc"], ["column" => "project_plans.sub_open_date", "sorting" => "asc"], ["column" => "project_plans.award_notice_date", "sorting" => "asc"], ["column" => "project_plans.contract_signing_date", "sorting" => "asc"]];
          $data = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);
          if (count($data) > 0) {
            $clonedWorksheet = clone $spreadsheet->getSheetByName('others');
            $clonedWorksheet->setTitle($sheet_name);
            $spreadsheet->addSheet($clonedWorksheet);
            $worksheet = $spreadsheet->setActiveSheetIndexByName($sheet_name);
            $worksheet->getCell('A6')->setValue($sheet_name);
            //set signatories
            $worksheet->getCell('J17')->setValue(strtoupper(strtolower($governor->name)));
            $member_rows = ceil((count($bac_infra_members) + 2) / 4 * 3);
            $worksheet->insertNewRowBefore(25, $member_rows);
            for ($i = 25; $i < (25 + $member_rows); $i++) {
              $worksheet->mergeCells('A' . $i . ':' . 'D' . $i);
              $worksheet->mergeCells('E' . $i . ':' . 'G' . $i);
              $worksheet->mergeCells('H' . $i . ':' . 'K' . $i);
              $worksheet->mergeCells('L' . $i . ':' . 'O' . $i);
              if (($i % 3) == 1) {
                $worksheet->getStyle('A' . $i . ':' . 'O' . $i)->applyFromArray($bold);
                $worksheet->getStyle('A' . $i . ':' . 'O' . $i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
              } else {
                $worksheet->getStyle('A' . $i . ':' . 'O' . $i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
              }
              $worksheet->getStyle('A' . $i . ':' . 'O' . $i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }

            // insert data
            $member_row = 1;
            $member = 0;

            for ($i = 25; $i < (25 + $member_rows); $i += 3) {
              if ($member_row == 1) {
                $worksheet->getCell('A' . $i)->setValue(strtoupper(strtolower($bac->bac_chairman_name)));
                $worksheet->getCell('E' . $i)->setValue(strtoupper(strtolower($bac->bac_vice_chairman_name)));
                $worksheet->getCell('A' . ($i + 1))->setValue('BAC Chairperson');
                $worksheet->getCell('E' . ($i + 1))->setValue('BAC-Vice Chairperson');

                if (count($bac_infra_members) === 0) {
                } else if (count($bac_infra_members) === 1) {
                  $worksheet->getCell('H' . $i)->setValue(strtoupper(strtolower($bac_infra_members[$member]->member_name)));
                } else {
                  $worksheet->getCell('H' . $i)->setValue(strtoupper(strtolower($bac_infra_members[$member]->member_name)));
                  $worksheet->getCell('L' . $i)->setValue(strtoupper(strtolower($bac_infra_members[$member + 1]->member_name)));
                  $worksheet->getCell('H' . ($i + 1))->setValue('BAC Member');
                  $worksheet->getCell('L' . ($i + 1))->setValue('BAC Member');
                  $member = $member + 3;
                }
              } else {
                $position = 1;
                $member_checker = ($member_row * 4) - 2;
                while ($member <= $member_checker) {
                  if ($member <= count($bac_infra_members)) {
                    if ($position == 1) {
                      $column = "A";
                    } else if ($position == 2) {
                      $column = "E";
                    } else if ($position == 3) {
                      $column = "H";
                    } else {
                      $column = "L";
                    }
                    $worksheet->getCell($column . $i)->setValue(strtoupper(strtolower($bac_infra_members[$member - 1]->member_name)));
                    $worksheet->getCell($column . ($i + 1))->setValue('BAC Member');
                    $member = $member + 1;
                    $position = $position + 1;
                  } else {
                    break;
                  }
                }

                $member = $member + 5;
              }
              $member_row = $member_row + 1;
            }



            $row = 10;
            $item_number = 1;
            $init_classification = $data[0]->account_id;
            $start_row = 11;
            foreach ($data as $key => $value) {
              $row++;
              $procurement_mode = DB::table('procacts')->where('plan_id', $value->plan_id)->orderBy('procact_id', 'asc')
                ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')->first();
              $worksheet->getCell('A' . $row)->setValue($item_number);
              $worksheet->getCell('B' . $row)->setValue($value->project_no);
              $worksheet->getCell('C' . $row)->setValue($value->account_code);
              $worksheet->getCell('D' . $row)->setValue($value->project_title);
              $worksheet->getCell('E' . $row)->setValue(strtoupper(strtolower($value->municipality_display)));
              if ($procurement_mode->mode === "SVP") {
                $procurement_mode->mode = "Small Value Procurement";
              } else if ($procurement_mode->mode === "Bidding") {
                $procurement_mode->mode = "Public Bidding";
              } else {
                $procurement_mode->mode = "Small Value Procurement - Negotiated Procurement";
              }
              $worksheet->getCell('F' . $row)->setValue($procurement_mode->mode);
              $worksheet->getCell('G' . $row)->setValue(date('M-Y', strtotime($value->abc_post_date)));
              $worksheet->getCell('H' . $row)->setValue(date('M-Y', strtotime($value->sub_open_date)));
              $worksheet->getCell('I' . $row)->setValue(date('M-Y', strtotime($value->award_notice_date)));
              $worksheet->getCell('J' . $row)->setValue(date('M-Y', strtotime($value->contract_signing_date)));
              $worksheet->getCell('K' . $row)->setValue($value->source);
              if ($value->account_id === 1) {
                $worksheet->getCell('M' . $row)->setValue($value->abc);
              } else {
                $worksheet->getCell('L' . $row)->setValue($value->abc);
              }
              $worksheet->getCell('N' . $row)->setValue($value->abc);
              $worksheet->getCell('O' . $row)->setValue($value->remarks);
              if ($value->date_pow_added != null) {
                $worksheet->getCell('P' . $row)->setValue(date('F d,Y', strtotime($value->date_pow_added)));
              }
              $worksheet->insertNewRowBefore($row + 1, 1);
              $item_number++;
            }
            $end_row = $row;
            $row++;
            $worksheet->getStyle('D' . $row . ':' . 'P' . $row)->applyFromArray($bold);
            $worksheet->getCell('D' . $row)->setValue("TOTAL");
            $worksheet->getCell('L' . $row)->setValue("=sum(L" . $start_row . ":L" . $end_row . ")");
            $worksheet->getCell('M' . $row)->setValue("=sum(M" . $start_row . ":M" . $end_row . ")");
            $worksheet->getCell('N' . $row)->setValue("=sum(N" . $start_row . ":N" . $end_row . ")");
          }
        }
      }
      $worksheet = $spreadsheet->setActiveSheetIndexByName('others');
      $sheet_index = $spreadsheet->getActiveSheetIndex();
      $spreadsheet->removeSheetByIndex($sheet_index);
      $spreadsheet->setActiveSheetIndexByName('SAPP');
      $sheet_index = $spreadsheet->getActiveSheetIndex();
      $spreadsheet->removeSheetByIndex($sheet_index);
      $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
      $writer->save(public_path() . '\\' . "excel_templates/APP.xlsx");
      return  response()->download(public_path() . '\\' . "excel_templates/APP.xlsx")->deleteFileAfterSend(true);
    }

    // supplemental
    else {
      $filter = null;
      if ($request->sapp_number != null) {
        $filter = [["project_plans.app_group_no", $request->sapp_number]];
      }
      $sort = [["column" => DB::RAW('cast(project_plans.app_group_no as unsigned)'), "sorting" => "asc"], ["column" => "fund_category.sort", "sorting" => "asc"], ["column" => "project_plans.project_no", "sorting" => "asc"], ["column" => "project_plans.abc_post_date", "sorting" => "asc"], ["column" => "project_plans.sub_open_date", "sorting" => "asc"], ["column" => "project_plans.award_notice_date", "sorting" => "asc"], ["column" => "project_plans.contract_signing_date", "sorting" => "asc"]];
      $data = $APP->getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, false);

      if (count($data) > 0) {
        $init_sapp = "initial";
        $co_total = null;
        $mooe_total = null;
        $total = null;
        $row = 8;
        $start_row = 8;
        $worksheet = (object)[];

        foreach ($data as $key => $value) {
          if ($value->app_group_no != $init_sapp) {
            if ($init_sapp != "initial") {
              // subtotal
              $worksheet->insertNewRowBefore($row + 1, 1);
              $worksheet->getStyle('C' . $row . ':' . 'M' . $row)->applyFromArray($bold);
              $worksheet->getCell('C' . $row)->setValue("SUBTOTAL");
              $worksheet->getCell('K' . $row)->setValue("=SUM(K" . $start_row . ":K" . ($row - 1) . ")");
              $worksheet->getCell('L' . $row)->setValue("=SUM(L" . $start_row . ":L" . ($row - 1) . ")");
              $worksheet->getCell('M' . $row)->setValue("=SUM(M" . $start_row . ":M" . ($row - 1) . ")");
              if ($total == null) {
                $co_total = "=K" . $row;
                $mooe_total = "=K" . $row;
                $total = "=M" . $row;
              } else {
                $co_total = $co_total . "+K" . $row;
                $mooe_total = $mooe_total . "+L" . $row;
                $total = $total . "+M" . $row;
              }
              $row++;
              // Total
              $worksheet->insertNewRowBefore($row + 1, 1);
              $worksheet->getStyle('C' . $row . ':' . 'M' . $row)->applyFromArray($bold);
              $worksheet->getCell('C' . $row)->setValue("TOTAL");
              $worksheet->getCell('K' . $row)->setValue($co_total);
              $worksheet->getCell('L' . $row)->setValue($mooe_total);
              $worksheet->getCell('M' . $row)->setValue($total);
              $row++;
            }

            $letter = "A";
            $item_number = 1;
            $initial_fund = "";
            $row = 8;
            $co_total = null;
            $mooe_total = null;
            $total = null;
            $init_sapp = $value->app_group_no;
            $clonedWorksheet = clone $spreadsheet->getSheetByName('SAPP');
            $sheet_name = "SAPP" . $value->app_group_no;
            if ($value->app_group_no == null) {
              $sheet_name = "SAPP_recheck_groupings";
            }
            $clonedWorksheet->setTitle($sheet_name);
            $spreadsheet->addSheet($clonedWorksheet);
            $worksheet = $spreadsheet->setActiveSheetIndexByName($sheet_name);
            $worksheet->getCell('A4')->setValue("SUPPLEMENTAL APP NO. " . $value->app_group_no);
            //set signatories
            $worksheet->getCell('M13')->setValue(strtoupper(strtolower($governor->name)));
            $member_rows = ceil((count($bac_infra_members) + 2) / 4 * 3);
            $worksheet->insertNewRowBefore(18, $member_rows);
            for ($i = 18; $i < (18 + $member_rows); $i++) {
              $worksheet->mergeCells('E' . $i . ':' . 'F' . $i);
              $worksheet->mergeCells('G' . $i . ':' . 'K' . $i);
              if (($i % 3) == 0) {
                $worksheet->getStyle('A' . $i . ':' . 'O' . $i)->applyFromArray($bold);
                $worksheet->getStyle('A' . $i . ':' . 'O' . $i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
              } else {
                $worksheet->getStyle('A' . $i . ':' . 'O' . $i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
              }
              $worksheet->getStyle('A' . $i . ':' . 'O' . $i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }

            // // insert data
            $member_row = 1;
            $member = 0;

            for ($i = 18; $i < (18 + $member_rows); $i += 3) {
              if ($member_row == 1) {
                $worksheet->getCell('C' . $i)->setValue(strtoupper(strtolower($bac->bac_chairman_name)));
                $worksheet->getCell('E' . $i)->setValue(strtoupper(strtolower($bac->bac_vice_chairman_name)));
                $worksheet->getCell('C' . ($i + 1))->setValue('BAC Chairperson');
                $worksheet->getCell('E' . ($i + 1))->setValue('BAC-Vice Chairperson');

                if (count($bac_infra_members) === 0) {
                } else if (count($bac_infra_members) === 1) {
                  $worksheet->getCell('G' . $i)->setValue(strtoupper(strtolower($bac_infra_members[$member]->member_name)));
                } else {
                  $worksheet->getCell('G' . $i)->setValue(strtoupper(strtolower($bac_infra_members[$member]->member_name)));
                  $worksheet->getCell('L' . $i)->setValue(strtoupper(strtolower($bac_infra_members[$member + 1]->member_name)));
                  $worksheet->getCell('G' . ($i + 1))->setValue('BAC Member');
                  $worksheet->getCell('L' . ($i + 1))->setValue('BAC Member');
                  $member = $member + 3;
                }
              } else {
                $position = 1;
                $member_checker = ($member_row * 4) - 2;
                while ($member <= $member_checker) {
                  if ($member <= count($bac_infra_members)) {
                    if ($position == 1) {
                      $column = "C";
                    } else if ($position == 2) {
                      $column = "E";
                    } else if ($position == 3) {
                      $column = "G";
                    } else {
                      $column = "L";
                    }
                    $worksheet->getCell($column . $i)->setValue(strtoupper(strtolower($bac_infra_members[$member - 1]->member_name)));
                    $worksheet->getCell($column . ($i + 1))->setValue('BAC Member');
                    $member = $member + 1;
                    $position = $position + 1;
                  } else {
                    break;
                  }
                }

                $member = $member + 5;
              }
              $member_row = $member_row + 1;
            }
          }
          if ($initial_fund != $value->title) {
            if ($initial_fund != "") {
              $worksheet->insertNewRowBefore($row + 1, 1);
              $worksheet->getStyle('C' . $row . ':' . 'M' . $row)->applyFromArray($bold);
              $worksheet->getCell('C' . $row)->setValue("SUBTOTAL");
              $worksheet->getCell('K' . $row)->setValue("=SUM(K" . $start_row . ":K" . ($row - 1) . ")");
              $worksheet->getCell('L' . $row)->setValue("=SUM(L" . $start_row . ":L" . ($row - 1) . ")");
              $worksheet->getCell('M' . $row)->setValue("=SUM(M" . $start_row . ":M" . ($row - 1) . ")");
              if ($total == null) {
                $co_total = "=K" . $row;
                $mooe_total = "=L" . $row;
                $total = "=M" . $row;
              } else {
                $co_total = $co_total . "+K" . $row;
                $mooe_total = $mooe_total . "+L" . $row;
                $total = $total . "+M" . $row;
              }
              $row++;
            }
            $worksheet->insertNewRowBefore($row + 1, 1);
            $worksheet->mergeCells('A' . $row . ':' . 'N' . $row);
            $worksheet->getStyle('A' . $row)->applyFromArray($align_left);
            $worksheet->getCell('A' . $row)->setValue($letter . ".) " . $value->title);
            $row++;
            $start_row = $row;
            $initial_fund = $value->title;
            $letter = ++$letter;
          }

          $worksheet->insertNewRowBefore($row + 1, 1);
          $procurement_mode = DB::table('procacts')->where('plan_id', $value->plan_id)->orderBy('procact_id', 'asc')
            ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')->first();
          $worksheet->getCell('A' . $row)->setValue($item_number);
          $worksheet->getCell('B' . $row)->setValue($value->project_no);
          $worksheet->getCell('C' . $row)->setValue($value->project_title);
          $worksheet->getCell('D' . $row)->setValue($value->municipality_name);
          if ($procurement_mode->mode === "SVP") {
            $procurement_mode->mode = "Small Value Procurement";
          } else if ($procurement_mode->mode === "Bidding") {
            $procurement_mode->mode = "Public Bidding";
          } else {
            $procurement_mode->mode = "Small Value Procurement - Negotiated Procurement";
          }
          $worksheet->getCell('E' . $row)->setValue($procurement_mode->mode);
          $worksheet->getCell('F' . $row)->setValue(date('M-Y', strtotime($value->abc_post_date)));
          $worksheet->getCell('G' . $row)->setValue(date('M-Y', strtotime($value->sub_open_date)));
          $worksheet->getCell('H' . $row)->setValue(date('M-Y', strtotime($value->award_notice_date)));
          $worksheet->getCell('I' . $row)->setValue(date('M-Y', strtotime($value->contract_signing_date)));
          $worksheet->getCell('J' . $row)->setValue($value->source);
          if ($value->account_id === 1) {
            $worksheet->getCell('L' . $row)->setValue($value->abc);
          } else {
            $worksheet->getCell('K' . $row)->setValue($value->abc);
          }
          $worksheet->getCell('M' . $row)->setValue($value->abc);
          $worksheet->getCell('N' . $row)->setValue($value->remarks);
          $row++;
          $item_number++;
        }

        // last subtotal and total_abc
        $worksheet->insertNewRowBefore($row + 1, 1);
        $worksheet->getStyle('C' . $row . ':' . 'M' . $row)->applyFromArray($bold);
        $worksheet->getCell('C' . $row)->setValue("SUBTOTAL");
        $worksheet->getCell('K' . $row)->setValue("=SUM(K" . $start_row . ":K" . ($row - 1) . ")");
        $worksheet->getCell('L' . $row)->setValue("=SUM(L" . $start_row . ":L" . ($row - 1) . ")");
        $worksheet->getCell('M' . $row)->setValue("=SUM(M" . $start_row . ":M" . ($row - 1) . ")");
        if ($total == null) {
          $co_total = "=K" . $row;
          $mooe_total = "=L" . $row;
          $total = "=M" . $row;
        } else {
          $co_total = $co_total . "+K" . $row;
          $mooe_total = $mooe_total . "+L" . $row;
          $total = $total . "+M" . $row;
        }
        $row++;
        $worksheet->insertNewRowBefore($row + 1, 1);
        $worksheet->getStyle('C' . $row . ':' . 'M' . $row)->applyFromArray($bold);
        $worksheet->getCell('C' . $row)->setValue("TOTAL");
        $worksheet->getCell('K' . $row)->setValue($co_total);
        $worksheet->getCell('L' . $row)->setValue($mooe_total);
        $worksheet->getCell('M' . $row)->setValue($total);

        $worksheet = $spreadsheet->setActiveSheetIndexByName('GF');
        $sheet_index = $spreadsheet->getActiveSheetIndex();
        $spreadsheet->removeSheetByIndex($sheet_index);

        $worksheet = $spreadsheet->setActiveSheetIndexByName('20%PDF');
        $sheet_index = $spreadsheet->getActiveSheetIndex();
        $spreadsheet->removeSheetByIndex($sheet_index);

        $worksheet = $spreadsheet->setActiveSheetIndexByName('others');
        $sheet_index = $spreadsheet->getActiveSheetIndex();
        $spreadsheet->removeSheetByIndex($sheet_index);

        $worksheet = $spreadsheet->setActiveSheetIndexByName('SAPP');
        $sheet_index = $spreadsheet->getActiveSheetIndex();
        $spreadsheet->removeSheetByIndex($sheet_index);

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save(public_path() . '\\' . "excel_templates/SAPP.xlsx");
        return  response()->download(public_path() . '\\' . "excel_templates/SAPP.xlsx")->deleteFileAfterSend(true);
      } else {
        return back()->with("message", "no data found");
      }
    }
  }
}
