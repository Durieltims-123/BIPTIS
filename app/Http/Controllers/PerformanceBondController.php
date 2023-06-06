<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\APP;
use App\PerformanceBond;
use App\Contract;

class PerformanceBondController extends Controller
{
  function getAdditionalPerformanceBond(Request $request)
  {
    $title = "Additional Performance Bonds";
    $APP = new APP;
    if ($request->project_year != null) {
      $year = $request->project_year;
      $data = PerformanceBond::where("additional_performance_bonds.additional_pb_date_issuance", "like", $year . "%")
        ->join("contracts", "contracts.contract_id", "additional_performance_bonds.contract_id")
        ->join("project_bidders", "project_bidders.project_bid", "contracts.project_bid_id")
        ->join("contractors", "contractors.contractor_id", "additional_performance_bonds.contractor_id")
        ->join("project_plans", "project_plans.project_bid_id", "project_bidders.project_bid")
        ->get();
      return back()->withInput()->with("data", $data);
    } else {
      $year = date("Y");
      $data = PerformanceBond::where("additional_performance_bonds.additional_pb_date_issuance", "like", $year . "%")
        ->join("contracts", "contracts.contract_id", "additional_performance_bonds.contract_id")
        ->join("project_bidders", "project_bidders.project_bid", "contracts.project_bid_id")
        ->join("contractors", "contractors.contractor_id", "additional_performance_bonds.contractor_id")
        ->join("project_plans", "project_plans.project_bid_id", "project_bidders.project_bid")
        ->get();
      $links = getUserLinks();
      $user_privilege = getUserPrivilege();
      return view("admin.additional_performance_bond", ["links" => $links, 'user_privilege' => $user_privilege, "title" => $title, "data" => $data, "year" => $year]);
    }
  }

  function getInsufficientPerformanceBond(Request $request)
  {

    $title = "Insufficient Performance Bond";
    $year = date("Y");
    $data = getInsufficientPerformanceBond($year, false);
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view("admin.insufficient_performance_bond", ["links" => $links, 'user_privilege' => $user_privilege, "title" => $title, "data" => $data, "year" => $year]);
  }

  function autoCompleteProjectWithContracts(Request $request)
  {
    $term = $request->term;
    $APP = new APP;

    $project_plans = Contract::where([
      ["project_plans.project_bid_id", "<>", null],
      ["project_plans.project_title", "LIKE", "%" . $term . "%"],
      ["project_timelines.timeline_status", "set"]
    ])
      ->join("project_bidders", "project_bidders.project_bid", "contracts.project_bid_id")
      ->join("project_plans", "project_plans.project_bid_id", "project_bidders.project_bid")
      ->join("procacts", "project_plans.latest_procact_id", "procacts.procact_id")
      ->join("project_timelines", "project_timelines.procact_id", "procacts.procact_id")
      ->orderBy("project_plans.project_title", "desc")
      ->distinct()
      ->take(10)
      ->get();

    if (sizeOf($project_plans) != 0) {
      foreach ($project_plans as $project_plan) {
        $bid_details = $APP->getBid($project_plan->project_bid_id);
        $results[] = [
          "id" => $project_plan->plan_id,
          "procact_id" =>  $bid_details->procact_id,
          "procact" =>  $bid_details->procact_id,
          "value" => $project_plan->project_title,
          "project_number" => $project_plan->project_no,
          "project_bid_id" =>  $project_plan->project_bid_id,
          "contractor_id" =>  $bid_details->contractor_id,
          "contractor" =>  $bid_details->business_name,
          "contract_id" =>  $project_plan->contract_id,
        ];
      }
    } else {
      $results[] = [
        "id" => "",
        "value" => "No Match Found"
      ];
    }
    return response()->json($results);
  }

  public function submitAdditionalPerformanceBond(Request $request)
  {
    $APP = new APP;
    $contractor_id = $request->input("bidder_id");
    $contract_id = $request->input("contract_id");
    $contract = Contract::find($contract_id);
    $id = $request->input("additional_pb_id");
    $message = "success";

    $data = $request->validate([
      "project_title" => "required",
      "contractor" => "required",
      "request_to_submit_performance_bond" => "required|before:tomorrow",
      "performance_bond_expiration" => "required|after:request_to_submit_performance_bond",
      "date_received_by_bac" => "required|before:tomorrow|after_or_equal:request_to_submit_performance_bond"
    ]);

    if ($contract != null) {
      $latest_additional_pb = PerformanceBond::where("contract_id", $contract_id)->orderBy("additional_pb_id", "desc")->first();
      $next_additional_pb = null;
      if ($id != null) {
        $latest_additional_pb = PerformanceBond::where([["contract_id", $contract_id], ["additional_pb_id", "<", $id]])->orderBy("additional_pb_id", "desc")->first();
        $next_additional_pb = PerformanceBond::where([["contract_id", $contract_id], ["additional_pb_id", ">", $id]])->orderBy("additional_pb_id", "asc")->first();
      }

      if ($latest_additional_pb === null) {
        $received_date = $contract->performance_bond_receive_date;
        $last_expiration = $contract->performance_bond_expiration;
      } else {
        $received_date = $latest_additional_pb->additional_pb_received_date;
        $last_expiration = $latest_additional_pb->additional_pb_expiration;
      }
      if ($next_additional_pb != null) {
        $data = $request->validate([
          "request_to_submit_performance_bond" => "required|before:tomorrow|after:" . Date(
            "m/d/Y",
            strtotime($received_date)
          ),
          "performance_bond_expiration" => "after:" . Date(
            "m/d/Y",
            strtotime($last_expiration)
          ) . "|before:" . Date(
            "m/d/Y",
            strtotime($next_additional_pb->additional_pb_expiration)
          )
        ]);
      } else {
        $data = $request->validate([
          "request_to_submit_performance_bond" => "required|after:" . Date(
            "m/d/Y",
            strtotime($received_date)
          ),
          "performance_bond_expiration" => "after:" . Date(
            "m/d/Y",
            strtotime($last_expiration)
          )
        ]);
      }
    }

    $date_received_by_bac = date("Y-m-d", strtotime($request->input("date_received_by_bac")));
    $request_to_submit_performance_bond = date("Y-m-d", strtotime($request->input("request_to_submit_performance_bond")));
    $performance_bond_expiration = date("Y-m-d", strtotime($request->input("performance_bond_expiration")));
    $performance_bond_remarks = $request->input("performance_bond_remarks");


    if ($id === null) {
      $cluster_bids = $APP->getClusterBids($contract->project_bid_id);
      foreach ($cluster_bids as $contract) {
        PerformanceBond::where("contract_id", $contract->contract_id)->update([
          "additional_pb_status" => 0
        ]);

        PerformanceBond::create([
          "contract_id" => $contract->contract_id,
          "contractor_id" => $contract->contractor_id,
          "additional_pb_date_issuance" => $request_to_submit_performance_bond,
          "additional_pb_expiration" => $performance_bond_expiration,
          "additional_pb_received_date" => $date_received_by_bac,
          "additional_pb_remarks" => $performance_bond_remarks,
          "additional_pb_cluster" => $contract->plan_cluster_id
        ]);
      }
    } else {
      $performance_bond = PerformanceBond::find($id);
      $cluster_bids = $APP->getClusterBids($contract->project_bid_id);
      if ($performance_bond->additional_pb_cluster != null) {

        PerformanceBond::where([["additional_pb_cluster", $performance_bond->additional_pb_cluster], ["created_at", $performance_bond->created_at]])->update([
          "additional_pb_date_issuance" => $request_to_submit_performance_bond,
          "additional_pb_expiration" => $performance_bond_expiration,
          "additional_pb_received_date" => $date_received_by_bac,
          "additional_pb_remarks" => $performance_bond_remarks
        ]);
      } else {
        $performance_bond->contract_id = $contract_id;
        $performance_bond->contractor_id = $contractor_id;
        $performance_bond->additional_pb_date_issuance = $request_to_submit_performance_bond;
        $performance_bond->additional_pb_expiration = $performance_bond_expiration;
        $performance_bond->additional_pb_received_date = $date_received_by_bac;
        $performance_bond->additional_pb_remarks = $performance_bond_remarks;
        $performance_bond->save();
      }
    }



    return back()->with("message", $message);
  }

  public function deleteAdditionalPerformanceBond(Request $request)
  {

    $performance_bond = PerformanceBond::find($request->id);
    if ($performance_bond->additional_pb_cluster != null) {
      $clusters = PerformanceBond::where([["additional_pb_cluster", $performance_bond->additional_pb_cluster], ["created_at", $performance_bond->created_at]])->get();
      PerformanceBond::where([["additional_pb_cluster", $performance_bond->additional_pb_cluster], ["created_at", $performance_bond->created_at]])->delete();
      foreach ($clusters as $data) {
        $latest = PerformanceBond::where("contract_id", $data->contract_id)->orderBy("additional_pb_id", "desc")->first();
        if ($latest != null) {
          PerformanceBond::where("additional_pb_id", $latest->additional_pb_id)->update(["additional_pb_status" => 1]);
        }
      }
    } else {
      $performance_bond->delete();
      $latest = PerformanceBond::where("contract_id", $performance_bond->contract_id)->orderBy("additional_pb_id", "desc")->first();
      if ($latest != null) {
        PerformanceBond::where("additional_pb_id", $latest->additional_pb_id)->update(["additional_pb_status" => 1]);
      }
    }
    return "success";
  }
}
