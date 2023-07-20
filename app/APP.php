<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\LCEEvaluation;

class APP extends Model
{
  // get APP for view
  public function getAPP($year, $project_type, $status, $mode, $municipality, $fund_category, $type, $account_classification, $month, $pow, $sort, $filter, $count)
  {

    $query = DB::table('project_plans')
      ->select(
        '*',
        'childproject.plan_id as child_id',
        'project_plans.*',
        'project_plans.status as project_status',
        'procacts.post_qual as procact_post_qual',
        'procacts.advertisement as procact_advertisement',
        DB::raw('CONCAT(DATE_FORMAT(project_timelines.advertisement_start,"%b %d,%Y"),"-",DATE_FORMAT(project_timelines.advertisement_end,"%b %d,%Y")) AS fullAdvertisement'),
        DB::raw('CONCAT(DATE_FORMAT(project_timelines.pre_bid_start,"%b %d,%Y"),"-",DATE_FORMAT(project_timelines.pre_bid_end,"%b %d,%Y")) AS fullPrebid'),
        DB::raw('CONCAT(DATE_FORMAT(project_timelines.bid_submission_start,"%b %d,%Y"),"-",DATE_FORMAT(project_timelines.bid_submission_end,"%b %d,%Y")) AS fullOpening'),
        DB::raw('CONCAT(DATE_FORMAT(project_timelines.bid_evaluation_start,"%b %d,%Y"),"-",DATE_FORMAT(project_timelines.bid_evaluation_end,"%b %d,%Y")) AS fullBidEvalutation'),
        DB::raw('CONCAT(DATE_FORMAT(project_timelines.post_qualification_start,"%b %d,%Y"),"-",DATE_FORMAT(project_timelines.post_qualification_end,"%b %d,%Y")) AS fullPostQualification'),
        DB::raw('CONCAT(DATE_FORMAT(project_timelines.award_notice_start,"%b %d,%Y"),"-",DATE_FORMAT(project_timelines.award_notice_end,"%b %d,%Y")) AS fullNOA'),
        DB::raw('CONCAT(DATE_FORMAT(project_timelines.authority_approval_start,"%b %d,%Y"),"-",DATE_FORMAT(project_timelines.authority_approval_end,"%b %d,%Y")) AS fullApproval'),
        DB::raw('CONCAT(DATE_FORMAT(project_timelines.contract_signing_start,"%b %d,%Y"),"-",DATE_FORMAT(project_timelines.contract_signing_end,"%b %d,%Y")) AS fullContract'),
        DB::raw('CONCAT(DATE_FORMAT(project_timelines.proceed_notice_start,"%b %d,%Y"),"-",DATE_FORMAT(project_timelines.proceed_notice_end,"%b %d,%Y")) AS fullNTP'),
        'municipalities.*'
      );

    // year

    if ($year != null) {
      $query = $query->where('project_plans.project_year', $year);
    }

    // project type
    if ($project_type != null) {
      $query = $query->where('project_plans.project_type', $project_type);
    }

    // status
    if ($status != null) {
      if ($status === 'all_ongoing') {
        $query = $query->whereIn('project_plans.status', ['onprocess'])->where([['project_timelines.timeline_status', 'set'], ['procacts.post_qual', null]]);
      } else if ($status === "all_schedule") {
        $query = $query->whereIn('project_plans.status', ['onprocess', 'for_rebid', 'pending'])->where('project_plans.is_old', false);
      } else if ($status === "all_completed") {
        // $query=$query->whereIn('project_plans.status', ['completed']);
        // $available_months=[];
        // $month_now=(int)date('m');
        // $month_number=$month_now-6+1;
        // $start_month=date('Y-m-d',strtotime(date('Y').'-'.$month_number.'-01'));
        $query = $query->where('project_plans.abc_post_date', 'like', $year . '%')->where([['project_plans.is_old', '<>', true], ['project_activity_status.main_status', 'completed'], ['project_plans.project_bid_id', '<>', null]]);
        // $query=$query->where('project_plans.abc_post_date','like',$year.'%')->where([['project_plans.is_old','<>',true],['project_plans.project_bid_id','<>',null]]);

      } else if ($status === "with_attachment") {
        $query = $query->where('project_plans.with_attachment', true);
      } else if ($status === "without_attachment") {
        $query = $query->where('project_plans.with_attachment', false);
      } else if ($status === "with_itbrfq_attachment") {
        $query = $query->where([['procacts.itbrfq_attachment', true], ['project_timelines.timeline_status', 'set']]);
      } else if ($status === "without_itbrfq_attachment") {
        $query = $query->where([['procacts.itbrfq_attachment', false], ['project_timelines.timeline_status', 'set']]);
      } else if ($status === "with_or_without_itbrfq_attachment") {
        $query = $query->where([['project_timelines.timeline_status', 'set']]);
      } else if ($status === "new") {
        // $query=$query->where('project_plans.is_old', false);
      } else if ($status === "for_review") {
        $query = $query->whereIn('project_plans.status', ['for_review'])->where(['bid_submission_start']);
      } else if ($status === 'for_rebid') {
        $query = $query->whereIn('project_plans.status', ['for_rebid'])->where('project_timelines.timeline_status', 'pending');
      } else if ($status === 'reverted') {
        $query = $query->where('project_plans.status', 'reverted');
      } else if ($status === 'terminated') {
        $query = $query->where('project_activity_status.main_status', 'terminated');
      } else if ($status === 'unprocured_projects') {
        $year = date('Y');
        $available_months = [];
        $month_now = (int)date('m');
        if ($month_now < 6) {
          $year = $year - 1;
          $month_now = $month_now + 12;
        }

        $month_number = $month_now - 6 + 1;
        $start_month = date('Y-m-d', strtotime($year . '-' . ($month_number+2) . '-01'));
        $query = $query->where('project_plans.abc_post_date', '<', $start_month)->where([['child.plan_id', null], ['project_plans.is_old', '<>', true], ['procacts.advertisement', null], ['project_plans.project_bid_id', null], ['project_plans.status', 'pending']]);
      } else {
        $query = $query->where('project_plans.status', $status);
      }
    }
    //mode
    if ($mode != null) {
      if ($mode === 3) {
        $query = $query->whereIn('procacts.procact_mode_id', [3]);
      } else if ($mode === 2) {
        $query = $query->whereIn('procacts.procact_mode_id', [2]);
      } else {
        $query = $query->where('procacts.procact_mode_id', $mode);
      }
    }


    // $municipality
    if ($municipality != null) {
      $query = $query->where('project_plans.municipalilty_id', $municipality);
    }

    // pow
    if ($pow === "false" || $pow === "true") {
      if ($pow === "false") {
        $boolean = false;
      } else {
        $boolean = true;
      }
      $query = $query->where([['project_plans.pow_ready', $boolean]]);
    }

    // source
    if ($fund_category != null) {
      $query = $query->where('fund_category.fund_category_id', $fund_category);
    }

    // type
    if ($type != null) {
      $query = $query->where('project_plans.projtype_id', $type);
    }

    if ($filter != null) {
      $query = $query->where($filter);
    }

    // account classification
    if ($account_classification != null) {
      $query = $query->where('project_plans.account_id', $account_classification);
    }

    // month
    if ($month != null) {
      $monthadded = date("Y-m", strtotime('01-' . $month)) . '%';
      $query = $query->where('project_plans.date_added', 'like', $monthadded);
    }




    // join
    if ($status === "unprocured_projects") {
      $query->leftJoin('project_plans as child', 'project_plans.plan_id', 'child.parent_id');
    }
    $query->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id');
    $query->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id');
    $query->join('projtypes', 'project_plans.projtype_id', 'projtypes.projtype_id');
    $query->join('funds', 'project_plans.fund_id', 'funds.fund_id');
    $query->leftJoin('sectors', 'project_plans.sector_id', 'sectors.sector_id');
    $query->join('fund_category', 'fund_category.fund_category_id', 'funds.fund_category_id');
    $query->join('account_classifications', 'project_plans.account_id', 'account_classifications.account_id');
    if ($status == 'with_itbrfq_attachment' || $status == 'without_itbrfq_attachment' || $status == 'with_or_without_itbrfq_attachment') {
      $query->join('procacts', 'project_plans.plan_id', 'procacts.plan_id');
    } else if ($status === 'terminated') {
      $query->join('procacts', 'project_plans.plan_id', 'procacts.plan_id');
    } else {
      $query->join('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id');
    }
    $query->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id');
    $query->leftJoin('project_plans as childproject', 'project_plans.plan_id', 'childproject.parent_id');
    $query->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id');
    $query->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id');

    // sort
    if ($sort != null) {
      foreach ($sort as $key => $value) {
        $query = $query->orderBy($value['column'], $value['sorting']);
      }
    }



    // dd($query->count());

    if ($count === true) {
      $query = $query->count();
    } else {
      $query = $query->get();
    }

    return $query;
  }

  public function computeFee($abc)
  {
    if ($abc < 500000) {
      $fee = 500;
    } else if ($abc > 500000 && $abc <= 1000000) {
      $fee = 1000;
    } else if ($abc > 1000000 && $abc <= 5000000) {
      $fee = 5000;
    } else if ($abc > 5000000 && $abc <= 10000000) {
      $fee = 10000;
    } else if ($abc > 10000000 && $abc <= 50000000) {
      $fee = 25000;
    } else if ($abc > 50000000 && $abc <= 500000000) {
      $fee = 50000;
    } else {
      $fee = 75000;
    }

    return $fee;
  }

  public function getIncomingEvents($procurement_activity)
  {

    $events = DB::table('project_timelines')
      // ->whereIn('project_plans.status', ['onprocess', 'for_rebid'])
      ->join('procacts', 'procacts.procact_id', 'project_timelines.procact_id')
      ->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id');
    $date = date('Y-m-d');
    if ($procurement_activity === 'advertisement') {
      $process = 'Advertisement/Posting';
      $events = $events->select('project_plans.plan_id', 'project_plans.project_no', 'project_plans.project_title', 'project_timelines.advertisement_start as start_date', 'project_timelines.advertisement_end as end_date')
        ->where([['project_timelines.advertisement_start', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->orWhere([['project_timelines.advertisement_end', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->get();
    } else if ($procurement_activity === 'pre_bid') {
      $process = 'Pre-bid';
      $events = $events->select('project_plans.plan_id', 'project_plans.project_no', 'project_plans.project_title', 'project_timelines.pre_bid_start as start_date', 'project_timelines.pre_bid_end as end_date')
        ->where([['project_timelines.pre_bid_start', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->orWhere([['project_timelines.pre_bid_end', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->get();
    } else if ($procurement_activity === 'submission') {
      $process = 'Bid Submission/Opening';
      $events = $events->select('project_plans.plan_id', 'project_plans.project_no', 'project_plans.project_title', 'project_timelines.bid_submission_start as start_date', 'project_timelines.bid_submission_end as end_date')
        ->where([['project_timelines.bid_submission_start', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->orWhere([['project_timelines.bid_submission_end', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->get();
    } else if ($procurement_activity === 'bid_evaluation') {
      $process = 'Bid Evaluation';
      $events = $events->select('project_plans.plan_id', 'project_plans.project_no', 'project_plans.project_title', 'project_timelines.bid_evaluation_start as start_date', 'project_timelines.bid_evaluation_start as end_date')
        ->where([['project_timelines.bid_evaluation_start', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->orWhere([['project_timelines.bid_evaluation_end', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->get();
    } else if ($procurement_activity === 'post_qualification') {
      $process = 'Post Qualification';
      $events = $events->select('project_plans.plan_id', 'project_plans.project_no', 'project_plans.project_title', 'project_timelines.post_qualification_start as start_date', 'project_timelines.post_qualification_end as end_date')
        ->where([['project_timelines.post_qualification_start', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])
        ->orWhere([['project_timelines.post_qualification_end', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->get();
    } else if ($procurement_activity === 'notice_of_award') {
      $process = 'Notice of Award';
      $events = $events->select('project_plans.plan_id', 'project_plans.project_no', 'project_plans.project_title', 'project_timelines.award_notice_start as start_date', 'project_timelines.award_notice_end as end_date')
        ->where([['project_timelines.award_notice_start', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->orWhere([['project_timelines.award_notice_end', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->get();
    } else if ($procurement_activity === 'contract_signing') {
      $process = 'Contract Signing/preparation';
      $events = $events->select('project_plans.plan_id', 'project_plans.project_no', 'project_plans.project_title', 'project_timelines.contract_signing_start as start_date', 'project_timelines.contract_signing_end as end_date')
        ->where([['project_timelines.contract_signing_start', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->orWhere([['project_timelines.contract_signing_end', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->get();
    } else if ($procurement_activity === 'authority_approval') {
      $process = 'Approval by Higher Authority';
      $events = $events->select('project_plans.plan_id', 'project_plans.project_no', 'project_plans.project_title', 'project_timelines.authority_approval_start as start_date', 'project_timelines.authority_approval_end as end_date')
        ->where([['project_timelines.authority_approval_start', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->orWhere([['project_timelines.authority_approval_end', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->get();
    } else if ($procurement_activity === 'notice_to_proceed') {
      $process = 'Notice to Proceed';
      $events = $events->select('project_plans.plan_id', 'project_plans.project_no', 'project_plans.project_title', 'project_timelines.proceed_notice_start as start_date', 'project_timelines.proceed_notice_end as end_date')
        ->where([['project_timelines.proceed_notice_start', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->orWhere([['project_timelines.proceed_notice_end', '>=', $date], ['project_plans.is_old', false], ['procacts.is_inactive', 0]])->get();
    } else {
    }

    foreach ($events as $event) {
      $event->process = $process;
    }

    return $events;
  }


  public function getSpecificProcurementActivity($procurement_activity, $year)
  {
    $tomorrow = date('Y-m-d', strtotime(Date('Y-m-d') . ' + 10 days'));
    $project_plans = DB::table('project_plans')
      ->select('project_plans.*', 'procacts.*', 'barangays.barangay_name', 'municipalities.municipality_name', 'procurement_modes.mode', 'funds.source', 'project_timelines.*', 'project_activity_status.main_status');

    if ($year != null && $procurement_activity !== "pending_rdf") {
      $project_plans = $project_plans->where('project_plans.project_year', $year);
    }

    $project_plans = $project_plans
      // ->where('project_plans.is_old',false)
      ->orderBy('project_plans.project_year', 'desc')
      ->orderBy('procacts.open_bid', 'asc')
      ->orderBy('project_plans.mode_id', 'asc')
      ->orderBy('municipalities.municipality_name', 'asc')
      ->orderBy('project_plans.current_cluster', 'asc')
      ->orderBy('project_plans.project_cost', 'desc');

    if ($procurement_activity === 'pre_procurement') {
      $project_plans = $project_plans->where([['project_activity_status.main_status', 'pending'], ['project_activity_status.pre_proc', 'pending'], ['project_timelines.timeline_status', 'set']]);
    } else if ($procurement_activity === 'advertisement_posting') {
      $project_plans = $project_plans->where([['project_activity_status.main_status', 'pending'], ['project_activity_status.advertisement', 'pending'], ['project_timelines.timeline_status', 'set']])->whereIn('project_activity_status.pre_proc', ['not_needed', 'finished']);
    } else if ($procurement_activity === 'pre_bid') {
      $project_plans = $project_plans->where([['project_activity_status.main_status', 'pending'], ['project_activity_status.advertisement', 'finished'], ['project_activity_status.pre_bid', 'pending'], ['project_timelines.timeline_status', 'set']]);
    } else if ($procurement_activity === 'submission_opening') {
      $project_plans = $project_plans->where([['project_activity_status.main_status', 'pending'], ['project_activity_status.open_bid', 'pending'], ['project_timelines.timeline_status', 'set'], ['project_activity_status.pre_bid', 'finished']]);
      $project_plans = $project_plans->orWhere([['project_activity_status.main_status', 'pending'], ['project_activity_status.open_bid', 'pending'], ['project_timelines.timeline_status', 'set'], ['project_activity_status.pre_bid', 'not_needed'], ['project_activity_status.advertisement', 'finished']]);
    } else if ($procurement_activity === 'bid_evaluation') {
      $project_plans = $project_plans->where([['project_activity_status.main_status', 'pending'], ['project_activity_status.open_bid', 'finished'], ['project_activity_status.bid_evaluation', 'pending'], ['project_timelines.timeline_status', 'set']]);
    } else if ($procurement_activity === 'post_qualification') {
      $project_plans = $project_plans
        ->where([
          ['project_activity_status.main_status', 'pending'],
          ['project_activity_status.bid_evaluation', 'finished'],
          ['project_plans.project_bid_id', null],
          ['project_timelines.timeline_status', 'set']
        ])
        ->orderBy('project_timelines.post_qualification_end');
    } else if ($procurement_activity === 'post_qual_to_verify') {
      $project_plans = $project_plans->where([['project_activity_status.main_status', 'pending'], ['project_activity_status.bid_evaluation', 'finished'], ['project_timelines.timeline_status', 'set']])->orderBy('project_timelines.post_qualification_end');
    } else if ($procurement_activity === 'notice_of_award') {
      $project_plans = $project_plans->where([['project_activity_status.main_status', 'pending'], ['project_activity_status.post_qual', 'finished'], ['project_activity_status.award_notice', 'pending'], ['project_timelines.timeline_status', 'set']]);
    } else if ($procurement_activity === 'contract_preparation_signing') {
      $project_plans = $project_plans->where([['project_activity_status.main_status', 'pending'], ['project_activity_status.award_notice', 'finished'], ['project_activity_status.contract_signing', 'pending'], ['project_timelines.timeline_status', 'set']]);
    } else if ($procurement_activity === 'approval_by_higher_authority') {
      $project_plans = $project_plans->where([['project_activity_status.main_status', 'pending'], ['project_activity_status.contract_signing', 'finished'], ['project_activity_status.authority_approval', 'pending'], ['project_timelines.timeline_status', 'set']]);
    } else if ($procurement_activity === 'notice_to_proceed') {
      $project_plans = $project_plans->where([['project_activity_status.main_status', 'pending'], ['project_activity_status.authority_approval', 'finished'], ['project_activity_status.proceed_notice', 'pending'], ['project_timelines.timeline_status', 'set']]);
      $project_plans = $project_plans->orWhere([['project_activity_status.main_status', 'pending'], ['project_activity_status.contract_signing', 'finished'], ['project_activity_status.authority_approval', 'not_needed'], ['project_activity_status.proceed_notice', 'pending'], ['project_timelines.timeline_status', 'set']]);
    } else if ($procurement_activity === 'projects_without_resolution') {
      $project_plans = $project_plans->where([['project_activity_status.post_qual', 'finished'], ['project_activity_status.award_notice', 'pending']])
        ->whereNull('resolution_projects.procact_id');
    } else if ($procurement_activity === 'projects_without_bidders') {
      $project_plans = $project_plans->where([['project_timelines.timeline_status', 'set'], ['procacts.open_bid', '<>', null], ['procacts.open_bid', '<', date('Y-m-d', strtotime('+1 day'))]])
        ->whereNull('resolution_projects.procact_id');
    } else if ($procurement_activity === 'projects_with_resolution_pending_noa') {
      $project_plans = $project_plans->where([['project_plans.is_old', ''], ['project_timelines.timeline_status', 'set'], ['resolution_projects.resolution_project_id', '<>', null], ['resolution_projects.cancelled', null], ['procacts.contract_signing', null], ['procacts.open_bid', '<>', null], ['resolutions.type', 'RRA'], ['procacts.open_bid', '<', date('Y-m-d', strtotime('+1 day'))]]);
    } else if ($procurement_activity === 'pending_rdf') {
      $project_plans = $project_plans->where([['project_timelines.timeline_status', 'set'], ['procacts.open_bid', '<>', null], ['procacts.open_bid', '<', date('Y-m-d', strtotime('+1 day'))]])
        ->whereNull('resolution_projects.procact_id');
    } else if ($procurement_activity === 'projects_for_review') {
      $project_plans = $project_plans->where([['project_plans.re_bid_count', '>', 2], ['project_activity_status.main_status', 'pending'], ['project_timelines.timeline_status', 'set'], ['procacts.open_bid', '<>', null], ['procacts.open_bid', '<', $tomorrow]])
        ->whereNull('resolution_projects.procact_id');
    } else if ($procurement_activity === 'projects_for_rebid') {
      $project_plans = $project_plans->whereRaw('project_plans.re_bid_count <=  3 AND project_plans.is_old != true AND project_timelines.timeline_status="set" AND procacts.open_bid IS NOT NULL AND r1.type IN ("RRRC", "RDF") AND project_activity_status.main_status IN  ("pending", "terminated", "review", "cancelled")')
        ->orWhereRaw('project_plans.re_bid_count <=  3 AND project_plans.is_old != true AND project_timelines.timeline_status="set" AND procacts.open_bid IS NOT NULL AND r2.type IN ("RRRC", "RDF") AND project_activity_status.main_status IN  ("pending", "terminated", "review", "cancelled")');
    } else if ($procurement_activity === 'projects_to_reactivate') {
      $project_plans = $project_plans->where([['project_plans.re_bid_count', '>', 0], ['project_timelines.timeline_status', '<>', 'set']]);
    } else if ($procurement_activity === 'projects_with_bidders') {
      $project_plans = $project_plans->where([['project_activity_status.main_status', 'pending'], ['project_timelines.timeline_status', 'set']]);
    } else if ($procurement_activity === 'project_for_notices') {
      $project_plans = $project_plans->where([['project_timelines.timeline_status', 'set'], ['project_activity_status.advertisement', 'finished']]);
    } else if ($procurement_activity === 'for_notice_of_award') {
      // $project_plans=$project_plans->where([['project_activity_status.post_qual','finished'],['project_timelines.timeline_status','set'],['project_activity_status.contract_signing','pending']]);
      $project_plans = $project_plans->where([['project_activity_status.post_qual', 'finished'], ['project_timelines.timeline_status', 'set'], ['resolutions.type', 'RRA']]);
    } else if ($procurement_activity === 'performance_bond') {
      $project_plans = $project_plans->where([['project_activity_status.award_notice', 'finished'], ['project_timelines.timeline_status', 'set']]);
      // $project_plans=$project_plans->where([['project_activity_status.main_status','pending'],['project_activity_status.award_notice','finished'],['project_activity_status.contract_signing','pending'],['project_timelines.timeline_status','set']]);
      // $project_plans=$project_plans->where([['project_activity_status.award_notice','finished'],['project_timelines.timeline_status','set']])
      // ->wherein('project_activity_status.contract_signing',['pending','finished']);

    } else if ($procurement_activity === 'chsp') {
      $project_plans = $project_plans->where([['project_activity_status.award_notice', 'finished'], ['project_timelines.timeline_status', 'set'], ['project_activity_status.award_notice', 'finished']]);
      // $project_plans=$project_plans->where([['project_activity_status.main_status','pending'],['project_activity_status.award_notice','finished'],['project_activity_status.contract_signing','pending'],['project_timelines.timeline_status','set']]);
      // $project_plans=$project_plans->where([['project_activity_status.award_notice','finished'],['project_timelines.timeline_status','set']])
      // ->wherein('project_activity_status.contract_signing',['pending','finished']);

    } else if ($procurement_activity === 'for_contract_generation') {
      // $project_plans=$project_plans->where([['project_activity_status.main_status','pending'],['project_activity_status.award_notice','finished'],['project_timelines.timeline_status','set']]);
      $project_plans = $project_plans->where([['project_activity_status.post_qual', 'finished'], ['project_timelines.timeline_status', 'set']]);
    } else if ($procurement_activity === 'for_notice_to_proceed') {
      $project_plans = $project_plans->where([['project_activity_status.post_qual', 'finished'], ['project_timelines.timeline_status', 'set']]);
      // $project_plans=$project_plans->orWhere([['project_activity_status.contract_signing','finished'],['project_activity_status.authority_approval','not_needed'],['project_timelines.timeline_status','set']]);
    } else {
    }

    // another validation for RDF
    if ($procurement_activity === 'projects_without_bidders' || $procurement_activity === 'pending_rdf' || $procurement_activity === "pending_rdf_count" || $procurement_activity === 'projects_with_resolution_pending_noa') {
      $project_plans = $project_plans->join('procacts', 'project_plans.plan_id', 'procacts.plan_id');
    } else {
      $project_plans = $project_plans->join('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id');
      // $project_plans=$project_plans->join('procacts','project_plans.plan_id','procacts.plan_id');
    }


    if ($procurement_activity === 'projects_without_resolution' || $procurement_activity === 'projects_without_bidders' || $procurement_activity === 'pending_rdf' || $procurement_activity === "pending_rdf_count" ||  $procurement_activity === 'projects_for_review') {
      $project_plans = $project_plans->leftJoin('resolution_projects', 'resolution_projects.procact_id', 'procacts.procact_id');
    }
    if ($procurement_activity === 'projects_with_resolution_pending_noa') {
      $project_plans = $project_plans->leftJoin('resolution_projects', 'resolution_projects.procact_id', 'procacts.procact_id')->leftJoin('resolutions', 'resolutions.resolution_id', 'resolution_projects.resolution_id');
    }

    if ($procurement_activity === 'for_notice_of_award') {
      $project_plans = $project_plans->join('resolution_projects', 'resolution_projects.procact_id', 'procacts.procact_id')
        ->join('resolutions', 'resolution_projects.resolution_id', 'resolutions.resolution_id');
    }
    if ($procurement_activity === 'projects_for_rebid') {
      $project_plans = $project_plans->leftJoin('resolution_projects', 'resolution_projects.procact_id', 'procacts.procact_id')
        ->leftJoin('resolution_project_bids', 'resolution_project_bids.procact_id', 'procacts.procact_id')
        ->leftJoin('resolutions as r1', 'resolution_projects.resolution_id', 'r1.resolution_id')
        ->leftJoin('resolutions as r2', 'resolution_project_bids.resolution_id', 'r2.resolution_id');
    }
    $project_plans = $project_plans->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id');
    $project_plans = $project_plans->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id');
    $project_plans = $project_plans->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id');
    $project_plans = $project_plans->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id');
    $project_plans = $project_plans->join('funds', 'funds.fund_id', 'project_plans.fund_id');
    $project_plans = $project_plans->join('fund_category', 'funds.fund_category_id', 'fund_category.fund_category_id');
    $project_plans = $project_plans->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id');
    $project_plans = $project_plans->orderBy('procacts.procact_id', 'desc')->get();




    if ($procurement_activity === 'for_notice_of_award' || $procurement_activity === 'performance_bond' || $procurement_activity === 'chsp' || $procurement_activity === 'for_contract_generation' || $procurement_activity === 'notice_of_award' || $procurement_activity === 'contract_preparation_signing' || $procurement_activity === 'approval_by_higher_authority' ||  $procurement_activity === 'notice_to_proceed' || $procurement_activity === 'for_notice_to_proceed') {

      $project_plans_temp = [];
      foreach ($project_plans as $project_plan) {

        $array = DB::table('procacts')
          ->where('procacts.procact_id', $project_plan->procact_id)
          ->select(DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), 'project_plans.*', 'procacts.*', 'barangays.barangay_name', 'municipalities.municipality_name', "notice_of_awards.*", "notice_of_awards.date_released as noa_released", "notice_of_awards.date_received as noa_received", 'procurement_modes.mode', 'funds.source', 'project_timelines.*', 'project_bidders.*', 'bid_docs.*', 'contractors.*', 'contracts.*', 'contracts.*', 'notice_of_awards.*', 'notice_to_proceeds.*', 'chsp.*')
          ->join('project_plans', 'project_plans.plan_id', 'procacts.plan_id')
          ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
          ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
          ->join('project_bidders', 'project_bidders.project_bid', 'project_plans.project_bid_id')
          ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
          ->leftJoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_bidders.project_bid')
          ->leftJoin('notice_to_proceeds', 'notice_to_proceeds.project_bid_id', 'project_bidders.project_bid')
          ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
          ->join('contractors', 'contractors.contractor_id', 'bid_docs.contractor_id')
          ->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id')
          ->leftJoin('contracts', 'project_bidders.project_bid', 'contracts.project_bid_id')
          ->leftJoin('chsp', 'chsp.chsp_project_bid', 'project_bidders.project_bid')
          ->leftJoin('twg_evaluations', 'twg_evaluations.project_bid', 'project_bidders.project_bid');

        // if($procurement_activity==='for_contract_generation'){
        //   $array=$array->where('contract_id','<>',null);
        // }

        $array = $array->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
          ->join('funds', 'funds.fund_id', 'project_plans.fund_id')
          ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')->first();



        if ($array == null) {

          $array = DB::table('project_plans')
            ->where('procacts.procact_id', $project_plan->procact_id)
            ->select(DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), 'project_plans.*', 'procacts.*', 'barangays.barangay_name', 'municipalities.municipality_name', "notice_of_awards.*", "notice_of_awards.date_released as noa_released", "notice_of_awards.date_received as noa_received", 'procurement_modes.mode', 'funds.source', 'project_timelines.*', 'project_bidders.*', 'rfqs.*', 'contractors.*', 'contracts.*', 'notice_of_awards.*', 'notice_to_proceeds.*', 'chsp.*')
            ->join('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')
            ->join('project_activity_status', 'project_activity_status.procact_id', 'procacts.procact_id')
            ->join('project_timelines', 'project_timelines.procact_id', 'procacts.procact_id')
            ->join('project_bidders', 'project_bidders.project_bid', 'project_plans.project_bid_id')
            ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
            ->leftJoin('notice_of_awards', 'notice_of_awards.project_bid_id', 'project_bidders.project_bid')
            ->leftJoin('notice_to_proceeds', 'notice_to_proceeds.project_bid_id', 'project_bidders.project_bid')
            ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
            ->join('contractors', 'contractors.contractor_id', 'rfqs.contractor_id')
            ->leftJoin('barangays', 'project_plans.barangay_id', 'barangays.barangay_id')
            ->leftJoin('contracts', 'project_bidders.project_bid', 'contracts.project_bid_id')
            ->leftJoin('chsp', 'chsp.chsp_project_bid', 'project_bidders.project_bid')
            ->leftJoin('twg_evaluations', 'twg_evaluations.project_bid', 'project_bidders.project_bid');

          // if($procurement_activity==='for_contract_generation'){
          //   $array=$array->where('contract_id','<>',null);
          // }
          $array = $array->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
            ->join('funds', 'funds.fund_id', 'project_plans.fund_id')
            ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')->first();
        }


        if ($array != null) {
          array_push($project_plans_temp, $array);
        }
      }
      $project_plans = $project_plans_temp;
    } else  if ($procurement_activity === 'projects_without_resolution') {
      // dd($project_plans);
    } else if ($procurement_activity === 'projects_without_bidders' || $procurement_activity === 'projects_for_rebid' || $procurement_activity === 'projects_for_review') {


      $project_plans_temp = [];
      foreach ($project_plans as $project_plan) {
        $non_responsive = $this->getActiveBidders($project_plan->procact_id, 'non-responsive');
        $non_disqualified_bidders = $this->getActiveBidders($project_plan->procact_id, 'responsive,active');
        $disqualified_bidders = $this->getActiveBidders($project_plan->procact_id, 'disqualified');
        $project_plan = (array) $project_plan;
        if ($non_disqualified_bidders === 0) {
          if ($disqualified_bidders > 0) {
            $project_plan = array_merge($project_plan, array('failure_status' => "Failure Upon Opening"));
          } else if ($non_responsive > 0) {
            $project_plan = array_merge($project_plan, array('failure_status' => "Failure After Post Qual"));
          } else {
            $project_plan = array_merge($project_plan, array('failure_status' => "No Bidders"));
          }
          array_push($project_plans_temp, (object)$project_plan);
        } else {
          if ($project_plan['main_status'] === "terminated") {
            $project_plan = array_merge($project_plan, array('failure_status' => "Terminated Contract"));
            array_push($project_plans_temp, (object)$project_plan);
          }
        }
      }

      $project_plans = $project_plans_temp;
    } else if ($procurement_activity === 'pending_rdf') {
      $project_plans_temp = [];
      foreach ($project_plans as $project_plan) {
        $non_responsive = $this->getBiddersData($project_plan->procact_id, 'non-responsive,disqualified');
        $non_disqualified_bidders = $this->getBiddersData($project_plan->procact_id, 'responsive,active');
        if (count($non_responsive) > 0 && count($non_disqualified_bidders) === 0) {
          //check Received Notice of Post Disqualification
          foreach ($non_responsive as $row) {
            $unreceived = DB::table('project_bidder_notices')->where([['date_received_by_contractor', null], ['project_bid', $row->project_bid]])->count();
            if ($unreceived > 0) {
              $project_plan = (array) $project_plan;
              $project_plan = array_merge($project_plan, array('failure_status' => "Unreceived Notice of Disqualification/Post Disqualification " . $row->business_name));
              array_push($project_plans_temp, (object)$project_plan);
            } else {
              $motion = DB::table('motion_for_reconsideration')->where([['project_bid_id', $row->project_bid], ['resolution_mr_project_bid_id', null]])
                ->join('motion_for_reconsideration_project_bid', 'motion_for_reconsideration_project_bid.mr_id', 'motion_for_reconsideration.mr_id')
                ->leftJoin('resolution_mr_project_bids', 'motion_for_reconsideration_project_bid.mr_project_bid_id', 'resolution_mr_project_bids.mr_project_bid_id')
                ->count();
              if ($motion > 0) {
                $project_plan = (array) $project_plan;
                $project_plan = array_merge($project_plan, array('failure_status' => "Pending Motion for Reconsideration of " . $row->business_name));
                array_push($project_plans_temp, (object)$project_plan);
              }
            }
          }

          // check pending MR

          // check pending Resolution
        }
        // $non_disqualified_bidders = $this->getActiveBidders($project_plan->procact_id, 'responsive,active');
        // $disqualified_bidders = $this->getActiveBidders($project_plan->procact_id, 'disqualified');
        // $project_plan = (array) $project_plan;
        // $pending_mr = DB::table('motion_for_reconsideration');

        // if ($non_disqualified_bidders === 0) {
        //   if ($disqualified_bidders > 0) {
        //     $project_plan = array_merge($project_plan, array('failure_status' => "Failure Upon Opening"));
        //   } else if ($non_responsive > 0) {
        //     $project_plan = array_merge($project_plan, array('failure_status' => "Failure After Post Qual"));
        //   } else {
        //     $project_plan = array_merge($project_plan, array('failure_status' => "No Bidders"));
        //   }
        //   array_push($project_plans_temp, (object)$project_plan);
        // } else {
        //   if ($project_plan['main_status'] === "terminated") {
        //     $project_plan = array_merge($project_plan, array('failure_status' => "Terminated Contract"));
        //     array_push($project_plans_temp, (object)$project_plan);
        //   }
        // }
      }

      $project_plans = $project_plans_temp;
    } else if ($procurement_activity === 'post_qual_to_verify') {
      $project_plans_temp = [];
      foreach ($project_plans as $project_plan) {
        $post_qual_days = $this->getPostQualDays($project_plan->procact_id);
        $array = (array) $project_plan;
        $bidders_to_verify = $this->getBiddersToVerify($project_plan->procact_id);
        if ($bidders_to_verify >= 1) {
          $bidder_count = $this->getActiveBidders($project_plan->procact_id, 'responsive,active,non-responsive,disqualified');
          $responsive_count = $this->getActiveBidders($project_plan->procact_id, 'responsive');
          $active_count = $this->getActiveBidders($project_plan->procact_id, 'active');
          $all_bidders = $this->getActiveBidders($project_plan->procact_id, 'responsive,non-responsive,active,late,disapproved,withdrawn');
          $bidder_on_post_qual = null;
          $twg_responsive = $this->getBiddersTWGBased($project_plan->procact_id, 'responsive');
          $twg_active = $this->getBiddersTWGBased($project_plan->procact_id, 'active');
          if ($all_bidders == 0) {
          }
          if (count($twg_responsive) > 0) {
            if ($twg_responsive[0]->bid_status === "responsive" || $twg_responsive[0]->bid_status === "active") {
              $bidder_on_post_qual = $twg_responsive;
            } else {
              if (count($twg_active) > 0) {
                $bidder_on_post_qual = $twg_active;
              } else {
                if ($responsive_count > 0) {
                  $bidder_on_post_qual = null;
                } else if ($active_count > 0) {
                  $bidder_on_post_qual = $this->getBiddersData($project_plan->procact_id, 'active');
                } else {
                }
              }
            }
          } else if (count($twg_active) > 0) {
            $bidder_on_post_qual = $twg_active;
          } else {
            if ($responsive_count > 0) {
              $bidder_on_post_qual = null;
            } else if ($active_count > 0) {
              $bidder_on_post_qual = $this->getBiddersData($project_plan->procact_id, 'active');
            } else {
            }
          }

          if ($bidder_on_post_qual === null) {
            // $array=array_merge($array,array("ongoing_post_qual"=>''));
            // $array=array_merge($array,array("ongoing_post_qual_amount"=>''));
            // $array=array_merge($array,array("post_qual_days"=>''));
          } else {
            if (count($bidder_on_post_qual) > 0) {
              if ($bidder_on_post_qual[0]->detailed_bid_as_evaluated > 0) {
                $array = array_merge($array, array("ongoing_post_qual_amount" => $bidder_on_post_qual[0]->minimum_detailed_cost));
                $array = array_merge($array, array("ongoing_post_qual" => $bidder_on_post_qual[0]->business_name));
                $array = array_merge($array, array("post_qual_days" => $post_qual_days));
              } else {
                $array = array_merge($array, array("ongoing_post_qual" => $bidder_on_post_qual[0]->business_name));
                $array = array_merge($array, array("ongoing_post_qual_amount" => $bidder_on_post_qual[0]->minimum_cost));
                $array = array_merge($array, array("post_qual_days" => $post_qual_days));
              }
            }
            $maximum_days = $all_bidders * 45;
            $array = array_merge($array, array("maximum_days" => $maximum_days));
            $array = array_merge($array, array("bidder_count" => $bidder_count));
            $array = array_merge($array, array("active_count" => $active_count));
            $array = array_merge($array, array("responsive_count" => $responsive_count));
            array_push($project_plans_temp, (object) $array);
          }
        }
      }
      $project_plans = $project_plans_temp;
    } else if ($procurement_activity === 'projects_with_bidders') {
      $project_plans_temp = [];
      foreach ($project_plans as $project_plan) {
        $bidder_count = $this->getActiveBidders($project_plan->procact_id, 'responsive,active,non-responsive,disapproved,withdrawn');
        $array = (array) $project_plan;
        $array = array_merge($array, array("bidder_count" => $bidder_count));
        array_push($project_plans_temp, (object) $array);
      }
      $project_plans = $project_plans_temp;
    } else {
      $project_plans_temp = [];
      foreach ($project_plans as $project_plan) {
        $post_qual_days = $this->getPostQualDays($project_plan->procact_id);
        $array = (array) $project_plan;
        $bidder_count = $this->getActiveBidders($project_plan->procact_id, 'responsive,active,non-responsive,disapproved,withdrawn');
        $responsive_count = $this->getActiveBidders($project_plan->procact_id, 'responsive');

        $active_count = $this->getActiveBidders($project_plan->procact_id, 'active');
        $all_bidders = $this->getActiveBidders($project_plan->procact_id, 'responsive,non-responsive,active,late,disapproved,withdrawn');
        $bidder_on_post_qual = null;
        $twg_responsive = $this->getBiddersTWGBased($project_plan->procact_id, 'responsive');
        $twg_active = $this->getBiddersTWGBased($project_plan->procact_id, 'active');
        $responsive_bidder = null;
        $responsive_bid = null;

        if ($responsive_count > 0) {
          $responsive = $this->getBid($project_plan->project_bid_id);
          if ($responsive != null) {
            $responsive_bidder = $responsive->business_name;
            $responsive_bid = $project_plan->project_bid_id;
          } else {
            $responsive_bidder = null;
            $responsive_bid = null;
          }
        }

        if ($all_bidders == 0) {
        }
        if (count($twg_responsive) > 0) {
          if ($twg_responsive[0]->bid_status === "responsive" || $twg_responsive[0]->bid_status === "active") {
            $bidder_on_post_qual = $twg_responsive;
          } else {
            if (count($twg_active) > 0) {
              $bidder_on_post_qual = $twg_active;
            } else {
              if ($responsive_count > 0) {
                $bidder_on_post_qual = null;
              } else if ($active_count > 0) {
                $bidder_on_post_qual = $this->getBiddersData($project_plan->procact_id, 'active');
              } else {
              }
            }
          }
        } else if (count($twg_active) > 0) {
          $bidder_on_post_qual = $twg_active;
        } else {
          if ($responsive_count > 0) {
            $bidder_on_post_qual = null;
          } else if ($active_count > 0) {
            $bidder_on_post_qual = $this->getBiddersData($project_plan->procact_id, 'active');
          } else {
          }
        }

        if ($bidder_on_post_qual === null) {
          // $array=array_merge($array,array("ongoing_post_qual"=>''));
          // $array=array_merge($array,array("ongoing_post_qual_amount"=>''));
          // $array=array_merge($array,array("post_qual_days"=>''));
        } else {
          if (count($bidder_on_post_qual) > 0) {
            $maximum_days = 12;
            $request_for_extesion = RequestForExtensionBids::where('project_bid', $bidder_on_post_qual[0]->project_bid)->exists();
            if ($request_for_extesion) {
              $maximum_days = 45;
            }

            if ($bidder_on_post_qual[0]->detailed_bid_as_evaluated > 0) {
              $array = array_merge($array, array("ongoing_post_qual_amount" => $bidder_on_post_qual[0]->minimum_detailed_cost));
              $array = array_merge($array, array("ongoing_post_qual" => $bidder_on_post_qual[0]->business_name));
              $array = array_merge($array, array("post_qual_days" => $post_qual_days));
              $array = array_merge($array, array("twg_evaluation" => $bidder_on_post_qual[0]->twg_final_bid_evaluation));
            } else {
              $array = array_merge($array, array("ongoing_post_qual" => $bidder_on_post_qual[0]->business_name));
              $array = array_merge($array, array("ongoing_post_qual_amount" => $bidder_on_post_qual[0]->minimum_cost));
              $array = array_merge($array, array("post_qual_days" => $post_qual_days));
              $array = array_merge($array, array("twg_evaluation" => $bidder_on_post_qual[0]->twg_final_bid_evaluation));
            }
            // dump($bidder_on_post_qual[0]->project_bid);
            // $extension=RequestForExtensionBids::where('project_bid',$bidder_on_post_qual[0]->project_bid)
            // ->join('request_for_extension',"request_for_extension.request_id","request_for_extension_bids.request_id")
            // ->orderBy('request_date','desc')->first();
            // if($extension===null){
            //   $array=array_merge($array,array("maximum_days"=>12));
            // }
            // else{
            //
            // }

          }
          $array = array_merge($array, array("responsive_bidder" => $responsive_bidder));
          $array = array_merge($array, array("responsive_bid" => $responsive_bid));
          $array = array_merge($array, array("maximum_days" => $maximum_days));
          $array = array_merge($array, array("bidder_count" => $bidder_count));
          $array = array_merge($array, array("active_count" => $active_count));
          $array = array_merge($array, array("responsive_count" => $responsive_count));
          array_push($project_plans_temp, (object) $array);
        }
      }
      $project_plans = $project_plans_temp;
    }

    return $project_plans;
  }

  public function getActiveBiddersData($id)
  {

    $data = array('title' => '', 'project_bidders' => '', 'abc' => '', 'open_bid' => '');
    $latest_procact = DB::table('project_plans')->where('project_plans.plan_id', $id)->join('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();
    $mode = $latest_procact->mode_id;
    $procact_id = $latest_procact->procact_id;

    $procacts_array = [];
    $procacts_objects = DB::table('project_plans')
      ->select('latest_procact_id')
      ->where([['current_cluster', $latest_procact->current_cluster], ['current_cluster', '<>', null]])->get();

    foreach ($procacts_objects as $procacts_object) {
      array_push($procacts_array, $procacts_object->latest_procact_id);
    }

    if ($mode == 1) {
      $project_bidders = DB::table('bid_doc_projects')
        ->select(DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), "bid_docs.*", "procacts.*", "contractors.*", "project_bidders.*", "twg_evaluations.twg_evaluation_status", "twg_evaluations.twg_evaluation_remarks", "twg_evaluations.twg_final_bid_evaluation", "twg_evaluations.post_qual_start", "twg_evaluations.post_qual_end")
        ->where([['bid_doc_projects.procact_id', $procact_id]])
        ->whereNotIn('project_bidders.bid_status', ['disqualified', 'ineligible'])
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        // ->orderBy('final_minimum_cost','asc')
        ->orderBy('minimum_cost', 'asc')
        ->get();

      $detailed_bids = DB::table('bid_doc_projects')
        ->select('*', 'project_bidders.project_bid as bid_id')
        ->whereIn('bid_doc_projects.procact_id', $procacts_array)
        ->whereNotIn('project_bidders.bid_status', ['disqualified', 'ineligible'])
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
        // ->orderBy('project_bidders.bid_status','asc')
        ->orderBy('bid_docs.bid_as_evaluated', 'asc')
        ->orderBy('bid_docs.date_received', 'asc')
        ->get();
    } else {
      $project_bidders = DB::table('rfq_projects')
        ->select(DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), "rfq_projects.*", "rfqs.*", "procacts.*", "contractors.*", "project_bidders.*", "twg_evaluations.twg_evaluation_status", "twg_evaluations.twg_evaluation_remarks", "twg_evaluations.twg_final_bid_evaluation", "twg_evaluations.post_qual_start", "twg_evaluations.post_qual_end")
        ->where([['rfq_projects.procact_id', $procact_id]])
        ->whereNotIn('project_bidders.bid_status', ['disqualified', 'ineligible'])
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->leftJoin("twg_evaluations", "twg_evaluations.project_bid", "project_bidders.project_bid")
        ->orderBy('final_minimum_cost', 'asc')
        ->orderBy('minimum_cost', 'asc')
        ->get();

      $detailed_bids = DB::table('rfq_projects')
        ->select('*', 'project_bidders.project_bid as bid_id')
        ->whereIn('rfq_projects.procact_id', $procacts_array)
        ->whereNotIn('project_bidders.bid_status', ['disqualified', 'ineligible'])
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
        ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
        // ->orderBy('project_bidders.bid_status','asc')
        ->orderBy('rfqs.bid_as_evaluated', 'asc')
        ->orderBy('rfqs.date_received', 'asc')
        ->get();
    }



    if ($latest_procact->current_cluster != null) {
      $titles = DB::table('project_plans')->select(DB::raw('group_concat(project_title separator " *** ") as titles'))->where([['current_cluster', $latest_procact->current_cluster], ['current_cluster', '<>', null]])->get();
      $title = $titles[0]->titles;
      $title2 = $latest_procact->project_title;
      $project_cost = DB::table('project_plans')->where('current_cluster', $latest_procact->current_cluster)->sum('project_plans.project_cost');
      $open_bid = $latest_procact->open_bid;
      $project_number = $latest_procact->project_no;
    } else {
      $title = $latest_procact->project_title;
      $title2 = $latest_procact->project_title;
      $project_cost = $latest_procact->project_cost;
      $open_bid = $latest_procact->open_bid;
      $project_number = $latest_procact->project_no;
    }

    if ($open_bid == null) {
      $timeline = DB::table('project_timelines')->where('procact_id', $latest_procact->latest_procact_id)->first();
      $open_bid = $timeline->bid_submission_start;
    }

    $data['title'] = $title;
    $data['title2'] = $title2;
    $data['project_bidders'] = $project_bidders;
    $data['detailed_bids'] = $detailed_bids;
    $data['project_cost'] = $project_cost;
    $data['open_bid'] = $open_bid;
    $data['project_number'] = $project_number;
    return $data;
  }

  public function getNoticeToSubmitPostQualDocs($id, $year, $with_ntspqd)
  {

    if ($with_ntspqd) {
      $bid_doc_based = DB::table('notice_to_submit_post_qual_docs')
        ->select("project_bidders.*", "notice_to_submit_post_qual_docs.*", "notice_to_submit_post_qual_docs.date_received as ntspqd_received", "contractors.*", "procacts.*", "project_plans.*", "project_timelines.*")
        ->join('project_bidders', 'project_bidders.project_bid', 'notice_to_submit_post_qual_docs.project_bid_id')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->get();

      $rfq_based = DB::table('notice_to_submit_post_qual_docs')
        ->select("project_bidders.*", "notice_to_submit_post_qual_docs.*", "notice_to_submit_post_qual_docs.date_received as ntspqd_received", "contractors.*", "procacts.*", "project_plans.*", "project_timelines.*")
        ->leftJoin('project_bidders', 'project_bidders.project_bid', 'notice_to_submit_post_qual_docs.project_bid_id')
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
        ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->get();
    } else {
      $bid_doc_based = DB::table('project_bidders')
        ->select("project_bidders.*", "notice_to_submit_post_qual_docs.*", "notice_to_submit_post_qual_docs.date_received as ntspqd_received", "contractors.*", "procacts.*", "project_plans.*", "project_timelines.*")
        ->leftJoin('notice_to_submit_post_qual_docs', 'project_bidders.project_bid', 'notice_to_submit_post_qual_docs.project_bid_id')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->get();

      $rfq_based = DB::table('project_bidders')
        ->select("project_bidders.*", "notice_to_submit_post_qual_docs.*", "notice_to_submit_post_qual_docs.date_received as ntspqd_received", "contractors.*", "procacts.*", "project_plans.*", "project_timelines.*")
        ->leftJoin('notice_to_submit_post_qual_docs', 'project_bidders.project_bid', 'notice_to_submit_post_qual_docs.project_bid_id')
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
        ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->get();
    }


    if (count($bid_doc_based) == null) {
      $project_plans = $rfq_based;
    } else if (count($rfq_based) == null) {
      $project_plans = $bid_doc_based;
    } else {
      $project_plans = array_merge((array) json_decode($bid_doc_based), (array) json_decode($rfq_based));
      $project_plans = (object) $project_plans;
    }

    return $project_plans;
  }

  public function getRequirementsChecklist($status, $year, $with_checklist)
  {
    $table_header1 = DB::table('project_bidder_additional_required_documents')->where('procacts.open_bid', 'like', $year . '%');
    $table_header2 = DB::table('project_bidder_additional_required_documents')->where('procacts.open_bid', 'like', $year . '%');
    if ($status != null) {
      if ($status == "released") {
        $table_header1 = $table_header1->where('project_bidder_additional_required_documents.date_released', "!=", null);
        $table_header2 = $table_header2->where('project_bidder_additional_required_documents.date_released', "!=", null);
      } else {
        $table_header1 = $table_header1->whereIn('additional_docs_status', $status);
        $table_header2 = $table_header2->whereIn('additional_docs_status', $status);
      }
    }

    if ($with_checklist === true) {

      $bid_doc_based = $table_header1->select("project_bidders.*", "procurement_modes.*", "project_bidder_additional_required_documents.*", "project_bidder_additional_required_documents.date_received as ntspqd_received", "contractors.*", "procacts.*", "project_plans.*", "project_timelines.*")
        ->join('project_bidders', 'project_bidders.project_bid', 'project_bidder_additional_required_documents.project_bid_id')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
        ->get();

      $rfq_based = $table_header2
        ->select("project_bidders.*", "procurement_modes.*", "project_bidder_additional_required_documents.*", "project_bidder_additional_required_documents.date_received as ntspqd_received", "contractors.*", "procacts.*", "project_plans.*", "project_timelines.*")
        ->select("project_bidders.*", "procurement_modes.*", "project_bidder_additional_required_documents.*", "project_bidder_additional_required_documents.date_received as ntspqd_received", "contractors.*", "procacts.*", "project_plans.*", "project_timelines.*")
        ->join('project_bidders', 'project_bidders.project_bid', 'project_bidder_additional_required_documents.project_bid_id')
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
        ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
        ->get();
    } else if ($with_checklist === false) {
      $bid_doc_based = $table_header1->where('project_bidder_additional_required_documents.pbard_id', null)
        ->select("project_bidders.*", "procurement_modes.*", "project_bidder_additional_required_documents.*", "project_bidder_additional_required_documents.date_received as ntspqd_received", "contractors.*", "procacts.*", "project_plans.*", "project_timelines.*")
        ->leftJoin('project_bidders', 'project_bidders.project_bid', 'project_bidder_additional_required_documents.project_bid_id')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
        ->get();

      $rfq_based = $table_header2->where('project_bidder_additional_required_documents.pbard_id', null)
        ->select("project_bidders.*", "procurement_modes.*", "project_bidder_additional_required_documents.*", "project_bidder_additional_required_documents.date_received as ntspqd_received", "contractors.*", "procacts.*", "project_plans.*", "project_timelines.*")
        ->leftJoin('project_bidders', 'project_bidders.project_bid', 'project_bidder_additional_required_documents.project_bid_id')
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
        ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
        ->get();
    } else {
      $bid_doc_based = DB::table('project_bidders')
        ->where('procacts.open_bid', 'like', $year . '%')
        ->select("project_bidders.*", "procurement_modes.*", "project_bidder_additional_required_documents.*", "project_bidder_additional_required_documents.date_received as ntspqd_received", "contractors.*", "procacts.*", "project_plans.*", "project_timelines.*")
        ->leftJoin('project_bidder_additional_required_documents', 'project_bidders.project_bid', 'project_bidder_additional_required_documents.project_bid_id')
        ->join('bid_doc_projects', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
        ->get();

      $rfq_based = DB::table('project_bidders')
        ->where('procacts.open_bid', 'like', $year . '%')
        ->select("project_bidders.*", "procurement_modes.*", "project_bidder_additional_required_documents.*", "project_bidder_additional_required_documents.date_received as ntspqd_received", "contractors.*", "procacts.*", "project_plans.*", "project_timelines.*")
        ->leftJoin('project_bidder_additional_required_documents', 'project_bidders.project_bid', 'project_bidder_additional_required_documents.project_bid_id')
        ->join('rfq_projects', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
        ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
        ->join('project_timelines', 'procacts.procact_id', 'project_timelines.procact_id')
        ->join('procurement_modes', 'procurement_modes.mode_id', 'project_plans.mode_id')
        ->get();
    }


    if (count($bid_doc_based) == null) {
      $project_plans = $rfq_based;
    } else if (count($rfq_based) == null) {
      $project_plans = $bid_doc_based;
    } else {
      $project_plans = array_merge((array) json_decode($bid_doc_based), (array) json_decode($rfq_based));
      $project_plans = (object) $project_plans;
    }

    return $project_plans;
  }

  public function getPostQualDays($procact_id)
  {
    $bidders = DB::table('rfq_projects')
      ->select('project_bidders.*', 'twg_evaluations.post_qual_end', 'project_timelines.post_qualification_start')
      ->where([['procacts.procact_id', $procact_id]])
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->join("project_timelines", "project_timelines.procact_id", "procacts.procact_id")
      ->orderBy('twg_evaluations.post_qual_end', 'desc')
      ->first();

    if ($bidders == null) {
      $bidders = DB::table('bid_doc_projects')
        ->select('project_bidders.*', 'twg_evaluations.post_qual_end', 'project_timelines.post_qualification_start')
        ->where([['procacts.procact_id', $procact_id]])
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
        ->join("project_timelines", "project_timelines.procact_id", "procacts.procact_id")
        ->orderBy('twg_evaluations.post_qual_end', 'desc')
        ->first();
    }

    if ($bidders === null) {
      $post_qual_days = "";
    } else {
      if ($bidders->bid_status === "disapproved") {
        $lce_evaluation = LCEEvaluation::where('project_bid', $bidders->project_bid)->first();
        $post_qual_days = (date_diff(date_create(date("Y/m/d")), date_create($lce_evaluation->lce_contractor_date_received))->days) - 3;
      } else {
        if ($bidders->post_qual_end === null) {
          $post_qual_days = date_diff(date_create(date("Y/m/d")), date_create($bidders->post_qualification_start))->days;
        } else {
          $post_qual_days = date_diff(date_create(date("Y/m/d")), date_create($bidders->post_qual_end))->days;
        }
      }
    }

    return $post_qual_days;
  }

  public function getItemNumber($procact_id, $date_opened)
  {

    $projects_opened = DB::table('procacts')->where('open_bid', $date_opened)->orderBy('itb_arrangement', 'asc')->get();
    $temp_cluster = null;
    $item_number = 0;
    foreach ($projects_opened as $key => $project_opened) {
      if ($project_opened->plan_cluster_id === null) {
        $item_number = $item_number + 1;
      } else if ($project_opened->plan_cluster_id != $temp_cluster) {
        $item_number = $item_number + 1;
        $temp_cluster = $project_opened->plan_cluster_id;
      } else {
      }
      if ($procact_id === $project_opened->procact_id) {
        break;
      }
    }
    return $item_number;
  }

  public function getSimilarBidder($procact_ids, $status)
  {
    $bidder_error = false;
    $status = explode(",", $status);
    $status_string = "'" . implode("','", $status) . "'";
    $procact_ids_array = explode(",", $procact_ids);
    $plan_count = count(explode(",", $procact_ids));
    if ($plan_count > 1) {
      $bidders = DB::select(DB::raw("select c.bid_status,e.contractor_id,e.business_name,f.mr_due_date,f.notice_type,c.project_bid,count(e.contractor_id) as project_count from rfq_projects a, procacts b,project_bidders c,rfqs d,contractors e ,project_bidder_notices f where a.procact_id=b.procact_id and a.rfq_project_id=c.rfq_project_id and a.rfq_id=d.rfq_id and d.contractor_id=e.contractor_id and b.procact_id in (" . $procact_ids . ") and c.bid_status in (" . $status_string . ")  and c.project_bid=f.project_bid and f.date_received IS NOT NULL group by e.contractor_id,f.mr_due_date,f.notice_type,e.business_name having count(e.business_name)=" . $plan_count));
      if (count($bidders) == 0) {
        $bidders = DB::select(DB::raw("select c.bid_status,e.contractor_id,e.business_name,f.mr_due_date,f.notice_type,c.project_bid,count(e.contractor_id) as project_count from bid_doc_projects a, procacts b,project_bidders c,bid_docs d,contractors e ,project_bidder_notices f where a.procact_id=b.procact_id and a.bid_doc_project_id=c.bid_doc_project_id and a.bid_doc_id=d.bid_doc_id and d.contractor_id=e.contractor_id and b.procact_id in (" . $procact_ids . ") and c.bid_status in (" . $status_string . ")    and c.project_bid=f.project_bid and f.date_received IS NOT NULL group by e.contractor_id,f.mr_due_date,f.notice_type,e.business_name having count(e.business_name)=" . $plan_count));
      }
    } else {
      $bidders = DB::table('rfq_projects')
        ->select('project_bidders.bid_status', 'contractors.contractor_id', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), 'project_bidders.project_bid', 'rfqs.bid_in_words', 'project_bidder_notices.*', 'rfqs.date_received', 'rfqs.time_received', 'project_bidders.bid_status', 'rfq_projects.rfq_project_id', 'rfq_projects.detailed_bid_as_read', 'rfq_projects.detailed_bid_as_evaluated', 'rfqs.proposed_bid', 'rfqs.amount_of_discount', 'rfqs.bid_as_evaluated', 'rfqs.discount', 'rfqs.discount_source', 'contractors.business_name', 'contractors.address', 'contractors.owner')
        ->whereIn('procacts.procact_id', $procact_ids_array)
        ->whereIn('project_bidders.bid_status', $status)
        ->where('project_bidder_notices.date_received', '<>', null)
        ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
        ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->join('project_bidder_notices', 'project_bidders.project_bid', 'project_bidder_notices.project_bid')
        ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
        ->orderBy('minimum_cost', 'asc')
        ->get();

      if (count($bidders) == 0) {
        $bidders = DB::table('bid_doc_projects')
          ->select('project_bidders.bid_status', 'contractors.contractor_id', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), 'bid_docs.date_received', 'bid_docs.bid_in_words', 'project_bidder_notices.*', 'bid_docs.time_received', 'project_bidders.bid_status', 'project_bidders.project_bid', 'bid_doc_projects.bid_doc_project_id', 'bid_doc_projects.detailed_bid_as_read', 'bid_doc_projects.detailed_bid_as_evaluated', 'bid_docs.proposed_bid', 'bid_docs.amount_of_discount', 'bid_docs.bid_as_evaluated', 'bid_docs.discount', 'bid_docs.discount_source', 'contractors.business_name', 'contractors.address', 'contractors.owner')
          ->whereIn('procacts.procact_id', $procact_ids_array)
          ->whereIn('project_bidders.bid_status', $status)
          ->where('project_bidder_notices.date_received', '<>', null)
          ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
          ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
          ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
          ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
          ->join('project_bidder_notices', 'project_bidders.project_bid', 'project_bidder_notices.project_bid')
          ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
          ->orderBy('minimum_cost', 'asc')
          ->get();
      }
    }
    return $bidders;
  }

  public function getTWGBiddersData($procact_id, $status)
  {
    $bidder_error = false;
    $status = explode(",", $status);
    $bidders = DB::table('rfq_projects')
      ->select(DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), 'twg_evaluations.twg_final_bid_evaluation', 'twg_evaluations.post_qual_start', 'twg_evaluations.post_qual_end', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), 'project_bidders.project_bid', 'rfqs.contractor_id', 'project_bidders.bid_status', 'rfq_projects.rfq_project_id', 'rfq_projects.detailed_bid_as_read', 'rfq_projects.detailed_bid_as_evaluated', 'rfqs.proposed_bid', 'rfqs.bid_as_evaluated', 'rfqs.discount', 'contractors.business_name', 'contractors.owner', 'contractors.address')
      ->where([['procacts.procact_id', $procact_id]])
      ->whereIn('twg_evaluations.twg_evaluation_status', $status)
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->join('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->orderByRaw('ISNULL(minimum_cost), minimum_cost ASC')
      ->get();

    if (count($bidders) == 0) {
      $bidders = DB::table('bid_doc_projects')
        ->select(DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), 'twg_evaluations.twg_final_bid_evaluation', 'twg_evaluations.post_qual_start', 'twg_evaluations.post_qual_end', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated,twg_evaluations.detailed_bid_as_calculated) AS minimum_detailed_cost"), 'project_bidders.bid_status', 'bid_docs.contractor_id', 'project_bidders.project_bid', 'bid_doc_projects.bid_doc_project_id', 'bid_doc_projects.detailed_bid_as_read', 'bid_doc_projects.detailed_bid_as_evaluated', 'bid_docs.proposed_bid', 'bid_docs.bid_as_evaluated', 'bid_docs.discount', 'contractors.business_name', 'contractors.owner', 'contractors.address')
        ->where([['procacts.procact_id', $procact_id]])
        ->whereIn('twg_evaluations.twg_evaluation_status', $status)
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->join('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
        ->orderByRaw('ISNULL(minimum_cost), minimum_cost ASC')
        ->get();
    }


    return $bidders;
  }



  public function getBiddersData($procact_id, $status)
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
      ->get();


    if (count($bidders) == 0) {
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
        ->get();
    }

    if (count($bidders) === 0) {
      // $plans=DB::table('project_plans')->where('procact_id',$procact_id)
      // ->join('procacts','procacts.procact_id','project_plans.latest_procact_id')
      // ->first();
      // dump($plans);
      //
      // dd($procact_id);
      // dd("an error occured");
      // transferDataToSAPP($procact_id);
    }


    return $bidders;
  }

  public function getSpecificBiddersData($date_opened, $plan_id, $contractor_id, $status)
  {
    $bidder_error = false;
    $status = explode(",", $status);
    $bidder = DB::table('rfq_projects')
      ->select(DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), 'twg_evaluations.twg_final_bid_evaluation', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), 'project_bidders.project_bid', 'project_bidders.bid_status', 'rfq_projects.rfq_project_id', 'rfq_projects.detailed_bid_as_read', 'rfq_projects.detailed_bid_as_evaluated', 'rfqs.proposed_bid', 'rfqs.bid_as_evaluated', 'rfqs.discount', 'contractors.business_name', 'contractors.owner', 'contractors.address')
      ->where([['procacts.plan_id', $plan_id], ['procacts.open_bid', $date_opened], ['contractors.contractor_id', $contractor_id]])
      ->whereIn('project_bidders.bid_status', $status)
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->first();

    if ($bidder == null) {
      $bidder = DB::table('bid_doc_projects')
        ->select(DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), 'twg_evaluations.twg_final_bid_evaluation', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), 'project_bidders.project_bid', 'project_bidders.bid_status', 'bid_doc_projects.bid_doc_project_id', 'bid_doc_projects.detailed_bid_as_read', 'bid_doc_projects.detailed_bid_as_evaluated', 'bid_docs.proposed_bid', 'bid_docs.bid_as_evaluated', 'bid_docs.discount', 'contractors.business_name', 'contractors.owner', 'contractors.address')
        ->where([['procacts.plan_id', $plan_id], ['procacts.open_bid', $date_opened], ['contractors.contractor_id', $contractor_id]])
        ->whereIn('project_bidders.bid_status', $status)
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
        ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
        ->first();
    }
    return $bidder;
  }

  public function getBidEvalBiddersData($procact_id, $status)
  {
    $bidder_error = false;
    $status = explode(",", $status);

    $bidders = DB::table('rfq_projects')
      ->select(DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), 'project_bidders.project_bid', 'rfqs.bid_in_words', 'rfqs.date_received', 'rfqs.time_received', 'project_bidders.bid_status', 'rfq_projects.rfq_project_id', 'rfq_projects.detailed_bid_as_read', 'rfq_projects.detailed_bid_as_evaluated', 'rfqs.proposed_bid', 'rfqs.amount_of_discount', 'rfqs.bid_as_evaluated', 'rfqs.discount', 'rfqs.discount_source', 'contractors.business_name', 'contractors.address', 'contractors.owner')
      ->where([['procacts.procact_id', $procact_id]])
      ->whereIn('project_bidders.bid_status', $status)
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->orderBy('minimum_cost', 'asc')
      ->get();

    if (count($bidders) == 0) {
      $bidders = DB::table('bid_doc_projects')
        ->select(DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), 'bid_docs.date_received', 'bid_docs.bid_in_words', 'bid_docs.time_received', 'project_bidders.bid_status', 'project_bidders.project_bid', 'bid_doc_projects.bid_doc_project_id', 'bid_doc_projects.detailed_bid_as_read', 'bid_doc_projects.detailed_bid_as_evaluated', 'bid_docs.proposed_bid', 'bid_docs.amount_of_discount', 'bid_docs.bid_as_evaluated', 'bid_docs.discount', 'bid_docs.discount_source', 'contractors.business_name', 'contractors.address', 'contractors.owner')
        ->where([['procacts.procact_id', $procact_id]])
        ->whereIn('project_bidders.bid_status', $status)
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
        ->orderBy('minimum_cost', 'asc')
        ->get();
    }

    return $bidders;
  }

  public function getAllTakers($procact_id)
  {
    $bidder_error = false;
    $bidders = DB::table('rfqs')
      ->select('rfqs.rfq_id  as main_id', 'rfqs.date_released', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), 'project_bidders.project_bid', 'rfqs.bid_in_words', 'rfqs.date_received', 'rfqs.time_received', 'project_bidders.bid_status', 'rfq_projects.rfq_project_id', 'rfq_projects.detailed_bid_as_read', 'rfq_projects.detailed_bid_as_evaluated', 'rfqs.proposed_bid', 'rfqs.bid_as_evaluated', 'rfqs.discount', 'rfqs.discount_source', 'contractors.business_name', 'contractors.owner')
      ->where([['procacts.procact_id', $procact_id]])
      ->join('rfq_projects', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->leftJoin('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->orderBy('minimum_cost', 'asc')
      ->get();

    if (count($bidders) == 0) {
      $bidders = DB::table('bid_docs')
        ->select('bid_docs.bid_doc_id  as main_id', 'bid_docs.date_released', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), 'bid_docs.date_received', 'bid_docs.bid_in_words', 'bid_docs.time_received', 'project_bidders.bid_status', 'project_bidders.project_bid', 'bid_doc_projects.bid_doc_project_id', 'bid_doc_projects.detailed_bid_as_read', 'bid_doc_projects.detailed_bid_as_evaluated', 'bid_docs.proposed_bid', 'bid_docs.bid_as_evaluated', 'bid_docs.discount', 'bid_docs.discount_source', 'contractors.business_name', 'contractors.owner')
        ->where([['procacts.procact_id', $procact_id]])
        ->join('bid_doc_projects', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->leftJoin('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->orderBy('minimum_cost', 'asc')
        ->get();
    }

    return $bidders;
  }

  public function getBiddersToVerify($procact_id)
  {
    $bidder_error = false;
    $rfqs = DB::table('rfq_projects')->select('rfq_projects.rfq_project_id')
      ->where([['procacts.procact_id', $procact_id], ['twg_evaluations.twg_evaluation_status', '<>', null], ['project_bidders.bid_status', 'active']])
      ->whereIn('twg_evaluation_status', ['responsive', 'non-responsive'])
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->count();

    $bid_docs = DB::table('bid_doc_projects')->select('bid_doc_projects.bid_doc_project_id')
      ->where([['procacts.procact_id', $procact_id], ['twg_evaluations.twg_evaluation_status', '<>', null], ['project_bidders.bid_status', 'active']])
      ->whereIn('twg_evaluation_status', ['responsive', 'non-responsive'])
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->count();

    $total = $rfqs + $bid_docs;
    return $total;
  }



  public function getActiveBidders($procact_id, $status)
  {
    $bidder_error = false;
    $status = explode(",", $status);

    $rfqs = DB::table('rfq_projects')->select('rfq_projects.rfq_project_id')->where([['procacts.procact_id', $procact_id]])
      ->whereIn('project_bidders.bid_status', $status)
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->count();

    $bid_docs = DB::table('bid_doc_projects')->select('bid_doc_projects.bid_doc_project_id')->where([['procacts.procact_id', $procact_id]])
      ->whereIn('project_bidders.bid_status', $status)
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->count();

    $total = $rfqs + $bid_docs;



    return $total;
  }

  public function getBiddersTWGBased($procact_id, $status)
  {
    $bidder_error = false;
    $status = explode(",", $status);

    $bidders = DB::table('rfq_projects')
      ->select('project_bidders.project_bid', 'project_bidders.bid_status', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), 'rfq_projects.rfq_project_id', 'rfq_projects.detailed_bid_as_read', 'rfq_projects.detailed_bid_as_evaluated', 'rfqs.proposed_bid', 'rfqs.bid_as_evaluated', 'contractors.business_name', 'twg_evaluations.twg_final_bid_evaluation')
      ->where([['procacts.procact_id', $procact_id]])
      ->whereIn('twg_evaluations.twg_evaluation_status', $status)
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->orderBy('minimum_cost', 'asc')
      ->get();

    if (count($bidders) === 0) {
      $bidders = DB::table('bid_doc_projects')
        ->select('project_bidders.project_bid', 'project_bidders.bid_status', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), 'bid_doc_projects.bid_doc_project_id', 'bid_doc_projects.detailed_bid_as_read', 'bid_doc_projects.detailed_bid_as_evaluated', 'bid_docs.proposed_bid', 'bid_docs.bid_as_evaluated', 'contractors.business_name', 'twg_evaluations.twg_final_bid_evaluation')
        ->where([['procacts.procact_id', $procact_id]])
        ->whereIn('twg_evaluations.twg_evaluation_status', $status)
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
        ->orderBy('minimum_cost', 'asc')
        ->get();
    }

    return $bidders;
  }

  public function getActiveBiddersWithAmount($procact_id)
  {
    $bidder_error = false;

    $rfqs = DB::table('rfq_projects')->select('rfq_projects.rfq_project_id')->where([['procacts.procact_id', $procact_id], ['project_bidders.bid_status', 'active'], ['rfqs.proposed_bid', '>', 0]])
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('rfqs', 'rfq_projects.rfq_id', 'rfqs.rfq_id')
      ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')->count();

    $bid_docs = DB::table('bid_doc_projects')->select('bid_doc_projects.bid_doc_project_id')->where([['procacts.procact_id', $procact_id], ['project_bidders.bid_status', 'active'], ['bid_docs.proposed_bid', '>', 0]])
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('bid_docs', 'bid_doc_projects.bid_doc_id', 'bid_docs.bid_doc_id')
      ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')->count();

    $total = $rfqs + $bid_docs;

    if ($total < 1) {
      $bidder_error = true;
    }

    return $bidder_error;
  }

  public function getAllCurrentBidders($id)
  {
    $data = array('title' => '', 'project_bidders' => '', 'abc' => '', 'open_bid' => '', "mode" => '', 'detailed_bids' => '');
    $latest_procact = DB::table('project_plans')->where('project_plans.plan_id', $id)->join('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();
    $mode = $latest_procact->mode_id;
    $procact_id = $latest_procact->procact_id;

    if ($mode == 1) {
      $project_bidders = DB::table('bid_doc_projects')
        ->select(DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), 'bid_doc_projects.*', 'bid_docs.*', 'contractors.*', 'project_bidders.*')
        ->where([['bid_doc_projects.procact_id', $procact_id]])
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        // ->orderBy('project_bidders.bid_status','asc')
        ->orderBy('minimum_cost', 'asc')
        // ->orderBy('bid_docs.date_received','asc')
        ->get();
    } else {
      $project_bidders = DB::table('rfq_projects')
        ->select(DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), 'rfq_projects.*', 'rfqs.*', 'contractors.*', 'project_bidders.*')
        ->where([['rfq_projects.procact_id', $procact_id]])
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        // ->orderBy('project_bidders.bid_status','asc')
        ->orderBy('minimum_cost', 'asc')
        // ->orderBy('rfqs.date_received','asc')
        ->get();
    }


    if ($latest_procact->current_cluster != null) {
      $titles = DB::table('project_plans')->select(DB::raw('group_concat(project_title separator " *** ") as titles'))->where([['current_cluster', $latest_procact->current_cluster], ['current_cluster', '<>', null]])->get();
      $title = $titles[0]->titles;
      $title2 = $latest_procact->project_title;
      $project_cost = DB::table('project_plans')->where('current_cluster', $latest_procact->current_cluster)->sum('project_plans.project_cost');
      $open_bid = $latest_procact->open_bid;
      $project_number = $latest_procact->project_no;
    } else {
      $title = $latest_procact->project_title;
      $title2 = $latest_procact->project_title;
      $project_cost = $latest_procact->project_cost;
      $open_bid = $latest_procact->open_bid;
      $project_number = $latest_procact->project_no;
    }

    if ($open_bid == null) {
      $timeline = DB::table('project_timelines')->where('procact_id', $latest_procact->latest_procact_id)->first();
      $open_bid = $timeline->bid_submission_start;
    }

    $data['title'] = $title;
    $data['title2'] = $title2;
    $data['project_bidders'] = $project_bidders;
    $data['project_cost'] = $project_cost;
    $data['open_bid'] = $open_bid;
    $data['project_number'] = $project_number;
    $data['mode'] = $latest_procact->mode_id;
    if ($latest_procact->current_cluster != null) {
      $procacts_array = [];
      $procacts_objects = DB::table('project_plans')
        ->select('latest_procact_id')
        ->where([['current_cluster', $latest_procact->current_cluster], ['current_cluster', '<>', null]])->get();
      foreach ($procacts_objects as $procacts_object) {
        array_push($procacts_array, $procacts_object->latest_procact_id);
      }

      if ($mode === 1) {
        $detailed_bids = DB::table('bid_doc_projects')
          ->select('*', 'project_bidders.project_bid as bid_id')
          ->whereIn('bid_doc_projects.procact_id', $procacts_array)
          ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
          ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
          ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
          ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
          ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
          // ->orderBy('project_bidders.bid_status','asc')
          ->orderBy('bid_docs.bid_as_evaluated', 'asc')
          ->orderBy('bid_docs.date_received', 'asc')
          ->get();
      } else {
        $detailed_bids = DB::table('rfq_projects')
          ->select('*', 'project_bidders.project_bid as bid_id')
          ->whereIn('rfq_projects.procact_id', $procacts_array)
          ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
          ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
          ->join('project_plans', 'procacts.procact_id', 'project_plans.latest_procact_id')
          ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
          ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
          // ->orderBy('project_bidders.bid_status','asc')
          ->orderBy('rfqs.bid_as_evaluated', 'asc')
          ->orderBy('rfqs.date_received', 'asc')
          ->get();
      }
      $data['detailed_bids'] = $detailed_bids;
    } else {
      $data['detailed_bids'] = null;
    }

    return $data;
  }

  public function getAllBidders($id)
  {
    $data = array('title' => '', 'project_bidders' => '', 'abc' => '', 'open_bid' => '');
    $latest_procact = DB::table('project_plans')->where('project_plans.plan_id', $id)
      ->join('procacts', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();
    $mode = $latest_procact->mode_id;
    $procact_id = $latest_procact->procact_id;
    $procacts = DB::table('procacts')->select(DB::raw('group_concat(procact_id separator ",") as procact_ids'))->where('plan_id', $id)->first();
    $procacts = explode(',', $procacts->procact_ids);

    $project_bidders_temp = [];

    $project_bidders1 = DB::table('bid_doc_projects')
      ->select(
        DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"),
        DB::raw("MAX(project_bidder_notices.project_bidder_notice_id)"),
        DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"),
        "bid_docs.*",
        "bid_doc_projects.*",
        "procacts.*",
        "contractors.*",
        "project_bidder_notices.*",
        "project_bidder_notices.project_bidder_notice_id",
        "project_bidder_notices.notice_type",
        "project_bidder_notices.date_received as notice_date_received",
        "project_bidder_notices.date_generated as notice_date_generated",
        "project_bidder_notices.date_released as notice_date_released",
        "project_bidder_notices.notice_remarks",
        "project_bidder_notices.date_released as notice_released",
        "twg_evaluations.twg_final_bid_evaluation",
        "twg_evaluations.twg_evaluation_status",
        "twg_evaluations.post_qual_start",
        "twg_evaluations.post_qual_end",
        "twg_evaluations.twg_evaluation_remarks",
        "procurement_modes.mode",
        "twg_evaluations.updated_at as date_updated",
        "project_bidders.*"
      )
      ->whereIn('procacts.procact_id', $procacts)
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
      ->leftJoin('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->leftJoin('project_bidder_notices', 'project_bidders.project_bid', 'project_bidder_notices.project_bid')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->orderBy('procacts.procact_id', 'asc')
      ->orderBy('minimum_cost', 'asc')
      ->orderBy('project_bidder_notices.project_bidder_notice_id', 'desc')
      ->groupBy('project_bidders.project_bid')
      ->get();


    $project_bidders2 = DB::table('rfq_projects')
      ->select(
        DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"),
        DB::raw("MAX(project_bidder_notices.project_bidder_notice_id)"),
        DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"),
        "rfqs.*",
        "rfq_projects.*",
        "procacts.*",
        "contractors.*",
        "project_bidder_notices.*",
        "project_bidder_notices.project_bidder_notice_id",
        "project_bidder_notices.notice_type",
        "project_bidder_notices.date_received as notice_date_received",
        "project_bidder_notices.date_generated as notice_date_generated",
        "project_bidder_notices.date_released as notice_date_released",
        "project_bidder_notices.notice_remarks",
        "project_bidder_notices.date_released as notice_released",
        "twg_evaluations.twg_final_bid_evaluation",
        "twg_evaluations.twg_evaluation_status",
        "twg_evaluations.post_qual_start",
        "twg_evaluations.post_qual_end",
        "twg_evaluations.twg_evaluation_remarks",
        "procurement_modes.mode",
        "twg_evaluations.updated_at as date_updated",
        "project_bidders.*"
      )
      ->whereIn('procacts.procact_id', $procacts)
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->join('procurement_modes', 'procurement_modes.mode_id', 'procacts.procact_mode_id')
      ->leftJoin('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->leftJoin('project_bidder_notices', 'project_bidders.project_bid', 'project_bidder_notices.project_bid')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->orderBy('procacts.procact_id', 'asc')
      ->orderBy('minimum_cost', 'asc')
      ->orderBy('project_bidder_notices.project_bidder_notice_id', 'desc')
      ->groupBy('project_bidders.project_bid')
      ->get();

    $project_bidders = [];

    if (count($project_bidders1) >= 1 && count($project_bidders2) >= 1) {
      $project_bidders = array_merge((array) json_decode($project_bidders1), (array) json_decode($project_bidders2));
    } else if (count($project_bidders1) === 0 && count($project_bidders2) >= 1) {
      $project_bidders = $project_bidders2;
    } else if (count($project_bidders1) >= 1 && count($project_bidders2) === 0) {
      $project_bidders = $project_bidders1;
    } else {
    }

    foreach ($project_bidders as $project_bidder) {
      $array = (array)$project_bidder;
      $bidding_order = array_search($project_bidder->procact_id, $procacts) + 1;
      $bidding_order = $bidding_order . date("S", mktime(0, 0, 0, 0, $bidding_order, 0)) . " Procurement";
      $array = array_merge($array, array("bid_order" => $bidding_order));
      array_push($project_bidders_temp, (object)$array);
    }
    $project_bidders = $project_bidders_temp;

    return $project_bidders;
  }

  public function getBiddersDisqualificationRecords($id)
  {
    $data = array('title' => '', 'project_bidders' => '', 'abc' => '', 'open_bid' => '');
    $disqualifications_bid_docs = DB::table('bid_doc_projects')
      ->select("*", 'disqualification_records.remarks as disqualification_remarks', 'disqualification_records.updated_at as date_updated')
      ->where([['project_plans.plan_id', $id]])
      ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
      ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
      ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
      ->join('disqualification_records', 'project_bidders.project_bid', 'disqualification_records.project_bid')
      ->join('users', 'disqualification_records.user_id', 'users.id')
      ->orderBy('disqualification_records.record_id', 'desc')
      ->get();

    $disqualifications_rfq = DB::table('rfq_projects')
      ->select("*", 'disqualification_records.remarks as disqualification_remarks', 'disqualification_records.updated_at as date_updated')
      ->where([['project_plans.plan_id', $id]])
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('disqualification_records', 'project_bidders.project_bid', 'disqualification_records.project_bid')
      ->join('users', 'disqualification_records.user_id', 'users.id')
      ->orderBy('disqualification_records.record_id', 'desc')
      ->get();


    $disqualifications = [];

    if (count($disqualifications_bid_docs) >= 1 && count($disqualifications_rfq) >= 1) {
      $disqualifications = array_merge((array) json_decode($disqualifications_bid_docs), (array) json_decode($disqualifications_rfq));
    } else if (count($disqualifications_bid_docs) === 0 && count($disqualifications_rfq) >= 1) {
      $disqualifications = $disqualifications_rfq;
    } else if (count($disqualifications_bid_docs) >= 1 && count($disqualifications_rfq) === 0) {
      $disqualifications = $disqualifications_bid_docs;
    } else {
    }

    return $disqualifications;
  }

  public function getWinner($id)
  {
    $latest_procact = DB::table('procacts')->where('project_plans.plan_id', $id)->join('project_plans', 'project_plans.latest_procact_id', 'procacts.procact_id')->first();
    $mode = $latest_procact->mode_id;
    $procact_id = $latest_procact->procact_id;
    if ($mode == 1) {
      $project_bidders = DB::table('bid_doc_projects')
        ->where([['bid_doc_projects.procact_id', $procact_id], ['project_bidders.bid_status', 'active']])
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->orderBy('bid_docs.bid_as_evaluated', 'asc')
        ->orderBy('bid_docs.date_received', 'asc')
        ->first();
    } else {

      $project_bidders = DB::table('rfq_projects')->where([['rfq_projects.procact_id', $procact_id], ['project_bidders.bid_status', 'active']])
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
        ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
        ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->orderBy('project_bidders.bid_status', 'asc')
        ->orderBy('rfqs.bid_as_evaluated', 'asc')
        ->first();
    }

    return $project_bidders;
  }


  public function extendSpecificProcess($ids, $process, $date, $remarks)
  {
    $ids_array = explode(",", $ids);
    $message = "success";

    $range_error = false;


    // Check
    foreach ($ids_array as $id) {


      $project_timeline = DB::table('project_timelines')
        ->select("project_plans.current_cluster", "project_timelines.*", DB::raw("DATEDIFF(project_timelines.post_qualification_end, project_timelines.post_qualification_start) as post_qual_interval"))
        ->where("project_plans.plan_id", $id)
        ->join("procacts", "project_timelines.procact_id", "procacts.procact_id")
        ->join("project_plans", "project_plans.latest_procact_id", "procacts.procact_id")
        ->first();

      if ($process === "advertisement_posting") {
        if (date("Y-m-d", strtotime($date)) < date("Y-m-d", strtotime($project_timeline->advertisement_end))) {
          $range_error = true;
        }
        if ($project_timeline->current_cluster != null) {
          $clusters = DB::table("project_plans")->select("plan_id")->where([["current_cluster", $project_timeline->current_cluster], ["plan_id", "<>", $id]])->get();
          foreach ($clusters as $cluster) {
            if (in_array($cluster->plan_id, $ids_array) === false) {
              array_push($ids_array, $cluster->plan_id);
            }
          }
        }
      }
      if ($process === "pre_bid") {
        if (date("Y-m-d", strtotime($date)) <= date("Y-m-d", strtotime($project_timeline->pre_bid_end))) {
          $range_error = true;
        }
        if ($project_timeline->current_cluster != null) {
          $clusters = DB::table("project_plans")->select("plan_id")->where([["current_cluster", $project_timeline->current_cluster], ["plan_id", "<>", $id]])->get();
          foreach ($clusters as $cluster) {
            if (in_array($cluster->plan_id, $ids_array) === false) {
              array_push($ids_array, $cluster->plan_id);
            }
          }
        }
      }
      if ($process === "submission_opening") {
        if (date("Y-m-d", strtotime($date)) <= date("Y-m-d", strtotime($project_timeline->bid_submission_end))) {
          $range_error = true;
        }
        if ($project_timeline->current_cluster != null) {
          $clusters = DB::table("project_plans")->select("plan_id")->where([["current_cluster", $project_timeline->current_cluster], ["plan_id", "<>", $id]])->get();
          foreach ($clusters as $cluster) {
            if (in_array($cluster->plan_id, $ids_array) === false) {
              array_push($ids_array, $cluster->plan_id);
            }
          }
        }
      }
      if ($process === "bid_evaluation") {
        if (date("Y-m-d", strtotime($date)) < date("Y-m-d", strtotime($project_timeline->bid_submission_end))) {
          $range_error = true;
        }
      }
      if ($process === "post_qualification") {
        if (date("Y-m-d", strtotime($date)) <= date("Y-m-d", strtotime($project_timeline->post_qualification_end))) {
          // $range_error=true;
        }
      }
      if ($process === "notice_of_award") {
        if (date("Y-m-d", strtotime($date)) <= date("Y-m-d", strtotime($project_timeline->award_notice_end))) {
          // $range_error=true;
        }
      }
      if ($process === "contract_preparation_signing") {
        if (date("Y-m-d", strtotime($date)) <= date("Y-m-d", strtotime($project_timeline->contract_signing_end))) {
          // $range_error=true;
        }
      }
      if ($process === "approval_by_higher_authority") {
        if (date("Y-m-d", strtotime($date)) <= date("Y-m-d", strtotime($project_timeline->authority_approval_end))) {
          $range_error = true;
        }
      }
      if ($process === "notice_to_proceed") {
        if (date("Y-m-d", strtotime($date)) <= date("Y-m-d", strtotime($project_timeline->award_notice_end))) {
          $range_error = true;
        }
      }
    }

    if ($range_error) {
      $message = "less_range";
    } else {
      foreach ($ids_array as $id) {
        $project_timeline = DB::table('project_timelines')
          ->select("project_timelines.*", DB::raw("DATEDIFF(project_timelines.post_qualification_end, project_timelines.post_qualification_start) as post_qual_interval"))
          ->where("project_plans.plan_id", $id)
          ->join("procacts", "project_timelines.procact_id", "procacts.procact_id")
          ->join("project_plans", "project_plans.latest_procact_id", "procacts.procact_id")
          ->first();

        if ($process === "advertisement_posting") {
          $process_name = "Advertisement Posting";
          $old_date = $project_timeline->advertisement_end;
          $advertisement_start = $project_timeline->advertisement_start;
          $advertisement_end = date("Y-m-d", strtotime($date));
          $pre_bid_start = date("Y-m-d", strtotime($advertisement_end));
          $pre_bid_days = date_diff(date_create($project_timeline->advertisement_end), date_create($project_timeline->pre_bid_end))->days;
          $pre_bid_end = date("Y-m-d", strtotime($pre_bid_start . "+" . $pre_bid_days . " days"));
          $pre_bid_start = date("Y-m-d", strtotime($pre_bid_start . "+" . $pre_bid_days . " days"));
          $bid_submission_start = date("Y-m-d", strtotime($pre_bid_end));
          $bid_submission_days = date_diff(date_create($project_timeline->bid_submission_start), date_create($project_timeline->pre_bid_end))->days;
          $bid_submission_end = date("Y-m-d", strtotime($bid_submission_start . "+" . $bid_submission_days . " days"));
          $bid_submission_start = date("Y-m-d", strtotime($bid_submission_start . "+" . $bid_submission_days . " days"));
          $bid_evaluation_start = date("Y-m-d", strtotime($bid_submission_end));
          $bid_evaluation_days = date_diff(date_create($project_timeline->bid_submission_end), date_create($project_timeline->bid_evaluation_end))->days;
          $bid_evaluation_end = date("Y-m-d", strtotime($bid_evaluation_start . "+" . $bid_evaluation_days . " days"));
          $bid_evaluation_start = date("Y-m-d", strtotime($bid_evaluation_start . "+" . $bid_evaluation_days . " days"));
          $post_qualification_start = date("Y-m-d", strtotime($bid_evaluation_end . "+1 days"));
          $post_qualification_days = date_diff(date_create($project_timeline->post_qualification_start), date_create($project_timeline->post_qualification_end))->days;
          $post_qualification_end = date("Y-m-d", strtotime($post_qualification_start . "+" . $post_qualification_days . " days"));
          $award_notice_start = date("Y-m-d", strtotime($post_qualification_end . "+1 days"));
          $award_days = date_diff(date_create($project_timeline->award_notice_start), date_create($project_timeline->award_notice_end))->days;
          $award_notice_end = date("Y-m-d", strtotime($award_notice_start . "+" . $award_days . " days"));
          $contract_signing_start = date("Y-m-d", strtotime($award_notice_end . "+1 days"));
          $contract_signing_days = date_diff(date_create($project_timeline->contract_signing_start), date_create($project_timeline->contract_signing_end))->days;
          $contract_signing_end = date("Y-m-d", strtotime($contract_signing_start . "+" . $contract_signing_days . " days"));
          $contract_signing_start = date("Y-m-d", strtotime($contract_signing_start . "+" . $contract_signing_days . " days"));
          if ($project_timeline->authority_approval_start != null) {
            $authority_approval_start = date("Y-m-d", strtotime($contract_signing_end . "+1 days"));
            $authority_approval_days = date_diff(date_create($project_timeline->authority_approval_start), date_create($project_timeline->authority_approval_end))->days;
            $authority_approval_end = date("Y-m-d", strtotime($authority_approval_start . "+" . $authority_approval_days . " days"));
            $authority_approval_start = date("Y-m-d", strtotime($authority_approval_start . "+" . $authority_approval_days . " days"));
            $proceed_notice_start = date("Y-m-d", strtotime($authority_approval_end . "+1 days"));
          } else {
            $proceed_notice_start = date("Y-m-d", strtotime($contract_signing_end . "+1 days"));
            $authority_approval_end = null;
            $authority_approval_start = null;
          }
          $proceed_notice_days = date_diff(date_create($project_timeline->proceed_notice_start), date_create($project_timeline->proceed_notice_end))->days;
          $proceed_notice_end = date("Y-m-d", strtotime($proceed_notice_start . "+" . $proceed_notice_days . " days"));
          $proceed_notice_start = date("Y-m-d", strtotime($proceed_notice_start . "+" . $proceed_notice_days . " days"));
        } else if ($process === "pre_bid") {
          $process_name = "Pre Bid";
          $old_date = $project_timeline->pre_bid_end;
          $advertisement_start = $project_timeline->advertisement_start;
          $advertisement_end = $project_timeline->advertisement_end;
          $pre_bid_end = date("Y-m-d", strtotime($date));
          $pre_bid_start = date("Y-m-d", strtotime($date));
          $bid_submission_start = date("Y-m-d", strtotime($pre_bid_end));
          $bid_submission_days = date_diff(date_create($project_timeline->bid_submission_start), date_create($project_timeline->pre_bid_end))->days;
          $bid_submission_end = date("Y-m-d", strtotime($bid_submission_start . "+" . $bid_submission_days . " days"));
          $bid_submission_start = date("Y-m-d", strtotime($bid_submission_start . "+" . $bid_submission_days . " days"));
          $bid_evaluation_start = date("Y-m-d", strtotime($bid_submission_end));
          $bid_evaluation_days = date_diff(date_create($project_timeline->bid_submission_end), date_create($project_timeline->bid_evaluation_end))->days;
          $bid_evaluation_end = date("Y-m-d", strtotime($bid_evaluation_start . "+" . $bid_evaluation_days . " days"));
          $bid_evaluation_start = date("Y-m-d", strtotime($bid_evaluation_start . "+" . $bid_evaluation_days . " days"));
          $post_qualification_start = date("Y-m-d", strtotime($bid_evaluation_end . "+1 days"));
          $post_qualification_days = date_diff(date_create($project_timeline->post_qualification_start), date_create($project_timeline->post_qualification_end))->days;
          $post_qualification_end = date("Y-m-d", strtotime($post_qualification_start . "+" . $post_qualification_days . " days"));
          $award_notice_start = date("Y-m-d", strtotime($post_qualification_end . "+1 days"));
          $award_days = date_diff(date_create($project_timeline->award_notice_start), date_create($project_timeline->award_notice_end))->days;
          $award_notice_end = date("Y-m-d", strtotime($award_notice_start . "+" . $award_days . " days"));
          $contract_signing_start = date("Y-m-d", strtotime($award_notice_end . "+1 days"));
          $contract_signing_days = date_diff(date_create($project_timeline->contract_signing_start), date_create($project_timeline->contract_signing_end))->days;
          $contract_signing_end = date("Y-m-d", strtotime($contract_signing_start . "+" . $contract_signing_days . " days"));
          $contract_signing_start = date("Y-m-d", strtotime($contract_signing_start . "+" . $contract_signing_days . " days"));
          if ($project_timeline->authority_approval_start != null) {
            $authority_approval_start = date("Y-m-d", strtotime($contract_signing_end . "+1 days"));
            $authority_approval_days = date_diff(date_create($project_timeline->authority_approval_start), date_create($project_timeline->authority_approval_end))->days;
            $authority_approval_end = date("Y-m-d", strtotime($authority_approval_start . "+" . $authority_approval_days . " days"));
            $authority_approval_start = date("Y-m-d", strtotime($authority_approval_start . "+" . $authority_approval_days . " days"));
            $proceed_notice_start = date("Y-m-d", strtotime($authority_approval_end . "+1 days"));
          } else {
            $proceed_notice_start = date("Y-m-d", strtotime($contract_signing_end . "+1 days"));
            $authority_approval_end = null;
            $authority_approval_start = null;
          }
          $proceed_notice_days = date_diff(date_create($project_timeline->proceed_notice_start), date_create($project_timeline->proceed_notice_end))->days;
          $proceed_notice_end = date("Y-m-d", strtotime($proceed_notice_start . "+" . $proceed_notice_days . " days"));
          $proceed_notice_start = date("Y-m-d", strtotime($proceed_notice_start . "+" . $proceed_notice_days . " days"));
        } else if ($process === "submission_opening") {
          $process_name = "Bid Submission and Opening";
          $old_date = $project_timeline->bid_submission_end;
          $advertisement_start = $project_timeline->advertisement_start;
          $advertisement_end = $project_timeline->advertisement_end;
          $pre_bid_start = $project_timeline->pre_bid_start;
          $pre_bid_end = $project_timeline->pre_bid_end;
          $bid_submission_start = date("Y-m-d", strtotime($date));
          $bid_submission_end = date("Y-m-d", strtotime($date));
          $bid_evaluation_start = date("Y-m-d", strtotime($bid_submission_end));
          $bid_evaluation_days = date_diff(date_create($project_timeline->bid_submission_end), date_create($project_timeline->bid_evaluation_end))->days;
          $bid_evaluation_end = date("Y-m-d", strtotime($bid_evaluation_start . "+" . $bid_evaluation_days . " days"));
          $bid_evaluation_start = date("Y-m-d", strtotime($bid_evaluation_start . "+" . $bid_evaluation_days . " days"));
          $post_qualification_start = date("Y-m-d", strtotime($bid_evaluation_end . "+1 days"));
          $post_qualification_days = date_diff(date_create($project_timeline->post_qualification_start), date_create($project_timeline->post_qualification_end))->days;
          $post_qualification_end = date("Y-m-d", strtotime($post_qualification_start . "+" . $post_qualification_days . " days"));
          $award_notice_start = date("Y-m-d", strtotime($post_qualification_end . "+1 days"));
          $award_days = date_diff(date_create($project_timeline->award_notice_start), date_create($project_timeline->award_notice_end))->days;
          $award_notice_end = date("Y-m-d", strtotime($award_notice_start . "+" . $award_days . " days"));
          $contract_signing_start = date("Y-m-d", strtotime($award_notice_end . "+1 days"));
          $contract_signing_days = date_diff(date_create($project_timeline->contract_signing_start), date_create($project_timeline->contract_signing_end))->days;
          $contract_signing_end = date("Y-m-d", strtotime($contract_signing_start . "+" . $contract_signing_days . " days"));
          $contract_signing_start = date("Y-m-d", strtotime($contract_signing_start . "+" . $contract_signing_days . " days"));
          if ($project_timeline->authority_approval_start != null) {
            $authority_approval_start = date("Y-m-d", strtotime($contract_signing_end . "+1 days"));
            $authority_approval_days = date_diff(date_create($project_timeline->authority_approval_start), date_create($project_timeline->authority_approval_end))->days;
            $authority_approval_end = date("Y-m-d", strtotime($authority_approval_start . "+" . $authority_approval_days . " days"));
            $authority_approval_start = date("Y-m-d", strtotime($authority_approval_start . "+" . $authority_approval_days . " days"));
            $proceed_notice_start = date("Y-m-d", strtotime($authority_approval_end . "+1 days"));
          } else {
            $proceed_notice_start = date("Y-m-d", strtotime($contract_signing_end . "+1 days"));
            $authority_approval_end = null;
            $authority_approval_start = null;
          }
          $proceed_notice_days = date_diff(date_create($project_timeline->proceed_notice_start), date_create($project_timeline->proceed_notice_end))->days;
          $proceed_notice_end = date("Y-m-d", strtotime($proceed_notice_start . "+" . $proceed_notice_days . " days"));
          $proceed_notice_start = date("Y-m-d", strtotime($proceed_notice_start . "+" . $proceed_notice_days . " days"));
        } else if ($process === "bid_evaluation") {
          $process_name = "Bid Evaluation";
          $old_date = $project_timeline->bid_evaluation_end;
          $advertisement_start = $project_timeline->advertisement_start;
          $advertisement_end = $project_timeline->advertisement_end;
          $pre_bid_start = $project_timeline->pre_bid_start;
          $pre_bid_end = $project_timeline->pre_bid_end;
          $bid_submission_start = $project_timeline->bid_submission_start;
          $bid_submission_end = $project_timeline->bid_submission_end;
          $bid_evaluation_start = date("Y-m-d", strtotime($date));
          $bid_evaluation_end = date("Y-m-d", strtotime($date));
          $post_qualification_start = date("Y-m-d", strtotime($bid_evaluation_end . "+1 days"));
          $post_qualification_days = date_diff(date_create($project_timeline->post_qualification_start), date_create($project_timeline->post_qualification_end))->days;
          $post_qualification_end = date("Y-m-d", strtotime($post_qualification_start . "+" . $post_qualification_days . " days"));
          $award_notice_start = date("Y-m-d", strtotime($post_qualification_end . "+1 days"));
          $award_days = date_diff(date_create($project_timeline->award_notice_start), date_create($project_timeline->award_notice_end))->days;
          $award_notice_end = date("Y-m-d", strtotime($award_notice_start . "+" . $award_days . " days"));
          $contract_signing_start = date("Y-m-d", strtotime($award_notice_end . "+1 days"));
          $contract_signing_days = date_diff(date_create($project_timeline->contract_signing_start), date_create($project_timeline->contract_signing_end))->days;
          $contract_signing_end = date("Y-m-d", strtotime($contract_signing_start . "+" . $contract_signing_days . " days"));
          $contract_signing_start = date("Y-m-d", strtotime($contract_signing_start . "+" . $contract_signing_days . " days"));
          if ($project_timeline->authority_approval_start != null) {
            $authority_approval_start = date("Y-m-d", strtotime($contract_signing_end . "+1 days"));
            $authority_approval_days = date_diff(date_create($project_timeline->authority_approval_start), date_create($project_timeline->authority_approval_end))->days;
            $authority_approval_end = date("Y-m-d", strtotime($authority_approval_start . "+" . $authority_approval_days . " days"));
            $authority_approval_start = date("Y-m-d", strtotime($authority_approval_start . "+" . $authority_approval_days . " days"));
            $proceed_notice_start = date("Y-m-d", strtotime($authority_approval_end . "+1 days"));
          } else {
            $proceed_notice_start = date("Y-m-d", strtotime($contract_signing_end . "+1 days"));
            $authority_approval_end = null;
            $authority_approval_start = null;
          }
          $proceed_notice_days = date_diff(date_create($project_timeline->proceed_notice_start), date_create($project_timeline->proceed_notice_end))->days;
          $proceed_notice_end = date("Y-m-d", strtotime($proceed_notice_start . "+" . $proceed_notice_days . " days"));
          $proceed_notice_start = date("Y-m-d", strtotime($proceed_notice_start . "+" . $proceed_notice_days . " days"));
        } else if ($process === "post_qualification") {
          $new_date_mdy = date("Y-m-d", strtotime($date));
          if ($project_timeline->post_qualification_end == $new_date_mdy) {
            return "equal_error";
          }
          $process_name = "Post Qualification";
          $old_date = $project_timeline->post_qualification_end;
          $advertisement_start = $project_timeline->advertisement_start;
          $advertisement_end = $project_timeline->advertisement_end;
          $pre_bid_start = $project_timeline->pre_bid_start;
          $pre_bid_end = $project_timeline->pre_bid_end;
          $bid_submission_start = $project_timeline->bid_submission_start;
          $bid_submission_end = $project_timeline->bid_submission_end;
          $bid_evaluation_start = $project_timeline->bid_evaluation_start;
          $bid_evaluation_end = $project_timeline->bid_evaluation_end;
          $post_qualification_start = $project_timeline->post_qualification_start;
          $post_qualification_end = date("Y-m-d", strtotime($date));
          $award_notice_start = date("Y-m-d", strtotime($post_qualification_end . "+1 days"));
          $award_days = date_diff(date_create($project_timeline->award_notice_start), date_create($project_timeline->award_notice_end))->days;
          $award_notice_end = date("Y-m-d", strtotime($award_notice_start . "+" . $award_days . " days"));
          $contract_signing_start = date("Y-m-d", strtotime($award_notice_end . "+1 days"));
          $contract_signing_days = date_diff(date_create($project_timeline->contract_signing_start), date_create($project_timeline->contract_signing_end))->days;
          $contract_signing_end = date("Y-m-d", strtotime($contract_signing_start . "+" . $contract_signing_days . " days"));
          $contract_signing_start = date("Y-m-d", strtotime($contract_signing_start . "+" . $contract_signing_days . " days"));
          if ($project_timeline->authority_approval_start != null) {
            $authority_approval_start = date("Y-m-d", strtotime($contract_signing_end . "+1 days"));
            $authority_approval_days = date_diff(date_create($project_timeline->authority_approval_start), date_create($project_timeline->authority_approval_end))->days;
            $authority_approval_end = date("Y-m-d", strtotime($authority_approval_start . "+" . $authority_approval_days . " days"));
            $authority_approval_start = date("Y-m-d", strtotime($authority_approval_start . "+" . $authority_approval_days . " days"));
            $proceed_notice_start = date("Y-m-d", strtotime($authority_approval_end . "+1 days"));
          } else {
            $proceed_notice_start = date("Y-m-d", strtotime($contract_signing_end . "+1 days"));
            $authority_approval_end = null;
            $authority_approval_start = null;
          }
          $proceed_notice_days = date_diff(date_create($project_timeline->proceed_notice_start), date_create($project_timeline->proceed_notice_end))->days;
          $proceed_notice_end = date("Y-m-d", strtotime($proceed_notice_start . "+" . $proceed_notice_days . " days"));
          $proceed_notice_start = date("Y-m-d", strtotime($proceed_notice_start . "+" . $proceed_notice_days . " days"));
        } else if ($process === "notice_of_award") {
          $process_name = "Notice of Award";
          $old_date = $project_timeline->award_notice_end;
          $advertisement_start = $project_timeline->advertisement_start;
          $advertisement_end = $project_timeline->advertisement_end;
          $pre_bid_start = $project_timeline->pre_bid_start;
          $pre_bid_end = $project_timeline->pre_bid_end;
          $bid_submission_start = $project_timeline->bid_submission_start;
          $bid_submission_end = $project_timeline->bid_submission_end;
          $bid_evaluation_start = $project_timeline->bid_evaluation_start;
          $bid_evaluation_end = $project_timeline->bid_evaluation_end;
          $post_qualification_start = $project_timeline->post_qualification_start;
          $post_qualification_end = $project_timeline->post_qualification_end;
          $award_notice_start = $project_timeline->award_notice_start;
          $award_notice_end = date("Y-m-d", strtotime($date));
          $contract_signing_start = date("Y-m-d", strtotime($award_notice_end . "+1 days"));
          $contract_signing_days = date_diff(date_create($project_timeline->contract_signing_start), date_create($project_timeline->contract_signing_end))->days;
          $contract_signing_end = date("Y-m-d", strtotime($contract_signing_start . "+" . $contract_signing_days . " days"));
          $contract_signing_start = date("Y-m-d", strtotime($contract_signing_start . "+" . $contract_signing_days . " days"));
          if ($project_timeline->authority_approval_start != null) {
            $authority_approval_start = date("Y-m-d", strtotime($contract_signing_end . "+1 days"));
            $authority_approval_days = date_diff(date_create($project_timeline->authority_approval_start), date_create($project_timeline->authority_approval_end))->days;
            $authority_approval_end = date("Y-m-d", strtotime($authority_approval_start . "+" . $authority_approval_days . " days"));
            $authority_approval_start = date("Y-m-d", strtotime($authority_approval_start . "+" . $authority_approval_days . " days"));
            $proceed_notice_start = date("Y-m-d", strtotime($authority_approval_end . "+1 days"));
          } else {
            $proceed_notice_start = date("Y-m-d", strtotime($contract_signing_end . "+1 days"));
            $authority_approval_end = null;
            $authority_approval_start = null;
          }
          $proceed_notice_days = date_diff(date_create($project_timeline->proceed_notice_start), date_create($project_timeline->proceed_notice_end))->days;
          $proceed_notice_end = date("Y-m-d", strtotime($proceed_notice_start . "+" . $proceed_notice_days . " days"));
          $proceed_notice_start = date("Y-m-d", strtotime($proceed_notice_start . "+" . $proceed_notice_days . " days"));
        } else if ($process === "contract_preparation_signing") {
          $process_name = "Contract Signing Preparation";
          $old_date = $project_timeline->contract_signing_end;
          $advertisement_start = $project_timeline->advertisement_start;
          $advertisement_end = $project_timeline->advertisement_end;
          $pre_bid_start = $project_timeline->pre_bid_start;
          $pre_bid_end = $project_timeline->pre_bid_end;
          $bid_submission_start = $project_timeline->bid_submission_start;
          $bid_submission_end = $project_timeline->bid_submission_end;
          $bid_evaluation_start = $project_timeline->bid_evaluation_start;
          $bid_evaluation_end = $project_timeline->bid_evaluation_end;
          $post_qualification_start = $project_timeline->post_qualification_start;
          $post_qualification_end = $project_timeline->post_qualification_end;
          $award_notice_start = $project_timeline->award_notice_start;
          $award_notice_end = $project_timeline->award_notice_end;
          $contract_signing_start = $project_timeline->contract_signing_start;
          $contract_signing_end = date("Y-m-d", strtotime($date));
          if ($project_timeline->authority_approval_start != null) {
            $authority_approval_start = date("Y-m-d", strtotime($contract_signing_end . "+1 days"));
            $authority_approval_days = date_diff(date_create($project_timeline->authority_approval_start), date_create($project_timeline->authority_approval_end))->days;
            $authority_approval_end = date("Y-m-d", strtotime($authority_approval_start . "+" . $authority_approval_days . " days"));
            $authority_approval_start = date("Y-m-d", strtotime($authority_approval_start . "+" . $authority_approval_days . " days"));
            $proceed_notice_start = date("Y-m-d", strtotime($authority_approval_end . "+1 days"));
          } else {
            $proceed_notice_start = date("Y-m-d", strtotime($contract_signing_end . "+1 days"));
            $authority_approval_end = null;
            $authority_approval_start = null;
          }
          $proceed_notice_days = date_diff(date_create($project_timeline->proceed_notice_start), date_create($project_timeline->proceed_notice_end))->days;
          $proceed_notice_end = date("Y-m-d", strtotime($proceed_notice_start . "+" . $proceed_notice_days . " days"));
          $proceed_notice_start = date("Y-m-d", strtotime($proceed_notice_start . "+" . $proceed_notice_days . " days"));
        } else if ($process === "approval_by_higher_authority") {
          $process_name = "Approval By Higher Authority";
          $old_date = $project_timeline->authority_approval_end;
          $advertisement_start = $project_timeline->advertisement_start;
          $advertisement_end = $project_timeline->advertisement_end;
          $pre_bid_start = $project_timeline->pre_bid_start;
          $pre_bid_end = $project_timeline->pre_bid_end;
          $bid_submission_start = $project_timeline->bid_submission_start;
          $bid_submission_end = $project_timeline->bid_submission_end;
          $bid_evaluation_start = $project_timeline->bid_evaluation_start;
          $bid_evaluation_end = $project_timeline->bid_evaluation_end;
          $post_qualification_start = $project_timeline->post_qualification_start;
          $post_qualification_end = $project_timeline->post_qualification_end;
          $award_notice_start = $project_timeline->award_notice_start;
          $award_notice_end = $project_timeline->award_notice_end;
          $contract_signing_start = $project_timeline->contract_signing_start;
          $contract_signing_end = $project_timeline->contract_signing_end;
          $authority_approval_start = $project_timeline->authority_approval_start;
          $authority_approval_end = date("Y-m-d", strtotime($date));
          $proceed_notice_start = date("Y-m-d", strtotime($authority_approval_end . "+1 days"));
          $proceed_notice_days = date_diff(date_create($project_timeline->proceed_notice_start), date_create($project_timeline->proceed_notice_end))->days;
          $proceed_notice_end = date("Y-m-d", strtotime($proceed_notice_start . "+" . $proceed_notice_days . " days"));
          $proceed_notice_start = date("Y-m-d", strtotime($proceed_notice_start . "+" . $proceed_notice_days . " days"));
        } else if ($process === "notice_to_proceed") {
          $process_name = "Notice To Proceed";
          $old_date = $project_timeline->proceed_notice_end;
          $advertisement_start = $project_timeline->advertisement_start;
          $advertisement_end = $project_timeline->advertisement_end;
          $pre_bid_start = $project_timeline->pre_bid_start;
          $pre_bid_end = $project_timeline->pre_bid_end;
          $bid_submission_start = $project_timeline->bid_submission_start;
          $bid_submission_end = $project_timeline->bid_submission_end;
          $bid_evaluation_start = $project_timeline->bid_evaluation_start;
          $bid_evaluation_end = $project_timeline->bid_evaluation_end;
          $post_qualification_start = $project_timeline->post_qualification_start;
          $post_qualification_end = $project_timeline->post_qualification_end;
          $award_notice_start = $project_timeline->award_notice_start;
          $award_notice_end = $project_timeline->award_notice_end;
          $contract_signing_start = $project_timeline->contract_signing_start;
          $contract_signing_end = $project_timeline->contract_signing_end;
          $authority_approval_start = $project_timeline->authority_approval_start;
          $authority_approval_end = $project_timeline->authority_approval_end;
          $proceed_notice_start = $project_timeline->proceed_notice_start;
          $proceed_notice_end = date("Y-m-d", strtotime($date));
          $proceed_notice_start = date("Y-m-d", strtotime($date));
        } else {
        }
        $update = DB::table('project_timelines')
          ->where("project_plans.plan_id", $id)
          ->join("procacts", "project_timelines.procact_id", "procacts.procact_id")
          ->join("project_plans", "project_plans.latest_procact_id", "procacts.procact_id")
          ->update([
            "advertisement_start" => $advertisement_start,
            "advertisement_end" => $advertisement_end,
            "pre_bid_start" => $pre_bid_start,
            "pre_bid_end" => $pre_bid_end,
            "bid_submission_start" => $bid_submission_start,
            "bid_submission_end" => $bid_submission_end,
            "bid_evaluation_start" => $bid_evaluation_start,
            "bid_evaluation_end" => $bid_evaluation_end,
            "post_qualification_start" => $post_qualification_start,
            "project_timelines.post_qualification_end" => $post_qualification_end,
            "project_timelines.award_notice_start" => $award_notice_start,
            "project_timelines.award_notice_end" => $award_notice_end,
            "project_timelines.contract_signing_start" => $contract_signing_start,
            "project_timelines.contract_signing_end" => $contract_signing_end,
            "project_timelines.authority_approval_start" => $authority_approval_start,
            "project_timelines.authority_approval_end" => $authority_approval_end,
            "project_timelines.proceed_notice_start" => $proceed_notice_start,
            "project_timelines.proceed_notice_end" => $proceed_notice_end,
            "project_timelines.updated_at" => now(),
          ]);
        DB::table('project_logs')->insert([
          'plan_id' =>  $id,
          'user_id' => Auth::user()->id,
          'project_log_type' => "Extended " . $process_name . " from " . date("m/d/y", strtotime($old_date)) . " to " . $date . ".",
          'project_log_remarks' => $remarks,
          'log_date' => date("Y-m-d"),
          'created_at' => now(),
          'updated_at' => now()
        ]);
      }
    }

    return $message;
  }


  public function checkOngoingSpecificProject($id, $process)
  {
    if ($process === "opening") {
      $count = DB::table('project_plans')
        ->where([['project_plans.plan_id', $id], ['project_activity_status.main_status', 'pending'], ['project_activity_status.post_qual', 'pending']])
        ->join("procacts", "project_plans.latest_procact_id", "procacts.procact_id")
        ->join("project_activity_status", "project_activity_status.procact_id", "procacts.procact_id")
        ->count();
    }

    if ($process === "bid_evaluation") {
      $count = DB::table('project_plans')
        ->where([['project_plans.plan_id', $id], ['project_activity_status.main_status', 'pending'], ['project_activity_status.bid_evaluation', 'pending'], ['project_activity_status.post_qual', 'pending']])
        ->join("procacts", "project_plans.latest_procact_id", "procacts.procact_id")
        ->join("project_activity_status", "project_activity_status.procact_id", "procacts.procact_id")
        ->count();
    }

    return $count;
  }



  public function evaluateBidEvaluationStatus($procact_ids)
  {

    $procact_id = $procact_ids[0];
    $bidders_without_bid = DB::table('rfq_projects')
      ->where([['procacts.procact_id', $procact_id], ["rfqs.proposed_bid", 0], ["rfqs.bid_as_evaluated", 0]])
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->count();

    if ($bidders_without_bid === 0) {
      $bidders = DB::table('bid_doc_projects')
        ->where([['procacts.procact_id', $procact_id], ["bid_docs.proposed_bid", 0], ["bid_docs.bid_as_evaluated", 0]])
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
        ->count();
    }

    $bidders_with_bid = DB::table('rfq_projects')
      ->where([['procacts.procact_id', $procact_id], ["rfqs.proposed_bid", '>', 0], ["rfqs.bid_as_evaluated", '>', 0]])
      ->join('procacts', 'procacts.procact_id', 'rfq_projects.procact_id')
      ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('contractors', 'rfqs.contractor_id', 'contractors.contractor_id')
      ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
      ->count();

    if ($bidders_with_bid === 0) {
      $bidders_with_bid = DB::table('bid_doc_projects')
        ->where([['procacts.procact_id', $procact_id], ["bid_docs.proposed_bid", '>', 0], ["bid_docs.bid_as_evaluated", '>', 0]])
        ->join('procacts', 'procacts.procact_id', 'bid_doc_projects.procact_id')
        ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'bid_docs.contractor_id', 'contractors.contractor_id')
        ->leftJoin('twg_evaluations', 'project_bidders.project_bid', 'twg_evaluations.project_bid')
        ->count();
    }
    if ($bidders_without_bid === 0 && $bidders_with_bid > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function getBid($bid_id)
  {

    $project_bid = DB::table('procacts')
      ->select('rfqs.*', 'procacts.*', 'funds.*', 'contractors.*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_plans.*', 'procacts.plan_cluster_id', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), 'twg_evaluations.twg_final_bid_evaluation', 'twg_evaluations.post_qual_end', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost,twg_evaluations.detailed_bid_as_calculated"), 'project_bidders.project_bid', 'project_bidders.bid_status', 'rfq_projects.rfq_project_id', 'rfq_projects.detailed_bid_as_read', 'rfq_projects.detailed_bid_as_evaluated', 'rfqs.proposed_bid', 'rfqs.bid_as_evaluated', 'rfqs.discount')
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
        ->select('bid_docs.*', 'procacts.*', 'funds.*', 'contractors.*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_plans.*', 'procacts.plan_cluster_id', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), 'twg_evaluations.twg_final_bid_evaluation', 'twg_evaluations.post_qual_end', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated,twg_evaluations.detailed_bid_as_calculated) AS minimum_detailed_cost"), 'project_bidders.bid_status', 'project_bidders.project_bid', 'bid_doc_projects.bid_doc_project_id', 'bid_doc_projects.detailed_bid_as_read', 'bid_doc_projects.detailed_bid_as_evaluated', 'bid_docs.proposed_bid', 'bid_docs.bid_as_evaluated', 'bid_docs.discount')
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

  public function  getClusterBids($bid_id)
  {

    $project_bid = DB::table('procacts')
      ->select('contracts.*', 'contractors.*', 'rfq_projects.*', 'rfqs.*', 'funds.*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_plans.*', 'procacts.plan_cluster_id', 'procacts.open_bid', 'procacts.procact_mode_id', 'municipalities.municipality_name', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), 'twg_evaluations.twg_final_bid_evaluation', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost,twg_evaluations.detailed_bid_as_calculated"), 'project_bidders.project_bid', 'project_bidders.bid_status', 'rfq_projects.rfq_project_id', 'rfq_projects.detailed_bid_as_read', 'rfq_projects.detailed_bid_as_evaluated', 'rfqs.proposed_bid', 'rfqs.bid_as_evaluated', 'rfqs.discount', 'twg_evaluations.detailed_bid_as_calculated')
      ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
      ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
      ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
      ->join('rfq_projects', 'rfq_projects.procact_id', 'procacts.procact_id')
      ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
      ->join('contractors', 'contractors.contractor_id', 'rfqs.contractor_id')
      ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
      ->where('project_bidders.project_bid', $bid_id)
      ->leftJoin('twg_evaluations', 'twg_evaluations.project_bid', 'project_bidders.project_bid')
      ->leftJoin('contracts', 'contracts.project_bid_id', 'project_bidders.project_bid')
      ->orderBy('procacts.itb_arrangement', 'asc')
      ->get();

    if (count($project_bid) === 0) {
      $project_bid = DB::table('procacts')
        ->select('contracts.*', 'contractors.*', 'bid_doc_projects.*', 'bid_docs.*', 'funds.*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_plans.*', 'procacts.open_bid', 'procacts.plan_cluster_id', 'procacts.procact_mode_id', 'municipalities.municipality_name', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), 'twg_evaluations.twg_final_bid_evaluation', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated,twg_evaluations.detailed_bid_as_calculated) AS minimum_detailed_cost"), 'project_bidders.bid_status', 'project_bidders.project_bid', 'bid_doc_projects.bid_doc_project_id', 'bid_doc_projects.detailed_bid_as_read', 'bid_doc_projects.detailed_bid_as_evaluated', 'bid_docs.proposed_bid', 'bid_docs.bid_as_evaluated', 'bid_docs.discount', 'twg_evaluations.detailed_bid_as_calculated')
        ->join('project_plans', 'project_plans.plan_id', 'procacts.plan_id')
        ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
        ->join('bid_doc_projects', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'contractors.contractor_id', 'bid_docs.contractor_id')
        ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->where('project_bidders.project_bid', $bid_id)
        ->leftJoin('twg_evaluations', 'twg_evaluations.project_bid', 'project_bidders.project_bid')
        ->leftJoin('contracts', 'contracts.project_bid_id', 'project_bidders.project_bid')
        ->orderBy('procacts.itb_arrangement', 'asc')
        ->get();
    }
    if ($project_bid[0]->plan_cluster_id != null) {
      $rfq_project_bid = DB::table('procacts')
        ->select('contracts.*', 'contractors.*', 'rfq_projects.*', 'rfqs.*', 'funds.*', DB::raw('CONCAT("RFQ",rfqs.rfq_id) AS init_id'), 'project_plans.*', 'procacts.plan_cluster_id', 'procacts.open_bid', 'procacts.procact_mode_id', 'municipalities.municipality_name', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words) AS minimum_cost"), 'twg_evaluations.twg_final_bid_evaluation', DB::raw("LEAST(rfqs.bid_as_evaluated,rfqs.proposed_bid,rfqs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::raw("LEAST(rfq_projects.detailed_bid_as_read,rfq_projects.detailed_bid_as_evaluated) AS minimum_detailed_cost"), 'project_bidders.project_bid', 'project_bidders.bid_status', 'rfq_projects.rfq_project_id', 'rfq_projects.detailed_bid_as_read', 'rfq_projects.detailed_bid_as_evaluated', 'rfqs.proposed_bid', 'rfqs.bid_as_evaluated', 'rfqs.discount', 'twg_evaluations.detailed_bid_as_calculated')
        ->join('project_plans', 'project_plans.plan_id', 'procacts.plan_id')
        ->join('rfq_projects', 'rfq_projects.procact_id', 'procacts.procact_id')
        ->join('rfqs', 'rfqs.rfq_id', 'rfq_projects.rfq_id')
        ->join('contractors', 'contractors.contractor_id', 'rfqs.contractor_id')
        ->join('project_bidders', 'project_bidders.rfq_project_id', 'rfq_projects.rfq_project_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->leftJoin('twg_evaluations', 'twg_evaluations.project_bid', 'project_bidders.project_bid')
        ->leftJoin('contracts', 'contracts.project_bid_id', 'project_bidders.project_bid')
        ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
        ->orderBy('procacts.itb_arrangement', 'asc')
        ->where([['procacts.plan_cluster_id', $project_bid[0]->plan_cluster_id], ['bid_status', $project_bid[0]->bid_status], ['contractors.contractor_id', $project_bid[0]->contractor_id]])
        ->get();

      $bid_doc_project_bid = DB::table('procacts')
        ->select('contracts.*', 'contractors.*', 'bid_doc_projects.*', 'bid_docs.*', 'funds.*', DB::raw('CONCAT("BD",bid_docs.bid_doc_id) AS init_id'), 'project_plans.*', 'procacts.plan_cluster_id', 'procacts.open_bid', 'procacts.procact_mode_id', 'municipalities.municipality_name', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words) AS minimum_cost"), 'twg_evaluations.twg_final_bid_evaluation', DB::raw("LEAST(bid_docs.bid_as_evaluated,bid_docs.proposed_bid,bid_docs.bid_in_words,twg_evaluations.twg_final_bid_evaluation) AS final_minimum_cost"), DB::raw("LEAST(bid_doc_projects.detailed_bid_as_read,bid_doc_projects.detailed_bid_as_evaluated,twg_evaluations.detailed_bid_as_calculated) AS minimum_detailed_cost"), 'project_bidders.bid_status', 'project_bidders.project_bid', 'bid_doc_projects.bid_doc_project_id', 'bid_doc_projects.detailed_bid_as_read', 'bid_doc_projects.detailed_bid_as_evaluated', 'bid_docs.proposed_bid', 'bid_docs.bid_as_evaluated', 'bid_docs.discount', 'twg_evaluations.detailed_bid_as_calculated')
        ->join('project_plans', 'project_plans.plan_id', 'procacts.plan_id')
        ->join('bid_doc_projects', 'bid_doc_projects.procact_id', 'procacts.procact_id')
        ->join('bid_docs', 'bid_docs.bid_doc_id', 'bid_doc_projects.bid_doc_id')
        ->join('contractors', 'contractors.contractor_id', 'bid_docs.contractor_id')
        ->join('project_bidders', 'project_bidders.bid_doc_project_id', 'bid_doc_projects.bid_doc_project_id')
        ->join('funds', 'project_plans.fund_id', 'funds.fund_id')
        ->leftJoin('twg_evaluations', 'twg_evaluations.project_bid', 'project_bidders.project_bid')
        ->leftJoin('contracts', 'contracts.project_bid_id', 'project_bidders.project_bid')
        ->join('municipalities', 'municipalities.municipality_id', 'project_plans.municipality_id')
        ->orderBy('procacts.itb_arrangement', 'asc')
        ->where([['procacts.plan_cluster_id', $project_bid[0]->plan_cluster_id], ['bid_status', $project_bid[0]->bid_status], ['contractors.contractor_id', $project_bid[0]->contractor_id]])
        ->get();

      if (count($rfq_project_bid) > 0 && count($bid_doc_project_bid) > 0) {
        $cluster_bids = (object) array_merge((array)$rfq_project_bid, (array)$bid_doc_project_bid);
      } elseif (count($rfq_project_bid) > 0 && count($bid_doc_project_bid) === 0) {
        $cluster_bids = $rfq_project_bid;
      } elseif (count($rfq_project_bid) === 0 && count($bid_doc_project_bid) > 0) {
        $cluster_bids = $bid_doc_project_bid;
      } else {
        $cluster_bids = $project_bid;
      }
    } else {
      $cluster_bids = $project_bid;
    }

    return $cluster_bids;
  }

  public function getDateProjects($date)
  {
    $date = date("Y-m-d", strtotime($date));
    DB::statement(DB::raw('set @row:=0'));

    $projects = DB::table("project_timelines")
      ->join("procacts", "procacts.procact_id", "project_timelines.procact_id")
      ->join("project_plans", "project_plans.plan_id", "procacts.plan_id")
      ->join("funds", "project_plans.fund_id", "funds.fund_id")
      ->join("procurement_modes", "procacts.procact_mode_id", "procurement_modes.mode_id")
      ->join("municipalities", "municipalities.municipality_id", "project_plans.municipality_id")
      ->where("project_timelines.bid_submission_end", $date)
      ->orderBy("procacts.procact_mode_id", "asc")
      ->orderBy("procacts.itb_arrangement", "asc")
      ->orderBy("municipalities.municipality_name", "asc")
      ->orderBy("project_plans.project_cost", "asc")
      ->orderBy("procacts.plan_cluster_id", "asc")
      ->orderBy("project_plans.project_title", "asc")
      ->get();

    $row = 1;
    $projects_array = [];
    foreach ($projects as $project) {
      $array = (array) $project;
      $array = array_merge($array, array("row_number" => $row));
      $row = $row + 1;
      array_push($projects_array, (object)$array);
    }

    $projects = (object)$projects_array;

    return $projects;
  }


  public function sortObject(&$objects, $order)
  {

    usort($objects, function ($a, $b) use ($order) {
      $t = array(true => -1, false => 1);
      $r = true;
      $k = 1;
      foreach ($order as $key => $value) {
        $k = ($value === 'asc') ? 1 : -1;
        $r = ($a->$key < $b->$key);
        if ($a->$key !== $b->$key) {
          return $t[$r] * $k;
        }
      }
      return $t[$r] * $k;
    });

    return $objects;
  }


  public function itemizeProject($projects)
  {
    $plan_ids_array = [];
    $formatted_project_plans = [];
    $plan_format = (object)["project_title" => null, "location" => null, "project_number" => null, "source_of_fund" => null, "abc" => null, "duration" => null, "project_engineer" => null];
    foreach ($projects as $project) {


      if (in_array($project->plan_id, $plan_ids_array) === false) {
        if ($project->plan_cluster_id != null) {
          $project_title = "";
          $location = null;
          $source_of_fund = "";
          $project_number = "";
          $abc = "";
          $duration = 0;
          $letter = "A";
          $total = 0;
          $clusters = $projects = DB::table('project_timelines')
            ->where('plan_cluster_id', $project->plan_cluster_id)
            ->join('procacts', 'procacts.procact_id', 'project_timelines.procact_id')
            ->join('project_plans', 'procacts.plan_id', 'project_plans.plan_id')
            ->join('procurement_modes', 'procacts.procact_mode_id', 'procurement_modes.mode_id')
            ->join('municipalities', 'project_plans.municipality_id', 'municipalities.municipality_id')
            ->join("funds", "project_plans.fund_id", "funds.fund_id")
            ->orderBy('procacts.itb_arrangement')
            ->get();

          foreach ($clusters as $cluster) {
            $temp = $letter . '. ' . $cluster->project_title . ";";
            $project_title = $project_title . "   " . $temp;
            $source_of_fund = $source_of_fund . " " . $letter . '. ' . $cluster->source . ";";
            $project_number = $project_number . " " . $letter . '. ' . $cluster->project_no . ";";
            $total = $total + $cluster->project_cost;
            $abc = $abc . $letter . '.' . " ₱  " . number_format((float)$cluster->project_cost, 2, '.', ',') . "; ";
            $duration = $duration + $cluster->duration;
            $letter = ++$letter;
            array_push($plan_ids_array, $cluster->plan_id);
          }
          $plan_format->project_title = strtoupper(strtolower($project_title));
          $plan_format->location = strtoupper(strtolower($project->municipality_name . ",BENGUET"));
          $plan_format->source_of_fund = strtoupper(strtolower($source_of_fund));
          $plan_format->project_number = strtoupper(strtolower($project_number));
          $plan_format->abc = $abc . " = " . " ₱  " . number_format((float)$total, 2, '.', ',');
          $plan_format->duration = $duration . " C.D.";
          $plan_format->project_engineer = $clusters[0]->project_engineer;
          array_push($formatted_project_plans, (array)$plan_format);
        } else {
          $plan_format->project_title = strtoupper(strtolower($project->project_title));
          $plan_format->location = strtoupper(strtolower($project->municipality_name . ",BENGUET"));
          $plan_format->source_of_fund = strtoupper(strtolower($project->source));
          $plan_format->project_number = strtoupper(strtolower($project->project_no));
          $plan_format->abc = " ₱  " . number_format((float)$project->project_cost, 2, '.', ',');
          $plan_format->duration = $project->duration . " C.D.";
          $plan_format->project_engineer = $project->project_engineer;
          array_push($plan_ids_array, $project->plan_id);
          array_push($formatted_project_plans, (array)$plan_format);
        }
      }
    }
    return $formatted_project_plans;
  }
}
