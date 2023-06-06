<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\APP;
use Validator;

class AdditionalDocumentController extends Controller
{
  public function getSVPAdditionalDocuments()
  {
    $year = date('Y');
    $additional_documents = DB::table('document_types')->join('additional_required_documents', 'document_types.id', 'additional_required_documents.document_type_id')
      ->where('document_types.project_type', 'svp')->orderBy('additional_required_documents.sequence')->get();
    $additional_ids = DB::table('document_types')->select(DB::raw(" GROUP_CONCAT(document_type_id) AS ids"))->join('additional_required_documents', 'document_types.id', 'additional_required_documents.document_type_id')->where('document_types.project_type', 'svp')->get();
    $additional_ids = explode(",", $additional_ids[0]->ids);
    $project_documents = DB::table('document_types')->where('project_type', 'svp')->whereNotIn('id', $additional_ids)->get();
    $title = "SVP/Negotiated Additional Documents";

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('admin.additional_requirements', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'year' => $year, "project_documents" => $project_documents, "additional_documents" => $additional_documents, "project_type" => "svp"]);
  }

  public function getBiddingAdditionalDocuments()
  {
    $year = date('Y');
    $additional_documents = DB::table('document_types')
      ->join('additional_required_documents', 'document_types.id', 'additional_required_documents.document_type_id')
      ->where('document_types.project_type', 'bidding')->orderBy('additional_required_documents.sequence')
      ->get();
    $additional_ids = DB::table('document_types')->select(DB::raw(" GROUP_CONCAT(document_type_id) AS ids"))->join('additional_required_documents', 'document_types.id', 'additional_required_documents.document_type_id')->where('document_types.project_type', 'bidding')->get();
    $additional_ids = explode(",", $additional_ids[0]->ids);
    $project_documents = DB::table('document_types')->where('project_type', 'bidding')->whereNotIn('id', $additional_ids)->get();
    $title = "Public Bidding Additional Documents";

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view('admin.additional_requirements', ['links' => $links, 'user_privilege' => $user_privilege, 'title' => $title, 'year' => $year, "project_documents" => $project_documents, "additional_documents" => $additional_documents, "project_type" => "bidding"]);
  }

  public function submitAdditionalDocuments(Request $request)
  {
    $document_ids = $request->input('document_type_ids');
    if ($document_ids === "" || $document_ids === null) {
      $message = "no_documents_selected";
    } else {
      $document_ids_array = explode(",", $document_ids);
      $message = "success";
      $counter = 1;
      $project_type = $request->input('project_type');
      $delete = DB::table('additional_required_documents')->join('document_types', 'document_types.id', 'additional_required_documents.document_type_id')->where("document_types.project_type", $project_type)->delete();
      foreach ($document_ids_array as $document_id) {
        $add = DB::table('additional_required_documents')->insert([
          "document_type_id" => (int)$project_type,
          "sequence" => $counter,
          "document_type_id" => $document_id,
          "created_at" => now(),
          "updated_at" => now()
        ]);
        $counter = $counter + 1;
      }
    }

    return back()->with("message", $message);
  }

  //additional documents

  public function getRequirementsChecklist()
  {
    $title = "Requirements Checklist";
    $year = date('Y');
    $APP = new App;
    $svp_requirements = DB::table('additional_required_documents')
      ->join('document_types', 'document_types.id', 'additional_required_documents.document_type_id')
      ->where('document_types.project_type', 'svp')
      ->orderBy('sequence', 'asc')->get();
    $bidding_requirements = DB::table('additional_required_documents')
      ->join('document_types', 'document_types.id', 'additional_required_documents.document_type_id')
      ->where('document_types.project_type', 'bidding')
      ->orderBy('sequence', 'asc')->get();

    $svp_requirements = (array)json_decode($svp_requirements);
    $bidding_requirements = (array)json_decode($bidding_requirements);
    $project_plans = (array) $APP->getRequirementsChecklist(null, $year, null);
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view("admin.project_bidders_additional_docs_checklist", ['links' => $links, 'user_privilege' => $user_privilege,  "title" => $title, "year" => $year, "svp_requirements" => $svp_requirements, "bidding_requirements" => $bidding_requirements, "project_plans" => $project_plans]);
  }

  public function filterProjectBiddersRequirements(Request $request)
  {
    $data = $request->validate([
      "project_year" => 'required|digits:4|integer|min:2020|max:' . (date('Y')),
    ]);
    $APP = new APP;
    $year = $request->project_year;
    $project_plans = (array) $APP->getRequirementsChecklist(null, $year, null);
    return back()->withInput()->with("project_plans", $project_plans);
  }

  public function filterReleaseProjectBiddersRequirements(Request $request)
  {
    $data = $request->validate([
      "project_year" => 'required|digits:4|integer|min:2020|max:' . (date('Y')),
    ]);
    $APP = new APP;
    $year = $request->project_year;
    $project_plans = (array) $APP->getRequirementsChecklist(["incomplete", "complete"], $year, true);
    return back()->withInput()->with("project_plans", $project_plans);
  }

  public function filterReceiveProjectBiddersRequirements(Request $request)
  {
    $data = $request->validate([
      "project_year" => 'required|digits:4|integer|min:2020|max:' . (date('Y')),
    ]);
    $APP = new APP;
    $year = $request->project_year;
    $project_plans = (array) $APP->getRequirementsChecklist("released", $year, true);
    return back()->withInput()->with("project_plans", $project_plans);
  }

  public function submitProjectBiddersAdditionalDocuments(Request $request)
  {
    $data = $request->validate([
      "date_created" => "required|after_or_equal:opening_date"
    ]);

    $APP = new APP;
    $pbard_id = $request->input('pbard_id');
    $project_bid_id = $request->input('project_bid_id');
    $present_documents = $request->input('present_documents');
    $missing_documents = $request->input('missing_documents');
    $na_documents = $request->input('na_documents');
    $date_created = date("Y-m-d", strtotime($request->input('date_created')));
    $status = "complete";
    $cluster_bids = $APP->getClusterBids($project_bid_id);
    $is_duplicate = false;
    if ($missing_documents != null) {
      $status = "incomplete";
    }
    if ($pbard_id === null) {
      foreach ($cluster_bids as $key => $cluster_bid) {
        $duplicate = DB::table('project_bidder_additional_required_documents')->where('project_bid_id', $cluster_bid->project_bid)->count();
        if ($duplicate >= 1) {
          $is_duplicate = true;
        }
      }
      if ($is_duplicate) {
        return back()->withInput()->with("message", "duplicate");
      } else {
        foreach ($cluster_bids as $key => $cluster_bid) {
          $insert = DB::table('project_bidder_additional_required_documents')->insert([
            "project_bid_id" => $cluster_bid->project_bid,
            "present_docs" => $present_documents,
            "missing_docs" => $missing_documents,
            "na_docs" => $na_documents,
            "date_created" => $date_created,
            "additional_docs_status" => $status,
            "created_at" => now(),
            "updated_at" => now()
          ]);
        }
        return back()->with("message", "success");
      }
    }
    // update
    else {
      foreach ($cluster_bids as $key => $cluster_bid) {
        $update = DB::table('project_bidder_additional_required_documents')
          ->where("project_bid_id", $cluster_bid->project_bid)
          ->update([
            "present_docs" => $present_documents,
            "missing_docs" => $missing_documents,
            "na_docs" => $na_documents,
            "date_created" => $date_created,
            "additional_docs_status" => $status,
            "updated_at" => now()
          ]);
      }
      return back()->with("message", "success");
    }
  }

  public function releaseNoticeToSubmitDocuments()
  {
    $title = "Release Notice to Submit Documents";
    $year = date('Y');
    $APP = new App;
    $svp_requirements = DB::table('additional_required_documents')
      ->join('document_types', 'document_types.id', 'additional_required_documents.document_type_id')
      ->where('document_types.project_type', 'svp')->get();

    $bidding_requirements = DB::table('additional_required_documents')
      ->join('document_types', 'document_types.id', 'additional_required_documents.document_type_id')
      ->where('document_types.project_type', 'bidding')->get();

    $svp_requirements = (array)json_decode($svp_requirements);
    $bidding_requirements = (array)json_decode($bidding_requirements);
    $project_plans = (array) $APP->getRequirementsChecklist(["incomplete", "complete"], $year, true);

    $links = getUserLinks();
    $user_privilege = getUserPrivilege();
    return view("admin.release_notice_to_submit_documents", ['links' => $links, 'user_privilege' => $user_privilege, "title" => $title, "year" => $year, "svp_requirements" => $svp_requirements, "bidding_requirements" => $bidding_requirements, "project_plans" => $project_plans]);
  }

  public function submitReleaseNoticeToSubmitDocuments(Request $request)
  {

    $opening = (date('m/d/Y', strtotime($request->input('opening_date'))));
    $data = $request->validate([
      "date_released" => "required|after_or_equal:" . $opening
    ]);

    $APP = new APP;
    if ($request->input('date_released') != null) {
      $date_released = date("Y-m-d", strtotime($request->input('date_released')));
    } else {
      $date_released = null;
    }

    $project_bid_id = $request->input('project_bid_id');
    $pbard_id = $request->input('pbard_id');
    $cluster_bids = $APP->getClusterBids($project_bid_id);

    $pbard = DB::table("project_bidder_additional_required_documents")->where("pbard_id", $pbard_id)->first();

    if ($pbard != null) {
      if ($pbard->date_received != null) {
        return back()->with("message", "received_error");
      }
    }

    foreach ($cluster_bids as $key => $cluster_bid) {
      $update = DB::table('project_bidder_additional_required_documents')
        ->where("project_bid_id", $cluster_bid->project_bid)
        ->update([
          "date_released" => $date_released,
          "updated_at" => now()
        ]);
    }

    return back()->with("message", "success");
  }

  public function receiveDocuments()
  {
    $title = "Receive Additional/Post Qualification Documents";
    $year = date('Y');
    $APP = new App;
    $svp_requirements = DB::table('additional_required_documents')
      ->join('document_types', 'document_types.id', 'additional_required_documents.document_type_id')
      ->where('document_types.project_type', 'svp')->get();

    $bidding_requirements = DB::table('additional_required_documents')
      ->join('document_types', 'document_types.id', 'additional_required_documents.document_type_id')
      ->where('document_types.project_type', 'bidding')->get();

    $svp_requirements = (array)json_decode($svp_requirements);
    $bidding_requirements = (array)json_decode($bidding_requirements);
    $project_plans = (array) $APP->getRequirementsChecklist("released", $year, true);
    $links = getUserLinks();
    $user_privilege = getUserPrivilege();

    return view("admin.receive_documents", ['links' => $links, 'user_privilege' => $user_privilege, "title" => $title, "year" => $year, "svp_requirements" => $svp_requirements, "bidding_requirements" => $bidding_requirements, "project_plans" => $project_plans]);
  }

  public function submitReceiveDocuments(Request $request)
  {
    $date_released = (date('m/d/Y', strtotime($request->input('date_released'))));
    $data = $request->validate([
      "date_received" => "required|after_or_equal:" . $date_released,
      "status" => "required"
    ]);

    $APP = new APP;
    $date_received = date("Y-m-d", strtotime($request->input('date_received')));
    $project_bid_id = $request->input('project_bid_id');
    $pbard_status = $request->input('status');
    $pbard_remarks = $request->input('remarks');
    $cluster_bids = $APP->getClusterBids($project_bid_id);

    foreach ($cluster_bids as $key => $cluster_bid) {
      $update = DB::table('project_bidder_additional_required_documents')
        ->where("project_bid_id", $cluster_bid->project_bid)
        ->update([
          "date_received" => $date_received,
          "pbard_status" => $pbard_status,
          "pbard_remarks" => $pbard_remarks,
          "updated_at" => now()
        ]);
    }

    return back()->with("message", "success");
  }
}
