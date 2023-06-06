<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\APP;
use Validator;

class ProcurementController extends Controller
{
  public function getProcurementActivity($plan_id)
  {


    $project_plans=DB::table("project_plans")->select("*","project_plans.status as project_status")
    ->where("project_plans.plan_id",$plan_id)
    ->join("municipalities", "project_plans.municipality_id","=","municipalities.municipality_id")
    ->join("barangays", "project_plans.barangay_id","=","barangays.barangay_id")
    ->join("projtypes", "project_plans.projtype_id","=","projtypes.projtype_id")
    ->join("procurement_modes", "project_plans.mode_id","=","procurement_modes.mode_id")
    ->join("funds", "project_plans.fund_id","=","funds.fund_id")
    ->join("account_classifications", "project_plans.account_id","=","account_classifications.account_id")
    ->join("procacts", "project_plans.plan_id","=","procacts.plan_id")
    ->join("project_timelines", "project_plans.plan_id","=","project_timelines.plan_id")
    ->get();


    if($project_plans[0]->timeline_status=="pending"){
      return abort(404);
    }
    else{
      $activity_status=DB::table("project_activity_status")->where("plan_id",$plan_id)->orderBy("pro_act_stat_id","DESC")->first();

      // Get Current Tab
      $current_tab=0;


      if($activity_status->pre_proc=="finished"){
        $current_tab=$current_tab+1;
      }
      if($activity_status->advertisement=="finished"){
        $current_tab=$current_tab+1;
      }
      if($activity_status->pre_bid=="finished"){
        $current_tab=$current_tab+1;
      }
      if($activity_status->open_bid=="finished"){
        $current_tab=$current_tab+1;
      }
      if($activity_status->bid_evaluation=="finished"){
        $current_tab=$current_tab+1;
      }
      if($activity_status->post_qual=="finished"){
        $current_tab=$current_tab+1;
      }
      if($activity_status->award_notice=="finished"){
        $current_tab=$current_tab+1;
      }
      if($activity_status->contract_signing=="finished"){
        $current_tab=$current_tab+1;
      }
      if($activity_status->authority_approval=="finished"){
        $current_tab=$current_tab+1;
      }
      if($activity_status->proceed_notice=="finished"){
        $current_tab=$current_tab+1;
      }

      if($current_tab>=10){
        $current_tab=10;
      }
      else{
        $current_tab=$current_tab+1;
      }
      $links=getUserLinks();
      $user_privilege=getUserPrivilege();


      return view("admin.procurement_activity",["links"=>$links,'user_privilege'=>$user_privilege,"project_plans"=>$project_plans,"activity_status"=>$activity_status,"current_tab"=>$current_tab]);
    }

  }


  public function submitPreprocurement(Request $request)
  {

    $data=$request->validate([
      "preprocurement_date"=>"required",
    ]);

    $date_error=false;
    $plan_ids=$request->input("plan_id");
    $plan_ids_array=explode(",",$plan_ids);
    $preprocurement_date=$request->input("preprocurement_date");
    $latest_procacts_array=[];

    foreach ($plan_ids_array as $plan_id) {
      // get latest linked files
      $latest_activity_status=DB::table("project_activity_status")->where("plan_id",$plan_id)->orderBy("pro_act_stat_id","desc")->first();
      $latest_procact=DB::table("project_plans")->select("procacts.*","project_plans.*")->where("project_plans.plan_id",$plan_id)->join("procacts","project_plans.latest_procact_id","procacts.procact_id")->first();
      $latest_timeline=DB::table("project_timelines")->where("plan_id",$plan_id)->orderBy("timeline_id","desc")->first();
      $latest_advertisement=date("m/d/Y", strtotime($latest_timeline->advertisement_start));
      array_push($latest_procacts_array,$latest_procact->procact_id);

      if($latest_procact->current_cluster!=null){
        $clustered_plans=DB::table("project_plans")->where("project_plans.current_cluster",$latest_procact->current_cluster)->get();
        foreach($clustered_plans as $clustered_plan){
          if(in_array($clustered_plan->latest_procact_id,$latest_procacts_array)==false){
            array_push($latest_procacts_array,$clustered_plan->latest_procact_id);
          }
        }
      }

      $date1 = date_create($latest_advertisement);
      $date2 = date_create($preprocurement_date);
      $diff=date_diff($date1,$date2);

      if($diff->invert==0){
        $difference=$diff->format("%d")*-1;
      }
      else{
        $difference=$diff->format("%d");
      }

      if($difference<0){
        $date_error=true;
      }
    }

    if($date_error){
      $message="date_error";
    }
    else{
      // update
      $update=DB::table("project_activity_status")->whereIn("pro_act_stat_id",$latest_procacts_array)
      ->update(["pre_proc"=>"finished"]);

      DB::table("procacts")->whereIn("procact_id",$latest_procacts_array)
      ->update(["pre_proc"=>date("Y-m-d", strtotime($request->input("preprocurement_date")))]);

      $message="success";
    }

    return redirect()->back()->with("message",$message);
  }


  public function submitAdvertisementPosting(Request $request)
  {

    $data=$request->validate([
      "advertisement_posting_date"=>"required",
    ]);


    $plan_ids=$request->input("plan_id");
    $range_error=false;
    $plan_ids_array=explode(",",$plan_ids);
    $latest_procacts_array=[];
    $advertisement_posting_date=$request->input("advertisement_posting_date");

    // get latest linked files
    foreach ($plan_ids_array as $plan_id) {
      $latest_activity_status=DB::table("project_activity_status")->where("plan_id",$plan_id)->orderBy("pro_act_stat_id","desc")->first();
      $latest_procact=DB::table("project_plans")->select("procacts.*","project_plans.*")->where("project_plans.plan_id",$plan_id)->join("procacts","project_plans.latest_procact_id","procacts.procact_id")->first();
      $latest_timeline=DB::table("project_timelines")->where("plan_id",$plan_id)->orderBy("timeline_id","desc")->first();

      $advertisement_start=date("m/d/Y", strtotime($latest_timeline->advertisement_start));
      $advertisement_end=date("m/d/Y", strtotime($latest_timeline->advertisement_end));
      array_push($latest_procacts_array,$latest_procact->procact_id);

      if($latest_procact->current_cluster!=null){
        $clustered_plans=DB::table("project_plans")->where("project_plans.current_cluster",$latest_procact->current_cluster)->get();
        foreach($clustered_plans as $clustered_plan){
          if(in_array($clustered_plan->latest_procact_id,$latest_procacts_array)==false){
            array_push($latest_procacts_array,$clustered_plan->latest_procact_id);
          }
        }
      }

      if($advertisement_posting_date<$advertisement_start||$advertisement_posting_date>$advertisement_end){
        $range_error=true;
      }
    }


    if($range_error){
      $message="range_error";
    }
    else{
      DB::table("project_activity_status")->whereIn("procact_id",$latest_procacts_array)
      ->update(["advertisement"=>"finished"]);

      DB::table("procacts")->whereIn("procact_id",$latest_procacts_array)
      ->update(["advertisement"=>date("Y-m-d", strtotime($request->input("advertisement_posting_date")))]);
      $message="success";
    }



    return redirect()->back()->with("message",$message);


  }

  public function submitPrebid(Request $request)
  {
    $data=$request->validate([
      "pre_bid_date"=>"required",
    ]);


    $plan_ids=$request->input("plan_id");
    $range_error=false;
    $plan_ids_array=explode(",",$plan_ids);

    $latest_procacts_array=[];
    $pre_bid=$request->input("pre_bid_date");

    // get latest linked files
    foreach ($plan_ids_array as $plan_id) {
      $latest_activity_status=DB::table("project_activity_status")->where("plan_id",$plan_id)->orderBy("pro_act_stat_id","desc")->first();
      $latest_procact=DB::table("project_plans")->select("procacts.*","project_plans.*")->where("project_plans.plan_id",$plan_id)->join("procacts","project_plans.latest_procact_id","procacts.procact_id")->first();
      $latest_timeline=DB::table("project_timelines")->where("plan_id",$plan_id)->orderBy("timeline_id","desc")->first();

      $pre_bid_start=date("m/d/Y", strtotime($latest_timeline->pre_bid_start));
      $pre_bid_end=date("m/d/Y", strtotime($latest_timeline->pre_bid_end));
      array_push($latest_procacts_array,$latest_procact->procact_id);

      if($latest_procact->current_cluster!=null){
        $clustered_plans=DB::table("project_plans")->where("project_plans.current_cluster",$latest_procact->current_cluster)->get();
        foreach($clustered_plans as $clustered_plan){
          if(in_array($clustered_plan->latest_procact_id,$latest_procacts_array)==false){
            array_push($latest_procacts_array,$clustered_plan->latest_procact_id);
          }
        }
      }

      if($pre_bid<$pre_bid_start||$pre_bid>$pre_bid_end){
        $range_error=true;
      }
    }

    if($range_error==true){
      $message="range_error";
    }
    else{

      DB::table("project_activity_status")->whereIn("procact_id",$latest_procacts_array)
      ->update(["pre_bid"=>"finished"]);

      DB::table("procacts")->whereIn("procact_id",$latest_procacts_array)
      ->update(["pre_bid"=>date("Y-m-d", strtotime($request->input("pre_bid_date")))]);
      $message="success";
    }


    return redirect()->back()->with("message",$message);


  }


  public function submitSubmissionOpeningOfBid(Request $request)
  {
    if($request->input("bypass")==true){

    }
    else{
      $data=$request->validate([
        "submission_opening_of_bid_time"=>"required",
        "submission_opening_of_bid_date"=>"required",
      ]);
    }


    $APP = new APP;
    $plan_ids=$request->input("plan_id");
    $range_error=false;
    $bidder_error=false;
    $plan_ids_array=explode(",",$plan_ids);
    $status="active";
    $latest_procacts_array=[];
    $bid_submission=$request->input("submission_opening_of_bid_date");

    // get latest linked files
    foreach ($plan_ids_array as $plan_id) {
      $latest_activity_status=DB::table("project_activity_status")->where("plan_id",$plan_id)->orderBy("pro_act_stat_id","desc")->first();
      $latest_procact=DB::table("project_plans")->select("procacts.*","project_plans.*")->where("project_plans.plan_id",$plan_id)->join("procacts","project_plans.latest_procact_id","procacts.procact_id")->first();
      $latest_timeline=DB::table("project_timelines")->where("plan_id",$plan_id)->orderBy("timeline_id","desc")->first();

      $bid_submission_start=date("m/d/Y", strtotime($latest_timeline->bid_submission_start));
      $bid_submission_end=date("m/d/Y", strtotime($latest_timeline->bid_submission_end));
      array_push($latest_procacts_array,$latest_procact->procact_id);

      if($latest_procact->current_cluster!=null){
        $clustered_plans=DB::table("project_plans")->where("project_plans.current_cluster",$latest_procact->current_cluster)->get();
        foreach($clustered_plans as $clustered_plan){
          if(in_array($clustered_plan->latest_procact_id,$latest_procacts_array)==false){
            array_push($latest_procacts_array,$clustered_plan->latest_procact_id);
          }
        }
      }

      if($bid_submission<$bid_submission_start||$bid_submission>$bid_submission_end){
        $range_error=true;
      }

      // check bidders for project
      // if($APP->getActiveBidders($latest_procact->procact_id,$status)<1){
      //   $bidder_error=true;
      // }


    }
    if($range_error==true){
      $message="range_error";
    }
    else if($bidder_error==true){
      $message="bidder_error";
    }
    else{

      DB::table("project_activity_status")->whereIn("procact_id",$latest_procacts_array)
      ->update(["open_bid"=>"finished"]);

      DB::table("procacts")->whereIn("procact_id",$latest_procacts_array)
      ->update([
        "open_bid"=>date("Y-m-d", strtotime($request->input("submission_opening_of_bid_date"))),
        "open_time"=>$request->input("submission_opening_of_bid_time")
      ]);

      // DB::table("project_bidders")->where("rfqs.date_received",">",date("Y-m-d", strtotime($request->input("submission_opening_of_bid_date"))))
      // ->orWhere([["rfqs.date_received",date("Y-m-d", strtotime($request->input("submission_opening_of_bid_date")))],["rfqs.time_received",">",$request->input("submission_opening_of_bid_time")]])
      // ->whereIn("rfq_projects.procact_id",$latest_procacts_array)
      // ->join("rfq_projects","rfq_projects.rfq_project_id","project_bidders.rfq_project_id")
      // ->join("rfqs","rfqs.rfq_id","rfq_projects.rfq_id")
      // ->join("procacts","rfq_projects.procact_id","procacts.procact_id")
      // ->update([
      //   "bid_status"=>"late"
      // ]);
      //
      // DB::table("project_bidders")->where("bid_docs.date_received",">",date("Y-m-d", strtotime($request->input("submission_opening_of_bid_date"))))
      // ->orWhere([["bid_docs.date_received",date("Y-m-d", strtotime($request->input("submission_opening_of_bid_date")))],["bid_docs.time_received",">",$request->input("submission_opening_of_bid_time")]])
      // ->whereIn("bid_doc_projects.procact_id",$latest_procacts_array)
      // ->join("bid_doc_projects","bid_doc_projects.bid_doc_project_id","project_bidders.bid_doc_project_id")
      // ->join("bid_docs","bid_docs.bid_doc_id","bid_doc_projects.bid_doc_id")
      // ->join("procacts","bid_doc_projects.procact_id","procacts.procact_id")
      // ->update([
      //   "bid_status"=>"late"
      // ]);

      $message="success";
    }



    return redirect()->back()->with("message",$message);


  }


  public function submitBidEvaluation(Request $request)
  {
    if($request->input("bypass")==true){

    }
    else{
      $data=$request->validate([
        "bid_evaluation_date"=>"required",
      ]);
    }

    $APP = new APP;
    $plan_ids=$request->input("plan_id");
    $range_error=false;
    $bidder_error=false;
    $plan_ids_array=explode(",",$plan_ids);

    $latest_procacts_array=[];
    $bid_evaluation=$request->input("bid_evaluation_date");

    // get latest linked files
    foreach ($plan_ids_array as $plan_id) {


      // get latest linked files
      $latest_activity_status=DB::table("project_activity_status")->where("plan_id",$plan_id)->orderBy("pro_act_stat_id","desc")->first();
      $latest_procact=DB::table("project_plans")->select("procacts.*","project_plans.*")->where("project_plans.plan_id",$plan_id)->join("procacts","project_plans.latest_procact_id","procacts.procact_id")->first();
      $latest_timeline=DB::table("project_timelines")->where("plan_id",$plan_id)->orderBy("timeline_id","desc")->first();

      $bid_evaluation_start=date("m/d/Y", strtotime($latest_timeline->bid_evaluation_start));
      $bid_evaluation_end=date("m/d/Y", strtotime($latest_timeline->bid_evaluation_end));
      array_push($latest_procacts_array,$latest_procact->procact_id);

      if($latest_procact->current_cluster!=null){
        $clustered_plans=DB::table("project_plans")->where("project_plans.current_cluster",$latest_procact->current_cluster)->get();
        foreach($clustered_plans as $clustered_plan){
          if(in_array($clustered_plan->latest_procact_id,$latest_procacts_array)==false){
            array_push($latest_procacts_array,$clustered_plan->latest_procact_id);
          }
        }
      }

      if($bid_evaluation<$bid_evaluation_start||$bid_evaluation>$bid_evaluation_end){
        $range_error=true;
      }
      if($APP->getActiveBiddersWithAmount($latest_procact->procact_id)){
        $bidder_error=true;
      }
    }

    if($range_error==true){
      $message="range_error";
    }

    else if($bidder_error==true){
      $message="bidder_error";
    }

    else{
      DB::table("project_activity_status")->whereIn("procact_id",$latest_procacts_array)
      ->update(["bid_evaluation"=>"finished"]);

      DB::table("procacts")->whereIn("procact_id",$latest_procacts_array)
      ->update(["bid_evaluation"=>date("Y-m-d", strtotime($request->input("bid_evaluation_date")))]);

      $message="success";
    }

    return redirect()->back()->with("message",$message);
  }

  public function submitPostQualification(Request $request)
  {

    if($request->input("bypass")==true){

    }
    else{
      $data=$request->validate([
        "post_qualification_date"=>"required",
      ]);
    }

    $APP = new APP;
    $plan_ids=$request->input("plan_id");
    $range_error=false;
    $bidder_error=false;
    $plan_ids_array=explode(",",$plan_ids);
    $status="responsive";
    $latest_procacts_array=[];

    $post_qualification=$request->input("post_qualification_date");

    // get latest linked files
    foreach ($plan_ids_array as $plan_id) {

      // get latest linked files
      $latest_activity_status=DB::table("project_activity_status")->where("plan_id",$plan_id)->orderBy("pro_act_stat_id","desc")->first();
      $latest_procact=DB::table("project_plans")->select("procacts.*","project_plans.*")->where("project_plans.plan_id",$plan_id)->join("procacts","project_plans.latest_procact_id","procacts.procact_id")->first();
      $latest_timeline=DB::table("project_timelines")->where("plan_id",$plan_id)->orderBy("timeline_id","desc")->first();

      $post_qualification_start=date("m/d/Y", strtotime($latest_timeline->post_qualification_start));
      $post_qualification_end=date("m/d/Y", strtotime($latest_timeline->post_qualification_end));
      array_push($latest_procacts_array,$latest_procact->procact_id);

      if(strtotime($post_qualification)<strtotime($post_qualification_start)||strtotime($post_qualification)>strtotime($post_qualification_end)){
        $range_error=true;
      }
      if($APP->getActiveBidders($latest_procact->procact_id,$status)<1){
        $bidder_error=true;
      }
    }

    if($range_error==true){
      $message="range_error";
    }

    else if($bidder_error==true){
      $message="bidder_error";
    }

    else{

      DB::table("project_activity_status")->whereIn("procact_id",$latest_procacts_array)
      ->update(["post_qual"=>"finished"]);
      DB::table("procacts")->whereIn("procact_id",$latest_procacts_array)
      ->update(["post_qual"=>date("Y-m-d", strtotime($request->input("post_qualification_date")))]);

      $message="success";
    }

    return redirect()->back()->with("message",$message);

  }

  public function submitAwardNotice(Request $request)
  {

    if($request->input("bypass")==true){

    }
    else{
      $data=$request->validate([
        "award_notice_date"=>"required",
      ]);
    }




    $APP = new APP;
    $plan_ids=$request->input("plan_id");
    $range_error=false;
    $bidder_error=false;
    $plan_ids_array=explode(",",$plan_ids);
    $status="responsive";
    $latest_procacts_array=[];
    $award_notice=$request->input("award_notice_date");


    foreach ($plan_ids_array as $plan_id) {
      // get latest linked files
      $latest_activity_status=DB::table("project_activity_status")->where("plan_id",$plan_id)->orderBy("pro_act_stat_id","desc")->first();
      $latest_procact=DB::table("project_plans")->select("procacts.*","project_plans.*")->where("project_plans.plan_id",$plan_id)->join("procacts","project_plans.latest_procact_id","procacts.procact_id")->first();
      $latest_timeline=DB::table("project_timelines")->where("plan_id",$plan_id)->orderBy("timeline_id","desc")->first();

      $award_notice_start=date("m/d/Y", strtotime($latest_timeline->award_notice_start));
      $award_notice_end=date("m/d/Y", strtotime($latest_timeline->award_notice_end));
      array_push($latest_procacts_array,$latest_procact->procact_id);
      if(strtotime($award_notice)<strtotime($award_notice_start)||strtotime($award_notice)>strtotime($award_notice_end)){
        $range_error=true;
      }
      if($APP->getActiveBidders($latest_procact->procact_id,$status)<1){
        $bidder_error=true;
      }

    }


    if($range_error==true){
      $message="range_error";
    }

    else if($bidder_error==true){
      $message="bidder_error";
    }

    else{

      DB::table("project_activity_status")->whereIn("procact_id",$latest_procacts_array)
      ->update(["award_notice"=>"finished"]);

      $update=DB::table("procacts")->whereIn("procact_id",$latest_procacts_array)
      ->update(["award_notice"=>date("Y-m-d", strtotime($request->input("award_notice_date")))]);

      $message="success";
    }

    return redirect()->back()->with("message",$message);
  }

  public function submitContractPreparationAndSigning(Request $request)
  {
    if($request->input("bypass")!=true){
      $data=$request->validate([
        "contract_preparation_and_signing_date"=>"required",
      ]);
    }
    $APP = new APP;
    $plan_ids=$request->input("plan_id");
    $range_error=false;
    $bidder_error=false;
    $plan_ids_array=explode(",",$plan_ids);
    $status="responsive";
    $latest_procacts_array=[];
    $contract_date=$request->input("contract_preparation_and_signing_date");

    foreach ($plan_ids_array as $plan_id) {
      // get latest linked files
      $latest_activity_status=DB::table("project_activity_status")->where("plan_id",$plan_id)->orderBy("pro_act_stat_id","desc")->first();
      $latest_procact=DB::table("project_plans")->select("procacts.*","project_plans.*")->where("project_plans.plan_id",$plan_id)->join("procacts","project_plans.latest_procact_id","procacts.procact_id")->first();
      $latest_timeline=DB::table("project_timelines")->where("plan_id",$plan_id)->orderBy("timeline_id","desc")->first();
      array_push($latest_procacts_array,$latest_procact->procact_id);
      $contract_date_start=date("m/d/Y", strtotime($latest_timeline->contract_signing_start));
      $contract_date_end=date("m/d/Y", strtotime($latest_timeline->contract_signing_end));

      if(strtotime($contract_date)<strtotime($contract_date_start)||strtotime($contract_date)>strtotime($contract_date_end)){
        $range_error=true;
      }
      if($APP->getActiveBidders($latest_procact->procact_id,$status)<1){
        $bidder_error=true;
      }
    }

    if($range_error==true){
      $message="range_error";
    }

    else if($bidder_error==true){
      $message="bidder_error";
    }

    else{
      DB::table("project_activity_status")->whereIn("procact_id",$latest_procacts_array)
      ->update(["contract_signing"=>"finished"]);
      DB::table("procacts")->whereIn("procact_id",$latest_procacts_array)
      ->update(["contract_signing"=>date("Y-m-d", strtotime($request->input("contract_preparation_and_signing_date")))]);
      $message="success";
    }


    return redirect()->back()->with("message",$message);

  }

  public function submitAuthorityApproval(Request $request)
  {

    $data=$request->validate([
      "authority_approval_date"=>"required",
    ]);


    $APP = new APP;
    $plan_ids=$request->input("plan_id");
    $range_error=false;
    $bidder_error=false;
    $plan_ids_array=explode(",",$plan_ids);
    $status="responsive";
    $latest_procacts_array=[];
    $authority_approval=$request->input("authority_approval_date");

    foreach ($plan_ids_array as $plan_id) {
      // get latest linked files
      $latest_activity_status=DB::table("project_activity_status")->where("plan_id",$plan_id)->orderBy("pro_act_stat_id","desc")->first();
      $latest_procact=DB::table("project_plans")->select("procacts.*","project_plans.*")->where("project_plans.plan_id",$plan_id)->join("procacts","project_plans.latest_procact_id","procacts.procact_id")->first();
      $latest_timeline=DB::table("project_timelines")->where("plan_id",$plan_id)->orderBy("timeline_id","desc")->first();

      $authority_approval_start=date("m/d/Y", strtotime($latest_timeline->authority_approval_start));
      $authority_approval_end=date("m/d/Y", strtotime($latest_timeline->authority_approval_end));
      array_push($latest_procacts_array,$latest_procact->procact_id);
      if($authority_approval<$authority_approval_start||$authority_approval>$authority_approval_end){

        $range_error=true;
      }
      if($APP->getActiveBidders($latest_procact->procact_id,$status)<1){
        $bidder_error=true;
      }
    }

    if($range_error==true){
      $message="range_error";
    }

    else if($bidder_error==true){
      $message="bidder_error";
    }

    else{
      DB::table("project_activity_status")->whereIn("procact_id",$latest_procacts_array)
      ->update(["authority_approval"=>"finished"]);
      DB::table("procacts")->whereIn("procact_id",$latest_procacts_array)
      ->update(["authority_approval"=>date("Y-m-d", strtotime($request->input("authority_approval_date")))]);
      $message="success";
    }

    return redirect()->back()->with("message",$message);

  }

  public function submitNoticeToProceed(Request $request)
  {

    if($request->input("bypass")!=true){
      $data=$request->validate([
        "notice_to_proceed_date"=>"required",
      ]);
    }

    $APP = new APP;
    $plan_ids=$request->input("plan_id");
    $range_error=false;
    $bidder_error=false;
    $plan_ids_array=explode(",",$plan_ids);

    $latest_procacts_array=[];
    $status="responsive";
    $proceed_notice=$request->input("notice_to_proceed_date");

    foreach ($plan_ids_array as $plan_id) {
      // get latest linked files
      $latest_activity_status=DB::table("project_activity_status")->where("plan_id",$plan_id)->orderBy("pro_act_stat_id","desc")->first();
      $latest_procact=DB::table("project_plans")->select("procacts.*","project_plans.*")->where("project_plans.plan_id",$plan_id)->join("procacts","project_plans.latest_procact_id","procacts.procact_id")->first();
      $latest_timeline=DB::table("project_timelines")->where("plan_id",$plan_id)->orderBy("timeline_id","desc")->first();

      $proceed_notice_start=date("m/d/Y", strtotime($latest_timeline->proceed_notice_start));
      $proceed_notice_end=date("m/d/Y", strtotime($latest_timeline->proceed_notice_end));
      array_push($latest_procacts_array,$latest_procact->procact_id);
      if(strtotime($proceed_notice)<strtotime($proceed_notice_start)||strtotime($proceed_notice)>strtotime($proceed_notice_end)){
        $range_error=true;
      }
      if($APP->getActiveBidders($latest_procact->procact_id,$status)<1){
        $bidder_error=true;
      }
    }

    if($range_error==true){
      $message="range_error";
    }

    else if($bidder_error==true){
      $message="bidder_error";
    }

    else{
      DB::table("project_activity_status")->whereIn("procact_id",$latest_procacts_array)
      ->update(["proceed_notice"=>"finished"]);

      DB::table("procacts")->whereIn("procact_id",$latest_procacts_array)
      ->update(["proceed_notice"=>date("Y-m-d", strtotime($request->input("notice_to_proceed_date")))]);

      DB::table("project_activity_status")->whereIn("procact_id",$latest_procacts_array)->update(["main_status"=>"completed"]);

      DB::table("project_plans")->whereIn("plan_id",$plan_ids_array)->update(["status"=>"completed"]);

      $message="success";
    }

    return redirect()->back()->with("message",$message);

  }


  public function submitRebid(Request $request)
  {
    $data=$request->validate([
      "rebid_remarks"=>"required",
    ]);
    $plan_id=$request->input("rebid_plan_id");

    $count_in_resolution=DB::table("project_plans")->join("procacts","procacts.procact_id","project_plans.latest_procact_id")
    ->where([["resolutions.type","RDF"],["project_plans.plan_id",$plan_id]])
    ->join("resolution_projects","procacts.procact_id","resolution_projects.procact_id")
    ->join("resolutions","resolutions.resolution_id","resolution_projects.resolution_id")->count();

    // if($count_in_resolution>0){
    if(true){
      $rebid_count=0;
      $remarks=$request->input("rebid_remarks");
      // get latest linked files
      $project_plan=DB::table("project_plans")->where("plan_id",$plan_id)->first();
      $latest_activity_status=DB::table("project_activity_status")->where("plan_id",$plan_id)->orderBy("pro_act_stat_id","desc")->first();
      $latest_procact=DB::table("project_plans")->select("procacts.*","project_plans.*")->where("project_plans.plan_id",$plan_id)->join("procacts","project_plans.latest_procact_id","procacts.procact_id")->first();
      $latest_timeline=DB::table("project_timelines")->where("plan_id",$plan_id)->orderBy("timeline_id","desc")->first();

      if($project_plan->re_bid_count==null){
        $rebid_count=0;
      }
      else{
        $rebid_count=$project_plan->re_bid_count;
      }
      $rebid_count=$rebid_count+1;

      if($project_plan->mode_id===2){
        $log="Another SVP";
      }
      else{
        $log="Project for Rebid";
      }


      DB::table("project_activity_status")->where("procact_id",$latest_procact->procact_id)->update([
        "main_status"=>"rebid"
      ]);


      $pre_procurement="not_needed";
      if($project_plan->abc>5000000){
        $pre_procurement="pending";
      }
      // insert to project logs

      DB::table("project_logs")->insert([
        "plan_id"=>	$project_plan->plan_id,
        "user_id"=>Auth::user()->id,
        "project_log_type"=>$log,
        "project_log_remarks"=>$remarks,
        "log_date"=>date("Y-m-d"),
        "created_at"=> now(),
        "updated_at"=> now()
      ]);

      // create new timeline, status and procurement_activity
      DB::table("procacts")->insert([
        "procact_mode_id"=>$project_plan->mode_id,
        "plan_id"=>$project_plan->plan_id,
        "created_at" => now(),
        "updated_at" => now()
      ]);

      $latest_procact=DB::table("procacts")->where("plan_id",$project_plan->plan_id)->orderBy("created_at","desc")->first();

      DB::table("project_plans")->where("plan_id",$plan_id)->update([
        "re_bid_count"=>$rebid_count,
        "status"=>"for_rebid",
        "current_cluster"=>null,
        "latest_procact_id"=>$latest_procact->procact_id,
      ]);

      DB::table("project_timelines")->insert([
        "plan_id"=>$project_plan->plan_id,
        "procact_id"=>$latest_procact->procact_id,
        "timeline_status"=>"pending",
        "created_at" => now(),
        "updated_at" => now()
      ]);

      DB::table("project_activity_status")->insert([
        "procact_id"=>$latest_procact->procact_id,
        "plan_id"=>$project_plan->plan_id,
        "pre_proc"=>$pre_procurement,
        "created_at" => now(),
        "updated_at" => now()
      ]);

      return redirect()->back()->with("message","rebid_success");
    }
    else{
      return redirect()->back()->with("message","resolution_error");
    }

  }

  public function submitReactivateProject(Request $request)
  {
    $data=$request->validate([
      "reactivate_remarks"=>"required",
    ]);
    $plan_id=$request->input("reactivate_plan_id");

    $rebid_count=0;
    $remarks=$request->input("reactivate_remarks");
    // get latest linked files
    $project_plan=DB::table("project_plans")->where("plan_id",$plan_id)->first();
    $activity_status=DB::table("project_activity_status")->where("plan_id",$plan_id)->orderBy("pro_act_stat_id","desc")->get();
    $procact=DB::table("procacts")->select("procacts.*","project_plans.*")->where("project_plans.plan_id",$plan_id)->join("project_plans","project_plans.plan_id","procacts.plan_id")->orderBy("procact_id","desc")->get();
    $timeline=DB::table("project_timelines")->where("plan_id",$plan_id)->orderBy("timeline_id","desc")->get();

    // $rebid_count=$project_plan->re_bid_count-1;
    $rebid_count=0;

    if($project_plan->re_bid_count>3){
      $log="Reactivated from For Review Projects";
    }
    else{
      $log="Reactivated from For Rebid Projects";
    }

    DB::table("project_activity_status")->where("procact_id",$procact[1]->procact_id)->update([
      "main_status"=>"pending"
    ]);


    // insert to project logs
    DB::table("project_logs")->insert([
      "plan_id"=>	$project_plan->plan_id,
      "user_id"=>Auth::user()->id,
      "project_log_type"=>$log,
      "project_log_remarks"=>$remarks,
      "log_date"=>date("Y-m-d"),
      "created_at"=> now(),
      "updated_at"=> now()
    ]);

    // update
    DB::table("project_plans")->where("plan_id",$plan_id)->update([
      "re_bid_count"=>$rebid_count,
      "status"=>"onprocess",
      "current_cluster"=>$procact[1]->plan_cluster_id,
      "latest_procact_id"=>$procact[1]->procact_id,
    ]);

    // delete newer data
    DB::table("project_activity_status")->where("procact_id",$procact[0]->procact_id)->delete();
    DB::table("project_timelines")->where("procact_id",$procact[0]->procact_id)->delete();
    DB::table("procacts")->where("procact_id",$procact[0]->procact_id)->delete();


    return redirect()->back()->with("message","reactivate_success");


  }

  public function submitReview(Request $request)
  {
    $data=$request->validate([
      "review_remarks"=>"required",
    ]);

    $rebid_count=0;
    $plan_id=$request->input("review_plan_id");
    $remarks=$request->input("review_remarks");

    $count_in_resolution=DB::table("project_plans")->join("procacts","procacts.procact_id","project_plans.latest_procact_id")
    ->where([["resolutions.type","RDF"],["project_plans.plan_id",$plan_id]])
    ->join("resolution_projects","procacts.procact_id","resolution_projects.procact_id")
    ->join("resolutions","resolutions.resolution_id","resolution_projects.resolution_id")->count();

    // if($count_in_resolution>0){
    if(true){
      $project_plan=DB::table("project_plans")->where("plan_id",$plan_id)->first();
      $latest_procact=DB::table("project_plans")->select("procacts.*","project_plans.*")->where("project_plans.plan_id",$plan_id)->join("procacts","project_plans.latest_procact_id","procacts.procact_id")->first();

      DB::table("project_plans")->where("plan_id",$plan_id)->update([
        "status"=>"for_review"
      ]);

      DB::table("project_activity_status")->where("procact_id",$latest_procact->procact_id)->update([
        "main_status"=>"review"
      ]);

      // insert to project logs
      DB::table("project_logs")->insert([
        "plan_id"=>	$project_plan->plan_id,
        "user_id"=>Auth::user()->id,
        "project_log_type"=>"Project for Review",
        "project_log_remarks"=>$remarks,
        "log_date"=>date("Y-m-d"),
        "created_at"=> now(),
        "updated_at"=> now()
      ]);

      return redirect()->back()->with("message","review_success");
    }
    else{
      return redirect()->back()->with("message","resolution_error");
    }
  }

  public function getPreprocurementActivity()
  {
    $year=date("Y");
    $title="PRE-PROCUREMENT";
    $APP = new APP;
    $project_plans=$APP->getSpecificProcurementActivity("pre_procurement",$year);
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view("admin.pre_procurement",["links"=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_plans"=>$project_plans,"year"=>$year]);
  }

  public function getAdvertisementPostingActivity()
  {
    $year=date("Y");
    $title="ADVERTISEMENT/POSTING";
    $APP = new APP;
    $project_plans=$APP->getSpecificProcurementActivity("advertisement_posting",$year);
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view("admin.advertisement_posting",["links"=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_plans"=>$project_plans,"year"=>$year]);
  }

  public function getPreBidActivity()
  {
    $year=date("Y");
    $title="PRE-BID";
    $APP = new APP;
    $project_plans=$APP->getSpecificProcurementActivity("pre_bid",$year);
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view("admin.pre_bid",["links"=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_plans"=>$project_plans,"year"=>$year]);
  }

  public function getSubmissionOpeningActivity()
  {
    $year=date("Y");
    $title="SUBMISSION/OPENING OF BIDS";
    $APP = new APP;
    $project_plans=$APP->getSpecificProcurementActivity("submission_opening",$year);
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view("admin.submission_opening",["links"=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_plans"=>$project_plans,"year"=>$year]);
  }

  public function getBidEvaluationActivity()
  {
    $year=date("Y");
    $title="BID EVALUATION";
    $APP = new APP;
    $project_plans=$APP->getSpecificProcurementActivity("bid_evaluation",$year);
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view("admin.bid_evaluation",["links"=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_plans"=>$project_plans,"year"=>$year]);
  }

  public function getPostQualificationActivity(Request $request)
  {
    if($request->project_year!=null){
      $year=$request->project_year;
      $title="POST QUALIFICATION";
      $APP = new APP;
      $project_plans=$APP->getSpecificProcurementActivity("post_qualification",$year);

      return back()->withInput()->with("project_plans",$project_plans);
    }
    else{
      $year=date("Y");
      $title="POST QUALIFICATION";
      $APP = new APP;
      $project_plans=$APP->getSpecificProcurementActivity("post_qualification",$year);
      $links=getUserLinks();
      $user_privilege=getUserPrivilege();


      return view("admin.post_qualification",["links"=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_plans"=>$project_plans,"year"=>$year]);
    }
  }

  public function getPostQualToVerify()
  {
    $year=null;
    $title="POST QUALIFICATIONS (UNVERIFIED)";
    $APP = new APP;
    $project_plans=$APP->getSpecificProcurementActivity("post_qual_to_verify",$year);
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();

    return view("admin.verify_post_qualification",["links"=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_plans"=>$project_plans,"year"=>$year]);
  }

  public function getNoticeOfAwardActivity()
  {
    $year=date("Y");
    $title="NOTICE OF AWARD";
    $APP = new APP;
    $project_plans=$APP->getSpecificProcurementActivity("notice_of_award",$year);

    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view("admin.notice_of_award",["links"=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_plans"=>$project_plans,"year"=>$year]);
  }

  public function getContractPreparationSigningActivity()
  {
    $year=date("Y");
    $title="CONTRACT PREPARATION AND SIGNING";
    $APP = new APP;
    $project_plans=$APP->getSpecificProcurementActivity("contract_preparation_signing",$year);
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view("admin.contract_preparation_signing",["links"=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_plans"=>$project_plans,"year"=>$year]);
  }

  public function getApprovalByAuthorityActivity()
  {
    $year=date("Y");
    $title="APPROVAL BY HIGHER AUTHORITY";
    $APP = new APP;
    $project_plans=$APP->getSpecificProcurementActivity("approval_by_higher_authority",$year);
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view("admin.authority_approval",["links"=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_plans"=>$project_plans,"year"=>$year]);
  }

  public function getNoticeToProceedActivity()
  {
    $year=date("Y");
    $title="NOTICE TO PROCEED";
    $APP = new APP;
    $project_plans=$APP->getSpecificProcurementActivity("notice_to_proceed",$year);
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view("admin.notice_to_proceed",["links"=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_plans"=>$project_plans,"year"=>$year]);
  }


  public function getEndingPostquals()
  {
    $date=date("Y-m-d",strtotime("+1 day"));

    $ending_post_qual=DB::table("project_plans")
    ->select("project_plans.project_no","project_plans.plan_id","project_timelines.post_qualification_end")
    ->where([["project_activity_status.main_status","pending"],["project_activity_status.bid_evaluation","finished"],["project_activity_status.post_qual","pending"],["project_plans.pow_ready",true],["project_timelines.timeline_status","set"],["project_timelines.post_qualification_end","<=",$date]])
    ->whereRaw("DATEDIFF(CURDATE(), project_timelines.post_qualification_start) >= 40")
    ->join("procacts","project_plans.latest_procact_id","procact_id")
    ->join("project_activity_status","procacts.procact_id","project_activity_status.procact_id")
    ->join("project_timelines","procacts.procact_id","project_timelines.procact_id")
    ->orderBy("project_timelines.post_qualification_end","asc")
    ->get();

    $extension_post_qual=DB::table("project_plans")
    ->select("project_plans.project_no","project_plans.plan_id","project_timelines.post_qualification_end",DB::raw("DATEDIFF(project_timelines.post_qualification_end,project_timelines.post_qualification_start) as date_diff"))
    ->where([[DB::raw("DATEDIFF(CURDATE(),project_timelines.post_qualification_start)"),12],["project_activity_status.main_status","pending"],["project_activity_status.bid_evaluation","finished"],["project_activity_status.post_qual","pending"],["project_plans.pow_ready",true],["project_timelines.timeline_status","set"]])
    ->orWhere([[DB::raw("DATEDIFF(CURDATE(),project_timelines.post_qualification_start)"),30],["project_activity_status.main_status","pending"],["project_activity_status.bid_evaluation","finished"],["project_activity_status.post_qual","pending"],["project_plans.pow_ready",true],["project_timelines.timeline_status","set"]])
    ->orWhere([[DB::raw("DATEDIFF(project_timelines.post_qualification_end,project_timelines.post_qualification_start)"),"<",12],["project_activity_status.main_status","pending"],["project_activity_status.bid_evaluation","finished"],["project_activity_status.post_qual","pending"],["project_plans.pow_ready",true],["project_timelines.timeline_status","set"]])
    ->join("procacts","project_plans.latest_procact_id","procact_id")
    ->join("project_activity_status","procacts.procact_id","project_activity_status.procact_id")
    ->join("project_timelines","procacts.procact_id","project_timelines.procact_id")
    ->orderBy("project_timelines.post_qualification_end","asc")
    ->get();




    return ["ending_post_qual"=>$ending_post_qual,"extension_post_qual"=>$extension_post_qual];

  }


  public function submitRequestExtension(Request $request)
  {
    $data=$request->validate([
      "request_extension_remarks"=>"required",
    ]);

    $message="success";
    $date=date("Y-m-d",strtotime("+1 day"));
    $plan_ids=$request->input("request_extension_plan_id");
    $plan_ids_array=explode(",",$plan_ids);
    $non_extendable=false;
    $remarks=$request->input("request_extension_remarks");
    $APP = new APP;

    foreach ($plan_ids_array as $plan_id) {
      $plan=DB::table("project_plans")
      ->select("project_plans.*")
      ->where([["project_plans.plan_id",$plan_id]])->first();

      $bidders=$APP->getActiveBidders($plan->latest_procact_id,"responsive,disqualified,active");
      $maximum_day=$bidders*45;

      $post_qual_days=DB::table("project_plans")
      ->select(DB::raw("DATEDIFF(post_qualification_end, post_qualification_start) AS post_qual_interval"))
      ->where([["project_plans.plan_id",$plan_id],["project_activity_status.main_status","pending"],["project_activity_status.bid_evaluation","finished"],["project_activity_status.post_qual","pending"],["project_plans.pow_ready",true],["project_timelines.timeline_status","set"],["project_timelines.post_qualification_end","<=",$date]])
      // ->whereRaw("DATEDIFF(project_timelines.post_qualification_end, project_timelines.post_qualification_start) >= 40")
      ->join("procacts","project_plans.latest_procact_id","procact_id")
      ->join("project_activity_status","procacts.procact_id","project_activity_status.procact_id")
      ->join("project_timelines","procacts.procact_id","project_timelines.procact_id")
      ->first();

      if($post_qual_days->post_qual_interval<$maximum_day){

      }

    }
    if($non_extendable==false){
      foreach ($plan_ids_array as $plan_id) {
        $project_timeline=DB::table("project_timelines")
        ->select("project_timelines.*",DB::raw("DATEDIFF(project_timelines.post_qualification_end, project_timelines.post_qualification_start) as post_qual_interval"))
        ->where("project_plans.plan_id",$plan_id)
        ->join("procacts","project_timelines.procact_id","procacts.procact_id")
        ->join("project_plans","project_plans.latest_procact_id","procacts.procact_id")
        ->first();

        if($project_timeline->post_qual_interval<12){
          $additional=12;
        }
        else if($project_timeline->post_qual_interval>=12){
          $additional=45;
        }
        else{
          $additional=0;
        }

        $post_qualification_end=date("Y-m-d", strtotime($project_timeline->post_qualification_start."+".$additional." days"));
        $award_notice_start=date("Y-m-d", strtotime($post_qualification_end."+1 days"));
        $award_days=date_diff(date_create($project_timeline->award_notice_start),date_create($project_timeline->award_notice_end))->format("%d");
        $award_notice_end=date("Y-m-d", strtotime($award_notice_start."+".$award_days." days"));
        $contract_signing_start=date("Y-m-d", strtotime($award_notice_end."+1 days"));
        $contract_signing_days=date_diff(date_create($project_timeline->contract_signing_start),date_create($project_timeline->contract_signing_end))->format("%d");
        $contract_signing_end=date("Y-m-d", strtotime($contract_signing_start."+".$contract_signing_days." days"));
        $authority_approval_start=date("Y-m-d", strtotime($contract_signing_end."+1 days"));
        $authority_approval_days=date_diff(date_create($project_timeline->authority_approval_start),date_create($project_timeline->authority_approval_end))->format("%d");
        $authority_approval_end=date("Y-m-d", strtotime($authority_approval_start."+".$authority_approval_days." days"));
        $proceed_notice_start=date("Y-m-d", strtotime($authority_approval_end."+1 days"));
        $proceed_notice_days=date_diff(date_create($project_timeline->proceed_notice_start),date_create($project_timeline->proceed_notice_end))->format("%d");
        $proceed_notice_end=date("Y-m-d", strtotime($proceed_notice_start."+".$proceed_notice_days." days"));

        $update=DB::table("project_timelines")
        ->select("project_timelines.*",DB::raw("DATEDIFF(project_timelines.post_qualification_end, project_timelines.post_qualification_start) as post_qual_interval"))
        ->where("project_plans.plan_id",$plan_id)
        ->join("procacts","project_timelines.procact_id","procacts.procact_id")
        ->join("project_plans","project_plans.latest_procact_id","procacts.procact_id")
        ->update([
          "project_timelines.post_qualification_end"=>$post_qualification_end,
          "project_timelines.award_notice_start"=>$award_notice_start,
          "project_timelines.award_notice_end"=>$award_notice_end,
          "project_timelines.contract_signing_start"=>$contract_signing_start,
          "project_timelines.contract_signing_end"=>$contract_signing_end,
          "project_timelines.authority_approval_start"=>$authority_approval_start,
          "project_timelines.authority_approval_end"=>$authority_approval_end,
          "project_timelines.proceed_notice_start"=>$proceed_notice_start,
          "project_timelines.proceed_notice_end"=>$proceed_notice_end,
          "project_timelines.updated_at"=>now(),
        ]);

        DB::table("project_logs")->insert([
          "plan_id"=>	$plan_id,
          "user_id"=>Auth::user()->id,
          "project_log_type"=>"Request for Post Qual Extension",
          "project_log_remarks"=>$remarks,
          "log_date"=>date("Y-m-d"),
          "created_at"=> now(),
          "updated_at"=> now()
        ]);

      }
    }


    return redirect()->back()->with("message",$message);

  }


  public function extendProcess(Request $request)
  {

    $data=$request->validate([
      "extend_remarks"=>"required",
      "extend_date"=>"required",
    ]);
    $ids=$request->input("extend_plan_id");
    $process=$request->input("process");
    $date=$request->input("extend_date");
    $remarks=$request->input("extend_remarks");


    $APP= new APP();
    $message=$APP->extendSpecificProcess($ids,$process,$date,$remarks);
    return redirect()->back()->with("message",$message);
  }

  public function getProjectsForRebid()
  {
    $year=null;
    $title="Projects For Rebid";
    $APP = new APP;
    $project_plans=$APP->getSpecificProcurementActivity("projects_for_rebid",$year);
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view("admin.rebid",["links"=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_plans"=>$project_plans,"year"=>$year]);
  }

  public function getProjectsForReview()
  {
    $year=null;
    $title="Projects For Review";
    $APP = new APP;
    $project_plans=$APP->getSpecificProcurementActivity("projects_for_review",$year);
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view("admin.rebid",["links"=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_plans"=>$project_plans,"year"=>$year]);
  }

  public function getProjectsToReactivate()
  {
    $year=date("Y");
    $title="Reactivate Projects";
    $APP = new APP;
    $project_plans=$APP->getSpecificProcurementActivity("projects_to_reactivate",$year);
    $links=getUserLinks();
    $user_privilege=getUserPrivilege();


    return view("admin.reactivate_projects",["links"=>$links,'user_privilege'=>$user_privilege,"title"=>$title,"project_plans"=>$project_plans,"year"=>$year]);
  }

  public function submitRevert(Request $request)
  {
    $data=$request->validate([
      "revert_remarks"=>"required",
    ]);
    $plan_id=$request->input("revert_plan_id");

    $remarks=$request->input("revert_remarks");
    // get latest linked files
    $project_plan=DB::table("project_plans")->where("plan_id",$plan_id)->first();
    $latest_activity_status=DB::table("project_activity_status")->where("plan_id",$plan_id)->orderBy("pro_act_stat_id","desc")->first();
    $latest_procact=DB::table("project_plans")->select("procacts.*","project_plans.*")->where("project_plans.plan_id",$plan_id)->join("procacts","project_plans.latest_procact_id","procacts.procact_id")->first();
    $latest_timeline=DB::table("project_timelines")->where("plan_id",$plan_id)->orderBy("timeline_id","desc")->first();
    $log="Revert Project";

    DB::table("project_activity_status")->where("procact_id",$latest_procact->procact_id)->update([
      "main_status"=>"reverted"
    ]);

    DB::table("project_plans")->where("plan_id",$plan_id)->update([
      "status"=>"reverted"
    ]);

    DB::table("project_logs")->insert([
      "plan_id"=>	$project_plan->plan_id,
      "user_id"=>Auth::user()->id,
      "project_log_type"=>$log,
      "project_log_remarks"=>$remarks,
      "log_date"=>date("Y-m-d"),
      "created_at"=> now(),
      "updated_at"=> now()
    ]);

    return redirect()->back()->with("message","revert_success");


  }

}
