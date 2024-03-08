<?php

namespace App\Http\Controllers;

use App\{Termination, Procact};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\APP;
use Validator;


class TerminationController extends Controller
{
  public function getTerminationOfContract(Request $request)
  {
    $year = $request->year;
    $APP = new APP;
    if ($request->year === null) {
      $year = Date('Y');
    }

    $raw_terminations = Termination::where('termination.created_at', "like", $year . "%")
      ->orderBy("termination_id", "desc")
      ->join('governors', 'governors.governor_id', 'termination.governor_id')
      ->get();
    $terminations = [];
    foreach ($raw_terminations as $termination) {
      $bid_details = $APP->getBid($termination->project_bid);
      $terminations[] = [
        'termination_id' => $termination->termination_id,
        'project_number' => $bid_details->project_no,
        'procact_id' =>  $bid_details->procact_id,
        'project_title' => $bid_details->project_title,
        'project_bid' =>  $termination->project_bid,
        'contractor' => $bid_details->business_name,
        'governor_id' => $termination->governor_id,
        'governor' => $termination->name,
        'reason' =>  $termination->reason,
      ];
    }

    if ($request->year === null) {
      $governors = DB::table("governors")->orderBy('governor_id', 'desc')->limit(10)->get();
      $links = getUserLinks();
      $user_privilege = getUserPrivilege();


      return view('admin.terminations', ['links' => $links, 'user_privilege' => $user_privilege, 'terminations' => $terminations, "year" => $year, "governors" => $governors]);
    } else {
      return back()->withInput()->with('terminations', $terminations);
    }
  }


  public function submitTerminationOfContract(Request $request)
  {
    $data = $request->validate([
      "project_title" => "required",
      "procact_id" => "required",
      "project_bid" => "required",
      "contractor" => "required",
      "governor" => "required",
      "reason" => "required"
    ]);
    $message = "success";
    $termination_id = $request->termination_id;
    $APP = new APP;
    $cluster_bids = $APP->getClusterBids($request->project_bid);
    if ($termination_id === null) {
      foreach ($cluster_bids as $bid) {
        $duplicate = Termination::where('project_bid', $bid->project_bid)->count();
        if ($duplicate > 0) {
          $message = "duplicate";
        }
      }
      if ($message === "success") {
        foreach ($cluster_bids as $bid) {
          Termination::create([
            "procact_id" => $request->procact_id,
            "project_bid" => $bid->project_bid,
            "contractor_id" => $bid->contractor_id,
            "governor_id" => $request->governor,
            "reason" => $request->reason
          ]);

          // update project activity status to terminated

          DB::table('project_activity_status')->where('procact_id', $bid->procact_id)->update([
            "main_status" => "terminated"
          ]);

          DB::table('project_plans')->where('procacts.procact_id', $bid->procact_id)
            ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')->update([
              'project_plans.project_bid_id' => null
            ]);

          DB::table('project_logs')->insert([
            'plan_id' =>  $bid->plan_id,
            'user_id' => Auth::user()->id,
            'project_log_type' => "Termination of Contract",
            'project_log_remarks' => $bid->business_name . ":" . $request->reason,
            'log_date' => date("Y-m-d"),
            'created_at' => now(),
            'updated_at' => now()
          ]);
        }
      }
    } else {
      $termination = Termination::find($termination_id);
      if ($termination != null) {
        $cluster_bids = $APP->getClusterBids($termination->project_bid);
        foreach ($cluster_bids as $bid) {

          DB::table('project_plans')->where('procacts.procact_id', $bid->procact_id)
            ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')->update([
              'project_plans.project_bid_id' => null
            ]);

          Termination::where('project_bid', $bid->project_bid)->update([
            "reason" => $request->reason,
            "governor_id" => $request->governor,
          ]);
        }
      } else {
        $message = "unknown_termination";
      }
    }
    // return message
    if ($message === "success") {
      return back()->with('message', $message);
    } else {
      return back()->withInput()->with('message', $message);
    }
  }
}
