<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;
use App\{App, ProjectPlans, RequestForExtension, RequestForExtensionBids};

class RequestForExtensionController extends Controller
{
  function getRequestForExtensions(Request $request)
  {
    $title = "Requests for Extension";
    if ($request->year != null) {
      $year = $request->year;
      $requests = RequestForExtension::where('request_date_generated', 'like', $year . '%')->with('bids')->orderBy('request_id', 'desc')->get();
      return back()->withInput()->with('requests', $requests);
    } else {
      $year = date('Y');
      $requests = RequestForExtension::where('request_date_generated', 'like', $year . '%')->with('bids')->orderBy('request_id', 'desc')->get();
      $links = getUserLinks();
      $user_privilege = getUserPrivilege();
      return view('twg.request_for_extension', ['links' => $links, 'user_privilege' => $user_privilege, "title" => $title, "year" => $year, 'requests' => $requests]);
    }
  }

  function getBACRequestForExtensions(Request $request)
  {
    $title = "Requests for Extension";
    if ($request->year != null) {
      $year = $request->year;
      $requests = RequestForExtension::where('request_date_generated', 'like', $year . '%')->with('bids')->orderBy('request_id', 'desc')->get();
      return back()->withInput()->with('requests', $requests);
    } else {
      $year = date('Y');
      $requests = RequestForExtension::where('request_date_generated', 'like', $year . '%')->with('bids')->orderBy('request_id', 'desc')->get();
      $links = getUserLinks();
      $user_privilege = getUserPrivilege();
      return view('twg.request_for_extension', ['links' => $links, 'user_privilege' => $user_privilege, "title" => $title, "year" => $year, 'requests' => $requests]);
    }
  }

  function  getRequestForExtensionForm(Request $request)
  {
    $user_privilege = getUserPrivilegeByLink('bac_request_for_extension');
    $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();
    if (isset($request->id) == false) {
      $request_for_extension = null;
      $project_bidders = null;
      $title = "Create Request for Extension";
      $access = checkUserAccess('add', $user_privilege);
    } else {
      $request_for_extension = RequestForExtension::find($request->id);
      $project_bidders = RequestForExtensionBids::select(DB::raw('group_concat(project_bid  separator ",") as project_bids'))->where('request_id', $request->id)->first();
      $project_bidders = $project_bidders->project_bids;
      $title = "Edit Request for Extension";
      $access = checkUserAccess('update', $user_privilege);
    }
    $links = getUserLinks();
    return view('twg.request_for_extension_form', ['links' => $links, 'user_privilege' => $user_privilege, "title" => $title, "modes" => $modes, "request_for_extension" => $request_for_extension, "project_bidders" => $project_bidders]);
  }

  function  viewRequestForExtensionForm(Request $request)
  {


    $modes = DB::table('procurement_modes')->orderBy('procurement_modes.mode')->get();

    $request_for_extension = RequestForExtension::find($request->id);
    $project_bidders = RequestForExtensionBids::select(DB::raw('group_concat(project_bid  separator ",") as project_bids'))->where('request_id', $request->id)->first();
    $project_bidders = $project_bidders->project_bids;
    $title = "View Request for Extension";



    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view('admin.request_for_extension_form', ["links" => $links, 'user_privilege' => $user_privilege, "title" => $title, "modes" => $modes, "request_for_extension" => $request_for_extension, "project_bidders" => $project_bidders]);
  }

  function getOngoingPostual(Request $request)
  {
    $APP = new App;
    $mode_of_procurement = $request->input('mode_of_procurement');
    $length = $request->input('length');
    $search = $request->input('search')['value'];
    $order = $request->input('order');
    $start = $request->input('start');
    $draw = $request->input('draw');
    $columns = $request->input('columns');

    if ($mode_of_procurement == "1") {
      $project_plans = DB::table('bid_doc_projects')
        ->select(DB::raw('min(LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words)) '), 'procurement_modes.*', 'procacts.*', 'project_plans.*', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), "bid_docs.*", "procacts.*", "contractors.*", "project_bidders.*", "twg_evaluations.twg_evaluation_status", "twg_evaluations.twg_evaluation_remarks", "twg_evaluations.twg_final_bid_evaluation", "twg_evaluations.post_qual_start", "twg_evaluations.post_qual_end")
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procurement_modes', 'procacts.procact_mode_id', '=', 'procurement_modes.mode_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->where('procact_mode_id', $mode_of_procurement)
        ->groupBy('procacts.procact_id');
    } else {
      $project_plans = DB::table('rfq_projects')
        ->select(DB::raw('min(LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words)) '), 'procurement_modes.*', 'procacts.*', 'project_plans.*', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), "rfqs.*", "procacts.*", "contractors.*", "project_bidders.*", "twg_evaluations.twg_evaluation_status", "twg_evaluations.twg_evaluation_remarks", "twg_evaluations.twg_final_bid_evaluation", "twg_evaluations.post_qual_start", "twg_evaluations.post_qual_end")
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
        ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procurement_modes', 'procacts.procact_mode_id', '=', 'procurement_modes.mode_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->where('procact_mode_id', $mode_of_procurement)
        ->groupBy('procacts.procact_id');
    }



    if ($search != null) {
      $project_plans = $project_plans
        ->where([['project_plans.status', 'onprocess'], ['twg_evaluations.twg_evaluation_id', null], ['bid_evaluation', '<>', null], ['project_title', 'like', '%' . $search . '%']])
        ->orWhere([['project_plans.status', 'onprocess'], ['twg_evaluations.twg_evaluation_id', null], ['bid_evaluation', '<>', null], ['procacts.open_bid', 'like', '%' . $search . '%']])
        ->orWhere([['project_plans.status', 'onprocess'], ['twg_evaluations.twg_evaluation_id', null], ['bid_evaluation', '<>', null], ['contractors.business_name', 'like', '%' . $search . '%']])
        ->orWhere([['project_plans.status', 'onprocess'], ['twg_evaluations.twg_evaluation_id', null], ['bid_evaluation', '<>', null], ['project_plans.project_no', 'like', '%' . $search . '%']]);
    } else {
      $project_plans = $project_plans->where([['project_plans.status', 'onprocess'], ['twg_evaluations.twg_evaluation_id', null], ['bid_evaluation', '<>', null], ['project_bidders.bid_status', 'active']]);
    }

    if (count($order) != null) {

      $column = $order[0]['column'];
      $dir = $order[0]['dir'];
      $column_name = $columns[intval($column)]['data'];
      if ($column_name != "consumed_days") {
        if ($column_name == "plan_id") {
          $column_name = "project_plans." . $column_name;
        }
        if ($column_name == "project_bid") {
          $column_name = "project_bidders." . $column_name;
        }
        if ($column_name == "open_bid") {
          $column_name = "procacts." . $column_name;
        }
        $project_plans = $project_plans
          ->orderBy($column_name, $dir);
      } else {
        $project_plans = $project_plans
          ->orderBy('procacts.open_bid', $dir)
          ->orderBy('procacts.itb_arrangement', 'asc')
          ->orderBy('minimum_cost', 'asc');
      }
    } else {
      $project_plans = $project_plans
        ->orderBy('procacts.open_bid', 'asc')
        ->orderBy('procacts.itb_arrangement', 'asc')
        ->orderBy('minimum_cost', 'asc');
    }


    $count = count($project_plans->get());
    $project_plans = $project_plans->skip($start)->limit($length)->get();
    foreach ($project_plans as $plan) {
      $bidders = $APP->getBiddersData($plan->procact_id, 'non-responsive');
      $non_responsive_count = count($bidders);
      if ($non_responsive_count > 0) {
        $last_bidder = $bidders[$non_responsive_count - 1];
        $consumed_days = round((strtotime(date('Y-m-d')) - strtotime($last_bidder->post_qual_end)) / (60 * 60 * 24));
      } else {
        $consumed_days = round((strtotime(date('Y-m-d')) - strtotime($plan->open_bid)) / (60 * 60 * 24));
      }
      $plan->consumed_days = $consumed_days;
    }

    $data = [
      "draw" => $draw,
      "recordsTotal" => $count,
      "recordsFiltered" => $count,
      "data" => $project_plans
    ];

    return $data;
  }

  function getSelectedBids(Request $request)
  {
    $APP = new App;
    $length = $request->input('length');
    $search = $request->input('search')['value'];
    $order = $request->input('order');
    $start = $request->input('start');
    $draw = $request->input('draw');
    $columns = $request->input('columns');
    $project_bidders = explode(",", $request->input('project_bidders'));

    $project_plans1 = DB::table('bid_doc_projects')
      ->select(DB::raw('min(LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words)) '), 'procurement_modes.*', 'procacts.*', 'project_plans.*', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), "bid_docs.*", "procacts.*", "contractors.*", "project_bidders.*", "twg_evaluations.twg_evaluation_status", "twg_evaluations.twg_evaluation_remarks", "twg_evaluations.twg_final_bid_evaluation", "twg_evaluations.post_qual_start", "twg_evaluations.post_qual_end")
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->join('procurement_modes', 'procacts.procact_mode_id', '=', 'procurement_modes.mode_id')
      ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
      ->whereIn('project_bidders.project_bid', $project_bidders)
      ->groupBy('procacts.procact_id');

    $project_plans2 = DB::table('rfq_projects')
      ->select(DB::raw('min(LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words)) '), 'procurement_modes.*', 'procacts.*', 'project_plans.*', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), "rfqs.*", "procacts.*", "contractors.*", "project_bidders.*", "twg_evaluations.twg_evaluation_status", "twg_evaluations.twg_evaluation_remarks", "twg_evaluations.twg_final_bid_evaluation", "twg_evaluations.post_qual_start", "twg_evaluations.post_qual_end")
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('procurement_modes', 'procacts.procact_mode_id', '=', 'procurement_modes.mode_id')
      ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
      ->whereIn('project_bidders.project_bid', $project_bidders)
      ->groupBy('procacts.procact_id');


    if ($search != null) {
      $project_plans1 = $project_plans1
        ->where([['bid_evaluation', '<>', null], ['project_title', 'like', '%' . $search . '%']])
        ->orWhere([['bid_evaluation', '<>', null], ['procacts.open_bid', 'like', '%' . $search . '%']])
        ->orWhere([['bid_evaluation', '<>', null], ['contractors.business_name', 'like', '%' . $search . '%']])
        ->orWhere([['bid_evaluation', '<>', null], ['project_plans.project_no', 'like', '%' . $search . '%']]);

      $project_plans2 = $project_plans2
        ->where([['bid_evaluation', '<>', null], ['project_title', 'like', '%' . $search . '%']])
        ->orWhere([['bid_evaluation', '<>', null], ['procacts.open_bid', 'like', '%' . $search . '%']])
        ->orWhere([['bid_evaluation', '<>', null], ['contractors.business_name', 'like', '%' . $search . '%']])
        ->orWhere([['bid_evaluation', '<>', null], ['project_plans.project_no', 'like', '%' . $search . '%']]);
    } else {
      $project_plans1 = $project_plans1->where('bid_evaluation', '<>', null);
      $project_plans2 = $project_plans2->where('bid_evaluation', '<>', null);
    }

    if (count($order) != null) {

      $column = $order[0]['column'];
      $dir = $order[0]['dir'];
      $column_name = $columns[intval($column)]['data'];
      if ($column_name != "consumed_days") {
        if ($column_name == "plan_id") {
          $column_name = "project_plans." . $column_name;
        }
        if ($column_name == "project_bid") {
          $column_name = "project_bidders." . $column_name;
        }
        if ($column_name == "open_bid") {
          $column_name = "procacts." . $column_name;
        }
        $project_plans1 = $project_plans1
          ->orderBy($column_name, $dir);
        $project_plans2 = $project_plans2
          ->orderBy($column_name, $dir);
      } else {
        $project_plans1 = $project_plans1
          ->orderBy('procacts.open_bid', $dir)
          ->orderBy('procacts.itb_arrangement', 'asc')
          ->orderBy('minimum_cost', 'asc');

        $project_plans2 = $project_plans2
          ->orderBy('procacts.open_bid', $dir)
          ->orderBy('procacts.itb_arrangement', 'asc')
          ->orderBy('minimum_cost', 'asc');
      }
    } else {
      $project_plans1 = $project_plans1
        ->orderBy('procacts.open_bid', 'asc')
        ->orderBy('procacts.itb_arrangement', 'asc')
        ->orderBy('minimum_cost', 'asc');

      $project_plans2 = $project_plans2
        ->orderBy('procacts.open_bid', 'asc')
        ->orderBy('procacts.itb_arrangement', 'asc')
        ->orderBy('minimum_cost', 'asc');
    }
    $project_plans1 = $project_plans1->get()->toArray();
    $project_plans2 = $project_plans2->get()->toArray();

    $project_plans = array_merge($project_plans1, $project_plans2);

    $count = count($project_plans);

    if ($count === 0) {
      $output = [];
    } else {
      if (($start + $length) > $count) {
        $length = $count % $length;
      }
      $output = array_slice($project_plans, $start, $length);
    }
    //
    // foreach($project_plans as $plan){
    //     $bidders=$APP->getBiddersData($plan->procact_id,'non-responsive');
    //     $non_responsive_count=count($bidders);
    //     if($non_responsive_count>0){
    //         $last_bidder=$bidders[$non_responsive_count-1];
    //         $consumed_days=round((strtotime(date('Y-m-d'))-strtotime($last_bidder->post_qual_end))/(60 * 60 * 24));
    //     }
    //     else{
    //         $consumed_days=round((strtotime(date('Y-m-d'))-strtotime($plan->open_bid))/(60 * 60 * 24));
    //     }
    //     $plan->consumed_days=$consumed_days;
    // }

    $data = [
      "draw" => $draw,
      "recordsTotal" => $count,
      "recordsFiltered" => $count,
      "data" => $output
    ];

    return $data;
  }

  function submitRequestForExtension(Request $request)
  {
    $max_date = Date('m/d/Y', strtotime(max(explode(',', $request->opening_dates)) . ' + 12 days'));
    $data = $request->validate([
      "project_bidders" => "required",
      "date_generated" => "required|before:tomorrow",
      "request_date" => "required|after:date_generated|after:" . $max_date,
      "reason" => "required",
      "remarks" => "nullable"
    ]);

    $date_generated = Date('Y-m-d', strtotime($request->date_generated));
    $request_date = Date('Y-m-d', strtotime($request->request_date));
    $project_bidders = explode(",", $request->project_bidders);
    $message = "success";
    $request_id = $request->request_id;

    // dd($request->project_bidders);
    if ($request_id === null) {

      $duplicate = RequestForExtension::where([
        ["request_date_generated", $date_generated],
        ["request_date", $request_date]
      ])->count();
      if (false) {
        $message = "duplicate";
      } else {
        $governor = DB::table('governors')->orderBy('governor_id', 'desc')->first();
        $RequestForExtension = RequestForExtension::create([
          "request_date_generated" => $date_generated,
          "request_date" => $request_date,
          "governor_id" => $governor->governor_id,
          "request_reason" => $request->reason,
          "request_remarks" => $request->remarks,
          "opening_dates" => $request->opening_dates,
          "with_attachment" => false
        ]);
        if ($RequestForExtension) {
          foreach ($project_bidders as $bid) {
            RequestForExtensionBids::create([
              "request_id" => $RequestForExtension->request_id,
              "project_bid" => $bid,
            ]);
          }
        }
      }
    } else {

      $duplicate = RequestForExtension::where([
        ["request_date_generated", $date_generated],
        ["request_date", $request_date], ['request_id', '<>', $request_id]
      ])->count();
      if (false) {
        $message = "duplicate";
      } else {
        $RequestForExtension = RequestForExtension::find($request_id);
        $RequestForExtension->request_date_generated = $date_generated;
        $RequestForExtension->request_date = $request_date;
        $RequestForExtension->request_reason = $request->reason;
        $RequestForExtension->request_remarks = $request->remarks;
        $RequestForExtension->opening_dates = $request->opening_dates;
        $RequestForExtension->save();
        $existing = RequestForExtensionBids::where("request_id", $request_id)->get()->toArray();
        $delete_request_bids = RequestForExtensionBids::where("request_id", $request_id)->whereNotIn('project_bid', $project_bidders)->delete();
        foreach ($project_bidders as $bid) {
          RequestForExtensionBids::updateOrCreate([
            "request_id" => $RequestForExtension->request_id,
            "project_bid" => $bid,
          ]);
        }
      }
    }

    if ($message === "success") {
      return back()->with("message", $message);
    } else {
      return back()->withInput()->with("message", $message);
    }
  }

  function deleteRequestForExtension(Request $request)
  {
    $message = "success";
    $request_id = $request->request_id;
    $RequestForExtension = RequestForExtension::find($request_id);
    $delete_request_bids = RequestForExtensionBids::where("request_id", $request_id)->delete();
    $delete = RequestForExtension::where('request_id', $request_id)->delete();

    return $message;
  }

  function generateRequestForExtension(Request $request)
  {
    $APP = new APP;
    $RequestForExtension = RequestForExtension::find($request->id);

    $RequestForExtensionBids = RequestForExtensionBids::where("request_id", $RequestForExtension->request_id)
      ->join('project_bidders', 'request_for_extension_bids.project_bid', 'project_bidders.project_bid')
      ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->orderBy('procacts.open_bid')
      ->orderBy('procacts.itb_arrangement')
      ->get();


    $RequestForExtensionSvps = RequestForExtensionBids::where("request_id", $RequestForExtension->request_id)
      ->where('procact_mode_id', 2)
      ->join('project_bidders', 'request_for_extension_bids.project_bid', 'project_bidders.project_bid')
      ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->orderBy('procacts.open_bid')
      ->orderBy('procacts.itb_arrangement')
      ->get();

    $RequestForExtensionNPs = RequestForExtensionBids::where("request_id", $RequestForExtension->request_id)
      ->where('procact_mode_id', 3)
      ->join('project_bidders', 'request_for_extension_bids.project_bid', 'project_bidders.project_bid')
      ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->orderBy('procacts.open_bid')
      ->orderBy('procacts.itb_arrangement')
      ->get();

    $bidding = processRequestForExtension($RequestForExtensionBids);
    $svp = processRequestForExtension($RequestForExtensionSvps);
    $np = processRequestForExtension($RequestForExtensionNPs);

    $request_bids = count($bidding) + count($svp) + count($np);
    if ($request_bids === 1) {
      if (count($bidding) === 1) {
        $processed_bids = $bidding;
      }
      if (count($svp) === 1) {
        $processed_bids = $svp;
      }
      if (count($np) === 1) {
        $processed_bids = $np;
      }
      $templateProcessor = new TemplateProcessor(public_path() . '\\' . "word_templates/request_for_extension_one.docx");
      $templateProcessor->setValue('bidder', $processed_bids[0]->business_name);
      $templateProcessor->setValue('rank', $processed_bids[0]->rank);
      $templateProcessor->setValue('date_opened', $processed_bids[0]->date_formatted);
      $templateProcessor->setValue('project_title', $processed_bids[0]->title);
      if ($processed_bids[0]->procact_mode_id === 1) {
        $templateProcessor->setValue('bid_or_quotation', "bid");
      } else {
        $templateProcessor->setValue('bid_or_quotation', "quotation");
      }
    } else if ($request_bids > 1) {
      $dates = [];
      $modes = [];
      if (count($bidding) > 1) {
        $p_dates = array_unique(array_column($bidding, 'date_formatted'));
        foreach ($p_dates as $data) {
          if (in_array($data, $dates) === false) {
            array_push($dates, $data);
          }
        }
        array_push($modes, 1);
      }

      if (count($svp) > 1) {
        $p_dates = array_unique(array_column($svp, 'date_formatted'));
        foreach ($p_dates as $data) {
          if (in_array($data, $dates) === false) {
            array_push($dates, $data);
          }
        }
        array_push($modes, 2);
      }

      if (count($np) > 1) {
        $p_dates = array_unique(array_column($svp, 'date_formatted'));
        foreach ($p_dates as $data) {
          if (in_array($data, $dates) === false) {
            array_push($dates, $data);
          }
        }
        array_push($modes, 3);
      }

      if (count($dates) === 1) {
        $templateProcessor = new TemplateProcessor(public_path() . '\\' . "word_templates/request_for_extension_multiple_1.docx");

        if (count($bidding) > 1) {
          $templateProcessor->cloneBlock('public_bidding', 1, true, true);
          $templateProcessor->setValue('date_opened', $bidding[0]->date_formatted);
          $templateProcessor->cloneRow('pb_no#1', count($bidding));
          $index = 1;
          foreach ($bidding as $processed_bid) {
            $templateProcessor->setValue('pb_no#1#' . $index, $index);
            $templateProcessor->setValue('pb_title#1#' . $index, $processed_bid->title);
            $templateProcessor->setValue('pb_location#1#' . $index, $processed_bid->location);
            $templateProcessor->setValue('pb_abc#1#' . $index, $processed_bid->project_cost);
            $index++;
          }
        } else {
          $templateProcessor->cloneBlock('public_bidding', 0, true, true);
        }

        if (count($svp) > 1) {
          $templateProcessor->cloneBlock('svp', 1, true, true);
          $templateProcessor->setValue('date_opened', $svp[0]->date_formatted);
          $templateProcessor->cloneRow('svp_no#1', count($svp));
          $index = 1;
          foreach ($svp as $processed_bid) {
            $templateProcessor->setValue('svp_no#1#' . $index, $index);
            $templateProcessor->setValue('svp_title#1#' . $index, $processed_bid->title);
            $templateProcessor->setValue('svp_location#1#' . $index, $processed_bid->location);
            $templateProcessor->setValue('svp_abc#1#' . $index, $processed_bid->project_cost);
            $index++;
          }
        } else {
          $templateProcessor->cloneBlock('svp', 0, true, true);
        }

        if (count($np) > 1) {
          $templateProcessor->cloneBlock('np', 1, true, true);
          $templateProcessor->setValue('date_opened', $np[0]->date_formatted);
          $templateProcessor->cloneRow('np_no#!', count($np));
          $index = 1;
          foreach ($np as $processed_bid) {
            $templateProcessor->setValue('np_no#1#' . $index, $index);
            $templateProcessor->setValue('np_title#1#' . $index, $processed_bid->title);
            $templateProcessor->setValue('np_location#1#' . $index, $processed_bid->location);
            $templateProcessor->setValue('np_abc#1#' . $index, $processed_bid->project_cost);
            $index++;
          }
        } else {
          $templateProcessor->cloneBlock('np', 0, true, true);
        }
      } else {
        $RequestForExtensionAll = RequestForExtensionBids::where("request_id", $RequestForExtension->request_id)->get();
        $processed_bids = processRequestForExtension($RequestForExtensionAll);
        $dates_formatted = "";
        $templateProcessor = new TemplateProcessor(public_path() . '\\' . "word_templates/request_for_extension_multiple_2.docx");
        $templateProcessor->cloneBlock('table_block', count($dates), true, true);
        foreach ($dates as $date_index => $date) {
          if ($date_index === 0) {
            $dates_formatted = $dates_formatted . $date;
          } else if ($date_index === count($dates) - 1) {
            $dates_formatted = $dates_formatted . " and " . $date;
          } else {
            $dates_formatted = $dates_formatted . "," . $date;
          }
          $templateProcessor->setValue('opening#' . ($date_index + 1), $date);
          $projects = array_filter($processed_bids, function ($value) use ($date) {
            return $value->date_formatted == $date;
          });
          $templateProcessor->cloneRow('no#' . ($date_index + 1), count($projects));

          $project_index = 1;
          foreach ($projects as $project) {
            $templateProcessor->setValue('no#' . ($date_index + 1) . "#" . $project_index, $project_index);
            $templateProcessor->setValue('title#' . ($date_index + 1) . "#" . $project_index, $project->title);
            $templateProcessor->setValue('location#' . ($date_index + 1) . "#" . $project_index, $project->location);
            $templateProcessor->setValue('abc#' . ($date_index + 1) . "#" . $project_index, $project->project_cost);
            $project_index++;
          }
        }
        $templateProcessor->setValue('date_opened', $dates_formatted);
      }

      if (count($modes) === 1) {
        if ($modes[0] === 1) {
          $templateProcessor->setValue('bid_and_qoutation', "bids");
        } else {
          $templateProcessor->setValue('bid_and_qoutation', "quotations");
        }
      } else {
        $templateProcessor->setValue('bid_and_qoutation', "bids and quotations");
      }
    } else {
      return abort(403, "Missing Requested Projects!");
    }
    $bac = DB::table('bids_and_awards_committee')
      ->select(
        'bids_and_awards_committee.*',
        DB::raw("UPPER(CONCAT(if(bac_ch.member_prefix is null ,'',bac_ch.member_prefix),' ',bac_ch.member_fname,' ',if(bac_ch.member_minitial is null ,'',bac_ch.member_minitial),' ',bac_ch.member_lname)) AS bac_chairman_name"),
        DB::raw("CONCAT(bac_vice_ch.member_fname,' ',if(bac_vice_ch.member_minitial is null ,'',bac_vice_ch.member_minitial),' ',bac_vice_ch.member_lname) AS bac_vice_chairman_name"),
        DB::raw("CONCAT(bac_alternate_vice_ch.member_fname,' ',if(bac_alternate_vice_ch.member_minitial is null ,'',bac_alternate_vice_ch.member_minitial),' ',bac_alternate_vice_ch.member_lname) AS bac_alternate_vice_chairman_name"),
        DB::raw("CONCAT(bac_sec_ch.member_fname,' ',if(bac_sec_ch.member_minitial is null ,'',bac_sec_ch.member_minitial),' ',bac_sec_ch.member_lname) AS bac_sec_chairman_name"),
        DB::raw("CONCAT(bac_sec_vice_ch.member_fname,' ',if(bac_sec_vice_ch.member_minitial is null ,'',bac_sec_vice_ch.member_minitial),' ',bac_sec_vice_ch.member_lname) AS bac_sec_vice_chairman_name"),
        DB::raw("UPPER(CONCAT(if(bac_twg_ch.member_prefix is null ,'',bac_twg_ch.member_prefix),' ',bac_twg_ch.member_fname,' ',if(bac_twg_ch.member_minitial is null ,'',bac_twg_ch.member_minitial),' ',bac_twg_ch.member_lname)) AS bac_twg_chairman_name"),
        DB::raw("CONCAT(bac_twg_vice_ch.member_fname,' ',if(bac_twg_vice_ch.member_minitial is null ,'',bac_twg_vice_ch.member_minitial),' ',bac_twg_vice_ch.member_lname) AS bac_twg_vice_chairman_name")
      )
      ->join('member as bac_ch', 'bac_ch.member_id', '=', 'bids_and_awards_committee.bac_chairman')
      ->join('member as bac_vice_ch', 'bac_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_vice_chairman')
      ->join('member as bac_alternate_vice_ch', 'bac_alternate_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_alternate_vice_chairman')
      ->join('member as bac_sec_ch', 'bac_sec_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_chairman')
      ->join('member as bac_sec_vice_ch', 'bac_sec_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_sec_vice_chairman')
      ->join('member as bac_twg_ch', 'bac_twg_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_chairman')
      ->join('member as bac_twg_vice_ch', 'bac_twg_vice_ch.member_id', '=', 'bids_and_awards_committee.bac_twg_vice_chairman')
      ->orderBy("bac_id", "desc")
      ->first();

    $templateProcessor->setValue('request_date_generated', date("F d, Y", strtotime($RequestForExtension->request_date_generated)));
    $templateProcessor->setValue('reason', $RequestForExtension->request_reason);
    $templateProcessor->setValue('request_date', date("F d, Y", strtotime($RequestForExtension->request_date)));
    $templateProcessor->setValue('bac_chairman', $bac->bac_chairman_name);
    $templateProcessor->setValue('bac_twg_chairman', $bac->bac_twg_chairman_name);

    $path = public_path() . '\\' . 'word_results/Request For Extension.docx';
    $templateProcessor->saveAs($path);
    return  response()->download($path)->deleteFileAfterSend(true);
  }

  function getAllPostQual(Request $request)
  {
    $APP = new App;
    $mode_of_procurement = $request->input('mode_of_procurement');
    $length = $request->input('length');
    $search = $request->input('search')['value'];
    $order = $request->input('order');
    $start = $request->input('start');
    $draw = $request->input('draw');
    $columns = $request->input('columns');
    $array = [];
    $procact_array = [];

    if ($mode_of_procurement === "1") {
      $project_plans = DB::table('bid_doc_projects')
        ->select(
          'procurement_modes.*',
          'procacts.*',
          'project_plans.*',
          DB::raw("CAST(LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS DECIMAL(20,2)) AS minimum_cost"),
          DB::raw("CAST(LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS DECIMAL(20,2)) AS final_minimum_cost"),
          "bid_docs.*",
          "procacts.*",
          "contractors.*",
          "project_bidders.*",
          "twg_evaluations.twg_evaluation_status",
          "twg_evaluations.twg_evaluation_remarks",
          "twg_evaluations.twg_final_bid_evaluation",
          "twg_evaluations.post_qual_start",
          "twg_evaluations.post_qual_end"
        )
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('procurement_modes', 'procacts.procact_mode_id', '=', 'procurement_modes.mode_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin("request_for_extension_bids", "request_for_extension_bids.project_bid", "project_bidders.project_bid");
    } else {
      $project_plans = DB::table('rfq_projects')
        ->select(
          'procurement_modes.*',
          'procacts.*',
          'project_plans.*',
          DB::raw("CAST(LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS DECIMAL(20,2)) AS minimum_cost"),
          DB::raw("CAST(LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS DECIMAL(20,2)) AS final_minimum_cost"),
          "rfqs.*",
          "procacts.*",
          "contractors.*",
          "project_bidders.*",
          "twg_evaluations.twg_evaluation_status",
          "twg_evaluations.twg_evaluation_remarks",
          "twg_evaluations.twg_final_bid_evaluation",
          "twg_evaluations.post_qual_start",
          "twg_evaluations.post_qual_end"
        )
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
        ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('procurement_modes', 'procacts.procact_mode_id', '=', 'procurement_modes.mode_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->leftJoin("request_for_extension_bids", "request_for_extension_bids.project_bid", "project_bidders.project_bid");
    }

    if ($search != null) {
      $project_plans = $project_plans
        ->where([['procact_mode_id', $mode_of_procurement], ['project_plans.status', 'onprocess'], ['twg_evaluations.twg_evaluation_id', null], ['bid_evaluation', '<>', null], ['project_bidders.bid_status', 'active'], ['project_title', 'like', '%' . $search . '%']])
        ->orWhere([['procact_mode_id', $mode_of_procurement], ['project_plans.status', 'onprocess'], ['twg_evaluations.twg_evaluation_id', null], ['bid_evaluation', '<>', null], ['project_bidders.bid_status', 'active'], ['procacts.open_bid', 'like', '%' . $search . '%']])
        ->orWhere([['procact_mode_id', $mode_of_procurement], ['project_plans.status', 'onprocess'], ['twg_evaluations.twg_evaluation_id', null], ['bid_evaluation', '<>', null], ['project_bidders.bid_status', 'active'], ['contractors.business_name', 'like', '%' . $search . '%']])
        ->orWhere([['procact_mode_id', $mode_of_procurement], ['project_plans.status', 'onprocess'], ['twg_evaluations.twg_evaluation_id', null], ['bid_evaluation', '<>', null], ['project_bidders.bid_status', 'active'], ['project_plans.project_no', 'like', '%' . $search . '%']]);
    } else {
      $project_plans = $project_plans
        ->where([['procact_mode_id', $mode_of_procurement], ['project_plans.status', 'onprocess'], ['twg_evaluations.twg_evaluation_id', null], ['bid_evaluation', '<>', null], ['project_bidders.bid_status', 'active']]);
    }

    if (count($order) != null) {

      $column = $order[0]['column'];
      $dir = $order[0]['dir'];
      $column_name = $columns[intval($column)]['data'];

      if ($column_name != "consumed_days") {
        if ($column_name == "plan_id") {
          $column_name = "project_plans." . $column_name;
        }
        if ($column_name == "project_bid") {
          $column_name = "project_bidders." . $column_name;
        }
        if ($column_name == "open_bid") {
          $column_name = "procacts." . $column_name;
        }

        $project_plans = $project_plans
          ->orderBy('procacts.open_bid', 'asc')
          ->orderBy('procacts.itb_arrangement', 'asc')
          ->orderBy('minimum_cost', 'asc')
          ->orderBy($column_name, $dir);
      } else {
        $project_plans = $project_plans
          ->orderBy('procacts.open_bid', 'asc')
          ->orderBy('procacts.itb_arrangement', 'asc')
          ->orderBy('minimum_cost', 'asc');
      }
    } else {
      $project_plans = $project_plans
        ->orderBy('procacts.open_bid', 'asc')
        ->orderBy('procacts.itb_arrangement', 'asc')
        ->orderBy('minimum_cost', 'asc');
    }

    $count = count($project_plans->get());
    $project_plans = $project_plans->skip($start)->limit($length)->get();


    foreach ($project_plans as $plan) {
      if (in_array($plan->procact_id, $procact_array) == false) {
        array_push($procact_array, $plan->procact_id);
        $bidders = $APP->getBiddersData($plan->procact_id, 'non-responsive');
        $non_responsive_count = count($bidders);
        if ($non_responsive_count > 0) {
          $last_bidder = $bidders[$non_responsive_count - 1];
          $consumed_days = round((strtotime(date('Y-m-d')) - strtotime($last_bidder->post_qual_end)) / (60 * 60 * 24));
        } else {
          $consumed_days = round((strtotime(date('Y-m-d')) - strtotime($plan->open_bid)) / (60 * 60 * 24));
        }
        $plan->consumed_days = $consumed_days;
        $array[] = $plan;
      }
    }

    $data = [
      "draw" => $draw,
      "recordsTotal" => $count,
      "recordsFiltered" => $count,
      "data" => $array
    ];

    // ddd($project_plans);
    return $data;
  }
}
