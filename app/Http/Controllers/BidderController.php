<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\ProcurementController;
use App\{APP, LCEEvaluation};
use App\Contractors;
use App\ProjectBidder;
use Validator;

class BidderController extends Controller
{

  ////////////////// Contractors //////////////////

  public function getContractors()
  {
    $contractors = Contractors::orderBy('contractor_id', 'desc')->limit(1000)->get();
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view("admin.contractors", ["contractors" => $contractors, "links" => $links, 'user_privilege' => $user_privilege]);
  }

  public function filterContractors(Request $request)
  {

    $business_name = (isset($request->filter_business_name)) ? $request->filter_business_name : '';
    $owner = (isset($request->filter_owner)) ? $request->filter_owner : '';
    $contractors = Contractors::orderBy('contractor_id', 'desc')
      ->where([['business_name', 'like', '%' . $business_name . '%'], ['owner', 'like', '%' . $owner . '%'],])
      ->limit(1000)
      ->get();
    return back()->withInput()->with('contractors', $contractors);
  }

  public function submitContractor(Request $request)
  {

    $data = $request->validate([
      "business_name" => 'required',
      "owner" => 'required',
      "address" => 'required',
      "contact_number" => 'required',
      "status" => 'required',
      "position" => 'required',
      "email" => 'nullable|email',
    ]);

    if ($request->input("contractor_id") == null) {
      $duplicate = Contractors::where("business_name", $request->input("business_name"))->count();
    } else {
      $duplicate = Contractors::where([["business_name", $request->input("business_name")], ["contractor_id", '<>', $request->input("contractor_id")]])->count();
    }

    if ($duplicate > 0) {
      $message = "duplicate";
    } else {
      if ($request->input("contractor_id") == null) {
        Contractors::create([
          "business_name" => $request->input("business_name"),
          "owner" => $request->input("owner"),
          "position" => $request->input("position"),
          "address" => $request->input("address"),
          "contact_number" => $request->input("contact_number"),
          "email" => $request->input("email"),
          "status" => $request->input("status"),
        ]);
      } else {
        $contractor = Contractors::find($request->input("contractor_id"));
        $contractor->business_name = $request->input("business_name");
        $contractor->owner = $request->input("owner");
        $contractor->address = $request->input("address");
        $contractor->position = $request->input("position");
        $contractor->contact_number = $request->input("contact_number");
        $contractor->email = $request->input("email");
        $contractor->status = $request->input("status");
        $contractor->save();
      }
      $message = "success";
    }
    return redirect()->back()->with('message', $message);
  }

  public function editProposedBid(Request $request)
  {

    $data = $request->validate([
      "bid_in_words" => 'required',
      "proposed_bid" => 'required',
      "initial_bid_as_evaluated" => 'required',
      "bid_as_evaluated" => 'required',
      "discount" => 'required|lt:100'
    ]);

    $APP = new APP;
    $ProcurementController = new ProcurementController;
    $proposed_bid = str_replace(",", "", $request->input('proposed_bid'));
    $initial_bid_as_evaluated = str_replace(",", "", $request->input('initial_bid_as_evaluated'));
    $bid_as_evaluated = str_replace(",", "", $request->input('bid_as_evaluated'));
    $bid_in_words = str_replace(",", "", $request->input('bid_in_words'));
    $amount_of_discount = str_replace(",", "", $request->input('amount_of_discount'));
    $discount = $request->input('discount');
    $discount_source = $request->input('discount_source');
    $discount_type = $request->input('discount_type');
    if ($discount_type == "") {
      $discount_type = null;
    }

    $project_plan = DB::table('project_bidders')
      ->select('rfq_projects.rfq_id', 'procacts.open_bid')
      ->where('project_bidders.project_bid', $request->input('proposed_bid_bidder_id'))
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();

    if ($project_plan == null) {
      $project_plan = DB::table('project_bidders')
        ->select('bid_doc_projects.bid_doc_id', 'procacts.open_bid')
        ->where('project_bidders.project_bid', $request->input('proposed_bid_bidder_id'))
        ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();
      $procact_ids = DB::table('bid_doc_projects')->select(DB::raw('GROUP_CONCAT(procact_id) AS procacts'))->where('bid_doc_id', $project_plan->bid_doc_id)->distinct()->get();

      DB::table('bid_docs')->where('bid_doc_id', $project_plan->bid_doc_id)->update([
        "bid_in_words" => $bid_in_words,
        "proposed_bid" => $proposed_bid,
        "initial_bid_as_evaluated" => $initial_bid_as_evaluated,
        "bid_as_evaluated" => $bid_as_evaluated,
        "discount" => $discount,
        "discount_source" => $discount_source,
        "amount_of_discount" => $amount_of_discount,
        "discount_type" => $discount_type,
        "updated_at" => now()
      ]);
    } else {
      $procact_ids = DB::table('rfq_projects')->select(DB::raw('GROUP_CONCAT(procact_id) AS procacts'))->where('rfq_id', $project_plan->rfq_id)->distinct()->get();
      DB::table('rfqs')->where('rfq_id', $project_plan->rfq_id)->update([
        "bid_in_words" => $bid_in_words,
        "proposed_bid" => $proposed_bid,
        "initial_bid_as_evaluated" => $initial_bid_as_evaluated,
        "bid_as_evaluated" => $bid_as_evaluated,
        "discount" => $discount,
        "discount_source" => $discount_source,
        "amount_of_discount" => $amount_of_discount,
        "discount_type" => $discount_type,
        "updated_at" => now()
      ]);
    }

    $procacts_array = explode(',', $procact_ids[0]->procacts);
    $update_activity_status = $APP->evaluateBidEvaluationStatus($procacts_array);

    if ($update_activity_status) {
      $plan_ids = "";
      $clusters = $APP->getClusterBids($request->input('proposed_bid_bidder_id'));

      foreach ($clusters as $key => $cluster) {
        if ($key == count($clusters) - 1) {
          $plan_ids = $plan_ids . $cluster->plan_id;
        } else {
          $plan_ids = $plan_ids . $cluster->plan_id . ",";
        }
      }
      $parameters = ["plan_id" => $plan_ids, "bid_evaluation_date" => date("m/d/Y", strtotime($project_plan->open_bid)), "bypass" => true];
      $request = new \Illuminate\Http\Request();
      $request->replace($parameters);
      $ProcurementController->submitBidEvaluation($request);
    }

    $message = 'edit_success';
    return redirect()->back()->with('message', $message);
  }

  public function editDetailedBid(Request $request)
  {
    $data = $request->validate([
      "bid_in_words" => 'required',
      "proposed_bid" => 'required',
      "initial_bid_as_evaluated" => 'required',
      "bid_as_evaluated" => 'required',
      "discount" => 'required|lt:100'
    ]);

    $APP = new APP;
    $ProcurementController = new ProcurementController;
    $proposed_bid = str_replace(",", "", $request->input('proposed_bid'));
    $initial_bid_as_evaluated = str_replace(",", "", $request->input('initial_bid_as_evaluated'));
    $bid_as_evaluated = str_replace(",", "", $request->input('bid_as_evaluated'));
    $bid_in_words = str_replace(",", "", $request->input('bid_in_words'));
    $amount_of_discount = str_replace(",", "", $request->input('amount_of_discount'));
    $discount = $request->input('discount');
    $discount_source = $request->input('discount_source');
    $discount_type = $request->input('discount_type');
    if ($discount_type == "") {
      $discount_type = null;
    }

    DB::table('rfq_projects')
      ->join('project_bidders', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->where('project_bidders.project_bid', $request->input('proposed_bid_bidder_id'))->update([
        "rfq_projects.detailed_bid_in_words" => $bid_in_words,
        "rfq_projects.detailed_bid_as_read" => $proposed_bid,
        "rfq_projects.detailed_initial_bid_as_evaluated" => $initial_bid_as_evaluated,
        "rfq_projects.detailed_bid_as_evaluated" => $bid_as_evaluated,
        "rfq_projects.detailed_discount" => $discount,
        "rfq_projects.detailed_discount_source" => $discount_source,
        "rfq_projects.detailed_amount_of_discount" => $amount_of_discount,
        "rfq_projects.detailed_discount_type" => $discount_type,
        "rfq_projects.updated_at" => now()
      ]);

    DB::table('bid_doc_projects')
      ->join('project_bidders', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
      ->where('project_bidders.project_bid', $request->input('proposed_bid_bidder_id'))->update([
        "bid_doc_projects.detailed_bid_in_words" => $bid_in_words,
        "bid_doc_projects.detailed_bid_as_read" => $proposed_bid,
        "bid_doc_projects.detailed_initial_bid_as_evaluated" => $initial_bid_as_evaluated,
        "bid_doc_projects.detailed_bid_as_evaluated" => $bid_as_evaluated,
        "bid_doc_projects.detailed_discount" => $discount,
        "bid_doc_projects.detailed_discount_source" => $discount_source,
        "bid_doc_projects.detailed_amount_of_discount" => $amount_of_discount,
        "bid_doc_projects.detailed_discount_type" => $discount_type,
        "bid_doc_projects.updated_at" => now()
      ]);

    $clustered_bids = $APP->getClusterBids($request->input('proposed_bid_bidder_id'));
    $project_bids = [];
    $bid_in_words = 0;
    $proposed_bid = 0;
    $initial_bid_as_evaluated = 0;
    $bid_as_evaluated = 0;
    $discount = 0;
    $is_same_discount = true;
    $initial_discount = null;
    $initial_discount_type = null;
    $initial_discount_source = null;
    $initial_amount_of_discount = null;
    $discount = null;
    $discount_source = null;
    $amount_of_discount = null;
    foreach ($clustered_bids as $key => $value) {
      $proposed_bid = $proposed_bid + $value->detailed_bid_as_read;
      $bid_in_words = $bid_in_words + $value->detailed_bid_in_words;
      $initial_bid_as_evaluated = $initial_bid_as_evaluated + $value->detailed_initial_bid_as_evaluated;
      $bid_as_evaluated = $bid_as_evaluated + $value->detailed_bid_as_evaluated;
      if ($initial_discount_type != null && $initial_discount != null && $initial_discount_source != null && $initial_amount_of_discount != null) {
        if ($initial_discount_type != $value->detailed_discount_type || $initial_discount != $value->detailed_discount && $initial_discount_source != $value->detailed_discount_source && $initial_amount_of_discount != $value->detailed_amount_of_discount) {
          $is_same_discount = false;
        }
      }
      $initial_discount_type = $value->detailed_discount_type;
      $initial_discount = $value->detailed_discount;
      $initial_discount_source = $value->detailed_discount_source;
      $amount_of_discount = $amount_of_discount + $value->detailed_amount_of_discount;
      array_push($project_bids, $value->project_bid);
    }

    if ($is_same_discount == false) {
      $initial_discount_type = 'amount';
      $initial_discount = 0.00;
      $initial_discount_source = null;
    }

    DB::table('rfqs')
      ->join('rfq_projects', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('project_bidders', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->whereIn('project_bidders.project_bid', $project_bids)
      ->update([
        "rfqs.bid_in_words" => $bid_in_words,
        "rfqs.proposed_bid" => $proposed_bid,
        "rfqs.initial_bid_as_evaluated" => $initial_bid_as_evaluated,
        "rfqs.bid_as_evaluated" => $bid_as_evaluated,
        "rfqs.discount" => $initial_discount,
        "rfqs.discount_source" => $initial_discount_source,
        "rfqs.amount_of_discount" => $amount_of_discount,
        "rfqs.discount_type" => $initial_discount_type,
        "rfqs.updated_at" => now()
      ]);


    DB::table('bid_docs')
      ->join('bid_doc_projects', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->join('project_bidders', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
      ->whereIn('project_bidders.project_bid', $project_bids)
      ->update([
        "bid_docs.bid_in_words" => $bid_in_words,
        "bid_docs.proposed_bid" => $proposed_bid,
        "bid_docs.initial_bid_as_evaluated" => $initial_bid_as_evaluated,
        "bid_docs.bid_as_evaluated" => $bid_as_evaluated,
        "bid_docs.discount" => $initial_discount,
        "bid_docs.discount_source" => $initial_discount_source,
        "bid_docs.amount_of_discount" => $amount_of_discount,
        "bid_docs.discount_type" => $initial_discount_type,
        "bid_docs.updated_at" => now()
      ]);

    $message = 'edit_success';
    return redirect()->back()->with('message', $message);
  }

  public function deleteContractor($id)
  {
    $rfqs = DB::table('rfqs')
      ->where('rfqs.contractor_id', $id)
      ->count();

    $bid_docs = DB::table('bid_docs')
      ->where('bid_docs.contractor_id', $id)
      ->count();


    $data = $rfqs + $bid_docs;
    if ($data > 0) {
      $message = "delete_error";
    } else {
      Contractors::where("contractor_id", $id)->delete();
      $message = "delete_success";
    }
    return redirect()->back()->with('message', $message);
  }


  public function autoCompleteContractors(Request $request)
  {
    $term = $request->term;
    $results = array();
    $contractors = Contractors::select('contractor_id', 'business_name')->where('status', '=', 'active')->where('business_name', 'LIKE', '%' . $term . '%')->take(10)->get();

    if (sizeOf($contractors) != 0) {
      foreach ($contractors as $contractor) {
        $results[] = [
          'id' => $contractor->contractor_id,
          'value' => $contractor->business_name
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

  public function autoCompleteBidders(Request $request)
  {
    $APP = new APP;
    $term = $request->term;
    $plan_id = $request->plan_id;
    $date_opening = null;
    if ($request->opening_date != null && $plan_id != null) {
      $opening_date = date("Y-m-d", strtotime($request->opening_date));
      if ($request->status != null) {
        $status = $request->status;
      } else {
        $status = "disqualified,non-responsive,active,responsive";
      }
      $procact = DB::table('procacts')->where([['open_bid', $opening_date], ["plan_id", $plan_id]])->first();
      $contractors = $APP->getBiddersData($procact->procact_id, $status);
      if (sizeOf($contractors) != 0) {
        foreach ($contractors as $contractor) {
          $results[] = [
            'id' => $contractor->project_bid,
            'contractor_id' => $contractor->contractor_id,
            'value' => $contractor->business_name
          ];
        }
      } else {
        $results[] = [
          'id' => '',
          'value' => 'No Match Found'
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

  public function autoCompleteSimilarBidders(Request $request)
  {
    $APP = new APP;
    $term = $request->term;
    $plan_id = $request->plan_id;
    $plan_ids = explode(",", $plan_id);
    $date_opening = null;
    if ($request->opening_date != null && $plan_id != null) {
      $opening_date = date("Y-m-d", strtotime($request->opening_date));
      $procacts = DB::table('procacts')->select(DB::raw('GROUP_CONCAT(procact_id) as ids'))->where([['open_bid', $opening_date]])->whereIn('plan_id', $plan_ids)->get();
      $contractors = $APP->getSimilarBidder($procacts[0]->ids, "ineligible,disqualified,non-responsive,responsive,disapproved,withdrawn");
      if (sizeOf($contractors) != 0) {

        foreach ($contractors as $contractor) {
          $due_date = Date("m/d/Y", strtotime($contractor->mr_due_date));
          $type = $contractor->notice_type;
          if ($contractor->bid_status === "disapproved") {
            $lce = LCEEvaluation::where('project_bid', $contractor->project_bid)->first();
            $due_date = Date("m/d/Y", strtotime(calculateDate($lce->lce_evaluation_date, 3, "Working Days")));
            $type = "NODA";
          }
          $results[] = [
            'id' => $contractor->contractor_id,
            'value' => $contractor->business_name,
            'notice_type' =>  $type,
            'mr_due_date' => $due_date
          ];
        }
      } else {
        $results[] = [
          'id' => '',
          'value' => 'No Match Found',
          'notice_type' => '',
          'mr_due_date' => '',
          'notice_type' => '',
          'mr_due_date' => ''
        ];
      }
    } else {
      $results[] = [
        'id' => '',
        'value' => 'No Match Found',
        'notice_type' => '',
        'mr_due_date' => '',
      ];
    }
    return response()->json($results);
  }


  public function autoCompleteUnreceiveContractors(Request $request)
  {
    $APP = new APP;
    $term = $request->term;
    $plan_id = $request->plan_id;
    if ($request->opening_date != null) {
      $opening_date = date('Y-m-d', strtotime($request->opening_date));
    } else {
      $opening_date = null;
    }
    $procact = DB::table('procacts')->where([["open_bid", $opening_date], ["plan_id", $plan_id]])->first();

    $contractors = $APP->getBiddersData($procact->procact_id, 'active,responsive,non-responsive');
    // $contractors = Contractors::select('contractor_id', 'business_name')->where('status', '=', 'active')->where('business_name', 'LIKE', '%'.$term.'%')->take(10)->get();
    if (sizeOf($contractors) != 0) {
      foreach ($contractors as $contractor) {
        $results[] = [
          'id' => $contractor->contractor_id,
          'value' => $contractor->business_name
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

  public function getProjectBidders($id)
  {
    $APP = new APP;
    $data = (object)$APP->getAllCurrentBidders($id);
    $links = getUserLinks();
    $user_privilege = getUserPrivilegeByLink('twg_projects_with_bidders');
    $access = checkUserAccess('view', $user_privilege);
    return view("twg.project_bidders", ["data" => $data, "links" => $links, 'user_privilege' => $user_privilege]);
  }

  public function getPostQualProjectBidders($id)
  {
    $APP = new APP;
    $data = (object)$APP->getActiveBiddersData($id);
    $links = getUserLinks();
    $user_privilege = getUserPrivilegeByLink('post_qual_bidders');
    $access = checkUserAccess('update', $user_privilege);
    return view("admin.post_qual_project_bidders", ["data" => $data, "links" => $links, 'user_privilege' => $user_privilege]);
  }

  public function getTWGBidders($id)
  {
    $APP = new APP;
    $data = (object)$APP->getActiveBiddersData($id);
    $links = getUserLinks();
    $user_privilege = getUserPrivilegeByLink('twg_post_qualification');
    $access = checkUserAccess('view', $user_privilege);
    return view("twg.post_qual_project_bidders", ["data" => $data, "links" => $links, 'user_privilege' => $user_privilege]);
  }

  public function getLatestPQER(Request $request)
  {
    $pqer = DB::table('pqer')->where('contractor_id', $request->contractor_id)->orderBy('pqer_id', 'desc')->first();
    return compact('pqer');
  }



  public function disqualifyBidder(Request $request)
  {
    $data = $request->validate([
      "remarks" => 'required',
    ]);

    if ($request->ineligibility != null && $request->ineligibility == "Disqualify") {
      $term = "Disqualified";
      $bidder_status = "disqualified";
    } else {
      $term = "Ineligible";
      $bidder_status = "ineligible";
    }

    $APP = new APP;

    $clusters = $APP->getClusterBids($request->input('bidder_id'));
    foreach ($clusters as $project_bidder) {

      $update = DB::table('project_bidders')->where('project_bidders.project_bid', $project_bidder->project_bid)
        ->update([
          "bid_status" => $bidder_status
        ]);

      DB::table('disqualification_records')->insert([
        'project_bid'  => $project_bidder->project_bid,
        'remarks'  => $term . ': ' . $request->input('remarks'),
        'user_id'  => Auth::user()->id,
        'created_at'  => now(),
        'updated_at' => now()
      ]);

      $bidders = $APP->getBiddersData($project_bidder->procact_id, 'responsive,active');
      if (count($bidders) === 0) {
        DB::table('procacts')->where('procact_id', $project_bidder->procact_id)->update([
          "is_inactive" => true
        ]);
      }
    }

    return redirect()->back()->with('message', 'success');
  }

  public function reactivateBidder(Request $request)
  {
    $data = $request->validate([
      "remarks" => 'required',
    ]);

    $APP = new APP;

    $project_plan = DB::table('project_bidders')
      ->select('rfq_projects.rfq_id', 'project_plans.latest_procact_id')
      ->where('project_bidders.project_bid', $request->input('bidder_id'))
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();


    if ($project_plan == null) {

      $project_plan = DB::table('project_bidders')
        ->select('bid_doc_projects.bid_doc_id', 'project_plans.latest_procact_id')
        ->where('project_bidders.project_bid', $request->input('bidder_id'))
        ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();
      $project_timelines = DB::table("project_activity_status")->where("procact_id", $project_plan->latest_procact_id)->first();

      $update = DB::table('project_bidders')->where('bid_doc_projects.bid_doc_id', $project_plan->bid_doc_id)
        ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
        ->update([
          "bid_status" => "active",
        ]);

      $ids = $update = DB::table('project_bidders')->where('bid_doc_projects.bid_doc_id', $project_plan->bid_doc_id)
        ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')->get();
    } else {
      $project_timelines = DB::table("project_activity_status")->where("procact_id", $project_plan->latest_procact_id)->first();
      $update = DB::table('project_bidders')->where('rfq_projects.rfq_id', $project_plan->rfq_id)
        ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
        ->update([
          "bid_status" => "active",
        ]);

      $ids = DB::table('project_bidders')->where('rfq_projects.rfq_id', $project_plan->rfq_id)
        ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')->get();
    }

    $term = "Reactivated";

    $clusters = $APP->getClusterBids($request->input('bidder_id'));
    foreach ($clusters as $project_bidder) {

      $bidders = $APP->getBiddersData($project_bidder->procact_id, 'responsive,active');
      if (count($bidders) != 0) {
        DB::table('procacts')->where('procact_id', $project_bidder->procact_id)->update([
          "is_inactive" => false
        ]);
      }
    }

    foreach ($ids as $id) {
      DB::table('disqualification_records')->insert([
        'project_bid'  => $id->project_bid,
        'remarks'  => $term . ': ' . $request->input('remarks'),
        'user_id'  => Auth::user()->id,
        'created_at'  => now(),
        'updated_at' => now()
      ]);
    }


    return redirect()->back()->with('message', 'success');
  }

  public function setTWGNonResponsiveBiddder(Request $request)
  {
    $data = $request->validate([
      "post_qual_start_date" => 'required',
      "post_qual_end_date" => 'required|after:post_qual_start_date',
      "bid_as_calculated" => 'required',
      "detailed_bid_as_calculated" => 'required_if:process,"detailed_non_responsive"',
      "philgeps" => 'required_if:procurement_mode,"Bidding"',
      "ongoing_projects" => 'required_if:procurement_mode,"Bidding"',
      "slcc" => 'required_if:procurement_mode,"Bidding"',
      "bsd" => 'required_if:procurement_mode,"Bidding"',
      "nfcc" => 'required_if:procurement_mode,"Bidding"',
      "spcab" => 'required_if:procurement_mode,"Bidding"',
      "orgchart" => 'required_if:procurement_mode,"Bidding"',
      "key_personnel" => 'required_if:procurement_mode,"Bidding"',
      "major_equipment" => 'required_if:procurement_mode,"Bidding"',
      "oss" => 'required_if:procurement_mode,"Bidding"',
      "jva" => 'required_if:procurement_mode,"Bidding"',
      "boq" => 'required_if:procurement_mode,"Bidding"',
      "detailed_estimates" => 'required_if:procurement_mode,"Bidding"',
      "cash_flow" => 'required_if:procurement_mode,"Bidding"',
      "provincial_permit" => 'required_if:procurement_mode,"Bidding"',
      "construction_shedule" => 'required_if:procurement_mode,"Bidding"',
      "man_power" => 'required_if:procurement_mode,"Bidding"',
      "construction_methods" => 'required_if:procurement_mode,"Bidding"',
      "eus" => 'required_if:procurement_mode,"Bidding"',
      "chsp" => 'required_if:procurement_mode,"Bidding"',
      "pert_cpm" => 'required_if:procurement_mode,"Bidding"',
    ]);


    $message = "success";
    $bid_as_calculated = str_replace(",", "", $request->input('bid_as_calculated'));
    $detailed_bid_as_calculated = $request->input('detailed_bid_as_calculated');
    if ($detailed_bid_as_calculated != null) {
      $detailed_bid_as_calculated = str_replace(",", "", $request->input('detailed_bid_as_calculated'));
    }


    $APP = new APP;

    $project_plan = DB::table('project_bidders')
      ->select('rfq_projects.rfq_id', 'project_plans.*', 'project_timelines.post_qualification_start', 'project_timelines.post_qualification_end')
      ->where('project_bidders.project_bid', $request->input('bidder_id'))
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();


    if ($project_plan == null) {
      $project_plan = DB::table('project_bidders')
        ->select('bid_doc_projects.bid_doc_id', 'project_plans.*', 'project_timelines.post_qualification_start', 'project_timelines.post_qualification_end')
        ->where('project_bidders.project_bid', $request->input('bidder_id'))
        ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();



      if ($project_plan == null) {
        return abort(404);
      } else {

        if (strtotime($request->input('post_qual_start_date')) >=  strtotime($project_plan->post_qualification_start) &&  strtotime($request->input('post_qual_end_date')) <=  strtotime($project_plan->post_qualification_end)) {
          // dd(date("Y-m-d", strtotime($request->input('post_qual_start_date'))));
          $clusters = $APP->getClusterBids($request->input('bidder_id'));
          foreach ($clusters as $cluster) {
            $duplicate = DB::table("twg_evaluations")->where("project_bid", $cluster->project_bid)->count();
            if ($duplicate === 0) {
              $add = DB::table("twg_evaluations")->insert([
                "project_bid" => $cluster->project_bid,
                "twg_evaluation_status" => "non-responsive",
                "twg_final_bid_evaluation" => $bid_as_calculated,
                "detailed_bid_as_calculated" => $detailed_bid_as_calculated,
                "post_qual_start" => date("Y-m-d", strtotime($request->input('post_qual_start_date'))),
                "post_qual_end" => date("Y-m-d", strtotime($request->input('post_qual_end_date'))),
                "twg_evaluation_remarks" => $request->input('remarks'),
                'created_at'  => now(),
                'updated_at' => now()
              ]);
              DB::table("pqer")->insert([
                "pqer_bidder_id" => $cluster->project_bid,
                "contractor_id" => $request->contractor_id,
                "philgeps" => $request->philgeps,
                "ongoing_projects" => $request->ongoing_projects,
                "slcc" => $request->slcc,
                "bsd" => $request->bsd,
                "nfcc" => $request->nfcc,
                "spcab" => $request->spcab,
                "orgchart" => $request->orgchart,
                "key_personnel" => $request->key_personnel,
                "major_equipment" => $request->major_equipment,
                "oss" => $request->oss,
                "jva" => $request->jva,
                "boq" => $request->boq,
                "detailed_estimates" => $request->detailed_estimates,
                "cash_flow" => $request->cash_flow,
                "provincial_permit" => $request->provincial_permit,
                "construction_shedule" => $request->construction_shedule,
                "man_power" => $request->man_power,
                "construction_methods" => $request->construction_methods,
                "eus" => $request->eus,
                "chsp" => $request->chsp,
                "pert_cpm" => $request->pert_cpm,
              ]);
            }
          }
        } else {
          $message = "range_error";
        }
      }



      // $ids=$update=DB::table('project_bidders')->where('bid_doc_projects.bid_doc_id',$project_plan->bid_doc_id)
      // ->join('bid_doc_projects','bid_doc_projects.bid_doc_project_id','project_bidders.bid_doc_project_id')->get();

    } else {
      if (strtotime($request->input('post_qual_start_date')) >= strtotime($project_plan->post_qualification_start) && strtotime($request->input('post_qual_end_date')) <= strtotime($project_plan->post_qualification_end)) {
        $non_responsive_cnt = DB::table('twg_evaluations')->where("project_bid", $request->input('bidder_id'))->count();
        if ($non_responsive_cnt > 0) {
          $message = "duplicate";
        } else {
          $clusters = $APP->getClusterBids($request->input('bidder_id'));
          foreach ($clusters as $cluster) {
            $duplicate = DB::table("twg_evaluations")->where("project_bid", $cluster->project_bid)->count();
            if ($duplicate === 0) {
              $add = DB::table("twg_evaluations")->insert([
                "project_bid" => $cluster->project_bid,
                "twg_evaluation_status" => "non-responsive",
                "twg_final_bid_evaluation" => $bid_as_calculated,
                "detailed_bid_as_calculated" => $detailed_bid_as_calculated,
                "post_qual_start" => date("Y-m-d", strtotime($request->input('post_qual_start_date'))),
                "post_qual_end" => date("Y-m-d", strtotime($request->input('post_qual_end_date'))),
                "twg_evaluation_remarks" => $request->input('remarks'),
                'created_at'  => now(),
                'updated_at' => now()
              ]);
            }
          }
        }
      } else {
        $message = "range_error";
      }

      // $ids=DB::table('project_bidders')->where('rfq_projects.rfq_id',$project_plan->rfq_id)
      // ->join('rfq_projects','rfq_projects.rfq_project_id','project_bidders.rfq_project_id')->get();
    }

    // foreach ($ids as $id) {
    //   DB::table('disqualification_records')->insert([
    //     'project_bid'	=>$id->project_bid,
    //     'remarks'	=>'Non-responsive: '.$request->input('remarks'),
    //     'user_id'	=>Auth::user()->id,
    //     'created_at'	=>now(),
    //     'updated_at' =>now()
    //   ]);
    // }


    return redirect()->back()->with('message', $message);
  }

  public function setTWGResponsiveBiddder(Request $request)
  {
    $data = $request->validate([
      "post_qual_start_date" => 'required',
      "post_qual_end_date" => 'required|after_or_equal:post_qual_start_date',
      "bid_as_calculated" => 'required',
      "detailed_bid_as_calculated" => 'required_if:process,"detailed_responsive"',
      "philgeps" => 'required_if:procurement_mode,"Bidding"',
      "ongoing_projects" => 'required_if:procurement_mode,"Bidding"',
      "slcc" => 'required_if:procurement_mode,"Bidding"',
      "bsd" => 'required_if:procurement_mode,"Bidding"',
      "nfcc" => 'required_if:procurement_mode,"Bidding"',
      "spcab" => 'required_if:procurement_mode,"Bidding"',
      "orgchart" => 'required_if:procurement_mode,"Bidding"',
      "key_personnel" => 'required_if:procurement_mode,"Bidding"',
      "major_equipment" => 'required_if:procurement_mode,"Bidding"',
      "oss" => 'required_if:procurement_mode,"Bidding"',
      "jva" => 'required_if:procurement_mode,"Bidding"',
      "boq" => 'required_if:procurement_mode,"Bidding"',
      "detailed_estimates" => 'required_if:procurement_mode,"Bidding"',
      "cash_flow" => 'required_if:procurement_mode,"Bidding"',
      "provincial_permit" => 'required_if:procurement_mode,"Bidding"',
      "construction_shedule" => 'required_if:procurement_mode,"Bidding"',
      "man_power" => 'required_if:procurement_mode,"Bidding"',
      "construction_methods" => 'required_if:procurement_mode,"Bidding"',
      "eus" => 'required_if:procurement_mode,"Bidding"',
      "chsp" => 'required_if:procurement_mode,"Bidding"',
      "pert_cpm" => 'required_if:procurement_mode,"Bidding"',
    ]);

    $APP = new APP;
    $id = $request->input('bidder_id');
    $bid_as_calculated = str_replace(",", "", $request->input('bid_as_calculated'));
    $detailed_bid_as_calculated = $request->input('detailed_bid_as_calculated');
    if ($detailed_bid_as_calculated != null) {
      $detailed_bid_as_calculated = str_replace(",", "", $request->input('detailed_bid_as_calculated'));
    }
    $message = "success";
    $project_plan = DB::table('project_bidders')
      ->select('rfq_projects.rfq_id', 'project_plans.*', 'project_timelines.post_qualification_start', 'project_timelines.post_qualification_end')
      ->where([['project_bidders.project_bid', $id], ['rfqs.proposed_bid', '>', 0]])
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();


    if ($project_plan == null) {
      $project_plan = DB::table('project_bidders')
        ->select('bid_doc_projects.bid_doc_id', 'project_plans.*', 'project_timelines.post_qualification_start', 'project_timelines.post_qualification_end')
        ->where([['project_bidders.project_bid', $id], ['bid_docs.proposed_bid', '>', 0]])
        ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
        ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();


      $reponsive_bidder_cnt = DB::table('twg_evaluations')
        ->where([["project_plans.plan_id", $project_plan->plan_id], ["twg_evaluations.twg_evaluation_status", "responsive"], ["project_bidders.bid_status", "active"]])
        ->join("project_bidders", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
        ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->count();


      if ($project_plan == null) {
        $message = "zero_bid";
      } else {
        if ($reponsive_bidder_cnt == 0) {
          if (strtotime($request->input('post_qual_start_date')) >= strtotime($project_plan->post_qualification_start) &&  strtotime($request->input('post_qual_end_date')) <= strtotime($project_plan->post_qualification_end)) {
            $clusters = $APP->getClusterBids($id);
            foreach ($clusters as $cluster) {
              $duplicate = DB::table("twg_evaluations")->where("project_bid", $cluster->project_bid)->count();
              if ($duplicate === 0) {
                $add = DB::table("twg_evaluations")->insert([
                  "project_bid" => $cluster->project_bid,
                  "twg_evaluation_status" => "Responsive",
                  "detailed_bid_as_calculated" => $detailed_bid_as_calculated,
                  "twg_final_bid_evaluation" => $bid_as_calculated,
                  "post_qual_start" => date("Y-m-d", strtotime($request->input('post_qual_start_date'))),
                  "post_qual_end" => date("Y-m-d", strtotime($request->input('post_qual_end_date'))),
                  "twg_evaluation_remarks" => $request->input('remarks'),
                  'created_at'  => now(),
                  'updated_at' => now()

                ]);
              }

              DB::table("pqer")->insert([
                "pqer_bidder_id" => $cluster->project_bid,
                "contractor_id" => $request->contractor_id,
                "philgeps" => $request->philgeps,
                "ongoing_projects" => $request->ongoing_projects,
                "slcc" => $request->slcc,
                "bsd" => $request->bsd,
                "nfcc" => $request->nfcc,
                "spcab" => $request->spcab,
                "orgchart" => $request->orgchart,
                "key_personnel" => $request->key_personnel,
                "major_equipment" => $request->major_equipment,
                "oss" => $request->oss,
                "jva" => $request->jva,
                "boq" => $request->boq,
                "detailed_estimates" => $request->detailed_estimates,
                "cash_flow" => $request->cash_flow,
                "provincial_permit" => $request->provincial_permit,
                "construction_shedule" => $request->construction_shedule,
                "man_power" => $request->man_power,
                "construction_methods" => $request->construction_methods,
                "eus" => $request->eus,
                "chsp" => $request->chsp,
                "pert_cpm" => $request->pert_cpm,
              ]);
            }
          } else {
            $message = "range_error";
          }
        } else {
          $message = "bidder_chosen";
        }
      }
    } else {

      $reponsive_bidder_cnt = DB::table('twg_evaluations')
        ->where([["project_plans.plan_id", $project_plan->plan_id], ["twg_evaluations.twg_evaluation_status", "responsive"]])
        ->join("project_bidders", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->count();

      if ($project_plan == null) {
        $message = "zero_bid";
      } else {

        if ($reponsive_bidder_cnt == 0) {

          if (strtotime($request->input('post_qual_start_date')) >= strtotime($project_plan->post_qualification_start) && strtotime($request->input('post_qual_end_date')) <= strtotime($project_plan->post_qualification_end)) {
            $clusters = $APP->getClusterBids($id);

            foreach ($clusters as $cluster) {
              $duplicate = DB::table("twg_evaluations")->where("project_bid", $cluster->project_bid)->count();
              if ($duplicate === 0) {
                $add = DB::table("twg_evaluations")->insert([
                  "project_bid" => $cluster->project_bid,
                  "twg_evaluation_status" => "Responsive",
                  "twg_final_bid_evaluation" => $bid_as_calculated,
                  "detailed_bid_as_calculated" => $detailed_bid_as_calculated,
                  "post_qual_start" => date("Y-m-d", strtotime($request->input('post_qual_start_date'))),
                  "post_qual_end" => date("Y-m-d", strtotime($request->input('post_qual_end_date'))),
                  "twg_evaluation_remarks" => $request->input('remarks'),
                  'created_at'  => now(),
                  'updated_at' => now()
                ]);
              }
            }
          } else {
            $message = "range_error";
          }
        } else {
          $message = "bidder_chosen";
        }
      }
    }

    return redirect()->back()->with('message', $message);
  }

  public function setTWGBidAsCalculated(Request $request)
  {
    // dd($request->input('process'));
    $data = $request->validate([
      "post_qual_start_date" => 'required',
      "post_qual_end_date" => 'required|after:post_qual_start_date',
      "bid_as_calculated" => 'required',
      "detailed_bid_as_calculated" => 'required_if:process,"detailed_bid_as_calculated"'
    ]);

    $APP = new APP;
    $id = $request->input('bidder_id');
    $detailed_bid_as_calculated = $request->input('detailed_bid_as_calculated');
    if ($detailed_bid_as_calculated != null) {
      $detailed_bid_as_calculated = str_replace(",", "", $request->input('detailed_bid_as_calculated'));
    }
    $bid_as_calculated = str_replace(",", "", $request->input('bid_as_calculated'));

    $message = "success";
    $project_plan = DB::table('project_bidders')
      ->select('rfq_projects.rfq_id', 'project_plans.*', 'project_timelines.post_qualification_start', 'project_timelines.post_qualification_end')
      ->where([['project_bidders.project_bid', $id], ['rfqs.proposed_bid', '>', 0]])
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();


    if ($project_plan == null) {
      $project_plan = DB::table('project_bidders')
        ->select('bid_doc_projects.bid_doc_id', 'project_plans.*', 'project_timelines.post_qualification_start', 'project_timelines.post_qualification_end')
        ->where([['project_bidders.project_bid', $id], ['bid_docs.proposed_bid', '>', 0]])
        ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
        ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();


      if ($project_plan == null) {
        $message = "zero_bid";
      } else {

        if (strtotime($request->input('post_qual_start_date')) >=  strtotime($project_plan->post_qualification_start) &&  strtotime($request->input('post_qual_end_date')) <=  strtotime($project_plan->post_qualification_end)) {
          $clusters = $APP->getClusterBids($id);
          foreach ($clusters as $cluster) {
            $duplicate = DB::table("twg_evaluations")->where("project_bid", $cluster->project_bid)->count();
            if ($duplicate === 0) {
              $add = DB::table("twg_evaluations")->insert([
                "project_bid" => $cluster->project_bid,
                "twg_evaluation_status" => "active",
                "twg_final_bid_evaluation" => $bid_as_calculated,
                "detailed_bid_as_calculated" => $detailed_bid_as_calculated,
                "post_qual_start" => date("Y-m-d", strtotime($request->input('post_qual_start_date'))),
                "post_qual_end" => date("Y-m-d", strtotime($request->input('post_qual_end_date'))),
                "twg_evaluation_remarks" => $request->input('remarks'),
                'created_at'  => now(),
                'updated_at' => now()
              ]);
            }
          }
        } else {
          $message = "range_error";
        }
      }
    } else {

      if ($project_plan == null) {
        $message = "zero_bid";
      } else {

        if (strtotime($request->input('post_qual_start_date')) >= strtotime($project_plan->post_qualification_start) && strtotime($request->input('post_qual_end_date')) <= strtotime($project_plan->post_qualification_end)) {
          $clusters = $APP->getClusterBids($id);
          foreach ($clusters as $cluster) {
            $duplicate = DB::table("twg_evaluations")->where("project_bid", $cluster->project_bid)->count();
            if ($duplicate === 0) {
              $add = DB::table("twg_evaluations")->insert([
                "project_bid" => $cluster->project_bid,
                "twg_evaluation_status" => "active",
                "twg_final_bid_evaluation" => $bid_as_calculated,
                "detailed_bid_as_calculated" => $detailed_bid_as_calculated,
                "post_qual_start" => date("Y-m-d", strtotime($request->input('post_qual_start_date'))),
                "post_qual_end" => date("Y-m-d", strtotime($request->input('post_qual_end_date'))),
                "twg_evaluation_remarks" => $request->input('remarks'),
                'created_at'  => now(),
                'updated_at' => now()
              ]);
            }
          }
        } else {
          $message = "range_error";
        }
      }
    }

    return redirect()->back()->with('message', $message);
  }


  public function setNonResponsiveBiddder(Request $request)
  {
    $data = $request->validate([
      "remarks" => 'required',
    ]);

    $message = "success";
    $APP = new APP;
    $id = $request->input('bidder_id');
    $project_plan = DB::table('project_bidders')
      ->select('rfq_projects.rfq_id', "procacts.plan_id")
      ->where('project_bidders.project_bid', $id)
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();


    if ($project_plan == null) {
      $project_plan = DB::table('project_bidders')
        ->select('bid_doc_projects.bid_doc_id', "procacts.plan_id")
        ->where('project_bidders.project_bid', $id)
        ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();

      $twg_evaluation = DB::table('project_bidders')
        ->where([['project_plans.plan_id', $project_plan->plan_id], ['bid_doc_projects.bid_doc_id', $project_plan->bid_doc_id]])
        ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
        ->join("procacts", "bid_doc_projects.procact_id", "procacts.procact_id")
        ->join("project_plans", "procacts.procact_id", "project_plans.latest_procact_id")
        ->join("twg_evaluations", 'twg_evaluations.project_bid', 'project_bidders.project_bid')
        ->first();

      if ($twg_evaluation != null) {
        $clusters = $APP->getClusterBids($id);
        foreach ($clusters as $cluster) {
          $update = DB::table('project_bidders')->where([['project_plans.plan_id', $cluster->plan_id], ['bid_doc_projects.bid_doc_id', $cluster->bid_doc_id]])
            ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
            ->join("procacts", "bid_doc_projects.procact_id", "procacts.procact_id")
            ->join("project_plans", "procacts.procact_id", "project_plans.latest_procact_id")
            ->update([
              "bid_status" => "non-responsive",
            ]);
          $bidders = $APP->getBiddersData($cluster->procact_id, 'responsive,active');
          if (count($bidders) == 0) {
            DB::table('procacts')->where('procact_id', $cluster->procact_id)->update([
              "is_inactive" => true
            ]);
          }
        }

        $ids = DB::table('project_bidders')->where('bid_doc_projects.bid_doc_id', $project_plan->bid_doc_id)
          ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')->get();
      } else {
        $message = "twg_evaluation_error";
      }
    } else {
      $twg_evaluation = DB::table('project_bidders')->where([['project_plans.plan_id', $project_plan->plan_id], ['rfq_projects.rfq_id', $project_plan->rfq_id]])
        ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
        ->join("procacts", "rfq_projects.procact_id", "procacts.procact_id")
        ->join("project_plans", "procacts.procact_id", "project_plans.latest_procact_id")
        ->join("twg_evaluations", 'twg_evaluations.project_bid', 'project_bidders.project_bid')
        ->first();

      if ($twg_evaluation != null) {
        $clusters = $APP->getClusterBids($id);
        foreach ($clusters as $cluster) {
          $update = DB::table('project_bidders')->where([['project_plans.plan_id', $cluster->plan_id], ['rfq_projects.rfq_id', $cluster->rfq_id]])
            ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
            ->join("procacts", "rfq_projects.procact_id", "procacts.procact_id")
            ->join("project_plans", "procacts.procact_id", "project_plans.latest_procact_id")
            ->update([
              "bid_status" => "non-responsive",
            ]);

          $bidders = $APP->getBiddersData($cluster->procact_id, 'responsive,active');
          if (count($bidders) == 0) {
            DB::table('procacts')->where('procact_id', $cluster->procact_id)->update([
              "is_inactive" => true
            ]);
          }
        }

        $ids = DB::table('project_bidders')->where('rfq_projects.rfq_id', $project_plan->rfq_id)
          ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')->get();
      } else {
        $message = "twg_evaluation_error";
      }
    }

    if ($message === "success") {
      foreach ($ids as $id) {
        DB::table('disqualification_records')->insert([
          'project_bid'  => $id->project_bid,
          'remarks'  => 'Non-responsive: ' . $request->input('remarks'),
          'user_id'  => Auth::user()->id,
          'created_at'  => now(),
          'updated_at' => now()
        ]);
      }
    }

    return redirect()->back()->with('message', $message);
  }

  public function setResponsiveBiddder($id)
  {
    $message = "success";
    $ProcurementController = new ProcurementController;
    $APP = new APP;
    $project_plan = DB::table('project_bidders')
      ->select('rfq_projects.rfq_id', 'project_plans.*', 'project_timelines.post_qualification_start', 'project_timelines.post_qualification_end', 'procacts.*')
      ->where([['project_bidders.project_bid', $id], ['rfqs.proposed_bid', '>', 0]])
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
      ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();


    if ($project_plan == null) {
      $project_plan = DB::table('project_bidders')
        ->select('bid_doc_projects.bid_doc_id', 'project_plans.*', 'project_timelines.post_qualification_start', 'project_timelines.post_qualification_end', 'procacts.*')
        ->where([['project_bidders.project_bid', $id], ['bid_docs.proposed_bid', '>', 0]])
        ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
        ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();

      $responsive_bidder = $APP->getBiddersData($project_plan->procact_id, 'responsive');


      if ($project_plan == null) {
        $message = "zero_bid";
      } else {

        if ($project_plan->project_bid_id == null) {

          //Check TWG Evaluation

          $twg_evaluation = DB::table('project_bidders')->where([['project_plans.plan_id', $project_plan->plan_id], ['bid_doc_projects.bid_doc_id', $project_plan->bid_doc_id]])
            ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
            ->join("procacts", "bid_doc_projects.procact_id", "procacts.procact_id")
            ->join("project_plans", "procacts.procact_id", "project_plans.latest_procact_id")
            ->join("twg_evaluations", 'twg_evaluations.project_bid', 'project_bidders.project_bid')
            ->first();

          if ($twg_evaluation != null) {


            $post_qualification_start = date("m/d/Y", strtotime($project_plan->post_qualification_start));
            $post_qualification_end = date("m/d/Y", strtotime($project_plan->post_qualification_end));
            if (strtotime($twg_evaluation->post_qual_end) < strtotime($post_qualification_start) || strtotime($twg_evaluation->post_qual_end) > strtotime($post_qualification_end)) {
              $message = "range_error";
            } else {
              $clusters = $APP->getClusterBids($id);
              foreach ($clusters as $cluster) {
                $update = DB::table('project_bidders')->where([['project_plans.plan_id', $cluster->plan_id], ['bid_doc_projects.bid_doc_id', $cluster->bid_doc_id]])
                  ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
                  ->join("procacts", "bid_doc_projects.procact_id", "procacts.procact_id")
                  ->join("project_plans", "procacts.procact_id", "project_plans.latest_procact_id")
                  ->update([
                    "bid_status" => "responsive",
                  ]);


                DB::table('project_plans')->where('plan_id', $cluster->plan_id)->update([
                  "project_bid_id" => $id
                ]);

                $extend = $APP->extendSpecificProcess($cluster->plan_id, "post_qualification", date("m/d/Y", strtotime($twg_evaluation->post_qual_end)), "Project Timeline Adjusted");
                $parameters = ["plan_id" => $cluster->plan_id, "post_qualification_date" => date("m/d/Y", strtotime($twg_evaluation->post_qual_end)), "bypass" => true];
                $request = new \Illuminate\Http\Request();
                $request->replace($parameters);
                $ProcurementController->submitPostQualification($request);
              }
            }
          } else {
            $message = "twg_evaluation_error";
          }
        } else {
          $message = "bidder_chosen";
        }
      }
    } else {

      if ($project_plan == null) {
        $message = "zero_bid";
      } else {
        $responsive_bidder = $APP->getBiddersData($project_plan->latest_procact_id, 'responsive');
        // dd($responsive_bidder);
        if ($project_plan->project_bid_id == null) {

          $twg_evaluation = DB::table('project_bidders')->where([['project_plans.plan_id', $project_plan->plan_id], ['rfq_projects.rfq_id', $project_plan->rfq_id]])
            ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
            ->join("procacts", "rfq_projects.procact_id", "procacts.procact_id")
            ->join("project_plans", "procacts.procact_id", "project_plans.latest_procact_id")
            ->join("twg_evaluations", 'twg_evaluations.project_bid', 'project_bidders.project_bid')
            ->first();

          if ($twg_evaluation != null) {
            $clusters = $APP->getClusterBids($id);
            foreach ($clusters as $cluster) {
              $update = DB::table('project_bidders')->where([['project_plans.plan_id', $cluster->plan_id], ['rfq_projects.rfq_id', $cluster->rfq_id]])
                ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
                ->join("procacts", "rfq_projects.procact_id", "procacts.procact_id")
                ->join("project_plans", "procacts.procact_id", "project_plans.latest_procact_id")
                ->update([
                  "bid_status" => "responsive",
                ]);

              DB::table('project_plans')->where('plan_id', $cluster->plan_id)->update([
                "project_bid_id" => $id
              ]);

              $extend = $APP->extendSpecificProcess($cluster->plan_id, "post_qualification", date("m/d/Y", strtotime($twg_evaluation->post_qual_end)), "Project Timeline Adjusted");
              $parameters = ["plan_id" => $cluster->plan_id, "post_qualification_date" => date("m/d/Y", strtotime($twg_evaluation->post_qual_end)), "bypass" => true];
              $request = new \Illuminate\Http\Request();
              $request->replace($parameters);
              $ProcurementController->submitPostQualification($request);
            }
          } else {
            $message = "twg_evaluation_error";
          }
        } else {
          $message = "bidder_chosen";
        }
      }
    }
    return redirect()->back()->with('message', $message);
  }

  public function TWGClearPostQualificationEvaluation(Request $request)
  {
    $data = $request->validate([
      "remarks" => 'required',
    ]);

    if (is_null($request->type) === false) {
      $data = $request->validate([
        "plan_id" => "required",
        "project_title" => 'required',
        "contractor" => 'required',
        "bidder_id" => 'required',
        "remarks" => 'required',
        "password" => "required"
      ]);
      $password = $request->password;
      $user_id = Auth::user()->id;
      $checkPassword = checkPassword($user_id, $password);

      if ($checkPassword === false) {
        return back()->withInput()->with("message", "password_error");
      }
    }

    $term = "Cleared TWG Post Qualification Evaluation";
    $id = $request->input('bidder_id');
    $remarks = $request->input("remarks");
    $project_plan = DB::table('project_bidders')
      ->select('rfq_projects.rfq_id', 'project_plans.*', 'project_timelines.post_qualification_start', 'project_timelines.post_qualification_end')
      ->where([['project_bidders.project_bid', $id], ['rfqs.proposed_bid', '>', 0]])
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
      ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();


    if ($project_plan == null) {
      $project_plan = DB::table('project_bidders')
        ->select('bid_doc_projects.bid_doc_id', 'project_plans.*', 'project_timelines.post_qualification_start', 'project_timelines.post_qualification_end')
        ->where([['project_bidders.project_bid', $id], ['bid_docs.proposed_bid', '>', 0]])
        ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
        ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();
    }


    if ($project_plan === null) {
      return abort('403', "Unknown Project Bidder");
    } else {
      $APP = new APP;
      $clusters = $APP->getClusterBids($id);
      foreach ($clusters as $cluster) {


        DB::table('twg_evaluations')->where('project_bid', $cluster->project_bid)->delete();
        DB::table('pqer')->where('pqer_bidder_id', $cluster->project_bid)->delete();

        $project_bidder = ProjectBidder::find($cluster->project_bid);
        $project_bidder->bid_status = "active";
        $project_bidder->save();

        DB::table('procacts')->where('procact_id', $cluster->latest_procact_id)->update([
          "post_qual" => null
        ]);

        DB::table('project_activity_status')->where('procact_id', $cluster->latest_procact_id)->update([
          "post_qual" => "pending"
        ]);

        DB::table('project_plans')->where('project_bid_id', $cluster->project_bid)->update([
          "project_bid_id" => null
        ]);

        DB::table('project_bidder_notices')->where('project_bid', $cluster->project_bid)->delete();

        DB::table('disqualification_records')
          ->where([['project']])
          ->insert([
            'project_bid'  => $cluster->project_bid,
            'remarks'  => $term . ': ' . $request->input('remarks'),
            'user_id'  => Auth::user()->id,
            'created_at'  => now(),
            'updated_at' => now()
          ]);
      }


      return redirect()->back()->with('message', 'success');
    }
  }

  function downloadPQER($id)
  {
    $APP = new APP;
    $pqer = DB::table('pqer')->where('pqer_bidder_id', $id)->first();
    if ($pqer === null) {
      return abort('403', "Unknown Project Bidder");
    }
    $project_plan = DB::table('project_bidders')
      ->select('procacts.*', 'rfq_projects.rfq_id', 'project_plans.*', 'project_timelines.post_qualification_start', 'project_timelines.post_qualification_end')
      ->where([['project_bidders.project_bid', $id], ['rfqs.proposed_bid', '>', 0]])
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
      ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();

    if ($project_plan == null) {
      $project_plan = DB::table('project_bidders')
        ->select('procacts.*', 'bid_doc_projects.bid_doc_id', 'project_plans.*', 'project_timelines.post_qualification_start', 'project_timelines.post_qualification_end')
        ->where([['project_bidders.project_bid', $id], ['bid_docs.proposed_bid', '>', 0]])
        ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
        ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();
    }
    if ($project_plan === null) {
      return abort('403', "Unknown Project Bidder");
    } else {
      if ($project_plan->plan_cluster_id != null) {
        $letter = 'A';
        $total = 0;
        $title = "";

        $clusters = DB::table('project_plans')
          ->where([['project_timelines.bid_submission_start', $project_plan->open_bid], ['procacts.plan_cluster_id', $project_plan->plan_cluster_id]])
          ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
          ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
          ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
          ->orderBy('procacts.itb_arrangement', 'asc')
          ->get();

        foreach ($clusters as $cluster) {
          array_push($ids_array, $cluster->plan_id);
          $temp = $letter . '. ' . $cluster->project_title . ";";
          $title = $title . "   " . $temp;
          $temp_source = $letter . '. ' . $cluster->source . ";";
          $temp_project_number = $letter . '. ' . $cluster->project_no . ";";
          // $temp2=$letter.'. '.$cluster->project_cost;
          $total = $total + $cluster->project_cost;
          $letter = ++$letter;
          if ($cluster->special_case_1 == 1) {
            $title = $cluster->project_title;
          }
        }
      } else {
        $title = $project_plan->project_title;
      }
      // dd("in");
      $bid = $APP->getBid($id);
      $rank = getRank($project_plan->procact_id, $id);
      $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path() . '\\' . "excel_templates/pqer2.xlsx");
      if ($bid->twg_evaluation_status === "responsive") {
        // $sheetIndex = $spreadsheet->getIndex($spreadsheet->setActiveSheetIndexByName('non-responsive'));
        // $spreadsheet->removeSheetByIndex($sheetIndex);
        $worksheet = $spreadsheet->setActiveSheetIndexByName("responsive");
      } else {
        // $sheetIndex = $spreadsheet->getIndex($spreadsheet->setActiveSheetIndexByName('responsive'));
        // $spreadsheet->removeSheetByIndex($sheetIndex);
        $worksheet = $spreadsheet->setActiveSheetIndexByName("non-responsive");
      }
      $worksheet->getCell('A5')->setValue(strtoupper(strtolower($title)));
      $worksheet->getCell('E10')->setValue(strtoupper(strtolower($bid->business_name)));
      $worksheet->getCell('E11')->setValue(strtoupper(strtolower($rank)));
      $worksheet->getCell('E12')->setValue($bid->final_minimum_cost);
      $worksheet->getCell('E13')->setValue(date("F d,Y", strtotime($bid->post_qual_start)) . '-' . date("F d,Y", strtotime($bid->post_qual_end)));
      $worksheet->getCell('G16')->setValue($pqer->philgeps);
      if (strpos($pqer->ongoing_projects, "o ongoing") != false) {
        $worksheet->mergeCells("F17:G17");
      } else {
        $worksheet->getCell('G17')->setValue("No negative slippages based on verification ");
      }
      $worksheet->getCell('F17')->setValue($pqer->ongoing_projects);
      if (strpos($pqer->slcc, "has no") != false) {
        $worksheet->mergeCells("F18:G18");
      } else {
        $worksheet->getCell('G18')->setValue("Complied");
      }

      if ($pqer->spcab != "Not Applicable") {
        $worksheet->mergeCells("F19:G19");
      } else {
      }

      if ($pqer->orgchart != "Complied") {
        $worksheet->mergeCells("F25:G25");
        $worksheet->getCell('F25')->setValue($pqer->orgchart);
      } else {
        $worksheet->getCell('G25')->setValue($pqer->orgchart);
      }
      if ($pqer->key_personnel != "Complied") {
        $worksheet->mergeCells("F26:G26");
        $worksheet->getCell('F26')->setValue($pqer->key_personnel);
      } else {
        $worksheet->getCell('G26')->setValue($pqer->key_personnel);
      }
      if ($pqer->major_equipment != "Complied") {
        $worksheet->mergeCells("F27:G27");
        $worksheet->getCell('F27')->setValue($pqer->major_equipment);
      } else {
        $worksheet->getCell('G27')->setValue($pqer->major_equipment);
      }
      if ($pqer->oss != "Complied") {
        $worksheet->mergeCells("F28:G28");
        $worksheet->getCell('F28')->setValue($pqer->oss);
      } else {
        $worksheet->getCell('G28')->setValue($pqer->oss);
      }
      if ($pqer->jva != "Complied") {
        $worksheet->mergeCells("F30:G30");
        $worksheet->getCell('F30')->setValue($pqer->jva);
      } else {
        $worksheet->getCell('G30')->setValue($pqer->jva);
      }
      $worksheet->getCell('F18')->setValue($pqer->slcc);
      $worksheet->getCell('G19')->setValue($pqer->spcab);
      $worksheet->getCell('F23')->setValue($pqer->bsd);
      $worksheet->getCell('G29')->setValue($pqer->nfcc);
      $worksheet->getCell('G32')->setValue($bid->proposed_bid);
      $worksheet->getCell('G34')->setValue($bid->bid_as_evaluated);



      if ($pqer->detailed_estimates != "Submitted") {
        $worksheet->mergeCells("F35:G35");
        $worksheet->getCell('F35')->setValue($pqer->detailed_estimates);
      } else {
        $worksheet->getCell('G35')->setValue($pqer->detailed_estimates);
      }
      if ($pqer->cash_flow != "Submitted") {
        $worksheet->mergeCells("F36:G36");
        $worksheet->getCell('F36')->setValue($pqer->cash_flow);
      } else {
        $worksheet->getCell('G36')->setValue($pqer->cash_flow);
      }
      if ($pqer->provincial_permit != "Submitted") {
        $worksheet->mergeCells("F37:G37");
        $worksheet->getCell('F37')->setValue($pqer->provincial_permit);
      } else {
        $worksheet->getCell('G37')->setValue($pqer->provincial_permit);
      }
      if ($pqer->construction_shedule != "Submitted") {
        $worksheet->mergeCells("F38:G38");
        $worksheet->getCell('F38')->setValue($pqer->construction_shedule);
      } else {
        $worksheet->getCell('G38')->setValue($pqer->construction_shedule);
      }
      if ($pqer->man_power != "Submitted") {
        $worksheet->mergeCells("F39:G39");
        $worksheet->getCell('F39')->setValue($pqer->man_power);
      } else {
        $worksheet->getCell('G39')->setValue($pqer->man_power);
      }
      if ($pqer->construction_methods != "Submitted") {
        $worksheet->mergeCells("F40:G40");
        $worksheet->getCell('F40')->setValue($pqer->construction_methods);
      } else {
        $worksheet->getCell('G40')->setValue($pqer->construction_methods);
      }
      if ($pqer->eus != "Submitted") {
        $worksheet->mergeCells("F41:G41");
        $worksheet->getCell('F41')->setValue($pqer->eus);
      } else {
        $worksheet->getCell('G41')->setValue($pqer->eus);
      }
      if ($pqer->chsp != "Submitted") {
        $worksheet->mergeCells("F42:G42");
        $worksheet->getCell('F42')->setValue($pqer->chsp);
      } else {
        $worksheet->getCell('G42')->setValue($pqer->chsp);
      }
      if ($pqer->pert_cpm != "Submitted") {
        $worksheet->mergeCells("F43:G43");
        $worksheet->getCell('F43')->setValue($pqer->pert_cpm);
      } else {
        $worksheet->getCell('G43')->setValue($pqer->pert_cpm);
      }

      $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
      $writer->save(public_path() . '\\' . "excel_templates/" . $project_plan->project_no . ".xlsx");
      return  response()->download(public_path() . '\\' . "excel_templates/"  . $project_plan->project_no . ".xlsx")->deleteFileAfterSend(true);
    }
  }

  public function clearPostQualificationEvaluation(Request $request)
  {
    $data = $request->validate([
      "remarks" => 'required',
    ]);

    if (is_null($request->type) === false) {
      $data = $request->validate([
        "plan_id" => "required",
        "project_title" => 'required',
        "contractor" => 'required',
        "bidder_id" => 'required',
        "remarks" => 'required',
        "password" => "required"
      ]);
      $password = $request->password;
      $user_id = Auth::user()->id;
      $checkPassword = checkPassword($user_id, $password);

      if ($checkPassword === false) {
        return back()->withInput()->with("message", "password_error");
      }
    }

    $term = "Cleared Post Qualification Evaluation";
    $id = $request->input('bidder_id');
    $remarks = $request->input("remarks");
    $project_plan = DB::table('project_bidders')
      ->select('rfq_projects.rfq_id', 'project_plans.*', 'project_timelines.post_qualification_start', 'project_timelines.post_qualification_end')
      ->where([['project_bidders.project_bid', $id], ['rfqs.proposed_bid', '>', 0]])
      ->join('rfq_projects', 'rfq_projects.rfq_project_id', 'project_bidders.rfq_project_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('procacts', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
      ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();

    if ($project_plan == null) {
      $project_plan = DB::table('project_bidders')
        ->select('bid_doc_projects.bid_doc_id', 'project_plans.*', 'project_timelines.post_qualification_start', 'project_timelines.post_qualification_end')
        ->where([['project_bidders.project_bid', $id], ['bid_docs.proposed_bid', '>', 0]])
        ->join('bid_doc_projects', 'bid_doc_projects.bid_doc_project_id', 'project_bidders.bid_doc_project_id')
        ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
        ->join('procacts', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
        ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();
    }

    if ($project_plan === null) {
      return abort('403', "Unknown Project Bidder");
    } else {
      $APP = new APP;
      $clusters = $APP->getClusterBids($id);
      foreach ($clusters as $cluster) {
        $project_bidder = ProjectBidder::find($cluster->project_bid);
        $project_bidder->bid_status = "active";
        $project_bidder->save();

        DB::table('procacts')->where('procact_id', $cluster->latest_procact_id)->update([
          "post_qual" => null
        ]);

        DB::table('project_activity_status')->where('procact_id', $cluster->latest_procact_id)->update([
          "post_qual" => "pending"
        ]);

        DB::table('project_plans')->where('project_bid_id', $cluster->project_bid)->update([
          "project_bid_id" => null
        ]);

        DB::table('project_bidder_notices')->where('project_bid', $cluster->project_bid)->delete();


        DB::table('disqualification_records')
          ->where([['project']])
          ->insert([
            'project_bid'  => $cluster->project_bid,
            'remarks'  => $term . ': ' . $request->input('remarks'),
            'user_id'  => Auth::user()->id,
            'created_at'  => now(),
            'updated_at' => now()
          ]);

        $bidders = $APP->getBiddersData($cluster->procact_id, 'responsive,active');
        if (count($bidders) != 0) {
          DB::table('procacts')->where('procact_id', $cluster->procact_id)->update([
            "is_inactive" => false
          ]);
        }
      }



      return redirect()->back()->with('message', 'success');
    }
  }
}
