<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\{APP, Contract, PerformanceBond, ProjectBidder, Procact, ProjectTimeline, Holiday, link, UserLink, ProjectBidderNotice};

function prepare_notice($notice_type, $filter, $year)
{

  $rfq_project_bidders = ProjectBidder::select('project_bidders.project_bid as main_id', 'project_bidders.*', 'rfqs.*', 'project_plans.project_no', 'project_plans.project_title', 'contractors.*', 'twg_evaluations.*', 'project_bidder_notices.*', 'procacts.*', 'project_bidder_notices.date_released as notice_date_released', 'project_bidder_notices.date_received as notice_date_received', 'project_bidder_notices.date_generated as notice_date_generated');
  $bid_doc_project_bidders = ProjectBidder::select('project_bidders.project_bid as main_id', 'project_bidders.*', 'bid_docs.*', 'project_plans.project_no', 'project_plans.project_title', 'twg_evaluations.*', 'contractors.*', 'project_bidder_notices.*', 'procacts.*', 'project_bidder_notices.date_released as notice_date_released', 'project_bidder_notices.date_received as notice_date_received', 'project_bidder_notices.date_generated as notice_date_generated');


  if ($notice_type === "NOD") {
    if ($filter === "all") {
      $rfq_project_bidders = $rfq_project_bidders
        ->where([['bid_status', 'disqualified'], ['project_bidder_notices.date_released', 'like', $year . '%']])
        ->orWhere([['bid_status', 'disqualified'], ['project_bidder_notices.date_released', null], ['procacts.open_bid', 'like', $year . '%']]);

      $bid_doc_project_bidders = $bid_doc_project_bidders
        ->where([['bid_status', 'disqualified'], ['project_bidder_notices.date_released', 'like', $year . '%']])
        ->orWhere([['bid_status', 'disqualified'], ['project_bidder_notices.date_released', null], ['procacts.open_bid', 'like', $year . '%']]);
    } else if ($filter === "for_preparation") {
      $rfq_project_bidders = $rfq_project_bidders
        ->where([['bid_status', 'disqualified'], ['project_bidder_notice_id', null], ['procacts.open_bid', 'like', $year . '%']]);

      $bid_doc_project_bidders = $bid_doc_project_bidders
        ->where([['bid_status', 'disqualified'], ['project_bidder_notice_id', null], ['procacts.open_bid', 'like', $year . '%']]);
    } else {
      $rfq_project_bidders = $rfq_project_bidders
        ->where([['bid_status', 'disqualified'], ['project_bidder_notices.date_released', 'like', $year . '%']]);

      $bid_doc_project_bidders = $bid_doc_project_bidders
        ->where([['bid_status', 'disqualified'], ['project_bidder_notices.date_released', 'like', $year . '%']]);
    }


    $rfq_project_bidders = $rfq_project_bidders
      ->leftJoin('project_bidder_notices', 'project_bidder_notices.project_bid', 'project_bidders.project_bid')
      ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->distinct()
      ->get();

    $bid_doc_project_bidders = $bid_doc_project_bidders
      ->leftJoin('project_bidder_notices', 'project_bidder_notices.project_bid', 'project_bidders.project_bid')
      ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->distinct()
      ->get();

    $project_bidders = [];

    if (count($rfq_project_bidders) >= 1 && count($bid_doc_project_bidders) >= 1) {
      $project_bidders = array_merge((array) json_decode($rfq_project_bidders), (array) json_decode($bid_doc_project_bidders));
    } else if (count($rfq_project_bidders) === 0 && count($bid_doc_project_bidders) >= 1) {
      $project_bidders = $bid_doc_project_bidders;
    } else if (count($rfq_project_bidders) >= 1 && count($bid_doc_project_bidders) === 0) {
      $project_bidders = $rfq_project_bidders;
    } else {
      $project_bidders = [];
    }
  } else if ($notice_type === "NOI") {
    if ($filter === "all") {
      $rfq_project_bidders = $rfq_project_bidders
        ->where([['bid_status', 'ineligible'], ['project_bidder_notices.date_released', 'like', $year . '%']])
        ->orWhere([['bid_status', 'ineligible'], ['project_bidder_notices.date_released', null], ['procacts.open_bid', 'like', $year . '%']]);
      $bid_doc_project_bidders = $bid_doc_project_bidders
        ->where([['bid_status', 'ineligible'], ['project_bidder_notices.date_released', 'like', $year . '%']])
        ->orWhere([['bid_status', 'ineligible'], ['project_bidder_notices.date_released', null], ['procacts.open_bid', 'like', $year . '%']]);
    } else if ($filter === "for_preparation") {
      $rfq_project_bidders = $rfq_project_bidders
        ->where([['bid_status', 'ineligible'], ['project_bidder_notice_id', null], ['procacts.open_bid', 'like', $year . '%']]);

      $bid_doc_project_bidders = $bid_doc_project_bidders
        ->where([['bid_status', 'ineligible'], ['project_bidder_notice_id', null], ['procacts.open_bid', 'like', $year . '%']]);
    } else {
      $rfq_project_bidders = $rfq_project_bidders
        ->where([['bid_status', 'ineligible'], ['project_bidder_notices.date_released', 'like', $year . '%']]);

      $bid_doc_project_bidders = $bid_doc_project_bidders
        ->where([['bid_status', 'ineligible'], ['project_bidder_notices.date_released', 'like', $year . '%']]);
    }


    $rfq_project_bidders = $rfq_project_bidders
      ->leftJoin('project_bidder_notices', 'project_bidder_notices.project_bid', 'project_bidders.project_bid')
      ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->distinct()
      ->get();

    $bid_doc_project_bidders = $bid_doc_project_bidders
      ->leftJoin('project_bidder_notices', 'project_bidder_notices.project_bid', 'project_bidders.project_bid')
      ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->distinct()
      ->get();

    $project_bidders = [];

    if (count($rfq_project_bidders) >= 1 && count($bid_doc_project_bidders) >= 1) {
      $project_bidders = array_merge((array) json_decode($rfq_project_bidders), (array) json_decode($bid_doc_project_bidders));
    } else if (count($rfq_project_bidders) === 0 && count($bid_doc_project_bidders) >= 1) {
      $project_bidders = $bid_doc_project_bidders;
    } else if (count($rfq_project_bidders) >= 1 && count($bid_doc_project_bidders) === 0) {
      $project_bidders = $rfq_project_bidders;
    } else {
      $project_bidders = [];
    }
  } else if ($notice_type === "NOPD") {
    if ($filter === "all") {
      $rfq_project_bidders = $rfq_project_bidders
        ->where([['bid_status', 'non-responsive'], ['project_bidder_notices.date_released', 'like', $year . '%']])
        ->orWhere([['bid_status', 'non-responsive'], ['project_bidder_notices.date_released', null], ['twg_evaluations.post_qual_end', 'like', $year . '%']]);

      $bid_doc_project_bidders = $bid_doc_project_bidders
        ->where([['bid_status', 'non-responsive'], ['project_bidder_notices.date_released', 'like', $year . '%']])
        ->orWhere([['bid_status', 'non-responsive'], ['project_bidder_notices.date_released', null], ['twg_evaluations.post_qual_end', 'like', $year . '%']]);
    } else if ($filter === "for_preparation") {
      $rfq_project_bidders = $rfq_project_bidders
        ->where([['bid_status', 'non-responsive'], ['project_bidder_notices.date_released', null], ['twg_evaluations.post_qual_end', 'like', $year . '%']]);

      $bid_doc_project_bidders = $bid_doc_project_bidders
        ->where([['bid_status', 'non-responsive'], ['project_bidder_notices.date_released', null], ['twg_evaluations.post_qual_end', 'like', $year . '%']]);
    } else {
      $rfq_project_bidders = $rfq_project_bidders
        ->where([['bid_status', 'non-responsive'], ['project_bidder_notices.date_released', 'like', $year . '%']]);

      $bid_doc_project_bidders = $bid_doc_project_bidders
        ->where([['bid_status', 'non-responsive'], ['project_bidder_notices.date_released', 'like', $year . '%']]);
    }


    $rfq_project_bidders = $rfq_project_bidders
      ->leftJoin('project_bidder_notices', 'project_bidder_notices.project_bid', 'project_bidders.project_bid')
      ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->distinct()
      ->get();

    $bid_doc_project_bidders = $bid_doc_project_bidders
      ->leftJoin('project_bidder_notices', 'project_bidder_notices.project_bid', 'project_bidders.project_bid')
      ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->distinct()
      ->get();

    $project_bidders = [];

    if (count($rfq_project_bidders) >= 1 && count($bid_doc_project_bidders) >= 1) {
      $project_bidders = array_merge((array) json_decode($rfq_project_bidders), (array) json_decode($bid_doc_project_bidders));
    } else if (count($rfq_project_bidders) === 0 && count($bid_doc_project_bidders) >= 1) {
      $project_bidders = $bid_doc_project_bidders;
    } else if (count($rfq_project_bidders) >= 1 && count($bid_doc_project_bidders) === 0) {
      $project_bidders = $rfq_project_bidders;
    } else {
      $project_bidders = [];
    }
  } else if ($notice_type === "NOPQ") {
    if ($filter === "all") {
      $rfq_project_bidders = $rfq_project_bidders
        ->where([['bid_status', 'responsive'], ['project_bidder_notices.date_released', 'like', $year . '%'], ['project_plans.is_old', '<>', true]])
        ->orWhere([['bid_status', 'responsive'], ['project_bidder_notices.date_released', null], ['twg_evaluations.post_qual_end', 'like', $year . '%'], ['project_plans.is_old', '<>', true]]);


      $bid_doc_project_bidders = $bid_doc_project_bidders
        ->where([['bid_status', 'responsive'], ['project_bidder_notices.date_released', 'like', $year . '%']])
        ->orWhere([['bid_status', 'responsive'], ['project_bidder_notices.date_released', null], ['twg_evaluations.post_qual_end', 'like', $year . '%']]);
    } else if ($filter === "for_preparation") {
      $rfq_project_bidders = $rfq_project_bidders
        ->where([['bid_status', 'responsive'], ['project_bidder_notices.date_released', null], ['twg_evaluations.post_qual_end', 'like', $year . '%']]);

      $bid_doc_project_bidders = $bid_doc_project_bidders
        ->where([['bid_status', 'responsive'], ['project_bidder_notices.date_released', null], ['twg_evaluations.post_qual_end', 'like', $year . '%']]);
    } else {
      $rfq_project_bidders = $rfq_project_bidders
        ->where([['bid_status', 'responsive'], ['project_bidder_notices.date_released', 'like', $year . '%']]);

      $bid_doc_project_bidders = $bid_doc_project_bidders
        ->where([['bid_status', 'responsive'], ['project_bidder_notices.date_released', 'like', $year . '%']]);
    }


    $rfq_project_bidders = $rfq_project_bidders
      ->leftJoin('project_bidder_notices', 'project_bidder_notices.project_bid', 'project_bidders.project_bid')
      ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->distinct()
      ->get();

    $bid_doc_project_bidders = $bid_doc_project_bidders
      ->leftJoin('project_bidder_notices', 'project_bidder_notices.project_bid', 'project_bidders.project_bid')
      ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->distinct()
      ->get();

    $project_bidders = [];

    if (count($rfq_project_bidders) >= 1 && count($bid_doc_project_bidders) >= 1) {
      $project_bidders = array_merge((array) json_decode($rfq_project_bidders), (array) json_decode($bid_doc_project_bidders));
    } else if (count($rfq_project_bidders) === 0 && count($bid_doc_project_bidders) >= 1) {
      $project_bidders = $bid_doc_project_bidders;
    } else if (count($rfq_project_bidders) >= 1 && count($bid_doc_project_bidders) === 0) {
      $project_bidders = $rfq_project_bidders;
    } else {
      $project_bidders = [];
    }
  } else if ($notice_type === "NTLB") {
    if ($filter === "all") {
      $rfq_project_bidders = $rfq_project_bidders
        ->where([['bid_status', 'active'], ['project_bidder_notices.date_released', 'like', $year . '%'], ['procacts.post_qual', '<>', null]])
        ->orWhere([['bid_status', 'active'], ['project_bidder_notices.date_released', null], ['procacts.post_qual', 'like', $year . '%']]);


      $bid_doc_project_bidders = $bid_doc_project_bidders
        ->where([['bid_status', 'active'], ['project_bidder_notices.date_released', 'like', $year . '%'], ['procacts.post_qual', '<>', null]])
        ->orWhere([['bid_status', 'active'], ['project_bidder_notices.date_released', null], ['procacts.post_qual', 'like', $year . '%']]);
    } else if ($filter === "for_preparation") {
      $rfq_project_bidders = $rfq_project_bidders
        ->where([['bid_status', 'active'], ['project_bidder_notices.date_released', null], ['procacts.post_qual', 'like', $year . '%']]);

      $bid_doc_project_bidders = $bid_doc_project_bidders
        ->where([['bid_status', 'active'], ['project_bidder_notices.date_released', null], ['procacts.post_qual', 'like', $year . '%']]);
    } else {
      $rfq_project_bidders = $rfq_project_bidders
        ->where([['bid_status', 'active'], ['project_bidder_notices.date_released', 'like', $year . '%'], ['procacts.post_qual', '<>', null]]);
      $bid_doc_project_bidders = $bid_doc_project_bidders
        ->where([['bid_status', 'active'], ['project_bidder_notices.date_released', 'like', $year . '%'], ['procacts.post_qual', '<>', null]]);
    }


    $rfq_project_bidders = $rfq_project_bidders
      ->leftJoin('project_bidder_notices', 'project_bidder_notices.project_bid', 'project_bidders.project_bid')
      ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->distinct()
      ->get();

    $bid_doc_project_bidders = $bid_doc_project_bidders
      ->leftJoin('project_bidder_notices', 'project_bidder_notices.project_bid', 'project_bidders.project_bid')
      ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->distinct()
      ->get();

    $project_bidders = [];

    if (count($rfq_project_bidders) >= 1 && count($bid_doc_project_bidders) >= 1) {
      $project_bidders = array_merge((array) json_decode($rfq_project_bidders), (array) json_decode($bid_doc_project_bidders));
    } else if (count($rfq_project_bidders) === 0 && count($bid_doc_project_bidders) >= 1) {
      $project_bidders = $bid_doc_project_bidders;
    } else if (count($rfq_project_bidders) >= 1 && count($bid_doc_project_bidders) === 0) {
      $project_bidders = $rfq_project_bidders;
    } else {
      $project_bidders = [];
    }
  } else {
    return abort(404);
  }

  $array = [];

  foreach ($project_bidders as $bidder) {
    if ($bidder->notice_type != $notice_type && $bidder->notice_type != null) {
      // Check notices with same bid but not same type
      $data = ProjectBidderNotice::where([['project_bid', $bidder->project_bid], ["notice_type", $notice_type]])->first();
      if ($data == null) {
        $bidder->notice_type = null;
        $bidder->bac_id = null;
        $bidder->notice_date_released = null;
        $bidder->notice_date_received = null;
        $bidder->date_received_by_contractor = null;
        $bidder->notice_date_generated = null;
        $bidder->mr_due_date = null;
        $bidder->with_attachment = null;
        $bidder->notice_remarks = null;
        $array[] = $bidder;
      }
    } else {
      $array[] = $bidder;
    }
  }

  return $array;
}

function getOpeningNumber($procact_id)
{
  $activity = Procact::find($procact_id);
  $procacts_array = [];
  $procacts = ProjectTimeline::where([["bid_submission_start", $activity->open_bid], ["procact_mode_id", $activity->procact_mode_id]])
    ->join('procacts', 'procacts.procact_id', 'project_timelines.procact_id')
    ->orderBy("itb_arrangement")->get();
  $increment = 1;
  $number = 1;

  foreach ($procacts as $value) {
    if (in_array($value->procact_id, $procacts_array) === false) {
      if ($value->plan_cluster_id != null) {
        $clusters = Procact::where('plan_cluster_id', $value->plan_cluster_id)->get();
        foreach ($clusters as $cluster) {
          if ($cluster->procact_id === $procact_id) {
            $number = $increment;
            break;
          }
          array_push($procacts_array, $cluster->procact_id);
        }
      } else {
        if ($value->procact_id === $procact_id) {
          $number = $increment;
          break;
        }
        array_push($procacts_array, $value->procact_id);
      }
      $increment = $increment + 1;
    }
  }
  return $number;
}

function getInsufficientPerformanceBond($year, $count)
{
  $APP = new APP;

  // contract performance bond
  $data1 = Contract::where([
    ['performance_bond_expiration', '<>', null],
    ['additional_pb_id', null],
    ['notice_to_proceeds.duration_end_date', '<>', null],
  ])
    ->whereRaw('notice_to_proceeds.duration_end_date >contracts.performance_bond_expiration')
    ->select(
      'additional_performance_bonds.additional_pb_id',
      'contracts.contract_id',
      'project_plans.project_no',
      'project_plans.project_title',
      'contracts.performance_bond_posted as  additional_pb_date_issuance',
      'contracts.performance_bond_expiration as  additional_pb_expiration',
      'contracts.performance_bond_receive_date as  additional_pb_received_date',
      'contracts.performance_bond_remarks as  additional_pb_remarks',
      'notice_to_proceeds.duration_end_date',
      'contracts.project_bid_id'
    )
    ->leftJoin('additional_performance_bonds', 'additional_performance_bonds.contract_id', 'contracts.contract_id')
    ->join('project_bidders', 'project_bidders.project_bid', 'contracts.project_bid_id')
    ->join('notice_to_proceeds', 'project_bidders.project_bid', 'notice_to_proceeds.project_bid_id')
    ->join('project_plans', 'project_plans.project_bid_id', 'project_bidders.project_bid');

  $data2 = Contract::where([
    ['performance_bond_expiration', '<>', null],
    ['notice_to_proceeds.duration_end_date', '<>', null],
    ['additional_performance_bonds.additional_pb_status', 1],

  ])
    ->whereRaw('notice_to_proceeds.duration_end_date >additional_performance_bonds.additional_pb_expiration')
    ->select(
      'additional_performance_bonds.*',
      'contractors.contractor_id',
      'contractors.business_name',
      'contracts.contract_id',
      'project_plans.project_no',
      'project_plans.project_title',
      'notice_to_proceeds.duration_end_date',
      'contracts.project_bid_id',
      DB::raw("MAX('additional_pb_id')")
    )
    ->join('additional_performance_bonds', 'additional_performance_bonds.contract_id', 'contracts.contract_id')
    ->join('project_bidders', 'project_bidders.project_bid', 'contracts.project_bid_id')
    ->join('notice_to_proceeds', 'project_bidders.project_bid', 'notice_to_proceeds.project_bid_id')
    ->join('project_plans', 'project_plans.project_bid_id', 'project_bidders.project_bid')
    ->join('contractors', 'contractors.contractor_id', 'additional_performance_bonds.contractor_id')
    ->groupBy('contracts.contract_id');


  if ($count === true) {
    $data1 = $data1->count();
    $data2 = $data2->count();
    $data = $data1 + $data2;
  } else {
    $data1 = $data1->get()->toArray();
    $data_temp = [];
    foreach ($data1 as $value) {
      $bid = $APP->getBid($value['project_bid_id']);
      $value['contractor_id'] = $bid->contractor_id;
      $value['business_name'] = $bid->business_name;
      $data_temp[] = $value;
    }
    $data2 = $data2->get()->toArray();
    $data = array_merge($data_temp, $data2);
  }

  return $data;
}


function getIssued($id)
{
  $rfq_count = DB::table('rfq_projects')->where('procact_id', $id)->count();
  $bidding_count = DB::table('bid_doc_projects')->where('procact_id', $id)->count();
  $count = $rfq_count + $bidding_count;
  return $count;
}

function getReceived($id)
{
  $rfq_count = DB::table('rfq_projects')->where([['procact_id', $id], ['date_received', '<>', null]])->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')->count();
  $bidding_count = DB::table('bid_doc_projects')->where([['procact_id', $id], ['date_received', '<>', null]])->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')->count();
  $count = $rfq_count + $bidding_count;
  return $count;
}

function checkPassword($id, $password)
{
  $user = DB::table('users')->where('id', $id)->first();
  if (password_verify($password, $user->password)) {
    return true;
  } else {
    return false;
  }
}

function getUserLinks()
{
  $user_id = Auth::user()->id;
  $raw_links = UserLink::select('links.*')
    ->where([['user_id', $user_id], ['privilege', 'view'], ['link_type', 'sidebar']])
    ->join('link_privileges', 'user_links.link_privilege_id', 'link_privileges.id')
    ->join('links', 'link_privileges.link_id', 'links.id')
    ->orderBy('link_order', 'asc')
    ->get();
  $parents = [];

  $temp_parent = "";
  $links = [];
  foreach ($raw_links as $row) {
    if ($row->parent_name != null) {
      if ($temp_parent != $row->parent_name) {
        $temp_parent = $row->parent_name;
        if (in_array($temp_parent, $parents) === false) {
          $sublinks = UserLink::select('links.*')
            ->where([['user_id', $user_id], ['privilege', 'view'], ['link_type', 'sidebar'], ['parent_name', $row->parent_name]])
            ->join('link_privileges', 'user_links.link_privilege_id', 'link_privileges.id')
            ->join('links', 'link_privileges.link_id', 'links.id')
            ->orderBy('link_order', 'asc')
            ->get();

          array_push($parents, $temp_parent);

          $row->parent_id = preg_replace('/[^A-Za-z0-9\-]/', '', $row->parent_name);
          $row->sublinks = $sublinks;
          $links[] = $row;
        }
      }
    } else {
      $row->url = "{{route('" . $row->link_route . "')}}";
      $links[] = $row;
    }
  }

  return $links;
}

function getUserPrivilege()
{

  $user_id = Auth::user()->id;
  $url = url()->current();
  // $ip = request()->server('SERVER_ADDR');
  $ip = $_SERVER['HTTP_HOST'];
  $route = str_replace("http://" . $ip . "/", "", $url);
  $privileges = UserLink::select('link_privileges.privilege')
    ->where([['user_id', $user_id], ['link_route', $route]])
    ->join('link_privileges', 'user_links.link_privilege_id', 'link_privileges.id')
    ->join('links', 'link_privileges.link_id', 'links.id')
    ->get();


  $privileges_array = [];
  foreach ($privileges as $row) {
    $privileges_array[] = $row->privilege;
  }

  // if(in_array("view", $privileges_array)){
  return $privileges_array;
  // }
  // else{
  //   return abort(403,"Sorry, You don't have access to this feature.If you think this is an error. Please Contact Your System Administrator for more details");
  // }


}

function getUserPrivilegeByLink($route)
{

  $user_id = Auth::user()->id;
  if ($route === "post_qual_bidders") {
    $privileges = UserLink::select('link_privileges.privilege')
      ->where([['user_id', $user_id]])
      ->whereIn('link_route', ['post_qualification_to_verify', 'post_qualification'])
      ->join('link_privileges', 'user_links.link_privilege_id', 'link_privileges.id')
      ->join('links', 'link_privileges.link_id', 'links.id')
      ->get();
  } else {
    $privileges = UserLink::select('link_privileges.privilege')
      ->where([['user_id', $user_id], ['link_route', $route]])
      ->join('link_privileges', 'user_links.link_privilege_id', 'link_privileges.id')
      ->join('links', 'link_privileges.link_id', 'links.id')
      ->get();
  }

  $privileges_array = [];
  foreach ($privileges as $row) {
    $privileges_array[] = $row->privilege;
  }

  // if(in_array("view", $privileges_array)){
  return $privileges_array;
  // }
  // else{
  //   return abort(403,"Sorry, You don't have access to this feature.If you think this is an error. Please Contact Your System Administrator for more details.");
  // }
}

function checkUserAccess($access, $array)
{
  if (!in_array($access, $array)) {
    return abort(403, "Sorry! You don't have Access Privilege. Please Contact Your System Administrator");
  }
  return true;
}

function getBiddersDataFirst($procact_id, $status)
{
  $bidder_error = false;
  $status = explode(",", $status);
  $bidders = DB::table('rfq_projects')
    ->select(DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), 'twg_evaluations.twg_final_bid_evaluation', 'twg_evaluations.post_qual_start', 'twg_evaluations.post_qual_end', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), 'project_bidders.project_bid', 'rfqs.contractor_id', 'project_bidders.bid_status', 'rfq_projects.rfq_project_id', 'rfq_projects.detailed_bid_as_read', 'rfq_projects.detailed_bid_as_evaluated', 'rfqs.proposed_bid', 'rfqs.bid_as_evaluated', 'rfqs.discount', 'contractors.business_name', 'contractors.owner', 'contractors.address')
    ->where([['procacts.procact_id', $procact_id]])
    ->whereIn('project_bidders.bid_status', $status)
    ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
    ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
    ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
    ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
    ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
    ->orderByRaw('ISNULL(minimum_cost), minimum_cost ASC')
    ->first();

  if ($bidders === null) {
    $bidders = DB::table('bid_doc_projects')
      ->select(DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), 'twg_evaluations.twg_final_bid_evaluation', 'twg_evaluations.post_qual_start', 'twg_evaluations.post_qual_end', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated,twg_evaluations.detailed_bid_as_calculated) AS minimum_detailed_cost"), 'project_bidders.bid_status', 'bid_docs.contractor_id', 'project_bidders.project_bid', 'bid_doc_projects.bid_doc_project_id', 'bid_doc_projects.detailed_bid_as_read', 'bid_doc_projects.detailed_bid_as_evaluated', 'bid_docs.proposed_bid', 'bid_docs.bid_as_evaluated', 'bid_docs.discount', 'contractors.business_name', 'contractors.owner', 'contractors.address')
      ->where([['procacts.procact_id', $procact_id]])
      ->whereIn('project_bidders.bid_status', $status)
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->orderByRaw('ISNULL(minimum_cost), minimum_cost ASC')
      ->first();
  }

  return $bidders;
}


function transferDataToSAPP($procact_id)
{
  // dd($procact_id);
  $org_plan = DB::table('project_plans')
    ->where('procact_id', $procact_id)
    ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')->first();

  $last_child = false;

  $child = DB::table('project_plans')
    ->where('parent_id', $org_plan->plan_id)
    ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')->first();


  if ($child === null) {

    $child = DB::table('project_plans')
      ->where([['project_title', $org_plan->project_title], ['project_no', $org_plan->project_no], ['project_plans.plan_id', '>', $org_plan->plan_id]])
      ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
      ->orderBy('project_plans.plan_id', 'desc')
      ->first();

    if ($child === null) {
      if ($org_plan->parent_id != null) {
        $parent_count = DB::table('project_plans')
          ->select("project_plans.*")
          ->where([['project_plans.project_no', $org_plan->project_no], ['project_plans.plan_id', '<>', $org_plan->plan_id], ['project_bid_id', '<>', null]])
          ->orWhere([['project_plans.project_title', $org_plan->project_title], ['project_plans.plan_id', '<>', $org_plan->plan_id], ['project_bid_id', '<>', null]])
          ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
          ->distinct()
          ->get();

        // dd($parent_count);
        if (count($parent_count) > 1) {
          dd("multiple fix manually");
        }

        $parent = DB::table('project_plans')
          ->where([['project_plans.project_no', $org_plan->project_no], ['project_plans.plan_id', '<>', $org_plan->plan_id], ['project_bid_id', '<>', null]])
          ->orWhere([['project_plans.project_title', $org_plan->project_title], ['project_plans.plan_id', '<>', $org_plan->plan_id], ['project_bid_id', '<>', null]])
          ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')
          ->first();


        // dd($parent);

        $parent_update = DB::table('project_plans')
          ->where('project_plans.plan_id', $org_plan->parent_id)
          ->update([
            'is_old' => false,
            "parent_id" => $org_plan->plan_id,
            "project_type" => $org_plan->project_type,
            "status" => $org_plan->status,
            "date_added" => $org_plan->date_added,
            "app_group_no" => $org_plan->app_group_no,
            "date_added" => $org_plan->date_added,
            "account_code" => $org_plan->account_code,
            "projtype_id" => $org_plan->projtype_id,

          ]);

        $child_update = DB::table('project_plans')
          ->where('project_plans.plan_id', $org_plan->plan_id)
          ->update([
            'is_old' => true,
            "parent_id" => $parent->project_type,
            'project_bid_id' => null,
            "project_type" => $parent->project_type,
            "status" => $parent->status,
            "date_added" => $parent->date_added,
            "app_group_no" => $parent->app_group_no,
            "date_added" => $parent->date_added,
            "account_code" => $parent->account_code,
            "projtype_id" => $parent->projtype_id,

          ]);
      } else {
        dd("unknown scenario");
      }

      // dd("missing_child ".$org_plan->plan_id);
    }
  } else {
    $first_child = $child;
    while ($last_child === false) {
      $child = DB::table('project_plans')
        ->where('parent_id', $first_child->plan_id)
        ->join('procacts', 'procacts.plan_id', 'project_plans.plan_id')->first();
      if ($child === null) {
        $last_child = true;
      } else {
        $first_child = $child;
      }
    }
  }
}


function processRequestForExtension($array)
{
  $APP = new APP;
  $bids_array = [];
  $processed_bids = [];
  foreach ($array as $key => $value) {
    if (in_array($value->project_bid, $bids_array) === false) {
      $bids_format = (object)[];
      $clusters = $APP->getClusterBids($value->project_bid);
      $bid = $APP->getBid($value->project_bid);
      $bidders_rank = $APP->getBiddersData($bid->procact_id, 'active,responsive,non-responsive');

      if (count($bidders_rank) == 1) {
        if ($bid->procact_mode_id == 1) {
          $rank = "Lone Bidder";
        } else {
          $rank = "Lone Quotation";
        }
      } else {
        foreach ($bidders_rank as $key_rank => $bidder_rank) {
          if ($bidder_rank->project_bid === $bid->project_bid) {
            $number = $key_rank + 1;
            if ($bid->procact_mode_id == 1) {
              $rank = $number . date("S", mktime(0, 0, 0, 0, $number, 0)) . " LCB";
            } else {
              $rank = $number . date("S", mktime(0, 0, 0, 0, $number, 0)) . " LCPQ";
            }
            break;
          }
        }
      }
      $title = "";
      $total = 0;
      $project_cost = "";
      if (count($clusters) > 1) {
        $letter = 'A';
        foreach ($clusters as $cluster) {
          array_push($bids_array, $cluster->project_bid);
          $temp = $letter . '. ' . strtoupper(strtolower($cluster->project_title));
          $title = $title . "   " . $temp;
          $total = $total + $cluster->project_cost;
          $project_cost = $project_cost . " PHP " . number_format((float)$cluster->project_cost, 2, '.', ',');
          $letter = ++$letter;
        }
        $project_cost = $project_cost . "= PHP " . number_format((float)$total, 2, '.', ',');
      } else {
        $title = strtoupper(strtolower($clusters[0]->project_title));
        $project_cost = "PHP " . number_format((float)$bid->project_cost, 2, '.', ',');
      }
      $location = strtoupper(strtolower($clusters[0]->municipality_name)) . ",Benguet";
      $bids_format->title = str_replace("&", "&amp;", $title);
      $bids_format->rank = $rank;
      $bids_format->business_name = str_replace("&", "&amp;", strtoupper(strtolower($bid->business_name)));
      $bids_format->date_opened = $clusters[0]->open_bid;
      $bids_format->date_formatted = date("F d, Y", strtotime($clusters[0]->open_bid));
      $bids_format->opening_number = getOpeningNumber($clusters[0]->procact_id);
      $bids_format->procact_mode_id = $clusters[0]->procact_mode_id;
      $bids_format->location = $location;
      $bids_format->project_cost = $project_cost;
      array_push($processed_bids, $bids_format);
    }
  }

  return $processed_bids;
}




function calculateDate($date_start, $days, $date_type)
{
  $due_date = null;
  $counter = 0;
  if ($date_type === "Working Days") {
    $due_date = strtotime($date_start);
    while ($counter < $days) {
      $due_date = strtotime("+1 day", $due_date);
      if (Date('l', $due_date) != "Saturday" && Date('l', $due_date) != "Sunday") {
        $holiday = Holiday::where('holiday_date', Date('Y-m-d', $due_date))->count();
        if ($holiday === 0) {
          $counter = $counter + 1;
        }
      }
    }
  }
  if ($date_type === "Calendar Days") {
    $due_date = strtotime("+" . $days . " day", $date_start);
  }

  return Date('m/d/Y', $due_date);
}

function calculateDateDiff($date1, $date2)
{
  $date1 = new DateTime($date1);
  $date2 = new DateTime($date2);
  $interval = $date1->diff($date2);
  return $interval->days;
}

function getRank($procact_id, $project_bid_id)
{

  $APP = new APP;
  $bidders = $APP->getBiddersData($procact_id, 'responsive,active,non-responsive,disapproved,withdrawn');

  $rank = 1;
  foreach ($bidders as $bidder) {
    if ($bidder->project_bid === $project_bid_id) {
      break;
    } else {
      $rank = $rank + 1;
    }
  }
  if (count($bidders) === 1) {
    return "LONE BIDDER";
  } else {
    return $rank . date("S", mktime(0, 0, 0, 0, $rank, 0)) . " LCB";
  }
}




function getResolutionBidders($resolutions)
{
  $resolutions_processed = [];
  foreach ($resolutions  as $row) {
    $row->contractor = "one";
    $data = DB::table('resolution_mr_project_bids')->select('project_bid_id')->where('resolution_id', $row->resolution_id)
      ->join('motion_for_reconsideration_project_bid', 'resolution_mr_project_bids.mr_project_bid_id', 'motion_for_reconsideration_project_bid.mr_project_bid_id')->first();
    $bid = getBid($data->project_bid_id);
    $row->bidder = $bid->business_name;
    array_push($resolutions_processed, $row);
  }
  return $resolutions_processed;
}

function getBid($bid_id)
{

  $project_bid = DB::table('procacts')
    ->select('rfqs.*', 'procacts.*', 'funds.*', 'contractors.*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_plans.*', 'procacts.plan_cluster_id', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), 'twg_evaluations.twg_final_bid_evaluation', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost,twg_evaluations.detailed_bid_as_calculated"), 'project_bidders.project_bid', 'project_bidders.bid_status', 'rfq_projects.rfq_project_id', 'rfq_projects.detailed_bid_as_read', 'rfq_projects.detailed_bid_as_evaluated', 'rfqs.proposed_bid', 'rfqs.bid_as_evaluated', 'rfqs.discount')
    ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
    ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
    ->join('rfq_projects', 'rfq_projects.procact_id', 'procacts.procact_id')
    ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
    ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
    ->where('project_bidders.project_bid', $bid_id)
    ->join('contractors', 'contractors.contractor_id', 'rfqs.contractor_id')
    ->leftJoin('twg_evaluations', 'twg_evaluations.project_bid', 'project_bidders.project_bid')
    ->orderBy('procacts.itb_arrangement', 'asc')
    ->first();

  if ($project_bid == null) {
    $project_bid = DB::table('procacts')
      ->select('bid_docs.*', 'procacts.*', 'funds.*', 'contractors.*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_plans.*', 'procacts.plan_cluster_id', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), 'twg_evaluations.twg_final_bid_evaluation', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated,twg_evaluations.detailed_bid_as_calculated) AS minimum_detailed_cost"), 'project_bidders.bid_status', 'project_bidders.project_bid', 'bid_doc_projects.bid_doc_project_id', 'bid_doc_projects.detailed_bid_as_read', 'bid_doc_projects.detailed_bid_as_evaluated', 'bid_docs.proposed_bid', 'bid_docs.bid_as_evaluated', 'bid_docs.discount')
      ->join('project_plans', 'project_plans.plan_id', 'procacts.plan_id')
      ->join('bid_doc_projects', 'bid_doc_projects.procact_id', 'procacts.procact_id')
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->where('project_bidders.project_bid', $bid_id)
      ->join('contractors', 'contractors.contractor_id', 'bid_docs.contractor_id')
      ->leftJoin('twg_evaluations', 'twg_evaluations.project_bid', 'project_bidders.project_bid')
      ->orderBy('procacts.itb_arrangement', 'asc')
      ->first();
  }

  return $project_bid;
}
